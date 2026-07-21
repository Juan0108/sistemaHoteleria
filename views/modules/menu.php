<?php
// Obtén el nombre de la página actual
$currentPage = basename($_SERVER["REQUEST_URI"], ".php");

// Si no estás en la página 'reset', muestra el menú
if ($currentPage !== "reset"): ?>
<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="active">
                <a href="inicio">
                    <i class="fa fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <?php
            if ($_SESSION["Perfil"] == "Soporte Tecnico") {
                echo '<li>
                        <a href="negocios">
                            <i class="fa fa-handshake-o"></i>
                            <span>Negocios</span>
                        </a>
                    </li>
                    <li>
                        <a href="usuarios">
                            <i class="fa fa-user"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="categorias">
                            <i class="fa fa-th"></i>
                            <span>Categorías</span>
                        </a>
                    </li>
                    <li>
                        <a href="marcas">
                            <i class="fa fa-registered"></i>
                            <span>Marcas</span>
                        </a>
                    </li>
                    <li>
                        <a href="submarcas">
                            <i class="fa fa-cc"></i>
                            <span>SubMarcas</span>
                        </a>
                    </li>
                    <li>
                        <a href="clasificaciones">
                            <i class="fa fa-clone"></i>
                            <span>Clasificaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="productos">
                            <i class="fa fa-product-hunt"></i>
                            <span>Productos</span>
                        </a>
                    </li>
                    <li>
                        <a href="inventarios">
                            <i class="fa fa-cubes"></i>
                            <span>Inventarios</span>
                        </a>
                    </li>';
            }

            if ($_SESSION["Perfil"] == "Administrador" || $_SESSION["Perfil"] == "Ventas") {
                echo '<li>
                        <a href="inventarios">
                            <i class="fa fa-cubes"></i>
                            <span>Inventarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="clientes">
                            <i class="fa fa-users"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li>
                        <a href="productos">
                            <i class="fa fa-product-hunt"></i>
                            <span>Productos</span>
                        </a>
                    </li>';
            }

            if ($_SESSION["Perfil"] == "Vendedor") {
                echo '<li>
                        <a href="negocios">
                            <i class="fa fa-handshake-o"></i>
                            <span>Negocios</span>
                        </a>
                    </li>
                    <li>
                        <a href="productos">
                            <i class="fa fa-product-hunt"></i>
                            <span>Productos</span>
                        </a>
                    </li>
                    <li>
                        <a href="usuarios">
                            <i class="fa fa-user"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>';
            }
            ?>
        </ul>
    </section>
</aside>
<?php endif; ?>
