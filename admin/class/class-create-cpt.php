<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
class Mage_Cpt {
	
	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
	}
	
	
	public function register_cpt() {
		
		$labels = array(
			'name'         => _x( 'Service Management', '' ),
			'add_new_item' => _x( 'Add New Product', '' ),
			'add_new'      => _x( 'Add New Customer Services', '' ),
		
		);
		
		$args = array(
			'public'    => true,
			'labels'    => $labels,
			'menu_icon' => 'dashicons-layout',
			'supports'  => array( 'title', 'editor', 'thumbnail' ),
			'rewrite'   => array( 'slug' => 'service_management' ),
		
		);
		register_post_type( 'service_management', $args );
		
		
		$labels = array(
			'name'         => _x( 'Add New Service Pricing', '' ),
			'add_new_item' => _x( 'Add New Services Pricing', '' ),
		);
		
		$args = array(
			'public'       => true,
			'labels'       => $labels,
			'menu_icon'    => 'dashicons-layout',
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'rewrite'      => array( 'slug' => 'service_management' ),
			'show_in_menu' => 'edit.php?post_type=service_management',
		
		);
		register_post_type( 'service_pricing', $args );
	}
	
}//end class Mage_Cpt
new Mage_Cpt();