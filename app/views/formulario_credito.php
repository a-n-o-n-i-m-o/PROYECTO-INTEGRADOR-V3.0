<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=KoHo&display=swap" rel="stylesheet">
    <title>Formulario de Crédito Vehicular</title>
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

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #FF4500;
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        .form-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-column {
            flex: 1;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .form-column h3 {
            text-align: center;
            color: #FF4500;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #002f6c;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            background-color: #fff;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            border-color: #FF4500;
            outline: none;
        }

        .credit-section {
            padding: 20px;
        }

        .credit-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .credit-row label {
            width: 45%;
        }

        .credit-row input,
        .credit-row select {
            width: 50%;
        }

        .slider-container {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .slider-container input {
            flex: 1;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: #FF4500;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #e03d00;
        }

        footer {
            margin-top: 40px;
        }

        footer p {
            font-size: 0.85rem;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <h1>Crédito Vehicular</h1>
    </header>

    <!-- Contenedor Principal -->
    <div class="container">
        <h2>Formulario de Crédito Vehicular</h2>

        <!-- Formulario -->
        <form action="../controllers/procesar_credito.php" method="POST">

            <!-- Sección de Formularios en Tres Columnas -->
            <div class="form-section">

                <!-- Columna Datos del Cliente -->
                <div class="form-column">
                    <h3>Datos del Cliente</h3>
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>

                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" required>

                    <label for="dni">DNI:</label>
                    <input type="number" id="dni" name="dni" required>

                    <label for="fechaNacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fechaNacimiento" name="fechaNacimiento" required>

                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" required>

                    <label for="telefono">Teléfono:</label>
                    <input type="number" id="telefono" name="telefono" required>

                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required>

                    <label for="ingresoMensual">Ingreso Mensual:</label>
                    <input type="number" id="ingresoMensual" name="ingresoMensual" required>
                </div>

                <!-- Columna Datos del Vehículo -->
                <div class="form-column">
                    <h3>Datos del Vehículo</h3>
                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" required>

                    <label for="modelo">Modelo:</label>
                    <input type="text" id="modelo" name="modelo" required>

                    <label for="montoVehiculo">Monto del Vehículo:</label>
                    <input type="number" id="montoVehiculo" name="montoVehiculo" required>
                </div>

                <!-- Columna Datos del Crédito -->
                <div class="form-column credit-section">
                    <h3>Datos del Crédito</h3>
                    <div class="credit-row">
                        <label for="porcentajeCuotaInicial">Porcentaje de Cuota Inicial (%):</label>
                        <input type="number" id="porcentajeCuotaInicial" name="porcentajeCuotaInicial" required>
                    </div>

                    <div class="credit-row">
                        <label>Plazo de Crédito:</label>
                        <div class="slider-container">
                            <input type="range" id="plazoMeses" name="plazoMeses" min="12" max="72" value="24" oninput="this.nextElementSibling.value = this.value">
                            <output>24</output> meses
                        </div>
                    </div>

                    <div class="credit-row">
                        <label for="tipoSeguro">Tipo de Seguro:</label>
                        <select id="tipoSeguro" name="tipoSeguro" required>
                            <option value="sin_seguro">Sin Seguro</option>
                            <option value="sgrabaven">Seguro Grabaven</option>
                        </select>
                    </div>

                    <!-- Botón de Acción -->
                    <button type="submit">Ver Cuota</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2024 BCP | Todos los derechos reservados. Sede Central, Centenario 156, La Molina 15026, Lima, Perú. BANCO DE CREDITO DEL PERU S.A - RUC 20100047218</p>
    </footer>

</body>

</html>
