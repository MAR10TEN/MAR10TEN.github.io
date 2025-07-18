<?php
// config.php

// 1. Credenciales de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'siste247_root');
// ¡CUIDADO! Reemplaza con tu contraseña real
define('DB_PASS', 'x8^n#ma?o(~3jW.V'); 
define('DB_NAME', 'siste247_users');

// 2. Habilitar reportes de error de MySQLi
// Esto hará que los errores de la base de datos lancen una excepción de PHP,
// lo que facilita la captura de errores con un bloque try-catch.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>