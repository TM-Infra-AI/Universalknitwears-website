<?php
/**
 * Handle Order functions
 *
 * @version     1.0.0
 * @package     woocommerce-quotation/functions/
 * @category    Functions
 * @author      Aldaba Digital
 */

/*
 *  Register new Order Status 
 */
if( !function_exists( 'adq_register_quote_request_order_status' ) ) {
    function adq_register_quote_request_order_status() {
        register_post_status( 'wc-request', array(
            'label' => _x('Request', 'Order status', 'woocommerce-quotation'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Request <span class="count">(%s)</span>', 'Request <span class="count">(%s)</span>', 'woocommerce-quotation' )
        ) );

        register_post_status( 'wc-proposal', array(
            'label' => _x('Proposal', 'Order status', 'woocommerce-quotation'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Proposal <span class="count">(%s)</span>', 'Proposal <span class="count">(%s)</span>', 'woocommerce-quotation' )
        ) );
        
        register_post_status( 'wc-proposal-sent', array(
            'label' => _x('Proposal Sent', 'Order status', 'woocommerce-quotation'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Proposal sent <span class="count">(%s)</span>', 'Proposal sent <span class="count">(%s)</span>', 'woocommerce-quotation' )
        ) );

        register_post_status( 'wc-proposal-expired', array(
            'label' => _x('Proposal expired', 'Order status', 'woocommerce-quotation'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Proposal expired <span class="count">(%s)</span>', 'Proposal expired <span class="count">(%s)</span>', 'woocommerce-quotation' )
        ) );

        register_post_status( 'wc-proposal-rejected', array(
            'label' => _x('Proposal rejected', 'Order status', 'woocommerce-quotation'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Proposal rejected <span class="count">(%s)</span>', 'Proposal rejected <span class="count">(%s)</span>', 'woocommerce-quotation' )
        ) );

        register_post_status( 'wc-proposal-canceled', array(
            'label' => _x('Proposal canceled', 'Order status', 'woocommerce-quotation'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Proposal canceled <span class="count">(%s)</span>', 'Proposal canceled <span class="count">(%s)</span>', 'woocommerce-quotation' )
        ) );
        
        register_post_status( 'wc-proposal-accepted', array(
            'label' => _x('Proposal accepted', 'Order status', 'woocommerce-quotation'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Proposal accepted <span class="count">(%s)</span>', 'Proposal accepted <span class="count">(%s)</span>', 'woocommerce-quotation' )
        ) );
    }
    add_action( 'init', 'adq_register_quote_request_order_status' );
}

/*
 *  Add to list of WC Order statuses
 */
if( !function_exists( 'adq_add_quote_request_order_statuses' ) ) {
    function adq_add_quote_request_order_statuses( $order_statuses ) {
        //Do not add if we are looking on my account
        if ( is_page( wc_get_page_id( 'myaccount' ) ) ) {
                return $order_statuses;
        }
        
        $new_order_statuses = array();
        // add new order status after processing
        foreach ( $order_statuses as $key => $status ) {
            $new_order_statuses[ $key ] = $status;
            if ( 'wc-pending' === $key ) {
                $new_order_statuses['wc-request'] = _x('Request', 'Order status', 'woocommerce-quotation');
                $new_order_statuses['wc-proposal'] = _x('Proposal', 'Order status', 'woocommerce-quotation');
                $new_order_statuses['wc-proposal-sent'] = _x('Proposal sent', 'Order status', 'woocommerce-quotation');
                $new_order_statuses['wc-proposal-expired'] = _x('Proposal expired', 'Order status', 'woocommerce-quotation');
                $new_order_statuses['wc-proposal-rejected'] = _x('Proposal rejected', 'Order status', 'woocommerce-quotation');
                $new_order_statuses['wc-proposal-canceled'] = _x('Proposal canceled', 'Order status', 'woocommerce-quotation');
                $new_order_statuses['wc-proposal-accepted'] = _x('Proposal accepted', 'Order status', 'woocommerce-quotation');                
            }
        }
        return $new_order_statuses;
    }
    add_filter( 'wc_order_statuses', 'adq_add_quote_request_order_statuses' ); 
}


if( !function_exists( 'adq_get_order_statuses' ) ) {
    function adq_get_order_statuses () {
            $new_order_statuses = array();
            
            $new_order_statuses['wc-request'] = __('Request', 'woocommerce-quotation');
            $new_order_statuses['wc-proposal'] = __('Proposal', 'woocommerce-quotation');
            $new_order_statuses['wc-proposal-sent'] = __('Proposal sent', 'woocommerce-quotation');
            $new_order_statuses['wc-proposal-expired'] = __('Proposal expired', 'woocommerce-quotation');
            $new_order_statuses['wc-proposal-rejected'] = __('Proposal rejected', 'woocommerce-quotation');
            $new_order_statuses['wc-proposal-canceled'] = __('Proposal canceled', 'woocommerce-quotation');
            $new_order_statuses['wc-proposal-accepted'] = __('Proposal accepted', 'woocommerce-quotation');
            
            return $new_order_statuses;
    }
}

if( !function_exists( 'adq_get_order_status_name' ) ) {
    function adq_get_order_status_name( $status ) {
            $statuses = wc_get_order_statuses();
            $statuses = array_merge( $statuses, adq_get_order_statuses() );
            $status   = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
            $status   = isset( $statuses[ 'wc-' . $status ] ) ? $statuses[ 'wc-' . $status ] : $status;

            return $status;
    }
}

/*
 * Override if our Quote Request Order needs shipping
 */
if( !function_exists( 'adq_check_shipping' ) ) {
    function adq_check_shipping($needs_shipping) {        
        
        if(isset($_POST["adq_quote_place_order"])) {
            $adq_inherit_shipping_conf = get_option("adq_inherit_shipping_conf");
            $adq_enable_shipping = get_option("adq_enable_shipping");
            
            if ( $adq_inherit_shipping_conf == "no" && $adq_enable_shipping == "no" ) {
                    return false;
            }
        
            $adq_shipping_method = isset( $_POST['adq_shipping_method'] ) ? TRUE : FALSE;
            
            if(!$adq_shipping_method) {
                   return false;
            }
        }
        
        return $needs_shipping;
    }
    add_filter('woocommerce_cart_needs_shipping', 'adq_check_shipping');
}

/*
 * Override if our Quote Request Order needs shipping
 */
if( !function_exists( 'adq_check_payment' ) ) {
    function adq_check_payment($needs_payment) {
        if(isset($_POST["adq_quote_place_order"])) {
                return false;
        }
        
        return $needs_payment;
    }
    add_filter('woocommerce_cart_needs_payment', 'adq_check_payment');
}

/*
 * Override default Order Status
 */
if( !function_exists( 'adq_default_order_status' ) ) {
    function adq_default_order_status($default_status) {
        if(isset($_POST["adq_quote_place_order"])) {
                return 'request';
        }
        
        return $default_status;
    }
    add_filter('woocommerce_default_order_status', 'adq_default_order_status');
}


/*
 * Override Redirect With No payment to end the POST Process
 */
if( !function_exists( 'adq_no_payment_needed_redirect' ) ) {
    function adq_no_payment_needed_redirect($default_redirect) {
        
        if(isset($_POST["adq_quote_place_order"])) {
                WC_Adq()->order->post_process_create_order();
            
                return StaticAdqQuoteRequest::redirect_after_quote_list();
        }
        
        return $default_redirect;
    }
    add_filter('woocommerce_checkout_no_payment_needed_redirect', 'adq_no_payment_needed_redirect');
}


/*
 * Add meta informations on products in order
 */
if( !function_exists( 'adq_add_order_item_meta' ) ) {
    function adq_add_order_item_meta( $item_id, $values ) {

        if( isset($values['_product_note'] ) && $values['_product_note'] ){
            
                wc_add_order_item_meta( $item_id, __('Product Note', 'woocommerce-quotation'), $values['_product_note'] );                
        }
        
    }
    add_action('woocommerce_add_order_item_meta', 'adq_add_order_item_meta', 10, 2);
}


/*
 * Add meta informations on products in order
 */
if( !function_exists( 'adq_automatic_creation' ) ) {
    function adq_automatic_creation( $order_id ) {        
        if ( get_option("adq_automatic_proposal") == "yes" ) {
                $order = wc_get_order( $order_id );
                $order->update_status( "proposal-sent" );
        }        
    }
    add_action('adq_after_quote_email_send', 'adq_automatic_creation');
}



/*
 * Catch Order Id
 */
if( !function_exists( 'adq_set_order_id' ) ) {
    function adq_set_order_id($order_id) {
        
        if(isset($_POST["adq_quote_place_order"])) {
                WC_Adq()->order->order_id = $order_id;
        }
    }
    add_action('woocommerce_checkout_order_processed', 'adq_set_order_id');
}

/*
 * Check our status if is editable
 */
if( !function_exists( 'adq_is_editable' ) ) {
    function adq_is_editable($editable, $order) {
        if( !$editable) {
                $editable = in_array( $order->get_status(), array( 'request', 'proposal', 'proposal-sent' ) );
        }
        
        return $editable;
    }
    add_filter('wc_order_is_editable', 'adq_is_editable', 10, 2);
}

/*
 * Execute actions on status change
*/
if( !function_exists( 'adq_order_status_changed' ) ) {
    function adq_order_status_changed ($post_id, $old_status, $new_status) {  

            if( $new_status == 'proposal-sent' ) {
                   do_action( 'adq_email_customer_proposal', $post_id );
            }
            
            if( $new_status == 'proposal-accepted' ) {                    

                    $order_id = get_post_meta( $post_id, '_order_id', true );
                    if ( !$order_id || $order_id == ""  ) {
                        
                            $order = wc_get_order( $post_id );
                            //unset ID
                            $vars = get_object_vars($order->post);
                            unset($vars['ID']);
                            unset($vars['guid']);
                            $new_order_id = wp_insert_post( $vars  , true ); 
                            $new_order = wc_get_order( $new_order_id );
                            
                            $billing_address = array(
                                    'first_name'    => $order->billing_first_name,
                                    'last_name'     => $order->billing_last_name,
                                    'company'       => $order->billing_company,
                                    'address_1'     => $order->billing_address_1,
                                    'address_2'     => $order->billing_address_2,
                                    'city'          => $order->billing_city,
                                    'state'         => $order->billing_state,
                                    'postcode'      => $order->billing_postcode,
                                    'country'       => $order->billing_country,
                            );
                            $new_order->set_address( $billing_address, 'billing' );
                            
                            $shipping_address =  array(
                                    'first_name'    => $order->shipping_first_name,
                                    'last_name'     => $order->shipping_last_name,
                                    'company'       => $order->shipping_company,
                                    'address_1'     => $order->shipping_address_1,
                                    'address_2'     => $order->shipping_address_2,
                                    'city'          => $order->shipping_city,
                                    'state'         => $order->shipping_state,
                                    'postcode'      => $order->shipping_postcode,
                                    'country'       => $order->shipping_country
                            );
                            $new_order->set_address( $shipping_address, 'shipping' );
                            
                            update_post_meta( $new_order_id, '_customer_user', $order->get_user_id() );
                            update_post_meta( $new_order_id, '_billing_email', get_post_meta( $order->id, '_billing_email', true ) );            
                            
                            $order_items = $order->get_items( array( 'line_item', 'fee', 'shipping' ) );

                            foreach ( $order_items as $new_order_item_id => $new_order_item ) {

                                    // Prevents errors when the order has no taxes
                                    if ( ! isset( $new_order_item['refund_tax'] ) ) {
                                            $new_order_item['refund_tax'] = array();
                                    }
                                    
                                    switch ( $order_items[ $new_order_item_id ]['type'] ) {
                                            case 'line_item' :
                                                    $_product = wc_get_product( $new_order_item['variation_id'] ? $new_order_item['variation_id'] : $new_order_item['product_id'] );
                                                    $line_item_args = array(
                                                            'totals' => array(
                                                                    'subtotal'     => $new_order_item['line_subtotal'],
                                                                    'total'        => $new_order_item['line_total'],
                                                                    'subtotal_tax' => $new_order_item['line_subtotal_tax'],
                                                                    'tax'          => $new_order_item['line_tax'],
                                                                    'tax_data'     => maybe_unserialize($new_order_item['line_tax_data']),
                                                            )
                                                    );
                                                   
                                                    if ( $_product->product_type == "variation"  ) {
                                                            $line_item_args['variation'] = $_product->get_variation_attributes();
                                                    }
                                                    
                                                    $new_item_id = $new_order->add_product( $_product, isset( $new_order_item['qty'] ) ? $new_order_item['qty'] : 0, $line_item_args );
                                                    
                                                    //Add Meta Values
                                                    $cart_item_data = apply_filters( 'woocommerce_order_again_cart_item_data', array(), $new_order_item, $order );
                                                    if( is_array( $cart_item_data ) && count( $cart_item_data ) > 0 ) {
                                                            $new_order_item['data'] = $_product;
                                                            do_action( 'woocommerce_add_order_item_meta', $new_item_id, array_merge($new_order_item, $cart_item_data) );
                                                    }                                                   
                                                    
                                            break;
                                            case 'shipping' :
                                                    $shipping        = new stdClass();
                                                    $shipping->label = $new_order_item['name'];
                                                    $shipping->id    = $new_order_item['method_id'];
                                                    $shipping->cost  = $new_order_item['cost'];
                                                    $shipping->taxes = $new_order_item['taxes'];                                                     

                                                    $new_item_id = $new_order->add_shipping( $shipping );                                                    
                                            break;
                                            case 'fee' :
                                                    $fee            = new stdClass();
                                                    $fee->name      = $new_order_item['name'];
                                                    $fee->tax_class = $new_order_item['tax_class'];
                                                    $fee->taxable   = $fee->tax_class !== '0';
                                                    $fee->amount    = $new_order_item['line_total'];
                                                    $fee->tax       = $new_order_item['line_tax'];
                                                    $fee->tax_data  = maybe_unserialize( $new_order_item['line_tax_data'] );

                                                    $new_item_id = $new_order->add_fee( $fee );                                                    
                                            break;
                                    }
                                            
                            }
                            
                            $new_order->update_taxes();
                            
                            $new_order->set_total( $order->get_total_shipping(), 'shipping' );
                            $new_order->set_total( $order->get_total_discount(), 'cart_discount' );
                            $new_order->set_total( $order->get_total_discount(false), 'cart_discount_tax' );
                            $new_order->set_total( $order->get_total_tax(), 'tax' );
                            $new_order->set_total( $order->get_shipping_tax(), 'shipping_tax' );
                            $new_order->set_total( $order->get_total() );
                            
                            $new_order->update_status( 'wc-pending' );                    

                            update_post_meta( $post_id, '_order_id', $new_order->id );
                            update_post_meta( $new_order->id, '_quotation_id', $post_id );

                            do_action( 'adq_email_customer_proposal_ok', $post_id );
                            do_action( 'adq_email_admin_proposal_ok', $post_id );
                    }                           
            }
            
            if( $new_status == 'proposal-rejected' ) {
                   do_action( 'adq_email_customer_proposal_ko', $post_id );
                   do_action( 'adq_email_admin_proposal_ko', $post_id );
            }                        
    }

    add_action( 'woocommerce_order_status_changed','adq_order_status_changed', 10, 3);
}


/*
 * Rewrite line subtotals if is necessary
 */
if( !function_exists( 'adq_line_subtotal' ) ) {
    function adq_line_subtotal( $price, $order, $item ) {
        $adq_display_discount = get_option('adq_display_discount');
        
        $adq_statuses = adq_get_order_statuses();
        $_quotation_id = get_post_meta( $order->id, '_quotation_id', true );
        
        if( $adq_display_discount == "no" && 
                ( array_key_exists( "wc-".$order->get_status(), $adq_statuses ) || ( $_quotation_id && $_quotation_id != "" ) ) ) {
                $price = $item['line_total'];
                
                $price = round( $price, 2 );
        }
        
        return $price;
    }
    add_filter('woocommerce_order_amount_line_subtotal', 'adq_line_subtotal', 10, 3);
}


/*
 * Don't show discount if is necesssary
 */
if( !function_exists( 'adq_total_rows' ) ) {
    function adq_total_rows( $total_rows, $order ) {
        $adq_display_discount = get_option('adq_display_discount');
        
        $adq_statuses = adq_get_order_statuses();
        $_quotation_id = get_post_meta( $order->id, '_quotation_id', true );        
        
        if( array_key_exists( "wc-".$order->get_status(), $adq_statuses )  || ( $_quotation_id && $_quotation_id != "" ) ) {
                unset($total_rows['cart_subtotal']); 
                if( $adq_display_discount == "no" ) {                
                        unset($total_rows['discount']);                        
                }
        }
        
        return $total_rows;
    }
    add_filter('woocommerce_get_order_item_totals', 'adq_total_rows', 10, 5);
}

/*
 * Don't show discount if is necesssary
 */
if( !function_exists( 'adq_checkout_show_terms' ) ) {
    function adq_checkout_show_terms( $return ) {
        $adq_show_terms = get_option('adq_show_terms');
        
        if( $adq_show_terms == "no" ) {                            
                return false;                        
        }
        
        return $return;
    }
    add_filter('adq_checkout_show_terms', 'adq_checkout_show_terms', 10, 1);
}
