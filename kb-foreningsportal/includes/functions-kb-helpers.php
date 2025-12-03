<?php
/**
 * Helper functions for KB Föreningsportal.
 */

function kb_fp_is_feature_enabled( $flag ) {
    $options = get_option( 'kb_fp_features', array() );
    return isset( $options[ $flag ] ) ? (bool) $options[ $flag ] : true;
}

function kb_fp_create_default_options() {
    $defaults = array(
        'features.orgs.enabled'          => true,
        'features.admins.enabled'        => true,
        'features.campaigns.enabled'     => true,
        'features.provision.enabled'     => true,
        'features.orders.enabled'        => true,
        'features.members.enabled'       => false,
        'features.materials.enabled'     => true,
        'features.qr.auto_generate'      => true,
        'features.logs.enabled'          => true,
        'features.notifications.enabled' => true,
    );
    add_option( 'kb_fp_features', $defaults );
}

function kb_fp_create_custom_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    // Placeholder for actual schema creation.
    $tables = array(
        'kb_orgs'                => "CREATE TABLE {$wpdb->prefix}kb_orgs (id BIGINT unsigned NOT NULL AUTO_INCREMENT, name varchar(255) NOT NULL, PRIMARY KEY (id)) $charset_collate;",
        'kb_admins'              => "CREATE TABLE {$wpdb->prefix}kb_admins (id BIGINT unsigned NOT NULL AUTO_INCREMENT, name varchar(255) NOT NULL, PRIMARY KEY (id)) $charset_collate;",
        'kb_campaigns'           => "CREATE TABLE {$wpdb->prefix}kb_campaigns (id BIGINT unsigned NOT NULL AUTO_INCREMENT, org_id BIGINT unsigned NOT NULL, PRIMARY KEY (id)) $charset_collate;",
        'kb_org_provision'       => "CREATE TABLE {$wpdb->prefix}kb_org_provision (id BIGINT unsigned NOT NULL AUTO_INCREMENT, org_id BIGINT unsigned NOT NULL, amount decimal(10,2) NOT NULL DEFAULT 0, PRIMARY KEY (id)) $charset_collate;",
        'kb_order_link'          => "CREATE TABLE {$wpdb->prefix}kb_order_link (id BIGINT unsigned NOT NULL AUTO_INCREMENT, order_id BIGINT unsigned NOT NULL, org_id BIGINT unsigned NOT NULL, PRIMARY KEY (id)) $charset_collate;",
        'kb_members'             => "CREATE TABLE {$wpdb->prefix}kb_members (id BIGINT unsigned NOT NULL AUTO_INCREMENT, org_id BIGINT unsigned NOT NULL, email varchar(255), PRIMARY KEY (id)) $charset_collate;",
        'kb_sales_aggregates'    => "CREATE TABLE {$wpdb->prefix}kb_sales_aggregates (id BIGINT unsigned NOT NULL AUTO_INCREMENT, org_id BIGINT unsigned NOT NULL, total decimal(10,2), PRIMARY KEY (id)) $charset_collate;",
        'kb_admin_commissions'   => "CREATE TABLE {$wpdb->prefix}kb_admin_commissions (id BIGINT unsigned NOT NULL AUTO_INCREMENT, admin_id BIGINT unsigned NOT NULL, total decimal(10,2), PRIMARY KEY (id)) $charset_collate;",
        'kb_material_templates'  => "CREATE TABLE {$wpdb->prefix}kb_material_templates (id BIGINT unsigned NOT NULL AUTO_INCREMENT, name varchar(255), PRIMARY KEY (id)) $charset_collate;",
        'kb_generated_material'  => "CREATE TABLE {$wpdb->prefix}kb_generated_material (id BIGINT unsigned NOT NULL AUTO_INCREMENT, org_id BIGINT unsigned NOT NULL, url text, PRIMARY KEY (id)) $charset_collate;",
        'kb_qr_codes'            => "CREATE TABLE {$wpdb->prefix}kb_qr_codes (id BIGINT unsigned NOT NULL AUTO_INCREMENT, org_id BIGINT unsigned NOT NULL, code varchar(255), PRIMARY KEY (id)) $charset_collate;",
        'kb_logs'                => "CREATE TABLE {$wpdb->prefix}kb_logs (id BIGINT unsigned NOT NULL AUTO_INCREMENT, message text, created_at datetime DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)) $charset_collate;",
        'kb_notifications'       => "CREATE TABLE {$wpdb->prefix}kb_notifications (id BIGINT unsigned NOT NULL AUTO_INCREMENT, type varchar(50), payload text, created_at datetime DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)) $charset_collate;",
    );

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    foreach ( $tables as $sql ) {
        dbDelta( $sql );
    }
}

