<?php
class Mensajes {
  private $mensajes = [];

  public function agregar($de, $para, $contenido) {
    $this->mensajes[] = [
      'de' => $de,
      'para' => $para,
      'contenido' => $contenido,
      'fecha' => date("Y-m-d H:i:s")
    ];
  }

  public function obtener($usuarioId) {
    return array_filter($this->mensajes, function($m) use ($usuarioId) {
      return $m['para'] == $usuarioId;
    });
  }
}
