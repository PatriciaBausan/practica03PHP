<?php
header('Content-Type: application/json; charset=utf-8');

$result = ['postgres' => 'error', 'mariadb' => 'error'];

try {
    $db = new PDO('pgsql:host=postgres;port=5432;dbname=jukebox_pg',
        getenv('PGUSER'), getenv('PGPASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $result['postgres'] = 'ok';
    $result['postgres_db'] = $db->query('SELECT current_database()')->fetchColumn();
} catch (PDOException $error) {
    error_log('Fallo de conexión a PostgreSQL, código ' . $error->getCode());
}

try {
    $db = new PDO('mysql:host=mariadb;port=3306;dbname=jukebox_maria;charset=utf8mb4',
        getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $result['mariadb'] = 'ok';
    $result['mariadb_db'] = $db->query('SELECT DATABASE()')->fetchColumn();
} catch (PDOException $error) {
    error_log('Fallo de conexión a MariaDB, código ' . $error->getCode());
}

if ($result['postgres'] !== 'ok' || $result['mariadb'] !== 'ok') {
    http_response_code(503);
}
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);