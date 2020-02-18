<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

/**
 * @package    CSMS_Plugin
 * @subpackage Mage_Plugin/admin
 * @author     MagePeople team <magepeopleteam@gmail.com>
 */
class CSMS_Plugin_Admin {
	
	private $plugin_name;
	
	private $version;
	
	public function __construct() {
		
		// $this->plugin_name = $plugin_name;
		// $this->version = $version;
		
		$this->load_admin_dependencies();
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		//Show services by ajax and based on priority
		add_action( 'wp_ajax_priority_based_services', array( $this, 'priority_based_services' ) );
		add_action( 'wp_ajax_nopriv_priority_based_services', array( $this, 'priority_based_services' ) );
		
		//Show services by ajax and based on delivary date
		add_action( 'wp_ajax_delivery_based_services', array( $this, 'delivery_based_services' ) );
		add_action( 'wp_ajax_nopriv_delivery_based_services', array( $this, 'delivery_based_services' ) );
		
		//Append service pricing
		add_action( 'wp_ajax_service_pricing', array( $this, "append_service_pricing" ) );
		add_action( 'wp_ajax_product_pricing', array( $this, "append_product_pricing" ) );
		
		//check user availability
		add_action( "wp_ajax_check_user_availability", array( $this, "check_user_availability" ) );
		
		
		//custom post title change
		add_filter( 'enter_title_here', array( $this, 'custom_post_title_change' ), 20, 2 );
		
		add_filter( 'manage_service_management_posts_columns',
			array(
				$this,
				'add_custom_column',
			) );
		
		add_action( 'manage_service_management_posts_custom_column',
			array( $this, 'add_data_into_custom_column', ),
			10,
			2 );
		
		//Add dashboard widget for engineers
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget_for_engineer' ) );
		
		//Generate pdf
		add_action( 'wp_ajax_generate_pdf', array( $this, 'csms_generate_pdf' ) );
		add_action( 'wp_ajax_nopriv_generate_pdf', array( $this, 'csms_generate_pdf' ) );
		
		//For submenu hook
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
		
		//Add meta boxes for extra information (form builder)
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes_for_form_builder' ) );
		add_action( 'save_post', array( $this, 'save_settings_meta' ), 99, 2 );
		
		//Send mail to customer
		add_action( 'save_post', array( $this, 'sending_email_to_customer' ), 99, 2 );
		
		//Retrieve the history of posts
		//add_action( 'save_post', array( $this, 'the_history' ), 13, 2 );
		
		//search services by mobile number
		add_action( 'restrict_manage_posts', array( $this, 'search_mobile_number_filter' ) );
		add_filter( 'parse_query', array( $this, 'services_after_filtering_by_mobile_number' ) );
		
		//save service and product pricing
		add_action( "save_post", array( $this, "save_service_and_product_pricing" ), 99 );
		
		add_action( "save_post", array( "Customer_Service_Management_Helper", "create_product_order" ),99 );
		
	}//end magic method construct
	
