<?php

namespace restpressMVC\core\database\migration;

use restpressMVC\bootstraps\Environment;
use wpdb;

class MigrationManager
{
    private wpdb $wpdb;
    private string $migrationsTable;

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->migrationsTable = $wpdb->prefix.Environment::TablePreFix . 'migrations';
    }

    private function getExecutedMigrations(): array
    {
        return $this->wpdb->get_col("SELECT migration FROM {$this->migrationsTable}");
    }

    private function getNextBatchNumber(): int|string
    {
        $batch = $this->wpdb->get_var("SELECT MAX(batch) FROM {$this->migrationsTable}");
        return $batch ? $batch + 1 : 1;
    }

    public function runMigrations(): void
    {
        $executedMigrations = $this->getExecutedMigrations();
        $batch = $this->getNextBatchNumber();

        $migrationFiles = glob(PluginRoot . '/database/migrations/*.php');
        sort($migrationFiles); // Ensure execution order

        foreach ($migrationFiles as $file)
        {
            $migrationName = basename($file, ".php");

            if (!in_array($migrationName, $executedMigrations))
            {
                $migrationInstance = require $file; // Get the returned class

                // must be of type Migration
                if ($migrationInstance instanceof Migration)
                {
                    $migrationInstance->up();

                    $this->wpdb->insert($this->migrationsTable,
                        [
                            'migration' => $migrationName,
                            'batch' => $batch,
                            'created_at' => current_time('mysql')
                        ]
                    );
                }
            }
        }
    }

    public function rollbackLastBatch(): void
    {
        $lastBatch = $this->wpdb->get_var("SELECT MAX(batch) FROM {$this->migrationsTable}");
        if (!$lastBatch) return;

        $migrationsToRollback = $this->wpdb->get_col("SELECT migration FROM {$this->migrationsTable} WHERE batch = $lastBatch");

        foreach (array_reverse($migrationsToRollback) as $migrationName)
        {
            $file = PluginRoot . '/database/migrations/'.$migrationName.'.php';

            if (file_exists($file))
            {
                $migrationInstance = require $file;

                // must be of type Migration
                if ($migrationInstance instanceof Migration)
                {
                    $migrationInstance->down();
                    $this->wpdb->delete($this->migrationsTable, ['migration' => $migrationName]);
                }
            }
        }
    }

    public function rollbackAllMigrations(): void
    {
        $migrationsToRollback = $this->wpdb->get_col("SELECT migration FROM {$this->migrationsTable} WHERE NOT migration LIKE '%migrations%';");

        if ($migrationsToRollback)
        {
            foreach (array_reverse($migrationsToRollback) as $migrationName)
            {
                $file = PluginRoot . '/database/migrations/'.$migrationName.'.php';

                if (file_exists($file))
                {
                    $migrationInstance = require $file;

                    // must be of type Migration
                    if ($migrationInstance instanceof Migration)
                    {
                        $migrationInstance->down();
                        $this->wpdb->delete($this->migrationsTable, ['migration' => $migrationName]);
                    }
                }
            }
        }
    }
}
