<?php
/**
 * Handle Front functions
 *
 * @version     1.0.0
 * @package     woocommerce-quotation/functions/
 * @category    Functions
 * @author      Aldaba Digital
 */

/*
 *  Add Quote Button on Product Page
*/
if( !function_exists( 'adq_add_to_quote' ) ) {
    function adq_add_to_quote( $atts, $content = null ) {
        global $product;

        $html = '';
        if( $product->product_type == 'grouped' ) {
                $grouped_products = $product->get_children();
                
                if( count($grouped_products) > 0 ) {
                    if( is_quotable( $product ) && $product->is_in_stock()) {
                        $html = StaticAdqQuoteRequest::generate_quote_button( $product );
                    }
                }
        }
        else {                            
                if( is_quotable( $product ) && $product->is_in_stock()) {
                        $html = StaticAdqQuoteRequest::generate_quote_button( $product );
                }
        }        
        return apply_filters( 'woocommerce_adq_add_to_quote', $html, $product );
    }
    add_shortcode( 'adq_button', 'adq_add_to_quote');
        
}

if( !function_exists( 'adq_single_variation_shortcode' ) ) {
    function adq_single_variation_shortcode() {
        remove_action("woocommerce_after_add_to_cart_button", "adq_single_shortcode", 2);
        adq_single_shortcode();
    }
}

if( !function_exists( 'adq_single_shortcode' ) ) {
    function adq_single_shortcode() {
        echo do_shortcode( "[adq_button]" );
    }
}

add_action( 'woocommerce_after_add_to_cart_button', 'adq_single_shortcode', 2 );
add_action( 'woocommerce_after_single_variation', 'adq_single_variation_shortcode', 2 );


/*
 *  Add Quote Button on Product Loop Page
*/
if( !function_exists( 'adq_add_to_quote_loop' ) ) {
    function adq_add_to_quote_loop( $atts, $content = null ) {
        global $product;

        $html = '';
        
        if( is_quotable( $product ) && $product->is_in_stock()) {
            if($product->product_type == "simple") {       
                $html = StaticAdqQuoteRequest::generate_quote_button_loop( $product );
            }
        }

        return $html;
    }
    add_shortcode( 'adq_button_loop', 'adq_add_to_quote_loop');
}

if( !function_exists( 'adq_is_purchasable' ) ) {
    function adq_is_purchasable ( $purchasable, $product ) {

            //Only to avoid template exits       
            if ( did_action('woocommerce_single_product_summary') && !did_action('woocommerce_before_add_to_cart_button') ) {                
                    return is_quotable( $product );
            }

            return $purchasable;
    }
    add_filter('woocommerce_is_purchasable', 'adq_is_purchasable', 10, 2);
}

/*
 *  Prepare to add Quote Button on Header and process POST forms
 */
if( !function_exists( 'adq_add_quote_button' ) ) {
    function adq_add_quote_button() {
        global $post;
            
        $_positions = array(
            'add-to-cart' => array( 'hook' => 'woocommerce_single_product_summary', 'priority' => 31 ),
            'thumbnails'  => array( 'hook' => 'woocommerce_product_thumbnails', 'priority' => 21 ),
            'summary'     => array( 'hook' => 'woocommerce_after_single_product_summary', 'priority' => 11 ),
            'product-loop'=> array( 'hook' => 'woocommerce_after_shop_loop_item', 'priority' => 11 ),
        );

        if ( ! isset( $post ) || ! is_object( $post ) ) {
            return;
        }       
       
        // Add the link "Add to quote afer add to cart button";        
        $position = 'add-to-cart';    
        //add_action( $_positions[$position]['hook'], create_function( '', 'echo do_shortcode( "[adq_button]" );' ), $_positions[$position]['priority'] );

        // Add the link "Add to quote afer add to cart button on product loop";
        $position = 'product-loop';                       
        add_action( $_positions[$position]['hook'], create_function( '', 'echo do_shortcode( "[adq_button_loop]" );' ), $_positions[$position]['priority'] );               
    }
    add_action( 'wp_head', 'adq_add_quote_button', 1 );
}


