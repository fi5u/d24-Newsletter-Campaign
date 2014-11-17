<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/admin/partials
 */

if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', $this->plugin_name));
}
?>

<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <form method="POST" action="options.php">
        <?php settings_fields( 'd24nc_settings_page' );
        do_settings_sections( 'd24nc_settings_page' );
        submit_button( __('Save settings', $this->plugin_name), 'primary', 'd24nc_save_options', false ); ?>
    </form>
</div>