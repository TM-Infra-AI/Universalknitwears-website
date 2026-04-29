<?php
/**
 * Override WooCommerce Shipping Class
 *
 * Handles shipping and loads shipping methods via hooks.
 *
 * @class 		WC_Shipping
 * @version		1.2.0
 * @package		woocommerce-quotation/classes/
 * @category            Class
 * @author 		Aldaba Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ADQ_Shipping extends WC_Shipping {
    
        
	/**
	 * calculate_shipping_for_package function.
	 *
	 * Calculates each shipping methods cost. Rates are cached based on the package to speed up calculations.
	 *
	 * @access public
	 * @param array $package cart items
	 * @return array
	 * @todo Return array() instead of false for consistent return type?
	 */
	public function calculate_shipping_for_package( $package = array() ) {
		if ( ! $this->enabled ) return false;
		if ( ! $package ) return false;

		// Check if we need to recalculate shipping for this package
		$package_hash   = 'adq_ship_' . md5( json_encode( $package ) . WC_Cache_Helper::get_transient_version( 'shipping' ) );
		$status_options = get_option( 'woocommerce_status_options', array() );
                
                $status_options['shipping_debug_mode'] = 1;
                
		if ( false === ( $stored_rates = get_transient( $package_hash ) ) || ( ! empty( $status_options['shipping_debug_mode'] ) && current_user_can( 'manage_options' ) ) ) {                

			// Calculate shipping method rates
			$package['rates'] = array();                        

			foreach ( $this->load_shipping_methods( $package ) as $shipping_method ) {

				if ( ( ( get_option("adq_inherit_shipping_conf") == "no" && get_option( 'adq_'.esc_attr( $shipping_method->id ), $shipping_method->enabled ) == "yes" )
                                        || ( get_option("adq_inherit_shipping_conf") == "yes" && $shipping_method->is_available( $package ) )
                                        ) && ( empty( $package['ship_via'] ) || in_array( $shipping_method->id, $package['ship_via'] ) ) ) {

					// Reset Rates
					$shipping_method->rates = array();

					// Calculate Shipping for package
					$shipping_method->calculate_shipping( $package );

					// Place rates in package array
					if ( ! empty( $shipping_method->rates ) && is_array( $shipping_method->rates ) )
						foreach ( $shipping_method->rates as $rate )
							$package['rates'][ $rate->id ] = $rate;
				}
			}

			// Filter the calculated rates
			$package['rates'] = apply_filters( 'woocommerce_package_rates', $package['rates'], $package );

			// Store
			set_transient( $package_hash, $package['rates'], 60 * 60 ); // Cached for an hour

		} else {

			$package['rates'] = $stored_rates;

		}

		return $package;
	}
        
        
        /**
	 * load_shipping_methods function.
	 *
	 * Loads all shipping methods which are hooked in. If a $package is passed some methods may add themselves conditionally.
	 *
	 * Methods are sorted into their user-defined order after being loaded.
	 *
	 * @access public
	 * @param array $package
	 * @return array
	 */
	public function load_shipping_methods( $package = array() ) {

		$this->unregister_shipping_methods();

		$this->shipping_methods = WC()->shipping->load_shipping_methods( $package );

		return $this->shipping_methods;
	}

}