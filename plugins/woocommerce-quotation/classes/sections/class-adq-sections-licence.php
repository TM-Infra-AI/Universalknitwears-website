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

if ( ! class_exists( 'ADQ_Sections_Licence' ) ) :

/**
 * Dummy class to instance Edd plugin Licence Manager
 */
class ADQ_Sections_Licence extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'licence';
		$this->label = __( 'Licence', 'woocommerce-quotation' );
		
		add_action( 'woocommerce_quotation_' . $this->id, 'edd_adq_license_page' );
	}
	
}

endif;

return new ADQ_Sections_Licence();
