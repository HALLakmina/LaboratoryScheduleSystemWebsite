<?php

require_once __DIR__ . '/../utils/database_seed.php';

use Backend\Utils\DatabaseSeed;

try {
    $databaseSeed = new DatabaseSeed();
    $result = $databaseSeed->run();

    echo 'Seed completed successfully.' . PHP_EOL;
    echo 'Database       : ' . $result['database'] . PHP_EOL;
    echo 'Schema imported: ' . ($result['schema_imported'] ? 'yes' : 'no') . PHP_EOL;
    echo PHP_EOL;
    echo str_repeat('=', 55) . PHP_EOL;
    echo '  SEED CREDENTIALS — save these before closing this window' . PHP_EOL;
    echo str_repeat('=', 55) . PHP_EOL;
    foreach ($result['seeded_users'] as $user) {
        echo sprintf(
            '  %-10s  email: %-32s  password: %s',
            strtoupper($user['role'] ?? ''),
            $user['email'] ?? '',
            $user['password'] ?? ''
        ) . PHP_EOL;
    }
    echo str_repeat('=', 55) . PHP_EOL;
    echo PHP_EOL;
    echo 'Tip: set SEED_ADMIN_PASSWORD and SEED_LECTURER_PASSWORD in' . PHP_EOL;
    echo '.env before running the seed to use your own passwords.' . PHP_EOL;
    exit(0);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Seed failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
