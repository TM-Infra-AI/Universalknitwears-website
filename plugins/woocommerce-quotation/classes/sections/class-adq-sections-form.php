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

if ( ! class_exists( 'ADQ_Sections_Form' ) ) :

/**
 * ADQ_Sections_Options
 */
class ADQ_Sections_Form extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'form';
		$this->label = __( 'Form', 'woocommerce-quotation' );
		
		add_action( 'woocommerce_quotation_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_quotation_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
                if( !is_object( WC()->countries ) ) {
                        // Class instances
                        WC()->countries = new WC_Countries(); 
                }            

		$settings = array();
                
		$settings[] = array( 'title' => __( 'Form', 'woocommerce-quotation' ), 'type' => 'title', 'desc' => '', 'id' => 'adq_options' );

                $fields = WC()->countries->get_address_fields( 'billing_' );
                
                foreach ( $fields as $key => $field ) {
                        if( $key == 'billing_state' || $key == 'billing_email' ) {
                                continue;
                        }
                        
                        $mandatory = StaticAdqQuoteRequest::shipping_required_fields( $key, 'billing' );

                        if ( $mandatory ) {
                                update_option('adq_'.$key.'_visible', 'yes');
                                update_option('adq_'.$key.'_required', 'yes');
                        } 
                        
                        $default_visibility = array ('billing_first_name', 'billing_email');
                        $default_required = array ('billing_email');
                        
                        $settings[] =array(
				'title'   => ( isset($field ["label"]) && $field ["label"] != "" )?$field ["label"]:$key,
				'desc'    => $mandatory?__( 'Cannot modify beacause this field is required for shipping', 'woocommerce-quotation' ):'Visible',
				'id'      => 'adq_'.$key.'_visible',
				'default' => isset($default_visibility[$key])?'yes':'no',
				'type'    => 'checkbox',
                                'checkboxgroup' => 'start',
                                'custom_attributes' => $mandatory?array('readonly' => 'readonly'):array(),
			);
                        
                        $settings[] =array(
				'title'   => '',
				'desc'    => $mandatory?__( 'Cannot modify beacause this field is required for shipping', 'woocommerce-quotation' ):'Required',
				'id'      => 'adq_'.$key.'_required',
				'default' => isset($default_required[$key])?'yes':'no',
				'type'    => 'checkbox',
                                'checkboxgroup' => 'end',
                                'custom_attributes' => $mandatory?array('readonly' => 'readonly'):array(),
			);                        
                        
                }
                
                $settings = apply_filters( 'woocommerce_adq_'.$this->id.'_settings', $settings);
			
		$settings[] = array( 'type' => 'sectionend', 'id' => 'pricing_options' );                                                              

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

return new ADQ_Sections_Form();
