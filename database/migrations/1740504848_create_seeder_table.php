<?php

namespace sellerhub\database\migrations;

use sellerhub\core\database\migration\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->prefix}seeders` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `seeder` VARCHAR(255) NOT NULL UNIQUE,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) {$this->charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function down(): void
    {
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->prefix}seeders");
    }
};