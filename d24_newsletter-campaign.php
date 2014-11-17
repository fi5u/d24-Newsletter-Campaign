<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           d24_newsletter-campaign
 *
 * @wordpress-plugin
 * Plugin Name:       d24 Newsletter Campaign
 * Plugin URI:        http://newsletter-campaign.d24.fi
 * Description:       Send newsletter campaigns with WordPress posts
 * Version:           1.0.0
 * Author:            d24
 * Author URI:        http://d24.fi/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       d24nc
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-d24nc-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-d24nc-deactivator.php';

/** This action is documented in includes/class-d24nc-activator.php */
register_activation_hook( __FILE__, array( 'D24nc_Activator', 'activate' ) );

/** This action is documented in includes/class-d24nc-deactivator.php */
register_deactivation_hook( __FILE__, array( 'D24nc_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-d24nc.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_d24nc() {

	$plugin = new D24nc();
	$plugin->run();

}
run_d24nc();
