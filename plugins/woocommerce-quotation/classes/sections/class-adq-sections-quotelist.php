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

if ( ! class_exists( 'ADQ_Sections_Quotelist' ) ) :

/**
 * ADQ_Sections_Options
 */
class ADQ_Sections_Quotelist extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'quotelist';
		$this->label = __( 'Shipping', 'woocommerce-quotation' );
		
		add_action( 'woocommerce_quotation_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_quotation_save_' . $this->id, array( $this, 'save' ) );
                
                add_action( 'woocommerce_update_option_adq_shipping_methods', array( $this, 'adq_shipping_methods_update' ) );
                add_action( 'woocommerce_admin_field_adq_shipping_methods', array( $this, 'adq_shipping_methods_setting' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		$settings = apply_filters( 'woocommerce_adq_'.$this->id.'_settings', array(

			array( 'title' => __( 'Shipping', 'woocommerce-quotation' ), 'type' => 'title', 'desc' => '', 'id' => 'adq_options' ),
			                      
                        array(
				'title'   => __( 'Custom text for shipping', 'woocommerce-quotation' ),
				'desc'    => '',
				'id'      => 'adq_custom_text_shipping',
				'default' => __('I want Quote Request for Shipping', 'woocommerce-quotation'),
				'type'    => 'text',
                                'autoload' => false
			),                        
                        array(
				'title'   => __( 'Inherit the shipping configuration from WooCommerce', 'woocommerce-quotation' ),
				'desc'    => __( 'Enabled', 'woocommerce-quotation' ),
				'id'      => 'adq_inherit_shipping_conf',
				'default' => 'no',
				'type'    => 'checkbox'
			),			
                        array( 'type' => 'subpage_start', 'class' => 'adq_inherit_shipping' ),
                        array( 'title' => '', 'type' => 'title', 'desc' => '', 'id' => 'adq_options' ),
                        array(
				'title'         => __( 'Shipping Calculations', 'woocommerce' ),
				'desc'          => __( 'Enable shipping', 'woocommerce' ),
				'id'            => 'adq_enable_shipping',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start'
			),

			array(
				'desc'          => __( 'Enable the shipping calculator on the cart page', 'woocommerce' ),
				'id'            => 'adq_enable_shipping_calculation',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
				'autoload'      => false
			),

			array(
				'desc'          => __( 'Hide shipping costs until an address is entered', 'woocommerce' ),
				'id'            => 'adq_hide_shipping_cost',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'autoload'      => false
			),

			array(
				'title'    => __( 'Shipping Display Mode', 'woocommerce' ),
				'desc'     => __( 'This controls how multiple shipping methods are displayed on the frontend.', 'woocommerce' ),
				'id'       => 'adq_shipping_method_format',
				'default'  => '',
				'type'     => 'radio',
				'options'  => array(
					''       => __( 'Display shipping methods with "radio" buttons', 'woocommerce' ),
					'select' => __( 'Display shipping methods in a dropdown', 'woocommerce' ),
				),
				'desc_tip' =>  true,
				'autoload' => false
			),

			array(
				'title'   => __( 'Shipping Destination', 'woocommerce' ),
				'desc'    => __( 'This controls which shipping address is used by default.', 'woocommerce' ),
				'id'      => 'adq_ship_to_destination',
				'default' => 'shipping',
				'type'    => 'radio',
				'options' => array(
					'shipping'     => __( 'Default to shipping address', 'woocommerce' ),
					'billing'      => __( 'Default to billing address', 'woocommerce' ),
					'billing_only' => __( 'Only ship to the users billing address', 'woocommerce' ),
				),
				'autoload'        => false,
				'desc_tip'        =>  true,
				'show_if_checked' => 'option',
			),

			array(
				'title'    => __( 'Restrict shipping to Location(s)', 'woocommerce' ),
				'desc'     => sprintf( __( 'Choose which countries you want to ship to, or choose to ship to all <a href="%s">locations you sell to</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=general' ) ),
				'id'       => 'adq_ship_to_countries',
				'default'  => '',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'desc_tip' => false,
				'options'  => array(
					''         => __( 'Ship to all countries you sell to', 'woocommerce' ),
					'all'      => __( 'Ship to all countries', 'woocommerce' ),
					'specific' => __( 'Ship to specific countries only', 'woocommerce' )
				)
			),

			array(
				'title'   => __( 'Specific Countries', 'woocommerce' ),
				'desc'    => '',
				'id'      => 'adq_specific_ship_to_countries',
				'css'     => '',
				'default' => '',
				'type'    => 'multi_select_countries'
			),
			array(
				'type' => 'adq_shipping_methods', 'id' => 'adq_shipping_methods'                                 
			),
                        array( 'type' => 'sectionend', 'id' => 'adq_options' ),
                        array( 'type' => 'subpage_end' ),
                        array( 'type' => 'sectionend', 'id' => 'adq_options' ),

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
        

        public function adq_shipping_methods_update() {
                foreach ( WC()->shipping->load_shipping_methods() as $key => $method ) {
                        $id= 'adq_' . esc_attr( $method->id );
                        $option_name  = $id;
                        $option_value = isset( $_POST[ $id ] ) ? wp_unslash( $_POST[ $id ] ) : null;
                        $option_value = is_null( $option_value ) ? 'no' : 'yes';
                        
                        $update_options [ $option_name ] = $option_value;
                }                                 
                
                // Now save the options
                foreach ( $update_options as $name => $value ) {
                        update_option( $name, $value );
                }
        }        


        public function adq_shipping_methods_setting() {

		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Shipping Methods for Quotation', 'woocommerce-quotation' ) ?></th>
			<td class="forminp">
				<table class="wc_shipping widefat" cellspacing="0">
					<thead>
						<tr>
							<th class="default"><?php _e( 'Enabled', 'woocommerce-quotation' ); ?></th>
							<th class="name"><?php _e( 'Name', 'woocommerce-quotation' ); ?></th>
							<th class="id"><?php _e( 'ID', 'woocommerce-quotation' ); ?></th>
							<th class="settings">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( WC()->shipping->load_shipping_methods() as $key => $method ) {
							echo '<tr>
								<td width="1%" class="default">
									<input type="checkbox" name="adq_' . esc_attr( $method->id ) . '" value="1" ' . checked( get_option( 'adq_'.esc_attr( $method->id ), $method->enabled ), "yes", false ) . ' />									
								</td>
								<td class="name">
									' . $method->get_title() . '
								</td>
								<td class="id">
									' . $method->id . '
								</td>';
								
							echo '<td class="settings">';

							if ( $method->has_settings ) {
								echo '<a class="button" href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . strtolower( get_class( $method ) ) ) . '">' . __( 'Settings', 'woocommerce-quotation' ) . '</a>';
							}

							echo '</td>
							</tr>';
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}       

}

endif;

return new ADQ_Sections_Quotelist();
