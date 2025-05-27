<?php
session_start();
require '../conexion.php';

$usuarioId = $_SESSION['usuario_id'] ?? 0;
if (!$usuarioId) {
  echo "Debes iniciar sesión.";
  exit;
}

$res = $conn->query("
  SELECT m.contenido, m.fecha_envio, u.nombre AS remitente
  FROM mensajes m
  JOIN usuarios u ON m.de_id = u.id
  WHERE m.para_id = $usuarioId
  ORDER BY m.fecha_envio DESC
");
?>

<div class="container">
  <h3 class="mb-4">📨 Mensajes recibidos</h3>

  <?php if ($res->num_rows === 0): ?>
    <p class="text-muted">No tienes mensajes aún.</p>
  <?php else: ?>
    <div class="list-group">
      <?php while ($m = $res->fetch_assoc()): ?>
        <div class="list-group-item">
          <h5 class="mb-1">De: <?= htmlspecialchars($m['remitente']) ?></h5>
          <p class="mb-1"><?= nl2br(htmlspecialchars($m['contenido'])) ?></p>
          <small class="text-muted"><?= $m['fecha_envio'] ?></small>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>
