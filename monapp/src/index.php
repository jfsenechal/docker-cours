<?php
echo "<h1>Mon application33 PHP dans Docker</h1>";
echo "<p>Version de PHP22 : " . PHP_VERSION . "</p>";
echo "<p>pdo_mysql installé : " . (extension_loaded('pdo_mysql') ? '✅ oui' : '❌ non') . "</p>";
