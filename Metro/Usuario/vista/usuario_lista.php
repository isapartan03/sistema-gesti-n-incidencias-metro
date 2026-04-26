<?php
require_once"../utilidades/repositorio.php";
verifcarSession();
//<---Calculo en la paginacion en la tabla 
$registrosPorPagina = 5; 
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaActual = max(1, $paginaActual); 

// Calcular total de páginas
$totalRegistros = count($usuarios);
$totalPaginas = max(1, ceil($totalRegistros / $registrosPorPagina)); // <-- corrección

// Ajustar página actual si es mayor que el total
$paginaActual = min($paginaActual, $totalPaginas);

// Obtener los registros para la página actual
$inicio = ($paginaActual - 1) * $registrosPorPagina;
$usuariosPorPagina = array_slice($usuarios, $inicio, $registrosPorPagina);


?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Panel/Usuarios</title>
	<link rel="icon" type="text/css" href="../Assets/imagenes/imgMetro16.png">
	<link rel="stylesheet" href="../Assets/CSS/tablaMenu.css">
	<link rel="stylesheet" type="text/css" href="../Assets/CSS/sweetalert2.min.js">
	<link rel="stylesheet" type="text/css" href="../Assets/CSS/modal.css">
	<script src="../Assets/Js/sweetalert2.min.js"></script>
</head>
<body>
 <?php  
 include '../Assets/HTML/headerAdmin.php';
 ?> 
 <div class="center-wrapper">
  <div class="container">
	<h2>Panel de Usuarios</h2>
	<form method="gets" action="../public/index.php">
		<input type="" name="c" value="usuario" hidden>
		<input type="text" name="a" value="capturaCampos" hidden>
            <div class="form-row">
            	 <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="Nombre" class="form-control">
                </div>
                <div class="form-group">
                    <label for="id">Apellidos:</label>
                    <input type="text" name="Apellidos" class="form-control">
                </div>
               
            </div>
            <div class="botones">
                <button class="btn btn-filtrar" type="submit">Filtrar</button>
               <div class="grupo-izquierdo">
                	<button class="action-btn"><a href="index.php?c=usuario&a=showfrm"class="action-btn"> Editar Informacion Personal</a></button>
	  <button class="action-btn"><a href="index.php?c=usuario&a=mostraFormu" class="action-btn"> Registrar Usuario</a></button>
                </div>

                
            </div>
        </form>
	
  <div class="table-wrapper">
	  <table id="dataTable">
	   <thead>
		<tr>
			
			<th>Nombre</th>
			<th>Apellido</th>
			<th>Rol</th>
			<th>Estado</th>
			<th>Acciones</th>
			
		</tr>
	</thead>
	<tbody>
		<?php if (count($usuariosPorPagina) > 0 && $_SESSION['rol']=='Admin'): ?>
			<?php foreach ($usuariosPorPagina as $usuario): ?>
				<?php if($usuario['ID']!=$_SESSION['id']): ?>
				<tr>
					<td><?= htmlspecialchars($usuario['nombres']) ?></td>
					<td><?= htmlspecialchars($usuario['apellidos']) ?></td>
					<td><?= htmlspecialchars($usuario['rol']) ?></td>
					<td>
						<span class="estatus<?=$usuario['estatus'] === 'activo' ? 'activo' : 'inactivo'?>">
							<?=htmlspecialchars($usuario['estatus'])?>
						</span>
					</td>
					<td>
						
						<a href="index.php?c=usuario&a=showfrm&id=<?= $usuario['ID'] ?>" class="btn btn-sm btn-primary" title="Editar Informacion" >Editar</a>

						<a href="#" class="btn btn-info btn-sm btn-show-reset" title="Restablecer Preguntas" data-id="<?= $usuario['ID'] ?>">Restablecer</a>

						<?php if($usuario['estatus'] === 'activo'): ?>
							<a href="#" class="btn btn-sm btn-warning btn-show-susp" data-id="<?= $usuario['ID'] ?>" title="Suspender Temporalmente">Suspender</a>
						<?php else: ?>
							<a href="#" class="btn btn-sm btn-success btn-show-hab" data-id="<?= $usuario['ID'] ?>" title="Habilitar Nuevamente">Habilitar</a>
						<?php endif; ?>

						<a href="#" class="btn btn-danger btn-sm btn-show-delete" data-id="<?= $usuario['ID'] ?>" title="Eliminar del Sistema">Eliminar</a>



					</td>
					
					

				</tr>
			<?php endif;?>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="6" style="text-align: center;">No hay resultados para mostrar</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
