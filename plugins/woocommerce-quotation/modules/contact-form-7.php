<?php
/**
 * WooCommerce Contact Form 7 compatibility
 *
 * @author      Aldaba Digital
 * @category    Admin
 * @package     woocommerce-quotation/modules/
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'ADQ_WPCF7' ) ) :
/**
 * ADQ_WPCF7
 */
class ADQ_WPCF7  {

        protected $posted_data;
        
        protected $is_quote_submit = false;
        
        protected $invalid_fields = false;
        
        /**
	 * Constructor.
	 */
	public function __construct() {     
                if( isset( $_POST["adq_quote_place_order"] ) ) {
                        $this->is_quote_submit = true;
                }
                
                add_action( 'adq_quote_list_after', array( $this, 'adq_wpcf7_shortcode' ) );                 
                add_action( 'adq_after_quote_create', array( $this, 'adq_wpcf7_quote_create' ) ); 
                add_action( 'adq_add_order_detail_after', array( $this, 'adq_wpcf7_order_detail' ) );
                add_action( 'adq_order_detail_quote_after', array( $this, 'adq_wpcf7_order_detail_quote' ) );
                add_action( 'adq_email_proposal_after_header', array( $this, 'adq_wpcf7_email_order_meta' ), 15, 3 );
                add_action( 'wpcf7_submit', array( $this, 'adq_wpcf7_posted_data' ), 10, 2 );
                add_action( 'wpcf7_before_send_mail', array( $this, 'adq_wpcf7_get_attachments' ) );
                add_action( 'woocommerce_after_checkout_validation', array( $this, 'adq_wpcf7_checkout_validation' ) );
                
                add_filter( 'woocommerce_adq_form_settings', array( $this, 'adq_wpcf7_settings' ) );                
                add_filter( 'wpcf7_skip_mail', array( $this, 'adq_wpcf7_skip_mail' ) );                
        }

        public function adq_wpcf7_settings ( $settings ) {
                $settings[] = array(
                        'title'   => __( 'Contact Form 7 shortcode', 'woocommerce-quotation' ),
                        'desc'    => __('Enter the Contact Form 7 shortcode to show the form.', 'woocommerce-quotation'),
                        'id'      => 'adq_contact_form',
                        'default' => '',
                        'type'    => 'text'
                );

                return $settings;
        }   

        public function adq_wpcf7_shortcode () {  
                $contact_form = get_option('adq_contact_form', '');

                if( $contact_form != "" && function_exists( 'wpcf7_widget_text_filter' ) ) {

                        $return = wpcf7_widget_text_filter($contact_form);

                        //Remove submit button
                        $return = preg_replace('#<input\b[^>]*\s(type="submit")[^>]*>#i','', $return);
                        //Remove form tag. We use our own form submit
                        $return = preg_replace('#<form(.*?)>#is','', $return);
                        $return = str_replace('</form>', '', $return);

                        echo $return;
                }
        }    
        
        public function adq_wpcf7_posted_data ( $contactform, $result ) {
                //Check if we are in Order List submission
                if( ! $this->is_quote_submit ) {
                        return false;
                }
                
                $submission = WPCF7_Submission::get_instance();

                if ( ! $submission || ! $get_posted_data = $submission->get_posted_data() ) {
                        return;
                }
                
                if( isset( $result['invalid_fields'] ) ) {
                        $this->invalid_fields = true;
                }
                
                $contact_form = wpcf7_contact_form( (int) $get_posted_data['_wpcf7'] );                
                $wpcf7_fields = $contact_form->form_scan_shortcode(); 
                $posted_data = $this->posted_data;
                
                foreach ( $wpcf7_fields as $field) {
                        $key = $field['name'];
                        if ( isset( $get_posted_data[$key] ) && $get_posted_data[$key] != ""
                                && ! isset( $posted_data[$key] ) ) {
                                $posted_data[$key] = $get_posted_data[$key];
                        }
                }
                
                $this->posted_data = apply_filters( 'wpcf7_posted_data', $posted_data );
                
                foreach ( $this->posted_data as $key => $posted_data ) {
                        if( is_array( $posted_data ) ) {
                                $this->posted_data[$key] = implode( ', ', $posted_data );
                        }
                }
        }                       
        
        
        //Checkout validationd fields
        public function adq_wpcf7_checkout_validation () {
                //Check if we are in Order List submission
                if( ! $this->is_quote_submit ) {
                        return false;
                }
                
                if( $this->invalid_fields ) {
                        wc_add_notice( __( 'Some fields are not valid.', 'woocommerce-quotation' ), 'error' );
                }
        }
    
        //Save post data and associate to an order
        public function adq_wpcf7_quote_create ( $order_id ) {
                //Check if we are in Order List submission                
                if( ! $this->posted_data ) {
                        return false;
                }
                
                update_post_meta( $order_id, '_adq_wpcf7', $this->posted_data );
        }
        
