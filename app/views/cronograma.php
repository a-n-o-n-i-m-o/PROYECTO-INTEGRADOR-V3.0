<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montoVehiculo = floatval($_POST['montoVehiculo']);
    $cuotaInicial = floatval($_POST['cuotaInicial']);
    $plazo = intval($_POST['plazo']);
    $tipoSeguro = $_POST['tipoSeguro'];

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
            $totalSeguroGenerado += $seguro;
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

    // HTML y CSS mejorado
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

    // Header
    echo "<header>
            <h1>Simulador de Crédito Vehicular</h1>
          </header>";

    // Mostrar el cronograma al usuario
    echo "<h2>Cronograma de Pagos</h2>";
    echo "<table>";
    echo "<tr><th>Mes</th><th>Pago Capital</th><th>Interés</th><th>Cuota Mensual</th><th>Seguro</th><th>Saldo Capital</th></tr>";

    foreach ($cronograma_pagos as $pago) {
        echo "<tr>
            <td>{$pago['mes']}</td>
            <td>{$pago['monto_capital']}</td>
            <td>{$pago['interes']}</td>
            <td>{$pago['cuota_mensual']}</td>
            <td>{$pago['seguro']}</td>
            <td>{$pago['saldo_capital']}</td>
        </tr>";
    }
    echo "</table>";

    // Resumen
    echo "<div class='summary'>
            <h4>Resumen del Crédito</h4>
            <p><strong>Monto Total a Pagar:</strong> S/. " . round($montoTotal, 1) . "</p>
            <p><strong>Interés Total:</strong> S/. " . round($interesTotal, 1) . "</p>
            <p><strong>Capital Pagado Total:</strong> S/. " . round($totalCapitalPagado, 0) . "</p>
            <p><strong>Seguros Total:</strong> S/. " . round($totalSeguroGenerado, 0) . "</p>
          </div>";

    // Botones
    echo "<div class='buttons'>
            <button onclick=\"window.history.back();\">Regresar</button>
          </div>";

    // Footer
    echo "<footer>
            <p>&copy; 2024 Simulador de Crédito Vehicular. Todos los derechos reservados.</p>
          </footer>";
} else {
    echo "Acceso no permitido.";
}
?>
