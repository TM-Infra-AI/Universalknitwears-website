<?php
/*
Plugin Name:  Woocommerce quotation
Description:  System for request a special price for a quoted products.
Version:      2.3.6
Author: Aldaba Digital
Domain Path: /languages/
License: GPL v3
*/

if ( ! function_exists( 'is_woocommerce_plugin_active' ) ) {
    
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        
        function is_woocommerce_plugin_active() {

                $active_plugins = (array) get_option( 'active_plugins', array() );

                if ( is_multisite() ) {
                        $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
                }

                return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
        }
}
                
if ( is_woocommerce_plugin_active() ) :

    class WC_Quotation {               
            
            public $AdqEmail;  
            
            public $quote;
            
            public $order;
            
            public $modules = array();
            
            protected static $_instance;
            
            public function __construct() {
                    /* DEFINES STATIC VARIABLES */
                    define("ADQ_ADMIN_PAGE", "woocommerce-quotation");
                    define("ADQ_VERSION", "2.3.6");
                    define("ADQ_QUOTE_URL", plugin_dir_url( __FILE__ ) );
                    define("ADQ_QUOTE_DIR", plugin_dir_path( __FILE__ ) );
                    define("ADQ_PLUGIN_FILE", __FILE__ );                    

                    add_action( 'woocommerce_init', array( $this, 'init_wc' ) );

                    /* LANGUAGE TRANSLATE */                    
                    $adq_domain = 'woocommerce-quotation';
                    // The "plugin_locale" filter is also used in load_plugin_textdomain()
                    $adq_locale = apply_filters('plugin_locale', get_locale(), $adq_domain);
                    load_textdomain($adq_domain, WP_LANG_DIR.'/plugins/'.$adq_domain.'-'.$adq_locale.'.mo');
                    load_plugin_textdomain( 'woocommerce-quotation', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

                    $this->requires();                     

                    $this->quote = WC_Quotation_Quote::instance();

                    $this->order = WC_Quotation_Order::instance();                

                    add_action( 'widgets_init', array( $this, 'adq_register_widgets' ), 20 );
                    //add_action( 'plugins_loaded', array( $this, 'modules' ), 20 );
            }            
           
            /**
            * Cookie Class.
            * @return WC_Quotation_Cookie
            */
            public function cookie() {
                   return WC_Quotation_Cookie::instance();
            }

            public static function instance() {
                    if ( is_null( self::$_instance ) ) {
                            self::$_instance = new self();
                    }
                    return self::$_instance;
            }
            
            /*
             * Requires classes/functions that needs WC loaded
             */
            public function requires_wc() {
                    require_once dirname(__FILE__).'/classes/class-adq-settings-page.php'; 
                    require_once dirname(__FILE__).'/classes/class.adq.email.php';
                    
                    $this->modules();
            }
            
            public function init_wc() {
                
                    $this->requires_wc();
                    
                    //Instance Mailer
                    WC();

                    $this->AdqEmail = new ADQ_Email();

                    WC()->mailer();
                    
                    do_action('adq_init');
            }

            public function requires() {
                    require_once dirname(__FILE__).'/edd/edd-adq.php';

                    /* INSTANCE CLASSES */
                    require_once dirname(__FILE__).'/classes/class.static.quote.php';
                    require_once dirname(__FILE__).'/classes/class.quote.php';
                    require_once dirname(__FILE__).'/classes/class.order.quote.php';
                    require_once dirname(__FILE__).'/classes/class.cookie.php';                                       

                    /* INSTANCE FUNCTIONS FRONTEND/BACKEND FUNCTIONS */   
                    require_once dirname(__FILE__).'/functions/adq-order-quote.php';
                    require_once dirname(__FILE__).'/functions/adq-cron.php';
                    require_once dirname(__FILE__).'/functions/adq-global.php';

                    if ( is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) )
                            require_once dirname(__FILE__).'/functions/adq-admin-quote-request.php';
                    elseif(  is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX )
                            require_once dirname(__FILE__).'/functions/adq-quote-ajax.php';
                    else
                            require_once dirname(__FILE__).'/functions/adq-frontend-quote-request.php';
            } 
            
            /*
             * Compatibility with other modules
             */
            public function modules () {
                    
                    $this->modules[] = include dirname(__FILE__).'/modules/contact-form-7.php';
                    $this->modules[] = include dirname(__FILE__).'/modules/product-addons.php';
                    $this->modules[] = include dirname(__FILE__).'/modules/bookings.php';
                    $this->modules[] = include dirname(__FILE__).'/modules/product-bundles.php'; 
                    $this->modules[] = include dirname(__FILE__).'/modules/extra-options.php'; 
            }
            
            /*
             * Register Widgetes
             */
            public function adq_register_widgets() {
                    /* WIDGET */
                    require_once dirname(__FILE__).'/classes/widget/class-wc-widget-quote.php';
                    
                    register_widget( 'WC_Widget_Quote' );
            }
    }        

    /**
    * Returns the main instance of WC Quotation to prevent the need to use globals.
    *
    * @since  2.0.0
    * @return WooCommerce Quotation class
    */
    function WC_Adq() {
            return WC_Quotation::instance();
    }

    //Instance
    WC_Adq();

endif;