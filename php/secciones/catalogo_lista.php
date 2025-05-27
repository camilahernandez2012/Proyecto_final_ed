<?php
session_start();
require '../conexion.php';

$busqueda = $_GET['busqueda'] ?? '';
$categoria = $_GET['categoria'] ?? '';
$usuarioId = $_SESSION['usuario_id'] ?? 0;

$sql = "SELECT * FROM libros WHERE 1";

if ($busqueda) {
    $busqueda = $conn->real_escape_string($busqueda);
    $sql .= " AND (titulo LIKE '%$busqueda%' OR autor LIKE '%$busqueda%')";
}

if ($categoria) {
    $categoria = $conn->real_escape_string($categoria);
    $sql .= " AND categoria = '$categoria'";
}

$sql .= " ORDER BY titulo";
$resultado = $conn->query($sql);

// en esta parte mostramos todo consecuente a los estados del usuario en la lista del catalogo, es decir, los  botones e informaciones cambian de acuerdo a lo registradio
?>

<table class="table table-bordered table-hover">
  <thead class="table-light">
    <tr>
      <th>Título</th>
      <th>Autor</th>
      <th>Año</th>
      <th>Categoría</th>
      <th>Estado</th>
      <th>Calificación</th>
      <th>Acción</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($libro = $resultado->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($libro['titulo']) ?></td>
        <td><?= htmlspecialchars($libro['autor']) ?></td>
        <td><?= $libro['anio'] ?></td>
        <td><?= $libro['categoria'] ?></td>
        <td><?= $libro['estado'] ?></td>
        <td><?= number_format($libro['calificacion_promedio'], 1) ?> ⭐</td>
        <td>
          <?php
          $libroId = $libro['id'];

          $prestado = $conn->query("SELECT id FROM prestamos WHERE usuario_id = $usuarioId AND libro_id = $libroId AND fecha_devolucion IS NULL")->num_rows > 0;

          $enCola = $conn->query("SELECT id FROM colas WHERE usuario_id = $usuarioId AND libro_id = $libroId AND atendido = 0")->num_rows > 0;

          $totalEnCola = $conn->query("SELECT COUNT(*) AS total FROM colas WHERE libro_id = $libroId AND atendido = 0")->fetch_assoc()['total'];

          if ($libro['estado'] === 'disponible' && !$prestado) {
              echo "<button class='btn btn-primary btn-sm' onclick='alquilarLibro($libroId, this)' data-libro='$libroId'>📚 Alquilar</button>";
          } elseif ($prestado) {
              echo "<span class='text-success'>✅ Lo tienes</span>";
          } elseif ($enCola) {
              echo "<span class='text-warning'>🕒 En espera ($totalEnCola persona" . ($totalEnCola == 1 ? '' : 's') . " esperando)</span>";
          } else {
              echo "<button class='btn btn-secondary btn-sm' onclick='unirseCola($libroId, this)' data-libro='$libroId'>🕒 Unirse a la cola ($totalEnCola persona" . ($totalEnCola == 1 ? '' : 's') . " esperando)</button>";
          }
          ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
