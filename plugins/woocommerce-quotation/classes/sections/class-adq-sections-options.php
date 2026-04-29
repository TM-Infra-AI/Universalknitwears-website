<?php
/**
 * WooCommerce General Settings
 *
 * @author      Aldaba Digital
 * @category    Admin
 * @package     woocommerce-quotation/classes/
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'ADQ_Sections_Options' ) ) :

/**
 * ADQ_Sections_Options
 */
class ADQ_Sections_Options extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'options';
		$this->label = __( 'Options', 'woocommerce-quotation' );
		
		add_action( 'woocommerce_quotation_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_quotation_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		$settings = array(

			array( 'title' => __( 'Options', 'woocommerce-quotation' ), 'type' => 'title', 'desc' => '', 'id' => 'adq_options' ),		

			array(
				'title'   => __( 'Enable guest request', 'woocommerce-quotation' ),
				'desc'    => __( 'Allow customers to request for a quote without creating an account', 'woocommerce-quotation' ),
				'id'      => 'adq_enable_guest_request',
				'default' => 'yes',
				'type'    => 'checkbox'
			),
                        array(
				'title'   => __( 'Display discount', 'woocommerce-quotation' ),
				'desc'    => __( 'Show original price on products with discount', 'woocommerce-quotation' ),
				'id'      => 'adq_display_discount',
				'default' => 'no',
				'type'    => 'checkbox'
			),
                        array(
				'title'   => __( 'After add to quote', 'woocommerce-quotation' ),
				'desc'    => __( 'After Add to Quote', 'woocommerce-quotation' ),
				'id'      => 'adq_redirect_quote_page',
				'default' => 'yes',
				'type'    => 'radio',
				'options' => array(
					'yes'   => __( 'Redirect to quote list page', 'woocommerce-quotation' ),					
					'no'    => __( 'Show notice', 'woocommerce-quotation' ),
                                        'show'  => __( 'Show notice under quote button', 'woocommerce-quotation' ),
				),
				'autoload'        => false,
				'desc_tip'        =>  true,
				'show_if_checked' => 'option',
			),
                        array(
				'title'   => __( 'Redirect to payment when proposal is accepted', 'woocommerce-quotation' ),
				'desc'    => __( 'Redirect', 'woocommerce-quotation' ),
				'id'      => 'adq_redirect_payment',
				'default' => 'yes',
				'type'    => 'checkbox'
			),
                        array(
				'title'   => __( 'URL Custom redirects after submitting a quote', 'woocommerce-quotation' ),
				'desc'    => __( 'Leave blank to keep the default redirection', 'woocommerce-quotation' ),
				'id'      => 'adq_redirect_after_quote',
				'default' => '',
				'type'    => 'text',
                                'autoload' => false
			),
                        array(
				'title'   => __( 'Automatic proposal', 'woocommerce-quotation' ),
				'desc'    => __( 'Create automatic proposal after request', 'woocommerce-quotation' ),
				'id'      => 'adq_automatic_proposal',
				'default' => 'no',
				'type'    => 'checkbox'
			),
                        array(
				'title'   => __( 'Display returning customer login reminder', 'woocommerce-quotation' ),
				'desc'    => __( 'Enabled', 'woocommerce-quotation' ),
				'id'      => 'adq_enable_login_reminder',
				'default' => 'yes',
				'type'    => 'checkbox'
			),  
                        array(
				'title'   => __( 'Enable registration on the "Quote List" page', 'woocommerce-quotation' ),
				'desc'    => __( 'Enabled', 'woocommerce-quotation' ),
				'id'      => 'adq_enable_registration',
				'default' => 'yes',
				'type'    => 'checkbox'
			),
                        array(
				'title'   => __( 'Ajax error tolerance', 'woocommerce-quotation' ),
				'desc'    => __( 'Enabled', 'woocommerce-quotation' ),
				'id'      => 'adq_ajax_error_tolerance',
				'default' => 'yes',
				'type'    => 'checkbox'
			),
                );
                
                if( wc_get_page_id( 'terms' ) > 0 ) {
                        $settings[] =
                                array(
                                        'title'   => __( 'Show Terms', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Show Terms & Conditions on Quote List Page', 'woocommerce-quotation' ),
                                        'id'      => 'adq_show_terms',
                                        'default' => 'no',
                                        'type'    => 'checkbox'
                                );
                }
                
                $settings = array_merge($settings, array (
                        array(
				'title'   => __( 'Disable Checkout Hooks:', 'woocommerce-quotation' ),
				'desc'    => __( 'Disable some checkout hooks to avoid some plugins actions on Quote List', 'woocommerce-quotation' ),
				'id'      => 'adq_disable_checkout_hooks',
				'default' => 'no',
				'type'    => 'checkbox'
			),
                        array(
				'title'   => __( 'Default validity date:', 'woocommerce-quotation' ),
				'desc'    => __( 'Days before proposal expires', 'woocommerce-quotation' ),
				'id'      => 'adq_proposal_validity',
				'default' => '15',
				'type'    => 'text',
                                'autoload' => false
			),
                        array(
				'title'   => __( 'Default reminder date:', 'woocommerce-quotation' ),
				'desc'    => __( 'Days before reminder mail is send', 'woocommerce-quotation' ),
				'id'      => 'adq_proposal_reminder',
				'default' => '1',
				'type'    => 'text',
                                'autoload' => false
			),
			array( 'type' => 'sectionend', 'id' => 'pricing_options' )

		) );
                
                        
                $settings = apply_filters( 'woocommerce_adq_'.$this->id.'_settings', $settings);

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();

		WC_Admin_Settings::save_fields( $settings );
	}

}

endif;

return new ADQ_Sections_Options();
