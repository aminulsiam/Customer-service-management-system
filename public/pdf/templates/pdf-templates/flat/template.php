<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access
global $wtbm_plugin_public, $csms_functions, $wtbm_plugin_admin;

if ( $email_type == 'order-email' ) {
	$args = array(
		'post_type'      => array( 'service_management' ),
		'posts_per_page' => - 1,
		'meta_query'     => array(
			array(
				'key'     => 'wtbm_order_id',
				'value'   => $order_id,
				'compare' => '=',
			),
		),
	);
	
	
} else {
	
	$args = array(
		'post_type'      => array( 'service_management' ),
		'posts_per_page' => - 1,
		'post__in'       => array( $order_id ),
	);
	
	
}
$loop = new WP_Query( $args );

$logo = $csms_functions->csms_get_option( 'csms_settings_manager', 'pdf_logo', '' );

$bg = $csms_functions->csms_get_option( 'csms_settings_manager', 'pdf_bacckground_image', '' );

$bg_color   = $csms_functions->csms_get_option( 'csms_settings_manager', 'pdf_backgroud_color', '#fff' );
$text_color = $csms_functions->csms_get_option( 'csms_settings_manager', 'pdf_text_color', '#000' );
$address    = $csms_functions->csms_get_option( 'csms_settings_manager', 'pdf_company_address', '' );

$name = $csms_functions->csms_get_option( 'csms_settings_manager', 'pdf_company_name', '' );

$phone    = $csms_functions->csms_get_option( '', 'pdf_company_phone', '' );
$email    = $csms_functions->csms_get_option( 'csms_settings_manager', 'pdf_company_email', '' );
$tc_title = $csms_functions->csms_get_option( 'csms_settings_manager', 'pdf_terms_title', '' );
$tc_text  = $csms_functions->csms_get_option( 'csms_settings_manager', 'pdf_terms_text', '' );

