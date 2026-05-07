<?php

use MailerPress\Core\Enums\Tables;
use MailerPress\Core\Migrations\SchemaBuilder;
use MailerPress\Core\Migrations\CustomTableManager;

return function (SchemaBuilder $schema) {
    global $wpdb;

    $campaignsTable = Tables::get(Tables::MAILERPRESS_CAMPAIGNS);

    // Ajouter la colonne step_id si elle n'existe pas
    $schema->table(Tables::MAILERPRESS_CAMPAIGNS, function (CustomTableManager $table) use ($wpdb, $campaignsTable) {
        // Vérifier si la colonne existe déjà
        $columnExists = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = %s 
                AND TABLE_NAME = %s 
                AND COLUMN_NAME = 'step_id'",
                DB_NAME,
                $campaignsTable
            )
        );

        // Ajouter la colonne si elle n'existe pas
        if (empty($columnExists)) {
            $table->string('step_id', 192)->nullable()->after('automation_id');
            $table->addIndex('step_id');
        }

        $table->setVersion('1.2.0');
    });

    // Synchroniser les campagnes existantes avec leur step_id
    add_action('mailerpress_migration_completed', function () use ($wpdb) {
        $campaignsTable = Tables::get(Tables::MAILERPRESS_CAMPAIGNS);
        $stepsTable = Tables::get(Tables::MAILERPRESS_AUTOMATIONS_STEPS);

        // Vérifier que les tables existent
        $campaignsExists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_SCHEMA = %s 
                AND TABLE_NAME = %s",
                DB_NAME,
                $campaignsTable
            )
        );

        $stepsExists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_SCHEMA = %s 
                AND TABLE_NAME = %s",
                DB_NAME,
                $stepsTable
            )
        );

        if (!$campaignsExists || !$stepsExists) {
            return; // Les tables n'existent pas encore
        }

        // Récupérer tous les steps de type ACTION avec key='send_email' ou 'send_mail'
        $steps = $wpdb->get_results(
            "SELECT automation_id, step_id, settings 
            FROM {$stepsTable} 
            WHERE type = 'ACTION' 
            AND (`key` = 'send_email' OR `key` = 'send_mail')
            AND settings IS NOT NULL"
        );

        foreach ($steps as $step) {
            $stepId = $step->step_id;
            $settings = json_decode($step->settings, true);

            // Extraire le template_id des settings
            $templateId = isset($settings['template_id']) ? (int) $settings['template_id'] : null;

            if ($templateId && $stepId) {
                // Vérifier que la campagne existe
                $campaignExists = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM {$campaignsTable} WHERE campaign_id = %d",
                        $templateId
                    )
                );

                if ($campaignExists) {
                    // Mettre à jour le step_id de la campagne
                    // Seulement si elle n'a pas déjà un step_id ou si c'est différent
                    $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE {$campaignsTable} 
                            SET step_id = %s 
                            WHERE campaign_id = %d 
                            AND (step_id IS NULL OR step_id != %s)",
                            $stepId,
                            $templateId,
                            $stepId
                        )
                    );
                }
            }
        }
    });
};
