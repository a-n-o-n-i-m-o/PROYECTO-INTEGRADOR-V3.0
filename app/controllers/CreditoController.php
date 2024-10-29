<?php
require_once '../models/CreditoVehicular.php';
session_start();

function validarDatos($dni, $nombre, $email, $montoVehiculo, $porcentajeCuotaInicial, $plazoMeses) {
    // Validar el DNI
    if (empty($dni) || !preg_match('/^\d{8}$/', $dni)) {
        return "El DNI debe tener exactamente 8 dígitos y solo contener números.";
    }
    // Validar monto del vehículo
    if (empty($montoVehiculo) || $montoVehiculo < 1) {
        return "El monto del vehículo debe ser mayor o igual a 1.";
    }

    // Validar porcentaje de cuota inicial
    if (empty($porcentajeCuotaInicial) || $porcentajeCuotaInicial < 1 || $porcentajeCuotaInicial > 100) {
        return "El porcentaje de la cuota inicial debe estar entre 1 y 100.";
    }

    // Validar plazo de crédito
    if (empty($plazoMeses) || $plazoMeses < 12 || $plazoMeses > 72) {
        return "El plazo debe estar entre 12 y 72 meses.";
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene el DNI, nombre y email de la sesión o del POST
    $dni = $_SESSION['dni'] ?? '';
    $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

    // Sanitiza y valida los datos de entrada
    $montoVehiculo = filter_input(INPUT_POST, 'montoVehiculo', FILTER_VALIDATE_FLOAT);
    $porcentajeCuotaInicial = filter_input(INPUT_POST, 'cuotaInicial', FILTER_VALIDATE_FLOAT);
    $tasaEfectivaAnual = 12.9; // Tasa fija
    $plazoMeses = filter_input(INPUT_POST, 'plazo-credito', FILTER_VALIDATE_INT);
    $tipoSeguro = $_POST['tipo-seguro'] ?? 'sin-seguro';

    // Valida los datos
    $error = validarDatos($dni, $nombre, $email, $montoVehiculo, $porcentajeCuotaInicial, $plazoMeses);

    if ($error) {
        // Si hay error, muestra la vista con el error
        $_SESSION['error'] = $error; // Guarda el error en sesión
        include '../views/creditoVehicular.php';
    } else {
        try {
            // Cálculo del monto capital y cuota inicial
            $montoCapital = $montoVehiculo * (1 - ($porcentajeCuotaInicial / 100));
            $cuotaInicial = $montoVehiculo * ($porcentajeCuotaInicial / 100);
            $tem = (pow(1 + ($tasaEfectivaAnual / 100), (1.0 / 12))) - 1; // Tasa Efectiva Mensual
            $cuotaMensual = $montoCapital * ($tem / (1 - pow(1 + $tem, -$plazoMeses)));

            // Inicializar el cronograma de pagos
            $cronograma = [];
            $saldoCapital = $montoCapital;

            // Calcular el cronograma de pagos
            for ($mes = 1; $mes <= $plazoMeses; $mes++) {
                $interesMensual = $saldoCapital * $tem; // Interés del mes
                $aporteCapital = $cuotaMensual - $interesMensual; // Aporte a capital
                $saldoCapital -= $aporteCapital; // Nuevo saldo capital

                // Calcular el seguro si se aplica (aquí supongamos un 1% mensual del saldo capital)
                $seguro = ($tipoSeguro !== 'sin-seguro') ? ($saldoCapital * 0.01) : 0;

                // Guardar datos en el cronograma
                $cronograma[] = [
                    'mes' => $mes,
                    'aporteCapital' => $aporteCapital,
                    'interesMensual' => $interesMensual,
                    'cuotaMensual' => $cuotaMensual + $seguro,
                    'saldoCapital' => $saldoCapital,
                    'seguro' => $seguro
                ];
            }

            // Guarda los datos en variables de sesión para pasarlos a la vista
            $_SESSION['resultadoCronograma'] = $cronograma;
            $_SESSION['resumen'] = [
                'nombre' => $nombre,
                'dni' => $dni,
                'email' => $email,
                'montoVehiculo' => $montoVehiculo,
                'cuotaInicial' => $cuotaInicial,
                'plazoMeses' => $plazoMeses,
                'tasaEfectivaAnual' => $tasaEfectivaAnual,
                'tipoSeguro' => $tipoSeguro,
            ];

            // Redirige a la vista cronograma_view.php
            header('Location: ../views/cronograma_view.php');
            exit();
        } catch (Exception $e) {
            // Maneja errores de procesamiento
            $_SESSION['error'] = "Ocurrió un error al procesar su solicitud: " . htmlspecialchars($e->getMessage());
            include '../views/creditoVehicular.php';
        }
    }
} else {
    // Redirige a la página de inicio si no es una solicitud POST
    header('Location: /public/index.php');
    exit();
}
?>