</div>
<?php if($_SESSION['rol']=='Trabajador'):?>
	
<?php else:?>
	 <?php
// Construir la cadena base de la URL con los filtros actuales, excepto "pagina"
    $parametros = $_GET;
    unset($parametros['pagina']);
    $queryBase = http_build_query($parametros);
    ?>
<div class="paginacion" id="pag">
	<!-- Primera página -->
	<?php if ($paginaActual > 1): ?>
		<a href="?pagina=1" title="Primera página">« Primera</a>
	<?php else: ?>
		<span class="deshabilitado">« Primera</span>
	<?php endif; ?>

	<!-- Página anterior -->
	<?php if ($paginaActual > 1): ?>
		<a href="?pagina=<?= $paginaActual - 1 ?>" title="Página anterior">‹ Anterior</a>
	<?php else: ?>
		<span class="deshabilitado">‹ Anterior</span>
	<?php endif; ?>

	<!-- Rango de páginas -->
	<?php 
	$inicioRango = max(1, $paginaActual - 2);
	$finRango = min($totalPaginas, $paginaActual + 2);
	
	if ($inicioRango > 1): ?>
		<span>...</span>
	<?php endif;
	
	for ($i = $inicioRango; $i <= $finRango; $i++): ?>
		<?php if ($i == $paginaActual): ?>
			<span class="pagina-actual"><?= $i ?></span>
		<?php else: ?>
			<a href="?<?= $queryBase ?>&pagina=<?= $i ?>"><?= $i ?></a>
		<?php endif; ?>
	<?php endfor;
	
	if ($finRango < $totalPaginas): ?>
		<span>...</span>
	<?php endif; ?>

	<!-- Página siguiente -->
	<?php if ($paginaActual < $totalPaginas): ?>
		<a href="?pagina=<?= $paginaActual + 1 ?>" title="Página siguiente">Siguiente ›</a>
	<?php else: ?>
		<span class="deshabilitado">Siguiente ›</span>
	<?php endif; ?>

	<!-- Última página -->
	<?php if ($paginaActual < $totalPaginas): ?>
		<a href="?pagina=<?= $totalPaginas ?>" title="Última página">Última »</a>
	<?php else: ?>
		<span class="deshabilitado">Última »</span>
	<?php endif; ?>
</div>

<div class="info-paginacion">
	

	Mostrando usuarios <strong><?= $inicio + 1 ?></strong> a <strong><?= min($inicio + $registrosPorPagina, $totalRegistros) ?></strong> 
	de un total de <strong><?= $totalRegistros ?></strong> registros
<?php endif;?>	
</div>
</div>
</div>

<script>
//<--- permite mostrar la alerta segun los parametros de la variable global alertas--> 
	swal('<?=$GLOBALS['alertas']['titulo']?>','<?=$GLOBALS['alertas']['mesge']?>','<?=$GLOBALS['alertas']['icon']?>',);
</script>
<?php resetGlobals()?>
<!-- Modal Eliminar -->
<div id="modalDelete" class="modal">
  <div class="modal-content">
	<p>¿Está seguro de eliminar este usuario? Esta acción no se podrá revertir.</p>
	<button id="confirmDelete" class="btn btn-danger">Confirmar</button>
	<button class="btn btn-primary cancelModal">Cancelar</button>
</div>
</div>

<!-- Modal Restablecer -->
<div id="modalReset" class="modal">
  <div class="modal-content">
	<p>¿Desea restablecer el acceso del usuario?</p>
	<button id="confirmReset" class="btn btn-info">Confirmar</button>
	<button class="btn btn-primary cancelModal">Cancelar</button>
</div>
</div>

<!-- Modal Suspender -->
<div id="modalSusp" class="modal">
  <div class="modal-content">
	<p>¿Está seguro de suspender este usuario?</p>
	<button id="confirmSusp" class="btn btn-warning">Confirmar</button>
	<button class="btn btn-primary cancelModal">Cancelar</button>
</div>
</div>

<!-- Modal Habilitar -->
<div id="modalHabi" class="modal">
  <div class="modal-content">
	<p>¿Desea habilitar nuevamente al usuario?</p>
	<button id="confirmHabi" class="btn btn-success">Confirmar</button>
	<button class="btn btn-primary cancelModal">Cancelar</button>
</div>
</div>
<script type="text/javascript" src="../Assets/Js/modalUsuarios.js"></script>
</body>
</html>
