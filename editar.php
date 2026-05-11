<?php include 'conexion.php';

$id = (int)$_GET['id'];
$stmt = $db->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->execute([$id]);
$libro = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$libro) {
    header("Location: catalogo.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $db->prepare("UPDATE libros SET
        titulo=?, autor=?, categoria=?, subcategoria=?,
        anio=?, precio=?, stock=?, sinopsis=?
        WHERE id=?");

    $stmt->execute([
        $_POST['titulo'],
        $_POST['autor'],
        $_POST['categoria'],
        $_POST['subcategoria'],
        $_POST['anio'],
        $_POST['precio'],
        $_POST['stock'],
        $_POST['sinopsis'],
        $id
    ]);

    header("Location: catalogo.php?msg=editado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Editar Libro – Lumiere</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" type="image/png"/>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet"/>
    <link href="css/styles.css" rel="stylesheet"/>
    <style>
        body { padding-top: 80px; background: #f8f9fa; }
        .form-card {
            max-width: 700px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .form-label { font-weight: 600; font-family: 'Montserrat', sans-serif; }
        .form-control:focus {
            border-color: #ffc800;
            box-shadow: 0 0 0 0.25rem rgba(255,200,0,0.25);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background:#212529;">
    <div class="container">
        <a class="navbar-brand" href="index.html" style="font-family:'Montserrat',sans-serif; font-weight:700; font-size:1.4rem;">
            <i class="fa-solid fa-book me-2" style="color:#ffc800;"></i>
            <span style="color:#ffc800;">Lumiere</span>
        </a>
        <div class="ms-auto">
            <a href="catalogo.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver al catálogo
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-card">
        <h2 class="text-uppercase text-center mb-4" style="font-family:'Montserrat',sans-serif;">
            <i class="fas fa-edit me-2" style="color:#ffc800;"></i>Editar Libro
        </h2>

        <form method="POST" action="editar.php?id=<?= $id ?>" id="formEditar">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" id="titulo" class="form-control" value="<?= htmlspecialchars($libro['titulo']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Autor *</label>
                    <input type="text" name="autor" id="autor" class="form-control" value="<?= htmlspecialchars($libro['autor']) ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Categoría *</label>
                    <select name="categoria" class="form-control" id="categoria" onchange="actualizarSub()">
                        <option value="">-- Selecciona --</option>
                        <?php foreach(['novela','cuento','ciencia','conocimiento','comic','manga'] as $c): ?>
                            <option value="<?= $c ?>" <?= $libro['categoria']==$c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Subcategoría</label>
                    <select name="subcategoria" class="form-control" id="subcategoria">
                        <option value="<?= $libro['subcategoria'] ?>" selected><?= $libro['subcategoria'] ?></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Año</label>
                    <input type="number" name="anio" class="form-control" value="<?= $libro['anio'] ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Precio (MXN) *</label>
                    <input type="number" name="precio" id="precio" class="form-control" step="0.01" value="<?= $libro['precio'] ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stock *</label>
                    <input type="number" name="stock" id="stock" class="form-control" value="<?= $libro['stock'] ?>">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Sinopsis</label>
                <textarea name="sinopsis" class="form-control" rows="4"><?= htmlspecialchars($libro['sinopsis']) ?></textarea>
            </div>
            <div id="errorMsg" class="alert alert-danger d-none"></div>
            <div class="d-flex gap-2 justify-content-end">
                <a href="catalogo.php" class="btn btn-outline-dark px-4">Cancelar</a>
                <button type="button" onclick="validarFormulario()" class="btn btn-primary px-4 text-uppercase fw-bold">
                    <i class="fas fa-save me-2"></i>Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const subcats = {
    novela:       ['ciencia_ficcion','romance','aventura','fantasia','terror','policiaca'],
    cuento:       ['fantasia','infantil','clasicos','terror','humor','misterio'],
    ciencia:      ['astronomia','biologia','fisica','quimica','tecnologia','matematicas'],
    conocimiento: ['historia','filosofia','psicologia','arte','economia','politica'],
    comic:        ['superheroes','independiente','terror','humor','ciencia_ficcion','historico'],
    manga:        ['shonen','shojo','seinen','josei','isekai','deportes']
};

function actualizarSub() {
    const cat = document.getElementById('categoria').value;
    const sub = document.getElementById('subcategoria');
    sub.innerHTML = '<option value="">-- Selecciona --</option>';
    if(subcats[cat]) {
        subcats[cat].forEach(s => {
            const sel = s === '<?= $libro['subcategoria'] ?>' ? 'selected' : '';
            sub.innerHTML += `<option value="${s}" ${sel}>${s.replace('_',' ')}</option>`;
        });
    }
}
actualizarSub();

function validarFormulario() {
    const titulo = document.getElementById('titulo').value.trim();
    const autor  = document.getElementById('autor').value.trim();
    const cat    = document.getElementById('categoria').value;
    const precio = document.getElementById('precio').value;
    const stock  = document.getElementById('stock').value;
    const errDiv = document.getElementById('errorMsg');

    if(!titulo) { errDiv.textContent = 'El título es obligatorio.'; errDiv.classList.remove('d-none'); return; }
    if(!autor)  { errDiv.textContent = 'El autor es obligatorio.';  errDiv.classList.remove('d-none'); return; }
    if(!cat)    { errDiv.textContent = 'Selecciona una categoría.'; errDiv.classList.remove('d-none'); return; }
    if(!precio || precio <= 0) { errDiv.textContent = 'El precio debe ser mayor a 0.'; errDiv.classList.remove('d-none'); return; }
    if(stock === '' || stock < 0) { errDiv.textContent = 'El stock no puede ser negativo.'; errDiv.classList.remove('d-none'); return; }

    errDiv.classList.add('d-none');
    document.getElementById('formEditar').submit();
}
</script>
</body>
</html>