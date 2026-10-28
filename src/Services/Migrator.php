<?php

declare(strict_types=1);

namespace HaloSec\Services;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Applies database/migrations/*.sql in filename order and records each one in `schema_migrations`.
 * Run from the CLI during deployment (`php bin/admin.php migrate`), never on a web request.
 */
final class Migrator
{
    /** MySQL errors that mean "already applied": duplicate column, duplicate key name. */
    private const IGNORABLE_ERRORS = [1060, 1061];

    public function __construct(private PDO $pdo, private string $directory)
    {
    }

    /**
     * @return list<string> names of the migrations applied by this run
     */
    public function migrate(): array
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                migration VARCHAR(190) NOT NULL PRIMARY KEY,
                applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )',
        );
        $query = $this->pdo->query('SELECT migration FROM schema_migrations');
        $done = $query === false ? [] : $query->fetchAll(PDO::FETCH_COLUMN);
        $applied = [];
        foreach ($this->pending($done) as $name => $file) {
            foreach (self::statements((string) file_get_contents($file)) as $statement) {
                try {
                    $this->pdo->exec($statement);
                } catch (PDOException $e) {
                    $code = (int) ($e->errorInfo[1] ?? 0);
                    if (!in_array($code, self::IGNORABLE_ERRORS, true)) {
                        throw new RuntimeException("Migration {$name} failed: " . $e->getMessage(), 0, $e);
                    }
                }
            }
            $record = $this->pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (:name)');
            $record->execute(['name' => $name]);
            $applied[] = $name;
        }

        return $applied;
    }

    /**
     * @param array<int, mixed> $done
     * @return array<string, string>
     */
    private function pending(array $done): array
    {
        $files = glob(rtrim($this->directory, '/\\') . '/*.sql') ?: [];
        sort($files, SORT_STRING);
        $pending = [];
        foreach ($files as $file) {
            $name = basename($file);
            if (!in_array($name, $done, true)) {
                $pending[$name] = $file;
            }
        }

        return $pending;
    }

    /**
     * Splits a SQL file into statements. Supports `--` comment lines and statements ending with `;` at line end.
     *
     * @return list<string>
     */
    public static function statements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        foreach (preg_split('/\R/', $sql) ?: [] as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                continue;
            }
            $buffer .= $line . "\n";
            if (str_ends_with($trimmed, ';')) {
                $statements[] = rtrim(trim($buffer), ';');
                $buffer = '';
            }
        }
        if (trim($buffer) !== '') {
            $statements[] = trim($buffer);
        }

        return $statements;
    }
}
