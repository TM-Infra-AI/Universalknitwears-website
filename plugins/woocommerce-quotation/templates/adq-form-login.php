<?php
/**
 * Checkout login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_user_logged_in() ) return;
?>

<div class="woocommerce">
    
<?php
$info_message  =  __( 'Returning customer?', 'woocommerce-quotation' );
$info_message .= ' <a href="#" class="showlogin">' . __( 'Click here to login', 'woocommerce-quotation' ) . '</a>';
wc_print_notice( $info_message, 'notice' );
?>

<?php
	woocommerce_login_form(
		array(
			'message'  => __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer please proceed to the Register section.', 'woocommerce-quotation' ),
			'redirect' => get_permalink( $redirect_id ),
			'hidden'   => true
		)
	);
?>
    
</div>