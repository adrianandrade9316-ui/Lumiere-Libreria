<?php include 'conexion.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $db->prepare("INSERT INTO libros 
        (titulo, autor, categoria, subcategoria, anio, precio, stock, sinopsis)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_POST['titulo'],
        $_POST['autor'],
        $_POST['categoria'],
        $_POST['subcategoria'],
        $_POST['anio'],
        $_POST['precio'],
        $_POST['stock'],
        $_POST['sinopsis']
    ]);

    header("Location: catalogo.php?msg=agregado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Agregar Libro – Lumiere</title>
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
    .form-control.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.25rem rgba(220,53,69,0.25) !important;
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
            <i class="fas fa-plus-circle me-2" style="color:#ffc800;"></i>Agregar Libro
        </h2>

        <form method="POST" action="agregar.php" id="formAgregar">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ej: Dune">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Autor *</label>
                    <input type="text" name="autor" id="autor" class="form-control" placeholder="Ej: Frank Herbert">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Categoría *</label>
                    <select name="categoria" class="form-control" id="categoria" onchange="actualizarSub()">
                        <option value="">-- Selecciona --</option>
                        <option value="novela">Novela</option>
                        <option value="cuento">Cuento</option>
                        <option value="ciencia">Ciencia</option>
                        <option value="conocimiento">Conocimiento</option>
                        <option value="comic">Cómic</option>
                        <option value="manga">Manga</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Subcategoría</label>
                    <select name="subcategoria" class="form-control" id="subcategoria">
                        <option value="">-- Primero elige categoría --</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Año</label>
                    <input type="number" name="anio" class="form-control" min="1000" max="2099" placeholder="Ej: 2024">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Precio (MXN) *</label>
                    <input type="number" name="precio" id="precio" class="form-control" step="0.01" min="0" placeholder="Ej: 250.00">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stock *</label>
                    <input type="number" name="stock" id="stock" class="form-control" min="0" placeholder="Ej: 10">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Sinopsis</label>
                <textarea name="sinopsis" class="form-control" rows="4" placeholder="Breve descripción del libro..."></textarea>
            </div>
            <div id="errorMsg" class="alert alert-danger d-none"></div>
            <div class="d-flex gap-2 justify-content-end">
                <a href="catalogo.php" class="btn btn-outline-dark px-4">Cancelar</a>
                <button type="button" onclick="validarFormulario()" class="btn btn-primary px-4 text-uppercase fw-bold">
                    <i class="fas fa-save me-2"></i>Guardar libro
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Subcategorías por categoría
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
            sub.innerHTML += `<option value="${s}">${s.replace('_',' ')}</option>`;
        });
    }
}

// Validación JavaScript
function validarFormulario() {
    let valido = true;
    const errDiv = document.getElementById('errorMsg');

    // Limpiar errores anteriores
    document.querySelectorAll('.form-control').forEach(el => {
        el.classList.remove('is-invalid');
    });
    errDiv.classList.add('d-none');

    const titulo = document.getElementById('titulo');
    const autor  = document.getElementById('autor');
    const cat    = document.getElementById('categoria');
    const precio = document.getElementById('precio');
    const stock  = document.getElementById('stock');

    if(!titulo.value.trim()) {
        titulo.classList.add('is-invalid');
        valido = false;
    }
    if(!autor.value.trim()) {
        autor.classList.add('is-invalid');
        valido = false;
    }
    if(!cat.value) {
        cat.classList.add('is-invalid');
        valido = false;
    }
    if(!precio.value || precio.value <= 0) {
        precio.classList.add('is-invalid');
        valido = false;
    }
    if(stock.value === '' || stock.value < 0) {
        stock.classList.add('is-invalid');
        valido = false;
    }

    if(!valido) {
        errDiv.textContent = ' Por favor completa todos los campos obligatorios correctamente.';
        errDiv.classList.remove('d-none');
        errDiv.scrollIntoView({ behavior: 'smooth' });
        return;
    }

    if(confirm('¿Deseas guardar este libro?')) {
        document.getElementById('formAgregar').submit();
    }
}
</script>
</body>
</html>