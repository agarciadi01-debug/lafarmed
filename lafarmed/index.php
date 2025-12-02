<?php 
session_start();
if (!isset($_SESSION['usuario'])) { 
    header("Location: login.php"); 
    exit; 
}
include("conexion.php"); 

// Headers para prevenir cache en páginas protegidas
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>LAFARMED – Inventario</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>

<div class="header-top">
    <div class="header-left">
        <div class="logo-icon">+</div>
        <span class="logo-text">LAFARMED</span>
    </div>
    <div class="header-right">
        <nav class="header-nav">
            <a href="index.php" class="nav-link active">Productos</a>
            <a href="nuevo_producto.php" class="nav-link">Nuevo Producto</a>
            <a href="buscar.php" class="nav-link">Buscar</a>
        </nav>
        <div class="header-actions">
            <span class="theme-toggle">🌙</span>
            <span class="user-info"><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
            <a href="logout.php" class="logout-btn">Salir</a>
        </div>
    </div>
</div>

<div class="contenido">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">Panel de Control</h1>
        <div id="icono-alerta" class="icono-alerta" onclick="mostrarAlertas()" style="display: none;">
            <span class="alerta-badge" id="contador-alertas">0</span>
            🔔
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <div class="card-icon blue">📦</div>
            <div class="card-content">
                <h3>Total de Productos</h3>
                <p id="totalProductos">0</p>
            </div>
        </div>
        <div class="card">
            <div class="card-icon red">⚠️</div>
            <div class="card-content">
                <h3>Stock Bajo</h3>
                <p id="totalBajo">0</p>
            </div>
        </div>
        <div class="card">
            <div class="card-icon orange">📅</div>
            <div class="card-content">
                <h3>Próximos a Vencer</h3>
                <p id="totalVencer">0</p>
            </div>
        </div>
    </div>

    <div class="top-bar">
        <input type="text" id="buscar" placeholder="🔍 Buscar por nombre o código...">

        <select id="categoria">
            <option value="">Todas las categorías</option>
            <option>Analgésicos</option>
            <option>Antibióticos</option>
            <option>Antialérgicos</option>
            <option>Gastrointestinal</option>
            <option>Cardiovascular</option>
            <option>Antidiabéticos</option>
        </select>

        <select id="stock">
            <option value="">Todo el stock</option>
            <option value="normal">Stock Normal</option>
            <option value="bajo">Stock Bajo</option>
            <option value="vencer">Próximo a Vencer</option>
        </select>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>CÓDIGO</th>
                    <th>NOMBRE PRODUCTO</th>
                    <th>CATEGORÍA</th>
                    <th>STOCK</th>
                    <th>PRECIO</th>
                    <th>VENCIMIENTO</th>
                    <th>LOTE</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody id="tbody-productos"></tbody>
        </table>
    </div>

</div>

<!-- Modal de Alertas -->
<div id="modal-alertas" class="modal-alertas">
    <div class="modal-alertas-contenido">
        <div class="modal-alertas-header">
            <h2>⚠️ Alertas de Inventario</h2>
            <button class="cerrar-alertas" onclick="cerrarAlertas()">&times;</button>
        </div>
        <div class="modal-alertas-body" id="lista-alertas">
            <!-- Las alertas se cargarán aquí -->
        </div>
    </div>
</div>

<!-- SCRIPTS AL FINAL -->
<script>
let todosLosProductos = [];

function cargarProductos() {
    fetch("productos.php")
        .then(res => res.json())
        .then(data => {
            todosLosProductos = data;
            aplicarFiltros();
            verificarAlertas(); // Verificar alertas después de cargar productos
        })
        .catch(error => {
            console.error("Error al cargar productos:", error);
        });
}

function aplicarFiltros() {
    const buscarTexto = document.getElementById("buscar").value.toLowerCase().trim();
    const categoriaSeleccionada = document.getElementById("categoria").value;
    const stockSeleccionado = document.getElementById("stock").value;

    // Filtrar productos
    let productosFiltrados = todosLosProductos.filter(p => {
        // Filtro por búsqueda (nombre o código)
        const coincideBusqueda = buscarTexto === "" || 
            p.nombre.toLowerCase().includes(buscarTexto) || 
            p.codigo.toLowerCase().includes(buscarTexto);

        // Filtro por categoría
        const coincideCategoria = categoriaSeleccionada === "" || 
            p.categoria === categoriaSeleccionada;

        // Filtro por tipo de stock
        let coincideStock = true;
        if (stockSeleccionado === "bajo") {
            coincideStock = p.stock < 10;
        } else if (stockSeleccionado === "normal") {
            coincideStock = p.stock >= 10;
        } else if (stockSeleccionado === "vencer") {
            coincideStock = p.proximo_vencer == "1";
        }

        return coincideBusqueda && coincideCategoria && coincideStock;
    });

    // Mostrar productos filtrados
    mostrarProductos(productosFiltrados);
}

