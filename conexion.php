<?php
try {
    $db = new PDO('sqlite:database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS libros (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT NOT NULL,
        autor TEXT NOT NULL,
        categoria TEXT NOT NULL,
        subcategoria TEXT,
        anio INTEGER,
        precio REAL,
        stock INTEGER DEFAULT 0,
        sinopsis TEXT
    )");
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>