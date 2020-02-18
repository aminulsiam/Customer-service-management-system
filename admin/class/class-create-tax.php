<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

class Mage_Tax {
	public function __construct() {
		add_action( "init", array( $this, "mage_tax_init" ), 10 );
	}
	
	public function mage_tax_init() {
		
		
		$labels = array(
			'singular_name' => _x( 'Payment Status', '' ),
			'name'          => _x( 'Payment Status', '' ),
			'add_new_item'  => __( 'Add New Payment Status', 'textdomain' ),
		);
		
		$args = array(
			'hierarchical' => true,
			"public"       => true,
			'labels'       => $labels,
			'show_ui'      => true,
			
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'payment_status' ),
		);
		register_taxonomy( 'payment_status', 'service_management', $args );
		
		
		$labels = array(
			'singular_name' => _x( 'Service Priority', '' ),
			'name'          => _x( 'Service Priority', '' ),
			'add_new_item'  => __( 'Add New Priority', 'textdomain' ),
		);
		
		$args = array(
			'hierarchical'          => true,
			"public"                => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'priority' ),
		);
		register_taxonomy( 'priority', 'service_management', $args );
		
		$labels = array(
			'singular_name' => _x( 'Work Status', '' ),
			'name'          => _x( 'Work Status', '' ),
			'add_new_item'  => __( 'Add New Status', 'textdomain' ),
		);
		
		$args = array(
			'hierarchical'          => true,
			"public"                => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'status' ),
		);
		register_taxonomy( 'status', 'service_management', $args );
		
		
		/*$labels = array(
			'singular_name' => _x( 'Billing Types', '' ),
			'name'          => _x( 'Billing Types', '' ),
			'add_new_item'  => __( 'Add new service price', 'textdomain' ),
		);
		
		$args = array(
			'hierarchical'          => true,
			"public"                => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'show_in_menu'          => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'status' ),
		);
		register_taxonomy( 'Billing_types', 'service_pricing', $args );*/
		
		
	}
}

new Mage_Tax();