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

if ( ! class_exists( 'TM_Extra_Product_Options' ) ) {
        return;
}

if ( ! class_exists( 'ADQ_TM' ) ) :
/**
 * ADQ_PA
 */
class ADQ_TM  {

        var $cart_edit_key = null;
        /**
        * Constructor.
        */
        public function __construct() {
                         
                $this->cart_edit_key = isset($_REQUEST['tm_cart_item_key'])?$_REQUEST['tm_cart_item_key']: (( isset($_REQUEST['tc_cart_edit_key']) ) ?$_REQUEST['tc_cart_edit_key']:null);
                
                add_action( 'woocommerce_add_to_cart', array( $this,'adq_woocommerce_add_to_cart'), 10, 6);
        }


        public function adq_woocommerce_add_to_cart( $cart_item_key="", $product_id="", $quantity="", $variation_id="", $variation="", $cart_item_data="" ){
                
                if(is_array($cart_item_data) && isset($cart_item_data['tmhasepo'])){

                        $cart_contents = WC_Adq()->quote->quote_contents;

                        if (is_array($cart_contents) && isset($cart_contents[$cart_item_key]) && !isset($cart_contents[$cart_item_key]['tm_cart_item_key'])){
                                WC_Adq()->quote->quote_contents[$cart_item_key]['tm_cart_item_key'] = $cart_item_key;
                        }

                }                
        }    
        
}

endif;

return new ADQ_TM();