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

$db = new Database(
    $_ENV['DB_DRIVER'],
    [
        'host' => $_ENV['DB_HOST'],
        'port' => 3306,
        'dbname' => 'phpiggy'
    ],
    'phpiggy',
    'phpuser'
);

$sqlfile = file_get_contents('./database.sql');
try {
    $db->connection->query($sqlfile);
} catch (Exception $e) {
    if ($db->connection->inTransaction()) {
        $db->connection->rollBack();
    }
    echo "Transaction failed! {$e->getMessage()}";
}
