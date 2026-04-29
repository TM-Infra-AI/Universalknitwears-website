<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Quote List Widget
 *
 * Displays shopping quote list widget
 *
 * @author   Aldaba Digital
 * @category Widgets
 * @package  Classes/Widgets
 * @version  2.1.10
 * @extends  WC_Widget
 */
class WC_Widget_Quote extends WC_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_shopping_cart';
		$this->widget_description = __( "Display the user's Quote List in the sidebar.", 'woocommerce-quotation' );
		$this->widget_id          = 'woocommerce_widget_quote';
		$this->widget_name        = __( 'WooCommerce Quotation', 'woocommerce-quotation' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Quote List', 'woocommerce-quotation' ),
				'label' => __( 'Title', 'woocommerce-quotation' )
			),
			'hide_if_empty' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show simple widget version', 'woocommerce-quotation' )
			)
		);

		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {            
                
		$simple_quote_list = empty( $instance['hide_if_empty'] ) ? 0 : 1;

		$this->widget_start( $args, $instance );
                
                // Get mini cart
		ob_start();

                $simple_quote_list?adq_simple_mini_cart():adq_mini_cart();		

		$mini_quote = ob_get_clean();		              

		// Insert cart widget placeholder - code in woocommerce.js will update this on page load
		echo '<div class="widget_shopping_quote_list_content">'.$mini_quote.'</div>';

		$this->widget_end( $args );
	}
}
