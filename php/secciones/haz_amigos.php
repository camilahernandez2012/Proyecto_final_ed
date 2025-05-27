<?php
session_start();
require '../conexion.php';
require_once '../../estructuras/GrafoAfinidad.php';

$usuarioId = $_SESSION['usuario_id'] ?? 0;
if (!$usuarioId) {
  echo "Debes iniciar sesión.";
  exit;
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

// Construir grafo de afinidad
$grafo = new GrafoAfinidad();
$usuarios = array_keys($valoracionesPorUsuario);

foreach ($usuarios as $u1) {
  foreach ($usuarios as $u2) {
    if ($u1 >= $u2) continue;
    $comunes = 0;
    foreach ($valoracionesPorUsuario[$u1] as $libro => $p1) {
      if (isset($valoracionesPorUsuario[$u2][$libro])) {
        $p2 = $valoracionesPorUsuario[$u2][$libro];
        if (abs($p1 - $p2) <= 1) {
          $comunes++;
        }
      }
    }
    if ($comunes >= 3) {
      $grafo->agregarConexion($u1, $u2);
    }
  }
}

$sugeridos = $grafo->sugerenciasAmistad($usuarioId);

// Búsqueda de usuarios
$filtro = trim($_GET['filtro'] ?? '');
$usuariosEncontrados = [];
if ($filtro !== '') {
  $f = $conn->real_escape_string($filtro);
  $query = "SELECT id, nombre, email FROM usuarios WHERE id != $usuarioId AND (nombre LIKE '%$f%' OR email LIKE '%$f%')";
  $usuariosEncontrados = $conn->query($query);
}
?>

<div class="container">
  <h3 class="mb-4">🤝 Haz amigos</h3>

  <form method="GET" class="mb-4 d-flex">
    <input type="text" name="filtro" class="form-control me-2" placeholder="Buscar por nombre o email" value="<?= htmlspecialchars($filtro) ?>">
    <button class="btn btn-primary">Buscar</button>
  </form>

  <h4 class="mt-5">👥 Sugerencias de amistad</h4>
  <?php if (empty($sugeridos)): ?>
    <p class="text-muted">No hay sugerencias aún.</p>
  <?php else: ?>
    <div class="row">
      <?php foreach ($sugeridos as $id):
        $u = $conn->query("SELECT nombre, email FROM usuarios WHERE id = $id")->fetch_assoc();
        if (!$u) continue;
      ?>
        <div class="col-md-6 mb-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($u['nombre']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($u['email']) ?></p>
              <button class="btn btn-outline-primary btn-sm" onclick="enviarMensaje(<?= $id ?>)">💬 Enviar mensaje</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <h5 class="mt-5">Buscar usuarios</h5>
  <?php if ($filtro !== ''): ?>
    <div class="row mt-3">
      <?php if ($usuariosEncontrados && $usuariosEncontrados->num_rows > 0):
        while ($u = $usuariosEncontrados->fetch_assoc()): ?>
          <div class="col-md-6 mb-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($u['nombre']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($u['email']) ?></p>
                <button class="btn btn-outline-success btn-sm" onclick="enviarMensaje(<?= $u['id'] ?>)">💬 Enviar mensaje</button>
              </div>
            </div>
          </div>
        <?php endwhile;
      else: ?>
        <p class="text-muted">No se encontraron usuarios con ese nombre o email.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
