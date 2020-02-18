<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

/**
 * @since      1.0.0
 * @package    Mage_Plugin
 * @subpackage Mage_Plugin/includes
 * @author     MagePeople team <magepeopleteam@gmail.com>
 */
class CSMS_Plugin {
	
	
	protected $loader;
	
	protected $plugin_name;
	
	protected $version;
	
	public function __construct() {
		$this->load_dependencies();
	}
	
	private function load_dependencies() {
		require_once CSMS_PLUGIN_DIR . 'lib/classes/class-form-fields-generator.php';
		require_once CSMS_PLUGIN_DIR . 'lib/classes/class-form-fields-wrapper.php';
		require_once CSMS_PLUGIN_DIR . 'lib/classes/class-meta-box.php';
		require_once CSMS_PLUGIN_DIR . 'lib/classes/class-taxonomy-edit.php';
		require_once CSMS_PLUGIN_DIR . 'lib/classes/class-theme-page.php';
		require_once CSMS_PLUGIN_DIR . 'lib/classes/class-menu-page.php';
		require_once CSMS_PLUGIN_DIR . 'includes/class-csms-plugin-loader.php';
		require_once CSMS_PLUGIN_DIR . 'includes/class-csms-functions.php';
		require_once CSMS_PLUGIN_DIR . 'admin/class-csms-plugin-admin.php';
		require_once CSMS_PLUGIN_DIR . 'public/class-csms-plugin-public.php';
		require_once CSMS_PLUGIN_DIR . 'includes/class-csms-plugin-helper.php';
		$this->loader = new CSMS_Plugin_Loader();
	}
	
	
	public function run() {
		$this->loader->run();
	}
	
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	
	public function get_loader() {
		return $this->loader;
	}
	
	public function get_version() {
		return $this->version;
	}
	
}
