<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://brandmarketers.id
 * @since             1.0.0
 * @package           Mp_Design_Library
 *
 * @wordpress-plugin
 * Plugin Name:       Mp Design Library
 * Plugin URI:        https://brandmarketers.id
 * Description:       Template library for elementor builder
 * Version:           1.0.2
 * Author:            brandmarketers.id
 * Author URI:        https://brandmarketers.id
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mp-design-library
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MP_DESIGN_LIBRARY_VERSION', '1.0.2' );
define( 'MP_DESIGN_LIBRARY_NAME', 'Mp Design Library' );
define( 'MP_DESIGN_LIBRARY_ID', '2986' );
define( 'MP_DESIGN_LIBRARY_BASENAME', plugin_basename(__FILE__));
define( 'MP_DESIGN_LIBRARY_MEMBER', 'https://user.brandmarketers.id' );
define( 'MP_DESIGN_LIBRARY_PATH', plugin_dir_path( __FILE__ ) );
define( 'MP_DESIGN_LIBRARY_URL', plugin_dir_url(__FILE__ ) );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
    include( dirname( __FILE__ ) . '/admin/EDD_SL_Plugin_Updater.php' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mp-design-library-activator.php
 */
function activate_mp_design_library() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mp-design-library-activator.php';
	Mp_Design_Library_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mp-design-library-deactivator.php
 */
function deactivate_mp_design_library() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mp-design-library-deactivator.php';
	Mp_Design_Library_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mp_design_library' );
register_deactivation_hook( __FILE__, 'deactivate_mp_design_library' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mp-design-library.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mp_design_library() {

	$plugin = new Mp_Design_Library();
	$plugin->run();

}
run_mp_design_library();
