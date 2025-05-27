<?php
require '../conexion.php';
session_start();

$usuarioId = $_SESSION['usuario_id'];
$q = $conn->real_escape_string($_GET['q'] ?? '');

$res = $conn->query("
  SELECT id, nombre, email 
  FROM usuarios 
  WHERE id != $usuarioId AND 
        (nombre LIKE '%$q%' OR email LIKE '%$q%')
  LIMIT 10
");

while ($u = $res->fetch_assoc()): ?>
  <div class="card mb-2">
    <div class="card-body">
      <h5><?= htmlspecialchars($u['nombre']) ?></h5>
      <p><?= htmlspecialchars($u['email']) ?></p>
      <button class="btn btn-outline-primary btn-sm" onclick="enviarMensaje(<?= $u['id'] ?>)">💬 Enviar mensaje</button>
    </div>
  </div>
<?php endwhile; ?>
