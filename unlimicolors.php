<?php

/**
 * @link              https://github.com/unlimiTheme/unlimicolors
 * @since             1.0.0
 * @package           UNLIMICOLORS
 *
 * @wordpress-plugin
 * Plugin Name:       UNLIMICOLORS
 * Plugin URI:        https://github.com/unlimiTheme/unlimicolors
 * Description:       Color your website as you wish. Just select an item, choose your favorite color and then enjoy.
 * Version:           1.0.0
 * Author:            UnlimiTheme
 * Author URI:        https://github.com/unlimiTheme
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       unlimicolors
 * Domain Path:       /languages
 */

error_reporting(E_ALL);
ini_set('display_errors', true);

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'UNLIMICOLORS_VERSION', '1.0.0' );

/**
 * Plugin name.
 */
define( 'UNLIMICOLORS_NAME', 'UNLIMICOLORS' );

/**
 * Plugin slug.
 */
define( 'UNLIMICOLORS_SLUG', 'unlimicolors' );

/**
 * Plugin dir.
 */
define( 'UNLIMICOLORS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin dir.
 */
define( 'UNLIMICOLORS_PLUGIN_PATH', plugin_dir_url( __FILE__ ) );

/**
 * Plugin slug.
 */
define( 'UNLIMICOLORS_NONCE', 'unlimicolors-customize-action' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-unlimicolors-activator.php
 */
function unlimicolors_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-unlimicolors-activator.php';
	UNLIMICOLORS_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-unlimicolors-deactivator.php
 */
function unlimicolors_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-unlimicolors-deactivator.php';
	UNLIMICOLORS_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'unlimicolors_activate' );
register_deactivation_hook( __FILE__, 'unlimicolors_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-unlimicolors.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function unlimicolors_run() {

	$plugin = new UNLIMICOLORS();
	$plugin->run();

}
unlimicolors_run();