<?php

namespace sellerhub\database\migrations;

use sellerhub\core\database\migration\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}users (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `phone_number` VARCHAR(20) UNIQUE NOT NULL,
            `city` VARCHAR(255) NULL,
            `national_id` VARCHAR(50) UNIQUE NULL,
            `role` ENUM('customer', 'vendor') NOT NULL,
            `wallet_balance` BIGINT UNSIGNED DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) {$this->charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function down(): void
    {
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->prefix}users");
    }
};