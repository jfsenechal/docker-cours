<?php
$host = getenv('DB_HOST');

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=" . getenv('DB_NAME'),
        getenv('DB_USER'),
        getenv('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>Connexion impossible à $host</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}

$pdo->exec("CREATE TABLE IF NOT EXISTS visites (id INT AUTO_INCREMENT PRIMARY KEY, quand DATETIME)");
$pdo->exec("INSERT INTO visites (quand) VALUES (NOW())");
$total   = $pdo->query("SELECT COUNT(*) FROM visites")->fetchColumn();
$version = $pdo->query("SELECT VERSION()")->fetchColumn();

echo "<h1>PHP dans Docker → MySQL du PC</h1>";
echo "<p>Serveur : <strong>" . htmlspecialchars($version) . "</strong> (sur $host)</p>";
echo "<p>Nombre de visites : <strong>$total</strong></p>";
