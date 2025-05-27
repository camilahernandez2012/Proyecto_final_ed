<?php
class NodoValoracion {
    public $libro_id;
    public $puntuacion;
    public $siguiente;

    public function __construct($libro_id, $puntuacion) {
        $this->libro_id = $libro_id;
        $this->puntuacion = $puntuacion;
        $this->siguiente = null;
    }
}

class ListaEnlazadaValoraciones {
    private $cabeza = null;

    public function insertar($libro_id, $puntuacion) {
        $nuevo = new NodoValoracion($libro_id, $puntuacion);
        $nuevo->siguiente = $this->cabeza;
        $this->cabeza = $nuevo;
    }

    public function buscar($libro_id) {
        $actual = $this->cabeza;
        while ($actual !== null) {
            if ($actual->libro_id == $libro_id) return $actual;
            $actual = $actual->siguiente;
        }
        return null;
    }

    public function recorrer() {
        $valores = [];
        $actual = $this->cabeza;
        while ($actual !== null) {
            $valores[] = [
                'libro_id' => $actual->libro_id,
                'puntuacion' => $actual->puntuacion
            ];
            $actual = $actual->siguiente;
        }
        return $valores;
    }
}
