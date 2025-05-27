<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<header class="cabecera-negra">
    <div class="logo-circulo-blanco">
        <a href="index.php"><img src="img/logo (40).png" alt="Logo de tu Empresa" class="logo-dentro-circulo"></a>
    </div>
    <nav class="menu-cabecera">
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="listar_usuarios.php">Usuarios</a></li>
            <li><a href="listar_producto.php">Productos</a></li>
            <li><a href="listar_categorias.php">Categorías</a></li>
            <li>
                <div class="user-menu">
                    <button class="user-btn" onclick="toggleDropdown()">
                        <span class="material-icons">account_circle</span>
                    </button>
                    <div id="userDropdown" class="dropdown-content">
                        <a href="cerrar_sesion.php" class="cerrar-sesion">Cerrar Sesión</a>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
</header>

<style>
.user-menu {
    position: relative;
    display: inline-block;
    align-items: center;
}

.user-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 24px;
}

.user-btn .material-icons {
    color: white;
    font-size: 28px;
    transition: color 0.3s ease;
}

.user-btn:hover .material-icons {
    color: #E1B8E2;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 150px;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
    z-index: 1;
    border-radius: 5px;
    margin-top: 5px;
}

.dropdown-content.show {
    display: block;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    transition: background-color 0.3s;
}

.dropdown-content a:hover {
    background-color: #f1f1f1;
}

.cerrar-sesion {
    color: #333;
    text-decoration: none;
    border-top: 1px solid #eee;
}
</style>

<script>
function toggleDropdown() {
    document.getElementById("userDropdown").classList.toggle("show");
}

// Cierra el dropdown si se hace clic fuera
window.onclick = function(event) {
    if (!event.target.closest('.user-menu')) {
        var dropdown = document.getElementById("userDropdown");
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    }
}
</script>