/*
 * Add scripts to header
 */
if( !function_exists( 'adq_header_static_variables' ) ) {
    function adq_header_static_variables() {
            wp_enqueue_style( 'quote-request-style', ADQ_QUOTE_URL . 'assets/css/style.css' );

            wp_register_script( 'jquote-request-js', ADQ_QUOTE_URL . 'assets/js/functions.js', array( 'jquery' ), '1.0', true );       
            wp_enqueue_script( 'jquote-request-js' );
            $params = array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'redirectUrl' => StaticAdqQuoteRequest::must_redirect(),
                'add_order_note_nonce' => wp_create_nonce( 'add-order-note' ),
            );
            wp_localize_script( 'jquote-request-js', 'adqAjax', $params );                           
    }    
    add_action( 'wp_enqueue_scripts', 'adq_header_static_variables' );
}


/*
 * Add Shortcode list of Quoted Product and Submit Form
 */
if( !function_exists( 'adq_draw_quote_product_list' ) ) {
    function adq_draw_quote_product_list() { 
        
        include_once (ADQ_QUOTE_DIR.'classes/class.adq.shipping.php');        
        
        $shipping = new ADQ_Shipping();              
        
        // Woocommmerce JS 
        wp_enqueue_script( 'wc-country-select' );
        wp_enqueue_script( 'wc-address-i18n' );
        
        if( count( WC_Adq()->quote->get_quote() ) > 0 ) { ?>
            <div class="woocommerce">
                <?php
                
                do_action('adq_quote_list_before');
                
                //List of quoted Products
                adq_get_template( 'adq-list.php' );

                // Login and Register Form 
                if ( ! is_user_logged_in() && get_option("adq_enable_login_reminder") == "yes") { 
                    adq_get_template( 'adq-form-login.php',  array( 'redirect_id' =>  get_query_var('page_id') ) );                			
                }
                
                ?>
                
                <form class="checkout adq-billing" enctype="multipart/form-data" action="<?php echo StaticAdqQuoteRequest::get_quote_list_link() ?>" method="post" name="checkout">
                    <div class="col2-set">
                        <div class="col-1">
                <?php
                        //Billing/Account information
                        //, 'is_billing_filled' => $is_billing_filled
                        adq_get_template( 'adq-form-billing-details.php', array( 'checkout' => StaticAdqQuoteRequest::get_checkout() ) );

                        do_action('adq_quote_list_after') ?>

                        </div>

                        <div class="col-2">
                
                <?php

                        //Force enabled to avoid core Woocommerce system
                        $shipping->enabled = StaticAdqQuoteRequest::is_shipping_enabled();  
                        //Get Shipping options and address
                        $shipping->calculate_shipping( WC_Adq()->quote->get_shipping_packages() );                                              
                        $packages = $shipping->get_packages();                         
                        
                        foreach ( $packages as $i => $package ) {
                                $chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';

                                adq_get_template( 'adq-cart-shipping.php', array( 'package' => $package, 'available_methods' => $package['rates'], 'show_package_details' => ( sizeof( $packages ) > 1 ), 'index' => $i, 'chosen_method' => $chosen_method ) );
                        }                        
                    
                ?>

                        <p id="quote_comments_field" class="form-row notes woocommerce-validated">
                                <label class="" for="order_comments"><?php echo __('Leave a Comment','woocommerce-quotation') ?></label>
                                <textarea cols="5" rows="2" placeholder="<?php echo __('Notes about your Quote Request.','woocommerce-quotation') ?>" id="order_comments" class="input-text" name="order_comments"></textarea>
                        </p>
                        </div>

                    </div>

                    <?php if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'adq_checkout_show_terms', true ) ) : ?>
                            <p class="form-row terms">
                                    <label for="terms" class="checkbox"><?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'woocommerce-quotation' ), esc_url( get_permalink( wc_get_page_id( 'terms' ) ) ) ); ?></label>
                                    <input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
                            </p>
                    <?php endif; ?>
                            
                    
                    
                    <input type="submit" data-value="<?php echo __('Submit Quote Request','woocommerce-quotation') ?>" value="<?php echo __('Submit Quote Request','woocommerce-quotation') ?>" id="quote_place_order" name="adq_quote_place_order" class="button alt">                    
                </form>
            </div>
        <?php
        }
        else {
            adq_get_template( 'adq-empty.php' );
        }
    }
    add_filter('widget_text', 'do_shortcode');
    add_shortcode( 'quote_request_list', 'adq_draw_quote_product_list');
}


