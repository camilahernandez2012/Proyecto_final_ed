<?php
session_start();
require '../conexion.php';
require_once '../../estructuras/ListaEnlazadaValoraciones.php';
require_once '../../estructuras/ListaSugerencias.php';

$usuarioId = $_SESSION['usuario_id'] ?? 0;

$listaValoraciones = new ListaEnlazadaValoraciones();

$res = $conn->query("SELECT libro_id, puntuacion FROM valoraciones WHERE usuario_id = $usuarioId");
while ($row = $res->fetch_assoc()) {
    $listaValoraciones->insertar($row['libro_id'], $row['puntuacion']);
}

$valoradasPorUsuario = $listaValoraciones->recorrer();

$usuariosSimilares = [];

foreach ($valoradasPorUsuario as $v) {
    $libro_id = $v['libro_id'];
    $puntaje = $v['puntuacion'];

    $similares = $conn->query("
        SELECT usuario_id
        FROM valoraciones
        WHERE libro_id = $libro_id
          AND usuario_id != $usuarioId
          AND ABS(puntuacion - $puntaje) <= 1
    ");

    while ($row = $similares->fetch_assoc()) {
        $usuariosSimilares[] = $row['usuario_id'];
    }
}

$usuariosSimilares = array_unique($usuariosSimilares);

$listaSugerencias = new ListaSugerencias();

foreach ($usuariosSimilares as $otroUsuario) {
    $res = $conn->query("
        SELECT libro_id, AVG(puntuacion) as promedio
        FROM valoraciones
        WHERE usuario_id = $otroUsuario AND puntuacion >= 4
        GROUP BY libro_id
    ");

    while ($row = $res->fetch_assoc()) {
        if (!$listaValoraciones->buscar($row['libro_id'])) {
            $listaSugerencias->insertarSiNoExiste($row['libro_id'], $row['promedio']);
        }
    }
}

$listaSugerencias->ordenarPorPuntaje();
$sugeridos = $listaSugerencias->recorrer();

// en este archivo se recomiendan libros que el usuario no haya valorado aun, comparando las valoraciones en una lista enlazada y damos 
// la opcion de prestar el libro y si lo toma actualiza el estado
?>

<div class="container">
    <h4 class="mb-4">📚 Libros recomendados según tus gustos</h4>

    <?php if (count($sugeridos) === 0): ?>
        <p class="text-muted">Aún no hay recomendaciones. Valora más libros para obtener mejores sugerencias.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($sugeridos as $s):
                $libro = $conn->query("SELECT * FROM libros WHERE id = {$s['libro_id']}")->fetch_assoc();
            ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($libro['titulo']) ?></h5>
                            <p class="card-text">
                                Autor: <?= htmlspecialchars($libro['autor']) ?><br>
                                Categoría: <?= htmlspecialchars($libro['categoria']) ?><br>
                                Promedio recomendado: <?= number_format($s['promedio_valoracion'], 1) ?> ⭐
                            </p>
                            <?php
                            $tieneLibro = $conn->query("SELECT id FROM prestamos WHERE usuario_id = $usuarioId AND libro_id = {$libro['id']} AND fecha_devolucion IS NULL")->num_rows > 0;

                            if ($tieneLibro) {
                                echo "<span class='text-success'>✅ Ya lo tienes en préstamo</span>";
                            } elseif ($libro['estado'] === 'disponible') {
                                echo "<button class='btn btn-primary btn-sm' onclick='alquilarLibro({$libro['id']}, this)'>📚 Alquilar</button>";
                            } else {
                                echo "<button class='btn btn-secondary btn-sm' onclick='unirseCola({$libro['id']}, this)'>🕒 Unirse a la cola</button>";
                            }
                            ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>