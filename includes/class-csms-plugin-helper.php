<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 * Tour helper class
 *
 * Declare every function by static keyword.
 */
if ( ! class_exists( 'Customer_Service_Management_Helper' ) ) {
	
	/**
	 * Class Tour_Booking_Helper
	 *
	 * This is helper class for tour booking
	 */
	class Customer_Service_Management_Helper {
		
		/**
		 * @return null|string
		 */
		public static function symbol_and_left_position() {
			
			global $csms_functions;
			
			$symbol = $csms_functions->csms_get_option( 'csms_settings', 'currency_symbol', '$' );
			
			$positions = $csms_functions->csms_get_option( 'csms_settings', 'currency_position', 'left' );
			
			$position = implode( ' ', $positions );
			
			$left_position = "";
			
			if ( $position == 'left' ) {
				$left_position = $symbol;
			}
			
			return $left_position;
		}//end method symbol_and_left_position
		
		/**
		 * @return null|string
		 */
		public static function symbol_and_right_position() {
			
			global $csms_functions;
			
			$symbol = $csms_functions->csms_get_option( 'csms_settings', 'currency_symbol', '$' );
			
			$positions = $csms_functions->csms_get_option( 'csms_settings', 'currency_position', 'left' );
			
			$position = implode( ' ', $positions );
			
			$right_position = "";
			
			if ( $position == 'right' ) {
				$right_position = $symbol;
			}
			
			return $right_position;
		}//end method symbol_and_left_position
		
		/**
		 *
		 */
		public static function priority_color_details_for_admin() {
			
			$args  = array(
				'hide_empty' => false,
				'taxonomy'   => 'priority',
			);
			$terms = get_terms( $args );
			
			?>
            <div class="color_warning">
                <ul>
					<?php
					foreach ( $terms as $term ) {
						
						$color = get_term_meta( $term->term_id, 'colorpicker_field_priority', true );
						
						?>
                        <li><b><?php echo $term->name; ?></b> - <span
                                    style="margin-left:5px;border-right: 20px solid <?php echo $color; ?>"></span></li>
					
					<?php } ?>
                </ul>
            </div>
			
			<?php
			
		}//end method priority_color_details_for_admin
		
		
		public static function services( $posts ) {
			
			?>
            <style>
                .grid-container {
                    display: grid;
                    grid-column-gap: 50px;
                    grid-template-columns: auto auto auto;
                    padding: 10px;
                }

                .grid-item {
                    background-color: rgba(255, 255, 255, 0.8);
                    padding: 20px;
                    font-size: 15px;
                    margin-top: 10px;
                    width: 25vw;
                    border-radius: 10px;
                }
            </style>
			<?php
			
			foreach ( $posts as $post ) {
				
				$download_url = CSMS_PDF::get_invoice_ajax_url( array( 'order_id' => $post->ID ) );
				
				$post_edit_url = get_edit_post_link( $post->ID );
				
				$receive_date     = get_post_meta( $post->ID, 'received_date', true );
				$delivery_date    = get_post_meta( $post->ID, 'delivery_date', true );
				$post_work_status = get_post_meta( $post->ID, 'status', true );
				
				$priority = get_post_meta( $post->ID, 'priority', true );
				
				$user_ID = get_post_meta( $post->ID, 'assign_user_role', true );
				$user    = get_user_by( 'id', $user_ID );
				
				$args  = array(
					'hide_empty' => false,
					'meta_query' => array(
						array(
							'key'     => 'priority_value',
							'value'   => $priority,
							'compare' => 'LIKE',
						),
					),
					'taxonomy'   => 'priority',
				);
				$terms = get_terms( $args );
				
				$border_color = get_term_meta( $terms[0]->term_id,
					'colorpicker_field_priority',
					true );
				
				?>

                <div class="grid-item" style="border: 5px solid<?php echo $border_color; ?>">
                    <ul>
                        <li><b>Post Title :</b> <?php echo $post->post_title; ?></li>
                        <li><b>Received Date :</b> <?php echo $receive_date; ?></li>
                        <li><b>Delivery Date :</b> <?php echo $delivery_date; ?></li>
                        <li><b>Priority :</b> <?php echo ucfirst( $terms[0]->name ); ?></li>

                        <li><b>Work Status :</b>
                            <form action="" method="post">

                                <select name="status_from_all_services">
									
									<?php
									$status_terms = get_terms( [
										'taxonomy'   => "status",
										'hide_empty' => false,
									] );
									
									
									foreach ( $status_terms as $term ) {
										?>
                                        <option value="<?php echo $term->term_id; ?>"
											<?php if ( $post_work_status == $term->term_id ) {
												echo "selected";
											} ?>>
											
											<?php echo $term->name; ?>

                                        </option>
									<?php } ?>
                                </select>
                                <input type="hidden" name="service_id" value="<?php echo $post->ID; ?>">
                                <input type="submit" name="csms_update_service_status" class="button button-primary"
                                       value="save">

                            </form>

                        </li>

                        <li><b>Assign To :</b> <?php echo $user->first_name . " " . $user->last_name; ?></li>

                        <li style="margin-top: 20px"><b>Actions : </b>
                            <a href="<?php echo $post_edit_url; ?>">
                                <button class="button button-primary">Edit</button>
                            </a>
                            |
                            <a href="<?php echo $download_url; ?>">
                                <button class="button button-secondary">Download</button>
                            </a>
                        </li>

                    </ul>
                </div>
			<?php }
			
		}//end method services
		
		
		/**
		 * Display order and price information in right sideber
		 */
		public static function order_information() {
			
			$post_id = isset( $_GET['post'] ) ? $_GET['post'] : "";
			
			$get_pricing = maybe_unserialize( get_post_meta( $post_id, 'pricing', true ) );
			
			$service_total = isset( $get_pricing['service_total'] ) ? $get_pricing['service_total'] : 0;
			$product_total = isset( $get_pricing['product_total'] ) ? $get_pricing['product_total'] : 0;
			$grand_total   = isset( $get_pricing['grand_total'] ) ? $get_pricing['grand_total'] : 0;
			
			?>
            <div class="order_details" style="margin: 0 auto">

                <ul>
                    <li>Product Total - $<span class="product_total"><?php echo $product_total; ?></span>
                        <input type="hidden" name="pricing[product_total]" class="product_total_input"
                               value="<?php echo $product_total; ?>">
                    </li>

                    <li>Services Total - $<span
                                class="services_total"><?php echo $service_total; ?></span>
                        <input type="hidden" name="pricing[service_total]" class="service_total_input"
                               value="<?php echo $service_total; ?>">
                    </li>

                    <li>Grand Total - $<span class="grand_total"><?php echo $grand_total; ?></span>
                        <input type="hidden" name="pricing[grand_total]" class="grand_total_input"
                               value="<?php echo $grand_total; ?>">
                    </li>
                </ul>
            </div>
			<?php
		}//end method order_information
		
		
		public static function create_product_order( $post_id ) {
			
			$order_post_id = get_post_meta( $post_id, 'order_post_id', true );
			
			//if ( $post_id != $order_post_id ) {
				//create products order
				$status_id = get_post_meta( $post_id, 'status', true );
				$status    = isset( get_term( $status_id, 'status' )->name ) ? get_term( $status_id,
					'status' )->name : "";
				
				$get_products = maybe_unserialize( get_post_meta( $post_id,
					"product_pricing",
					true ) );
				
				if ( ! is_array( $get_products ) ) {
					$get_products = array();
				}
				
				// build order data
				$order_data = array(
					'post_name'      => 'order-',
					'post_type'      => 'shop_order',
					'post_title'     => 'Order &ndash;',
					'post_status'    => 'wc-processing',
					'ping_status'    => 'closed',
					'post_excerpt'   => 'Order Created From Dashboard.',
					'post_author'    => get_current_user_id(),
					'post_password'  => uniqid( 'order_' ),
					'post_date'      => date( 'Y-m-d H:i:s e' ),
					'comment_status' => 'open',
				);
				
				remove_action( "save_post",
					array(
						"Customer_Service_Management_Helper",
						"create_product_order",
					),
					99 );
				
				// create order
				$order_id = wp_insert_post( $order_data, true );
				
				add_action( "save_post",
					array( "Customer_Service_Management_Helper", "create_product_order" ),
					99 );
				
				$total = 0;
				foreach ( $get_products as $key => $product_pricing ) {
					if ( "product_price_last_count" != $key ) {
						
						if ( ! is_wp_error( $order_id ) ) {
						    
						    $total = $product_pricing['product_price'] * $product_pricing['qty'];
							
							add_post_meta( $order_id,
								'_payment_method_title',
								'Import',
								true );
							
							//add_post_meta($order_id, '_order_total', $total, true);
							
							add_post_meta( $order_id,
								'customer_user',
								get_current_user_id(),
								true );
							
							add_post_meta( $order_id,
								'_order_currency',
								get_woocommerce_currency_symbol(),
								true );
							
							
							// get product by item_id
							$product = new WC_Product( $product_pricing['product_id'] );
							if ( $product ) {
								
								// add item
								$item_id = wc_add_order_item( $order_id,
									array(
										'order_item_name' => $product->get_title(),
										get_the_title(),
										'order_item_type' => 'line_item',
									) );
								
								if ( $item_id ) {
									wc_add_order_item_meta( $item_id,
										'_qty',
										$product_pricing['qty'] );
									
									wc_add_order_item_meta( $item_id,
										'_tax_class',
										'no_tax' );
									
									wc_add_order_item_meta( $item_id,
										'_product_id',
										get_the_ID() );
									
									wc_add_order_item_meta( $item_id,
										'_variation_id',
										'' );
									
									wc_add_order_item_meta( $item_id, '_line_subtotal',
                                        wc_format_decimal( $total ) );
									
									wc_add_order_item_meta( $item_id, '_line_total',
                                        wc_format_decimal( $total ) );
									
									
									wc_add_order_item_meta( $item_id,
										'_line_tax',
										wc_format_decimal( 0 ) );
									
									wc_add_order_item_meta( $item_id,
										'_line_subtotal_tax',
										wc_format_decimal( 0 ) );
									
									wc_add_order_item_meta( $item_id,
										'_post_id',
										$post_id );
									
									wc_add_order_item_meta( $item_id,
										'_product_id',
										$product_pricing['product_id'] );
									
								}
								
								// set order status as completed
								wp_set_object_terms( $order_id,
									'completed',
									'shop_order_status' );
								
							}
						}
					}//end product_last_count
				}//end foreach loop
			//}//check existing products
			
			update_post_meta( $post_id, 'order_post_id', $post_id );
			
		}//end method create_product_order
		
		
	}//end class Customer_Service_Management_Helper
}//end if condition