/*
 * Add comments to order detail
 */
if( !function_exists( 'adq_order_notes' ) ) {
    function adq_order_notes( $order_id ) { 
            if ( ! $order_id ) return;
            
            if ( get_option('adq_allow_order_comments') == "yes" ) {
                    adq_get_template( 'adq-order-notes.php', array( 'order_id' => $order_id ) );
            }
    }
    add_action( 'woocommerce_view_order', 'adq_order_notes', 5 );
}



/*
 * Add information on Order Details
 */
if( !function_exists( 'adq_order_details' ) ) {
    function adq_order_details( $order_id ) { 
            if ( ! $order_id ) return;
            
            adq_get_template( 'adq-order-detail-quote.php', array( 'order_id' => $order_id ) );                           
    }
    add_action( 'woocommerce_view_order', 'adq_order_details', 6 );
}


/*
 * Add actions on order detail
 */
if( !function_exists( 'adq_my_account_my_orders_actions' ) ) {
    function adq_my_account_my_orders_actions( $actions, $order ) {
        
            if ( $order->get_status() == 'proposal-sent' ) {                    
                    $base_url = StaticAdqQuoteRequest::get_proposal_base_url( $order );
                    
                    $actions['adq-accept'] = array(
                            'url'  => $base_url.'&adq_action=accept',
                            'name' => __('Accept proposal','woocommerce-quotation')
                    );
                    
                    $actions['adq-reject'] = array(
                            'url'  => $base_url.'&adq_action=reject',
                            'name' => __('Reject proposal','woocommerce-quotation')
                    );
            }
            elseif ( $order->get_status() == 'request' ) {
                    $order_key = base64_encode ( get_post_meta( $order->id, '_order_key', true ) );
                    $email = urlencode ( get_post_meta( $order->id, '_billing_email', true ) );
                    
                    $link = get_option( 'permalink_structure' );
                    if($link != "")
                        $nedlee = '?';
                    else 
                        $nedlee = '&';
                    $base_url = get_permalink() . $nedlee . 'order_key='.$order_key.'&email='.$email;
                    
                    $actions['adq-accept'] = array(
                            'url'  => $base_url.'&adq_action=cancel',
                            'name' => __('Cancel','woocommerce-quotation')
                    );  
                    
                    //unset( $actions['view'] );
            }
            elseif ( $order->get_status() == 'proposal' ) {
                    unset( $actions['view'] );
            }
            
            $_order_id = get_post_meta( $order->id, '_order_id', true );            
            $_quotation_id = get_post_meta( $order->id, '_quotation_id', true );
            
            if( $_quotation_id && $_quotation_id != "" ) { 
                    $new_order = wc_get_order( $_quotation_id );
                    $actions['adq-related-quote'] = array(
                            'url'  => $new_order->get_view_order_url(),
                            'name' => __('Related quote','woocommerce-quotation')
                    );
            }
            
            if( $_order_id && $_order_id != "" ) {
                    $new_order = wc_get_order( $_order_id );
                    $actions['adq-related-order'] = array(
                            'url'  => $new_order->get_view_order_url(),
                            'name' => __('Related order','woocommerce-quotation')
                    );
            }
                        
            return $actions;
    }
    add_filter ( 'woocommerce_my_account_my_orders_actions', 'adq_my_account_my_orders_actions', 10, 2);
}

/*
 * Related quote or related order on details
 */
