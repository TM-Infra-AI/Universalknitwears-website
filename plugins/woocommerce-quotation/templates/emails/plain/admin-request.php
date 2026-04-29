<?php
/**
 * Admin new quote request email (plain text)
 *
 * @author 		Adquote
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo sprintf( __( 'You have received a quote request from %s. Their request is as follows:', 'woocommerce-quotation' ), $order->billing_first_name . ' ' . $order->billing_last_name ) . "\n\n";

if ( $order->get_user() ) :

        echo sprintf( __( 'You can access your account area to view your orders here: %s.', 'woocommerce-quotation' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ) . "\n\n"; 

endif;

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

echo sprintf( __( 'Order number: %s', 'woocommerce'), $order->get_order_number() ) . "\n";
echo sprintf( __( 'Quote request link: %s', 'woocommerce-quotation'), admin_url( 'post.php?post=' . $order->id . '&action=edit' ) ) . "\n";
echo sprintf( __( 'Quote request date: %s', 'woocommerce-quotation'), date_i18n( __( 'jS F Y', 'woocommerce' ), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

echo "\n" . $order->email_order_items_table( array( 	'show_sku'    => false, 	'show_image'  => false, 	'$image_size' => array( 32, 32 ), 	'plain_text'  => true ) );

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t ";

                if( ( StaticAdqQuoteRequest::can_show_price() && StaticAdqQuoteRequest::can_show_product_price( $order ) ) 
                    || ( $total['value'] == "" ) ) { 
                        echo $total['value'];
                }
                else {
                        echo __( 'Not yet proposed', 'woocommerce-quotation' );
                }

                echo "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );

echo __( 'Customer details', 'woocommerce' ) . "\n";

if ( $order->billing_email )
	echo __( 'Email:', 'woocommerce' ); echo $order->billing_email . "\n";

if ( $order->billing_phone )
	echo __( 'Tel:', 'woocommerce' ); ?> <?php echo $order->billing_phone . "\n";

wc_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );