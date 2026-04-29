<?php
/**
 * Handle Email functions
 *
 * @class 	ADQ_Email
 * @version     1.0.0
 * @package     woocommerce-quotation/classes/
 * @category    Class
 * @author      Aldaba Digital
 * @extends     WC_Email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'ADQ_Email' ) ) :
    

class ADQ_Email extends WC_Emails {    
            
        
    	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {               
                
                add_action( 'adq_email_customer_quote', array( $this, 'email_customer_quote' ), 10, 1 );
                add_action( 'adq_email_admin_new_quote', array( $this, 'email_admin_new_quote' ), 10, 1 );
                add_action( 'adq_email_customer_proposal', array( $this, 'email_customer_proposal' ), 10, 1 ); 
                add_action( 'adq_email_remind_proposal', array( $this, 'email_remind_proposal' ), 10, 1 );
                add_action( 'adq_email_admin_proposal_ok', array( $this, 'email_admin_proposal_ok' ), 10, 1 );
                add_action( 'adq_email_admin_proposal_ko', array( $this, 'email_admin_proposal_ko' ), 10, 1 );
                add_action( 'adq_email_customer_proposal_ok', array( $this, 'email_customer_proposal_ok' ), 10, 1 );
                add_action( 'adq_email_customer_proposal_ko', array( $this, 'email_customer_proposal_ko' ), 10, 1 );
                add_action( 'adq_email_new_customer_note', array( $this, 'email_new_customer_note' ), 10, 1 );                
                
                //Add additional information on proposal emails
                add_action( 'adq_email_proposal_after_header', array( $this, 'adq_add_meta_proposal' ), 10, 3 );
                
                //Override Header and footer
                add_action( 'adq_email_header', array( $this, 'adq_email_header' ), 10, 1 );
                add_action( 'adq_email_footer', array( $this, 'adq_email_footer' ), 10, 1 );
                
                add_filter('woocommerce_email_classes', array ($this, 'enqueue_emails') ); 
                
                //parent::__construct(); 
                //$this->init();
	}
        
        function adq_email_header ($email_heading) {
                adq_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
        }
        
        function adq_email_footer () {
                adq_get_template( 'emails/email-footer.php' );
        }    
        
        /*
         *  Enqueue in parent class our custom emails
         */
        function enqueue_emails ($emails) {
            
                $this->emails['ADQ_Customer_Quote'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-customer-quote.php' );
                $this->emails['ADQ_Admin_New_Quote'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-admin-new-quote.php' );
                $this->emails['ADQ_Customer_Proposal'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-customer-proposal.php' );
                $this->emails['ADQ_Proposal_Reminder'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-remind-proposal.php' );
                $this->emails['ADQ_Admin_Proposal_Ko'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-admin-proposal-ko.php' );
                $this->emails['ADQ_Admin_Proposal_Ok'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-admin-proposal-ok.php' );
                $this->emails['ADQ_Customer_Proposal_Ko'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-customer-proposal-ko.php' );
                $this->emails['ADQ_Customer_Proposal_Ok'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-customer-proposal-ok.php' );
                $this->emails['ADQ_Customer_Note'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-customer-note.php' );
                $this->emails['ADQ_Admin_Note'] = include( ADQ_QUOTE_DIR.'classes/emails/class-adq-email-admin-note.php' );    
                
                foreach ($this->emails as $key => $value) {
                    if(!isset( $emails[$key] )) {
                            $emails[$key] = $value;
                    }
                } 
                
                return $emails;
        }
    
        /**
	 * Customer new account welcome email.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	function email_customer_quote ( $order_id ) {
		if ( ! $order_id )
			return;

		$email = $this->emails['ADQ_Customer_Quote'];
		$email->trigger( $order_id );
	}
        
        /**
	 * Customer new account welcome email.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	function email_admin_new_quote ( $order_id ) {
		if ( ! $order_id )
			return;

		$email = $this->emails['ADQ_Admin_New_Quote'];
		$email->trigger( $order_id );
	}
        
        /**
	 * Customer proposal order.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	function email_customer_proposal ( $order_id ) {
		if ( ! $order_id )
			return;

		$email = $this->emails['ADQ_Customer_Proposal'];
		$email->trigger( $order_id );
	}
        
        /**
	 * Customer proposal reminder.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	function email_remind_proposal ( $order_id ) {
		if ( ! $order_id )
			return;

		$email = $this->emails['ADQ_Proposal_Reminder'];
		$email->trigger( $order_id );
	}
        
        /**
	 * Admin proposal Ok.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	function email_admin_proposal_ok ( $order_id ) {
		if ( ! $order_id )
			return;

		$email = $this->emails['ADQ_Admin_Proposal_Ok'];
		$email->trigger( $order_id );
	}
        
        /**
	 * Customer proposal reminder.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	function email_admin_proposal_ko ( $order_id ) {
		if ( ! $order_id )
			return;

		$email = $this->emails['ADQ_Admin_Proposal_Ko'];
		$email->trigger( $order_id );
	}
        
        /**
	 * Customer proposal reminder.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	function email_customer_proposal_ok ( $order_id ) {
		if ( ! $order_id )
			return;

		$email = $this->emails['ADQ_Customer_Proposal_Ok'];
		$email->trigger( $order_id );
	}
        
        /**
	 * Customer proposal reminder.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	function email_customer_proposal_ko ( $order_id ) {
		if ( ! $order_id )
			return;

		$email = $this->emails['ADQ_Customer_Proposal_Ko'];
		$email->trigger( $order_id );
	}
        
        
        /**
	 * Customer new note.
	 *
	 * @access public
	 * @param int $order_id
         * @param str $customer_note
	 * @return void
	 */
	function email_new_customer_note ( $args ) {
		if ( ! $args['order_id'] )
			return;

		$email = $this->emails['ADQ_Customer_Note'];
		$email->trigger( $args['order_id'], $args['customer_note'] );
                
                $email = $this->emails['ADQ_Admin_Note'];
		$email->trigger( $args['order_id'], $args['customer_note'] );
	}
        
        
        /**
         * Add additional information fields on proposals
         * 
         * @access public
         * @param object $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         * @return void
         */        
        function adq_add_meta_proposal ( $order, $sent_to_admin = false, $plain_text = false ) {
                $meta = array();
                
                $validity_date = get_post_meta( $order->id, '_validity_date', true );
                //$reminder_date = get_post_meta( $order->id, '_reminder_date', true );                
                if ( $validity_date ) {
                        $i = count($meta);
                        $meta[$i]['label'] = __( 'This proposal is valid until' , 'woocommerce-quotation' );
                        $meta[$i]['meta'] = date_i18n( get_option( 'date_format' ), strtotime( $validity_date ) );
                }  
               
                $downloadable_files = get_post_meta( $order->id, '_attached_files', true ); 
                if ( is_array($downloadable_files) && count($downloadable_files) > 0 ) {                   
                    foreach ( $downloadable_files as $key => $file ) {
                            $i = count($meta);
                            $meta[$i]['label'] = __( 'Attached file' , 'woocommerce-quotation' );
                            $meta[$i]['meta'] = '<a href="'.get_home_url()."?order_id=".$order->id."&adq_download_file=".$key.'" title="'.$file["name"].'">'.$file["name"].'</a>';
                    }
                }  
                
                $additional_info = get_post_meta( $order->id, '_adq_additional_info', true );
                if ( $additional_info && $additional_info != "") {
                        $i = count($meta);
                        $meta[$i]['label'] = __( 'Additional information' , 'woocommerce-quotation' );
                        $meta[$i]['meta'] = $additional_info;
                }                 
                
                if ( $plain_text ) {
                        foreach ( $meta as $value ) {
                                if ( $value ) {
                                        echo $value['label'] . ': ' . $value['meta'] . "\n";
                                }
                        }

                } else {

                        foreach ( $meta as $value ) {
                                if ( $value ) {
                                        echo '<p><strong>' . $value['label'] . ':</strong> ' . $value['meta'] . '</p>';
                                }
                        }
                }
        }
}

endif;