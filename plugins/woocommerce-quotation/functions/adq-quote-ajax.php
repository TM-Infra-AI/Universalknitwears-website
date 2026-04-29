<?php
/**
 * Handle Ajax functions
 *
 * @version     2.0.0
 * @package     woocommerce-quotation/functions/
 * @category    Functions
 * @author      Aldaba Digital
 */

if( !function_exists( 'adq_ajax_add_to_quote' ) ) {
    /*
     *  Add product to Quote List With Variables
     * 
     * @return array (1 => ok, 2 => duplicate, 0 => fail)
    */
    function adq_ajax_add_to_quote() {          

        if( get_option('adq_ajax_error_tolerance', 'yes') == "yes" ) {
               set_error_handler( "adq_error_handler" );
        }        
        
        do_action('adq_before_ajax_add');
        
        $product_id = 0;
        if(isset($_POST['product_id']))
            $product_id = sanitize_key( $_POST['product_id'] );
        
        $variation_id = false;
        if(isset($_POST['variation_id']))
            $variation_id = sanitize_key( $_POST['variation_id'] );
        
        $product_type = 'simple';
        if(isset($_POST['product_type']))
            $product_type = sanitize_key( $_POST['product_type'] );
        
        $response = 0;
        
        $all_variations_set = true;
        $was_added_to_cart = false;
        $variations = array();   

        if( $product_type === "grouped") {   
                if(isset($_POST['quantity'])) {                        

                        foreach ( $_POST['quantity'] as $product_id => $quantity ) {   
                                $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity);

                                if($passed_validation && (int)$quantity > 0) { 
                                    
                                        $response = WC_Adq()->quote->add_to_quote($product_id, $quantity);                                    
                                }

                                if( $response ) {
                                    $was_added_to_cart = true;                            
                                }                        
                        }
                }

                if ( ! $was_added_to_cart ) {
                        adq_add_notice( __('Please choose the quantity of items you wish to add to your quote&hellip;', 'woocommerce-quotation' ), 'error' ); 
                        $response = 0;
                }
        }
        elseif ( $product_type === 'variable' ) {
                if(isset($_POST['quantity']))
                        $quantity = $_POST['quantity'];
                else
                        $quantity = 1;

                $adding_to_cart = wc_get_product( $product_id );
                $attributes = $adding_to_cart->get_attributes();
                $missing_attributes = array();
                $variations         = array();
                if( $variation_id ) {
                    $variation  = wc_get_product( $variation_id );  
                }

                // Verify all attributes
                foreach ( $attributes as $attribute ) {
                        if ( ! $attribute['is_variation'] ) {
                                continue;
                        }

                        $taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

                        if ( isset( $_REQUEST[ $taxonomy ] ) ) {

                                // Get value from post data
                                if ( $attribute['is_taxonomy'] ) {
                                        // Don't use wc_clean as it destroys sanitized characters
                                        $value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
                                } else {
                                        $value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );
                                }

                                // Get valid value from variation
                                $valid_value = $variation->variation_data[ $taxonomy ];

                                // Allow if valid
                                if ( '' === $valid_value || $valid_value === $value ) {
                                        $variations[ $taxonomy ] = $value;
                                        continue;
                                }

                        } else {
                                $missing_attributes[] = wc_attribute_label( $attribute['name'] );
                        }
                }

                if ( $missing_attributes ) {
                        wc_add_notice( sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ), 'error' );
                        return;
                } elseif ( empty( $variation_id ) ) {
                        wc_add_notice( __( 'Please choose product options&hellip;', 'woocommerce' ), 'error' );
                        return;
                } else {
                        // Add to cart validation
                        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

                        if ( $passed_validation ) {
                                $response = WC_Adq()->quote->add_to_quote($product_id, $quantity, $variation_id, $variations);                                

                                if( !$response )  {
                                    $message = StaticAdqQuoteRequest::get_error_notice($product_id, true);            
                                    adq_add_notice( $message, 'error' ); 
                                }
                        }
                }
                
        }
        else {
                $quantity = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );

                // Add to cart validation
                $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

                if ( $passed_validation ) {                       
                        $response = WC_Adq()->quote->add_to_quote( $product_id, $quantity );
                        if( !$response )  {
                                $message = StaticAdqQuoteRequest::get_error_notice($product_id, true);            
                                adq_add_notice( $message, 'error' ); 
                        }
                }
        }
        
        $messages = '';
        if( $response && get_option('adq_redirect_quote_page') === "show" ) {
                wc_clear_notices();

                $messages = sprintf(
                        '<a href="%s" class="added_to_quote wc-forward">%s</a>',
                        StaticAdqQuoteRequest::get_quote_list_link(),
                        __( 'View Quote', 'woocommerce-quotation' )                        
                );
        }

        echo json_encode(array( "result" => $response, 'message' => $messages ));

        die;
    }
    add_action( 'wp_ajax_add_to_quote', 'adq_ajax_add_to_quote' );
    add_action( 'wp_ajax_nopriv_add_to_quote', 'adq_ajax_add_to_quote' );
}

