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

if ( ! class_exists( 'ADQ_Sections_Crm' ) ) :

/**
 * ADQ_Sections_Options
 */
class ADQ_Sections_Crm extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'crm';
		$this->label = __( 'CRM', 'woocommerce-quotation' );
		
		add_action( 'woocommerce_quotation_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_quotation_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		$settings = apply_filters( 'woocommerce_adq_'.$this->id.'_settings', array(

			array( 'title' => __( 'CRM', 'woocommerce-quotation' ), 'type' => 'title', 'desc' => '', 'id' => 'adq_options' ),		

			array(
				'title'   => __( 'Allow comments on products', 'woocommerce-quotation' ),
				'desc'    => __( 'Allow comments on products in quote list', 'woocommerce-quotation' ),
				'id'      => 'adq_allow_product_comments',
				'default' => 'yes',
				'type'    => 'checkbox'
			),
                        array(
				'title'   => __( 'Allow comments on orders', 'woocommerce-quotation' ),
				'desc'    => __( 'Allow comments on orders in order detail', 'woocommerce-quotation' ),
				'id'      => 'adq_allow_order_comments',
				'default' => 'yes',
				'type'    => 'checkbox'
			),
                        array(
				'title'   => __( 'Display comments on orders', 'woocommerce-quotation' ),
				'desc'    => __( 'Display comments on orders in order detail', 'woocommerce-quotation' ),
				'id'      => 'adq_allow_order_comments_history',
				'default' => 'yes',
				'type'    => 'checkbox'
			),                        
			array( 'type' => 'sectionend', 'id' => 'pricing_options' )

		) );

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

return new ADQ_Sections_Crm();
