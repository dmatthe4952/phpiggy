<?php

require 'vendor/autoload.php';
include  __DIR__ . '/src/Framework/Database.php';

use Framework\Database;
use App\Config\Paths;
use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable(Paths::ROOT);
    $dotenv->load();
} catch (EXCEPTION $e) {
    echo $e->getMessage();
}

try {
$db = new Database(
    $_ENV['DB_DRIVER'],
    [
        'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_NAME']
    ],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
);
} catch (PDOException $e) {
    echo "Failed to get DB handle..." . $e->getMessage();
    exit;
}

$sqlfile = file_get_contents('./database.sql');
try {
    $db->query($sqlfile);
} catch (Exception $e) {
    echo "Table createion failed..." . $e->getMessage();
}
