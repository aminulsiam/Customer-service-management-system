<?php
/*
* @Author 		MagePeople
* Copyright: 	MagePeople
*/

use Dompdf\Dompdf;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access
if ( ! class_exists( 'CSMS_PDF' ) ) {
	class CSMS_PDF {
		
		public $settings = array();
		
		public function __construct() {
			
			$this->set_shortcodes();
			$this->add_shortcodes();
			
		}
		
		public static function get_templates() {
			
			$templates     = array();
			$arr_templates = apply_filters( 'mage_pdf_templates_internal', array(
				'flat' => __( 'Flat' ),
			) );
			
			foreach ( $arr_templates as $template_name => $label ) {
				
				$templates[ $template_name ] = array(
					'label'      => $label,
					'thumb'      => sprintf( '%1$spublic/pdf/templates/pdf-templates/%2$s/%2$s.png', CSMS_PLUGIN_DIR, $template_name ),
					'template'   => sprintf( '%1$spublic/pdf/templates/pdf-templates/%2$s/template.php', CSMS_PLUGIN_DIR, $template_name ),
					'stylesheet' => sprintf( '%1$spublic/pdf/templates/pdf-templates/%2$s/style.css', CSMS_PLUGIN_DIR, $template_name ),
				);
			}
			
			return apply_filters( 'mage_pdf_templates', $templates );
		}
		
		
		public static function generate_pdf( $order_id = 0, $html = "", $download_pdf = true, $save_pdf = false, $email_type = null, $oid = null ) {
			
			
			if ( $order_id == 0 ) {
				return new WP_Error( 'invalid_data', __( 'Invalid order id provided' ) );
			}
			
			require CSMS_PLUGIN_DIR . 'lib/dompdf/autoload.inc.php';
			
			
			$html = empty( $html ) ? CSMS_PDF::get_order_pdf_html( $order_id, 'pdf', $email_type, $oid ) : $html;
			
			$dompdf = new Dompdf( array(
				'isRemoteEnabled' => true,
			) );
			
			$dompdf->setPaper( 'A4', 'portrait' );
			$dompdf->set_option( 'isHtml5ParserEnabled', true );
			$dompdf->loadHtml( $html, 'UTF-8' );
			$dompdf->render();
			
			$output = $dompdf->output();
			
			if ( $save_pdf ) {
				
				$invoice_file_name = sprintf( '%s/uploads/ticket-%s-%s.pdf', WP_CONTENT_DIR, $order_id, time( "H:s" ) );
				file_put_contents( $invoice_file_name, $output );
				
				return $invoice_file_name;
			}
			
			if ( $download_pdf ) {
				
				$dompdf->stream( sprintf( "%s-%s", __( 'Ticket' ), $order_id ) );
				
				return true;
			}
			
			return $output;
			
		}
		
		
		public static function get_order_pdf_html( $order_id = 0, $type = "", $email_type = null, $oid = null ) {
			
			if ( $order_id == 0 ) {
				return new WP_Error( 'invalid_data', __( 'Invalid order id provided' ) );
			}
			
			$shortcode = sprintf( '[order-pdf order_id="%s" type="%s" template="flat" email_type="%s" oid="%s"]',
				$order_id, $type, $email_type, $oid
			);
			
			ob_start();
			echo do_shortcode( $shortcode );
			
			return ob_get_clean();
		}
		
		
		public static function get_invoice_ajax_url( $args = array() ) {
			
			$default_args = array(
				'action' => 'generate_pdf',
			);
			
			$args      = wp_parse_args( $args, $default_args );
			$build_url = http_build_query( $args );
			$nonce_url = wp_nonce_url( admin_url( "admin-ajax.php?" . $build_url ), $args['action'] );
			
			return apply_filters( 'mage_filters_invoice_ajax_url', $nonce_url );
		}
		
		
		public function get_order_data( $order_id = 0, $return_as = false ) {
			
			if ( $order_id == 0 ) {
				return new WP_Error( 'invalid_data', __( 'Invalid order id provided' ) );
			}
			
			$data  = array();
			$order = wc_get_order( $order_id );
			$data  = $order->get_data();
			
			$item_total = 0;
			foreach ( $order->get_items() as $item_id => $item ) {
				
				$total    = isset( $item['total'] ) ? (float) $item['total'] : 0;
				$subtotal = isset( $item['subtotal'] ) ? (float) $item['subtotal'] : 0;
				
				$data['items'][] = array_merge( $item->get_data(), array(
					'thumbnail_url' => get_the_post_thumbnail_url( $item->get_product_id(), array( 50, 50 ) ),
					'permalink'     => get_the_permalink( $item->get_product_id() ),
					'discount'      => $subtotal > $total ? $subtotal - $total : 0,
				) );
				
				$item_total += $subtotal;
			}
			
			$data['order_date'] = $order->get_date_created()->date( 'M j, Y' );
			$data['item_total'] = $item_total;
			$data['order']      = $order;
			
			if ( $return_as && $return_as == 'object' ) {
				
				$data['billing']  = isset( $data['billing'] ) ? (object) $data['billing'] : (object) array();
				$data['shipping'] = isset( $data['shipping'] ) ? (object) $data['shipping'] : (object) array();
				
				return (object) $data;
			}
			if ( $return_as && $return_as == 'json' ) {
				return json_encode( $data );
			}
			
			return $data;
		}
		
		
		public function print_error( $wp_error ) {
			
			$classes = array( $wp_error->get_error_code() );
			
			if ( is_admin() ) {
				$classes[] = 'is-dismissible';
			}
			
			printf( "<div class='notice notice-error error wooin-notice %s'><p>%s</p></div>",
				implode( ' ', $classes ), $wp_error->get_error_message()
			);
		}
		
		
		function get_option( $option_name = '', $default = '' ) {
			
			$option_value = get_option( $option_name, $default );
			$option_value = empty( $option_value ) ? $default : $option_value;
			
			return $option_value;
		}
		
		private function set_shortcodes() {
			
			$this->shortcodes = apply_filters( 'mage_filter_shortcodes', array(
				'order-pdf' => array(
					'file-slug' => 'order-pdf',
				),
			) );
		}
		
		private function add_shortcodes() {
			
			foreach ( $this->shortcodes as $shortcode => $args ) :
				add_shortcode( $shortcode, array( $this, 'shortcode_content_display' ), $shortcode );
			endforeach;
		}
		
		public function shortcode_content_display( $atts, $content = null, $shortcode ) {
			
			ob_start();
			
			if ( is_wp_error( $shortcode_html = $this->get_shortcode_html( $shortcode, $atts ) ) ) {
				
				return ob_get_clean();
			}
			
			echo $shortcode_html;
			
			return ob_get_clean();
		}
		
		public function get_shortcode_html( $shortcode, $atts ) {
			
			$file_slug = isset( $this->shortcodes[ $shortcode ]['file-slug'] ) ? $this->shortcodes[ $shortcode ]['file-slug'] : '';
			
			$template_dir = sprintf( "%spublic/pdf/templates/%s.php", CSMS_PLUGIN_DIR, $file_slug );
			$template_dir = apply_filters( 'mage_filter_shortcode_template_dir', $template_dir, $shortcode );
			$template_dir = file_exists( $template_dir ) ? $template_dir : '';
			
			if ( empty( $template_dir ) ) {
				return new WP_Error( 'empty_data', sprintf( __( 'Template file not found for shortcode : [%s]', 'woo-invoice' ), $shortcode ) );
			}
			
			ob_start();
			
			include $template_dir;
			
			return ob_get_clean();
		}
		
		/**
		 * Email sending function
		 *
		 * @param string $order_id
		 * @param bool $order
		 * @param null $email_type
		 * @param null $oid
		 *
		 * @return bool
		 */
		public function send_email( $order_id = '', $order = false, $email_type = null, $oid = null ) {
			global $wtbmfunctions;
			if ( empty( $order_id ) || ! $order ) {
				return false;
			}
			
			$subject            = $wtbmfunctions->wtbm_get_option( 'tour_manager_settings', 'pdf_email_subject', 'PDF Ticket Confirmation' );
			$content            = $wtbmfunctions->wtbm_get_option( 'tour_manager_settings', 'pdf_email_text', 'Here is PDF Ticket Confirmation Attachment' );
			$form_name          = $wtbmfunctions->wtbm_get_option( 'tour_manager_settings', 'pdf_email_form_name', get_bloginfo( 'name' ) );
			$form_email         = $wtbmfunctions->wtbm_get_option( 'tour_manager_settings', 'pdf_email_form_email', get_bloginfo( 'admin_email' ) );
			$admin_notify_email = $wtbmfunctions->wtbm_get_option( 'tour_manager_settings', 'pdf_email_admin_notification_email', get_bloginfo( 'admin_email' ) );
			$email_sent_status  = $wtbmfunctions->wtbm_get_option( 'tour_manager_settings', 'pdf_email_send_on', array() );
			$email_status       = $wtbmfunctions->wtbm_get_option( 'tour_manager_settings', 'email_send_pdf', 'yes' );
			
			
			// if( ! empty( $subject ) && ! empty( $content ) ) {
			
			$attachments = array();
			$headers     = array(
				sprintf( "From: %s <%s>", $form_name, $form_email ),
			);
			if ( $email_status == 'yes' ) {
				
				$attathment_file_url = $this->generate_pdf( $order_id, "", false, true, $email_type, $oid );
				
				
				$attachments[] = $attathment_file_url;
				
				
				// }
				
				$email_address_arr = array(
					$order->get_billing_email(),
					$admin_notify_email
					// $this->settings->mage_email_to
				);
				
				$email_address = implode( ",", $email_address_arr );
				
				wp_mail( $email_address, $subject, $content, $headers, $attachments );
			}
		}
	}
	
	global $pdf_download;
	$pdf_download = new CSMS_PDF();
}