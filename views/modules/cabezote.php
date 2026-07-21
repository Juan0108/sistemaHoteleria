<header class="main-header">

<!-- Logotipo -->	
<?php
// Obtén el nombre de la página actual
$currentPage = basename($_SERVER["REQUEST_URI"], ".php");

// Mostrar el logotipo solo si no estás en la página 'reset'
if ($currentPage !== "reset"): ?>
	<a href="inicio" class="logo">
		<!-- logo mini -->
		<span class="logo-mini">
			<img src="views/img/Template/Principal.png" class="img-responsive" style="padding:10px">
		</span>
		<!-- logo normal -->
		<span class="logo-lg">
			<?php echo "''" . $_SESSION["RazonSocial"] . "''"; ?>
		</span>
	</a>
<?php endif; ?>

<!-- Barra de navegacion -->
<nav class="navbar navbar-static-top" role="navigation">
<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
    <span class="sr-only">Toggle navigation</span>
</a>

<!-- Perfil de Usuario -->
<div class="navbar-custom-menu"> 
	<ul class="nav navbar-nav">
		<li class="dropdown user user-menu">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<?php echo '<img src="'.$_SESSION["RutaFoto"].'" class="user-image">'; ?>  
				<span class="hidden-xs"><?php echo $_SESSION["NombreUser"]; ?></span>
			</a>
			<!-- Dropdown-toggle -->
			<ul class="dropdown-menu">
				<li class="user-body">
					<div class="pull-right">
						<a href="salir" class="btn btn-default btn-flat">Salir</a>
					</div>
				</li>
			</ul>
		</li>
	</ul>
</div>
</nav>
</header> 