<?php

require_once '../../librerias/dompdf/autoload.inc.php';
require_once '../Model/modulo_read_falla.php';

date_default_timezone_set('America/Caracas');

use Dompdf\Dompdf;
use Dompdf\Options;

// Obtener fallas de la sesión
if (isset($_SESSION['fallas_exportar']) && !empty($_SESSION['fallas_exportar'])) {
    $fallas = $_SESSION['fallas_exportar'];
    unset($_SESSION['fallas_exportar']); // Limpiar después de usar
} else {
    // Si no hay datos en sesión, obtener con filtros actuales
    $modelo = new ModeloFalla();
    $filtros = $_GET;
    $fallas = $modelo->ObtenerFallas($filtros);
}

if (empty($fallas)) {
        // Esto no debería pasar, pero por seguridad
        die("No hay fallas para exportar con los filtros aplicados");
    }

$imagePath = realpath('../../Assets/imagenes/cintillo3.jpeg');
if ($imagePath && file_exists($imagePath)) {
    $type = pathinfo($imagePath, PATHINFO_EXTENSION);
    $data = file_get_contents($imagePath);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
} else {
    $base64 = '';
}

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    body { 
      font-family: 'DejaVu Sans', sans-serif; 
      font-size: 9pt; 
      margin: 0;
      padding: 0;
      color: #333;
    }
    
    .header {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      border-bottom: 2px solid #808080;
      padding-bottom: 5px;
    }
    
    .logo {
      width: 70px;
      height: 70px;
      margin-right: 15px;
    }
    
    .institution-info {
      flex-grow: 1;
    }
    
    .institution-name {
      font-size: 16pt;
      font-weight: bold;
      color: #404040;
      margin: 0;
    }
    
    .report-title {
      font-size: 14pt;
      color: #606060;
      margin: 5px 0;
    }
    
    .report-subtitle {
      font-size: 10pt;
      color: #808080;
      margin: 3px 0;
    }
    
    .report-info {
      background-color: #f5f5f5;
      border: 1px solid #e0e0e0;
      border-radius: 5px;
      padding: 10px;
      margin-bottom: 10px;
      font-size: 9pt;
    }
    
    .info-row {
      display: flex;
      margin-bottom: 5px;
    }
    
    .info-label {
      font-weight: bold;
      width: 120px;
      color: #404040;
    }
    
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 0;
      font-size: 7pt;
      page-break-inside: auto; 
    }

    .data-table thead {
      display: table-header-group; /* <-- Repite el encabezado en cada página */
    }

    .data-table tfoot {
      display: table-footer-group; /* <-- Si usas pie de tabla */
    }

    .data-table tr {
      page-break-inside: avoid;
      page-break-after: auto;
    }

    .data-table th {
      background-color: #808080;
      color: white;
      text-align: left;
      padding: 8px;
      border: 1px solid #606060;
      font-weight: bold;
    }
    
    .data-table td {
      padding: 6px;
      border: 1px solid #ddd;
    }
    
    .data-table tr:nth-child(even) {
      background-color: #f5f5f5;
    }
    
    /* Estado de la falla */
    .status {
      padding: 3px 6px;
      border-radius: 10px;
      font-size: 8pt;
      font-weight: bold;
      text-align: center;
      display: inline-block;
    }
    
    .status-active {
      background-color: #dff0d8;
      color: #3c763d;
      border: 1px solid #d6e9c6;
    }
    
    .status-inactive {
      background-color: #f2dede;
      color: #a94442;
      border: 1px solid #ebccd1;
    }
    
    .footer {
      margin-top: 20px;
      padding-top: 10px;
      border-top: 1px solid #ddd;
      font-size: 8pt;
      color: #666;
      text-align: center;
    }
    
    .signature-area {
      margin-top: 30px;
      display: flex;
      justify-content: space-between;
      gap: 30px;
    }
    
    .signature-line {
      width: 200px;
      border-top: 1px solid #666;
      padding-top: 5px;
      text-align: center;
      font-size: 8pt;
    }
  </style>
