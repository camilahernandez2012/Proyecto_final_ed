<?php
require '../conexion.php';

$filtro = $_GET['filtro'] ?? '';
$sql = "SELECT * FROM usuarios WHERE rol = 'lector'";

if (!empty($filtro)) {
  $filtro = $conn->real_escape_string($filtro);
  $sql .= " AND (nombre LIKE '%$filtro%' OR email LIKE '%$filtro%')";
}

$sql .= " ORDER BY nombre";
$resultado = $conn->query($sql);

//seccion para mostarr los usuarios y opcion de eliminarlos o ver mas datos, asi como filtrar por nombre o email
?>

<div class="container">
  <h4 class="mb-3">👥 Lectores registrados</h4>

  <div class="row mb-3">
    <div class="col-md-6">
      <input type="text" id="busqueda-lector" class="form-control" placeholder="Buscar por nombre o correo...">
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary" onclick="filtrarLectores()">🔍 Buscar</button>
    </div>
  </div>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>Nombre</th>
        <th>Email</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody id="tabla-lectores">
      <?php while ($u = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($u['nombre']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td>
            <button class="btn btn-info btn-sm" onclick="verDetallesUsuario(<?= $u['id'] ?>)">Ver más</button>
            <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(<?= $u['id'] ?>)">Eliminar</button>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