        public function adq_wpcf7_order_detail ( $order_id ) {

                $posted_data = get_post_meta( $order_id, '_adq_wpcf7', true );
                
                if ( $posted_data && count($posted_data) > 0) { ?>
                        <table class="form-table">
                <?php   foreach ( $posted_data as $key => $data ) { ?>
                                <tr valign="top">
                                        <th class="titledesc" scope="row">
                                                <label><?php echo $key ?></label>
                                        </th>
                                        <td class="forminp forminp-text">
                                                <span><?php echo $data ?></span> 						
                                        </td>
                                </tr>
                 <?php  } ?>
                        </table>
        <?php   }
        }
        
        public function adq_wpcf7_order_detail_quote ( $order_id ) {
                
                $posted_data = get_post_meta( $order_id, '_adq_wpcf7', true );
                
                if ( $posted_data && count($posted_data) > 0) { ?>
                        <dl class="quote_details">
                <?php   foreach ( $posted_data as $key => $data ) { ?>
                                <dt><?php echo $key ?></dt>
                                <dd><?php echo $data ?></dd>
                 <?php  } ?>
                        </dl>
        <?php   }
        }
        
        public function adq_wpcf7_email_order_meta ( $order, $sent_to_admin, $plain_text ) {
                
                $posted_data = get_post_meta( $order->id, '_adq_wpcf7', true );

                if ( !$posted_data || count($posted_data) <= 1 ) {
                        return false;
                }
                
                if ( $plain_text ) {
                        foreach ( $posted_data as $key => $data ) {                                        
                                echo $key . ': ' . $data . "\n";                                       
                        }
                } else {
                        foreach ( $posted_data as $key => $data ) {                                        
                                echo '<p><strong>' . $key . ':</strong> ' . $data . '</p>';                                        
                        }
                }                
        }
        
        //Don't send mail on quote list submit
        public function adq_wpcf7_skip_mail ( $skip_mail ) {
                if( $this->is_quote_submit ) {
                        return true;
                }
                
                return $skip_mail;            
        }
        
        //Don't send mail on quote list submit
        public function adq_wpcf7_get_attachments ( $contact_form ) {            
                if( ! $this->is_quote_submit ) {
                        return;
                }
                
                $attachments = array();

		if ( $submission = WPCF7_Submission::get_instance() ) {
			$uploaded_files = $submission->uploaded_files();

			foreach ( (array) $uploaded_files as $key => $path ) {
				if ( ! empty( $path ) ) {
                                        $dir = $this->adq_wpcf7_copy_upload_dir( $path );                                        
                                        
                                        if( $dir ) {
                                                $uploads = wp_upload_dir();

                                                $url = $this->adq_wpcf7_upload_url() . trailingslashit( $dir['subdir'] ) . $dir['filename'];
                                                $this->posted_data[$key] = make_clickable( $url );
                                        }
				}
			}
		}                  
                
        }
        
        //Don't send mail on quote list submit
        private function adq_wpcf7_copy_upload_dir ( $path ) {
                $this->adq_wpcf7_init_uploads(); // Confirm upload dir
                $uploads_dir = $this->adq_wpcf7_upload_dir();
                $subdir = $this->adq_wpcf7_upload_subdir();
                $uploads_dir = trailingslashit( $uploads_dir ) . $subdir;
                wp_mkdir_p( $uploads_dir );
                
                $filename = basename( $path );
                $filename = wpcf7_canonicalize( $filename );
                $filename = sanitize_file_name( $filename );
                $filename = wpcf7_antiscript_file_name( $filename );
                
                $path_parts = pathinfo( $filename );
                $filename = md5( $path_parts['filename'] . AUTH_SALT ) . '.' . $path_parts['extension'];
                $filename = wp_unique_filename( $uploads_dir, $filename );

                $new_file = trailingslashit( $uploads_dir ) . $filename;
                //pathinfo
                
                if ( false !== copy( $path, $new_file ) ) {                     
                        // Make sure the uploaded file is only readable for the owner process
                        @chmod( $new_file, 0664 );

                        return array(
                                "new_file" => $new_file,
                                "filename" => $filename,
                                "subdir" => $subdir
                        );
                }
                
                return false;                
        }
        
        private function adq_wpcf7_init_uploads() {
            
                $dir = $this->adq_wpcf7_upload_dir();
                wp_mkdir_p( $dir );
        }
        
        private function adq_wpcf7_upload_dir() {
                
                $uploads = wp_upload_dir();
                
                return trailingslashit( $uploads['basedir'] ) . 'adq_wpcf7_uploads';
        }
        
        private function adq_wpcf7_upload_url() {
                
                $uploads = wp_upload_dir();
                
                return trailingslashit( $uploads['baseurl'] ) . 'adq_wpcf7_uploads';
        }
        
        private function adq_wpcf7_upload_subdir () {
            
                $uploads = wp_upload_dir();                
                
                return $uploads['subdir'];
        }
}
endif;

return new ADQ_WPCF7();