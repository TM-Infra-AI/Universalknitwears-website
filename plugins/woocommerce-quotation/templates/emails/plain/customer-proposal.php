<?php
/**
 * Customer processing order email
 *
 * @author 		Aldaba Digital
 * @package 	Woocommerce Quotation
 * @version     
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo __( "Your Quote Request has been processed and we made this proposal for you. Our proposal are shown below for your reference:", 'woocommerce-quotation' ) . "\n\n";

do_action( 'adq_email_proposal_after_header', $order, $sent_to_admin, $plain_text ); 

if ( $order->get_user() ) :

        echo sprintf( __( 'You can access your account area to view your orders here: %s.', 'woocommerce-quotation' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ) . "\n\n"; 

endif;

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

echo sprintf( __( 'Quote request number: %s', 'woocommerce-quotation'), $order->get_order_number() ) . "\n";
echo sprintf( __( 'Quote request date: %s', 'woocommerce-quotation'), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

echo "\n" . $order->email_order_items_table( array( 	'show_sku'    => false, 	'show_image'  => false, 	'$image_size' => array( 32, 32 ), 	'plain_text'  => true ) );

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );

echo __( 'Your details', 'woocommerce' ) . "\n\n";

if ( $order->billing_email )
	echo __( 'Email:', 'woocommerce' ); echo $order->billing_email . "\n";

if ( $order->billing_phone )
	echo __( 'Tel:', 'woocommerce' ); ?> <?php echo $order->billing_phone . "\n";

wc_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );