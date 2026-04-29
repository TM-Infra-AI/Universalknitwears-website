<?php
/**
 * WooCommerce Booking compatibility
 *
 * @author      Aldaba Digital
 * @category    Admin
 * @package     woocommerce-quotation/modules/
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Booking_Cart_Manager' ) ) {
        return;
}
    
if ( ! class_exists( 'ADQ_BO' ) ) :
/**
 * ADQ_PA
 */
class ADQ_BO extends WC_Booking_Cart_Manager {

    /**
    * Constructor.
    */
    public function __construct() {
        
            add_filter( 'woocommerce_adq_add_to_quote', array( $this, 'adq_add_to_quote' ), 10, 2);             
            
            add_filter( 'woocommerce_add_quote_item', array( $this, 'add_cart_item' ), 10, 1 );
            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );            
            
            add_action('woocommerce_after_add_to_cart_button', array( $this, 'adq_show_booking_price') );
            
            add_action( 'woocommerce_quote_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 9, 3 );
    }
    
    public function adq_add_to_quote( $html, $product ) {
            
            if( $product->product_type == 'booking' ) {
                    return $this->generate_booking_quote_button( $product );
            }
        
            return $html;
    }
    
    public function generate_booking_quote_button ( $product ) {
            if( !StaticAdqQuoteRequest::can_show_quote_button( $product ) ) {
                    return '';
            }
            
            ob_start();             

            ?>

            <div class="clear"></div>
            
            <div class="<?php echo $product->product_type."_add_to_quote button_add_to_quote" ?>">                
                    <button disabled="disabled" type="submit" id="add_to_quote" class="single_add_to_cart_button single_adq_button wc-bookings-booking-form-button button alt" data-product-id="<?php echo $product->id ?>" data-product-type="<?php echo $product->product_type ?>" style="display:none"><?php echo StaticAdqQuoteRequest::get_add_to_quote_text(); ?></button>
            </div>
            
            <div class="clear"></div>

            <?php
            return ob_get_clean();
        
    }
    
    public function adq_show_booking_price() {
            global $product;

            if ( $product->product_type == 'booking' && !StaticAdqQuoteRequest::can_show_price( $product )  ) {  
                    ?>                        
                    <script>                                        
                            jQuery('.wc-bookings-booking-cost').remove();
                    </script>
                    <?php
            }
    }
    
    /**
    * Check for invalid bookings
    */
    public function cart_loaded_from_session() {
        
            foreach( WC_Adq()->quote->get_quote() as $cart_item_key => $cart_item ) {
                    if ( isset( $cart_item['booking'] ) ) {
                            // If the booking is gone, remove from cart!
                            $booking_id = $cart_item['booking']['_booking_id'];
                            $booking    = get_wc_booking( $booking_id );

                            if ( ! $booking || ! $booking->has_status( array( 'was-in-cart', 'in-cart', 'unpaid', 'paid' ) ) ) {
                                    unset( WC_Adq()->quote->quote_contents[ $cart_item_key ] );

                                    WC_Adq()->quote->calculate_totals();

                                    wc_add_notice( sprintf( __( 'A booking for %s has been removed from your cart due to inactivity.', 'woocommerce-bookings' ), '<a href="' . get_permalink( $cart_item['product_id'] ) . '">' . get_the_title( $cart_item['product_id'] ) . '</a>' ), 'notice' );
                            } elseif ( $booking->has_status( 'in-cart' ) ) {
                                    $this->schedule_cart_removal( $cart_item['booking']['_booking_id'] );
                            }
                    }
            }
    }
        
}

endif;

return new ADQ_BO();