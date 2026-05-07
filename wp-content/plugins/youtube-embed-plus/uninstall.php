<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN'))
{
    die;
}

// if the option to do this is checked
$all_yt_options = get_option('youtubeprefs_alloptions');
if (!empty($all_yt_options['uninstall_data']))
{
    global $wpdb;

    // delete main options
    delete_option('youtubeprefs_alloptions');

    // delete backup options
    $backup_plugin_options = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'youtubeprefs\_alloptions\_backup\_%'");
    foreach ($backup_plugin_options as $option)
    {
        delete_option($option->option_name);
    }

    // delete vi data
    $vi_table_name = $wpdb->prefix . 'vi_consent_logs';
    $wpdb->query("DROP TABLE IF EXISTS $vi_table_name");
}