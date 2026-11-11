<?php
$pdo = new PDO(
    'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD')
);

$pdo->exec("CREATE TABLE IF NOT EXISTS visites (id INT AUTO_INCREMENT PRIMARY KEY, quand DATETIME)");
$pdo->exec("INSERT INTO visites (quand) VALUES (NOW())");
$total = $pdo->query("SELECT COUNT(*) FROM visites")->fetchColumn();

echo "<h1>Mon appli PHP + MySQL avec Compose</h1>";
echo "<p>Nombre de visites : <strong>$total</strong></p>";
