<?php

require_once __DIR__ . '/../utils/database_seed.php';

use Backend\Utils\DatabaseSeed;

try {
    $databaseSeed = new DatabaseSeed();
    $result = $databaseSeed->run();

    echo 'Seed completed successfully.' . PHP_EOL;
    echo 'Database: ' . $result['database'] . PHP_EOL;
    echo 'Schema imported: ' . ($result['schema_imported'] ? 'yes' : 'no') . PHP_EOL;
    echo 'Users seeded: ' . $result['seeded_users'] . PHP_EOL;
    exit(0);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Seed failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