</head>
<body>

<?php if ($base64): ?>
  <div style="text-align:center; margin-bottom: 5px;">
    <img src="<?= $base64 ?>" style="width:100%; max-height:600px;">
  </div>
<?php endif; ?>

  <div class="header">
    <div class="institution-info">
      <h1 class="institution-name">Sistema Metro de Los Teques</h1>
      <h2 class="report-title">Reporte de Fallas Técnicas</h2>
      <div class="report-subtitle">Centro de Control de Fallas</div>
    </div>
  </div>
  
  <div class="report-info">
    <div class="info-row">
      <span class="info-label">Fecha de generación:</span>
      <span><?= date('d/m/Y H:i') ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Total de registros:</span>
      <span><?= count($fallas) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Área responsable:</span>
      <span>Centro de control de Fallas</span>
    </div>
  </div>
  
  <table class="data-table">
    <thead>
      <tr>
        <th width="5%">ID</th>
        <th width="5%">Prioridad</th>
        <th width="10%">Supervisor</th>
        <th width="10%">Usuario quien registra la falla</th>
        <th width="10%">Usuario quien finaliza la falla</th>
        <th width="12%">Descripción</th>
        <th width="10%">Equipo</th>
        <th width="7%">Ubicación</th>
        <th width="12%">Justificación</th>
        <th width="12%">Observaciones</th>
        <th width="12%">Diagnostico</th>
        <th width="10%">F. Apertura</th>
        <th width="10%">F. Cierre</th>
        <th width="5%">Estado</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($fallas as $f): ?>
      <tr>
        <td><?= htmlspecialchars($f['ID_Falla']) ?></td>
        <td><?= htmlspecialchars($f['Prioridad']) ?></td>
        <td><?= htmlspecialchars($f['nombres']) ?></td>
        <td><?= htmlspecialchars($f['Username']) ?></td>
        <td><?= htmlspecialchars($f['UsuarioCierre'] ?? '-') ?></td>
        <td><?= htmlspecialchars($f['descripcion']) ?></td>
        <td><?= htmlspecialchars($f['Nombre']) ?></td>
        <td><?= htmlspecialchars($f['Ubicacion'] ?? '-') ?></td>
        <td><?= htmlspecialchars($f['Justificacion'] ?? '—') ?></td>
        <td><?= htmlspecialchars($f['Observaciones'] ?? '—') ?></td>
        <td><?= htmlspecialchars($f['Diagnostico'] ?? '—') ?></td>
        <td><?= htmlspecialchars($f['F_apertura'] ?? '—') ?></td>
        <td><?= htmlspecialchars($f['F_cierre'] ?? '—') ?></td>
        <td>
          <span class="status <?= $f['Falla_Status'] ? 'status-active' : 'status-inactive' ?>">
            <?= $f['Falla_Status'] ? 'Activo' : 'Cerrado' ?>
          </span>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  
  <div class="footer">
    <p>Sistema de Gestión de Fallas Técnicas - Versión 1.0<br>
    Generado automáticamente por el sistema</p>
    
    <table style="width: 100%; margin-top: 30px; text-align: center; font-size: 7pt;">
      <tr>
        <td style="width: 33%;">
          <div style="border-top: 1px solid #666; padding-top: 4px;">Departamento de Control de Fallas</div>
        </td>
        <td style="width: 33%;">
          <div style="border-top: 1px solid white; padding-top: 4px;"></div>
        </td>
        <td style="width: 33%;">
          <div style="border-top: 1px solid #666; padding-top: 4px;">Recibido</div>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
<?php
$html = ob_get_clean();
$opciones = new Options();
$opciones->set('defaultFont','Helvetica');
$dompdf = new Dompdf($opciones);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','landscape');
$dompdf->render();
$dompdf->stream("fallas_filtradas_".date('Y-m-d').".pdf", ["Attachment"=>true]);
exit()
?>