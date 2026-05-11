<?php include 'conexion.php';

$id = (int)$_GET['id'];
$stmt = $db->prepare("DELETE FROM libros WHERE id = ?");
$stmt->execute([$id]);

header("Location: catalogo.php?msg=eliminado");
exit;
?>