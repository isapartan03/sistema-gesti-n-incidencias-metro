<?php
require_once '../Model/modulo_read_falla.php';

date_default_timezone_set('America/Caracas');

if (empty($_GET)) {
    header("Location: controlador_read_falla.php");
    exit();
}

$modelo = new ModeloFalla();
$filtros = $_GET;
$fallas = $modelo->ObtenerFallas($filtros);

// Guardar en sesión para posibles PDFs futuros
$_SESSION['fallas_exportar'] = $fallas;

// Convertir datos a JSON para usar en JavaScript
$fallas_json = json_encode($fallas);

// Campos disponibles para agrupar
$campos_disponibles = [
    'Coordinacion' => 'Coordinación',
    'Prioridad' => 'Prioridad',
    'Falla_Status' => 'Estado de Falla',
    'Justificacion' => 'Justificación',
    'Ubicacion' => 'Ubicación',
    'Nombre' => 'Nombre de Estación',
    'Username' => 'Usuario Reportador'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="text/css" href="../../Assets/imagenes/imgMetro16.png">
    <title>Configuración de Gráficas</title>
    <link rel="stylesheet" type="text/css" href="../../Assets/CSS/btnExportarStilos.CSS">
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .downloads-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .downloads-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .download-item {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: white;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        /* Canvas de miniaturas pequeño para visualización */
        .download-item canvas {
            max-height: 120px !important;
            width: 100% !important;
        }
        /* Canvas principal GRANDE para buena calidad de descarga */
        .chart-container {
            height: 500px !important;
            margin-bottom: 20px;
        }
        .chart-container canvas {
            height: 100% !important;
            width: 100% !important;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: center;
        }
        .btn-pdf {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-pdf:hover {
            background-color: #c0392b;
        }
        .btn-volver {
            background-color: #95a5a6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-volver:hover {
            background-color: #7f8c8d;
        }
        .container {
            width: 90%;
            max-width: 800px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        /* Botón Generar Gráfica más oscuro */
        #generate-chart-btn {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        #generate-chart-btn:hover {
            background-color: #1a252f;
        }
        /* Botón Descargar Gráfica individual - MÁS VISIBLE */
        .btn-descargar-individual {
            background-color: #27ae60;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 8px;
            width: 100%;
            font-size: 12px;
        }
        .btn-descargar-individual:hover {
            background-color: #219653;
        }
        /* Estilo para indicar gráfica duplicada */
        .duplicado {
            border: 2px solid #e74c3c;
            background-color: #ffeaa7;
        }
        .mensaje-duplicado {
            color: #e74c3c;
            font-size: 10px;
            margin-top: 5px;
            font-weight: bold;
        }
        /* Modal para confirmación de duplicados */
        .modal-duplicado {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-duplicado-content {
            background-color: white;
            margin: 20% auto;
            padding: 25px;
            border-radius: 8px;
            width: 400px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .modal-duplicado-title {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
            font-weight: bold;
        }
        .modal-duplicado-message {
            margin-bottom: 20px;
            color: #666;
            line-height: 1.5;
        }
        .modal-duplicado-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .modal-duplicado-btn {
            padding: 10px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            min-width: 100px;
        }
        .modal-duplicado-confirm {
            background-color: #3498db;
            color: white;
        }
        .modal-duplicado-confirm:hover {
            background-color: #2980b9;
        }
        .modal-duplicado-cancel {
            background-color: #95a5a6;
            color: white;
        }
        .modal-duplicado-cancel:hover {
            background-color: #7f8c8d;
        }
        h1 {
            font-size: 28px;
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }
        h2 {
            font-size: 22px;
            color: #444;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .committee {
            background-color: #f9f9f9;
            border-left: 4px solid #4a86e8;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }
        
        .committee-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .committee-subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .water-pump {
            font-size: 18px;
            padding: 15px;
            background-color: #e6f2ff;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Modal de confirmación principal - Estilos originales -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title">Opciones de Reporte</h3>
            <p class="modal-message">¿Desea generar un grafico personalizado?</p>
            <div class="modal-buttons">
                <button class="modal-btn btn-confirm" id="confirmDownload" title="Generar Grafico">Configurar</button>
                <button class="modal-btn btn-cancel" id="cancelDownload" title="Generar solo PDF">Solo PDF</button>
                <a class="modal-btn btn-volver" href="controlador_read_falla.php">Volver</a>
            </div>
        </div>
    </div>

    <!-- Modal para confirmación de duplicados -->
    <div id="duplicateModal" class="modal-duplicado">
        <div class="modal-duplicado-content">
            <div class="modal-duplicado-title">Confirmación</div>
            <div class="modal-duplicado-message" id="duplicateModalMessage">
                <!-- El mensaje se insertará aquí -->
            </div>
            <div class="modal-duplicado-buttons">
                <button class="modal-duplicado-btn modal-duplicado-confirm" id="confirmDuplicate">Aceptar</button>
                <button class="modal-duplicado-btn modal-duplicado-cancel" id="cancelDuplicate">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Formulario invisible para descarga directa del PDF -->
    <form id="pdfForm" action="exportarPdf.php" method="GET" style="display: none;">
        <?php foreach ($filtros as $key => $value): ?>
            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
        <?php endforeach; ?>
    </form>

    <div class="container" id="content">
        <header>
            <h1>Seleccione los campos para crear el grafico</h1>
        </header>
        
        <div class="controls">
            <div class="form-group">
                <label for="campo">Agrupar por:</label>
                <select id="campo" name="campo">
                    <?php foreach ($campos_disponibles as $valor => $texto): ?>
                        <option value="<?php echo $valor; ?>">
                            <?php echo $texto; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="button" id="generate-chart-btn">Generar Gráfica</button>
        </div>
        
        <div class="chart-container">
            <canvas id="chart"></canvas>
        </div>

        <!-- Sección de descargas de gráficas -->
        <div class="downloads-section" id="downloadsSection" style="display: none;">
            <h3>Gráficas Generadas</h3>
            <div class="downloads-grid" id="downloadsGrid">
                <!-- Aquí se agregarán las gráficas generadas -->
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="action-buttons">
            <button class="btn-pdf" id="generatePdfBtn" style="display: none;">
                Generar Reporte PDF Completo
            </button>
            <a class="btn-volver" href="controlador_read_falla.php">Volver al Panel</a>
        </div>
    </div>

    <script src="../../Librerias/chart.js-4.4.9/package/dist/chart.umd.js"></script>
    <script>
        // Datos de fallas desde PHP
        const fallas = <?php echo $fallas_json; ?>;
        const camposDisponibles = <?php echo json_encode($campos_disponibles); ?>;
        const filtros = <?php echo json_encode($filtros); ?>;
        
        let chart = null;
        let generatedCharts = [];
        let camposYaGenerados = [];
        let pendingCampo = null;

        // Elementos de la modal
        const modal = document.getElementById('confirmationModal');
        const confirmDownloadBtn = document.getElementById('confirmDownload');
        const cancelDownloadBtn = document.getElementById('cancelDownload');
        const content = document.getElementById('content');
        const pdfForm = document.getElementById('pdfForm');

        // Elementos de la modal de duplicados
        const duplicateModal = document.getElementById('duplicateModal');
        const duplicateModalMessage = document.getElementById('duplicateModalMessage');
        const confirmDuplicateBtn = document.getElementById('confirmDuplicate');
        const cancelDuplicateBtn = document.getElementById('cancelDuplicate');

        // Mostrar modal al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            modal.style.display = 'block';
            content.style.display = 'none';
        });

        // Confirmar: Mostrar interfaz de gráficas
        confirmDownloadBtn.addEventListener('click', function() {
            modal.style.display = 'none';
            content.style.display = 'block';
            
            // Inicializar primera gráfica
            const campoInicial = document.getElementById('campo').value;
            actualizarGraficaPrincipal(campoInicial);
        });

        // Cancelar: Generar PDF directamente (SIN VENTANA EMERGENTE)
        cancelDownloadBtn.addEventListener('click', function() {
            // Enviar formulario para descargar PDF directamente
            pdfForm.submit();
        });

        // Confirmar duplicado
        confirmDuplicateBtn.addEventListener('click', function() {
            duplicateModal.style.display = 'none';
            if (pendingCampo) {
                agregarGraficaDescargas(pendingCampo, true);
                pendingCampo = null;
            }
        });

        // Cancelar duplicado
        cancelDuplicateBtn.addEventListener('click', function() {
            duplicateModal.style.display = 'none';
            pendingCampo = null;
        });

        // Función para calcular los ticks del eje Y
        function calcularTicksEjeY(data) {
            const maxValue = Math.max(...data);
            if (maxValue === 0) return [0, 1];
            
            const ticks = [];
            for (let i = 0; i <= maxValue; i++) {
                ticks.push(i);
            }
            return ticks;
        }

        // Función para crear una gráfica independiente
        function crearGraficaIndependiente(campo, containerId, esMiniatura = false) {
            const datosAgrupados = agruparDatos(fallas, campo);
            
            const labels = Object.keys(datosAgrupados);
            const data = Object.values(datosAgrupados);
            
            // Ordenar descendente
            const indices = Array.from(data.keys()).sort((a, b) => data[b] - data[a]);
            const labelsOrdenados = indices.map(i => labels[i]);
            const dataOrdenados = indices.map(i => data[i]);
            
            // Calcular los ticks del eje Y
            const yTicks = calcularTicksEjeY(dataOrdenados);
            const maxY = Math.max(...yTicks);
            
            let canvasElem;
            if (typeof containerId === 'string') {
                canvasElem = document.getElementById(containerId);
            } else {
                canvasElem = containerId;
            }
            if (!canvasElem) {
                throw new Error('Canvas no encontrado para crear la gráfica');
            }
            const ctx = canvasElem.getContext('2d');
            
            const options = {
                type: 'bar',
                data: {
                    labels: labelsOrdenados,
                    datasets: [{
                        label: 'Cantidad de Fallas',
                        data: dataOrdenados,
                        backgroundColor: [
                            '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', 
                            '#1abc9c', '#d35400', '#34495e', '#16a085', '#27ae60'
                        ],
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: maxY,
                            ticks: {
                                font: {
                                    size: esMiniatura ? 20 : 24
                                },
                                stepSize: 1,
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                }
                            },
                            title: {
                                display: !esMiniatura,
                                text: 'Cantidad de Fallas',
                                font: {
                                    size: 24
                                }
                            }
                        },
                        x: {
                            title: {
                                display: !esMiniatura,
                                text: camposDisponibles[campo],
                                font: {
                                    size: 22
                                }
                            },
                            ticks: {
                                font: {
                                    size: esMiniatura ? 20 : 24
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: !esMiniatura,
                            text: 'Distribución de Fallas por ' + camposDisponibles[campo],
                            font: {
                                size: 24,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: !esMiniatura,
                            labels: {
                                font: {
                                    size: 24
                                }
                            }
                        },
                        tooltip: {
                            enabled: !esMiniatura,
                            callbacks: {
                                label: function(context) {
                                    return `Cantidad: ${context.parsed.y}`;
                                }
                            }
                        }
                    }
                }
            };

            // Configuración específica para miniaturas
            if (esMiniatura) {
                options.options.scales.y.ticks.display = false;
                options.options.scales.x.ticks.display = false;
                options.options.plugins.legend.display = false;
                options.options.plugins.title.display = true;
                options.options.plugins.title.text = `Por ${camposDisponibles[campo]}`;
                options.options.plugins.title.font = { size: 10 };
            }
            
            return new Chart(ctx, options);
        }


        // Función para actualizar la gráfica principal
        function actualizarGraficaPrincipal(campoSeleccionado) {
            const ctx = document.getElementById('chart').getContext('2d');
            
            // Destruir gráfico anterior si existe
            if (chart) {
                chart.destroy();
            }
            
            // Crear nuevo gráfico
            chart = crearGraficaIndependiente(campoSeleccionado, 'chart', false);
        }

        // Función para agrupar datos
        function agruparDatos(datos, campo) {
            const resultado = {};
            
            datos.forEach(falla => {
                let valor = falla[campo] ?? 'No especificado';
                
                if (valor === null || valor === '') {
                    valor = 'No especificado';
                }

                if(campo == 'Falla_Status'){
                    valor = (valor == '1') ? 'Abierta' : 'Cerrada';
                }
                
                if (!resultado[valor]) {
                    resultado[valor] = 0;
                }
                
                resultado[valor]++;
            });
            
            return resultado;
        }

        // Función para descargar gráfica individual - CREAR UNA NUEVA EN ALTA CALIDAD
        function descargarGrafica(campo) {
            const filename = `grafica-fallas-${campo}-${new Date().toISOString().slice(0,10)}.png`;

            // Crear canvas temporal y añadir al DOM oculto para asegurar render
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = 700;
            tempCanvas.height = 500;
            // estilo oculto pero en DOM
            tempCanvas.style.position = 'fixed';
            tempCanvas.style.left = '-9999px';
            tempCanvas.style.top = '-9999px';
            document.body.appendChild(tempCanvas);

            // Crear gráfico en el canvas temporal (crearGraficaIndependiente ahora acepta elemento canvas)
            const tempChart = crearGraficaIndependiente(campo, tempCanvas, false);

            // Esperar render y descargar
            setTimeout(() => {
                try {
                const url = tempCanvas.toDataURL('image/png', 1.0);
                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                } catch (err) {
                    console.error('Error al generar la imagen:', err);
                    alert('Ocurrió un error al generar la imagen. Revisa la consola para más detalles.');
                } finally {
                // destruir chart temporal y remover canvas
                tempChart.destroy();
                document.body.removeChild(tempCanvas);
                }   
            }, 700); // pequeño delay para asegurar render; ajustar si es necesario
        }

        // Función para agregar gráfica a la sección de descargas
        function agregarGraficaDescargas(campo, esDuplicado = false) {
            const downloadsGrid = document.getElementById('downloadsGrid');
            const chartId = 'chart-' + Date.now() + '-' + campo;
            
            if (!esDuplicado) {
                camposYaGenerados.push(campo);
            }
            
            const downloadItem = document.createElement('div');
            downloadItem.className = `download-item ${esDuplicado ? 'duplicado' : ''}`;
            downloadItem.innerHTML = `
                <strong>${camposDisponibles[campo]}</strong>
                ${esDuplicado ? '<div class="mensaje-duplicado">(Duplicado)</div>' : ''}
                <div style="height: 120px; margin: 8px 0;">
                    <canvas id="${chartId}"></canvas>
                </div>
                <button class="btn-descargar-individual" data-campo="${campo}">
                    📥 Descargar Gráfica
                </button>
            `;
            
            downloadsGrid.appendChild(downloadItem);
            
            // Asignar evento de clic al botón de descarga
            const btnDescargar = downloadItem.querySelector('.btn-descargar-individual');
            btnDescargar.addEventListener('click', function() {
                const campo = this.getAttribute('data-campo');
                descargarGrafica(campo);
            });
            
            // Crear una gráfica independiente para esta miniatura
            setTimeout(() => {
                const nuevoChart = crearGraficaIndependiente(campo, chartId, true);
                generatedCharts.push({chart: nuevoChart, campo: campo, id: chartId});
            }, 100);
        }

        // Event Listeners para la página de configuración
        document.addEventListener('DOMContentLoaded', function() {
            const generateChartBtn = document.getElementById('generate-chart-btn');
            const campoSelect = document.getElementById('campo');
            const generatePdfBtn = document.getElementById('generatePdfBtn');
            const downloadsSection = document.getElementById('downloadsSection');

            // Actualizar gráfica principal cuando cambia el select
            campoSelect.addEventListener('change', function() {
                const campoSeleccionado = this.value;
                actualizarGraficaPrincipal(campoSeleccionado);
            });

            // Generar nueva gráfica y agregar a descargas
            generateChartBtn.addEventListener('click', function() {
                const campoSeleccionado = document.getElementById('campo').value;
                
                // Verificar si ya existe una gráfica para este campo
                const esDuplicado = camposYaGenerados.includes(campoSeleccionado);
                
                if (esDuplicado) {
                    // Mostrar modal de confirmación
                    pendingCampo = campoSeleccionado;
                    duplicateModalMessage.textContent = `Ya existe una gráfica para "${camposDisponibles[campoSeleccionado]}". ¿Desea generarla de nuevo?`;
                    duplicateModal.style.display = 'block';
                } else {
                    // Agregar directamente si no es duplicado
                    agregarGraficaDescargas(campoSeleccionado);
                    
                    // Mostrar sección de descargas y botón PDF
                    downloadsSection.style.display = 'block';
                    generatePdfBtn.style.display = 'block';
                }
            });

            // Generar PDF completo (DESCARGA DIRECTA - SIN VENTANA EMERGENTE)
            generatePdfBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Enviar formulario para descargar PDF directamente
                pdfForm.submit();
            });
        });
    </script>
</body>
</html>