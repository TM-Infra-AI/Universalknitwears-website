<?php
/**
 * Handle Static functions
 *
 * @class 	StaticAdqQuoteRequest
 * @version     1.0.0
 * @package     woocommerce-quotation/classes/
 * @category    Class
 * @author      Aldaba Digital
 */

if( !class_exists( 'StaticQuoteRequest' ) ) { 
    
    class StaticAdqQuoteRequest {

        public static function generate_quote_button( $product, $class = '' ) {
            if( !self::can_show_quote_button( $product ) ) {
                    return '';
            }
            
            ob_start();             
            
            switch ($product->product_type) {
                case "variable": 
                    $style = 'style="display:none;"';
                    break;                
                case "simple": 
                default:
                    $style = '';
                    break;
            }
            ?>

            <div class="clear"></div>
            
            <div class="<?php echo $product->product_type."_add_to_quote button_add_to_quote" ?>" <?php //echo $style ?>>                
                <button class="single_adq_button <?php echo $class ?> button alt" id="add_to_quote" data-product-id="<?php echo $product->id ?>" data-product-type="<?php echo $product->product_type ?>" data-button="<?php echo $product->product_type ?>_add_to_quote" data-is_quote="1" type="button"> <?php echo self::get_add_to_quote_text(); ?></button>  
            </div>
            
            <div class="clear"></div>

            <?php
            return ob_get_clean();
        }
        
        public static function generate_group_quote_button( $product ) {
                if( !self::can_show_quote_button( $product ) ) {
                        return '';
                }

                ob_start();
                ?>

                <div class="clear"></div>

                <div class="<?php echo $product->product_type."_add_to_quote" ?>" >                
                    <button class="grouped_adq_button button alt" id="add_to_quote" data-product-id="<?php echo $product->id ?>" data-product-type="<?php echo $product->product_type ?>" data-is_quote="1" type="button"> <?php echo self::get_add_to_quote_text(); ?></button>  
                </div>

                <div class="clear"></div>

                <?php
                return ob_get_clean();
        }
        
        public static function generate_quote_button_loop( $product ) {            
                if( !self::can_show_quote_button( $product ) ) {
                        return '';
                }

                $show = get_option('adq_general_quote_loop');
                if( $show == "no" ) {
                        return '';
                }

                ob_start(); ?>

                <div class="clear"></div>

                <p class="single_add_to_quote_loop">
                        <a class="button single_adq_button_loop product_type_simple" id="add_to_quote_loop" data-quantity="1" data-product-id="<?php echo $product->id ?>" rel="nofollow" href="#"><?php echo self::get_add_to_quote_text(); ?></a>
                </p>
                <div class="clear"></div>

                <?php
                return ob_get_clean();
        }
        
        public static function can_show_product_price ( $order ) {
            
                foreach ( $order->get_items() as $item ) {                    
                        $_product = $order->get_product_from_item( $item );
                        
                        if( !self::can_show_price( $_product ) ) {
                                return false;
                        }
                }
                
                return true;
        }
        
        public static function can_show_quote_button( $product = false ) {
                
                return self::can_show('quote', $product );
        }
        
        public static function can_show_price( $product = false ) {
                
                return self::can_show('price', $product );
        }
        
        public static function can_show_cart_button( $product  = false ) {
                
                if ( $product && !$product->is_purchasable() ) {
                        return false;
                }
                
                return self::can_show('cart', $product );
        }
        
        private static function can_show ( $element, $product  = false ) {
                $inherit = '_adq_inherit_visibility_'.$element;
                $option = 'adq_visibility_'.$element;
                
                $user = wp_get_current_user();
                
                if( $user ) {
                    $roles = $user->roles;               
                }
                
                if ( !$roles || count($roles) == 0 ) {
                    $roles = array('unregistered');
                }                
                
                if( is_user_logged_in() ) {
                    $roles[] = 'loggedin';
                }
                
                
                $filter_role = false;
                
                if( $product ) {
                        $id_product = $product->ID;
                        if( !$id_product || $id_product == 0) {
                             $id_product = $product->id;
                        }

                        $adq_inherit_visibility_cart = get_post_meta( (int)$id_product, $inherit, true );

                        if ( $adq_inherit_visibility_cart === "no" ) {                                       
                                $roles_option = get_post_meta( (int)$id_product, $option, true );
                        }
                        else {
                                //Check if visibility is determined by taxonomy
                                $taxonomy = get_option('adq_taxonomy_filter');
                                
                                if( $taxonomy != "" ) {
                                        $terms = get_option('adq_'.$taxonomy.'_'.$element);
                                        $terms_exclude = get_option('adq_'.$taxonomy.'_'.$element.'_exclude');
                                        
                                        if( is_object_in_term( (int)$id_product, $taxonomy, $terms ) ) {
                                                $filter_taxonomy = $terms_exclude == 'yes'?false:true;                                                
                                        }
                                        else  {
                                                $filter_taxonomy = $terms_exclude == 'yes'?true:false;
                                        }
                                }
                                
                                //Else use global options by roles
                                $roles_option = get_option($option);                                
                        }   
                }
                else {
                        $roles_option = get_option($option);
                }
                
                if( count($roles_option) <= 0 ) {
                        return false;
                }
                
                //User have correct role?
                foreach ( $roles as $role ) {
                        if( isset($roles_option['loggedin']) && is_user_logged_in() ) {
                                $filter_role = true;                                
                                
                                if( !isset($filter_taxonomy) ) {
                                        return $filter_role; 
                                }                            
                        }
                        elseif ( in_array($role, $roles_option) ) {
                                $filter_role = true;                                
                                
                                if( !isset($filter_taxonomy) ) {
                                        return $filter_role; 
                                }
                        }
                }

                if ( $filter_role && $filter_taxonomy ) {
                        return true;
                }
                
                return false;
        }
        
        public static function must_redirect() {
                $url = false;
                
                $redirect = get_option('adq_redirect_quote_page');
                if( $redirect == "yes" ) {
                        $url = self::get_quote_list_link();
                }
                
                return $url;
        }
        
        public static function is_quote_list_page () {
                global $post;                
                                   
                if(!$post) {
                    if( isset( $_REQUEST['page_id'] ) )
                        $post_id = $_REQUEST['page_id'];
                    else
                        return false;
                }
                else {
                        $post_id = $post->ID;
                }
                
                if((int)$post_id == (int)get_option('adq_quotelist_page_id')) {
                        return true;
                }
                
                return false;
        }
        
        public static function is_order_quote_status ( $status ) {
                $adq_statuses = adq_get_order_statuses();
                
                if( array_key_exists( "wc-".$status, $adq_statuses ) )
                        return true;
                
                return false;
        }
        
        public static function get_proposal_base_url ( $order ) {
                if( !$order )
                    return;
                
                $order_key = base64_encode ( get_post_meta( $order->id, '_order_key', true ) );
                $email = urlencode ( get_post_meta( $order->id, '_billing_email', true ) );

                $link = get_option( 'permalink_structure' );
                if($link != "")
                    $nedlee = '?';
                else 
                    $nedlee = '&';
                
                return get_permalink( wc_get_page_id( 'myaccount' ) ) . $nedlee . 'order_key='.$order_key.'&email='.$email;
        }     
        
        public static function redirect_after_quote_list() {                                
                $adq_redirect_after_quote = get_option('adq_redirect_after_quote');
                if($adq_redirect_after_quote != "0" && $adq_redirect_after_quote != "") {
                        return $adq_redirect_after_quote;
                }
                
                return self::get_quote_list_link();
        }

        public static function get_quote_list_link () {                
                return get_page_link( get_option('adq_quotelist_page_id') );
        }
        
        public static function get_add_to_quote_text() {
                return get_option('adq_quote_text');
        }
        
        public static function get_ok_notice($product_id, $show_button = false) {
                if($show_button)
                    if( self::must_redirect() )
                        $message = sprintf( __( '&quot;%s&quot; was successfully added to quote.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
                    else
                        $message = sprintf(
                            '<a href="%s" class="button wc-forward">%s</a> %s',
                            self::get_quote_list_link(),
                            __( 'View Quote', 'woocommerce-quotation' ),
                            sprintf( __( '&quot;%s&quot; was successfully added to quote.', 'woocommerce-quotation' ), get_the_title( $product_id ) )
                        );
                else
                    $message = sprintf( __( '&quot;%s&quot; was successfully added to quote.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
                
                return $message;
        }
        
        public static function get_error_notice($product_id, $show_button = false) {
                if($show_button)
                    if( self::must_redirect() )
                        $message = sprintf( __( '&quot;%s&quot; cannot be added to quote.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
                    else
                        $message = sprintf(
                            '<a href="%s" class="button wc-forward">%s</a> %s',
                            self::get_quote_list_link(),
                            __( 'View Quote', 'woocommerce-quotation' ),
                            sprintf( __( '&quot;%s&quot; cannot be added to quote.', 'woocommerce-quotation' ), get_the_title( $product_id ) )
                        );
                else
                    $message = sprintf( __( '&quot;%s&quot; cannot be added to quote.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
                
                return $message;
        }
        
        public static function get_duplicate_notice($product_id, $show_button = false) {
                if($show_button)
                    if( self::must_redirect() )
                        $message = sprintf( __( 'You cannot add another &quot;%s&quot; to your quote.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
                    else
                        $message = sprintf(
                            '<a href="%s" class="button wc-forward">%s</a> %s',
                            self::get_quote_list_link(),
                            __( 'View Quote', 'woocommerce-quotation' ),
                            sprintf( __( 'You cannot add another &quot;%s&quot; to your quote.', 'woocommerce-quotation' ), get_the_title( $product_id ) )
                        );
                else
                    $message = sprintf( __( 'You cannot add another &quot;%s&quot; to your quote.', 'woocommerce-quotation' ), get_the_title( $product_id ) );
                
                return $message;
        }
        
        /*
         * Is product comments on quote list allowed
         */
        public static function is_comments_allowed ( $id_product ) {                
                                
                if ( get_post_meta( (int)$id_product, "_adq_inherit_allow_product_comments", true ) === "no" ) {                                       
                        $option = get_post_meta( (int)$id_product, "_adq_inherit_allow_product_comments", true );
                        if( $option == "yes" ) {
                            return true;
                        }
                }
                else {
                        $option = get_option( "adq_allow_product_comments" );
                        if( $option == "yes" ) {
                            return true;
                        }
                } 
                
                return false;
        }        
        
        /*
         *  Is Shipping enabled
         */
        public static function is_shipping_enabled () {
           
                $adq_inherit_shipping_conf = get_option("adq_inherit_shipping_conf");
                $adq_enable_shipping = get_option("adq_enable_shipping");
                $woocommerce_calc_shipping = get_option("woocommerce_calc_shipping");

                if ( ( $adq_inherit_shipping_conf == "yes" && $woocommerce_calc_shipping == "yes" ) || ( $adq_inherit_shipping_conf == "no" && $adq_enable_shipping == "yes" ) ) {
                        return true;
                }
                
                return false;
        }
        
        /**
         * Get fields required if shipping is needed
         */
        public static function shipping_required_fields ( $key, $access ) {
            
                if ( $key == $access.'_email') {
                        return true;
                }
                
                if( !self::is_shipping_enabled() ) {
                        return false;
                }
                
                $shipping_required = array(
			$access.'_country' ,
			$access.'_address_1',
			//$access.'_address_2' ,
			$access.'_city',
			//$access.'_state',
			$access.'_postcode',
		);
                
                if ( in_array($key, $shipping_required) ) {
                        return true;
                }
                
                return false;
        }
       
        
        /**
	 * Get checkout fields and filter it.
	 * @param integer $product_id
	 */
        public static function get_checkout () {
                $checkout = WC()->checkout();                
                
                foreach ( $checkout->checkout_fields['billing'] as $key => $field ) { 
                    
                        $mandatory = self::shipping_required_fields( $key, 'billing' );
                        $required = get_option( 'adq_'.$key.'_required' );
                        $visible = get_option( 'adq_'.$key.'_visible' );
                        
                        if ( $mandatory && $required === 'no' ) {
                                update_option('adq_'.$key.'_required', 'yes');
                                $required = get_option( 'adq_'.$key.'_required' );
                        } 
                        
                        if ( $mandatory && $visible === 'no' ) {
                                update_option('adq_'.$key.'_visible', 'yes');
                                $visible = get_option( 'adq_'.$key.'_visible' );
                        }
                        
                        if( $key === 'billing_state' ) {
                                $checkout->checkout_fields['billing'][$key]['visible'] = get_option( 'adq_billing_country_visible' ) == "yes"?true:false;                                                        
                                if ( get_option( 'adq_billing_country_required' ) === 'no' ) {
                                        $checkout->checkout_fields['billing'][$key]['required'] = false; 
                                }
                        }
                        elseif( $key === 'billing_email' ) {
                                $checkout->checkout_fields['billing'][$key]['visible'] = true;                        
                                $checkout->checkout_fields['billing'][$key]['required'] = true;
                        }
                        else {
                                $checkout->checkout_fields['billing'][$key]['visible'] = $visible == "yes"?true:false;                        
                                $checkout->checkout_fields['billing'][$key]['required'] = $required == "yes"?true:false;
                        }
                }
                
                return $checkout;
        }
        
        /**
	 * Download a file - hook into init function.
	 * @param integer $product_id
	 */
	public static function download( $file_path, $product_id ) {
		global $is_IE;

		$file_download_method = get_option( 'woocommerce_file_download_method' );

		if ( ! $file_path ) {
			wp_die( __( 'No file defined', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce-quotation' ) . '</a>', '', array( 'response' => 404 ) );
		}

		// Redirect to the file...
		if ( $file_download_method == "redirect" ) {
			header( 'Location: ' . $file_path );
			exit;
		}

		// ...or serve it
		$remote_file      = true;
		$parsed_file_path = parse_url( $file_path );

		$wp_uploads       = wp_upload_dir();
		$wp_uploads_dir   = $wp_uploads['basedir'];
		$wp_uploads_url   = $wp_uploads['baseurl'];

		if ( ( ! isset( $parsed_file_path['scheme'] ) || ! in_array( $parsed_file_path['scheme'], array( 'http', 'https', 'ftp' ) ) ) && isset( $parsed_file_path['path'] ) && file_exists( $parsed_file_path['path'] ) ) {

			/** This is an absolute path */
			$remote_file  = false;

		} elseif( strpos( $file_path, $wp_uploads_url ) !== false ) {

			/** This is a local file given by URL so we need to figure out the path */
			$remote_file  = false;
			$file_path    = str_replace( $wp_uploads_url, $wp_uploads_dir, $file_path );

		} elseif( is_multisite() && ( strpos( $file_path, network_site_url( '/', 'http' ) ) !== false || strpos( $file_path, network_site_url( '/', 'https' ) ) !== false ) ) {

			/** This is a local file outside of wp-content so figure out the path */
			$remote_file = false;
			// Try to replace network url
                        $file_path   = str_replace( network_site_url( '/', 'https' ), ABSPATH, $file_path );
                        $file_path   = str_replace( network_site_url( '/', 'http' ), ABSPATH, $file_path );
                        // Try to replace upload URL
                        $file_path   = str_replace( $wp_uploads_url, $wp_uploads_dir, $file_path );

		} elseif( strpos( $file_path, site_url( '/', 'http' ) ) !== false || strpos( $file_path, site_url( '/', 'https' ) ) !== false ) {

			/** This is a local file outside of wp-content so figure out the path */
			$remote_file = false;
			$file_path   = str_replace( site_url( '/', 'https' ), ABSPATH, $file_path );
			$file_path   = str_replace( site_url( '/', 'http' ), ABSPATH, $file_path );

		} elseif ( file_exists( ABSPATH . $file_path ) ) {

			/** Path needs an abspath to work */
			$remote_file = false;
			$file_path   = ABSPATH . $file_path;
		}

		if ( ! $remote_file ) {
			// Remove Query String
			if ( strstr( $file_path, '?' ) ) {
				$file_path = current( explode( '?', $file_path ) );
			}

			// Run realpath
			$file_path = realpath( $file_path );
		}

		// Get extension and type
		$file_extension  = strtolower( substr( strrchr( $file_path, "." ), 1 ) );
		$ctype           = "application/force-download";

		foreach ( get_allowed_mime_types() as $mime => $type ) {
			$mimes = explode( '|', $mime );
			if ( in_array( $file_extension, $mimes ) ) {
				$ctype = $type;
				break;
			}
		}

		// Start setting headers
		if ( ! ini_get('safe_mode') ) {
			@set_time_limit(0);
		}

		if ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() ) {
			@set_magic_quotes_runtime(0);
		}

		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}

		@session_write_close();
		@ini_set( 'zlib.output_compression', 'Off' );

		/**
		 * Prevents errors, for example: transfer closed with 3 bytes remaining to read
		 */
		if ( ob_get_length() ) {

			if ( ob_get_level() ) {

				$levels = ob_get_level();

				for ( $i = 0; $i < $levels; $i++ ) {
					ob_end_clean(); // Zip corruption fix
				}

			} else {
				ob_end_clean(); // Clear the output buffer
			}
		}

		if ( $is_IE && is_ssl() ) {
			// IE bug prevents download via SSL when Cache Control and Pragma no-cache headers set.
			header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
			header( 'Cache-Control: private' );
		} else {
			nocache_headers();
		}

		$filename = basename( $file_path );

		if ( strstr( $filename, '?' ) ) {
			$filename = current( explode( '?', $filename ) );
		}

		$filename = apply_filters( 'woocommerce_file_download_filename', $filename, $product_id );
                
		header( "X-Robots-Tag: noindex, nofollow", true );
		header( "Content-Type: " . $ctype );
		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );
		header( "Content-Transfer-Encoding: binary" );

                if ( $size = @filesize( $file_path ) ) {
                        header( "Content-Length: " . $size );
                }

		if ( $file_download_method == 'xsendfile' ) {

			// Path fix - kudos to Jason Judge
                    if ( getcwd() ) {
                            $file_path = trim( preg_replace( '`^' . str_replace( '\\', '/', getcwd() ) . '`' , '', $file_path ), '/' );
                    }

                    header( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );

                    if ( function_exists( 'apache_get_modules' ) && in_array( 'mod_xsendfile', apache_get_modules() ) ) {

                        header("X-Sendfile: $file_path");
                        exit;

                    } elseif ( stristr( getenv( 'SERVER_SOFTWARE' ), 'lighttpd' ) ) {

                        header( "X-Lighttpd-Sendfile: $file_path" );
                        exit;

                    } elseif ( stristr( getenv( 'SERVER_SOFTWARE' ), 'nginx' ) || stristr( getenv( 'SERVER_SOFTWARE' ), 'cherokee' ) ) {

                        header( "X-Accel-Redirect: /$file_path" );
                        exit;

                    }
                }

                if ( $remote_file ) {
                        self::readfile_chunked( $file_path ) || header( 'Location: ' . $file_path );
                } else {
                        self::readfile_chunked( $file_path ) || wp_die( __( 'File not found', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce' ) . '</a>', '', array( 'response' => 404 ) );
                }

                exit;
            }
            
            
            /**
            * readfile_chunked
            * Reads file in chunks so big downloads are possible without changing PHP.INI - http://codeigniter.com/wiki/Download_helper_for_large_files/
            * @param    string $file
            * @param    bool   $retbytes return bytes of file
            * @return bool|int
            * @todo Meaning of the return value? Last return is status of fclose?
            */
           public static function readfile_chunked( $file, $retbytes = true ) {
                   $chunksize = 1 * ( 1024 * 1024 );
                   $buffer = '';
                   $cnt = 0;

                   if ( file_exists( $file ) ) {
                           $handle = fopen( $file, 'r' );
                           if ( $handle === FALSE ) {
                                   return FALSE;
                           }
                   } elseif ( version_compare( PHP_VERSION, '5.4.0', '<' ) && ini_get( 'safe_mode' ) ) {
                           $handle = @fopen( $file, 'r' );
                           if ( $handle === FALSE ) {
                                   return FALSE;
                           }
                   } else {
                           return FALSE;
                   }

                   while ( ! feof( $handle ) ) {
                           $buffer = fread( $handle, $chunksize );
                           echo $buffer;
                           if ( ob_get_length() ) {
                                   ob_flush();
                                   flush();
                           }

                           if ( $retbytes ) {
                                   $cnt += strlen( $buffer );
                           }
                   }

                   $status = fclose( $handle );

                   if ( $retbytes && $status ) {
                           return $cnt;
                   }

                   return $status;
            }                      
            
    }    
    
}