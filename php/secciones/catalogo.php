<?php
require '../conexion.php';

$categorias = $conn->query("SELECT DISTINCT categoria FROM libros ORDER BY categoria");

// catallogo, vista principal de los lectores, aqui lo primero que ven son los libros, permite buscar por nombre o autor y carga automaticamente lo que actualiza el admin
?>

<div class="container">
  <h4 class="mb-4">📚 Catálogo de Libros</h4>

  <div class="row mb-3">
    <div class="col-md-4">
      <input type="text" id="busqueda" class="form-control" placeholder="Buscar por título o autor...">
    </div>
    <div class="col-md-3">
      <select id="filtro-categoria" class="form-select">
        <option value="">Todas las categorías</option>
        <?php while ($cat = $categorias->fetch_assoc()): ?>
          <option value="<?= $cat['categoria'] ?>"><?= $cat['categoria'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary" onclick="filtrarLibros()">Buscar</button>
    </div>
  </div>

  <div id="lista-libros">
  </div>
</div>

