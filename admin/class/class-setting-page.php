<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


class CSMS_Setting_Page {
	
	public function __construct() {
		
		//error_reporting( 0 );
		
		$this->settings_page();
		
	}
	
	public function settings_page() {
		
		$page_1_options = array(
			'page_nav'      => __( '<i class="far fa-dot-circle"></i> Roles and Form', 'text-domain' ),
			'priority'      => 10,
			'page_settings' => array(
				
				'section_0' => array(
					'title'       => __( 'Engineer and Manager Roles', 'text-domain' ),
					'description' => __( 'This is section details', 'text-domain' ),
					'options'     => array(
						
						array(
							'id'       => 'user_roles_field',
							'title'    => __( 'Engineer roles', 'text-domain' ),
							'details'  => __( 'Description of user roles select field', 'text-domain' ),
							'multiple' => true,
							'type'     => 'select',
							'args'     => 'USER_ROLES',
						),
						
						array(
							'id'       => 'manager_roles_field',
							'title'    => __( 'Manager roles', 'text-domain' ),
							'details'  => __( 'Description of Manager roles select field', 'text-domain' ),
							'multiple' => true,
							'type'     => 'select',
							'args'     => 'USER_ROLES',
						),
					
					),
				),
				
				'section_1' => array(
					
					'title'       => __( 'Add form ', 'text-domain' ),
					'description' => __( 'This is section details', 'text-domain' ),
					'options'     => array(
						
						array(
							'id'          => 'add_field_in_meta_info',
							'title'       => __( 'Registration Form Builder', 'text-domain' ),
							'details'     => __( 'Build Your Attendee Form', 'text-domain' ),
							'collapsible' => true,
							'type'        => 'repeatable',
							'title_field' => 'field_label',
							'fields'      => array(
								
								array(
									'type'    => 'text',
									'default' => '',
									'item_id' => 'field_label',
									'name'    => 'Field Label',
								),
								
								array(
									'type'    => 'text',
									'default' => '',
									'item_id' => 'field_id',
									'name'    => 'Unique ID (Required Field, If this field is empty no info will be saved)',
								),
								
								array(
									'type'    => 'select',
									'default' => '',
									'item_id' => 'field_type',
									'name'    => 'Type',
									'args'    => array(
										'text'     => 'Text',
										'number'   => 'Number',
										'select'   => 'Select',
										'checkbox' => 'Checkbox',
										'radio'    => 'Radio',
										'textarea' => 'Textarea',
										'email'    => 'Email',
									),
								),
								
								array(
									'type'    => 'checkbox',
									'default' => array(
										'option_1',
										'option_3',
									),
									'item_id' => 'checkbox_field',
									'name'    => 'Required?',
									'args'    => array(
										'required' => 'Required',
									),
								),
								
								array(
									'type'    => 'textarea',
									'default' => '',
									'details' => __( 'Please Enter values for Select/Checkbox & Radio Fields. Must be comma seperated. Ex: Male,Female',
										'text-domain' ),
									'item_id' => 'field_values',
									'name'    => 'Enter values for Select/Checkbox & Radio Fields. Must be comma seperated. Ex: Male,Female',
								),
							
							),
						),
					),
				),
				
				'section_2' => array(
					'title'       => __( 'Currency Symbol and Position', 'text-domain' ),
					'description' => __( 'This is section details', 'text-domain' ),
					'options'     => array(
						
						array(
							'id'       => 'currency_symbol',
							'title'    => __( 'Currency Symbol', 'text-domain' ),
							'details'  => __( 'Description of user roles select field', 'text-domain' ),
							'multiple' => true,
							'type'     => 'text',
						),
						
						array(
							'id'       => 'currency_position',
							'title'    => __( 'Currency Position', 'text-domain' ),
							'details'  => __( 'Description of Manager roles select field', 'text-domain' ),
							'multiple' => true,
							'type'     => 'select',
							'args'     => array(
								'left'  => 'Left',
								'right' => 'right',
							),
						),
					
					),
				),
			),
		);
		
		$page_2_options = array(
			'page_nav'      => __( '<i class="fas fa-file-pdf"></i> PDF', 'text-domain' ),
			'priority'      => 10,
			'page_settings' => array(
				'section_20' => array(
					'title'       => __( 'PDF General Settings', 'text-domain' ),
					'nav_title'   => __( 'General', 'text-domain' ),
					'description' => __( 'This is section details', 'text-domain' ),
					'options'     => array(
						
						array(
							'id'          => 'pdf_logo',
							'title'       => __( 'Logo ', 'text-domain' ),
							'details'     => __( 'PDF Logo', 'text-domain' ),
							'placeholder' => 'https://i.imgur.com/GD3zKtz.png',
							'type'        => 'media',
						),
						
						array(
							'id'          => 'pdf_bacckground_image',
							'title'       => __( 'Background Image ', 'text-domain' ),
							'details'     => __( 'Select PDF Background Image', 'text-domain' ),
							'placeholder' => 'https://i.imgur.com/GD3zKtz.png',
							'type'        => 'media',
						),
						array(
							'id'      => 'pdf_backgroud_color',
							'title'   => __( 'PDF Background Color', 'text-domain' ),
							// 'details'	=> __('Description of colorpicker field','text-domain'),
							'default' => '#ffffff',
							'value'   => '#ffffff',
							'type'    => 'colorpicker',
						),
						array(
							'id'      => 'pdf_text_color',
							'title'   => __( 'PDF Text Color', 'text-domain' ),
							// 'details'	=> __('Description of colorpicker field','text-domain'),
							'default' => '#000000',
							'value'   => '#000000',
							'type'    => 'colorpicker',
						),
						
						array(
							'id'          => 'pdf_company_name',
							'title'       => __( 'Company Name', 'text-domain' ),
							'details'     => __( 'Enter your Company Name', 'text-domain' ),
							'type'        => 'text',
							'default'     => 'MagePeople',
							'placeholder' => __( 'Company Name', 'text-domain' ),
						),
						
						array(
							'id'          => 'pdf_company_address',
							'title'       => __( 'Company Address', 'text-domain' ),
							'details'     => __( 'Enter your Company Address', 'text-domain' ),
							'type'        => 'textarea',
							'default'     => '',
							'placeholder' => __( 'Company Address', 'text-domain' ),
						),
						
						array(
							'id'          => 'pdf_company_phone',
							'title'       => __( 'Company Phone', 'text-domain' ),
							'details'     => __( 'Enter your Company Phone No', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Company phone', 'text-domain' ),
						),
						
						array(
							'id'          => 'pdf_company_email',
							'title'       => __( 'Company Email', 'text-domain' ),
							'details'     => __( 'Enter your Company Email', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Company Email', 'text-domain' ),
						),
						array(
							'id'          => 'pdf_terms_title',
							'title'       => __( 'Terms & Condition Title', 'text-domain' ),
							'details'     => __( 'Enter Terms & Condition Title', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Terms & Condition Title', 'text-domain' ),
						),
						array(
							'id'              => 'pdf_terms_text',
							'title'           => __( 'Terms & Condition Text', 'text-domain' ),
							'details'         => __( 'Terms & Condition Text', 'text-domain' ),
							'editor_settings' => array(
								'textarea_name' => 'pdf_terms_text_fields',
								'editor_height' => '150px',
							),
							'placeholder'     => __( 'Terms & Condition Text', 'text-domain' ),
							'default'         => '',
							'type'            => 'textarea',
						),
					),
				),
				
				'section_2' => array(
					'title'     => __( 'PDF Email Settings', 'text-domain' ),
					'nav_title' => __( 'Email Settings', 'text-domain' ),
					// 'description' 	=> __('This is section details','text-domain'),
					'options'   => array(
						array(
							'id'       => 'email_send_pdf',
							//'field_name'		    => 'text_multi_field',
							'title'    => __( 'Send Ticket', 'text-domain' ),
							'details'  => __( 'Send pdf to email?', 'text-domain' ),
							'default'  => 'yes',
							'value'    => 'yes',
							'multiple' => false,
							'type'     => 'select',
							'args'     => array(
								'yes' => __( 'Yes', 'text-domain' ),
								'no'  => __( 'No', 'text-domain' ),
							),
						),
						
						array(
							'id'      => 'pdf_email_send_on',
							//'field_name'		    => 'text_multi_field',
							'title'   => __( 'Send Email on', 'text-domain' ),
							'details' => __( 'Send email with the ticket as attachment when these order status comes                            ',
								'text-domain' ),
							// 'default'		=> array('option_3','option_2'),
							// 'value'		    => array('option_2'),
							'type'    => 'checkbox_multi',
							'args'    => array(
								'pending'    => __( 'Pending', 'text-domain' ),
								'processing' => __( 'Processing', 'text-domain' ),
								'completed'  => __( 'Completed', 'text-domain' ),
								'refunded'   => __( 'Refunded', 'text-domain' ),
								'cancelled'  => __( 'Cancelled', 'text-domain' ),
								'on-hold'    => __( 'On Hold', 'text-domain' ),
								'failed'     => __( 'Failed', 'text-domain' ),
							),
						),
						array(
							'id'          => 'pdf_email_subject',
							//'field_name'		    => 'some_id_text_field_1',
							'title'       => __( 'Email Subject', 'text-domain' ),
							'details'     => __( 'Enter Email Subject', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Email Subject', 'text-domain' ),
						),
						array(
							'id'          => 'pdf_email_text',
							'title'       => __( 'Email Content', 'text-domain' ),
							'details'     => __( 'Email Content', 'text-domain' ),
							//'editor_settings'=>array('textarea_name'=>'wp_editor_field', 'editor_height'=>'150px'),
							'placeholder' => __( 'Email Content', 'text-domain' ),
							'default'     => '',
							'type'        => 'wp_editor',
						),
						array(
							'id'          => 'pdf_email_admin_notification_email',
							//'field_name'		    => 'some_id_text_field_1',
							'title'       => __( 'Admin Notification Email', 'text-domain' ),
							'details'     => __( 'Admin Notification Email', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Admin Notification Email', 'text-domain' ),
						),
						array(
							'id'          => 'pdf_email_form_name',
							//'field_name'		    => 'some_id_text_field_1',
							'title'       => __( 'Email From Name', 'text-domain' ),
							'details'     => __( 'Email From Name', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Email From Name', 'text-domain' ),
						),
						array(
							'id'          => 'pdf_email_form_email',
							//'field_name'		    => 'some_id_text_field_1',
							'title'       => __( 'Email From Email', 'text-domain' ),
							'details'     => __( 'Email From Email', 'text-domain' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Email From', 'text-domain' ),
						),
					),
				),
			),
		);
		
		$args         = array(
			'add_in_menu'     => true,
			'menu_type'       => 'sub',
			'menu_name'       => __( 'CSMS Settings', 'text-domain' ),
			'menu_title'      => __( 'CSMS Settings', 'text-domain' ),
			'page_title'      => __( 'CSMS Settings', 'text-domain' ),
			'menu_page_title' => __( 'CSMS Settings', 'text-domain' ),
			'capability'      => "manage_options",
			'cpt_menu'        => "edit.php?post_type=service_management",
			'menu_slug'       => "csms-settings",
			'option_name'     => "csms_settings_manager",
			'menu_icon'       => "dashicons-image-filter",
			
			'item_name'    => "MagePlugin",
			'item_version' => "1.0.0",
			'panels'       => apply_filters( 'mage_submenu_settings_panels',
				array(
					'panelGroup-1' => $page_1_options,
					'panelGroup-2' => $page_2_options,
				) ),
		);
		$AddThemePage = new AddThemePage( $args );
	}
}

new CSMS_Setting_Page();
