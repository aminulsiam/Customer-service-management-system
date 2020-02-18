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
class CSMS_Plugin_Functions {
	
	protected $loader;
	
	protected $plugin_name;
	
	protected $version;
	
	public function __construct() {
		$this->add_hooks();
		add_filter( 'mage_wc_products', array( $this, 'add_cpt_to_wc_product' ), 10, 1 );
	}
	
	private function add_hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}
	
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'mage-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
	
	// Adding Custom Post to WC Prodct Data Filter.
	public function add_cpt_to_wc_product( $data ) {
		$mage_cpt = array( 'mage_video' );
		
		return array_merge( $data, $mage_cpt );
	}
	
	/**
	 * All settings get options
	 *
	 * @param $setting_name
	 * @param $meta_key
	 * @param null $default
	 *
	 * @return null
	 */
	public function csms_get_option( $setting_name, $meta_key, $default = null ) {
		
		$get_settings = get_option( $setting_name );
		$get_val      = isset( $get_settings[ $meta_key ] ) ? $get_settings[ $meta_key ] : "";
		$output       = $get_val ? $get_val : $default;
		
		return $output;
	}
	
	
}

global $csms_functions;
$csms_functions = new CSMS_Plugin_Functions();