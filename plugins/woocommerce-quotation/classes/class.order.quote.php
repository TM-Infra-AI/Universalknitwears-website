<?php
/**
 * Handle Order Override functions
 *
 * @class 	WC_Quotation_Order
 * @version     2.0.0
 * @package     woocommerce-quotation/classes/
 * @category    Class
 * @author      Aldaba Digital
 */

if( !class_exists( 'WC_Quotation_Order' ) ) :    

class WC_Quotation_Order {
    
        public $posted;
        
        public $_cart;
        
        public $order_id = false;
        
        protected static $_instance;
        
        public static function instance() {
                if ( is_null( self::$_instance ) ) {
                        self::$_instance = new self();
                }
                return self::$_instance;
        }

        public function procces_quote_list () {
                //Load All Post Variables
                foreach($_POST as $key => $value) {
                        $this->posted[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
                }

                //Create New Customer
                if( isset($this->posted['account_username']) ) {
                        $username = ! empty( $this->posted['account_username'] ) ? $this->posted['account_username'] : '';
                        $password = ! empty( $this->posted['account_password'] ) ? $this->posted['account_password'] : '';
                        $new_customer = wc_create_new_customer( $this->posted['billing_email'], $username, $password );
                }
        }
    
    	/**
	 * Process the create order from Quote Request
	 *
	 * @access public
	 * @return void
	 */
        public function create_order() {
                
                // Prevent timeout
		@set_time_limit(0);          

                if ( sizeof( WC_Adq()->quote->get_quote() ) == 0 ) {
			adq_add_notice( sprintf( __( 'Sorry, you haven\'t any product. <a href="%s" class="wc-backward">Return to homepage</a>', 'woocommerce-quotation' ), home_url() ), 'error' );
                        return;
		}            
                
                $adq_inherit_shipping_conf = get_option("adq_inherit_shipping_conf");
                $adq_enable_shipping = get_option("adq_enable_shipping");
                
                if ( $adq_inherit_shipping_conf == "no" && $adq_enable_shipping == "no" ) {
                       $this->posted['adq_shipping_method'] =  false;
                }
                else {
                       $this->posted['adq_shipping_method'] = isset( $_POST['adq_shipping_method'] ) ? true : false;
                }
                
                if(!$this->posted['adq_shipping_method']) {
                        unset( $_POST['ship_to_different_address'] );
                }
                $_POST['_wpnonce'] = wp_create_nonce('woocommerce-process_checkout');
                
                //Backup Copy Current Cart
                $this->_cart = clone WC()->cart;
                
                //Reset Cart
                WC()->cart->empty_cart(); 
                
                //Add filter to avoid losing WC Cart on error in Quote checkout               
                add_filter('woocommerce_add_error', array ($this, 'notice_error_cart_restore') );
                //Add filter to check if is purchasable
                add_filter('woocommerce_is_purchasable', array ($this, 'is_purchasable'), 10, 2);
                
                //Fill with Quote Request
                foreach ( WC_Adq()->quote->get_quote() as $values ) {
                        $variation_id = ( $values['variation_id'] && $values['variation_id'] != "")?(int)$values['variation_id']:'';
                        $variations = ( $values['variation'] && is_array($values['variation']) )? $values['variation']:'';
                        $cart_item_data = ( isset( $values['cart_item_data'] ) && is_array($values['cart_item_data']) )? $values['cart_item_data']:array();
                        
                        if( isset($values['_product_note'] ) && $values['_product_note'] ) {            
                                $cart_item_data['_product_note'] = $values['_product_note'];                
                        }                        
                        
                        WC()->cart->add_to_cart( (int)$values['product_id'], (int)$values['quantity'], $variation_id, $variations, $cart_item_data );                        
                } 
                
                //Have discounts?
                foreach ( WC_Adq()->quote->get_applied_coupons() as $code ) {
                        WC()->cart->add_discount( $code );             
                }
                
                if( get_option('adq_disable_checkout_hooks') == "yes" ) {
                        remove_all_actions( 'woocommerce_before_checkout_process' );
                        remove_all_actions( 'woocommerce_checkout_process' );
                        remove_all_actions( 'woocommerce_after_checkout_validation' );
                }

                if( get_option('adq_show_terms') == "no" ) {
                        $_POST['terms'] = 1;
                }
                
                //Create Order                
                $checkout = StaticAdqQuoteRequest::get_checkout();
                
                if( !is_user_logged_in() ) {
                        if ( get_option("adq_enable_registration") == "yes" ) {
                                if( get_option("adq_enable_guest_request") == "no" ) {
                                        $_POST['createaccount'] = true;
                                }
                                else {
                                        $checkout->must_create_account = !empty( $_POST['createaccount'] );                                    
                                }
                        }
                        else {
                                $_POST['createaccount'] = false;
                                $checkout->must_create_account = false;
                        }
                }
                
                $checkout->process_checkout();
        }
        
        
        /**
        *  Filter tor restore Cart if there is an error during Woocommerce Checkout
        */
        public function notice_error_cart_restore ( $message ) {
                //Restore Cart
                if( $this->_cart ) {
                        WC()->cart = $this->_cart;
                        WC()->cart->set_session();
                        $this->_cart = false;
                }

                return $message;        
        }
        
        /*
         * On quote checkout check if is quotable instead of purchasable
         */        
        public function is_purchasable ( $purchasable, $product ) {
            
                return is_quotable( $product );                
        }
    
        
        public function post_process_create_order () {
                
                //Restore Cart
                WC()->cart = $this->_cart;
                WC()->cart->set_session();
                
                if($this->order_id) {
                        WC_Adq()->quote->remove_all_quote_item();
                        
                        do_action( 'adq_after_quote_create', $this->order_id );
                    
                        do_action( 'adq_email_customer_quote', $this->order_id );
                
                        do_action( 'adq_email_admin_new_quote', $this->order_id );
                        
                        do_action( 'adq_after_quote_email_send', $this->order_id );

                        $message = sprintf('%s<br/><br/>%s<br>%s',
                                strtoupper( __('Your quote request has been received','woocommerce-quotation') ),
                                __('Thank you for your interest!', 'woocommerce-quotation'),
                                sprintf(__('Your quote request # is: Q.%06d.','woocommerce-quotation'), $this->order_id)                
                        );
                        adq_add_notice( $message );                        
                }
                else {                        
                        adq_add_notice( __('There is an error creating your quote request','woocommerce-quotation'), 'error' );
                }
        }
        
        
        public function getVariations ($variation_id, $product_id) {
            
                $adding_to_cart      = wc_get_product( $product_id );
                $attributes = $adding_to_cart->get_attributes();
                $variation  = wc_get_product( $variation_id );
                $variations         = array();

                // Verify all attributes
                foreach ( $attributes as $attribute ) {
                        if ( ! $attribute['is_variation'] ) {
                                continue;
                        }

                        $taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

                        if ( isset( $_REQUEST[ $taxonomy ] ) ) {

                                // Get value from post data
                                // Don't use wc_clean as it destroys sanitized characters
                                $value = sanitize_title( trim( stripslashes( $_REQUEST[ $taxonomy ] ) ) );

                                // Get valid value from variation
                                $valid_value = $variation->variation_data[ $taxonomy ];

                                // Allow if valid
                                if ( $valid_value == '' || $valid_value == $value ) {
                                        if ( $attribute['is_taxonomy'] ) {
                                                $variations[ $taxonomy ] = $value;
                                        }
                                        else {
                                                // For custom attributes, get the name from the slug
                                                $options = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
                                                foreach ( $options as $option ) {
                                                        if ( sanitize_title( $option ) == $value ) {
                                                                $value = $option;
                                                                break;
                                                        }
                                                }
                                                 $variations[ $taxonomy ] = $value;
                                        }
                                        continue;
                                }

                        }

                        $all_variations_set = false;
                }
                
                return $variations;
        }        
       
}

endif;