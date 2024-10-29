<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=KoHo&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'KoHo', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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

        h2 {
            color: #002f6c;
            text-align: center;
        }

        main {
            flex: 1;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 150px); /* header y footer */
        }

        .container {
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form, .resultados {
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
            font-size: 16px;
            color: white;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #218838;
        }

        footer {
            width: 100%;
            margin-top: auto;
        }

    </style>
</head>
<body>

<header>
    <h1>Consultar Crédito Vehicular</h1>
</header>

<main>
    <div class="container">
        <form method="POST" action="">
            <label for="dni">Inserte su DNI:</label>
            <input type="text" id="dni" name="dni" required>
            <button type="submit">Consultar</button>
        </form>

        <?php
        require_once '../models/conexion.php';

        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = trim($_POST['dni'] ?? null);

            if ($dni) {
                echo "<div class='consulta'>DNI ingresado: " . htmlspecialchars($dni) . "</div>";

                try {
                    $database = new Database();
                    $db = $database->getConnection();

                    if (!$db) {
                        die("<div class='error'>Error de conexión: " . implode(", ", $db->errorInfo()) . "</div>");
                    }

                    // Consulta para obtener el Cliente_id basado en el DNI
                    $queryCliente = "SELECT Cliente_id, nombre, apellidos, telefono, correo FROM cliente WHERE dni = :dni";
                    $stmtCliente = $db->prepare($queryCliente);
                    $stmtCliente->bindParam(':dni', $dni);

                    if (!$stmtCliente->execute()) {
                        die("<div class='error'>Error en la ejecución de la consulta de cliente: " . implode(", ", $stmtCliente->errorInfo()) . "</div>");
                    }

                    $resultadoCliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

                    if ($resultadoCliente) {
                        // Mostrar datos del cliente
                        echo "<div class='resultados'>
                                <h2>Datos del Cliente</h2>
                                <p><strong>Nombre:</strong> " . htmlspecialchars($resultadoCliente['nombre']) . " " . htmlspecialchars($resultadoCliente['apellidos']) . "</p>
                                <p><strong>DNI:</strong> " . htmlspecialchars($dni) . "</p>
                                <p><strong>Teléfono:</strong> " . htmlspecialchars($resultadoCliente['telefono']) . "</p>
                                <p><strong>Correo:</strong> " . htmlspecialchars($resultadoCliente['correo']) . "</p>";

                        $clienteId = $resultadoCliente['Cliente_id'];
                        $queryCredito = "
                            SELECT cv.montoTotal, cv.cuotaInicial, cv.plazo, cv.tea, cv.fechaSolicitud, v.marca, v.modelo
                            FROM credito_vehicular cv
                            JOIN vehiculo v ON cv.vehiculo_id = v.vehiculo_id
                            WHERE cv.Cliente_id = :cliente_id
                        ";
                        $stmtCredito = $db->prepare($queryCredito);
                        $stmtCredito->bindParam(':cliente_id', $clienteId);

                        if (!$stmtCredito->execute()) {
                            die("<div class='error'>Error en la ejecución de la consulta de crédito: " . implode(", ", $stmtCredito->errorInfo()) . "</div>");
                        }

                        $resultadoCredito = $stmtCredito->fetch(PDO::FETCH_ASSOC);

                        if ($resultadoCredito) {
                            echo "<h2>Datos del Crédito</h2>
                                  <p><strong>Marca del Vehículo:</strong> " . htmlspecialchars($resultadoCredito['marca']) . "</p>
                                  <p><strong>Modelo del Vehículo:</strong> " . htmlspecialchars($resultadoCredito['modelo']) . "</p>
                                  <p><strong>Monto Total:</strong> " . htmlspecialchars($resultadoCredito['montoTotal']) . "</p>
                                  <p><strong>% Cuota Inicial:</strong> " . htmlspecialchars($resultadoCredito['cuotaInicial']) . "</p>
                                  <p><strong>Plazo (meses):</strong> " . htmlspecialchars($resultadoCredito['plazo']) . "</p>
                                  <p><strong>TEA:</strong> " . htmlspecialchars($resultadoCredito['tea']) . "%</p>
                                  <p><strong>Fecha de Solicitud:</strong> " . htmlspecialchars($resultadoCredito['fechaSolicitud']) . "</p>";

                            echo '<form method="POST" action="cronograma.php">
                                    <input type="hidden" name="montoVehiculo" value="' . htmlspecialchars($resultadoCredito['montoTotal']) . '">
                                    <input type="hidden" name="cuotaInicial" value="' . htmlspecialchars($resultadoCredito['cuotaInicial']) . '">
                                    <input type="hidden" name="plazo" value="' . htmlspecialchars($resultadoCredito['plazo']) . '">
                                    <input type="hidden" name="tipoSeguro" value="sin_seguro">
                                    <button type="submit">Generar Cronograma</button>
                                  </form>';
                        } else {
                            echo "<p>No se encontraron datos de crédito para este cliente.</p>";
                        }
                        echo "</div>";
                    } else {
                        echo "<p>No se encontraron datos para el DNI ingresado.</p>";
                    }
                } catch (Exception $e) {
                    echo "<div class='error'>Error al consultar los datos: " . $e->getMessage() . "</div>";
                }
            }
        }
        ?>
    </div>
</main>

<footer>
    <p>&copy; © 2024 BCP | Todos los derechos reservados. Sede Central, Centenario 156, La Molina 15026, Lima, Perú. BANCO DE CREDITO DEL PERU S.A - RUC 20100047218</p>
</footer>

</body>
</html>
