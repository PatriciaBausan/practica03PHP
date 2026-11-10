<?php
$limite = 4;

echo '<h2>for</h2><ul>';
for ($i = 1; $i <= $limite; $i++) {
    echo '<li>Posición ' . $i . '</li>';
}
echo '</ul>';

echo '<h2>while</h2><ul>';
$j = 1;
while ($j <= $limite) {
    echo '<li>Posición ' . $j . '</li>';
    $j++;
}
echo '</ul>';

$titulos = ['Sesión de prueba', 'Segundo tema', 'Tercer tema'];
echo '<h2>foreach</h2><ul>';
foreach ($titulos as $indice => $titulo) {
    echo '<li>' . ($indice + 1) . '. ' . htmlspecialchars($titulo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . ' (índice interno ' . $indice . ')</li>';
}
echo '</ul>';