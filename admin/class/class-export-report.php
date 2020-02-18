<?php

class CSMSReportExport {
	
	public function __construct() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'csv_export_report' ) {
			add_action( 'admin_init', array( $this, 'report_csv_export' ) );
		}
	}
	
	
	private function create_header_row( $post_id = null, $tab ) {
		
		$default_info = array(
			'Order ID',
			'Service Title',
			'Number of Products',
			'Service Charge',
			'Advanced Payment',
			'Due Payment',
			'Priority',
			'Received Person',
			'Assign To',
			'Order Status',
		);
		
		if ( $tab == 'last7' || $tab == 'datewise' || $tab == 'engineerwise' || $tab == 'prioritywise' ||
		     $tab == 'managerwise' || $tab == 'paymentwise' ) {
			return $default_info;
		}
	}
	
	
	private function create_data_row( $post_id, $tab ) {
		
		$user_ID = get_post_meta( get_the_ID(), 'assign_user_role', true );
		$user    = get_user_by( 'id', $user_ID );
		
		$status      = get_post_meta( get_the_ID(), 'status', true );
		$status_term = get_term_by( 'term_id', $status, 'status' );
		
		$priority = get_post_meta( get_the_ID(), 'priority', true );
		
		$args           = array(
			'hide_empty' => false, // also retrieve terms which are not used yet
			'meta_query' => array(
				array(
					'key'     => 'priority_value',
					'value'   => $priority,
					'compare' => '='
				)
			),
			'taxonomy'   => 'priority',
		);
		$priority_terms = get_terms( $args );
		
		$default_data = array(
			get_the_ID(),
			get_the_title(),
			get_post_meta( get_the_ID(), 'number_of_product', true ),
			get_post_meta( get_the_ID(), 'service_charge', true ),
			get_post_meta( get_the_ID(), 'advance_payment', true ),
			get_post_meta( get_the_ID(), 'due_payment', true ),
			ucfirst( $priority_terms[0]->name ),
			get_post_meta( get_the_ID(), 'received_by', true ),
			$user->first_name . " " . $user->last_name,
			ucfirst( $status_term->name ),
		);
		
		
		if ( $tab == 'last7' || $tab == 'datewise' || $tab == 'engineerwise' || $tab == 'prioritywise' ||
		     $tab == 'managerwise' || $tab == 'paymentwise' ) {
			return $default_data;
		}
	}
	
	/**
	 * @return bool
	 */
	public function report_csv_export() {
		// Check for current user privileges
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		// Check if we are in WP-Admin
		if ( ! is_admin() ) {
			return false;
		}
		// Nonce Check
		// $nonce = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
		// if ( ! wp_verify_nonce( $nonce, 'download_csv' ) ) {
		//     die( 'Security check error' );
		// }
		$post_id = get_the_ID();
		
		ob_start();
		$domain   = $_SERVER['SERVER_NAME'];
		$filename = 'csms_Report_Export_' . $domain . '_' . time() . '.csv';
		
		/**
		 * End condition last7
		 */
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'last7' ) {
			
			$header_row = $this->create_header_row( $post_id, $_GET['tab'] );
			$data_rows  = array();
			
			$start_date = date( 'Y-m-d', strtotime( '-7 days' ) );
			
			$args = array(
				'post_type'      => 'service_management',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					array(
						'key'     => 'received_date',
						'value'   => $start_date,
						'compare' => '>=',
					),
				)
			);
		}//end condition last7
		
		/**
		 * Datewise Condition
		 */
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'datewise' ) {
			
			$header_row = $this->create_header_row( $post_id, $_GET['tab'] );
			$data_rows  = array();
			
			$start_date   = $_GET['report_start'];
			$end_date     = $_GET['report_end'];
			$order_status = $_GET['report_order_status'];
			
			$args = array(
				'post_type'      => 'service_management',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					array(
						'key'     => 'status',
						'value'   => $order_status,
						'compare' => '='
					),
					
					array(
						'key'     => 'received_date',
						'value'   => array( $start_date, $end_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE'
					),
				
				),
			);
		}//end condition datewise
		
		/**
		 * Engineer Condition
		 */
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'engineerwise' ) {
			
			$header_row = $this->create_header_row( $post_id, $_GET['tab'] );
			$data_rows  = array();
			
			$start_date = $_GET['report_start'];
			$end_date   = $_GET['report_end'];
			$status     = $_GET['report_order_status'];
			$engineer   = $_GET['report_engineer'];
			
			
			$args = array(
				'post_type'      => 'service_management',
				'posts_per_page' => - 1,
				
				'meta_query' => array(
					array(
						'key'     => 'assign_user_role',
						'value'   => $engineer,
						'compare' => '='
					),
					array(
						'key'     => 'received_date',
						'value'   => array( $start_date, $end_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE'
					),
					
					array(
						'key'     => 'status',
						'value'   => $status,
						'compare' => '='
					),
				
				),
			);
			
			
		}//end condition engineerwise
		
		/**
		 * Priority Condition
		 */
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'prioritywise' ) {
			
			$header_row = $this->create_header_row( $post_id, $_GET['tab'] );
			$data_rows  = array();
			
			$start_date      = $_GET['report_start'];
			$end_date        = $_GET['report_end'];
			$status          = $_GET['report_order_status'];
			$report_priority = $_GET['report_priority'];
			
			$args = array(
				'post_type'      => 'service_management',
				'posts_per_page' => - 1,
				
				'meta_query' => array(
					
					array(
						'key'     => 'priority',
						'value'   => $report_priority,
						'compare' => '='
					),
					
					array(
						'key'     => 'received_date',
						'value'   => array( $start_date, $end_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE'
					),
					
					array(
						'key'     => 'status',
						'value'   => $status,
						'compare' => '='
					),
				
				),
			);
			
		}//end condition prioritywise
		
		/**
		 * Manager Condition
		 */
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'managerwise' ) {
			
			$header_row = $this->create_header_row( $post_id, $_GET['tab'] );
			$data_rows  = array();
			
			$start_date      = $_GET['report_start'];
			$end_date        = $_GET['report_end'];
			$status          = $_GET['report_order_status'];
			$report_priority = $_GET['report_priority'];
			$report_payment  = $_GET['report_order_payment'];
			$report_manager  = $_GET['report_manager'];
			
			$args = array(
				'post_type'      => 'service_management',
				'posts_per_page' => - 1,
				
				'meta_query' => array(
					
					array(
						'key'     => 'priority',
						'value'   => $report_priority,
						'compare' => '='
					),
					
					array(
						'key'     => 'payment_status',
						'value'   => $report_payment,
						'compare' => '='
					),
					
					array(
						'key'     => 'received_by',
						'value'   => $report_manager,
						'compare' => '='
					),
					
					array(
						'key'     => 'status',
						'value'   => $status,
						'compare' => '='
					),
					
					array(
						'key'     => 'received_date',
						'value'   => array( $start_date, $end_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE'
					),
				
				
				),
			);
			
		}//end condition managerwise
		
		/**
		 * Payment Condition
		 */
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'paymentwise' ) {
			
			$header_row = $this->create_header_row( $post_id, $_GET['tab'] );
			$data_rows  = array();
			
			$start_date      = $_GET['report_start'];
			$end_date        = $_GET['report_end'];
			$status          = $_GET['report_order_status'];
			$report_payment  = $_GET['report_payment'];
			
			$args = array(
				'post_type'      => 'service_management',
				'posts_per_page' => - 1,
				
				'meta_query' => array(
					
					array(
						'key'     => 'payment_status',
						'value'   => $report_payment,
						'compare' => '='
					),
					
					array(
						'key'     => 'received_date',
						'value'   => array( $start_date, $end_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE'
					),
					
					array(
						'key'     => 'status',
						'value'   => $status,
						'compare' => '='
					),
				
				),
			);
			
		}//end condition paymentwise
		
		
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			
			$row         = $this->create_data_row( get_the_id(), $_GET['tab'] );
			$data_rows[] = $row;
			
		}
		wp_reset_postdata();
		
		$fh = @fopen( 'php://output', 'w' );
		fprintf( $fh, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-type: text/csv' );
		header( "Content-Disposition: attachment; filename={$filename}" );
		header( 'Expires: 0' );
		header( 'Pragma: public' );
		fputcsv( $fh, $header_row );
		foreach ( $data_rows as $data_row ) {
			fputcsv( $fh, $data_row );
		}
		fclose( $fh );
		
		ob_end_flush();
		
		die();
	}
}

new CSMSReportExport();