if( !function_exists( 'adq_order_details_table' ) ) {
    function adq_order_details_table( $order_id ) {
        
            $_order_id = get_post_meta( $order_id, '_order_id', true );            
            $_quotation_id = get_post_meta( $order_id, '_quotation_id', true );
            
            if( $_quotation_id && $_quotation_id != "" ) { 
                    $new_order = wc_get_order( $_quotation_id );
                    
                    ?>
                        <a class="button adq-related-order" href="<?php echo $new_order->get_view_order_url() ?>"><?php echo _e('Related quote','woocommerce-quotation') ?></a>
                    <?php
                    
            }
            
            if( $_order_id && $_order_id != "" ) {
                    $new_order = wc_get_order( $_order_id );
                    ?>
                        <a class="button adq-related-order" href="<?php echo $new_order->get_view_order_url() ?>"><?php echo _e('Related order','woocommerce-quotation') ?></a>
                    <?php

            }
    }
    add_action( 'woocommerce_view_order', 'adq_order_details_table', 7 );
}

/*
 * Rewrite order total on my account page
 */
if( !function_exists( 'adq_my_account_order_total' ) ) {
    function adq_my_account_order_total ($formatted_total, $order) {
        
            if ( ( $order->get_status() == 'proposal' || $order->get_status() == 'request' ) ) {
                    //&& !StaticAdqQuoteRequest::can_show_price() ) {
                    return __( 'Not yet proposed', 'woocommerce-quotation' );
            }
            
            return $formatted_total;
    }
    add_filter ( 'woocommerce_get_formatted_order_total', 'adq_my_account_order_total', 10, 2);
}


/*
 * Rewrite order total on my account page
 */
if( !function_exists( 'adq_formatted_line_subtotal' ) ) {
    function adq_formatted_line_subtotal ($subtotal, $item, $order) {
        
            $_product = $order->get_product_from_item( $item );
            
            if ( ( $order->get_status() == 'proposal' || $order->get_status() == 'request' ) 
                    && !StaticAdqQuoteRequest::can_show_price( $_product ) ) {
                    return __( 'Not yet proposed', 'woocommerce-quotation' );
            }
            
            return $subtotal;
    }
    add_filter ( 'woocommerce_order_formatted_line_subtotal', 'adq_formatted_line_subtotal', 10, 3);
}


/*
 * Rewrite order frontoffice url
 */
if( !function_exists( 'adq_override_order_url' ) ) {
    function adq_override_order_url ( $view_order_url, $order ) {
        
            /*if ( $order->get_status() == 'proposal' || $order->get_status() == 'request' ) {
                    return '#';
            }*/
            
            return $view_order_url;
    }
    add_filter ( 'woocommerce_get_view_order_url', 'adq_override_order_url', 10, 2);
}

 
/*
 * Init to check changes
 */
