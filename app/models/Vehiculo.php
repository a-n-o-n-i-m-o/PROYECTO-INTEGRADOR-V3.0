<?php
class Vehiculo {
    private $tipoVehiculo;
    private $marca;
    private $modelo;

    public function __construct($tipoVehiculo, $marca, $modelo) {
        $this->tipoVehiculo = $tipoVehiculo;
        $this->marca = $marca;
        $this->modelo = $modelo;
    }

    public function getTipoVehiculo() { return $this->tipoVehiculo; }
    public function getMarca() { return $this->marca; }
    public function getModelo() { return $this->modelo; }
}