	/**
	 * Check user availability, if found auto fill user information[name, address, email].
	 */
	public function check_user_availability() {
		
		$mobile_number = isset( $_POST['mobile_number'] ) ? $_POST['mobile_number'] : "";
		
		$users = get_users( array(
				'meta_query' => array(
					array(
						'key'     => 'user_number',
						'value'   => $mobile_number,
						'compare' => '=',
					),
				),
			)
		);
		
		if ( is_array( $users ) && sizeof( $users ) > 0 ) {
			foreach ( $users as $user ) {
				$output['user_name']    = get_user_meta( $user->ID, 'user_name', true );
				$output['user_email']   = get_user_meta( $user->ID, 'user_email', true );
				$output['user_address'] = get_user_meta( $user->ID, 'user_address', true );
			}
			echo json_encode( $output );
		} else {
			echo "nodata";
		}
		
		exit();
		
	}//end method check_user_availability
	
	
	/**
	 * Save the history of post when post update and save.
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function the_history( $post_id ) {
		
		if ( "service_management" == get_post_type( $post_id ) ) {
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}
			
			if ( ! current_user_can( 'manage_options' ) ) {
				return $post_id;
			}
			
			$post = get_post( $post_id );
			if ( $post->post_status == 'trash' or $post->post_status == 'auto-draft' ) {
				return $post_id;
			}
			
			$u_time          = get_the_time( 'U' );
			$u_modified_time = get_the_modified_time( 'U' );
			
			$updated_date = date( "Y-m-d" );
			$updated_time = current_time( 'h:i a' );
			
			if ( $u_modified_time >= $u_time + 86400 ) {
				$updated_date = date( "Y-m-d" );
				$updated_time = current_time( 'h:i a' );
			}
			
			$get_status_id = get_post_meta( $post_id, "status", true );
			$status        = ! empty( $get_status_id ) ? get_term( $get_status_id, 'status' )->name : "";
			
			$get_notes = get_post_meta( $post_id, "note", true );
			
			$title = get_the_title( $post_id );
			
			$new_post = array(
				'post_title'    => $title,
				'post_content'  => '',
				'post_category' => array(),
				'tags_input'    => array(),
				'post_status'   => 'publish',
			);
			
			remove_action( "save_post", array( $this, "the_history" ), 13 );
			
			$new_post_id = wp_insert_post( $new_post );
			
			update_post_meta( $new_post_id, "updated_time", $updated_time );
			update_post_meta( $new_post_id, "updated_date", $updated_date );
			update_post_meta( $new_post_id, "post_status", $status );
			update_post_meta( $new_post_id, "status_id", $get_status_id );
			update_post_meta( $new_post_id, "post_id", $post_id );
			update_post_meta( $new_post_id, "user", get_current_user_id() );
			update_post_meta( $new_post_id, "notes", $get_notes );
			
			add_action( "save_post", array( $this, "the_history" ), 13, 2 );
			
			//prevent to exist auto draft post in database
			/*global $wpdb;
			$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'posts WHERE post_title = "Auto Draft"' );
			*/
			
		}//check post types
	}//end method the_history
	
	
	/**
	 * Save product and services price from meta.
	 *
	 * @param $post_id
	 *
	 * @throws Exception
	 */
	public function save_service_and_product_pricing( $post_id ) {
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		$services = isset( $_POST['service_pricing'] ) ? maybe_serialize( $_POST['service_pricing'] ) : "";
		$product  = isset( $_POST['product_pricing'] ) ? maybe_serialize( $_POST['product_pricing'] ) : "";
		$pricing  = isset( $_POST['pricing'] ) ? maybe_serialize( $_POST['pricing'] ) : "";
		
		update_post_meta( $post_id, "service_pricing", $services );
		update_post_meta( $post_id, "product_pricing", $product );
		update_post_meta( $post_id, "pricing", $pricing );
		
		//check user exist or not, if not exist insert an user
		$user_email   = get_post_meta( $post_id, 'email', true );
		$user_name    = get_post_meta( $post_id, 'customer_name', true );
		$user_number  = get_post_meta( $post_id, 'mobile_number', true );
		$user_address = get_post_meta( $post_id, 'customer_address', true );
		
		$users = get_users( array(
				'meta_query' => array(
					array(
						'key'     => 'user_number',
						'value'   => $user_number,
						'compare' => '=',
					),
				),
			)
		);
		
		//check email exist
		if ( ! email_exists( $user_email ) && sizeof( $users ) <= 0 ) {
			
			// An array, object, or WP_User object of user data arguments.
			$user_id = wp_create_user( $user_name, '', $user_email );
			
			update_user_meta( $user_id, 'user_name', $user_name );
			update_user_meta( $user_id, 'user_email', $user_email );
			update_user_meta( $user_id, 'user_number', $user_number );
			update_user_meta( $user_id, 'user_address', $user_address );
		}
		
	}//end method save_service_pricing
	
	/**
	 * Add a service by selected service in metabox
	 */
	public function append_service_pricing() {
		
		$post_id_arr = isset( $_POST['post_id'] ) ? $_POST['post_id'] : "";
		
		$last_count = isset( $_POST['last_count_val'] ) ? intval( $_POST['last_count_val'] ) + 1 : 0;
		
		foreach ( $post_id_arr as $post_id ) {
			
			$last_count ++;
			
			$get_post = get_post( $post_id );
			
			$service_price = get_post_meta( $get_post->ID, "service_price", true );
			
			$field = '<tr><td>' . $get_post->post_title . '<input type="hidden" name="service_pricing[' . $last_count . '][service_title]" value="' . $get_post->post_title . '"></td>
           <td>' . $service_price . '<input type="hidden" class="service_price"
		name="service_pricing[' . $last_count . '][service_price]"  value="' . $service_price . '"></td>
		
		<td> <input type="number" name="service_pricing[' . $last_count . '][qty]" min="1" value="1"
		class="service_qty qty" data-price="' . $service_price . '"></td>
		
		<td><a href="" class="button service_price_remove"><span class="dashicons dashicons-trash" style="margin-top: 3px;color: red;"></span>' . esc_html__( "Remove" ) . '</a></td></tr>';
			
			echo $field;
		}
		
		exit();
		
	}//end method append_service_pricing
	
	/**
	 * Add a service by selected service in metabox
	 */
	public function append_product_pricing() {
		
		$post_id_arr = isset( $_POST['post_id'] ) ? $_POST['post_id'] : "";
		$last_count  = isset( $_POST['last_count_val'] ) ? intval( $_POST['last_count_val'] ) + 1 : 0;
		
		foreach ( $post_id_arr as $post_id ) {
			
			$last_count ++;
			
			$product = wc_get_product( $post_id );
			
			$field = '<tr><td>' . $product->get_title() . '<input type="hidden" name="product_pricing[' . $last_count . '][product_title]" value="' . $product->get_title() . '"></td>

           <td>' . $product->get_regular_price() . '<input type="hidden" class="product_price"
		name="product_pricing[' . $last_count . '][product_price]"  value="' . $product->get_regular_price() . '">
		
		<input type="hidden" class="product_id"
		name="product_pricing[' . $last_count . '][product_id]"  value="' . $product->get_id() . '">
		
		</td>
		
		<td> <input type="number" name="product_pricing[' . $last_count . '][qty]" min="1" value="1"
		class="product_qty qty" data-price="' . $product->get_regular_price() . '"></td>
		
		<td><a href="" class="button product_price_remove"><span class="dashicons dashicons-trash" style="margin-top: 3px;color: red;"></span>' . esc_html__( "Remove" ) . '</a></td></tr>';
			
			echo $field;
		}
		
		exit();
		
	}//end method append_service_pricing
	
	/**
	 *
	 */
	public function service_pricing() {
		?>
        <form action="" method="post">
            <div class="pricing_section">

                <div class="service_prices">
                    <div class="service_price">
                        <label for="">Select Your Services : </label>
                        <select name="" class="regular-text selected_services" multiple>
							
							<?php
							$args      = array(
								'posts_per_page' => - 1,
								'post_type'      => 'service_pricing',
								'meta_query'     => array(
									array(
										'key'     => 'billing_type',
										'value'   => 'service',
										'compare' => '=',
									),
								),
							);
							$the_query = get_posts( $args );
							
							foreach ( $the_query as $query ) {
								
								$service_price = get_post_meta( $query->ID, "service_price", true );
								?>
                                <option value="<?php echo $query->ID; ?>">
									<?php echo $query->post_title . " #" . $service_price; ?>
                                </option>
							<?php } ?>
                        </select>

                        <a href="#" class="button service_price_add">
                        <span class="dashicons dashicons-plus-alt"
                              style="margin-top:3px;color: green;"></span>Add Services
                        </a>

                    </div>
                </div>

                <div class="service_pricing_table">
                    <table>
                        <tr>
                            <th>Services Title</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
						<?php
						$post_id = isset( $_GET['post'] ) ? $_GET['post'] : "";
						
						$get_service_pricing = maybe_unserialize( get_post_meta( $post_id, 'service_pricing', true ) );
						
						$last_count = isset( $get_service_pricing['service_price_last_count'] ) ? $get_service_pricing['service_price_last_count'] : 0;
						
						if ( ! is_array( $get_service_pricing ) ) {
							$get_service_pricing = array();
						}
						
						
						foreach ( $get_service_pricing as $key => $service_pricing ) {
							if ( "service_price_last_count" != $key ) {
								?>
                                <tr>
                                    <td>
										<?php echo $service_pricing['service_title']; ?>
                                        <input type="hidden"
                                               name="service_pricing[<?php echo $key; ?>][service_title]"
                                               value="<?php echo $service_pricing['service_title']; ?>">
                                    </td>

                                    <td>
										<?php echo $service_pricing['service_price']; ?>

                                        <input type="hidden" class="service_price"
                                               name="service_pricing[<?php echo $key; ?>][service_price]"
                                               value="<?php echo $service_pricing['service_price']; ?>">
                                    </td>

                                    <td>

                                        <input type="number" min="1"
                                               value="<?php echo $service_pricing['qty']; ?>"
                                               name="service_pricing[<?php echo $key; ?>][qty]"
                                               class="service_qty qty">

                                    </td>

                                    <td>
                                        <a href="" class="button service_price_remove"><span
                                                    class="dashicons dashicons-trash"
                                                    style="margin-top: 3px;color: red;"></span>Remove</a>
                                    </td>
                                </tr>
								
								<?php
							}
						} ?>
                    </table>
                    <input type="hidden" name="submit_service_pricing">
					<?php wp_nonce_field( 'service_prices_metabox_nonce', 'service_prices_nonce' ); ?>

                    <input type="hidden" name="service_pricing[service_price_last_count]"
                           class="service_price_last_count" value="<?php echo $last_count; ?>">
                </div>

                <!--------------
               
                Product Section start
                
                ---------------->

                <div class="product_prices">
                    <div class="product_price">
                        <label for="">Select Your Product : </label>
                        <select name="" class="selected_product" multiple>
							
							<?php
							
							$args      = array(
								'post_type'      => 'product',
								'posts_per_page' => - 1,
								'post_status'    => 'publish',
							);
							$the_query = get_posts( $args );
							
							foreach ( $the_query as $query ) {
								?>
                                <option value="<?php echo $query->ID; ?>">
									<?php echo $query->post_title; ?>
                                </option>
							<?php } ?>
                        </select>

                        <a href="#" class="button product_price_add">
                        <span class="dashicons dashicons-plus-alt"
                              style="margin-top:3px;color: green;"></span>Add Products
                        </a>
                    </div>
                </div>

                <div class="product_pricing_table">
                    <table>
                        <tr>
                            <th>Product Title</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
						<?php
						
						$get_product_pricing = maybe_unserialize( get_post_meta( $post_id, 'product_pricing', true ) );
						
						$last_count = isset( $get_product_pricing['product_price_last_count'] ) ? $get_product_pricing['product_price_last_count'] : 0;
						
						if ( ! is_array( $get_product_pricing ) ) {
							$get_product_pricing = array();
						}
						
						foreach ( $get_product_pricing as $key => $product_pricing ) {
							if ( "product_price_last_count" != $key ) {
								
								?>
                                <tr>
                                    <td>
										<?php echo $product_pricing['product_title']; ?>
                                        <input type="hidden"
                                               name="product_pricing[<?php echo $key; ?>][product_title]"
                                               value="<?php echo $product_pricing['product_title']; ?>">
                                    </td>

                                    <td>
										<?php echo $product_pricing['product_price']; ?>

                                        <input type="hidden" class="product_price"
                                               name="product_pricing[<?php echo $key; ?>][product_price]"
                                               value="<?php echo $product_pricing['product_price']; ?>">

                                        <input type="hidden" class="product_id"
                                               name="product_pricing[<?php echo $key; ?>][product_id]"
                                               value="<?php echo $product_pricing['product_id']; ?>">
                                    </td>

                                    <td>
                                        <input type="number" min="1"
                                               value="<?php echo $product_pricing['qty']; ?>"
                                               name="product_pricing[<?php echo $key; ?>][qty]"
                                               class="product_qty qty">
                                    </td>

                                    <td>
                                        <a href="" class="button product_price_remove"><span
                                                    class="dashicons dashicons-trash"
                                                    style="margin-top: 3px;color: red;"></span>Remove</a>
                                    </td>
                                </tr>
								
								<?php
								
								
							}
						}
						?>
                    </table>
                    <input type="hidden" name="submit_product_pricing">
					<?php wp_nonce_field( 'service_prices_metabox_nonce', 'service_prices_nonce' ); ?>

                    <input type="hidden" name="product_pricing[product_price_last_count]"
                           class="product_price_last_count" value="<?php echo $last_count; ?>">
                </div>

            </div>
        </form>
		<?php
	}//end method service_pricing
	
	
	/**
	 * Fire the query for getting the services.
	 *
	 * @param $query
	 */
	function services_after_filtering_by_mobile_number( $query ) {
		
		global $pagenow;
		
		if ( isset( $_GET['post_type'] ) ) {
			$type = $_GET['post_type'];
			if ( 'service_management' == $type && is_admin() && $pagenow == 'edit.php' && isset( $_GET['filter_by_mobile_number'] ) && $_GET['filter_by_mobile_number'] != '' ) {
				
				$query->query_vars['meta_key']   = 'mobile_number';
				$query->query_vars['meta_value'] = $_GET['filter_by_mobile_number'];
			}
		}
	}//end method services_after_filtering_by_mobile_number
	
	/**
	 * Search services by user mobile number.
	 */
	public function search_mobile_number_filter() {
		
		$type = 'service_management';
		
		if ( isset( $_GET['post_type'] ) ) {
			$type = $_GET['post_type'];
		}
		
		if ( $type == 'service_management' ) {
			$current_v = isset( $_GET['filter_by_mobile_number'] ) ? $_GET['filter_by_mobile_number'] : '';
			?>
            <input type="text" name="filter_by_mobile_number" value="<?php esc_attr_e( $current_v ); ?>"
                   class="regular-text" placeholder="Mobile number...."/>
		<?php }
	}//end method search_mobile_number_filter
	
	
	/**
	 * Notify customer for inform via email with his/her services work status.
	 *
	 * @param $post_ID
	 * @param $post
	 */
	public function sending_email_to_customer( $post_ID, $post ) {
		
		$status_id = get_post_meta( $post_ID, 'status', true );
		
		$enable_email    = get_term_meta( $status_id, 'enable_disable_email', true );
		$email_recipient = get_term_meta( $status_id, 'recipient', true );
		
		//check email enable and who will get email
		if ( "enable" == $enable_email && "customer" == $email_recipient ) {
			
			$to            = get_post_meta( $post_ID, 'email', true );
			$email_subject = get_term_meta( $status_id, 'subject', true );
			$email_body    = get_term_meta( $status_id, 'email_body', true );
			
			wp_mail( $to, $email_subject, $email_body );
		}
		
	}//end method sending_email_to_customer
	
	/**
	 *  Add field in meta form settings
	 */
	public function add_meta_boxes_for_form_builder() {
		
		global $csms_functions;
		
		$form_settings = $csms_functions->csms_get_option( 'csms_settings_manager',
			'add_field_in_meta_info',
			array() );
		
		if ( is_array( $form_settings ) && sizeof( $form_settings ) > 0 ) {
			add_meta_box(
				'add_meta_for_user_field',
				'Additional Information for Services',
				array( $this, 'display_form' ),
				array( 'service_management' )
			);
		}
		
		add_meta_box(
			'status',
			'Work Status History',
			array( $this, 'display_status' ),
			array( 'service_management' ),
			'side',
			'low'
		);
		
		add_meta_box(
			'notes',
			'Show Notes',
			array( $this, 'display_notes' ),
			array( 'service_management' ),
			'side',
			'low'
		);
		
		add_meta_box(
			'service_pricing',
			'Service Pricing',
			array( $this, 'service_pricing' ),
			array( 'service_management' )
		);
		
		
		add_meta_box(
			'order_information',
			'order_information',
			array( "Customer_Service_Management_Helper", 'order_information' ),
			array( 'service_management' ),
			'side',
			'low'
		);
		
		
	}//end method add_field_in_meta
	
	public function display_notes() {
		
		$args = array(
			'post_type'   => 'post',
			'post_status' => 'publish',
			'numberposts' => - 1,
			'order'       => "DESC",
			'meta_query'  => array(
				array(
					'key'     => 'post_id',
					'value'   => get_the_ID(),
					'compare' => '=',
				),
			),
		);
		
		$the_query = get_posts( $args );
		
		if ( is_array( $the_query ) && sizeof( $the_query ) > 0 ) {
			?>
            <div class="services-history">
				
				<?php
				foreach ( $the_query as $history ) {
					
					$updated_time = get_post_meta( $history->ID, "updated_time", true );
					$updated_date = get_post_meta( $history->ID, "updated_date", true );
					$user_id      = get_post_meta( $history->ID, "user", true );
					$notes        = get_post_meta( $history->ID, "notes", true );
					
					$user_name = get_current_user_id() ? new WP_User( $user_id ) :
						wp_get_current_user();
					
					if ( ! empty( $user_name->first_name ) ) {
						if ( ! empty( $user_name->last_name ) ) {
							$person_name = ucfirst( $user_name->first_name . ' ' . $user_name->last_name );
						}
					}
					
					if ( ! empty( $notes ) ) {
						?>
                        <style>
                            .status-color {
                                font-weight: bold;
                                font-size: 15px;
                            }
                        </style>
                        <p>
							<?php echo "<span class='status-color'>" . $notes . "</span> Create By " . $person_name . " At " . $updated_time . " In " . $updated_date; ?>
                        </p>
						<?php
					}
				} ?>
            </div>
			<?php
		} else {
			_e( "No Notes Found" );
		}
		
	}
	
	/**
	 * Display all work status in post edit page.
	 */
	public function display_status() {
		
		$args = array(
			'post_type'   => 'post',
			'post_status' => 'publish',
			'numberposts' => - 1,
			'order'       => "DESC",
			'meta_query'  => array(
				array(
					'key'     => 'post_id',
					'value'   => get_the_ID(),
					'compare' => '=',
				),
			),
		);
		
		$the_query = get_posts( $args );
		
		
		if ( is_array( $the_query ) && sizeof( $the_query ) > 0 ) {
			?>
            <div class="services-history">
				
				<?php
				foreach ( $the_query as $history ) {
					
					$updated_time = get_post_meta( $history->ID, "updated_time", true );
					$updated_date = get_post_meta( $history->ID, "updated_date", true );
					$user_id      = get_post_meta( $history->ID, "user", true );
					$new_status   = get_post_meta( $history->ID, "post_status", true );
					
					$user_name = get_current_user_id() ? new WP_User( $user_id ) :
						wp_get_current_user();
					
					if ( ! empty( $user_name->first_name ) ) {
						if ( ! empty( $user_name->last_name ) ) {
							$person_name = ucfirst( $user_name->first_name . ' ' . $user_name->last_name );
						}
					}
					
					?>
                    <style>
                        .status-color {
                            font-weight: bold;
                            font-size: 15px;
                        }
                    </style>
                    <p>
						<?php _e( 'Work Status Set <span class="status-color">' . $new_status . ' </span> By ' . $person_name . ' at ' . $updated_time . ' in ' . $updated_date ); ?>
                    </p>
					<?php
				} ?>
            </div>
			<?php
		} else {
			_e( "No History Found" );
		}
	}//end method display_status
	
	/**
	 * Save form builder data
	 */
	public function save_settings_meta( $post_id, $post ) {
		
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return $post_id;
		}
		
		if ( ! current_user_can( "edit_post", $post_id ) ) {
			return $post_id;
		}
		
		global $csms_functions;
		
		$form_settings = $csms_functions->csms_get_option( 'csms_settings_manager',
			'add_field_in_meta_info',
			array() );
		
		
		foreach ( $form_settings as $form_data ) {
			
			$field_id = isset( $form_data['field_id'] ) ? $form_data['field_id'] : "";
			$data     = isset( $_POST[ $field_id ] ) ? $_POST[ $field_id ] : "";
			
			update_post_meta( $post_id, $field_id, $data );
		}
		
	}//end method save_settings_meta
	
	/**
	 * Display user form in meta box which is coming from settings page.
	 */
	public function display_form() {
		
		global $csms_functions;
		
		$form_settings = $csms_functions->csms_get_option( 'csms_settings_manager',
			'add_field_in_meta_info',
			array() );
		
		?>

        <div class="csms_form_builder_container">
            <form action="" method="post">
				<?php
				foreach ( $form_settings as $form ) {
					$this->create_field( $form['field_type'],
						$form['field_label'],
						$form['field_id'],
						$form['field_values'] );
				}
				?>
            </form>
        </div>
		<?php
	}//end method add_field_in_meta
	
	
	/**
	 * Create form builder html field
	 *
	 * @param $field_label
	 * @param $field_id
	 * @param $values
	 */
	public function create_field( $field_type, $field_label, $field_id, $field_values ) {
		
		$metaval = get_post_meta( get_the_ID(), $field_id, true );
		
		// text, number, email field
		if ( 'text' == $field_type || 'number' == $field_type || 'email' == $field_type ) {
			
			?>
            <div class="row">
                <div class="col-25">
                    <label for="fname"><?php echo $field_label; ?></label>
                </div>
                <div class="col-75">
                    <input type="<?php echo $field_type; ?>" id="<?php echo $field_id; ?>"
                           name="<?php echo $field_id; ?>"
                           placeholder="Your name.." value="<?php echo $metaval; ?>">
                </div>
            </div>
			<?php
		}
		
		// text, number, email field
		if ( $field_type == 'textarea' ) {
			
			?>
            <div class="row">
                <div class="col-25">
                    <label for="subject"><?php echo $field_label; ?></label>
                </div>
                <div class="col-75">
                    <textarea id="subject" name="<?php echo $field_id; ?>" placeholder="Write something.."
                              style="height:200px"><?php echo $metaval; ?></textarea>
                </div>
            </div>
			<?php
		}
		
		//checkebox and radio field
		if ( $field_type == 'checkbox' || $field_type == 'radio' ) {
			
			$values = explode( ',', $field_values );
			
			?>

            <div class="row">
                <div class="col-25">
                    <label for="fname"><?php echo $field_label; ?></label>
                </div>
                <div class="col-75">
					
					<?php
					
					foreach ( $values as $_values ) {
						
						if ( ! is_array( $metaval ) ) {
							$metaval = array();
						}
						
						$checked = "";
						if ( in_array( $_values, $metaval ) ) {
							$checked = "checked";
						}
						
						?>
                        <label for="">
                            <input type="<?php echo $field_type; ?>" id="<?php echo $field_id; ?>"
                                   name="<?php echo $field_id; ?>[]" placeholder="Your name.."
                                   value="<?php echo $_values; ?>" <?php echo $checked; ?>>
							<?php echo $_values; ?>
                        </label>
						<?php
					}
					?>
                </div>
            </div>
			<?php
		}//end checked and radio field
		
		// select field
		if ( 'select' == $field_type ) {
			$option_values = explode( ',', $field_values );
			?>

            <div class="row">
                <div class="col-25">
                    <label for="country"><?php echo $field_label; ?></label>
                </div>
                <div class="col-75">
                    <select id="<?php echo $field_id; ?>" name="<?php echo $field_id; ?>"
                            class="select_field_form_builder">
						
						<?php
						
						foreach ( $option_values as $option_value ) {
							
							$selected = "";
							
							if ( $option_value == $metaval ) {
								$selected = "selected";
							}
							
							?>
                            <option value="<?php echo $option_value; ?>" <?php echo $selected; ?>>
								<?php echo ucfirst( $option_value ); ?>
                            </option>
						<?php } ?>

                    </select>
                </div>
            </div>
			
			<?php
		}//end select field
		
		
	}//end method create_field
	
	
	/**
	 * Sorting delivary based services in all services page.
	 */
	public function delivery_based_services() {
		
		echo Customer_Service_Management_Helper::priority_color_details_for_admin();
		
		?>

        <div class="grid-container">
			<?php
			
			$posts = query_posts( array(
				'post_type'      => 'service_management',
				'meta_key'       => 'delivery_date',
				'meta_compare'   => '>=',
				'post_status'    => 'publish',
				'posts_per_page' => '99',
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
			) );
			
			echo Customer_Service_Management_Helper::services( $posts );
			
			?>
        </div>
		
		<?php
		
		exit();
		
	}//end method priority_based_services
	
	/**
	 * Shorting priority based services in all services page.
	 */
	public function priority_based_services() {
		
		?>
		
		<?php echo Customer_Service_Management_Helper::priority_color_details_for_admin(); ?>

        <div class="grid-container">
			<?php
			
			$posts = query_posts( array(
				'post_type'      => 'service_management',
				'meta_key'       => 'priority',
				'meta_compare'   => '>=',
				'post_status'    => 'publish',
				'posts_per_page' => '99',
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
			) );
			
			echo Customer_Service_Management_Helper::services( $posts );
			
			?>

        </div>
		
		<?php
		
		exit();
		
	}//end method priority_based_services
	
	/**
	 * Add submenu page
	 *
	 * This menu is showing all services for admin.
	 */
	public function add_submenu_page() {
		
		add_submenu_page(
			'edit.php?post_type=service_management',
			'Products',
			'All Services',
			'manage_options',
			'services',
			array( $this, 'services' )
		);
		
	}//end method add_submenu_page
	
	/**
	 * All services including priority and delivary based services.
	 */
	public function services() {
		
		if ( isset( $_REQUEST['csms_update_service_status'] ) ) {
			
			$service_id     = $_POST['service_id'];
			$service_status = $_POST['status_from_all_services'];
			update_post_meta( $service_id, 'status', $service_status );
			
			$this->sending_email_to_customer( $service_id, "" );
			
		}
		
		?>
        <h2>All posts</h2>

        <a href="edit.php?post_type=service_management&page=services">
            <button class="button button-primary all_services">All Services</button>
        </a>

        <a href="edit.php?post_type=service_management&page=products&sorting=delivery">
            <button class="button button-primary delivery">Delivery based</button>
        </a>

        <a href="edit.php?post_type=service_management&page=products&sorting=priority">
            <button class="button button-primary priority">Priority based</button>
        </a>

        <div class="services_by_filter"></div>

        <div class="all_services_by_default">
			
			
			<?php echo Customer_Service_Management_Helper::priority_color_details_for_admin(); ?>


            <div class="grid-container">
				
				<?php
				
				$posts = query_posts( array(
					'post_type'      => 'service_management',
					'post_status'    => 'publish',
					'posts_per_page' => '99',
					'order'          => 'ASC',
				) );
				
				echo Customer_Service_Management_Helper::services( $posts );
				
				?>

            </div>
        </div>
		
		<?php
		
		
	}//end method products
	
	
	/**
	 *  Generate PDF
	 */
	public function csms_generate_pdf() {
		
		if ( empty( $_GET['action'] ) || ! check_admin_referer( $_GET['action'] ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woo-invoice' ) );
		}
		
		$order_id      = isset( $_GET['order_id'] ) ? sanitize_text_field( $_GET['order_id'] ) : '';
		$document_type = isset( $_GET['document_type'] ) ? sanitize_text_field( $_GET['document_type'] ) : '';
		
		echo CSMS_PDF::generate_pdf( $order_id, $document_type, true, false );
		
		exit;
	}//end method tour_generate_pdf
	
	
	/**
	 * Add dashboard widget for engineer
	 */
	public function add_dashboard_widget_for_engineer() {
		
		global $csms_functions;
		
		// get engineer user role
		$roles = $csms_functions->csms_get_option( 'csms_settings_manager',
			'user_roles_field' );
		
		if ( ! is_array( $roles ) ) {
			$roles = array();
		}
		
		$eng_role = implode( ' ', $roles );
		
		// get current user role
		$user               = wp_get_current_user();
		$current_user_roles = ( array ) $user->roles;
		
		$current_user_role = implode( ' ', $current_user_roles );
		
		
		if ( $current_user_role == $eng_role ) {
			wp_add_dashboard_widget(
				'dashboard',
				'Engineer dashboard',
				array( $this, 'add_engineer_assign_work' )
			);
		}
		
	}//end method add_dashboard_for_engineer
	
	/**
	 * This method is display engineer assign work in engineer panel dashboard
	 */
	public function add_engineer_assign_work() {
		
		$query_args = array(
			'post_type'  => 'service_management',
			'meta_query' => array(
				array(
					'key'     => 'assign_user_role',
					'value'   => get_current_user_id(),
					'compare' => "=",
				),
			),
		);
		
		$query = new WP_Query( $query_args );
		
		if ( is_object( $query ) ) {
			foreach ( $query->posts as $posts ) {
				
				$priority  = get_post_meta( $posts->ID, 'priority', true );
				$term      = get_term_by( 'term_id', $priority, 'priority' );
				$term_name = isset( $term->name ) ? $term->name : "";
				
				?>
                <section class="engineer_dashboard">

                    <ul>
                        <li><b>Product Name : </b> <?php echo $posts->post_title; ?></li>
                        <li><b>Number Of Product: </b> <?php echo get_post_meta( $posts->ID,
								'number_of_product',
								true ); ?></li>

                        <li><b>Problem Details: </b> <?php echo $posts->post_content; ?></li>

                        <li><b>Priority: <?php echo ucfirst( $term_name ); ?> </b></li>

                    </ul>

                </section>
				
				<?php
			}
		} else {
			_e( 'No assign work is found', '' );
		}
	}//end method add_engineer_assign_work
	
	/**
	 * Add custom column data
	 *
	 * @param $column
	 * @param $post_id
	 */
	public function add_data_into_custom_column( $column, $post_id ) {
		
		$download_url = CSMS_PDF::get_invoice_ajax_url( array( 'order_id' => $post_id ) );
		
		$status      = get_post_meta( $post_id, 'status', true );
		$status_term = get_term_by( 'term_id', $status, 'status' );
		
		
		$payment_status      = get_post_meta( $post_id, 'payment_status', true );
		$payment_status_term = get_term_by( 'term_id', $payment_status, 'payment_status' );
		
		$user_ID   = get_post_meta( $post_id, 'assign_user_role', true );
		$user      = get_user_by( 'id', $user_ID );
		$user_name = is_object( $user ) ? $user->first_name . " " . $user->last_name : "";
		
		
		switch ( $column ) {
			
			case 'received_by' :
				
				_e( get_post_meta( $post_id, 'received_by', true ) );
				
				break;
			
			case 'assign_to' :
				
				echo $user_name;
				
				break;
			
			case 'received_date' :
				
				_e( get_post_meta( $post_id, 'received_date', true ) );
				
				break;
			
			case 'delivery_date' :
				
				_e( get_post_meta( $post_id, 'delivery_date', true ) );
				
				break;
			
			case 'work_status' :
				
				if ( ! empty( $status_term ) ) {
					
					$bgcolor = get_term_meta( $status_term->term_id, 'colorpicker_field_status', true );
					?>
                    <style>
                        .work-status.status-<?php echo $status_term->name; ?> {
                            background: <?php echo $bgcolor;?>;
                            color: #fff;
                            font-weight: bold;
                        }
                    </style>
					<?php
					
					echo '<mark class="work-status status-' . $status_term->name . '"><span>' . $status_term->name . '</mark>';
					
					
					break;
				}
			
			case 'payment_status' :
				
				$bgcolor = get_term_meta( $payment_status_term->term_id, 'colorpicker_field_payment', true );
				
				?>
                <style>
                    .order-status.status-<?php echo $payment_status_term->name;?> {
                        background: <?php echo $bgcolor;?>;
                        color: #fff;
                        font-weight: bold;
                    }
                </style>
				
				<?php
				
				echo '<mark class="order-status status-' . $payment_status_term->name . '"><span>' . $payment_status_term->name . '</mark>';
				
				break;
			
			case 'download_ticket' :
				
				printf( '<a style="display: block;margin: 0 auto;background: #666;width: 100px;text-align: center;color: #fff;padding: 5px;border-radius: 5px;" href="%s">%s</a>',
					$download_url,
					__( '<span class="dashicons dashicons-tickets-alt"></span> ' . esc_html__( 'Download',
							'' ) . '' ) );
				break;
			
			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}
	
	/**
	 * Add column in admin area for more details
	 */
	public function add_custom_column( $columns ) {
		
		$columns = array(
			
			'cb'              => '&lt;<input type="checkbox" />',
			'title'           => __( 'Service Name' ),
			'received_by'     => __( 'Received By' ),
			'assign_to'       => __( 'Assign To' ),
			'received_date'   => __( 'Received Date' ),
			'delivery_date'   => __( 'Delivery Date' ),
			'work_status'     => __( 'Work Status' ),
			'payment_status'  => __( 'Payment Status' ),
			'download_ticket' => __( 'Download Ticket' ),
		
		);
		
		return $columns;
		
	}//end method add_custom_column
	
	
	/**
	 * change the custom post title placeholder
	 *
	 * @param $title
	 * @param $post
	 *
	 * @return string
	 */
	public function custom_post_title_change( $title, $post ) {
		if ( $post->post_type == 'service_management' ) {
			$title = "product name write here";
			
			return $title;
		}
		
		return $title;
		
	}//end method custom_post_title_change
	
	
	/**
	 *  enqueue styles
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'mage-jquery-ui-style', CSMS_PLUGIN_URL . 'admin/css/jquery-ui.css', array() );
		wp_enqueue_style( 'pickplugins-options-framework',
			CSMS_PLUGIN_URL . 'admin/assets/css/pickplugins-options-framework.css' );
		wp_enqueue_style( 'jquery-ui', CSMS_PLUGIN_URL . 'admin/assets/css/jquery-ui.css' );
		wp_enqueue_style( 'select2.min', CSMS_PLUGIN_URL . 'admin/assets/css/select2.min.css' );
		wp_enqueue_style( 'codemirror', CSMS_PLUGIN_URL . 'admin/assets/css/codemirror.css' );
		wp_enqueue_style( 'fontawesome', CSMS_PLUGIN_URL . 'admin/assets/css/fontawesome.min.css' );
		wp_enqueue_style( 'mage-admin-css',
			CSMS_PLUGIN_URL . 'admin/css/mage-plugin-admin.css',
			array(),
			time(),
			'all' );
	}//end method enqueue_styles
	
	
	/**
	 *  enqueue scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'magepeople-options-framework',
			plugins_url( 'assets/js/pickplugins-options-framework.js', __FILE__ ),
			array( 'jquery' ) );
		wp_localize_script( 'PickpluginsOptionsFramework',
			'PickpluginsOptionsFramework_ajax',
			array( 'PickpluginsOptionsFramework_ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'select2.min',
			plugins_url( 'assets/js/select2.min.js', __FILE__ ),
			array( 'jquery' ) );
		wp_enqueue_script( 'codemirror',
			CSMS_PLUGIN_URL . 'admin/assets/js/codemirror.min.js',
			array( 'jquery' ),
			null,
			false );
		wp_enqueue_script( 'form-field-dependency',
			plugins_url( 'assets/js/form-field-dependency.js', __FILE__ ),
			array( 'jquery' ),
			null,
			false );
		wp_enqueue_script( 'mage-plugin-js',
			CSMS_PLUGIN_URL . 'admin/js/mage-plugin-admin.js',
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-datepicker',
			),
			time(),
			true );
		
		
		$localzed_value = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		);
		
		wp_localize_script( 'mage-plugin-js', 'csms', $localzed_value );
		
	}
	
	/**
	 * Load all dependency file in admin area
	 */
	private function load_admin_dependencies() {
		require_once CSMS_PLUGIN_DIR . 'admin/class/class-create-cpt.php';
		require_once CSMS_PLUGIN_DIR . 'admin/class/class-create-tax.php';
		require_once CSMS_PLUGIN_DIR . 'admin/class/class-meta-box.php';
		require_once CSMS_PLUGIN_DIR . 'admin/class/class-tax-meta.php';
		require_once CSMS_PLUGIN_DIR . 'admin/class/class-setting-page.php';
		require_once CSMS_PLUGIN_DIR . 'admin/class/class-report.php';
		require_once CSMS_PLUGIN_DIR . 'admin/class/class-export-report.php';
		require_once CSMS_PLUGIN_DIR . 'lib/classes/class-pdf.php';
	}//end method load_admin_dependencies
	
	
}//end class CSMS_Plugin_Admin

new CSMS_Plugin_Admin();

