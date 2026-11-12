<?php
require __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

echo "<h1>Image multi-étapes</h1>";
echo "<p>Nous sommes le " . Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') . "</p>";