?>
<style>
    .pdf-ticket-body {
        padding: 20px;
        margin: 0 auto;
        overflow: hidden;
        display: block;
        color: #000;
    }

    .alter_logo {
        width: 50px;
        height: 50px;
        background: gray;
        border-radius: 50%;
        padding: 0 0 0 50px;
    }

    .pdf_ticket_header {
        width: 100%;
        height: 22%;
    <?php
		if(!empty($bg)){
		    
			?> background: url(<?php echo wp_get_attachment_url( $bg ); ?>);
    <?php }else{
		  ?> background-color: <?php echo $bg_color;?>;
    <?php
} ?> border-top: 7px solid orange;
    }

    .header_left img {
        width: 50%;
        height: 80px;
        border-radius: 50%;
    }

    .pdf_ticket_header .header_right {
        width: 50%;
        float: right;
        padding: 0 20px 150px 50px;
    }

    .pdf_ticket_header p {
        padding: 0;
        margin: 0 0 5px 0;
    }

    h6.pdf_ticket_title {
        font-size: 20px;
        display: block;
        margin: 0 0 5px 0;
    }

    table.table-ticket {
        width: 100%;
        margin-bottom: 20px;
        border: 0px solid #e9e9e9;
        border-collapse: collapse;
        border-spacing: 0;
    }

    tbody {
        display: table-row-group;
        vertical-align: middle;
        border-color: inherit;
    }

    tr {
        display: table-row;
        vertical-align: inherit;
        border-color: inherit;
    }

    table.table-ticket tr td {
        border: 0px solid #e9e9e9;
        line-height: 1.42857;
        padding: 5px;
        vertical-align: middle;
    }

    .pdf_footer {
        text-align: center;
    }

    .pdf_footer p {
        margin-left: 70%;
    }

    .pdf_footer h3 {
        font-size: 16px;
        font-weight: bold;
        margin: 0 0 5px 0;
    }

    .pdf_footer {
        font-size: 18px;
    }

    .pdf_ticket_info ul {
        display: block;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .pdf_ticket_info ul li {
        display: block;
        /*border-bottom: 1px dashed #ddd;*/
        padding: 3px 0;
    }

    .pdf-ticket-content {
        margin-top: 10px;
        margin-bottom: 20px;
    }

    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    th {
        background: orange;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    table.table-ticket {
        padding: 0 10px;

    <?php if($text_color) { ?> color: <?php echo $text_color; ?>;
    <?php }else{ ?> color: #000;
    <?php } ?> position: relative;
    }
</style>


<?php
while ( $loop->have_posts() ) {
	$loop->the_post();
	
	$id = get_the_ID();
	
	$priority = get_post_meta( get_the_ID(), 'priority', true );
	
	
	$args              = array(
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
	$priority_term_arr = get_terms( $args );
	
	$payment_status      = get_post_meta( get_the_ID(), 'payment_status', true );
	$payment_status_term = get_term_by( 'term_id', $payment_status, 'payment_status' );
	
	//$reg_form_arr = unserialize( get_post_meta( $tour_id, 'attendee_reg_form', true ) );
	
	
	?>
    <div class='pdf-ticket-body'>
        <div class='pdf_ticket_header'>

            <div class="" style="width: 50%;float: left;margin-top: 40px;margin-left: 15px">
				<?php if ( ! empty( $logo ) ) {
					printf( '<img src="%s" align="left" style="max-height: 45px"/>', wp_get_attachment_url( $logo ) );
				}
				?>

                <p style="margin-top: 50px;">
                    <b><?php echo $name; ?></b><br>
					<?php echo '<span>' . $address . '</span>'; ?>
                </p>

            </div>

            <div style="width: 50%;float: right;text-align: right;margin-top: 60px;margin-right: 15px">
                Invoice Number: <span><?php echo get_the_ID(); ?></span><br>
                Date : <span><?php echo get_post_meta( $id, 'received_date', true ); ?></span>
            </div>


        </div>

        <table class='table-ticket'>

            <tr>
                <td width=40%>
                    <div class='pdf_ticket_info'>

                        <h4>Bill To</h4>

                        <ul>
                            <li> Contact Name : <?php echo get_post_meta( $id,
									'customer_name',
									true ); ?>
                            </li>

                            <li> Address : <?php echo get_post_meta( $id,
									'customer_address',
									true ); ?>
                            </li>

                            <li> Phone : <?php echo get_post_meta( $id, 'mobile_number', true ); ?>
                            </li>

                            <li> Email : <?php echo get_post_meta( $id, 'email', true ); ?></li>

                        </ul>
                    </div>
                </td>

                <td width=40%>
                    <div class='pdf_ticket_info' style="margin-left: 50px">
                        <h4>Received By</h4>
                        <ul>
                            <li>
								<?php echo get_post_meta( get_the_ID(), 'received_by', true );
								?>
                            </li>
                            <li> Mobile Number : <?php echo get_post_meta( $id,
									'mobile_number',
									true ); ?>
                            </li>

                            <li>Mail : <?php echo get_post_meta( $id,
									'email',
									true ); ?>
                            </li>

                            <li> Address : <?php echo get_post_meta( $id,
									'customer_address',
									true ); ?>
                            </li>
                        </ul>

                    </div>
                </td>

            </tr>
        </table>

        <h1>Services</h1>
        <table>
            <tr>
                <th>Services</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Payment Status</th>
                <th>Total Price</th>
            </tr>
			
			<?php
			$get_services = maybe_unserialize( get_post_meta( get_the_ID(),
				'service_pricing',
				true
			) );
			
			$total_price = 0;
			
			if ( is_array( $get_services ) && sizeof( $get_services ) > 1 ) {
				foreach ( $get_services as $key => $service ) {
					if ( $key != 'service_price_last_count' ) {
						?>
                        <tr>
                            <td>
                                <b><?php echo $service['service_title']; ?></b>
                            </td>
                            <td><?php echo $service['qty']; ?></td>
                            <td><?php echo $total_price += $service['service_price']; ?></td>
                            <td><?php echo $payment_status_term->name; ?></td>
                            <td><?php echo $service['service_price']; ?></td>
                        </tr>
					<?php }
				}
			} else { ?>
                <tr>
                    <td colspan="5"><?php echo "No Services Selected"; ?></td>
                </tr>
				<?php
			} ?>
        </table>

        <h1>Products</h1>
        <table>
            <tr>
                <th>Products</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Payment Status</th>
                <th>Total Price</th>
            </tr>
			
			<?php
			$get_products = maybe_unserialize( get_post_meta( get_the_ID(),
				'product_pricing',
				true
			) );
			
			if ( is_array( $get_products ) && sizeof( $get_products ) > 0 ) {
				foreach ( $get_products as $key => $product ) {
					if ( $key != 'product_price_last_count' ) {
						?>
                        <tr>
                            <td>
                                <b><?php echo $product['product_title']; ?></b>
                            </td>
                            <td><?php echo $product['qty']; ?></td>
                            <td><?php echo $total_price += $product['product_price']; ?></td>
                            <td><?php echo $payment_status_term->name; ?></td>
                            <td><?php echo $product['product_price']; ?></td>
                        </tr>
					<?php }
				}
			} else {
				?>
                <tr>
                    <td colspan="5"><?php echo "No Products Selected"; ?></td>
                </tr>
			<?php } ?>
        </table>


        <div class='pdf_footer'>

            <!--            <p>Subtotal - -->
			<?php //echo get_post_meta( get_the_ID(), 'service_charge', true ); ?><!--</p>-->
            <!--<p>Discount - </p>
            <p>Tax -</p>
            <p>Shipping - </p>-->
            <p>Total Price : <?php echo $total_price; ?></p>

        </div>

        <h1 style=" border: 7px solid orange"></h1>

    </div>
	
	<?php
}
?>
