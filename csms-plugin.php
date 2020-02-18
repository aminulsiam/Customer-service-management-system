<?php
/**
 * Plugin Name:       Customer Service Management System For WordPress
 * Plugin URI:        mage-people.com
 * Description:       This plugin will help you for any kind of services.
 * Version:           1.0.5
 * Author:            MagePeople team
 * Author URI:        mage-people.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       csms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mage-plugin-activator.php
 */
function csms_activate_mage_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-csms-plugin-activator.php';
	CSMS_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mage-plugin-deactivator.php
 */
function csms_deactivate_mage_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-csms-plugin-deactivator.php';
	// Mage_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'csms_activate_mage_plugin' );
register_deactivation_hook( __FILE__, 'csms_deactivate_mage_plugin' );


class CSMS_Base {
	
	public function __construct() {
		$this->define_constants();
		$this->load_main_class();
		$this->run_mage_plugin();
	}
	
	public function define_constants() {
		define( 'CSMS_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
		define( 'CSMS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'CSMS_PLUGIN_FILE', plugin_basename( __FILE__ ) );
	}
	
	public function load_main_class() {
		require CSMS_PLUGIN_DIR . 'includes/class-csms-plugin.php';
	}
	
	public function run_mage_plugin() {
		$plugin = new CSMS_Plugin();
		$plugin->run();
	}
}

new CSMS_Base();