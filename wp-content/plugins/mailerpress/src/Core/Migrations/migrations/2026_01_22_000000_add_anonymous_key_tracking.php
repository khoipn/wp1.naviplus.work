<?php

use MailerPress\Core\Enums\Tables;
use MailerPress\Core\Migrations\SchemaBuilder;
use MailerPress\Core\Migrations\CustomTableManager;

/**
 * Migration to add anonymous_key column for anonymous tracking.
 *
 * This migration:
 * 1. Adds anonymous_key column to email_tracking table
 * 2. Adds anonymous_key column to click_tracking table
 * 3. Adds indexes for performance on anonymous_key
 */
return function (SchemaBuilder $schema) {
    global $wpdb;

    $emailTrackingTable = $wpdb->prefix . Tables::MAILERPRESS_EMAIL_TRACKING;
    $clickTrackingTable = $wpdb->prefix . Tables::MAILERPRESS_CLICK_TRACKING;

    // Check if email_tracking table exists
    $emailTrackingExists = $wpdb->get_var("SHOW TABLES LIKE '{$emailTrackingTable}'") === $emailTrackingTable;

    if ($emailTrackingExists) {
        // Check if anonymous_key column already exists
        $columnExists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = %s
                 AND COLUMN_NAME = 'anonymous_key'",
                $emailTrackingTable
            )
        );

        if ($columnExists == 0) {
            // Add anonymous_key column
            $wpdb->query("ALTER TABLE `{$emailTrackingTable}` ADD COLUMN `anonymous_key` VARCHAR(64) NULL AFTER `contact_id`");
            
            // Add index on anonymous_key for performance
            $wpdb->query("ALTER TABLE `{$emailTrackingTable}` ADD INDEX `idx_anonymous_key` (`anonymous_key`)");
            
            // Add composite index for anonymous tracking queries
            $wpdb->query("ALTER TABLE `{$emailTrackingTable}` ADD INDEX `idx_batch_anonymous_key` (`batch_id`, `anonymous_key`)");
        }
    }

    // Check if click_tracking table exists
    $clickTrackingExists = $wpdb->get_var("SHOW TABLES LIKE '{$clickTrackingTable}'") === $clickTrackingTable;

    if ($clickTrackingExists) {
        // Check if anonymous_key column already exists
        $columnExists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = %s
                 AND COLUMN_NAME = 'anonymous_key'",
                $clickTrackingTable
            )
        );

        if ($columnExists == 0) {
            // Add anonymous_key column
            $wpdb->query("ALTER TABLE `{$clickTrackingTable}` ADD COLUMN `anonymous_key` VARCHAR(64) NULL AFTER `contact_id`");
            
            // Add index on anonymous_key for performance
            $wpdb->query("ALTER TABLE `{$clickTrackingTable}` ADD INDEX `idx_anonymous_key` (`anonymous_key`)");
            
            // Add composite index for anonymous tracking queries
            $wpdb->query("ALTER TABLE `{$clickTrackingTable}` ADD INDEX `idx_campaign_anonymous_key` (`campaign_id`, `anonymous_key`)");
        }
    }
};