if( !function_exists( 'adq_process_order_actions' ) ) {
    function adq_process_order_actions () {    
        
            if ( isset( $_GET['adq_download_file'] ) && isset( $_GET['order_id'] ) ) {
                    
                    $download_id    = $_GET['adq_download_file'];
                    $order_id       = $_GET['order_id'];
                    
                    $file_path = false;
                    $downloadable_files = get_post_meta( $order_id, '_attached_files', true ); 
                    if ( $downloadable_files ) {
                        foreach ( $downloadable_files as $key => $file ) {
                            if($download_id == $key) {
                                $file_path = $file["file"];
                            }
                        }
                    }
                    
                    StaticAdqQuoteRequest::download( $file_path, $order_id );
            }
            
            if ( isset( $_GET['order_key'] ) && isset( $_GET['email'] ) && isset( $_GET['adq_action'] ) ) {
                
                    global $wpdb;
                    
                    $order_key      = base64_decode( $_GET['order_key'] );
                    $email          = sanitize_email ( urldecode( $_GET['email'] ) );
                    $adq_action     = $_GET['adq_action'];                    
                                       
                    $query = "
                            SELECT post_id
                            FROM " . $wpdb->prefix . "postmeta
                            WHERE meta_key = '_order_key' AND meta_value = %s ";

                    $args = array(
                            $order_key
                    );
                    
                    $result = $wpdb->get_row( $wpdb->prepare( $query, $args ) );
                    
                    $query = "
                            SELECT post_id
                            FROM " . $wpdb->prefix . "postmeta
                            WHERE meta_key = '_billing_email' AND meta_value = %s 
                            AND post_id = %s ";

                    $args = array(
                            $email,
                            $result->post_id
                    );                   
                    
                    $post_id = $wpdb->get_row( $wpdb->prepare( $query, $args ) );
                    
                    if ( ! $post_id ) {
                            adq_add_notice( __('Cannot change order status', 'woocommerce-quotation'), 'error' );
                    }
                    else {
                            $order = wc_get_order( $post_id->post_id );
                            
                            switch ( $adq_action ) {
                                case 'accept' :
                                    if( $order->get_status() != "proposal-sent" ) {
                                            adq_add_notice( __('This link is currently invalid', 'woocommerce-quotation'), 'error' );
                                            break;
                                    }
                                    
                                    $order->update_status( 'wc-proposal-accepted' );
                                    //$order->update_status( 'wc-pending' ); 
                                    adq_add_notice( __('Quote Request updated', 'woocommerce-quotation') );
                                    $new_order_id = get_post_meta( $order->id, '_order_id', true );
                                    
                                    if( ( get_option('adq_redirect_payment') == "yes" || !$order->get_user() ) && $new_order_id != "") {                                        
                                            $new_order = wc_get_order( $new_order_id );
                                            
                                            wp_redirect( $new_order->get_checkout_payment_url() ); 
                                            exit();
                                    }                                    
                                    break;
                                case 'reject' :
                                    $order->update_status( 'wc-proposal-rejected' );
                                    adq_add_notice( __('Quote Request updated', 'woocommerce-quotation') );
                                    break;
                                case 'cancel' :
                                    $order->update_status( 'wc-proposal-canceled' );
                                    adq_add_notice( __('Quote Request canceled', 'woocommerce-quotation') );
                                    break;
                                default:
                                    adq_add_notice( __('Cannot change order status', 'woocommerce-quotation'), 'error' );
                                    break;
                            }
                    }
            }
    }
    add_action( 'init', 'adq_process_order_actions' );
}

if( !function_exists( 'adq_wp_load' ) ) {
    function adq_wp_load () {                                              
            //POST Process Order
            if(isset($_POST["adq_quote_place_order"])) {
                    WC_Adq()->order->create_order();            
            }
            
            // Add Discount
            if ( ! empty( $_POST['adq_apply_coupon'] ) && ! empty( $_POST['adq_coupon_code'] ) ) {
                    WC_Adq()->quote->add_discount( sanitize_text_field( $_POST['adq_coupon_code'] ) );
            }

            // Remove Coupon Codes
            elseif ( isset( $_GET['adq_remove_coupon'] ) ) {

                    WC_Adq()->quote->remove_coupon( wc_clean( $_GET['adq_remove_coupon'] ) );

            }
            
    }
    add_action( 'wp_loaded', 'adq_wp_load', 20 );
}


/*
 * Override Price show on shop if no have permisions
 */
if( !function_exists( 'adq_show_price' ) ) {
    function adq_show_price($return, $product) {        
        if ( !StaticAdqQuoteRequest::can_show_price( $product ) ) {
            return '';
        }
        
        return $return;
    }
    add_filter('woocommerce_get_price_html', 'adq_show_price', 10, 2);
}

/*
 * Override Price show on shop if no have permisions
 */
if( !function_exists( 'adq_show_price_variation' ) ) {
    function adq_show_price_variation($available_variations, $product, $variation) {        
        if ( !StaticAdqQuoteRequest::can_show_price( $product ) ) {
                $available_variations['display_price'] = '';
                $available_variations['display_regular_price'] = '';
                $available_variations['price_html'] = '';
        }
        
        return $available_variations;
    }
    add_filter('woocommerce_available_variation', 'adq_show_price_variation', 10, 3);
}


/*
 * Override add to cart on shop if no have permisions
 */
