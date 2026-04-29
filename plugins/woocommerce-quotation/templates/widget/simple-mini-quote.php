<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 	WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

	<?php if ( ! WC_Adq()->quote->get_quote() ) : ?>
    
                <?php _e( 'No products in the quote list.', 'woocommerce-quotation' ); ?>
                
    	<?php else : ?>		

		<?php
                        $total = 0;
			foreach ( WC_Adq()->quote->get_quote() as $cart_item_key => $cart_item ) {
				$total+= $cart_item['quantity'];
			} ?>
                
                        <p>
                        <?php
                        printf(
                            '<a href="%s" class="button wc-forward">%s</a>',
                                StaticAdqQuoteRequest::get_quote_list_link(),
                                sprintf(
                                            '%s %s',
                                            $total,
                                            __( 'item in quote list', 'woocommerce-quotation' )       
                                    )
                            ); 
                        ?>
                        </p>	                 
                        
	<?php endif; ?>
