<?php
/**
 * Handle Quote functions
 *
 * @class 	WC_Quotation_Quote
 * @version     2.0.0
 * @package     woocommerce-quotation/classes/
 * @category    Class
 * @author      Aldaba Digital
 */

if( !class_exists( 'WC_Quotation_Quote' ) ) {     

    class WC_Quotation_Quote { 
        
        /** @var array Contains an array of cart items. */
	public $quote_contents = array();
        
        /** @var array Contains an array of coupon codes applied to the cart. */
	public $quote_applied_coupons = array();

	/** @var array Contains an array of coupon code discounts after they have been applied. */
	public $coupon_discount_amounts = array();

	/** @var array Contains an array of coupon code discount taxes. Used for tax incl pricing. */
	public $coupon_discount_tax_amounts = array();

	/** @var array Contains an array of coupon usage counts after they have been applied. */
	public $coupon_applied_count = array();

	/** @var array Array of coupons */
	public $coupons = array();
        
        /** @var boolean Prices inc tax */
	public $prices_include_tax;
        
        /** @var float The total cost of the cart items. */
	public $cart_contents_total;
        
        /** @var Array of removed contents */
        public $removed_cart_contents;

	/** @var float The total weight of the cart items. */
	public $cart_contents_weight;

	/** @var float The total count of the cart items. */
	public $cart_contents_count;

	/** @var float Cart grand total. */
	public $total;

	/** @var float Cart subtotal. */
	public $subtotal;

	/** @var float Cart subtotal without tax. */
	public $subtotal_ex_tax;

	/** @var float Total cart tax. */
	public $tax_total;

	/** @var array An array of taxes/tax rates for the cart. */
	public $taxes = array();

	/** @var array An array of taxes/tax rates for the shipping. */
	public $shipping_taxes = array();
        
        /** @var string */
        public $tax_display_cart;

	/** @var float Discount amount before tax */
	public $discount_cart;

	/** @var float Discounted tax amount. Used predominantly for displaying tax inclusive prices correctly */
	public $discount_cart_tax;

	/** @var float Total for additional fees. */
	public $fee_total;

	/** @var float Shipping cost. */
	public $shipping_total;

	/** @var float Shipping tax. */
	public $shipping_tax_total;
        
        /** @var array cart_session_data. Array of data the cart calculates and stores in the session with defaults */
	public $quote_session_data = array(
		'quote_applied_coupons'  => array()
	);
        
        /** @var bool flag if session is loaded */
        protected $loaded = false;
        
        protected static $_instance;        
        
        public function __construct() {                                   
                add_action( 'wp_loaded', array( $this, 'init' ), 15 );
                
                add_action( 'wp_head', array( $this, 'calculate_totals' ) );                
        }
        
        public static function instance() {
                if ( is_null( self::$_instance ) ) {
                        self::$_instance = new self();
                }
                return self::$_instance;
        }
        
        /**
	 * Loads the cart data from the PHP session during WordPress init and hooks in other methods.
	 */
	public function init() {   
                $this->loaded = true;
                $this->prices_include_tax = wc_prices_include_tax();
                
                if( !is_object( WC()->session ) ) {                    
                        include_once( WC()->plugin_path().'/includes/abstracts/abstract-wc-session.php' );
                        include_once( WC()->plugin_path().'/includes/class-wc-session-handler.php' );
                
                        $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

                        // Class instances
                        WC()->session  = new $session_class();
                }
                
		$this->get_quote_from_cookie();
	}
        
        /*
         * Add Quoted Product to a list, if the user isn't logged save into a cookie
         */
        public function add_to_quote ( $product_id, $quantity = 1, $variation_id = false, $variations = array(), $cart_item_data = array() ) {                
                
                // Wrap in try catch so plugins can throw an exception to prevent adding to cart
                try {
                        // Ensure we don't add a variation to the cart directly by variation ID
                        if ( 'product_variation' == get_post_type( $product_id ) ) {
                                $variation_id = $product_id;
                                $product_id   = wp_get_post_parent_id( $variation_id );
                        }

                        // Get the product
                        $product_data = wc_get_product( $variation_id? $variation_id : $product_id );

                        // Sanitity check
                        if ( $quantity <= 0 || ! $product_data || 'trash' === $product_data->post->post_status  ) {
                                throw new Exception();
                        }

                        // Load cart item data - may be added by other plugins
                        $cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id );

                        // Generate a ID based on product ID, variation ID, variation data, and other cart item data
                        $cart_id = $this->generate_quote_id( $product_id, $variation_id, $variations, $cart_item_data );

                        // Find the cart item key in the existing cart
                        $cart_item_key  = $this->find_product_in_quote( $cart_id );

                        // Force quantity to 1 if sold individually and check for exisitng item in cart
                        if ( $product_data->is_sold_individually() ) {
                                $quantity         = apply_filters( 'woocommerce_add_to_cart_sold_individually_quantity', 1, $quantity, $product_id, $variation_id, $cart_item_data );
                                $in_cart_quantity = $cart_item_key ? $this->quote_contents[ $cart_item_key ]['quantity'] : 0;

                                if ( $in_cart_quantity > 0 ) {
                                        throw new Exception( StaticAdqQuoteRequest::get_duplicate_notice($product_id, true) );
                                }
                        }

                        // Check product is_quotable
                        if ( ! is_quotable( $product_data ) ) {
                                throw new Exception( __( 'Sorry, this product cannot be purchased.', 'woocommerce-quotation' ) );
                        }

                        // Stock check - only check if we're managing stock and backorders are not allowed
                        if ( ! $product_data->is_in_stock() ) {
                                throw new Exception( sprintf( __( 'You cannot add &quot;%s&quot; to the quote list because the product is out of stock.', 'woocommerce-quotation' ), $product_data->get_title() ) );
                        }

                        if ( ! $product_data->has_enough_stock( $quantity ) ) {
                                throw new Exception( sprintf(__( 'You cannot add that amount of &quot;%s&quot; to the quote list because there is not enough stock (%s remaining).', 'woocommerce-quotation' ), $product_data->get_title(), $product_data->get_stock_quantity() ) );
                        }

                        // Stock check - this time accounting for whats already in-cart
                        if ( $managing_stock = $product_data->managing_stock() ) {
                                $products_qty_in_cart = $this->get_quote_item_quantities();

                                if ( $product_data->is_type( 'variation' ) && true === $managing_stock ) {
                                        $check_qty = isset( $products_qty_in_cart[ $variation_id ] ) ? $products_qty_in_cart[ $variation_id ] : 0;
                                } else {
                                        $check_qty = isset( $products_qty_in_cart[ $product_id ] ) ? $products_qty_in_cart[ $product_id ] : 0;
                                }

                                /**
                                 * Check stock based on all items in the cart
                                 */
                                if ( ! $product_data->has_enough_stock( $check_qty + $quantity ) ) {
                                        throw new Exception( sprintf(
                                                '<a href="%s" class="button wc-forward">%s</a> %s',
                                                $this->get_cart_url(),
                                                __( 'View Quote', 'woocommerce' ),
                                                sprintf( __( 'You cannot add that amount to the cart &mdash; we have %s in stock and you already have %s in your cart.', 'woocommerce' ), $product_data->get_stock_quantity(), $check_qty )
                                        ) );
                                }
                        }

                        // If cart_item_key is set, the item is already in the cart
                        if ( $cart_item_key ) {
                                $new_quantity = $quantity + (int)$this->quote_contents[ $cart_item_key ]['quantity'];
                                $this->set_quantity( $cart_item_key, $new_quantity );
                        } else {
                                $cart_item_key = $cart_id;

                                // Add item after merging with $cart_item_data - hook to allow plugins to modify cart item
                                $this->quote_contents[ $cart_item_key ] = apply_filters( 'woocommerce_add_quote_item', array_merge( $cart_item_data, array(
                                        'product_id'	=> $product_id,
                                        'variation_id'	=> $variation_id,
                                        'variation' 	=> $variations,
                                        'quantity'	=> $quantity,
                                        'data'		=> $product_data,
                                        'cart_item_data' => $cart_item_data
                                ) ), $cart_item_key );
                        }                                                                        
                        
                        adq_add_notice( StaticAdqQuoteRequest::get_ok_notice($product_id, true) );                        
                        
                        do_action( 'woocommerce_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variations, $cart_item_data );
                        
                        $this->set_quote_cookies();
                        
                        return $cart_item_key;

                } catch ( Exception $e ) {
                        if ( $e->getMessage() ) {
                                adq_add_notice( $e->getMessage(), 'error' );
                        }
                        return false;
                }                
        }
        
        /**
        * Remove a quote item
        *
        * @param  string $cart_item_key
        * @return bool
        */
        public function remove_quote_item( $cart_item_key ) {
                if ( isset( $this->quote_contents[ $cart_item_key ] ) ) {

                        unset( $this->quote_contents[ $cart_item_key ] );                                                

                        do_action( 'woocommerce_quote_item_removed', $cart_item_key, $this );
                        
                        $this->set_quote_cookies();                        

                        return true;
                }

                return false;
        }
        
        
        /**
        * Remove all quote items        
        */
        public function remove_all_quote_item () {
                return $this->unset_quote_cookies();
        }
        
        
        /**
        * Update meta value in a quote item
        *
        * @param  string $cart_item_key
        * @param  string $meta_key
        * @param  string $meta_value
        * @return bool
        */
        public function update_meta_quote_item( $cart_item_key, $meta_key, $meta_value ) {
                if ( isset( $this->quote_contents[ $cart_item_key ] ) ) {

                        $this->quote_contents[ $cart_item_key ][$meta_key] = $meta_value;

                        do_action( 'woocommerce_quote_item_updated_meta', $cart_item_key, $meta_key, $meta_value, $this );

                        $this->set_quote_cookies();                        

                        return true;
                }

                return false;
        }
        
        
        /**
        * Check if product is in the Quote List and return cart item key.
        *
        * Cart item key will be unique based on the item and its properties, such as variations.
        *
        * @param mixed id of product to find in the cart
        * @return string cart item key
        */
        public function find_product_in_quote( $quote_id = false ) {
                if ( $quote_id !== false ) {
                        if ( is_array( $this->quote_contents ) ) {
                                foreach ( $this->quote_contents as $cart_item_key => $quote_item ) {
                                        if ( $cart_item_key == $quote_id ) {
                                                return $cart_item_key;
                                        }
                                }
                        }
                }
                return '';
        }
        
        
        /**
         * Get cart items quantities - merged so we can do accurate stock checks on items across multiple lines.
         *
         * @return array
         */
        public function get_quote_item_quantities() {
                $quantities = array();

                foreach ( $this->get_quote() as $cart_item_key => $values ) {
                        $_product = $values['data'];

                        if ( $_product->is_type( 'variation' ) && true === $_product->managing_stock() ) {
                                // Variation has stock levels defined so its handled individually
                                $quantities[ $values['variation_id'] ] = isset( $quantities[ $values['variation_id'] ] ) ? $quantities[ $values['variation_id'] ] + $values['quantity'] : $values['quantity'];
                        } else {
                                $quantities[ $values['product_id'] ] = isset( $quantities[ $values['product_id'] ] ) ? $quantities[ $values['product_id'] ] + $values['quantity'] : $values['quantity'];
                        }
                }

                return $quantities;
        }
        
        /**
        * Set the quantity for an item in the quote list.
        *
        * @param string	cart_item_key	contains the id of the cart item
        * @param string	quantity		contains the quantity of the item
        *
        * @return bool
        */
        public function set_quantity( $cart_item_key, $quantity = 1) {               
               if ( $quantity == 0 || $quantity < 0 ) {
                       do_action( 'woocommerce_before_quote_item_quantity_zero', $cart_item_key );
                       unset( $this->quote_contents[ $cart_item_key ] );
               } else {
                       $old_quantity = $this->quote_contents[ $cart_item_key ]['quantity'];
                       $this->quote_contents[ $cart_item_key ]['quantity'] = $quantity;
                       do_action( 'woocommerce_after_quote_item_quantity_update', $cart_item_key, $quantity, $old_quantity );
               }

               return true;
        }
        
        
        
        /**
        * Set the quantity for an item in the quote list.
        *
        * @param string	cart_item_key	contains the id of the cart item
        * @param string	quantity		contains the quantity of the item
        *
        * @return bool
        */
        public function update_quantity( $cart_item_key, $quantity = 1) {
               
                $this->set_quantity($cart_item_key, $quantity);                 

                $this->set_quote_cookies();
                
                return true;
        }
        
        /**
        * Returns the contents of the cart in an array.
        *
        * @return array contents of the cart
        */
        public function get_quote() {                
                if ( ! $this->loaded ) {
                        $this->init();
                }
                return array_filter( (array) $this->quote_contents );
        }
        
        /**
        * Checks if the quote is empty.
        *
        * @return bool
        */
        public function is_empty() {
                return 0 === sizeof( $this->get_quote() );
        }
        
        
        /**
        * Get the cart data from the PHP COOKIE and store it in class variables.
        */        
        public function get_quote_from_cookie () {
             
                foreach ( $this->quote_session_data as $key => $default ) {
                        $this->$key = WC()->session->get( $key, $default );
                }
                
                $quote_contents = WC()->session->get( 'quote_contents', array() );
                
                foreach($quote_contents as $key => $values) {
                        $_product = wc_get_product( $values['variation_id'] ? $values['variation_id'] : $values['product_id'] );

                        if ( ! empty( $_product ) && $_product->exists() && $values['quantity'] > 0 ) {

                                if ( ! is_quotable( $_product ) ) {

                                        // Flag to indicate the stored cart should be update
                                        wc_add_notice( sprintf( __( '%s has been removed from your quote list because it can no longer be purchased. Please contact us if you need assistance.', 'woocommerce-quotation' ), $_product->get_title() ), 'error' );
                                } else {

                                        // Put session data into array. Run through filter so other plugins can load their own session data
                                        $session_data = array_merge( $values, array( 'data' => $_product ) );
                                        $this->quote_contents[ $key ] = apply_filters( 'woocommerce_get_quote_item_from_session', $session_data, $values, $key );                                                 
                                }
                        }
                }                 
                
                // Trigger action
                do_action( 'woocommerce_quote_loaded_from_session', $this );
                
                return $this;
        }                
        
        /**
	 * Set cart hash cookie and items in cart.
	 *
	 * @access private
	 * @param bool $set (default: true)
	 */
        private function set_quote_cookies ( $set = true ) {                
                                
                /* Unset data */
                $quote_contents = $this->quote_contents;
                foreach($quote_contents as $cart_item_key => $values) {
                        unset($quote_contents[$cart_item_key]['data']);
                }               
                
                WC()->session->set( 'quote_contents', $quote_contents );                
                
                foreach ( $this->quote_session_data as $key => $default ) {
                        WC()->session->set( $key, $this->$key );
                }                 
                
                do_action( 'woocommerce_set_cart_cookies', $set );
                
                return true;
        }
        
        /**
	 * Set cart hash cookie and items in cart.
	 *
	 * @access private
	 * @param bool $set (default: true)
	 */
        private function unset_quote_cookies ( $set = true ) {
                
                $this->quote_contents = array();                
                $this->quote_applied_coupons = array();
                $this->coupon_discount_amounts = array();
                $this->coupon_discount_tax_amounts = array();
                
                $this->set_quote_cookies();
                
                return true;
        }

        /**
         * Generate a unique ID for the cart item being added.
         *
         * @param int $product_id - id of the product the key is being generated for
         * @param int $variation_id of the product the key is being generated for
         * @param array $variation data for the cart item
         * @param array $cart_item_data other cart item data passed which affects this items uniqueness in the cart
         * @return string cart item key
         */
        public function generate_quote_id( $product_id, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {
                $id_parts = array( $product_id );

                if ( $variation_id && 0 != $variation_id )
                        $id_parts[] = $variation_id;
                
                if ( is_array( $variation ) && ! empty( $variation ) ) {
                        $variation_key = '';
                        foreach ( $variation as $key => $value ) {
                                $variation_key .= trim( $key ) . trim( $value );
                        }
                        $id_parts[] = $variation_key;
                }

                if ( is_array( $cart_item_data ) && ! empty( $cart_item_data ) ) {
                        $cart_item_data_key = '';
                        foreach ( $cart_item_data as $key => $value ) {
                                if ( is_array( $value ) ) $value = http_build_query( $value );
                                $cart_item_data_key .= trim($key) . trim($value);
                        }
                        $id_parts[] = $cart_item_data_key;
                }

                return md5( implode( '_', $id_parts ) );
        }
        
        /**
        * Looks through the cart to see if shipping is actually required.
        *
        * @return bool whether or not the cart needs shipping
        */
        public function needs_shipping() {
                if ( ( get_option("adq_inherit_shipping_conf") == "no" && get_option('adq_calc_shipping') == 'no' )
                    || ( get_option("adq_inherit_shipping_conf") == "yes" && get_option('woocommerce_calc_shipping') == 'no') )
                        return false;
                
                $needs_shipping = false;
               
                if ( $this->quote_contents ) {
                        foreach ( $this->quote_contents as $cart_item_key => $values ) {
                                $_product = $values['data'];
                                if ( $_product->needs_shipping() ) {
                                        $needs_shipping = true;
                                }
                        }
                }

                return $needs_shipping;
        }

        /**
        * Should the shipping address form be shown
        *
        * @return bool
        */
        function needs_shipping_address() {

               $needs_shipping_address = false;
              
               if ( $this->needs_shipping() === true && ! $this->ship_to_billing_address_only() ) {
                       $needs_shipping_address = true;
               }

               return $needs_shipping_address;
        }       
       
        /**
        * Sees if we need a shipping address.
        *
        * @return bool
        */
        public function ship_to_billing_address_only() {
                if ( get_option("adq_inherit_shipping_conf") == "no" ) {
                        return 'billing_only' === get_option( 'adq_ship_to_destination' );
                }
                else {
                        return 'billing_only' === get_option( 'woocommerce_ship_to_destination' );
                }
        }
        
        /**
        * Get packages to calculate shipping for.
        *
        * This lets us calculate costs for carts that are shipped to multiple locations.
        *
        * Shipping methods are responsible for looping through these packages.
        *
        * By default we pass the cart itself as a package - plugins can change this
        * through the filter and break it up.
        *
        * @since 1.5.4
        * @return array of cart items
        */
        public function get_shipping_packages() {
                // Packages array for storing 'carts'
                $packages = array();

                $packages[0]['contents']                 = $this->quote_contents; // Items in the package
                $packages[0]['contents_cost']            = 0;						// Cost of items in the package, set below
                $packages[0]['quote_applied_coupons']    = 0;
                $packages[0]['user']['ID']               = get_current_user_id();
                $packages[0]['destination']['country']   = WC()->customer->get_shipping_country();
                $packages[0]['destination']['state']     = WC()->customer->get_shipping_state();
                $packages[0]['destination']['postcode']  = WC()->customer->get_shipping_postcode();
                $packages[0]['destination']['city']      = WC()->customer->get_shipping_city();
                $packages[0]['destination']['address']   = WC()->customer->get_shipping_address();
                $packages[0]['destination']['address_2'] = WC()->customer->get_shipping_address_2();

                foreach ( $this->quote_contents as $item ) {
                        if ( $item['data']->needs_shipping() ) {
                                if ( isset( $item['line_total'] ) ) {
                                        $packages[0]['contents_cost'] += $item['line_total'];
                                }
                        }
                }

                return $packages;
        }
        
        /*-----------------------------------------------------------------------------------*/
	/* Coupons/Discount related functions */
	/*-----------------------------------------------------------------------------------*/

        /**
         * Check for user coupons (now that we have billing email). If a coupon is invalid, add an error.
         *
         * Checks two types of coupons:
         *  1. Where a list of customer emails are set (limits coupon usage to those defined)
         *  2. Where a usage_limit_per_user is set (limits coupon usage to a number based on user ID and email)
         *
         * @param array $posted
         */
        public function check_customer_coupons( $posted ) {
                if ( ! empty( $this->quote_applied_coupons ) ) {
                        foreach ( $this->quote_applied_coupons as $code ) {
                                $coupon = new WC_Coupon( $code );

                                if ( $coupon->is_valid() ) {

                                        // Limit to defined email addresses
                                        if ( is_array( $coupon->customer_email ) && sizeof( $coupon->customer_email ) > 0 ) {
                                                $check_emails           = array();
                                                $coupon->customer_email = array_map( 'sanitize_email', $coupon->customer_email );

                                                if ( is_user_logged_in() ) {
                                                        $current_user   = wp_get_current_user();
                                                        $check_emails[] = $current_user->user_email;
                                                }
                                                $check_emails[] = $posted['billing_email'];
                                                $check_emails   = array_map( 'sanitize_email', array_map( 'strtolower', $check_emails ) );

                                                if ( 0 == sizeof( array_intersect( $check_emails, $coupon->customer_email ) ) ) {
                                                        $coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_NOT_YOURS_REMOVED );

                                                        // Remove the coupon
                                                        $this->remove_coupon( $code );
                                                }
                                        }

                                        // Usage limits per user - check against billing and user email and user ID
                                        if ( $coupon->usage_limit_per_user > 0 ) {
                                                $check_emails = array();
                                                $used_by      = array_filter( (array) get_post_meta( $coupon->id, '_used_by' ) );

                                                if ( is_user_logged_in() ) {
                                                        $current_user   = wp_get_current_user();
                                                        $check_emails[] = sanitize_email( $current_user->user_email );
                                                        $usage_count    = sizeof( array_keys( $used_by, get_current_user_id() ) );
                                                } else {
                                                        $check_emails[] = sanitize_email( $posted['billing_email'] );
                                                        $user           = get_user_by( 'email', $posted['billing_email'] );
                                                        if ( $user ) {
                                                                $usage_count = sizeof( array_keys( $used_by, $user->ID ) );
                                                        } else {
                                                                $usage_count = 0;
                                                        }
                                                }

                                                foreach ( $check_emails as $check_email ) {
                                                        $usage_count = $usage_count + sizeof( array_keys( $used_by, $check_email ) );
                                                }

                                                if ( $usage_count >= $coupon->usage_limit_per_user ) {
                                                        $coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED );

                                                        // Remove the coupon
                                                        $this->remove_coupon( $code );
                                                }
                                        }
                                }
                        }
                }
        }

        /**
         * Returns whether or not a discount has been applied.
         *
         * @return bool
         */
        public function has_discount( $coupon_code ) {
                return in_array( apply_filters( 'woocommerce_coupon_code', $coupon_code ), $this->quote_applied_coupons );
        }

        /**
         * Applies a coupon code passed to the method.
         *
         * @param string $coupon_code - The code to apply
         * @return bool	True if the coupon is applied, false if it does not exist or cannot be applied
         */
        public function add_discount( $coupon_code ) {
                // Coupons are globally disabled
                if ( ! $this->coupons_enabled() ) {
                        return false;
                }

                // Sanitize coupon code
                $coupon_code = apply_filters( 'woocommerce_coupon_code', $coupon_code );

                // Get the coupon
                $the_coupon = new WC_Coupon( $coupon_code );

                // Check it can be used with cart
                if ( ! $the_coupon->is_valid() ) {
                        wc_add_notice( $the_coupon->get_error_message(), 'error' );
                        return false;
                }

                // Check if applied
                if ( $this->has_discount( $coupon_code ) ) {
                        $the_coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_ALREADY_APPLIED );
                        return false;
                }

                // If its individual use then remove other coupons
                if ( $the_coupon->individual_use == 'yes' ) {
                        $this->quote_applied_coupons = apply_filters( 'woocommerce_apply_individual_use_coupon', array(), $the_coupon, $this->quote_applied_coupons );
                }

                if ( $this->quote_applied_coupons ) {
                        foreach ( $this->quote_applied_coupons as $code ) {
                                $coupon = new WC_Coupon( $code );

                                if ( $coupon->individual_use == 'yes' && false === apply_filters( 'woocommerce_apply_with_individual_use_coupon', false, $the_coupon, $coupon, $this->quote_applied_coupons ) ) {

                                        // Reject new coupon
                                        $coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_ALREADY_APPLIED_INDIV_USE_ONLY );

                                        return false;
                                }
                        }
                }

                $this->quote_applied_coupons[] = $coupon_code;

                // Choose free shipping
                if ( $the_coupon->enable_free_shipping() ) {
                        $packages = WC()->shipping->get_packages();
                        $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

                        foreach ( $packages as $i => $package ) {
                                $chosen_shipping_methods[ $i ] = 'free_shipping';
                        }

                        WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
                }

                $the_coupon->add_coupon_message( WC_Coupon::WC_COUPON_SUCCESS );

                $this->set_quote_cookies(); 
                    
                do_action( 'woocommerce_applied_coupon', $coupon_code );

                return true;
        }

        /**
         * Get array of applied coupon objects and codes.
         * @return array of applied coupons
         */
        public function get_coupons( $deprecated = null ) {
                $coupons = array();

                if ( 'order' === $deprecated ) {
                        return $coupons;
                }

                foreach ( $this->get_applied_coupons() as $code ) {
                        $coupon = new WC_Coupon( $code );
                        $coupons[ $code ] = $coupon;
                }

                return $coupons;
        }

        /**
         * Gets the array of applied coupon codes.
         *
         * @return array of applied coupons
         */
        public function get_applied_coupons() {
                return $this->quote_applied_coupons;
        }

        /**
         * Get the discount amount for a used coupon
         * @param  string $code coupon code
         * @param  bool inc or ex tax
         * @return float discount amount
         */
        public function get_coupon_discount_amount( $code, $ex_tax = true ) {
                if ( $ex_tax ) {
                        return isset( $this->coupon_discount_amounts[ $code ] ) ? $this->coupon_discount_amounts[ $code ] - $this->get_coupon_discount_tax_amount( $code ) : 0;
                } else {
                        return isset( $this->coupon_discount_amounts[ $code ] ) ? $this->coupon_discount_amounts[ $code ] : 0;
                }
        }

        /**
         * Get the discount tax amount for a used coupon (for tax inclusive prices)
         * @param  string $code coupon code
         * @param  bool inc or ex tax
         * @return float discount amount
         */
        public function get_coupon_discount_tax_amount( $code ) {
                return isset( $this->coupon_discount_tax_amounts[ $code ] ) ? $this->coupon_discount_tax_amounts[ $code ] : 0;
        }

        /**
         * Remove coupons from the cart of a defined type. Type 1 is before tax, type 2 is after tax.
         */
        public function remove_coupons( $deprecated = null ) {
                $this->quote_applied_coupons = $this->coupon_discount_amounts = $this->coupon_discount_tax_amounts = $this->coupon_applied_count = array();
                
                $this->set_quote_cookies();
        }

        /**
         * Remove a single coupon by code
         * @param  string $coupon_code Code of the coupon to remove
         * @return bool
         */
        public function remove_coupon( $coupon_code ) {
                // Coupons are globally disabled
                if ( ! $this->coupons_enabled() )
                        return false;

                // Get the coupon
                $coupon_code  = apply_filters( 'woocommerce_coupon_code', $coupon_code );
                $position     = array_search( $coupon_code, $this->quote_applied_coupons );

                if ( $position !== false )
                        unset( $this->quote_applied_coupons[ $position ] );

                $this->set_quote_cookies();

                return true;
        }

        /**
         * Function to apply discounts to a product and get the discounted price (before tax is applied).
         *
         * @param mixed $values
         * @param mixed $price
         * @param bool $add_totals (default: false)
         * @return float price
         */
        public function get_discounted_price( $values, $price, $add_totals = false ) {
                if ( ! $price ) {
                        return $price;
                }

                if ( ! empty( $this->coupons ) ) {

                        $product = $values['data'];

                        foreach ( $this->coupons as $code => $coupon ) {                                
                                if ( $coupon->is_valid() && ( $coupon->is_valid_for_product( $product, $values ) || $coupon->is_valid_for_cart() ) ) {                                        
                                        $discount_amount = $coupon->get_discount_amount( $price, $values, $single = true );  
                                        
                                        $price           = max( $price - $discount_amount, 0 );

                                        // Store the totals for DISPLAY in the cart
                                        if ( $add_totals ) {
                                                $total_discount     = $discount_amount * $values['quantity'];
                                                $total_discount_tax = 0;

                                                if ( $this->prices_include_tax ) {
                                                        $tax_rates           = WC_Tax::get_rates( $product->get_tax_class() );
                                                        $taxes               = WC_Tax::calc_tax( $discount_amount, $tax_rates, true );
                                                        $total_discount_tax  = WC_Tax::get_tax_total( $taxes ) * $values['quantity'];
                                                }
                                                
                                                $this->increase_coupon_discount_amount( $code, $total_discount, $total_discount_tax );
                                                $this->increase_coupon_applied_count( $code, $values['quantity'] );
                                        }
                                }
                        }
                }

                return apply_filters( 'woocommerce_get_discounted_price', $price, $values, $this );
        }

        /**
         * Store how much discount each coupon grants.
         *
         * @access private
         * @param string $code
         * @param double $amount
         * @param double $tax
         */
        private function increase_coupon_discount_amount( $code, $amount, $tax ) {
                $this->coupon_discount_amounts[ $code ]     = isset( $this->coupon_discount_amounts[ $code ] ) ? $this->coupon_discount_amounts[ $code ] + $amount : $amount;
                $this->coupon_discount_tax_amounts[ $code ] = isset( $this->coupon_discount_tax_amounts[ $code ] ) ? $this->coupon_discount_tax_amounts[ $code ] + $tax : $tax;
        }

        /**
         * Store how many times each coupon is applied to cart/items
         *
         * @access private
         * @param string $code
         * @param integer $count
         */
        private function increase_coupon_applied_count( $code, $count = 1 ) {
                if ( empty( $this->coupon_applied_count[ $code ] ) ) {
                        $this->coupon_applied_count[ $code ] = 0;
                }
                $this->coupon_applied_count[ $code ] += $count;
        }
        
        
        public function coupons_enabled() {
                return apply_filters( 'adq_coupons_enabled', get_option( 'adq_coupons_enabled' ) == 'yes' );
        }
        
        
        /**
        * Calculate totals for the items in the cart.
        */
       public function calculate_totals() {

               $this->coupons = $this->get_coupons();

               do_action( 'woocommerce_before_calculate_totals', $this );
               
               if ( sizeof( $this->get_quote() ) == 0 ) {
                       return;
               }

               $tax_rates      = array();
               $shop_tax_rates = array();

               /**
                * Calculate subtotals for items. This is done first so that discount logic can use the values.
                */
               foreach ( $this->get_quote() as $cart_item_key => $values ) {
                    
                       $_product = $values['data'];

                       // Count items + weight
                       $this->cart_contents_weight += $_product->get_weight() * $values['quantity'];
                       $this->cart_contents_count  += $values['quantity'];

                       // Prices
                       $line_price = $_product->get_price() * $values['quantity'];

                       $line_subtotal = 0;
                       $line_subtotal_tax = 0;

                       /**
                        * No tax to calculate
                        */
                       if ( ! $_product->is_taxable() ) {

                               // Subtotal is the undiscounted price
                               $this->subtotal += $line_price;
                               $this->subtotal_ex_tax += $line_price;

                       /**
                        * Prices include tax
                        *
                        * To prevent rounding issues we need to work with the inclusive price where possible
                        * otherwise we'll see errors such as when working with a 9.99 inc price, 20% VAT which would
                        * be 8.325 leading to totals being 1p off
                        *
                        * Pre tax coupons come off the price the customer thinks they are paying - tax is calculated
                        * afterwards.
                        *
                        * e.g. $100 bike with $10 coupon = customer pays $90 and tax worked backwards from that
                        */
                       } elseif ( $this->prices_include_tax ) {

                               // Get base tax rates
                               if ( empty( $shop_tax_rates[ $_product->tax_class ] ) ) {
                                       $shop_tax_rates[ $_product->tax_class ] = WC_Tax::get_base_tax_rates( $_product->tax_class );
                               }

                               // Get item tax rates
                               if ( empty( $tax_rates[ $_product->get_tax_class() ] ) ) {
                                       $tax_rates[ $_product->get_tax_class() ] = WC_Tax::get_rates( $_product->get_tax_class() );
                               }

                               $base_tax_rates = $shop_tax_rates[ $_product->tax_class ];
                               $item_tax_rates = $tax_rates[ $_product->get_tax_class() ];

                               /**
                                * ADJUST TAX - Calculations when base tax is not equal to the item tax
                                */
                               if ( $item_tax_rates !== $base_tax_rates ) {

                                       // Work out a new base price without the shop's base tax
                                       $taxes                 = WC_Tax::calc_tax( $line_price, $base_tax_rates, true, true );

                                       // Now we have a new item price (excluding TAX)
                                       $line_subtotal         = $line_price - array_sum( $taxes );

                                       // Now add modified taxes
                                       $tax_result            = WC_Tax::calc_tax( $line_subtotal, $item_tax_rates );
                                       $line_subtotal_tax     = array_sum( $tax_result );

                               /**
                                * Regular tax calculation (customer inside base and the tax class is unmodified
                                */
                               } else {

                                       // Calc tax normally
                                       $taxes                 = WC_Tax::calc_tax( $line_price, $item_tax_rates, true );
                                       $line_subtotal_tax     = array_sum( $taxes );
                                       $line_subtotal         = $line_price - array_sum( $taxes );
                               }

                       /**
                        * Prices exclude tax
                        *
                        * This calculation is simpler - work with the base, untaxed price.
                        */
                       } else {

                               // Get item tax rates
                               if ( empty( $tax_rates[ $_product->get_tax_class() ] ) ) {
                                       $tax_rates[ $_product->get_tax_class() ] = WC_Tax::get_rates( $_product->get_tax_class() );
                               }

                               $item_tax_rates        = $tax_rates[ $_product->get_tax_class() ];

                               // Base tax for line before discount - we will store this in the order data
                               $taxes                 = WC_Tax::calc_tax( $line_price, $item_tax_rates );
                               $line_subtotal_tax     = array_sum( $taxes );

                               $line_subtotal         = $line_price;
                       }

                       // Add to main subtotal
                       $this->subtotal        += $line_subtotal + $line_subtotal_tax;
                       $this->subtotal_ex_tax += $line_subtotal;
               }

               /**
                * Calculate totals for items
                */
               foreach ( $this->get_quote() as $cart_item_key => $values ) {

                       $_product = $values['data'];

                       // Prices
                       $base_price = $_product->get_price();
                       $line_price = $_product->get_price() * $values['quantity'];

                       // Tax data
                       $taxes = array();
                       $discounted_taxes = array();

                       /**
                        * No tax to calculate
                        */
                       if ( ! $_product->is_taxable() ) {
                            
                               // Discounted Price (price with any pre-tax discounts applied)
                               $discounted_price      = $this->get_discounted_price( $values, $base_price, true );
                               $line_subtotal_tax     = 0;
                               $line_subtotal         = $line_price;
                               $line_tax              = 0;
                               $line_total            = WC_Tax::round( $discounted_price * $values['quantity'] );

                       /**
                        * Prices include tax
                        */
                       } elseif ( $this->prices_include_tax ) {

                               $base_tax_rates = $shop_tax_rates[ $_product->tax_class ];
                               $item_tax_rates = $tax_rates[ $_product->get_tax_class() ];

                               /**
                                * ADJUST TAX - Calculations when base tax is not equal to the item tax
                                */
                               if ( $item_tax_rates !== $base_tax_rates ) {

                                       // Work out a new base price without the shop's base tax
                                       $taxes             = WC_Tax::calc_tax( $line_price, $base_tax_rates, true, true );

                                       // Now we have a new item price (excluding TAX)
                                       $line_subtotal     = round( $line_price - array_sum( $taxes ), WC_ROUNDING_PRECISION );
                                       $taxes             = WC_Tax::calc_tax( $line_subtotal, $item_tax_rates );
                                       $line_subtotal_tax = array_sum( $taxes );

                                       // Adjusted price (this is the price including the new tax rate)
                                       $adjusted_price    = ( $line_subtotal + $line_subtotal_tax ) / $values['quantity'];

                                       // Apply discounts
                                       $discounted_price  = $this->get_discounted_price( $values, $adjusted_price, true );
                                       $discounted_taxes  = WC_Tax::calc_tax( $discounted_price * $values['quantity'], $item_tax_rates, true );
                                       $line_tax          = array_sum( $discounted_taxes );
                                       $line_total        = ( $discounted_price * $values['quantity'] ) - $line_tax;

                               /**
                                * Regular tax calculation (customer inside base and the tax class is unmodified
                                */
                               } else {

                                       // Work out a new base price without the item tax
                                       $taxes             = WC_Tax::calc_tax( $line_price, $item_tax_rates, true );

                                       // Now we have a new item price (excluding TAX)
                                       $line_subtotal     = $line_price - array_sum( $taxes );
                                       $line_subtotal_tax = array_sum( $taxes );

                                       // Calc prices and tax (discounted)
                                       $discounted_price = $this->get_discounted_price( $values, $base_price, true );
                                       $discounted_taxes = WC_Tax::calc_tax( $discounted_price * $values['quantity'], $item_tax_rates, true );
                                       $line_tax         = array_sum( $discounted_taxes );
                                       $line_total       = ( $discounted_price * $values['quantity'] ) - $line_tax;
                               }

                               // Tax rows - merge the totals we just got
                               foreach ( array_keys( $this->taxes + $discounted_taxes ) as $key ) {
                                       $this->taxes[ $key ] = ( isset( $discounted_taxes[ $key ] ) ? $discounted_taxes[ $key ] : 0 ) + ( isset( $this->taxes[ $key ] ) ? $this->taxes[ $key ] : 0 );
                               }

                       /**
                        * Prices exclude tax
                        */
                       } else {

                               $item_tax_rates        = $tax_rates[ $_product->get_tax_class() ];

                               // Work out a new base price without the shop's base tax
                               $taxes                 = WC_Tax::calc_tax( $line_price, $item_tax_rates );

                               // Now we have the item price (excluding TAX)
                               $line_subtotal         = $line_price;
                               $line_subtotal_tax     = array_sum( $taxes );

                               // Now calc product rates
                               $discounted_price      = $this->get_discounted_price( $values, $base_price, true );
                               $discounted_taxes      = WC_Tax::calc_tax( $discounted_price * $values['quantity'], $item_tax_rates );
                               $discounted_tax_amount = array_sum( $discounted_taxes );
                               $line_tax              = $discounted_tax_amount;
                               $line_total            = $discounted_price * $values['quantity'];

                               // Tax rows - merge the totals we just got
                               foreach ( array_keys( $this->taxes + $discounted_taxes ) as $key ) {
                                       $this->taxes[ $key ] = ( isset( $discounted_taxes[ $key ] ) ? $discounted_taxes[ $key ] : 0 ) + ( isset( $this->taxes[ $key ] ) ? $this->taxes[ $key ] : 0 );
                               }
                       }

                       // Cart contents total is based on discounted prices and is used for the final total calculation
                       $this->cart_contents_total += $line_total;

                       // Store costs + taxes for lines
                       $this->quote_contents[ $cart_item_key ]['line_total']        = $line_total;
                       $this->quote_contents[ $cart_item_key ]['line_tax']          = $line_tax;
                       $this->quote_contents[ $cart_item_key ]['line_subtotal']     = $line_subtotal;
                       $this->quote_contents[ $cart_item_key ]['line_subtotal_tax'] = $line_subtotal_tax;

                       // Store rates ID and costs - Since 2.2
                       $this->quote_contents[ $cart_item_key ]['line_tax_data']     = array( 'total' => $discounted_taxes, 'subtotal' => $taxes );
               }

               // Only calculate the grand total + shipping if on the cart/checkout
               /*if ( is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART') ) {

                       // Calculate the Shipping
                       //$this->calculate_shipping();

                       // Trigger the fees API where developers can add fees to the cart
                       //$this->calculate_fees();

                       // Total up/round taxes and shipping taxes
                       if ( $this->round_at_subtotal ) {
                               $this->tax_total          = WC_Tax::get_tax_total( $this->taxes );
                               $this->shipping_tax_total = WC_Tax::get_tax_total( $this->shipping_taxes );
                               $this->taxes              = array_map( array( 'WC_Tax', 'round' ), $this->taxes );
                               $this->shipping_taxes     = array_map( array( 'WC_Tax', 'round' ), $this->shipping_taxes );
                       } else {
                               $this->tax_total          = array_sum( $this->taxes );
                               $this->shipping_tax_total = array_sum( $this->shipping_taxes );
                       }

                       // VAT exemption done at this point - so all totals are correct before exemption
                       if ( WC()->customer->is_vat_exempt() ) {
                               $this->remove_taxes();
                       }

                       // Allow plugins to hook and alter totals before final total is calculated
                       do_action( 'woocommerce_calculate_totals', $this );

                       // Grand Total - Discounted product prices, discounted tax, shipping cost + tax
                       $this->total = max( 0, apply_filters( 'woocommerce_calculated_total', round( $this->cart_contents_total + $this->tax_total + $this->shipping_tax_total + $this->shipping_total + $this->fee_total, $this->dp ), $this ) );

               } else {

                       // Set tax total to sum of all tax rows
                       $this->tax_total = WC_Tax::get_tax_total( $this->taxes );

                       // VAT exemption done at this point - so all totals are correct before exemption
                       if ( WC()->customer->is_vat_exempt() ) {
                               $this->remove_taxes();
                       }
               }*/

               do_action( 'woocommerce_after_calculate_totals', $this );
       }
    }    
}