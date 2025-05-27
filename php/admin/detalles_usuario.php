<?php
require '../conexion.php';

$usuarioId = intval($_GET['id'] ?? 0);

$usuario = $conn->query("SELECT * FROM usuarios WHERE id = $usuarioId")->fetch_assoc();

$prestamos = $conn->query("
  SELECT libros.titulo, prestamos.fecha_prestamo
  FROM prestamos
  JOIN libros ON prestamos.libro_id = libros.id
  WHERE prestamos.usuario_id = $usuarioId AND prestamos.fecha_devolucion IS NULL
");

$valoraciones = $conn->query("
  SELECT libros.titulo, valoraciones.puntuacion AS valor
  FROM valoraciones
  JOIN libros ON valoraciones.libro_id = libros.id
  WHERE valoraciones.usuario_id = $usuarioId
");

$historial = $conn->query("
  SELECT libros.titulo, prestamos.fecha_prestamo, prestamos.fecha_devolucion
  FROM prestamos
  JOIN libros ON prestamos.libro_id = libros.id
  WHERE prestamos.usuario_id = $usuarioId
  ORDER BY prestamos.fecha_prestamo DESC
");

// esto es lo que se carga al dar click en ver mas en los usuarios
?>

<div class="container">
  <h4 class="mb-4">📘 Detalles del lector</h4>
  <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
  <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>

  <hr>

  <h5>📚 Libros actualmente prestados</h5>
  <?php if ($prestamos->num_rows > 0): ?>
    <ul>
      <?php while ($p = $prestamos->fetch_assoc()): ?>
        <li><?= htmlspecialchars($p['titulo']) ?> (<?= $p['fecha_prestamo'] ?>)</li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="text-muted">No tiene libros prestados actualmente.</p>
  <?php endif; ?>

  <hr>

  <h5>⭐ Valoraciones hechas</h5>
  <?php if ($valoraciones->num_rows > 0): ?>
    <ul>
      <?php while ($v = $valoraciones->fetch_assoc()): ?>
        <li><?= htmlspecialchars($v['titulo']) ?> → <?= $v['valor'] ?> estrellas</li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="text-muted">No ha valorado libros aún.</p>
  <?php endif; ?>

  <hr>

  <h5>📖 Historial completo de préstamos</h5>
  <?php if ($historial->num_rows > 0): ?>
    <ul>
      <?php while ($h = $historial->fetch_assoc()): ?>
        <li>
          <?= htmlspecialchars($h['titulo']) ?>  
          desde <?= $h['fecha_prestamo'] ?>
          <?php if ($h['fecha_devolucion']): ?>
            → devuelto el <?= $h['fecha_devolucion'] ?>
          <?php else: ?>
            → <span class="text-warning">Aún no devuelto</span>
          <?php endif; ?>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="text-muted">Sin historial de préstamos.</p>
  <?php endif; ?>

  <hr>
  <button class="btn btn-secondary" onclick="cargarSeccionAdmin('usuarios')">← Volver</button>
</div>
