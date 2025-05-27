<?php
class GrafoAfinidad
{
    private $nodos = [];

    public function agregarConexion($usuario1, $usuario2)
    {
        if (!isset($this->nodos[$usuario1])) {
            $this->nodos[$usuario1] = [];
        }
        if (!isset($this->nodos[$usuario2])) {
            $this->nodos[$usuario2] = [];
        }

        $this->nodos[$usuario1][$usuario2] = true;
        $this->nodos[$usuario2][$usuario1] = true;
    }

    public function sugerenciasAmistad($usuarioId)
    {
        if (!isset($this->nodos[$usuarioId])) {
            return [];
        }

        $sugerencias = [];

        foreach ($this->nodos as $otroId => $conexiones) {
            if ($otroId == $usuarioId) continue;

            if (isset($this->nodos[$usuarioId][$otroId])) {
                continue;
            }

            $comunes = array_intersect_key($this->nodos[$usuarioId], $conexiones);
            if (count($comunes) > 0) {
                $sugerencias[] = $otroId;
            }
        }

        return $sugerencias;
    }

    public function exportarConexiones()
{
    $enlaces = [];
    $vistos = [];

    foreach ($this->nodos as $origen => $destinos) {
        foreach ($destinos as $destino => $v) {
            $clave = $origen < $destino ? "$origen-$destino" : "$destino-$origen";
            if (!isset($vistos[$clave])) {
                $enlaces[] = [
                    "source" => $origen,
                    "target" => $destino
                ];
                $vistos[$clave] = true;
            }
        }
    }

    return $enlaces;
}

}