function mostrarProductos(productos) {
    const tbody = document.getElementById("tbody-productos");
    tbody.innerHTML = "";

    let total = 0;
    let bajo = 0;
    let vencer = 0;

    if (productos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px; color: #999; font-style: italic;">
                    🔍 No se encontraron productos con los filtros seleccionados
                </td>
            </tr>
        `;
    } else {
        productos.forEach(p => {
            total++;

            const diasVencimiento = calcularDiasVencimiento(p.fecha_vencimiento);
            
            // Determinar clases de estilo según el estado
            let stockClass = '';
            let vencerClass = '';
            
            // Stock: rojo si es 0 o negativo, naranja si es bajo pero > 0
            if (p.stock === 0 || p.stock < 0) {
                stockClass = 'style="color: #e74c3c; font-weight: 600;"';
            } else if (p.stock < 10) {
                stockClass = 'style="color: #e74c3c; font-weight: 600;"';
                bajo++;
            }
            
            // Vencimiento: rojo si ya venció, naranja si está próximo a vencer
            if (diasVencimiento < 0) {
                vencerClass = 'style="color: #e74c3c; font-weight: 600;"';
            } else if (diasVencimiento >= 0 && diasVencimiento <= 30) {
                vencerClass = 'style="color: #f39c12; font-weight: 600;"';
                vencer++;
            }

            const tr = document.createElement("tr");
            
            // Determinar color de stock
            let stockColor = '';
            if (p.stock === 0 || p.stock < 0) {
                stockColor = 'color: #e74c3c; font-weight: 600;';
            } else if (p.stock < 10) {
                stockColor = 'color: #e74c3c; font-weight: 600;';
            } else if (p.stock < 50) {
                stockColor = 'color: #f39c12; font-weight: 600;';
            } else {
                stockColor = 'color: #27ae60; font-weight: 600;';
            }

            // Formatear fecha
            const fecha = new Date(p.fecha_vencimiento);
            const fechaFormateada = fecha.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });

            tr.innerHTML = `
                <td><strong>${p.codigo}</strong></td>
                <td>${p.nombre}</td>
                <td>${p.categoria}</td>
                <td style="${stockColor}">${p.stock}</td>
                <td>$${parseFloat(p.precio || 0).toFixed(2)}</td>
                <td ${vencerClass}>${fechaFormateada}</td>
                <td>${p.lote || 'N/A'}</td>
                <td class="acciones-cell">
                    <span class="accion-icon" title="Editar">✏️</span>
                    <span class="accion-icon delete" title="Eliminar">🗑️</span>
                </td>
            `;

            tbody.appendChild(tr);
        });
    }

    // Actualizar estadísticas con los productos filtrados
    document.getElementById("totalProductos").innerText = total;
    document.getElementById("totalBajo").innerText = bajo;
    document.getElementById("totalVencer").innerText = vencer;
}

// Variables para alertas
let alertas = [];

function calcularDiasVencimiento(fechaVencimiento) {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    
    const vencimiento = new Date(fechaVencimiento);
    vencimiento.setHours(0, 0, 0, 0);
    
    const diferencia = vencimiento - hoy;
    const dias = Math.ceil(diferencia / (1000 * 60 * 60 * 24));
    
    return dias;
}

function verificarAlertas() {
    alertas = [];
    
    todosLosProductos.forEach(p => {
        const diasVencimiento = calcularDiasVencimiento(p.fecha_vencimiento);
        
        // Verificar si ya venció
        if (diasVencimiento < 0) {
            alertas.push({
                tipo: 'vencido',
                producto: p.nombre,
                codigo: p.codigo,
                lote: p.lote || 'N/A',
                dias: Math.abs(diasVencimiento),
                mensaje: `El producto "${p.nombre}" (Lote: ${p.lote || 'N/A'}) ya venció hace ${Math.abs(diasVencimiento)} día(s)`
            });
        }
        // Verificar si se acabó el stock
        else if (p.stock === 0 || p.stock < 0) {
            alertas.push({
                tipo: 'sin-stock',
                producto: p.nombre,
                codigo: p.codigo,
                lote: p.lote || 'N/A',
                mensaje: `El producto "${p.nombre}" (Lote: ${p.lote || 'N/A'}) ya no tiene stock disponible`
            });
        }
        // Verificar stock bajo (pero no cero)
        else if (p.stock < 10) {
            alertas.push({
                tipo: 'stock-bajo',
                producto: p.nombre,
                codigo: p.codigo,
                lote: p.lote || 'N/A',
                stock: p.stock,
                mensaje: `El producto "${p.nombre}" (Lote: ${p.lote || 'N/A'}) tiene solo ${p.stock} unidades en stock`
            });
        }
        
        // Verificar próximo a vencer (pero no vencido)
        if (diasVencimiento >= 0 && diasVencimiento <= 30) {
            alertas.push({
                tipo: 'vencimiento',
                producto: p.nombre,
                codigo: p.codigo,
                lote: p.lote || 'N/A',
                dias: diasVencimiento,
                mensaje: `El producto "${p.nombre}" (Lote: ${p.lote || 'N/A'}) está próximo a vencer en ${diasVencimiento} día(s)`
            });
        }
    });
    
    actualizarIconoAlerta();
}

function actualizarIconoAlerta() {
    const icono = document.getElementById('icono-alerta');
    const contador = document.getElementById('contador-alertas');
    
    if (alertas.length > 0) {
        icono.style.display = 'block';
        contador.textContent = alertas.length;
        
        // Abrir automáticamente el modal de alertas al cargar la página (solo la primera vez)
        if (!window.alertasYaMostradas) {
            setTimeout(() => {
                mostrarAlertas();
                window.alertasYaMostradas = true;
            }, 500); // Pequeño delay para que la página termine de cargar
        }
    } else {
        icono.style.display = 'none';
    }
}

function mostrarAlertas() {
    const modal = document.getElementById('modal-alertas');
    const lista = document.getElementById('lista-alertas');
    
    if (alertas.length === 0) {
        lista.innerHTML = '<div class="sin-alertas">✅ No hay alertas en este momento</div>';
    } else {
        lista.innerHTML = '';
        
        // Ordenar alertas: primero vencidos y sin stock, luego stock bajo, luego próximos a vencer
        const alertasOrdenadas = alertas.sort((a, b) => {
            const prioridad = { 'vencido': 1, 'sin-stock': 2, 'stock-bajo': 3, 'vencimiento': 4 };
            return (prioridad[a.tipo] || 5) - (prioridad[b.tipo] || 5);
        });
        
        alertasOrdenadas.forEach((alerta, index) => {
            const item = document.createElement('div');
            item.className = `alerta-item ${alerta.tipo}`;
            
            let titulo = '';
            let icono = '';
            
            switch(alerta.tipo) {
                case 'vencido':
                    titulo = '🚫 Producto Vencido';
                    icono = '⛔';
                    break;
                case 'sin-stock':
                    titulo = '❌ Sin Stock';
                    icono = '📭';
                    break;
                case 'stock-bajo':
                    titulo = '📦 Stock Bajo';
                    icono = '📉';
                    break;
                case 'vencimiento':
                    titulo = '⚠️ Próximo a Vencer';
                    icono = '⏰';
                    break;
            }
            
            item.innerHTML = `
                <div class="alerta-item-titulo">${icono} ${titulo}</div>
                <div class="alerta-item-descripcion">${alerta.mensaje}</div>
                <div class="alerta-item-detalle">Código: ${alerta.codigo}</div>
            `;
            
            lista.appendChild(item);
        });
    }
    
    modal.classList.add('mostrar');
}

function cerrarAlertas() {
    const modal = document.getElementById('modal-alertas');
    modal.classList.remove('mostrar');
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modal-alertas');
    if (event.target == modal) {
        cerrarAlertas();
    }
}

// Event listeners para los filtros
document.getElementById("buscar").addEventListener("input", aplicarFiltros);
document.getElementById("categoria").addEventListener("change", aplicarFiltros);
document.getElementById("stock").addEventListener("change", aplicarFiltros);

// Cargar productos al iniciar
cargarProductos();

// Actualizar alertas periódicamente (cada minuto) para reflejar cambios en tiempo real
setInterval(function() {
    if (todosLosProductos.length > 0) {
        verificarAlertas();
        aplicarFiltros(); // También actualizar la visualización de productos
    }
}, 60000); // Cada 60 segundos

// Prevenir que el botón atrás acceda a páginas en cache
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

</body>
</html>
