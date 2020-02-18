<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


class MageTaxMeta {
	public function __construct() {
		$this->tax_meta();
	}
	
	public function tax_meta() {
		
		$page_1_options = array(
			
			array(
				'id'      => 'colorpicker_field_priority',
				'title'   => __( 'Priority color', 'text-domain' ),
				'details' => __( 'Select Your Priority color', 'text-domain' ),
				'default' => '#1e73be',
				'value'   => '#ff0000',
				'type'    => 'colorpicker',
			),
			
			array(
				'id'      => 'priority_value',
				'title'   => __( 'Priority Value', 'text-domain' ),
				'details' => __( 'Enter Your Priority value', 'text-domain' ),
				'default' => '',
				'value'   => '',
				'type'    => 'number',
			),
		);
		
		
		$page_2_options = array(
			
			array(
				'id'      => 'colorpicker_field_status',
				'title'   => __( 'Priority color', 'text-domain' ),
				'details' => __( 'Select Your Priority color', 'text-domain' ),
				'default' => '#1e73be',
				'value'   => '#ff0000',
				'type'    => 'colorpicker',
			),
			
			array(
				'id'      => 'enable_disable_email',
				'title'   => __( 'Enable / Disable', 'text-domain' ),
				'details' => __( 'Description of checkbox field', 'text-domain' ),
				'default' => '',
				'value'   => 'enable',
				'type'    => 'checkbox',
				'args'    => array(
					'enable' => __( 'Enable this email notification', 'text-domain' ),
				),
			),
			
			array(
				'id'      => 'recipient',
				'title'   => __( 'Recipient', 'text-domain' ),
				'details' => __( 'Description of select field', 'text-domain' ),
				'default' => '',
				'value'   => '',
				'type'    => 'select',
				'args'    => array(
					'customer' => __( 'Customer', 'text-domain' ),
					'admin'    => __( 'Admin', 'text-domain' ),
				),
			),
			
			array(
				'id'          => 'subject',
				'title'       => __( 'Email Subject', 'text-domain' ),
				'details'     => __( 'Description of text field', 'text-domain' ),
				'type'        => 'text',
				'default'     => '',
				'placeholder' => __( 'Text value', 'text-domain' ),
			),
			
			array(
				'id'          => 'email_body',
				'title'       => __( 'Email body', 'text-domain' ),
				'details'     => __( 'Description of wp_editor field, please see detail here https://codex.wordpress.org/Function_Reference/wp_editor',
					'text-domain' ),
				'placeholder' => __( 'Email Body is here', 'text-domain' ),
				'default'     => '',
				'type'        => 'wp_editor',
			),
		
		);
		
		
		$page_3_options = array(
			array(
				'id'      => 'colorpicker_field_payment',
				'title'   => __( 'Priority color', 'text-domain' ),
				'details' => __( 'Select Your Priority color', 'text-domain' ),
				'default' => '#1e73be',
				'value'   => '#ff0000',
				'type'    => 'colorpicker',
			),
		);
		
		
		
		$page_4_options = array(
			array(
				'id'          => 'service_price',
				'title'       => __( 'Service Price', 'text-domain' ),
				'details'     => __( 'Set your service price', 'text-domain' ),
				'type'        => 'number',
				'default'     => "",
				'placeholder' => __( 'Price...', 'text-domain' ),
			),
		);
		
		
		$args         = array(
			'taxonomy' => 'priority',
			'options'  => $page_1_options,
		);
		$TaxonomyEdit = new TaxonomyEdit( $args );
		
		
		$args = array(
			'taxonomy' => 'status',
			'options'  => $page_2_options,
		);
		$TaxonomyEdit = new TaxonomyEdit( $args );
		
		
		$args = array(
			'taxonomy' => 'payment_status',
			'options'  => $page_3_options,
		);
		$TaxonomyEdit = new TaxonomyEdit( $args );
		
		
		
	}
}

new MageTaxMeta();