<?php

use MailerPress\Core\Enums\Tables;
use MailerPress\Core\Migrations\SchemaBuilder;
use MailerPress\Core\Migrations\CustomTableManager;

return function (SchemaBuilder $schema) {
    global $wpdb;

    $campaignsTable = Tables::get(Tables::MAILERPRESS_CAMPAIGNS);

    // Ajouter la colonne automation_id si elle n'existe pas
    $schema->table(Tables::MAILERPRESS_CAMPAIGNS, function (CustomTableManager $table) use ($wpdb, $campaignsTable) {
        // Vérifier si la colonne existe déjà
        $columnExists = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = %s 
                AND TABLE_NAME = %s 
                AND COLUMN_NAME = 'automation_id'",
                DB_NAME,
                $campaignsTable
            )
        );

        // Ajouter la colonne si elle n'existe pas
        if (empty($columnExists)) {
            $table->bigInteger('automation_id')->unsigned()->nullable()->after('campaign_type');
            $table->addIndex('automation_id');
        }

        // Ajouter la clé étrangère avec CASCADE DELETE si elle n'existe pas
        $foreignKeyExists = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = %s 
                AND TABLE_NAME = %s 
                AND COLUMN_NAME = 'automation_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL",
                DB_NAME,
                $campaignsTable
            )
        );

        if (empty($foreignKeyExists)) {
            // Supprimer d'abord toute clé étrangère existante sans CASCADE
            $table->dropForeign('automation_id');

            // Ajouter la nouvelle clé étrangère avec CASCADE DELETE
            $table->addForeignKey('automation_id', Tables::MAILERPRESS_AUTOMATIONS, 'id', 'CASCADE', 'RESTRICT');
        }

        $table->setVersion('1.3.0');
    });

    // Synchroniser les campagnes email des steps avec leur automation
    // Cette synchronisation sera exécutée après la migration via un hook
    // On utilise un hook personnalisé qui sera déclenché après la migration
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
            return; // Les tables n'existent pas encore, on ne peut pas synchroniser
        }

        // Récupérer tous les steps de type ACTION avec key='send_email'
        $steps = $wpdb->get_results(
            "SELECT automation_id, step_id, settings 
            FROM {$stepsTable} 
            WHERE type = 'ACTION' 
            AND `key` = 'send_email' 
            AND settings IS NOT NULL"
        );

        foreach ($steps as $step) {
            $automationId = (int) $step->automation_id;
            $settings = json_decode($step->settings, true);

            // Extraire le template_id des settings
            $templateId = isset($settings['template_id']) ? (int) $settings['template_id'] : null;

            if ($templateId && $automationId) {
                // Vérifier que la campagne existe
                $campaignExists = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM {$campaignsTable} WHERE campaign_id = %d",
                        $templateId
                    )
                );

                if ($campaignExists) {
                    // Mettre à jour l'automation_id de la campagne
                    // Seulement si elle n'a pas déjà un automation_id ou si c'est différent
                    $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE {$campaignsTable} 
                            SET automation_id = %d 
                            WHERE campaign_id = %d 
                            AND (automation_id IS NULL OR automation_id != %d)",
                            $automationId,
                            $templateId,
                            $automationId
                        )
                    );
                }
            }
        }
    });
};
