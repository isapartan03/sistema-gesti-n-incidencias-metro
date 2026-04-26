<link rel="stylesheet" type="text/css" href="../../../../Programas/Metro/Assets/CSS/header.css">
<header class="encabezado">
    <!-- hamburguer -->
    <img class="M" src="../../../../Programas/Metro/Assets/imagenes/imgMetro16WB1.png">
    <div class="containerh">
        <div class="line"></div>
        <div class="line"></div>  
        <div class="line"></div>
    </div>
    <!-- nar-var -->
    <nav class="menu">
        <ul>
            <li>
                <a href="../../../../Programas/Metro/Fallas/Controller/controlador_read_falla.php?action=mostrar">Fallas</a>
            </li>
            <li>
                <?php if($_SESSION['rol'] == 'Admin'): ?>
                    <a href="../../../../Programas/Metro/Coordinacion/controller/Coordcont.php?action=mostrar">Coordinaciones</a>
                <?php else: ?>
                    <span class="disabled-link" title="No posee Permisos">Coordinaciones</span>
                <?php endif; ?>
            </li>
            <li>
                    <a href="../../../../Programas/Metro/public/index.php">Usuarios</a>
            </li>
            <li>
                <?php if($_SESSION['rol'] == 'Admin'): ?>
                    <a href="../../../../Programas/Metro/Equipos/Controller/EquipoC.php?action=mostrar">Equipos</a>
                <?php else: ?>
                    <span class="disabled-link" title="No posee Permisos">Equipos</span>
                <?php endif; ?>
            </li>
            <li>
                <?php if($_SESSION['rol'] == 'Admin'): ?>
                    <a href="../../../../Programas/Metro/Estacion/Controller/estacionC.php?action=mostrar">Estaciones</a>
                <?php else: ?>
                    <span class="menu disabled-link" title="No posee Permisos" >Estaciones</span>
                <?php endif; ?>
            </li>
       
            <li>
                <?php if($_SESSION['rol'] == 'Admin'): ?>
                    <a href="../../../../Programas/Metro/Coordinadores/controller/CoorController.php?action=mostrar">Personal</a>
                <?php else: ?>
                    <span class="disabled-link" title="No posee Permisos" >Personal</span>
                <?php endif; ?>
            </li>
            <li>
                <?php if($_SESSION['rol'] == 'Admin'): ?>
                    <a href="../../../../Programas/Metro/Tecnicos/controller/TecCont.php?action=mostrar">Técnicos</a>
                <?php else: ?>
                    <span class="disabled-link" title="No posee Permisos">Técnicos</span>
                <?php endif; ?>
            </li>
            <li>
                <a href="../../../../Programas/Metro/public/index.php?c=usuario&a=cerrarSesion"><img src="../../../../Programas/Metro/Assets/imagenes/exit.svg"></a>
            </li>
        </ul>
    </nav>
</header>

<style>
    .disabled-link {
        color: #999; /* Color gris para indicar que está deshabilitado */
        cursor: not-allowed; /* Cambia el cursor a "no permitido" */
        text-decoration: none; /* Quita el subrayado */
    }
    .disabled-link:hover {
        color: #999; /* Mantiene el mismo color al pasar el mouse */
    }
</style>

<script> 
    container = document.querySelector(".containerh");
    container.onclick = function(){
        menu = document.querySelector(".menu");
        menu.classList.toggle("active");
    }
</script>