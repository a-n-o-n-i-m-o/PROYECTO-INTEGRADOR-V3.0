<?php
// /app/models/CreditoVehicular.php

class CreditoVehicular {
    private $dni;
    private $montoVehiculo;
    private $porcentajeCuotaInicial;
    private $tasaEfectivaAnual;
    private $plazoMeses;
    private $tipoSeguro;

    public function __construct($dni, $montoVehiculo, $porcentajeCuotaInicial, $plazoMeses, $tasaEfectivaAnual = 15, $tipoSeguro = 'sin_seguro') {
        if (!$this->validarDatos($dni, $montoVehiculo, $porcentajeCuotaInicial, $plazoMeses)) {
            throw new InvalidArgumentException("Datos inválidos proporcionados.");
        }

        $this->dni = $dni;
        $this->montoVehiculo = $montoVehiculo;
        $this->porcentajeCuotaInicial = $porcentajeCuotaInicial / 100;
        $this->tasaEfectivaAnual = $tasaEfectivaAnual / 100; // TEA a 15%
        $this->plazoMeses = $plazoMeses;
        $this->tipoSeguro = $tipoSeguro; // Asignar tipo de seguro
    }

    private function validarDatos($dni, $montoVehiculo, $porcentajeCuotaInicial, $plazoMeses) {
        return is_numeric($dni) && strlen((string)$dni) === 8
            && is_numeric($montoVehiculo) && $montoVehiculo > 0
            && is_numeric($porcentajeCuotaInicial) && $porcentajeCuotaInicial >= 0 && $porcentajeCuotaInicial <= 100
            && is_numeric($plazoMeses) && in_array($plazoMeses, range(12, 72));
    }

    public function calcularMontoCapital() {
        return $this->montoVehiculo * (1 - $this->porcentajeCuotaInicial);
    }

    public function calcularCuotaInicial() {
        return $this->montoVehiculo * $this->porcentajeCuotaInicial;
    }

    public function calcularTEM() {
        return pow(1 + $this->tasaEfectivaAnual, 1 / 12) - 1;
    }

    public function calcularCuotaMensual() {
        $montoCapital = $this->calcularMontoCapital();
        $tem = $this->calcularTEM();
        return round($montoCapital * ($tem / (1 - pow(1 + $tem, -$this->plazoMeses))), 2);
    }

    public function calcularSeguroMensual($saldoCapital) {
        if ($this->tipoSeguro === 'sgrabaven') {
            return $saldoCapital * 0.00077; // 0.077% del saldo capital
        }
        return 0; // Sin seguro
    }

    public function generarCronogramaPagos() {
        $montoCapital = $this->calcularMontoCapital();
        $tem = $this->calcularTEM();
        $cuotaMensual = $this->calcularCuotaMensual();
        $saldoCapital = $montoCapital;
        $cronograma = [];
        $totalInteres = 0;
        $totalSeguro = 0;

        // Cabecera del cronograma con información del seguro
        $cabecera = [
            'tipoSeguro' => $this->tipoSeguro,
            'montoVehiculo' => $this->montoVehiculo,
            'cuotaInicial' => $this->calcularCuotaInicial(),
            'montoCapital' => $montoCapital,
            'plazoMeses' => $this->plazoMeses,
        ];

        for ($mes = 1; $mes <= $this->plazoMeses; $mes++) {
            $interesMensual = $saldoCapital * $tem;
            $montoAbonoCapital = $cuotaMensual - $interesMensual;
            $saldoCapital -= $montoAbonoCapital;
            
            // Calcular seguro mensual si corresponde
            $seguroMensual = $this->calcularSeguroMensual($saldoCapital);
            $totalSeguro += $seguroMensual;

            // Ajuste final
            if ($mes === $this->plazoMeses) {
                $montoAbonoCapital += $saldoCapital;
                $saldoCapital = 0.00;
            }

            $totalInteres += $interesMensual;

            $cronograma[] = [
                'mes' => $mes,
                'montoCapital' => round($montoAbonoCapital, 2),
                'interes' => round($interesMensual, 2),
                'cuotaMensual' => round($cuotaMensual, 2),
                'saldoCapital' => round($saldoCapital, 2),
                'seguro' => round($seguroMensual, 2) // Añadir seguro si corresponde
            ];
        }

        return ['cabecera' => $cabecera, 'cronograma' => $cronograma, 'totalInteres' => round($totalInteres, 2), 'totalSeguro' => round($totalSeguro, 2)];
    }

    public function obtenerResumen() {
        $resultadoCronograma = $this->generarCronogramaPagos();
        $totalInteres = $resultadoCronograma['totalInteres'];
        $totalSeguro = $resultadoCronograma['totalSeguro'];

        return [
            'montoTotalVehiculo' => $this->montoVehiculo,
            'cuotaInicial' => round($this->calcularCuotaInicial(), 2),
            'montoCapital' => round($this->calcularMontoCapital(), 2),
            'totalInteres' => round($totalInteres, 2),
            'totalSeguro' => round($totalSeguro, 2),
            'montoTotalPagar' => round($this->calcularMontoCapital() + $totalInteres + $totalSeguro, 2)
        ];
    }
}
?>