if( !function_exists( 'adq_show_cart_button' ) ) {
    function adq_show_cart_button() {
        global $product;
        
        if ( !StaticAdqQuoteRequest::can_show_cart_button( $product ) ) {  
            ob_start();
            
            ?>
                        
            <script>                                        
                    jQuery('.single_add_to_cart_button:not(#add_to_quote)').remove();
            </script>
            
            <?php            
            $script = ob_get_clean();
            
            echo apply_filters( 'woocommerce_adq_show_cart_button', $script, $product );            
        }
    } 
    add_action('woocommerce_after_add_to_cart_button', 'adq_show_cart_button');
}

/*
 * Override add to cart on shop if no have permisions
 */
if( !function_exists( 'adq_show_cart_button_loop' ) ) {
    function adq_show_cart_button_loop() {
        global $product;
        
        if ( !StaticAdqQuoteRequest::can_show_cart_button( $product ) ) {  ?>
            <script>
                    jQuery(".product_type_simple[data-product_id='<?php echo $product->id ?>']").remove();
            </script>
        <?php }
    } 
    add_action('woocommerce_after_shop_loop_item', 'adq_show_cart_button_loop', 100);
}


/*
 * Show shipping costs
 */
if( !function_exists( 'adq_shipping_method_price' ) ) {
    function adq_shipping_method_price( $label, $method) { 
        
        if ( !StaticAdqQuoteRequest::is_quote_list_page() ) {
                return $label;
        }
        
        $adq_hide_shipping_cost = get_option( 'adq_hide_shipping_cost' );
        $shipping_address = WC()->checkout()->get_value( 'shipping_address_1' );
        
        if ( $adq_hide_shipping_cost == "yes" && !$shipping_address ) {
                return $method->label;
        }
        
        return $label;
    }
    add_filter('woocommerce_cart_shipping_method_full_label', 'adq_shipping_method_price', 10, 2);
}


if( !function_exists( 'adq_my_account_quotation_list' ) ) {
    function adq_my_account_quotation_list() { 
            global $order_count;
            
            adq_get_template( 'my-quotation.php', array( 'order_count' => $order_count ) );
    }
    add_action('woocommerce_before_my_account', 'adq_my_account_quotation_list');    
}

if( !function_exists( 'adq_before_template_part' ) ) {
    
    function adq_before_template_part ( $located, $template_name, $args, $template_path, $default_path ) {              
    
            switch ( $template_name ) {
                case 'order/order-details.php' :
                    $order = wc_get_order( $args['order_id'] );
                    
                case 'myaccount/view-order.php':
                    if( !isset($order) ) {
                        $order = $args['order'];
                    }
                    
                    if( !StaticAdqQuoteRequest::is_order_quote_status ( $order->get_status() )) {
                            return $located;
                    }
                
                    $adq_name = explode('/', $template_name);
                    $new_located = wc_locate_template( $adq_name[1], $template_path, ADQ_QUOTE_DIR."templates/" );

                    if ( file_exists( $new_located ) ) {
                            return $new_located;
                    }
                    
                    break;
            }
            
            return $located;
    }
    add_filter( 'wc_get_template','adq_before_template_part', 10, 5); 
}

if( !function_exists( 'adq_page_endpoint_title' ) ) {
        function adq_page_endpoint_title( $title ) {
                global $wp_query, $wp;


                if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && is_wc_endpoint_url() ) {
                        $endpoint = WC()->query->get_current_endpoint();

                        if ( $endpoint == 'view-order') {
                                $order = wc_get_order( $wp->query_vars['view-order'] );
                                
                                if( StaticAdqQuoteRequest::is_order_quote_status ( $order->get_status() ) ) {
                                        $title = ( $order ) ? sprintf( __( 'Quote #%s', 'woocommerce-quotation' ), $order->get_order_number() ) : '';

                                        remove_filter( 'the_title', 'wc_page_endpoint_title' );
                                        remove_filter( 'the_title', 'adq_page_endpoint_title' );
                                }
                        }                        
                }

                return $title;
        }

        add_filter( 'the_title', 'adq_page_endpoint_title', 1 );
}

