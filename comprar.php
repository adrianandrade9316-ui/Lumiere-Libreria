<?php include 'conexion.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];

    $stmt = $db->prepare("SELECT * FROM libros WHERE id = ?");
    $stmt->execute([$id]);
    $libro = $stmt->fetch(PDO::FETCH_ASSOC);

    if($libro && $libro['stock'] > 0) {
        $stmt = $db->prepare("UPDATE libros SET stock = stock - 1 WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: catalogo.php?msg=comprado&titulo=" . urlencode($libro['titulo']));
    } else {
        header("Location: catalogo.php?msg=agotado");
    }
    exit;
}
header("Location: catalogo.php");
exit;
?>