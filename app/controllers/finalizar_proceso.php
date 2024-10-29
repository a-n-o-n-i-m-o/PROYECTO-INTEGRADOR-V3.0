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
    $tipoSeguro = $_POST['tipoSeguro'] ?? null; // Asegúrate de que este campo esté en tu formulario
    $tea = 15; // Tasa de interés anual

    try {
        $database = new Database();
        $db = $database->getConnection();

        // Insertar en tabla `cliente`
        $queryCliente = "INSERT INTO cliente (nombre, apellidos, dni, fechaNacimiento, direccion, telefono, correo, ingresoMensual) 
                         VALUES (:nombreCliente, :apellidosCliente, :dni, :fechaNacimiento, :direccionCliente, :telefonoCliente, :emailCliente, :ingresoMensual)";
        $stmtCliente = $db->prepare($queryCliente);
        $stmtCliente->bindParam(':nombreCliente', $nombreCliente);
        $stmtCliente->bindParam(':apellidosCliente', $apellidosCliente);
        $stmtCliente->bindParam(':dni', $dni);
        $stmtCliente->bindParam(':fechaNacimiento', $fechaNacimiento);
        $stmtCliente->bindParam(':direccionCliente', $direccionCliente);
        $stmtCliente->bindParam(':telefonoCliente', $telefonoCliente);
        $stmtCliente->bindParam(':emailCliente', $emailCliente);
        $stmtCliente->bindParam(':ingresoMensual', $ingresoMensual);

        if (!$stmtCliente->execute()) {
            echo "Error al registrar cliente: " . implode(" - ", $stmtCliente->errorInfo());
            exit;
        }

        // Obtener el ID del cliente recién insertado
        $clienteId = $db->lastInsertId();

        // Insertar en tabla `vehiculo`
        $queryVehiculo = "INSERT INTO vehiculo (marca, modelo) VALUES (:marcaVehiculo, :modeloVehiculo)";
        $stmtVehiculo = $db->prepare($queryVehiculo);
        $stmtVehiculo->bindParam(':marcaVehiculo', $marcaVehiculo);
        $stmtVehiculo->bindParam(':modeloVehiculo', $modeloVehiculo);

        if (!$stmtVehiculo->execute()) {
            echo "Error al registrar vehículo: " . implode(" - ", $stmtVehiculo->errorInfo());
            exit;
        }

        // Obtener el ID del vehículo recién insertado
        $vehiculoId = $db->lastInsertId();

        // Asignar `id_seguro` según el tipo de seguro
        if ($tipoSeguro === 'sin_seguro') {
            $id_seguro = 1; // Id para "Sin Seguro"
        } elseif ($tipoSeguro === 'sgrabaven') {
            $id_seguro = 2; // Id para "Con Seguro"
        } else {
            $id_seguro = 1; // Id para "Sin Seguro"
        }

        // Insertar en tabla `credito_vehicular`
        $queryCredito = "INSERT INTO credito_vehicular (vehiculo_id, Cliente_id, montoTotal, cuotaInicial, plazo, tea, id_seguro, fechaSolicitud) 
                         VALUES (:vehiculo_id, :Cliente_id, :montoTotal, :cuotaInicial, :plazo, :tea, :id_seguro, NOW())";
        $stmtCredito = $db->prepare($queryCredito);
        $stmtCredito->bindParam(':vehiculo_id', $vehiculoId);
        $stmtCredito->bindParam(':Cliente_id', $clienteId);
        $stmtCredito->bindParam(':montoTotal', $montoVehiculo);
        $stmtCredito->bindParam(':cuotaInicial', $cuotaInicial);
        $stmtCredito->bindParam(':plazo', $plazo);
        $stmtCredito->bindParam(':tea', $tea);
        $stmtCredito->bindParam(':id_seguro', $id_seguro, PDO::PARAM_INT);

        if (!$stmtCredito->execute()) {
            echo "Error al registrar crédito vehicular: " . implode(" - ", $stmtCredito->errorInfo());
            exit;
        }

        // Mensaje de éxito y redirección
        echo "<script>
                alert('Proceso finalizó con éxito.');
                window.location.href = '../views/consultar_credito.php?dni=" . $dni . "';
              </script>";
    } catch (Exception $e) {
        echo "Error general: " . $e->getMessage();
    }
}
?>
