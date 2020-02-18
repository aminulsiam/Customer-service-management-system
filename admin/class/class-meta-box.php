<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


class MageMetaBox {
	
	public function __construct() {
		$this->meta_boxs();
	}
	
	
	public function meta_boxs() {
		global $csms_functions;
		
		$roles = $csms_functions->csms_get_option( 'csms_settings', 'user_roles_field' );
		
		if ( ! is_array( $roles ) ) {
			$roles = array();
		}
		
		$eng_role = implode( ' ', $roles );
		
		$page_1_options = array(
			'page_nav' => __( '<i class="far fa-dot-circle"></i> Nav Title 1', 'text-domain' ),
			'priority' => 10,
			'sections' => array(
				'section_0' => array(
					'title'       => __( 'This is Section Title', 'text-domain' ),
					'description' => __( 'This is section details', 'text-domain' ),
					'options'     => array(
						
						array(
							'id'          => 'mobile_number',
							'title'       => __( 'Mobile Number', 'text-domain' ),
							'details'     => __( 'Write mobile number.', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter your mobile number', 'text-domain' ),
						),
						
						
						array(
							'id'          => 'customer_name',
							'title'       => __( 'Customer Name', 'text-domain' ),
							'details'     => __( 'Write customer name is here.', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer Name', 'text-domain' ),
						),
						
						
						array(
							'id'          => 'customer_address',
							'title'       => __( 'Address', 'text-domain' ),
							'details'     => __( 'Customer Address write here', 'text-domain' ),
							'value'       => __( 'Textarea value', 'text-domain' ),
							'default'     => __( 'Default Text Value', 'text-domain' ),
							'type'        => 'textarea',
							'placeholder' => __( 'Textarea placeholder', 'text-domain' ),
						),
						
						array(
							'id'          => 'email',
							'title'       => __( 'E-Mail', 'text-domain' ),
							'details'     => __( 'Write email address please', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter your email address', 'text-domain' ),
						),
						
						array(
							'id'      => 'payment_status',
							'title'   => __( 'Payment Status', 'text-domain' ),
							'details' => __( 'Select payment status', 'text-domain' ),
							'default' => 'option_2',
							'value'   => 'option_2',
							'type'    => 'select',
							'args'    => 'TAX_%payment_status%',
						),
						
						array(
							'id'      => 'priority',
							'title'   => __( 'Priority', 'text-domain' ),
							'details' => __( 'Set product working priority', 'text-domain' ),
							'default' => '',
							'value'   => '',
							'type'    => 'select',
							'args'    => 'TAX_%priority%',
						),
						
						
						array(
							'id'          => 'received_date',
							'title'       => __( 'Received Date', 'text-domain' ),
							'details'     => __( 'received date', 'text-domain' ),
							'type'        => 'text',
							'default'     => date( 'Y-m-d' ),
							'placeholder' => __( '', 'text-domain' ),
							'readonly'    => __( 'yes', 'text-domain' ),
						),
						
						array(
							'id'          => 'delivery_date',
							'title'       => __( 'Product Delivery Date', 'woocommerce-tour-booking-manager' ),
							'details'     => __( 'Start date of tour', 'woocommerce-tour-booking-manager' ),
							'date_format' => 'yy-mm-dd',
							'placeholder' => 'yy-mm-dd',
							'default'     => '', // today date
							'value'       => '', // today date
							'type'        => 'datepicker',
						),
						
						array(
							'id'          => 'received_by',
							'title'       => __( 'Received Person', 'text-domain' ),
							'details'     => __( 'Display name who received the services.', 'text-domain' ),
							'type'        => 'text',
							'default'     => 'user',
							'placeholder' => __( '', 'text-domain' ),
							'readonly'    => __( 'yes', 'text-domain' ),
						),
						
						
						array(
							'id'       => 'assign_user_role',
							'title'    => __( 'Assign To an Engineer', 'text-domain' ),
							'details'  => __( 'Description of select2 user field', 'text-domain' ),
							'default'  => '1',
							'multiple' => false,
							'type'     => 'select',
							'args'     => "USER_IDS_ARRAY_%$eng_role%",
						),
						
						array(
							'id'      => 'status',
							'title'   => __( 'Work Status', 'text-domain' ),
							'details' => __( 'Set product status. Mind it, when you change received or deliverd a product status customer get an email.',
								'text-domain' ),
							'default' => '',
							'value'   => '',
							'type'    => 'select',
							'args'    => 'TAX_%status%',
						),
						
						array(
							'id'          => 'note',
							'title'       => __( 'Note', 'text-domain' ),
							'details'     => __( 'Any kind of note of this service', 'text-domain' ),
							'value'       => __( 'Textarea value', 'text-domain' ),
							'default'     => __( '', 'text-domain' ),
							'type'        => 'textarea',
							'placeholder' => __( 'Note', 'text-domain' ),
						),
					),
				),
			
			),
		);
		
		$page_2_options = array(
			
			'page_nav' => __( '<i class="fas fa-cog"></i> Nav Title 2', 'text-domain' ),
			'priority' => 10,
			'sections' => array(
				
				'section_2' => array(
					'title'       => __( '', 'text-domain' ),
					'description' => __( '', 'text-domain' ),
					'options'     => array(
						
						array(
							'id'          => 'service_price',
							'title'       => __( 'Service Price', 'text-domain' ),
							'details'     => __( 'Write total service price', 'text-domain' ),
							'type'        => 'number',
							'default'     => '0',
							'placeholder' => __( '', 'text-domain' ),
							'min'         => 1,
						),
						
						
						array(
							'id'      => 'billing_type',
							'title'   => __( 'Billing Types', 'text-domain' ),
							'details' => __( 'Select service type', 'text-domain' ),
							'default' => 'option_2',
							'value'   => 'option_2',
							'type'    => 'select',
							'args'    => array(
								'service' => 'Service',
								'product' => 'Product',
							),
						),
					
					),
				),
			
			),
		);
		
		
		$page_3_options = array(
			
			'page_nav' => __( '<i class="far fa-bell"></i> Nav Title 3', 'text-domain' ),
			'priority' => 10,
			'sections' => array(
				
				'section_3' => array(
					'title'       => __( '', 'text-domain' ),
					'description' => __( '', 'text-domain' ),
					'options'     => array(),
				),
			
			),
		);
		
		
		$page_4_options = array(
			
			'page_nav' => __( '<i class="fas fa-bomb"></i> Nav Title 4', 'text-domain' ),
			'priority' => 10,
			'sections' => array(
				
				'section_4' => array(
					'title'       => __( '', 'text-domain' ),
					'description' => __( '', 'text-domain' ),
					'options'     => array(),
				),
			
			),
		);
		
		$args = array(
			'meta_box_id'    => 'customer_service_meta_box',
			'meta_box_title' => __( 'Customer Service Management System Info', 'text-domain' ),
			'screen'         => array( 'service_management' ),
			'context'        => 'normal', // 'normal', 'side', and 'advanced'
			'priority'       => 'high', // 'high', 'low'
			'callback_args'  => array(),
			'nav_position'   => 'none', // right, top, left, none
			'item_name'      => "PickPlugins",
			'item_version'   => "1.0.2",
			'panels'         => array(
				'panelGroup-1' => $page_1_options,
				'panelGroup-3' => $page_3_options,
				'panelGroup-4' => $page_4_options,
			),
		);
		
		
		$args1 = array(
			'meta_box_id'    => 'new_services_meta_box',
			'meta_box_title' => __( 'Customer Service Management System Info', 'text-domain' ),
			'screen'         => array( 'service_pricing' ),
			'context'        => 'normal', // 'normal', 'side', and 'advanced'
			'priority'       => 'high', // 'high', 'low'
			'callback_args'  => array(),
			'nav_position'   => 'none', // right, top, left, none
			'item_name'      => "PickPlugins",
			'item_version'   => "1.0.2",
			'panels'         => array(
				'panelGroup-1' => $page_2_options,
			),
		);
		
		$AddMenuPage = new AddMetaBox( $args );
		$AddMenuPage = new AddMetaBox( $args1 );
		
	}
}

new MageMetaBox();