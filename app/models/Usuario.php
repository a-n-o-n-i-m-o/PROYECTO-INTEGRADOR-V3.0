<?php
class Usuario {
    private $nombre;
    private $apellidos;
    private $dni;
    private $fechaNacimiento;
    private $direccion;
    private $telefono;
    private $correo;
    private $ingresoMensual;
    private $estadoCivil;
    private $tipoIngreso;

    public function __construct($nombre, $apellidos, $dni, $fechaNacimiento, $direccion, $telefono, $correo, $ingresoMensual, $estadoCivil, $tipoIngreso) {
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->dni = $dni;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->ingresoMensual = $ingresoMensual;
        $this->estadoCivil = $estadoCivil;
        $this->tipoIngreso = $tipoIngreso;
    }

    public function getNombre() { return $this->nombre; }
    public function getApellidos() { return $this->apellidos; }
    public function getDni() { return $this->dni; }
    public function getFechaNacimiento() { return $this->fechaNacimiento; }
    public function getDireccion() { return $this->direccion; }
    public function getTelefono() { return $this->telefono; }
    public function getCorreo() { return $this->correo; }
    public function getIngresoMensual() { return $this->ingresoMensual; }
    public function getEstadoCivil() { return $this->estadoCivil; }
    public function getTipoIngreso() { return $this->tipoIngreso; }
}
