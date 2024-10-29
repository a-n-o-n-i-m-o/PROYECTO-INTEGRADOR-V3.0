<?php
// procesar_credito.php

require_once '../models/CreditoVehicular.php';
require_once '../models/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario y validarlos
    $nombreCliente = $_POST['nombre'] ?? null;
    $apellidosCliente = $_POST['apellidos'] ?? null;
    $dni = $_POST['dni'] ?? null;
    $fechaNacimiento = $_POST['fechaNacimiento'] ?? null;
    $direccionCliente = $_POST['direccion'] ?? null;
    $telefonoCliente = $_POST['telefono'] ?? null;
    $emailCliente = $_POST['correo'] ?? null;
    $ingresoMensual = $_POST['ingresoMensual'] ?? null;
    $montoVehiculo = $_POST['montoVehiculo'] ?? null;
    $marcaVehiculo = $_POST['marca'] ?? null;
    $modeloVehiculo = $_POST['modelo'] ?? null;
    $cuotaInicial = $_POST['porcentajeCuotaInicial'] ?? null;
    $plazo = $_POST['plazoMeses'] ?? null;
    $tipoSeguro = $_POST['tipoSeguro'] ?? null;
    
    // Cálculos
    $cuotaInicialSoles = $montoVehiculo * ($cuotaInicial / 100);
    $montoCapital = $montoVehiculo * (1 - ($cuotaInicial / 100));
    $tea = 0.15;
    $tem = (pow(1 + $tea, 1.0 / 12)) - 1;
    $payment = $montoCapital * ($tem / (1 - pow(1 + $tem, -$plazo)));
     
    $totalSeguroGenerado = 0;
    $interesTotal = 0;
    $totalCapitalPagado = 0;
    $cronograma_pagos = [];
    $fecha_inicio = new DateTime();

    for ($i = 0; $i < $plazo; $i++) {
        $interes = round($montoCapital * $tem, 2);
        $cuotaMensual = round($payment, 2);
        $capitalPagado = round($cuotaMensual - $interes, 2);
        $montoCapital -= $capitalPagado;
        $interesTotal += $interes;
        $totalCapitalPagado += $capitalPagado;
        $fecha_pago = clone $fecha_inicio;
        $fecha_pago->modify("+$i month");

        $seguro = 0;
        if (!empty($tipoSeguro) && $tipoSeguro !== "sin_seguro") {
            $seguro = round(0.00077 * $montoCapital, 2);
            $totalSeguroGenerado += $seguro; // Add to total insurance
        }

        $cronograma_pagos[] = [
            'mes' => $fecha_pago->format('Y-m'),
            'monto_capital' => $capitalPagado,
            'interes' => $interes,
            'cuota_mensual' => round($cuotaMensual + $seguro, 1),
            'saldo_capital' => round($montoCapital, 0),
            'seguro' => $seguro
        ];
    }

    $montoTotal = $cuotaInicialSoles + $totalCapitalPagado + $interesTotal + $totalSeguroGenerado;

    // HTML y CSS
    echo "<!DOCTYPE html>";
    echo "<html lang='es'>";
    echo "<head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <link href='https://fonts.googleapis.com/css2?family=KoHo&display=swap' rel='stylesheet'>
            <style>
        body {
            font-family: 'KoHo', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header, footer {
            background-color: #002f6c;
            color: white;
            text-align: center;
            padding: 15px 0;
        }

        header h1, footer p {
            margin: 0;
        }

        h2, h3, h4 {
            color: #002f6c;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #FF4500;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .summary {
            margin-top: 20px;
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .summary p {
            font-size: 16px;
            color: #333;
            margin: 5px 0;
        }
        .buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .buttons button {
            padding: 10px 25px;
            font-size: 16px;
            margin: 10px;
            border: none;
            cursor: pointer;
            color: white;
            background-color: #28a745;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .buttons button.cancel {
            background-color: #dc3545;
        }
        .buttons button:hover {
            opacity: 0.9;
        }
    </style>";

    // Cabecera
    echo "<header><h1>Cronograma de Pagos</h1></header>";

    // Mostrar el cronograma al usuario
    echo "<table>";
    echo "<tr><th>Mes</th><th>Monto Capital (S/)</th><th>Interés (S/)</th><th>Cuota Mensual (S/)</th><th>Saldo Capital (S/)</th><th>Seguro (S/)</th></tr>";
    foreach ($cronograma_pagos as $pago) {
        echo "<tr>
                <td>{$pago['mes']}</td>
                <td>S/ {$pago['monto_capital']}</td>
                <td>S/ {$pago['interes']}</td>
                <td>S/ {$pago['cuota_mensual']}</td>
                <td>S/ {$pago['saldo_capital']}</td>
                <td>S/ {$pago['seguro']}</td>
              </tr>";
    }
    echo "</table>";

    // Mostrar el monto total a pagar
    echo "<div class='summary'>";
    echo "<h4>Monto Total a Pagar: S/ {$montoTotal}</h4>";
    echo "<h4>Desglose del Monto Total</h4>";
    echo "<p>Cuota Inicial: S/ {$cuotaInicialSoles}</p>";
    echo "<p>Total de Cuotas Mensuales: S/ " . round($totalCapitalPagado, 0) . "</p>";
    echo "<p>Total de Intereses Generados: S/ {$interesTotal}</p>";
    echo "<p>Total de Seguro Generado: S/ {$totalSeguroGenerado}</p>"; 
    echo "</div>";

    // Botones para confirmar o cancelar
    echo "<div class='buttons'>";
    echo '<form method="POST" action="finalizar_proceso.php">';
    echo '<input type="hidden" name="nombre" value="' . htmlspecialchars($nombreCliente) . '">';
    echo '<input type="hidden" name="apellidos" value="' . htmlspecialchars($apellidosCliente) . '">';
    echo '<input type="hidden" name="dni" value="' . htmlspecialchars($dni) . '">';
    echo '<input type="hidden" name="fechaNacimiento" value="' . htmlspecialchars($fechaNacimiento) . '">';
    echo '<input type="hidden" name="direccion" value="' . htmlspecialchars($direccionCliente) . '">';
    echo '<input type="hidden" name="telefono" value="' . htmlspecialchars($telefonoCliente) . '">';
    echo '<input type="hidden" name="correo" value="' . htmlspecialchars($emailCliente) . '">';
    echo '<input type="hidden" name="ingresoMensual" value="' . htmlspecialchars($ingresoMensual) . '">';
    echo '<input type="hidden" name="montoVehiculo" value="' . htmlspecialchars($montoVehiculo) . '">';
    echo '<input type="hidden" name="marca" value="' . htmlspecialchars($marcaVehiculo) . '">';
    echo '<input type="hidden" name="modelo" value="' . htmlspecialchars($modeloVehiculo) . '">';
    echo '<input type="hidden" name="porcentajeCuotaInicial" value="' . htmlspecialchars($cuotaInicial) . '">';
    echo '<input type="hidden" name="plazoMeses" value="' . htmlspecialchars($plazo) . '">';
    echo '<input type="hidden" name="tipoSeguro" value="' . htmlspecialchars($tipoSeguro) . '">';
    echo '<button type="submit" name="confirmar">Confirmar Proceso</button>';
    echo '<button type="button" onclick="window.location.href=\'../views/formulario_credito.php\'" class="cancel">Cancelar</button>';
    echo '</form>';

    // Botón para generar PDF
    echo '<form method="POST" action="../controllers/generar_pdf.php" target="_blank">';
    echo '<input type="hidden" name="cronograma" value="' . htmlspecialchars(serialize($cronograma_pagos)) . '">';
    echo '<button type="submit" name="generar_pdf">Generar PDF</button>';
    echo '</form>';
    echo "</div>";

    // Pie de página
    echo "<footer>";
    echo "<p>&copy; © 2024 BCP | Todos los derechos reservados. Sede Central, Centenario 156, La Molina 15026, Lima, Perú. BANCO DE CREDITO DEL PERU S.A - RUC 20100047218</p>";
    echo "</footer>";
}
?>