if( !function_exists( 'adq_ajax_remove_from_list' ) ) {
    /*
     *  Remove product from Quote List
     * 
     * @return array (1 => ok, 2 => duplicate, 0 => fail)
    */
    function adq_ajax_remove_from_list() {  
        
        do_action('adq_before_ajax_add');

        $cart_item_key = sanitize_key( $_POST['cart_item_key'] );        
        $product_id = sanitize_key( $_POST['product_id'] ); 
        
        $response = WC_Adq()->quote->remove_quote_item( $cart_item_key );
        
        if( $response ) {
            $message = sprintf( __( '&quot;%s&quot; was successfully removed from list.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
            adq_add_notice( $message );
        }
        else {
            $message = sprintf( __( '&quot;%s&quot; cannot be removed from list.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
            adq_add_notice( $message, 'error' ); 
        }

        echo json_encode(array("result" => (int)$response));

        die;
    }
    add_action( 'wp_ajax_remove_from_list', 'adq_ajax_remove_from_list' );
    add_action( 'wp_ajax_nopriv_remove_from_list', 'adq_ajax_remove_from_list' );
}

if( !function_exists( 'adq_ajax_remove_all_list' ) ) {
    /*
     *  Remove All product from Quote List
     * 
     * @return array (1 => ok, 2 => duplicate, 0 => fail)
    */
    function adq_ajax_remove_all_list() {  
        
        $response = WC_Adq()->quote->remove_all_quote_item();
        
        if( $response ) {
            $message = sprintf( __( 'All items was successfully removed from list.', 'woocommerce-quotation' ) );
            adq_add_notice( $message );
        }
        else {
            $message = sprintf( __( 'Items cannot be removed from list.', 'woocommerce-quotation' ) );
            adq_add_notice( $message, 'error' ); 
        }

        echo json_encode(array("result" => (int)$response));

        die;
    }
    add_action( 'wp_ajax_remove_all_list', 'adq_ajax_remove_all_list' );
    add_action( 'wp_ajax_nopriv_remove_all_list', 'adq_ajax_remove_all_list' );
}


if( !function_exists( 'adq_ajax_add_quantity' ) ) {
    /*
     *  Remove product from Quote List
     * 
     * @return array (1 => ok, 2 => duplicate, 0 => fail)
    */
    function adq_ajax_add_quantity() {

        $cart_item_key = sanitize_key( $_POST['cart_item_key'] );
        $product_id = sanitize_key( $_POST['product_id'] );
        $quantity = sanitize_key( $_POST['quantity'] );
        
        $response = WC_Adq()->quote->update_quantity($cart_item_key, $quantity);                
        
        if( $response ) {
            $message = sprintf( __( '&quot;%s&quot; updated.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
            adq_add_notice( $message );
        }
        else {
            $message = sprintf( __( '&quot;%s&quot; cannot be updated.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
            adq_add_notice( $message, 'error' ); 
        }

        echo json_encode(array("result" => $response));

        die;
    }
    add_action( 'wp_ajax_add_quantity', 'adq_ajax_add_quantity' );
    add_action( 'wp_ajax_nopriv_add_quantity', 'adq_ajax_add_quantity' );
}


if( !function_exists( 'adq_add_quote_item_meta' ) ) {
        /**
	 * Add some meta to a line item
	 */
	function adq_add_quote_item_meta() {
                
                $cart_item_key = sanitize_key( $_POST['cart_item_key'] );
                $meta_key = '_product_note';
                $meta_value = stripslashes ( urldecode ( $_POST['meta_value'] ) );        
                
                WC_Adq()->quote->update_meta_quote_item( $cart_item_key, $meta_key , $meta_value );                
                
		die;
	}
        add_action( 'wp_ajax_add_quote_item_meta', 'adq_add_quote_item_meta' );
        add_action( 'wp_ajax_nopriv_add_quote_item_meta', 'adq_add_quote_item_meta' );
}

if( !function_exists( 'adq_add_order_note' ) ) {

        /**
        * Add order note via ajax
        */
        function adq_add_order_note() {
            
               include_once (ADQ_QUOTE_DIR.'classes/class.adq.order.php'); 
               
               $post_id   = absint( $_POST['post_id'] );
               $note      = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
               $note_type = $_POST['note_type'];

               $is_customer_note = $note_type == 'customer' ? 1 : 0;

               if ( $post_id > 0 ) {
                       $order      = new ADQ_Order( $post_id );
                       //$order      = wc_get_order( $post_id );
                       $comment_id = $order->add_order_note( $note, $is_customer_note );

                       echo '<li rel="' . esc_attr( $comment_id ) . '" class="note ';
                       if ( $is_customer_note ) {
                               echo 'customer-note';
                       }
                       echo '"><div class="note_content">';
                       echo wpautop( wptexturize( $note ) );
                       echo '</div><p class="meta"><a href="#" class="delete_note">'.__( 'Delete note', 'woocommerce-quotation' ).'</a></p>';
                       echo '</li>';
               }

               // Quit out
               die();
        }
        add_action( 'wp_ajax_adq_add_order_note', 'adq_add_order_note' );
        add_action( 'wp_ajax_nopriv_adq_add_order_note', 'adq_add_order_note' );
}


if( !function_exists( 'adq_get_refreshed_fragments' ) ) {
        /**
	 * Get a refreshed Quote fragment
	 */
	function adq_get_refreshed_fragments() {

		// Get mini cart
		ob_start();

		adq_mini_cart();

		$mini_cart = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
					'div.widget_shopping_quote_list_content' => '<div class="widget_shopping_quote_list_content">' . $mini_cart . '</div>'
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
		);

		wp_send_json( $data );

	}
}


if( !function_exists( 'adq_error_handler' ) ) {
        function adq_error_handler ($errno, $errstr, $errfile, $errline) {

                //Do nothing

                return true;
        }
}

?>