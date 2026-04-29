<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'ADQ_Customer_Note' ) ) :

/**
 * Customer Quote email
 *
 * @class 	ADQ_Customer_Note
 * @version     1.0.0
 * @package     woocommerce-quotation/classes/emails
 * @category    Class
 * @author      Aldaba Digital
 * @extends     WC_Email
 */
class ADQ_Customer_Note extends WC_Email {

        public $customer_note;
        
	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 		= 'adq_customer_note';
		$this->title 		= __( 'Customer note', 'woocommerce-quotation' );
		$this->description	= __( 'Customer note emails are sent when you add a note to an order.', 'woocommerce-quotation' );

		$this->heading 		= __( 'A customer have added a note', 'woocommerce-quotation' );
		$this->subject      	= __( 'Note added to your {site_title} order from {order_date}', 'woocommerce-quotation' );

		$this->template_html 	= 'emails/customer-note.php';
		$this->template_plain 	= 'emails/plain/customer-note.php';

		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $order_id, $customer_note ) {

		if ( $order_id ) {
			$this->object 		= wc_get_order( $order_id );
                        $this->customer_note    = $customer_note;
			$this->recipient	= $this->object->billing_email;

			$this->find['order-date']      = '{order_date}';
			$this->find['order-number']    = '{order_number}';

			$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
			$this->replace['order-number'] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	function get_subject() {
		return $this->format_string( $this->subject );
	}

	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	function get_heading() {
		return $this->format_string( $this->heading );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		adq_get_template( $this->template_html, array(
			'order' 	=> $this->object,
                        'customer_note' => $this->customer_note,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false
		) );
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		adq_get_template( $this->template_plain, array(
			'order' 	=> $this->object,
                        'customer_note' => $this->customer_note,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true
		) );
		return ob_get_clean();
	}

    /**
     * Initialise Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
    	$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'woocommerce-quotation' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable this email notification', 'woocommerce-quotation' ),
				'default' 		=> 'yes'
			),
			'subject' => array(
				'title' 		=> __( 'Subject', 'woocommerce-quotation' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce-quotation' ), $this->subject ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'heading' => array(
				'title' 		=> __( 'Email Heading', 'woocommerce-quotation' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce-quotation' ), $this->heading ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),			
			'email_type' => array(
				'title' 		=> __( 'Email type', 'woocommerce-quotation' ),
				'type' 			=> 'select',
				'description' 	=> __( 'Choose which format of email to send.', 'woocommerce-quotation' ),
				'default' 		=> 'html',
				'class'			=> 'email_type',
				'options'		=> array(
					'plain'	 	=> __( 'Plain text', 'woocommerce-quotation' ),
					'html' 			=> __( 'HTML', 'woocommerce-quotation' ),
					'multipart' 	=> __( 'Multipart', 'woocommerce-quotation' ),
				)
			)
		);
    }
}

endif;

return new ADQ_Customer_Note();
