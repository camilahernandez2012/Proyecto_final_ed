<?php
session_start();
require '../conexion.php';

$usuarioId = $_SESSION['usuario_id'] ?? 0;
if (!$usuarioId) {
  echo "Debes iniciar sesión.";
  exit;
}

// consulta de prstamos del usuario
$sql = "
SELECT 
  p.id AS prestamo_id,
  l.id AS libro_id,
  l.titulo,
  l.autor,
  p.fecha_prestamo,
  p.fecha_devolucion,
  (
    SELECT puntuacion
    FROM valoraciones
    WHERE usuario_id = $usuarioId AND libro_id = l.id
    LIMIT 1
  ) AS valoracion
FROM prestamos p
JOIN libros l ON p.libro_id = l.id
WHERE p.usuario_id = $usuarioId
ORDER BY p.fecha_prestamo DESC
";


$resultado = $conn->query($sql);
?>

<div class="container">
  <h4 class="mb-4">📖 Historial de Préstamos</h4>

  <?php if ($resultado->num_rows === 0): ?>
    <p>Aún no has realizado ningún préstamo.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Título</th>
          <th>Autor</th>
          <th>Fecha Préstamo</th>
          <th>Fecha Devolución</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($p = $resultado->fetch_assoc()): ?>
          <tr>
            <td><?= $p['titulo'] ?></td>
            <td><?= $p['autor'] ?></td>
            <td><?= $p['fecha_prestamo'] ?></td>
            <td><?= $p['fecha_devolucion'] ?: '⏳ En préstamo' ?></td>
            <td>
              <?php if (!$p['fecha_devolucion']): ?>
                <button class="btn btn-sm btn-warning" onclick="devolverLibro(<?= $p['libro_id'] ?>)">Devolver</button>
              <?php elseif (is_null($p['valoracion'])): ?>
                <select onchange="valorarLibro(<?= $p['libro_id'] ?>, this.value)">
                  <option value="">⭐ Puntuar</option>
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> estrella<?= $i > 1 ? 's' : '' ?></option>
                  <?php endfor; ?>
                </select>
              <?php else: ?>
                Valorado: <?= $p['valoracion'] ?> ⭐
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
