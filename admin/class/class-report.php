<?php

/**
 * Class WtbmReport
 */
class CSMSReport {
	
	public function __construct() {
		$this->add_hook();
	}
	
	public function add_hook() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		
	}
	
	public function add_admin_menu() {
		add_submenu_page( 'edit.php?post_type=service_management', __( 'Reports', 'wbtm-menu' ), __( 'Reports', 'wbtm-menu' ), 'manage_options', 'csms-reports', array(
			$this,
			'report_details'
		) );
	}
	
	public function report_details() {
		
		$date_format = get_option( 'date_format' );
		
		if ( 'Y-m-d' == $date_format ) {
			$date_format = "yy-mm-dd";
		}
		
		if ( 'm/d/Y' == $date_format ) {
			$date_format = "mm/dd/yy";
		}
		
		if ( 'd/m/Y' == $date_format ) {
			$date_format = "dd/mm/yy";
		}
		
		if ( 'F j, Y' == $date_format ) {
			$date_format = "MM dd, yy";
		}
		
		$localzed_value = array(
			'date_format' => $date_format,
		);
		
		wp_localize_script( 'mage-plugin-js', 'csms', $localzed_value );
		
		
		?>
        <div class='wrap woocommerce'>
            <div class="export-btn-sec" style='float:right'>
                <form action="" method='get'>
                    <input type="hidden" name='action' value='csv_export_report'>
                    <input type="hidden" name='tab' value='<?php if ( isset( $_GET['tab'] ) ) {
						echo $_GET['tab'];
					} else {
						echo 'last7';
					} ?>'>
                    <input type="hidden" name='post_type' value='service_management'>
					
					<?php if ( isset( $_GET['report_start'] ) ) { ?>
                        <input type="hidden" name='report_start' value='<?php echo $_GET['report_start']; ?>'>
					<?php } ?>
					
					<?php if ( isset( $_GET['report_end'] ) ) { ?>
                        <input type="hidden" name='report_end' value='<?php echo $_GET['report_end']; ?>'>
					<?php } ?>
					
					<?php if ( isset( $_GET['report_order_status'] ) ) { ?>
                        <input type="hidden" name='report_order_status'
                               value='<?php echo $_GET['report_order_status']; ?>'>
					<?php } ?>
					
					<?php if ( isset( $_GET['report_engineer'] ) ) { ?>
                        <input type="hidden" name='report_engineer'
                               value='<?php echo $_GET['report_engineer']; ?>'>
					<?php } ?>
					
					<?php if ( isset( $_GET['report_manager'] ) ) { ?>
                        <input type="hidden" name='report_manager'
                               value='<?php echo $_GET['report_manager']; ?>'>
					<?php } ?>
					
					<?php if ( isset( $_GET['report_priority'] ) ) { ?>
                        <input type="hidden" name='report_priority'
                               value='<?php echo $_GET['report_priority']; ?>'>
					<?php } ?>
					
					<?php if ( isset( $_GET['report_payment'] ) ) { ?>
                        <input type="hidden" name='report_payment'
                               value='<?php echo $_GET['report_payment']; ?>'>
					<?php } ?>


                    <input type="hidden" name='page' value='csms-reports'>
                    <button type='submit'
                            class="button button-secondary"><?php echo esc_html__( 'Export', '' ) ?></button>
                </form>
            </div>

            <nav class="nav-tab-wrapper woo-nav-tab-wrapper">

                <a href="<?php echo get_admin_url(); ?>edit.php?post_type=service_management&page=csms-reports&amp;tab=last7"
                   class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'last7' ) {
					   echo 'nav-tab-active';
				   }
				   if ( ! isset( $_GET['tab'] ) ) {
					   echo 'nav-tab-active';
				   } ?>">Last 7 Days</a>


                <a href="<?php echo get_admin_url(); ?>edit.php?post_type=service_management&page=csms-reports&amp;tab=datewise"
                   class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'datewise' ) {
					   echo 'nav-tab-active';
				   } ?>">Datewise</a>


                <a href="<?php echo get_admin_url(); ?>edit.php?post_type=service_management&page=csms-reports&amp;tab=engineerwise"
                   class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'engineerwise' ) {
					   echo 'nav-tab-active';
				   } ?>">Engineer wise</a>


                <a href="<?php echo get_admin_url(); ?>edit.php?post_type=service_management&page=csms-reports&amp;tab=prioritywise"
                   class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'prioritywise' ) {
					   echo 'nav-tab-active';
				   } ?>">Priority wise</a>

                <a href="<?php echo get_admin_url(); ?>edit.php?post_type=service_management&page=csms-reports&amp;tab=managerwise"
                   class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'managerwise' ) {
					   echo 'nav-tab-active';
				   } ?>">Manager wise</a>

                <a href="<?php echo get_admin_url(); ?>edit.php?post_type=service_management&page=csms-reports&amp;tab=paymentwise"
                   class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'paymentwise' ) {
					   echo 'nav-tab-active';
				   } ?>">Payment wise</a>


            </nav>
        </div>
        <div id="poststuff" class="woocommerce-reports-wide">
			<?php
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'last7' ) {
				$this->last_seven_days();
			}
			if ( ! isset( $_GET['tab'] ) ) {
				$this->last_seven_days();
			}
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'datewise' ) {
				$this->datewise();
			}
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'engineerwise' ) {
				$this->engineerwise();
			}
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'prioritywise' ) {
				$this->prioritywise();
			}
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'managerwise' ) {
				$this->managerwise();
			}
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'paymentwise' ) {
				$this->paymentwise();
			}
			
			?>
        </div>
		<?php
	}
	
	public function last_seven_days() {
		
		$start_date = date( 'Y-m-d', strtotime( '-7 days' ) );
		$end_date   = date( 'Y-m-d' );
		
		$set_left_position  = Customer_Service_Management_Helper::symbol_and_left_position();
		$set_right_position = Customer_Service_Management_Helper::symbol_and_right_position();
		
		$args = array(
			'post_type'      => 'service_management',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				array(
					'key'     => 'received_date',
					'value'   => $start_date,
					'compare' => '>=',
					//'type'    => 'DATETIME',
				),
			)
		);
		
		$loop = new WP_Query( $args );
		
		echo $this->show_stats( $start_date, $end_date );
		?>
        <table class='wp-list-table widefat fixed striped posts'>
            <tr>
                <th>Order No</th>
                <th>Service Title</th>
                <th>Number of Products</th>
                <th>Service Charge</th>
                <th>Advance Payment</th>
                <th>Due Payment</th>
                <th>Priority</th>
                <th>Received Person</th>
                <th>Assign To</th>
                <th>Order Status</th>
            </tr>
			<?php
			
			
			$sum     = 0;
			$advance = 0;
			$due     = 0;
			while ( $loop->have_posts() ) {
				$loop->the_post();
				
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
				
				?>
                <tr>
                    <td><?php echo get_the_ID(); ?></td>
                    <td><?php echo get_the_title(); ?></td>
                    <td>
						<?php echo get_post_meta( get_the_ID(), 'number_of_product', true ); ?>
                    </td>

                    <td><?php $t_price = get_post_meta( get_the_id(), 'service_charge', true );
						echo get_post_meta( get_the_id(), 'service_charge', true ); ?></td>

                    <td><?php $total_advance_price = get_post_meta( get_the_id(), 'advance_payment',
							true );
						echo get_post_meta( get_the_id(), 'advance_payment', true ); ?></td>

                    <td><?php $total_due = get_post_meta( get_the_id(), 'due_payment', true );
						echo get_post_meta( get_the_id(), 'due_payment', true ); ?></td>

                    <td><?php echo ucfirst( $priority_terms[0]->name ); ?></td>

                    <td><?php echo get_post_meta( get_the_ID(), 'received_by', true ) ?></td>

                    <td> <?php echo $user->first_name . " " . $user->last_name; ?> </td>

                    <td>
						<?php echo ucfirst( $status_term->name ); ?>
                    </td>

                </tr>
				<?php
				$sum     = $sum + $t_price;
				$advance = $advance + $total_advance_price;
				$due     = $due + $total_due;
			}
			wp_reset_postdata();
			?>
            <tr>
                <td colspan="2"></td>
                <td colspan=1 align='right'>Total Service Charge</td>
                <td><?php echo $set_left_position . $sum . $set_right_position; ?></td>
                <td colspan=1>Total Advance charge <?php echo $set_left_position . $advance .
				                                              $set_right_position; ?></td>
                <td colspan=1>Total Due charge <?php echo $set_left_position . $due . $set_right_position; ?></td>
            </tr>
        </table>
		
		<?php
	}
	
	public function show_stats( $start_date, $end_date, $order_status = null, $ticket = null, $ticket_type = null ) {
		
		if ( $order_status && $ticket ) {
			
			$args = array(
				'post_type'      => 'service_management',
				'posts_per_page' => - 1,
				'date_query'     => array(
					array(
						'after'     => array(
							'year'  => date( 'Y', strtotime( $start_date ) ),
							'month' => date( 'm', strtotime( $start_date ) ),
							'day'   => date( 'd', strtotime( $start_date ) ),
						),
						'before'    => array(
							'year'  => date( 'Y', strtotime( $end_date ) ),
							'month' => date( 'm', strtotime( $end_date ) ),
							'day'   => date( 'd', strtotime( $end_date ) ),
						),
						'inclusive' => true,
					),
				),
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'status',
						'value'   => $order_status,
						'compare' => '='
					),
					array(
						'key'     => 'assign_user_role',
						'value'   => $ticket,
						'compare' => '='
					)
				)
			);
			
			// change priority status
			if ( $ticket_type == "priority" ) {
				$args = array(
					'post_type'      => 'service_management',
					'posts_per_page' => - 1,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => 'status',
							'value'   => $order_status,
							'compare' => '='
						),
						array(
							'key'     => 'priority',
							'value'   => $ticket,
							'compare' => '='
						)
					)
				);
			}
			
			// change payment status
			if ( $ticket_type == "payment_status" ) {
				$args = array(
					'post_type'      => 'service_management',
					'posts_per_page' => - 1,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => 'status',
							'value'   => $order_status,
							'compare' => '='
						),
						array(
							'key'     => 'payment_status',
							'value'   => $ticket,
							'compare' => '='
						)
					)
				);
			}
			
			// change manager
			if ( $ticket_type == "manager" ) {
				
				$args = array(
					'post_type'      => 'service_management',
					'posts_per_page' => - 1,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => "priority",
							'value'   => $order_status,
							'compare' => '='
						),
						array(
							'key'     => 'received_by',
							'value'   => $ticket,
							'compare' => '='
						)
					)
				);
			}
			
		} elseif ( $order_status ) {
			$args = array(
				'post_type'      => 'service_management',
				'posts_per_page' => - 1,
				'date_query'     => array(
					array(
						'after'     => array(
							'year'  => date( 'Y', strtotime( $start_date ) ),
							'month' => date( 'm', strtotime( $start_date ) ),
							'day'   => date( 'd', strtotime( $start_date ) ),
						),
						'before'    => array(
							'year'  => date( 'Y', strtotime( $end_date ) ),
							'month' => date( 'm', strtotime( $end_date ) ),
							'day'   => date( 'd', strtotime( $end_date ) ),
						),
						'inclusive' => true,
					),
				),
				'meta_query'     => array(
					array(
						'key'     => 'status',
						'value'   => $order_status,
						'compare' => '='
					)
				)
			);
			
		} else {
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
		}
		$loop = new WP_Query( $args );
		$sum  = 0;
		
		$set_left_position  = Customer_Service_Management_Helper::symbol_and_left_position();
		$set_right_position = Customer_Service_Management_Helper::symbol_and_right_position();
		
		foreach ( $loop->posts as $value ) {
			$t_price = get_post_meta( $value->ID, 'service_charge', true );
			$sum     = $sum + $t_price;
		}
		?>
        <div class="show-reports-stat">
            <table>
                <tr>
					<?php if ( $ticket ) { ?>
                        <td>
                            <h3><?php echo esc_html__( 'Ticket', '' ); ?></h3>
							<?php echo get_the_title( $ticket ); ?>
                        </td>
					<?php } ?>
                    <td>
                        <h3><?php echo esc_html__( 'Total Sold', '' ); ?></h3>
						<?php echo $loop->post_count; ?>
                    </td>
                    <td>
                        <h3><?php echo esc_html__( 'Total Amount', '' ); ?></h3>
						<?php echo $set_left_position . $sum . $set_right_position; ?>
                    </td>
                    <td>
                        <h3><?php echo esc_html__( 'Report Start Date', '' ); ?></h3>
						<?php echo date( 'D,d M Y', strtotime( $start_date ) ); ?>
                    </td>
                    <td>
                        <h3><?php echo esc_html__( 'Report End Date', '' ); ?></h3>
						<?php echo date( 'D,d M Y', strtotime( $end_date ) ); ?>
                    </td>
                </tr>
            </table>
        </div>
		<?php
	}
	
	/**
	 *  Datewise reports show
	 */
	public function datewise() {
		$std    = isset( $_GET['report_start'] ) ? $_GET['report_start'] : '';
		$etd    = isset( $_GET['report_end'] ) ? $_GET['report_end'] : date( 'Y-m-d' );
		$status = isset( $_GET['report_order_status'] ) ? $_GET['report_order_status'] : '';
		
		$set_left_position  = Customer_Service_Management_Helper::symbol_and_left_position();
		$set_right_position = Customer_Service_Management_Helper::symbol_and_right_position();
		
		$terms = get_terms( array(
			'taxonomy'   => 'status',
			'hide_empty' => false,
		) );
		
		?>
        <form action="">
            <table>
                <tr>
                    <td>
                        <label for="csms_report_start">

                            Start Date

                            <input type="text" name="report_start" id="csms_report_start"
                                   placeholder="Start Date" value="<?php echo $std; ?>" required
                                   class="datepicker" autocomplete="off">

                            <input type="hidden" name='post_type' value='service_management'>
                            <input type="hidden" name='page' value='csms-reports'>
                            <input type="hidden" name='tab' value='datewise'>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_end">
                            End Date
                            <input type="text" name="report_end" id="wotm_report_end" placeholder="End Date"
                                   value="<?php echo $etd; ?>" class="datepicker" autocomplete="off"
                                   required>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_status">

                            Order Status

                            <select name="report_order_status" id="csms_report_status">
								
								<?php
								foreach ( $terms as $term ) {
									?>

                                    <option value="<?php esc_attr_e( $term->term_id ); ?>"
										<?php if ( $status == $term->term_id ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $term->name ); ?>
                                    </option>
								
								<?php } ?>

                            </select>
                        </label>
                    </td>
                    <td>
                        <button type='submit'
                                class="button button-primary"><?php echo esc_html__( 'Create Report', '' ) ?></button>
                    </td>
                </tr>
            </table>
        </form>
		<?php
		if ( isset( $_GET['report_start'] ) && isset( $_GET['report_end'] ) ) {
			
			$status     = $_GET['report_order_status'];
			$start_date = $_GET['report_start'];
			$end_date   = $_GET['report_end'];
			
			$start_date = date( "Y-m-d", strtotime( $start_date ) );
			$end_date   = date( "Y-m-d", strtotime( $end_date ) );
			
			$args = array(
				'post_type'      => 'service_management',
				'posts_per_page' => - 1,
				'meta_query'     => array(
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
			
			$loop = new WP_Query( $args );
			
			echo $this->show_stats( $start_date, $end_date, $status );
			?>
            <table class='wp-list-table widefat fixed striped posts'>
                <tr>
                    <th>Order No</th>
                    <th>Service Title</th>
                    <th>Number of Products</th>
                    <th>Service Charge</th>
                    <th>Advance Payment</th>
                    <th>Due Payment</th>
                    <th>Priority</th>
                    <th>Received Person</th>
                    <th>Assign To</th>
                    <th>Order Status</th>
                </tr>
				<?php
				$sum     = 0;
				$advance = 0;
				$due     = 0;
				while ( $loop->have_posts() ) {
					$loop->the_post();
					
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
					
					$user_ID = get_post_meta( get_the_ID(), 'assign_user_role', true );
					$user    = get_user_by( 'id', $user_ID );
					
					$status      = get_post_meta( get_the_ID(), 'status', true );
					$status_term = get_term_by( 'term_id', $status, 'status' );
					
					?>
                    <tr>
                        <td><?php echo get_the_ID(); ?></td>
                        <td><?php echo get_the_title(); ?></td>
                        <td>
							<?php echo get_post_meta( get_the_ID(), 'number_of_product', true ); ?>
                        </td>

                        <td><?php $t_price = get_post_meta( get_the_id(), 'service_charge', true );
							echo get_post_meta( get_the_id(), 'service_charge', true ); ?></td>

                        <td><?php $total_advance_price = get_post_meta( get_the_id(), 'advance_payment',
								true );
							echo get_post_meta( get_the_id(), 'advance_payment', true ); ?></td>

                        <td><?php $total_due = get_post_meta( get_the_id(), 'due_payment', true );
							echo get_post_meta( get_the_id(), 'due_payment', true ); ?></td>

                        <td><?php echo ucfirst( $priority_terms[0]->name ); ?></td>

                        <td><?php echo get_post_meta( get_the_ID(), 'received_by', true ); ?></td>

                        <td> <?php echo $user->first_name . " " . $user->last_name; ?> </td>

                        <td>
							<?php echo ucfirst( $status_term->name ); ?>
                        </td>

                    </tr>
					<?php
					$sum     = $sum + $t_price;
					$advance = $advance + $total_advance_price;
					$due     = $due + $total_due;
				}
				wp_reset_postdata();
				?>
                <tr>
                    <td colspan="2"></td>
                    <td colspan=1 align='right'>Total Service Charge</td>
                    <td><?php echo $set_left_position . $sum . $set_right_position; ?></td>
                    <td colspan=1>Total Advance charge <?php echo $set_left_position . $advance .
					                                              $set_right_position; ?></td>
                    <td colspan=1>Total Due charge <?php echo $set_left_position . $due . $set_right_position; ?></td>
                </tr>
            </table>
			
			<?php
		}
	}//end method datewise
	
	/**
	 * Engineer wise reports
	 */
	public function engineerwise() {
		
		global $csms_functions;
		
		$set_left_position  = Customer_Service_Management_Helper::symbol_and_left_position();
		$set_right_position = Customer_Service_Management_Helper::symbol_and_right_position();
		
		
		$std      = isset( $_GET['report_start'] ) ? $_GET['report_start'] : '';
		$etd      = isset( $_GET['report_end'] ) ? $_GET['report_end'] : date( 'Y-m-d' );
		$status   = isset( $_GET['report_order_status'] ) ? $_GET['report_order_status'] : '';
		$engineer = isset( $_GET['report_engineer'] ) ? $_GET['report_engineer'] : '';
		
		$roles = $csms_functions->csms_get_option( 'csms_settings',
			'user_roles_field', '' );
		
		$role = implode( ' ', $roles );
		
		$args  = array(
			'role' => $role,
		);
		$users = get_users( $args );
		
		$terms = get_terms( array(
			'taxonomy'   => 'status',
			'hide_empty' => false,
		) );
		
		?>
        <form action="">
            <table>
                <tr>
                    <td>
                        <label for="csms_report_ticket">
                            Engineer
                            <select name="report_engineer" id="csms_report_ticket">
								<?php
								
								foreach ( $users as $user ) {
									?>

                                    <option value="<?php esc_attr_e( $user->ID ); ?>"
										<?php if ( $engineer == $user->ID ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $user->display_name ); ?>
                                    </option>
								
								<?php } ?>
                            </select>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_start">
                            Start Date
                            <input type="text" name="report_start" id="csms_report_start" placeholder="Start Date"
                                   value="<?php echo $std; ?>" class="datepicker" autocomplete="off" required>
                            <input type="hidden" name='post_type' value='service_management'>
                            <input type="hidden" name='page' value='csms-reports'>
                            <input type="hidden" name='tab' value='engineerwise'>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_end">
                            End Date
                            <input type="text" name="report_end" id="csms_report_end" placeholder="End Date"
                                   value="<?php echo $etd; ?>" class="datepicker" autocomplete="off" required>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_status">
                            Order Status
                            <select name="report_order_status" id="csms_report_status">
								
								<?php
								foreach ( $terms as $term ) {
									?>

                                    <option value="<?php esc_attr_e( $term->term_id ); ?>"
										<?php if ( $status == $term->term_id ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $term->name ); ?>
                                    </option>
								
								<?php } ?>
								
								<?php
								$selected = "";
								if ( $status == 'all' ) {
									$selected = "selected";
								}
								?>

                                <option value="all" <?php echo $selected; ?>>All</option>

                            </select>
                        </label>
                    </td>
                    <td>
                        <button type='submit' class="button button-primary">Create Report</button>
                    </td>
                </tr>
            </table>
        </form>
		<?php
		if ( isset( $_GET['report_start'] ) && isset( $_GET['report_end'] ) ) {
			$order_status = $_GET['report_order_status'];
			$start_date   = $_GET['report_start'];
			$end_date     = $_GET['report_end'];
			$engineer     = $_GET['report_engineer'];
			
			$start_date = date( "Y-m-d", strtotime( $start_date ) );
			$end_date   = date( "Y-m-d", strtotime( $end_date ) );
			
			
			if ( 'all' == $order_status ) {
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
					),
				);
			} else {
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
			}
			
			
			$loop = new WP_Query( $args );
			
			echo $this->show_stats( $start_date, $end_date, $order_status, $engineer );
			?>
            <table class='wp-list-table widefat fixed striped posts'>
                <tr>
                    <th>Order No</th>
                    <th>Service Title</th>
                    <th>Number of Products</th>
                    <th>Service Charge</th>
                    <th>Advance Payment</th>
                    <th>Due Payment</th>
                    <th>Priority</th>
                    <th>Received Person</th>
                    <th>Assign To</th>
                    <th>Order Status</th>
                </tr>
				<?php
				$sum     = 0;
				$advance = 0;
				$due     = 0;
				while ( $loop->have_posts() ) {
					$loop->the_post();
					
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
					
					$user_ID = get_post_meta( get_the_ID(), 'assign_user_role', true );
					$user    = get_user_by( 'id', $user_ID );
					
					$status      = get_post_meta( get_the_ID(), 'status', true );
					$status_term = get_term_by( 'term_id', $status, 'status' );
					
					?>
                    <tr>
                        <td><?php echo get_the_ID(); ?></td>
                        <td><?php echo get_the_title(); ?></td>
                        <td>
							<?php echo get_post_meta( get_the_ID(), 'number_of_product', true ); ?>
                        </td>

                        <td><?php $t_price = get_post_meta( get_the_id(), 'service_charge', true );
							echo get_post_meta( get_the_id(), 'service_charge', true ); ?></td>

                        <td><?php $total_advance_price = get_post_meta( get_the_id(), 'advance_payment',
								true );
							echo get_post_meta( get_the_id(), 'advance_payment', true ); ?></td>

                        <td><?php $total_due = get_post_meta( get_the_id(), 'due_payment', true );
							echo get_post_meta( get_the_id(), 'due_payment', true ); ?></td>

                        <td><?php echo ucfirst( $priority_terms[0]->name ); ?></td>

                        <td><?php echo get_post_meta( get_the_ID(), 'received_by', true ) ?></td>

                        <td> <?php echo $user->first_name . " " . $user->last_name; ?> </td>

                        <td>
							<?php echo ucfirst( $status_term->name ); ?>
                        </td>

                    </tr>
					<?php
					$sum     = $sum + $t_price;
					$advance = $advance + $total_advance_price;
					$due     = $due + $total_due;
				}
				wp_reset_postdata();
				?>
                <tr>
                    <td colspan="2"></td>
                    <td colspan=1 align='right'>Total Service Charge</td>
                    <td><?php echo $set_left_position . $sum . $set_right_position; ?></td>
                    <td colspan=1>Total Advance charge <?php echo $set_left_position . $advance .
					                                              $set_right_position; ?></td>
                    <td colspan=1>Total Due charge <?php echo $set_left_position . $due . $set_right_position; ?></td>
                </tr>
            </table>
			
			<?php
		}
		
	}//end method engineerwise
	
	
	/**
	 * Engineer wise reports
	 */
	public function prioritywise() {
		
		$set_left_position  = Customer_Service_Management_Helper::symbol_and_left_position();
		$set_right_position = Customer_Service_Management_Helper::symbol_and_right_position();
		
		$std             = isset( $_GET['report_start'] ) ? $_GET['report_start'] : '';
		$etd             = isset( $_GET['report_end'] ) ? $_GET['report_end'] : date( 'Y-m-d' );
		$status          = isset( $_GET['report_order_status'] ) ? $_GET['report_order_status'] : '';
		$report_priority = isset( $_GET['report_priority'] ) ? $_GET['report_priority'] : '';
		
		
		$status_terms = get_terms( array(
			'taxonomy'   => 'status',
			'hide_empty' => false,
		) );
		
		$priority_terms = get_terms( array(
			'taxonomy'   => 'priority',
			'hide_empty' => false,
		) );
		
		?>
        <form action="">
            <table>
                <tr>
                    <td>
                        <label for="csms_report_ticket">
                            Priority
                            <select name="report_priority" id="csms_report_ticket">
								<?php
								
								foreach ( $priority_terms as $priority ) {
									
									$priority_tax_meta = get_term_meta( $priority->term_id,
										'priority_value', true );
									
									?>
                                    <option value="<?php echo $priority_tax_meta; ?>"
										<?php if ( $report_priority == $priority_tax_meta ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $priority->name ); ?>
                                    </option>
								
								<?php } ?>
                            </select>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_start">
                            Start Date
                            <input type="text" name="report_start" id="csms_report_start" placeholder="Start Date"
                                   value="<?php echo $std; ?>" class="datepicker" autocomplete="off" required>
                            <input type="hidden" name='post_type' value='service_management'>
                            <input type="hidden" name='page' value='csms-reports'>
                            <input type="hidden" name='tab' value='prioritywise'>
                        </label>
                    </td>
                    <td>
                        <label for="wotm_report_end">
                            End Date
                            <input type="text" name="report_end" id="csms_report_end" placeholder="End Date"
                                   value="<?php echo $etd; ?>" class="datepicker" autocomplete="off" required>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_status">
                            Order Status
                            <select name="report_order_status" id="csms_report_status">
								
								<?php
								foreach ( $status_terms as $term ) {
									?>

                                    <option value="<?php esc_attr_e( $term->term_id ); ?>"
										<?php if ( $status == $term->term_id ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $term->name ); ?>
                                    </option>
								
								<?php } ?>
								
								<?php
								$selected = "";
								if ( $status == 'all' ) {
									$selected = "selected";
								}
								?>

                                <option value="all" <?php echo $selected; ?>>All</option>

                            </select>
                        </label>
                    </td>
                    <td>
                        <button type='submit' class="button button-primary">Create Report</button>
                    </td>
                </tr>
            </table>
        </form>
		<?php
		if ( isset( $_GET['report_start'] ) && isset( $_GET['report_end'] ) ) {
			$status          = $_GET['report_order_status'];
			$start_date      = $_GET['report_start'];
			$end_date        = $_GET['report_end'];
			$report_priority = $_GET['report_priority'];
			
			$start_date = date( "Y-m-d", strtotime( $start_date ) );
			$end_date   = date( "Y-m-d", strtotime( $end_date ) );
			
			if ( 'all' == $status ) {
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
					
					),
				);
			} else {
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
			}
			
			
			$loop = new WP_Query( $args );
			
			echo $this->show_stats( $start_date, $end_date, $status, $report_priority, "priority" );
			
			?>
            <table class='wp-list-table widefat fixed striped posts'>
                <tr>
                    <th>Order No</th>
                    <th>Service Title</th>
                    <th>Number of Products</th>
                    <th>Service Charge</th>
                    <th>Advance Payment</th>
                    <th>Due Payment</th>
                    <th>Priority</th>
                    <th>Received Person</th>
                    <th>Assign To</th>
                    <th>Order Status</th>
                </tr>
				<?php
				$sum     = 0;
				$advance = 0;
				$due     = 0;
				while ( $loop->have_posts() ) {
					$loop->the_post();
					
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
					
					
					$user_ID = get_post_meta( get_the_ID(), 'assign_user_role', true );
					$user    = get_user_by( 'id', $user_ID );
					
					$status      = get_post_meta( get_the_ID(), 'status', true );
					$status_term = get_term_by( 'term_id', $status, 'status' );
					
					?>
                    <tr>
                        <td><?php echo get_the_ID(); ?></td>
                        <td><?php echo get_the_title(); ?></td>
                        <td>
							<?php echo get_post_meta( get_the_ID(), 'number_of_product', true ); ?>
                        </td>

                        <td><?php $t_price = get_post_meta( get_the_id(), 'service_charge', true );
							echo get_post_meta( get_the_id(), 'service_charge', true ); ?></td>

                        <td><?php $total_advance_price = get_post_meta( get_the_id(), 'advance_payment',
								true );
							echo get_post_meta( get_the_id(), 'advance_payment', true ); ?></td>

                        <td><?php $total_due = get_post_meta( get_the_id(), 'due_payment', true );
							echo get_post_meta( get_the_id(), 'due_payment', true ); ?></td>

                        <td><?php echo ucfirst( $priority_terms[0]->name ); ?></td>

                        <td><?php echo get_post_meta( get_the_ID(), 'received_by', true ) ?></td>

                        <td> <?php echo $user->first_name . " " . $user->last_name; ?> </td>

                        <td>
							<?php echo ucfirst( $status_term->name ); ?>
                        </td>

                    </tr>
					<?php
					$sum     = $sum + $t_price;
					$advance = $advance + $total_advance_price;
					$due     = $due + $total_due;
				}
				wp_reset_postdata();
				?>
                <tr>
                    <td colspan="2"></td>
                    <td colspan=1 align='right'>Total Service Charge</td>
                    <td><?php echo $set_left_position . $sum . $set_right_position; ?></td>
                    <td colspan=1>Total Advance charge <?php echo $set_left_position . $advance .
					                                              $set_right_position; ?></td>
                    <td colspan=1>Total Due charge <?php echo $set_left_position . $due . $set_right_position; ?></td>
                </tr>
            </table>
			
			<?php
		}
		
	}//end method engineerwise
	
	/**
	 * Engineer wise reports
	 */
	public function managerwise() {
		
		global $csms_functions;
		
		$set_left_position  = Customer_Service_Management_Helper::symbol_and_left_position();
		$set_right_position = Customer_Service_Management_Helper::symbol_and_right_position();
		
		$std = isset( $_GET['report_start'] ) ? $_GET['report_start'] : '';
		$etd = isset( $_GET['report_end'] ) ? $_GET['report_end'] : date( 'Y-m-d' );
		
		$status          = isset( $_GET['report_order_status'] ) ? $_GET['report_order_status'] : '';
		$report_manager  = isset( $_GET['report_manager'] ) ? $_GET['report_manager'] : '';
		$report_priority = isset( $_GET['report_priority'] ) ? $_GET['report_priority'] : '';
		$report_payment  = isset( $_GET['report_order_payment'] ) ? $_GET['report_order_payment'] : '';
		
		$roles = $csms_functions->csms_get_option( 'csms_settings',
			'manager_roles_field', '' );
		
		$role = implode( ' ', $roles );
		
		$args = array(
			'role' => $role,
		);
		
		$users = get_users( $args );
		
		$status_terms = get_terms( array(
			'taxonomy'   => 'status',
			'hide_empty' => false,
		) );
		
		$priority_terms = get_terms( array(
			'taxonomy'   => 'priority',
			'hide_empty' => false,
		) );
		
		$payment_terms = get_terms( array(
			'taxonomy'   => 'payment_status',
			'hide_empty' => false,
		) );
		
		
		?>
        <form action="">
            <table>
                <tr>
                    <td>
                        <label for="csms_report_ticket">
                            Manager Wise
                            <select name="report_manager" id="csms_report_ticket">
								<?php
								
								foreach ( $users as $user ) {
									
									?>
                                    <option value="<?php esc_attr_e( $user->display_name ); ?>"
										<?php if ( $report_manager == $user->ID ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $user->display_name ); ?>
                                    </option>
								
								<?php } ?>
                            </select>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_start">
                            Start Date
                            <input type="text" name="report_start" id="csms_report_start" placeholder="Start Date"
                                   value="<?php echo $std; ?>" class="datepicker" autocomplete="off" required>
                            <input type="hidden" name='post_type' value='service_management'>
                            <input type="hidden" name='page' value='csms-reports'>
                            <input type="hidden" name='tab' value='managerwise'>
                        </label>
                    </td>
                    <td>
                        <label for="wotm_report_end">
                            End Date
                            <input type="text" name="report_end" id="csms_report_end" placeholder="End Date"
                                   value="<?php echo $etd; ?>" class="datepicker" autocomplete="off" required>
                        </label>
                    </td>

                    <td>
                        <label for="csms_report_status">
                            Priority
                            <select name="report_priority" id="csms_report_status">
								
								<?php
								
								foreach ( $priority_terms as $priority ) {
									
									$priority_tax_meta = get_term_meta( $priority->term_id,
										'priority_value', true );
									
									?>
                                    <option value="<?php echo $priority_tax_meta; ?>"
										<?php if ( $report_priority == $priority_tax_meta ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $priority->name ); ?>
                                    </option>
								
								<?php } ?>
								
								<?php
								$selected = "";
								if ( $report_priority == 'all_priority' ) {
									$selected = "selected";
								}
								?>

                                <option value="all_priority" <?php echo $selected; ?>>All</option>

                            </select>
                        </label>
                    </td>

                    <td>
                        <label for="csms_report_status">
                            Payment
                            <select name="report_order_payment" id="csms_report_status">
								
								<?php
								
								foreach ( $payment_terms as $payment ) {
									?>
                                    <option value="<?php echo $payment->term_id; ?>"
										<?php if ( $report_payment == $payment->term_id ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $payment->name ); ?>
                                    </option>
								<?php } ?>
								
								<?php
								$selected = "";
								if ( $report_payment == 'all_payment' ) {
									$selected = "selected";
								}
								?>

                                <option value="all_payment" <?php echo $selected; ?>>All</option>

                            </select>
                        </label>
                    </td>


                    <td>
                        <label for="csms_report_status">
                            Order Status
                            <select name="report_order_status" id="csms_report_status">
								
								<?php
								
								foreach ( $status_terms as $term ) {
									
									?>
                                    <option value="<?php esc_attr_e( $term->term_id ); ?>"
										<?php if ( $status == $term->term_id ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $term->name ); ?>
                                    </option>
								<?php } ?>
								
								<?php
								$selected = "";
								if ( $status == 'all_status' ) {
									$selected = "selected";
								}
								?>

                                <option value="all_status" <?php echo $selected; ?>>All</option>

                            </select>
                        </label>
                    </td>
                    <td>
                        <button type='submit' class="button button-primary">Create Report</button>
                    </td>
                </tr>
            </table>
        </form>
		
		<?php
		
		if ( isset( $_GET['report_start'] ) && isset( $_GET['report_end'] ) ) {
			
			$status          = $_GET['report_order_status'];
			$start_date      = $_GET['report_start'];
			$end_date        = $_GET['report_end'];
			$report_manager  = isset( $_GET['report_manager'] ) ? $_GET['report_manager'] : '';
			$report_priority = isset( $_GET['report_priority'] ) ? $_GET['report_priority'] : '';
			$report_payment  = isset( $_GET['report_order_payment'] ) ? $_GET['report_order_payment'] : '';
			
			$start_date = date( "Y-m-d", strtotime( $start_date ) );
			$end_date   = date( "Y-m-d", strtotime( $end_date ) );
			
			if ( 'all_status' == $status && 'all_payment' != $report_payment || 'all_priority' == $report_priority ) {
				
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
							'key'     => 'received_date',
							'value'   => array( $start_date, $end_date ),
							'compare' => 'BETWEEN',
							'type'    => 'DATE'
						),
					
					
					),
				);
			}
			
			if ( 'all_priority' == $report_priority && 'all_status' != $status &&
			     'all_payment' != $report_payment ) {
				
				
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
			}
			
			if ( 'all_payment' == $report_payment && 'all_status' != $status && 'all_priority' != $report_priority ) {
				
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
			}
			
			if ( 'all_status' == $status && 'all_payment' == $report_payment && 'all_priority' != $report_priority ) {
				
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
							'key'     => 'received_by',
							'value'   => $report_manager,
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
			}
			
			if ( 'all_status' == $status && 'all_priority' == $report_priority && 'all_payment' != $report_payment ) {
				
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
							'key'     => 'received_by',
							'value'   => $report_manager,
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
			}
			
			if ( 'all_priority' == $report_priority && 'all_payment' == $report_payment && 'all_status' != $status ) {
				
				$args = array(
					'post_type'      => 'service_management',
					'posts_per_page' => - 1,
					
					'meta_query' => array(
						
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
			}
			
			
			if ( 'all_status' == $status && 'all_payment' == $report_payment &&
			     'all_priority' == $report_priority ) {
				
				$args = array(
					'post_type'      => 'service_management',
					'posts_per_page' => - 1,
					
					'meta_query' => array(
						
						array(
							'key'     => 'received_by',
							'value'   => $report_manager,
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
			}
			
			if ( 'all_status' != $status && 'all_payment' != $report_payment &&
			     'all_priority' != $report_priority ) {
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
							'key'     => 'status',
							'value'   => $status,
							'compare' => '='
						),
						
						array(
							'key'     => 'received_by',
							'value'   => $report_manager,
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
			}
			
			
			$loop = new WP_Query( $args );
			
			echo $this->show_stats( $start_date, $end_date, $report_priority, $report_manager,
				"manager" );
			
			?>
            <table class='wp-list-table widefat fixed striped posts'>
                <tr>
                    <th>Order No</th>
                    <th>Service Title</th>
                    <th>Number of Products</th>
                    <th>Service Charge</th>
                    <th>Advance Payment</th>
                    <th>Due Payment</th>
                    <th>Priority</th>
                    <th>Received Person</th>
                    <th>Assign To</th>
                    <th>Order Status</th>
                </tr>
				<?php
				$sum     = 0;
				$advance = 0;
				$due     = 0;
				while ( $loop->have_posts() ) {
					$loop->the_post();
					
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
					
					
					$user_ID = get_post_meta( get_the_ID(), 'assign_user_role', true );
					$user    = get_user_by( 'id', $user_ID );
					
					$status      = get_post_meta( get_the_ID(), 'status', true );
					$status_term = get_term_by( 'term_id', $status, 'status' );
					
					?>
                    <tr>
                        <td><?php echo get_the_ID(); ?></td>
                        <td><?php echo get_the_title(); ?></td>
                        <td>
							<?php echo get_post_meta( get_the_ID(), 'number_of_product', true ); ?>
                        </td>

                        <td><?php $t_price = get_post_meta( get_the_id(), 'service_charge', true );
							echo get_post_meta( get_the_id(), 'service_charge', true ); ?></td>

                        <td><?php $total_advance_price = get_post_meta( get_the_id(), 'advance_payment',
								true );
							echo get_post_meta( get_the_id(), 'advance_payment', true ); ?></td>

                        <td><?php $total_due = get_post_meta( get_the_id(), 'due_payment', true );
							echo get_post_meta( get_the_id(), 'due_payment', true ); ?></td>

                        <td><?php echo ucfirst( $priority_terms[0]->name ); ?></td>

                        <td><?php echo get_post_meta( get_the_ID(), 'received_by', true ) ?></td>

                        <td> <?php echo $user->first_name . " " . $user->last_name; ?> </td>

                        <td>
							<?php echo ucfirst( $status_term->name ); ?>
                        </td>

                    </tr>
					<?php
					$sum     = $sum + $t_price;
					$advance = $advance + $total_advance_price;
					$due     = $due + $total_due;
				}
				wp_reset_postdata();
				?>
                <tr>
                    <td colspan="2"></td>
                    <td colspan=1 align='right'>Total Service Charge</td>
                    <td><?php echo $set_left_position . $sum . $set_right_position; ?></td>
                    <td colspan=1>Total Advance charge <?php echo $set_left_position . $advance .
					                                              $set_right_position; ?></td>
                    <td colspan=1>Total Due charge <?php echo $set_left_position . $due . $set_right_position; ?></td>
                </tr>

            </table>
			
			<?php
		}
		
	}//end method managerwise
	
	/**
	 * Payment wise reports
	 */
	public function paymentwise() {
		
		$set_left_position  = Customer_Service_Management_Helper::symbol_and_left_position();
		$set_right_position = Customer_Service_Management_Helper::symbol_and_right_position();
		
		$std            = isset( $_GET['report_start'] ) ? $_GET['report_start'] : '';
		$etd            = isset( $_GET['report_end'] ) ? $_GET['report_end'] : date( 'Y-m-d' );
		$status         = isset( $_GET['report_order_status'] ) ? $_GET['report_order_status'] : '';
		$report_payment = isset( $_GET['report_payment'] ) ? $_GET['report_payment'] : '';
		
		
		$payment_status = get_terms( array(
			'taxonomy'   => 'payment_status',
			'hide_empty' => false,
		) );
		
		$status_terms = get_terms( array(
			'taxonomy'   => 'status',
			'hide_empty' => false,
		) );
		
		?>
        <form action="">
            <table>
                <tr>
                    <td>
                        <label for="csms_report_ticket">
                            Payment Status
                            <select name="report_payment" id="csms_report_ticket">
								<?php
								
								foreach ( $payment_status as $payment ) {
									?>
                                    <option value="<?php echo $payment->term_id; ?>"
										<?php if ( $report_payment == $payment->term_id ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $payment->name ); ?>
                                    </option>
								<?php } ?>
                            </select>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_start">
                            Start Date
                            <input type="text" name="report_start" id="csms_report_start" placeholder="Start Date"
                                   value="<?php echo $std; ?>" class="datepicker" autocomplete="off" required>
                            <input type="hidden" name='post_type' value='service_management'>
                            <input type="hidden" name='page' value='csms-reports'>
                            <input type="hidden" name='tab' value='paymentwise'>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_end">
                            End Date
                            <input type="text" name="report_end" id="csms_report_end" placeholder="End Date"
                                   value="<?php echo $etd; ?>" class="datepicker" autocomplete="off" required>
                        </label>
                    </td>
                    <td>
                        <label for="csms_report_status">
                            Order Status
                            <select name="report_order_status" id="csms_report_status">
								
								<?php
								foreach ( $status_terms as $term ) {
									?>

                                    <option value="<?php esc_attr_e( $term->term_id ); ?>"
										<?php if ( $status == $term->term_id ) {
											echo 'selected';
										} ?> >
										<?php echo ucfirst( $term->name ); ?>
                                    </option>
								
								<?php } ?>
								
								<?php
								$selected = "";
								if ( $status == 'all' ) {
									$selected = "selected";
								}
								?>

                                <option value="all" <?php echo $selected; ?>>All</option>

                            </select>
                        </label>
                    </td>
                    <td>
                        <button type='submit' class="button button-primary">Create Report</button>
                    </td>
                </tr>
            </table>
        </form>
		
		<?php
		
		if ( isset( $_GET['report_start'] ) && isset( $_GET['report_end'] ) ) {
			
			$status         = $_GET['report_order_status'];
			$start_date     = $_GET['report_start'];
			$end_date       = $_GET['report_end'];
			$report_payment = $_GET['report_payment'];
			
			$start_date = date( "Y-m-d", strtotime( $start_date ) );
			$end_date   = date( "Y-m-d", strtotime( $end_date ) );
			
			
			if ( 'all' == $status ) {
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
					
					
					),
				);
			} else {
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
			}
			
			$loop = new WP_Query( $args );
			
			echo $this->show_stats( $start_date, $end_date, $status, $report_payment,
				"payment_status" );
			
			?>
            <table class='wp-list-table widefat fixed striped posts'>
                <tr>
                    <th>Order No</th>
                    <th>Service Title</th>
                    <th>Number of Products</th>
                    <th>Service Charge</th>
                    <th>Advance Payment</th>
                    <th>Due Payment</th>
                    <th>Priority</th>
                    <th>Received Person</th>
                    <th>Assign To</th>
                    <th>Order Status</th>
                </tr>
				<?php
				$sum     = 0;
				$advance = 0;
				$due     = 0;
				while ( $loop->have_posts() ) {
					$loop->the_post();
					
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
					
					$user_ID = get_post_meta( get_the_ID(), 'assign_user_role', true );
					$user    = get_user_by( 'id', $user_ID );
					
					$status      = get_post_meta( get_the_ID(), 'status', true );
					$status_term = get_term_by( 'term_id', $status, 'status' );
					
					?>
                    <tr>

                        <td><?php echo get_the_ID(); ?></td>
                        <td><?php echo get_the_title(); ?></td>

                        <td>
							<?php
							echo get_post_meta( get_the_ID(), 'number_of_product', true );
							?>
                        </td>

                        <td><?php $t_price = get_post_meta( get_the_id(), 'service_charge', true );
							echo get_post_meta( get_the_id(), 'service_charge', true ); ?></td>

                        <td><?php $total_advance_price = get_post_meta( get_the_id(), 'advance_payment',
								true );
							echo get_post_meta( get_the_id(), 'advance_payment', true ); ?></td>

                        <td><?php $total_due = get_post_meta( get_the_id(), 'due_payment', true );
							echo get_post_meta( get_the_id(), 'due_payment', true ); ?></td>

                        <td><?php echo ucfirst( $priority_terms[0]->name ); ?></td>

                        <td><?php echo get_post_meta( get_the_ID(), 'received_by', true ) ?></td>

                        <td> <?php echo $user->first_name . " " . $user->last_name; ?> </td>

                        <td>
							<?php echo ucfirst( $status_term->name ); ?>
                        </td>

                    </tr>
					<?php
					$sum     = $sum + $t_price;
					$advance = $advance + $total_advance_price;
					$due     = $due + $total_due;
				}
				wp_reset_postdata();
				?>
                <tr>
                    <td colspan="2"></td>
                    <td colspan=1 align='right'>Total Service Charge</td>
                    <td><?php echo $set_left_position . $sum . $set_right_position; ?></td>
                    <td colspan=1>Total Advance charge <?php echo $set_left_position . $advance .
					                                              $set_right_position; ?></td>
                    <td colspan=1>Total Due charge <?php echo $set_left_position . $due . $set_right_position; ?></td>
                </tr>

            </table>
			
			<?php
		}
		
	}//end method paymentwise
	
	
}//end class CSMSReport

global $csmsreport;
$csmsreport = new CSMSReport();