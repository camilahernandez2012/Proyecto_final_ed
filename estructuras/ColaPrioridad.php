<?php
// cola de Prioridad para libros prestados

class NodoCola {
    public $usuarioId;
    public $siguiente;

    public function __construct($usuarioId) {
        $this->usuarioId = $usuarioId;
        $this->siguiente = null;
    }
}

class ColaPrioridad {
    /** @var ?NodoCola */
    private $frente;
    /** @var ?NodoCola */
    private $final;

    public function __construct() {
        $this->frente = null;
        $this->final = null;
    }

    // Agregar
    public function encolar($usuarioId) {
        $nuevo = new NodoCola($usuarioId);
        if ($this->esVacia()) {
            $this->frente = $nuevo;
            $this->final = $nuevo;
        } else {
            if ($this->final !== null) {
                $this->final->siguiente = $nuevo;
            }
            $this->final = $nuevo;
        }
    }

    // Sacar 
    public function desencolar() {
        if ($this->esVacia()) return null;
        $temp = $this->frente;
        $this->frente = $this->frente->siguiente;
        if ($this->frente === null) $this->final = null;
        return $temp->usuarioId;
    }

    public function esVacia() {
        return $this->frente === null;
    }

    public function verPrimero() {
        return $this->frente ? $this->frente->usuarioId : null;
    }

    public function imprimir() {
        $actual = $this->frente;
        while ($actual !== null) {
            echo $actual->usuarioId . " -> ";
            $actual = $actual->siguiente;
        }
        echo "NULL\n";
    }
}
?>
