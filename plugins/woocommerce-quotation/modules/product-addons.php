<?php
/**
 * WooCommerce Product Addons compatibility
 *
 * @author      Aldaba Digital
 * @category    Admin
 * @package     woocommerce-quotation/modules/
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'ADQ_PA' ) ) :
/**
 * ADQ_PA
 */
class ADQ_PA  {

    /**
    * Constructor.
    */
    public function __construct() {
        
            add_filter( 'get_product_addons', array( $this, 'adq_get_product_addons' ), 10, 1); 
            add_filter( 'woocommerce_adq_cart_item_list', array( $this, 'adq_addons_add_price_to_name' ), 10, 1); 
    }
    
    
    public function adq_get_product_addons ( $addons ) {  
            $_product = false;

            if( is_product() ) {
                    $post = get_post();
                    $_product = wc_get_product( $post->ID );
            }
            
            if( $_product && !StaticAdqQuoteRequest::can_show_price($_product) ) {                        
                    foreach( $addons as $key => $addon ) {  
                            foreach( $addon['options'] as $key2 => $option ) {                            
                                    if(isset($option['price']) &&  (float)$option['price'] > 0) {                                            
                                            $addons[$key]['options'][$key2]["price"] = 0;
                                    }
                            }
                    }
            }
            return $addons;
    }
    
    public function adq_addons_add_price_to_name ( $cart_item ) {                              
            
            if( !StaticAdqQuoteRequest::can_show_price( $cart_item['data'] ) ) {                 
                    foreach( $cart_item as $key => $cart_item_data ) {                              
                            if(is_array($cart_item_data)) {
                                    $cart_item[$key] = $this->adq_recursive_addons_add_price_to_name($cart_item_data);
                            }
                    }
            }
            
            return $cart_item;
    }
    
    public function adq_recursive_addons_add_price_to_name ( $cart_item ) { 
        
            if( !is_array( $cart_item ) ) {
                    return $cart_item;
            }
            
            if (array_key_exists('price', $cart_item)) {
                    $cart_item['price'] = 0;                    
                    return $cart_item;
            }
            
            foreach ($cart_item as $key => $cart_item_data) {
                    if( is_array($cart_item ) ) {
                            $cart_item[$key] = $this->adq_recursive_addons_add_price_to_name($cart_item_data);
                    }
            }
            
            return $cart_item;
    }    
        
}

endif;

return new ADQ_PA();