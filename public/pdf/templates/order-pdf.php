<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

global $wtbm;

$order_id = isset( $atts['order_id'] ) ? $atts['order_id'] : '';
$type     = isset( $atts['type'] ) ? $atts['type'] : '';
$template = isset( $atts['template'] ) ? $atts['template'] : $wtbm->get_option( 'magepdf_invoice_template', 'flat' );

$email_type = isset( $atts['email_type'] ) ? $atts['email_type'] : '';
$oid        = isset( $atts['oid'] ) ? $atts['oid'] : '';


if ( empty( $order_id ) || $order_id == 0 ) {
	$wtbm->print_error( new WP_Error( 'invalid_data', __( 'Order ID missing!' ) ) );
	
	return;
}

$all_templates = CSMS_PDF::get_templates();

$template_dir = isset( $all_templates[ $template ]['template'] ) ? $all_templates[ $template ]['template'] : '';
$template_dir = apply_filters( 'magepdf_filter_pdf_template_dir', $template_dir, $order_id );
$template_dir = file_exists( $template_dir ) ? $template_dir : '';

$stylesheet_dir = isset( $all_templates[ $template ]['stylesheet'] ) ? $all_templates[ $template ]['stylesheet'] : '';
$stylesheet_dir = apply_filters( 'magepdf_filter_pdf_stylesheet_dir', $stylesheet_dir, $order_id );

if ( empty( $template_dir ) || empty( $stylesheet_dir ) ) {
	
	$wtbm->print_error( new WP_Error( 'invalid_data', sprintf( __( 'Template file missing : <b>%s</b>' ), ucwords( $template ) ) ) );
	
	return;
}

// printf( '<link rel="stylesheet" href="%s">', $stylesheet_dir );
include( $template_dir );