/*
 *  Cart Coupons Compatibility
 */
if( !function_exists( 'adq_coupon_get_discount_amount' ) ) {
    function adq_coupon_get_discount_amount ($discount, $discounting_amount, $cart_item, $single, $coupon ) {
        
        if( !StaticAdqQuoteRequest::is_quote_list_page () ) {
            return $discount;
        }        
        
        $discount = 0;

        if ( $coupon->is_type( 'fixed_product' ) ) {

                $discount = $discounting_amount < $coupon->coupon_amount ? $discounting_amount : $coupon->coupon_amount;

                // If dealing with a line and not a single item, we need to multiple fixed discount by cart item qty.
                if ( ! $single && ! is_null( $cart_item ) ) {
                        // Discount for the line.
                        $discount = $discount * $cart_item['quantity'];
                }

        } elseif ( $coupon->is_type( array( 'percent_product', 'percent' ) ) ) {

                $discount = round( ( $discounting_amount / 100 ) * $coupon->coupon_amount, WC()->cart->dp );

        } elseif ( $coupon->is_type( 'fixed_cart' ) ) {
                if ( ! is_null( $cart_item ) ) {
                        /**
                         * This is the most complex discount - we need to divide the discount between rows based on their price in
                         * proportion to the subtotal. This is so rows with different tax rates get a fair discount, and so rows
                         * with no price (free) don't get discounted.
                         *
                         * Get item discount by dividing item cost by subtotal to get a %
                         */
                        $discount_percent = 0;
                        
                        if ( WC_Adq()->quote->subtotal_ex_tax ) {
                                $discount_percent = ( $cart_item['data']->get_price_excluding_tax() * $cart_item['quantity'] ) / WC_Adq()->quote->subtotal_ex_tax;
                        }

                        $discount = min( ( $coupon->coupon_amount * $discount_percent ) / $cart_item['quantity'], $discounting_amount );
                } else {
                        $discount = min( $coupon->coupon_amount, $discounting_amount );
                }
        }

        // Handle the limit_usage_to_x_items option
        if ( $coupon->is_type( array( 'percent_product', 'fixed_product' ) ) && ! is_null( $cart_item ) ) {
                $qty = '' === $coupon->limit_usage_to_x_items ? $cart_item['quantity'] : min( $coupon->limit_usage_to_x_items, $cart_item['quantity'] );

                // Reduce limits
                $coupon->limit_usage_to_x_items = max( 0, $coupon->limit_usage_to_x_items - $qty );

                if ( $single ) {
                        $discount = ( $discount * $qty ) / $cart_item['quantity'];
                } else {
                        $discount = ( $discount / $cart_item['quantity'] ) * $qty;
                }
        }
        
        return $discount;
        
    }
    add_filter( 'woocommerce_coupon_get_discount_amount', 'adq_coupon_get_discount_amount', 10, 5 );
}

/** Mini-Cart *************************************************************/

if ( ! function_exists( 'adq_mini_cart' ) ) {

	/**
	 * Output the Mini-cart - used by cart widget
	 *
	 */
	function adq_mini_cart( $args = array() ) {

		$defaults = array(
			'list_class' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		adq_get_template( 'widget/mini-quote.php', $args );
	}
}

if ( ! function_exists( 'adq_simple_mini_cart' ) ) {

	/**
	 * Output the Simple Mini-cart - used by cart widget
	 *
	 */
	function adq_simple_mini_cart( $args = array() ) {

		$defaults = array(
			'list_class' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		adq_get_template( 'widget/simple-mini-quote.php', $args );
	}
}



if ( ! function_exists( 'adq_body_class' ) ) {
        function adq_body_class( $classes ) {
                $classes = (array) $classes;
                
                if ( StaticAdqQuoteRequest::is_quote_list_page() ) {
                        $classes[] = 'woocommerce-cart';
                        $classes[] = 'woocommerce-adq';
                }
        
                return array_unique( $classes );
        }        
        add_filter( 'body_class', 'adq_body_class' );
}