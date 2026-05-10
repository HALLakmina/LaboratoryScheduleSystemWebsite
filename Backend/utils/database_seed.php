<?php
namespace Backend\Utils;

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;
use RuntimeException;

class DatabaseSeed {
    private $dbHost;
    private $dbUser;
    private $dbPassword;
    private $dbName;
    private $schemaPath;
    private $seedUsersPath;
    private $seedLockPath;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $this->dbHost = $_ENV['DB_HOST'] ?? 'localhost';
        $this->dbUser = $_ENV['DB_USER'] ?? 'root';
        $this->dbPassword = $_ENV['DB_PASSWORD'] ?? '';
        $this->dbName = $_ENV['DB_NAME'] ?? 'laboratory_schedule_system';
        $this->schemaPath = dirname(__DIR__) . '/seeds/laboratory_schedule_system.sql';
        $this->seedUsersPath = dirname(__DIR__) . '/seeds/users_seed.php';
        $this->seedLockPath = dirname(__DIR__) . '/seeds/.seed.lock';
    }

    public function run() {
        $this->ensureSeedCanRun();

        $this->createDatabase();
        $schemaImported = $this->importSchemaIfNeeded();
        $seededUsers = $this->seedUsers();
        $this->markSeedAsExecuted();

        return [
            'database' => $this->dbName,
            'schema_imported' => $schemaImported,
            'seeded_users' => $seededUsers,
        ];
    }

    public function createDatabase() {
        $pdo = $this->createServerConnection();
        $pdo->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci',
            str_replace('`', '``', $this->dbName)
        ));
    }

    private function importSchemaIfNeeded() {
        $pdo = $this->createDatabaseConnection();
        if ($this->tableExists($pdo, 'users')) {
            return false;
        }

        if (!is_file($this->schemaPath)) {
            throw new RuntimeException('Schema file not found: ' . $this->schemaPath);
        }

        $sql = file($this->schemaPath, FILE_IGNORE_NEW_LINES);
        if ($sql === false) {
            throw new RuntimeException('Failed to read schema file.');
        }

        $statement = '';
        foreach ($sql as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || strpos($trimmed, '--') === 0) {
                continue;
            }

            if (strpos($trimmed, '/*') === 0 || strpos($trimmed, '/*!') === 0) {
                continue;
            }

            $statement .= $line . PHP_EOL;

            if (substr(rtrim($line), -1) === ';') {
                $sqlStatement = trim($statement);
                $statement = '';

                if ($sqlStatement === '') {
                    continue;
                }

                $upperStatement = strtoupper($sqlStatement);
                if (
                    strpos($upperStatement, 'START TRANSACTION') === 0 ||
                    strpos($upperStatement, 'COMMIT') === 0 ||
                    strpos($upperStatement, 'SET SQL_MODE') === 0 ||
                    strpos($upperStatement, 'SET TIME_ZONE') === 0
                ) {
                    continue;
                }

                $pdo->exec($sqlStatement);
            }
        }

        return true;
    }

    public function seedUsers(): array {
        if (!is_file($this->seedUsersPath)) {
            throw new RuntimeException('Seed users file not found: ' . $this->seedUsersPath);
        }

        $seedUsers = require $this->seedUsersPath;
        if (!is_array($seedUsers)) {
            throw new RuntimeException('Seed users file must return an array.');
        }

        $pdo = $this->createDatabaseConnection();

        foreach ($seedUsers as $user) {
            $this->upsertUser($pdo, $user);
        }

        return $seedUsers;
    }

    public function createUser($user) {
        $pdo = $this->createDatabaseConnection();
        $this->upsertUser($pdo, $user);
    }

    private function upsertUser(PDO $pdo, array $user) {
        $requiredKeys = [
            'initials',
            'initials_stand_for',
            'first_name',
            'last_name',
            'email',
            'password',
            'role',
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $user) || trim((string)$user[$key]) === '') {
                throw new RuntimeException("Seed user field '{$key}' is required.");
            }
        }

        $existingId = $this->findUserIdByEmail($pdo, $user['email']);
        $passwordHash = password_hash((string)$user['password'], PASSWORD_DEFAULT);

        if ($existingId !== null) {
            $query = 'UPDATE users
                        SET initials = :initials,
                            initials_stand_for = :initials_stand_for,
                            first_name = :first_name,
                            last_name = :last_name,
                            honorifics = :honorifics,
                            nic = :nic,
                            mobile_number = :mobile_number,
                            password = :password,
                            role = :role,
                            updated_by = :updated_by
                        WHERE id = :id';
            $statement = $pdo->prepare($query);
            $statement->execute([
                'id' => $existingId,
                'initials' => $user['initials'],
                'initials_stand_for' => $user['initials_stand_for'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'honorifics' => $user['honorifics'] ?? null,
                'nic' => $user['nic'] ?? null,
                'mobile_number' => $user['mobile_number'] ?? null,
                'password' => $passwordHash,
                'role' => $user['role'],
                'updated_by' => $user['updated_by'] ?? 'seed-script',
            ]);
            return;
        }

        $query = 'INSERT INTO users
                    (
                        initials,
                        initials_stand_for,
                        first_name,
                        last_name,
                        honorifics,
                        nic,
                        email,
                        mobile_number,
                        password,
                        role,
                        created_by,
                        updated_by
                    ) VALUES (
                        :initials,
                        :initials_stand_for,
                        :first_name,
                        :last_name,
                        :honorifics,
                        :nic,
                        :email,
                        :mobile_number,
                        :password,
                        :role,
                        :created_by,
                        :updated_by
                    )';

        $statement = $pdo->prepare($query);
        $statement->execute([
            'initials' => $user['initials'],
            'initials_stand_for' => $user['initials_stand_for'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'honorifics' => $user['honorifics'] ?? null,
            'nic' => $user['nic'] ?? null,
            'email' => $user['email'],
            'mobile_number' => $user['mobile_number'] ?? null,
            'password' => $passwordHash,
            'role' => $user['role'],
            'created_by' => $user['created_by'] ?? 'seed-script',
            'updated_by' => $user['updated_by'] ?? 'seed-script',
        ]);
    }

    private function findUserIdByEmail(PDO $pdo, $email) {
        $statement = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['id'] ?? null;
    }

    private function tableExists(PDO $pdo, $tableName) {
        $statement = $pdo->prepare(
            'SELECT COUNT(*) AS total
             FROM information_schema.tables
             WHERE table_schema = :table_schema AND table_name = :table_name'
        );
        $statement->execute([
            'table_schema' => $this->dbName,
            'table_name' => $tableName,
        ]);

        return (int)$statement->fetchColumn() > 0;
    }

    private function createServerConnection() {
        return new PDO(
            sprintf('mysql:host=%s;charset=utf8mb4', $this->dbHost),
            $this->dbUser,
            $this->dbPassword,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    private function createDatabaseConnection() {
        return new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $this->dbHost, $this->dbName),
            $this->dbUser,
            $this->dbPassword,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    private function ensureSeedCanRun() {
        if (!is_file($this->seedLockPath)) {
            return;
        }

        $lockContent = file_get_contents($this->seedLockPath);
        $lockData = json_decode($lockContent ?: '', true);
        $executedAt = $lockData['executed_at'] ?? 'unknown time';
        $database = $lockData['database'] ?? $this->dbName;

        throw new RuntimeException(
            sprintf(
                'Seed has already been executed once for database "%s" at %s. This script is locked for one-time use.',
                $database,
                $executedAt
            )
        );
    }

    private function markSeedAsExecuted() {
        $lockPayload = json_encode([
            'database' => $this->dbName,
            'executed_at' => date('c'),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($lockPayload === false) {
            throw new RuntimeException('Failed to create seed lock payload.');
        }

        if (file_put_contents($this->seedLockPath, $lockPayload) === false) {
            throw new RuntimeException('Failed to write seed lock file.');
        }
    }
}
