<?php

use MailerPress\Core\Enums\Tables;
use MailerPress\Core\Migrations\SchemaBuilder;
use MailerPress\Core\Migrations\CustomTableManager;

return function (SchemaBuilder $schema) {
    global $wpdb;

    // Get the full table name with prefix
    $importChunksTable = $wpdb->prefix . Tables::MAILERPRESS_IMPORT_CHUNKS;

    // Check if table exists
    $tableExists = $wpdb->get_var("SHOW TABLES LIKE '{$importChunksTable}'") === $importChunksTable;

    if (!$tableExists) {
        return; // Skip if table doesn't exist
    }

    // Get existing columns
    $existingColumns = $wpdb->get_col("SHOW COLUMNS FROM {$importChunksTable}", 0);

    // Add processing_started_at column if it doesn't exist
    if (!in_array('processing_started_at', $existingColumns)) {
        $wpdb->query("ALTER TABLE {$importChunksTable} ADD COLUMN processing_started_at TIMESTAMP NULL DEFAULT NULL AFTER processed");
    }

    // Add processing_completed_at column if it doesn't exist
    if (!in_array('processing_completed_at', $existingColumns)) {
        $wpdb->query("ALTER TABLE {$importChunksTable} ADD COLUMN processing_completed_at TIMESTAMP NULL DEFAULT NULL AFTER processing_started_at");
    }

    // Add retry_count column if it doesn't exist
    if (!in_array('retry_count', $existingColumns)) {
        $wpdb->query("ALTER TABLE {$importChunksTable} ADD COLUMN retry_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER processing_completed_at");
    }

    // Add error_message column if it doesn't exist
    if (!in_array('error_message', $existingColumns)) {
        $wpdb->query("ALTER TABLE {$importChunksTable} ADD COLUMN error_message VARCHAR(255) NULL AFTER retry_count");
    }

    // Add indexes if they don't exist
    $existingIndexes = $wpdb->get_results("SHOW INDEX FROM {$importChunksTable}");
    $indexNames = array_column($existingIndexes, 'Key_name');

    // Add composite index on batch_id and processed if it doesn't exist
    $batchProcessedIndexExists = false;
    foreach ($existingIndexes as $index) {
        if (($index->Column_name === 'batch_id' || $index->Column_name === 'processed') &&
            $index->Key_name !== 'PRIMARY') {
            $batchProcessedIndexExists = true;
            break;
        }
    }

    if (!$batchProcessedIndexExists) {
        $wpdb->query("ALTER TABLE {$importChunksTable} ADD INDEX idx_batch_processed (batch_id, processed)");
    }

    // Add index on processing_started_at if it doesn't exist
    if (!in_array('processing_started_at', $indexNames) && !in_array('idx_processing_started_at', $indexNames)) {
        $wpdb->query("ALTER TABLE {$importChunksTable} ADD INDEX idx_processing_started_at (processing_started_at)");
    }

    // Update version marker
    update_option('mailerpress_import_chunks_migration_1_2_2', true);
};
