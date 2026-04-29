<?php
/**
 * Handle Core global functions
 *
 * @version     2.0.0
 * @package     woocommerce-quotation/functions/
 * @category    Functions
 * @author      Aldaba Digital
 */

/* GLOBAL FUNCTIONS */
if( !function_exists( 'adq_quote_request_link' ) ) {
    // Add settings link on plugin page
    function adq_quote_request_link( $links ) {
            $settings_link = '<a href="options-general.php?page='.ADQ_ADMIN_PAGE.'">'._x('Configuration', 'Configuration Quote', 'woocommerce-quotation').'</a>';
            array_unshift( $links, $settings_link );
            return $links;
    }
    $plugin = plugin_basename( __FILE__ );
    add_filter( "plugin_action_links_$plugin", 'adq_quote_request_link' );
}


if( !function_exists( 'adq_quote_request_install' ) ) {
    //Install table
    function adq_quote_request_install() {
                        
            //Create Page
            $pages = array(
                    'quotelist' => array(
                            'name'    => _x( 'quote-list', 'Page slug', 'woocommerce-quotation' ),
                            'title'   => _x( 'Quote List', 'Page title', 'woocommerce-quotation' ),
                            'content' => '[quote_request_list]'
                    )
            );
            foreach ( $pages as $key => $page ) {                    
                    $page_id = wc_create_page( esc_sql( $page['name'] ), 'adq_' . $key . '_page_id', $page['title'], $page['content'], '' );
            }                        
            
            //add_option( 'adq_version', ADQ_VERSION ); 
    }
    register_activation_hook( ADQ_PLUGIN_FILE, 'adq_quote_request_install' );
}

/*
 * UNINSTALL option
 */
if( !function_exists( 'adq_update_version' ) ) {
    function adq_quote_request_uninstall() {
            delete_option('adq_version');
    }
    register_deactivation_hook( ADQ_PLUGIN_FILE, 'adq_quote_request_uninstall' );
}

/*
 *  UPDATE issues
 */
if( !function_exists( 'adq_update_version' ) ) {
    function adq_update_version() {
        global $wpdb;
        
        $current_version = get_option( 'adq_version', '0.0.0' );   
        
        //Vanilla instalation
        if ( version_compare( $current_version, '0.0.1', '<' ) ) {
            //Create Options for Customization
            $sections = ADQ_Settings_Page::get_sections_pages();

            foreach ( $sections as $section ) {
                    if ( ! method_exists( $section, 'get_settings' ) ) {
                            continue;
                    }
                   
                    foreach ( $section->get_settings() as $value ) {
                            if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
                                    $autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
                                    add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
                            }
                    }                    
            }
        }
            
        if ( version_compare( $current_version, '2.0.0', '<' ) ) {        
                $result = $wpdb->query( 'DROP TABLE IF EXISTS `wp_adq_quote_request`, `wp_adq_quote_request_itemmeta`' );

                if ( $result ) {
                        update_option( 'adq_version', '2.0.0' );                    
                }
        } 
        
        if ( version_compare( $current_version, '2.1.1', '<' ) ) {        
                
                $sections = ADQ_Settings_Page::get_sections_pages();

                foreach ( $sections as $section ) {
                        if ( ! method_exists( $section, 'get_settings' ) ) {
                                continue;
                        }

                        foreach ( $section->get_settings() as $value ) {
                                if ( $value['type'] == "checkbox" ) {
                                        $option = get_option( $value['id'] );
                                        
                                        $option_value = false;
                                        if($option == '1')
                                            $option_value = 'yes';
                                        elseif($option == '0')
                                            $option_value = 'no';
                                        
                                        if ( $option_value ) {
                                            update_option( $value['id'], $option_value );
                                        }
                                }
                                elseif( $value['type'] == "text" ) {
                                        $option = get_option( $value['id'] );
                                        update_option( $value['id'], $option );
                                }
                        }                    
                } 
                
                update_option( 'adq_version', '2.1.1' );                
        }

        update_option( 'adq_version', ADQ_VERSION );
    }
    add_action( 'adq_init', 'adq_update_version' );
}


if( !function_exists( 'adq_get_template' ) ) {
    /**
     * Filter before call wc_get_template.
     *
     * @param string $template_name
     * @param array $args (default: array())
     * @param string $template_path (default: '')
     * @param string $default_path (default: '')
     * @return void
     */
    function adq_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {        
            if(!$default_path || $default_path == '') {
                //Check if template is in our module
                $located = wc_locate_template( $template_name, $template_path, ADQ_QUOTE_DIR."templates/" );

                if ( file_exists( $located ) ) {
                    $default_path = ADQ_QUOTE_DIR."templates/";
                }
            }

            wc_get_template($template_name, $args, $template_path, $default_path);
    }
}


if( !function_exists( 'adq_add_notice' ) ) {
    /**
    * Filter before call wc_add_notice.
    *
    * @param string $message
    * @param string $notice_type (default: 'success')
    * @return void
    */
    function adq_add_notice( $message, $notice_type = 'success' ) { 
            
            wc_add_notice( $message, $notice_type );
            WC_Adq()->cookie()->show_notices = 1;

            if( !is_user_logged_in() ) {                                  
                   WC_Adq()->cookie()->notices = wc_get_notices();                   
            }
            
            WC_Adq()->cookie()->write();
    }
}

if( !function_exists( 'adq_loader_noticies' ) ) {
    /*
     * If notice is set on cookie we override it on to Woocommerce notices
     */
    function adq_loader_noticies () {
        
        if ( ! is_woocommerce_plugin_active() ) {
                return false;
        }
        
        if(isset(WC_Adq()->cookie()->show_notices) && WC_Adq()->cookie()->show_notices == 1) {            

		unset(WC_Adq()->cookie()->show_notices);
                WC_Adq()->cookie()->write();    
        }

        if(isset(WC_Adq()->cookie()->notices) && count(WC_Adq()->cookie()->notices) > 0) {        
                foreach(WC_Adq()->cookie()->notices as $notice_type => $notices) {
                    foreach($notices as $message) {
                        if( !wc_has_notice($message, $notice_type) ) {
                                wc_add_notice( $message, $notice_type);
                        }
                    }
                }
                
                unset(WC_Adq()->cookie()->notices);
                WC_Adq()->cookie()->write();
        }
    }
    add_action( 'wp_loaded', 'adq_loader_noticies' );
}


if( !function_exists( 'is_quotable' ) ) {
	/**
	 * Rewrite function is_purchasable
	 *
	 * @return bool
	 */
	function is_quotable( $product ) {                
                
		$purchasable = true;

		// Products must exist of course
		if ( ! $product->exists() ) {
			$purchasable = false;

		// Check the product is published
		} elseif ( $product->post->post_status !== 'publish' && ! current_user_can( 'edit_post', $product->id ) ) {
			$purchasable = false;
		}

		return apply_filters( 'adq_is_quotable', $purchasable, $product );
	}
}
