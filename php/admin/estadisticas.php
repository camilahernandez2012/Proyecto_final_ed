<?php
require '../conexion.php';

// Libros más prestados
$masPrestados = $conn->query("
  SELECT libros.titulo, COUNT(*) AS cantidad
  FROM prestamos
  JOIN libros ON prestamos.libro_id = libros.id
  GROUP BY libros.id
  ORDER BY cantidad DESC
  LIMIT 5
");

// Libros mejor valorados
$mejorValorados = $conn->query("
  SELECT titulo, calificacion_promedio
  FROM libros
  WHERE calificacion_promedio IS NOT NULL
  ORDER BY calificacion_promedio DESC
  LIMIT 5
");

$prestadosLabels = $prestadosData = [];
while ($lp = $masPrestados->fetch_assoc()) {
  $prestadosLabels[] = $lp['titulo'];
  $prestadosData[] = $lp['cantidad'];
}

$valoradosLabels = $valoradosData = [];
while ($lv = $mejorValorados->fetch_assoc()) {
  $valoradosLabels[] = $lv['titulo'];
  $valoradosData[] = $lv['calificacion_promedio'];
}

// Totales
$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$totalLibros = $conn->query("SELECT COUNT(*) AS total FROM libros")->fetch_assoc()['total'];
$prestamosActivos = $conn->query("SELECT COUNT(*) AS total FROM prestamos WHERE fecha_devolucion IS NULL")->fetch_assoc()['total'];
?>

<div class="container">
  <h4 class="mb-4">📊 Estadísticas Generales</h4>

  <div class="row text-center mb-4">
    <div class="col-md-4">
      <div class="alert alert-info">👤 Usuarios: <strong><?= $totalUsuarios ?></strong></div>
    </div>
    <div class="col-md-4">
      <div class="alert alert-primary">📚 Libros: <strong><?= $totalLibros ?></strong></div>
    </div>
    <div class="col-md-4">
      <div class="alert alert-warning">⏳ Préstamos activos: <strong><?= $prestamosActivos ?></strong></div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <h5 class="text-center">📈 Libros más prestados</h5>
      <?php if (count($prestadosLabels) > 0): ?>
        <canvas id="graficoPrestados" height="250"></canvas>
      <?php else: ?>
        <p class="text-muted text-center">No hay datos suficientes para mostrar este gráfico.</p>
      <?php endif; ?>
    </div>
    <div class="col-md-6">
      <h5 class="text-center">⭐ Libros mejor valorados</h5>
      <?php if (count($valoradosLabels) > 0): ?>
        <canvas id="graficoValorados" height="250"></canvas>
      <?php else: ?>
        <p class="text-muted text-center">No hay valoraciones suficientes para mostrar este gráfico.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<div id="datos-estadisticas"
     data-prestados-labels='<?= json_encode($prestadosLabels) ?>'
     data-prestados-data='<?= json_encode($prestadosData) ?>'
     data-valorados-labels='<?= json_encode($valoradosLabels) ?>'
     data-valorados-data='<?= json_encode($valoradosData) ?>'>
</div>

