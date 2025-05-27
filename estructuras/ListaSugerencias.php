<?php
class NodoSugerencia {
    public $libro_id;
    public $promedio_valoracion;
    public $siguiente;

    public function __construct($libro_id, $promedio_valoracion) {
        $this->libro_id = $libro_id;
        $this->promedio_valoracion = $promedio_valoracion;
        $this->siguiente = null;
    }
}

class ListaSugerencias {
    private $cabeza = null;

    public function insertarSiNoExiste($libro_id, $promedio_valoracion) {
        if ($this->buscar($libro_id)) return;

        $nuevo = new NodoSugerencia($libro_id, $promedio_valoracion);
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

    public function ordenarPorPuntaje() {
        $array = $this->recorrer();

        usort($array, function($a, $b) {
            return $b['promedio_valoracion'] <=> $a['promedio_valoracion'];
        });

        $this->cabeza = null;
        foreach ($array as $item) {
            $this->insertarAlFinal($item['libro_id'], $item['promedio_valoracion']);
        }
    }

    private function insertarAlFinal($libro_id, $promedio_valoracion) {
        $nuevo = new NodoSugerencia($libro_id, $promedio_valoracion);
        if ($this->cabeza === null) {
            $this->cabeza = $nuevo;
        } else {
            $actual = $this->cabeza;
            while ($actual->siguiente !== null) {
                $actual = $actual->siguiente;
            }
            $actual->siguiente = $nuevo;
        }
    }

    public function recorrer() {
        $datos = [];
        $actual = $this->cabeza;
        while ($actual !== null) {
            $datos[] = [
                'libro_id' => $actual->libro_id,
                'promedio_valoracion' => $actual->promedio_valoracion
            ];
            $actual = $actual->siguiente;
        }
        return $datos;
    }
}
