<?php
/**
 * Quote Empty List Page
 *
 * @author 	Adquote
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="woocommerce">
    
    <?php wc_print_notices(); ?>

    <p class="cart-empty"><?php _e( 'Your quoted list is currently empty.', 'woocommerce-quotation' ) ?></p>

    <p class="return-to-shop"><a class="button wc-backward" href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>"><?php _e( 'Return To Shop', 'woocommerce-quotation' ) ?></a></p>

</div>