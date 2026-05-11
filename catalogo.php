<?php include 'conexion.php';

$cat      = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$sub      = isset($_GET['sub'])       ? $_GET['sub']       : '';
$busqueda = isset($_GET['buscar'])    ? $_GET['buscar']    : '';

$sql    = "SELECT * FROM libros WHERE 1=1";
$params = [];

if($cat)      { $sql .= " AND categoria = ?";                    $params[] = $cat; }
if($sub)      { $sql .= " AND subcategoria = ?";                 $params[] = $sub; }
if($busqueda) { $sql .= " AND (titulo LIKE ? OR autor LIKE ?)";  $params[] = "%$busqueda%"; $params[] = "%$busqueda%"; }

$sql .= " ORDER BY titulo ASC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total  = count($libros);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Catálogo – Lumiere</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" type="image/png"/>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet"/>
    <link href="css/styles.css" rel="stylesheet"/>
    <style>
        body { padding-top: 80px; }
        .libro-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .libro-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .badge-cat { background-color: #ffc800; color: #212529; font-weight: 700; }
        .stock-ok { color: #198754; font-weight: 600; }
        .stock-agotado { color: #dc3545; font-weight: 600; }
        .filtros-bar { background: #212529; padding: 1rem 0; margin-bottom: 2rem; }
        .btn-filtro { color: #fff; border-color: #fff; margin: 0.2rem; }
        .btn-filtro.active, .btn-filtro:hover { background-color: #ffc800; color: #212529; border-color: #ffc800; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background:#212529;">
    <div class="container">
        <a class="navbar-brand" href="index.html" style="font-family:'Montserrat',sans-serif; font-weight:700; font-size:1.4rem;">
            <i class="fa-solid fa-book me-2" style="color:#ffc800;"></i>
            <span style="color:#ffc800;">Lumiere</span>
        </a>
        <div class="ms-auto d-flex gap-2">
            <a href="agregar.php" class="btn btn-primary btn-sm text-uppercase fw-bold">
                <i class="fas fa-plus me-1"></i>Agregar libro
            </a>
            <a href="index.html" class="btn btn-outline-light btn-sm">
                <i class="fas fa-home me-1"></i>Inicio
            </a>
        </div>
    </div>
</nav>

<div class="filtros-bar">
    <div class="container text-center">
        <a href="catalogo.php" class="btn btn-sm btn-filtro <?= $cat=='' ? 'active' : '' ?>">Todos</a>
        <a href="catalogo.php?categoria=novela" class="btn btn-sm btn-filtro <?= $cat=='novela' ? 'active' : '' ?>">Novelas</a>
        <a href="catalogo.php?categoria=cuento" class="btn btn-sm btn-filtro <?= $cat=='cuento' ? 'active' : '' ?>">Cuentos</a>
        <a href="catalogo.php?categoria=ciencia" class="btn btn-sm btn-filtro <?= $cat=='ciencia' ? 'active' : '' ?>">Ciencias</a>
        <a href="catalogo.php?categoria=conocimiento" class="btn btn-sm btn-filtro <?= $cat=='conocimiento' ? 'active' : '' ?>">Conocimientos</a>
        <a href="catalogo.php?categoria=comic" class="btn btn-sm btn-filtro <?= $cat=='comic' ? 'active' : '' ?>">Comics</a>
        <a href="catalogo.php?categoria=manga" class="btn btn-sm btn-filtro <?= $cat=='manga' ? 'active' : '' ?>">Manga</a>
    </div>
</div>

<div class="container mb-4">
    <form method="GET" action="catalogo.php" class="d-flex gap-2">
        <?php if($cat): ?><input type="hidden" name="categoria" value="<?= htmlspecialchars($cat) ?>"><?php endif; ?>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por título o autor..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0" style="font-family:'Montserrat',sans-serif;">
            <?php
            if($sub)      echo "Subcategoría: " . ucfirst(str_replace('_',' ',$sub));
            elseif($cat)  echo "Categoría: " . ucfirst($cat);
            elseif($busqueda) echo "Resultados: \"$busqueda\"";
            else          echo "Todos los libros";
            ?>
        </h4>
        <a href="agregar.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Agregar
        </a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php
            $msgs = [
                'agregado'  => 'Libro agregado correctamente.',
                'editado'   => 'Libro actualizado correctamente.',
                'eliminado' => 'Libro eliminado correctamente.',
                'comprado'  => 'Compraste: ' . htmlspecialchars($_GET['titulo'] ?? '') . '. Stock actualizado.',
                'agotado'   => 'Este libro está agotado.',
            ];
            echo $msgs[$_GET['msg']] ?? '';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <p class="text-muted mb-4"><?= $total ?> libro(s) encontrado(s)</p>

    <?php if($total == 0): ?>
        <div class="text-center py-5">
            <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No se encontraron libros</h5>
            <a href="agregar.php" class="btn btn-primary mt-2">Agregar el primero</a>
        </div>
    <?php else: ?>
    <div class="row">
        <?php foreach($libros as $libro): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card libro-card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-3" style="font-size:3rem; color:#ffc800;">
                        <?php
                        $iconos = [
                            'novela'      => 'fa-book',
                            'cuento'      => 'fa-moon',
                            'ciencia'     => 'fa-flask',
                            'conocimiento'=> 'fa-brain',
                            'comic'       => 'fa-mask',
                            'manga'       => 'fa-dragon'
                        ];
                        $icono = $iconos[$libro['categoria']] ?? 'fa-book';
                        ?>
                        <i class="fas <?= $icono ?>"></i>
                    </div>
                    <span class="badge badge-cat mb-2 align-self-start"><?= ucfirst($libro['categoria']) ?></span>
                    <h5 class="card-title" style="font-size:1rem; font-family:'Montserrat',sans-serif;"><?= htmlspecialchars($libro['titulo']) ?></h5>
                    <p class="text-muted mb-1" style="font-size:0.85rem;"><i class="fas fa-user me-1"></i><?= htmlspecialchars($libro['autor']) ?></p>
                    <p class="text-muted mb-2" style="font-size:0.8rem;"><?= $libro['anio'] ?></p>
                    <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars(substr($libro['sinopsis'], 0, 80)) ?>...</p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold fs-5">$<?= number_format($libro['precio'], 2) ?></span>
                            <?php if($libro['stock'] > 0): ?>
                                <span class="stock-ok"><i class="fas fa-check-circle me-1"></i><?= $libro['stock'] ?> disp.</span>
                            <?php else: ?>
                                <span class="stock-agotado"><i class="fas fa-times-circle me-1"></i>Agotado</span>
                            <?php endif; ?>
                        </div>
                        <?php if($libro['stock'] > 0): ?>
                        <form method="POST" action="comprar.php" class="mb-2">
                            <input type="hidden" name="id" value="<?= $libro['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-shopping-cart me-1"></i>Comprar
                            </button>
                        </form>
                        <?php else: ?>
                        <button class="btn btn-secondary btn-sm w-100 mb-2" disabled>Agotado</button>
                        <?php endif; ?>
                        <div class="d-flex gap-1">
                            <a href="editar.php?id=<?= $libro['id'] ?>" class="btn btn-outline-dark btn-sm flex-fill">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <a href="eliminar.php?id=<?= $libro['id'] ?>"
                               class="btn btn-outline-danger btn-sm flex-fill"
                               onclick="return confirm('¿Eliminar este libro?')">
                                <i class="fas fa-trash me-1"></i>Eliminar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>