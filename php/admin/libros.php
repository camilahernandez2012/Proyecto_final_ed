<?php
require '../conexion.php';

$sql = "SELECT * FROM libros ORDER BY titulo";
$resultado = $conn->query($sql);

// registro y vista de todos los libros
?>

<div class="container">
  <h4 class="mb-4">📚 Gestión de Libros</h4>

  <form id="form-agregar-libro" class="row g-3 mb-4">
    <div class="col-md-4">
      <input type="text" name="titulo" class="form-control" placeholder="Título" required>
    </div>
    <div class="col-md-3">
      <input type="text" name="autor" class="form-control" placeholder="Autor" required>
    </div>
    <div class="col-md-2">
      <input type="number" name="anio" class="form-control" placeholder="Año" required min="1000" max="9999"
        oninput="if(this.value.length > 4) this.value = this.value.slice(0,4);">
    </div>
    <div class="col-md-2">
      <select name="categoria" class="form-select" required>
        <option value="">Selecciona categoría</option>
        <option value="Ficción">Ficción</option>
        <option value="No Ficción">No Ficción</option>
        <option value="Literatura Clásica">Literatura Clásica</option>
        <option value="Literatura Infantil">Literatura Infantil</option>
        <option value="Ciencia Ficción">Ciencia Ficción</option>
        <option value="Fantasía">Fantasía</option>
        <option value="Misterio / Suspenso">Misterio / Suspenso</option>
        <option value="Romance">Romance</option>
        <option value="Historia">Historia</option>
        <option value="Biografía / Memorias">Biografía / Memorias</option>
        <option value="Autoayuda / Desarrollo Personal">Autoayuda / Desarrollo Personal</option>
        <option value="Ciencia y Tecnología">Ciencia y Tecnología</option>
        <option value="Educación">Educación</option>
        <option value="Filosofía">Filosofía</option>
        <option value="Psicología">Psicología</option>
        <option value="Arte y Cultura">Arte y Cultura</option>
        <option value="Religión / Espiritualidad">Religión / Espiritualidad</option>
        <option value="Economía / Finanzas">Economía / Finanzas</option>
        <option value="Derecho">Derecho</option>
        <option value="Medicina / Salud">Medicina / Salud</option>
      </select>
    </div>
    <div class="col-md-1 d-grid">
      <button type="submit" class="btn btn-success">Agregar</button>
    </div>
  </form>

  <table class="table table-striped">
    <thead>
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
    <tbody id="tabla-libros">
      <?php while ($libro = $resultado->fetch_assoc()): ?>
      <tr>
        <td><?= $libro['titulo'] ?></td>
        <td><?= $libro['autor'] ?></td>
        <td><?= $libro['anio'] ?></td>
        <td><?= $libro['categoria'] ?></td>
        <td><?= $libro['estado'] ?></td>
        <td><?= number_format($libro['calificacion_promedio'], 1) ?> ⭐</td>
        <td>
          <button class="btn btn-sm btn-danger" onclick="eliminarLibro(<?= $libro['id'] ?>)">Eliminar</button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
