<?php
require '../conexion.php';
require_once '../../estructuras/GrafoAfinidad.php';

$usuariosQuery = $conn->query("SELECT id, nombre FROM usuarios");
$usuarios = [];
while ($u = $usuariosQuery->fetch_assoc()) {
  $usuarios[$u['id']] = $u['nombre'];
}

// Cargar valoraciones
$val = $conn->query("SELECT usuario_id, libro_id, puntuacion FROM valoraciones");
$valoracionesPorUsuario = [];

while ($row = $val->fetch_assoc()) {
  $uid = $row['usuario_id'];
  $libro = $row['libro_id'];
  $punt = $row['puntuacion'];
  $valoracionesPorUsuario[$uid][$libro] = $punt;
}

// Construir grafo
$grafo = new GrafoAfinidad();
$ids = array_keys($valoracionesPorUsuario);

foreach ($ids as $u1) {
  foreach ($ids as $u2) {
    if ($u1 >= $u2) continue;
    $comunes = 0;
    foreach ($valoracionesPorUsuario[$u1] as $libro => $p1) {
      if (isset($valoracionesPorUsuario[$u2][$libro]) && abs($p1 - $valoracionesPorUsuario[$u2][$libro]) <= 1) {
        $comunes++;
      }
    }
    if ($comunes >= 3) {
      $grafo->agregarConexion($u1, $u2);
    }
  }
}

header('Content-Type: application/json');
echo json_encode([
  "nodes" => array_map(fn($id) => ["id" => $id, "name" => $usuarios[$id] ?? "Usuario $id"], array_keys($usuarios)),
  "links" => $grafo->exportarConexiones()
]);
    