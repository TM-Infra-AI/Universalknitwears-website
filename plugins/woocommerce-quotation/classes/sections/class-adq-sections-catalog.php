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

if ( ! class_exists( 'ADQ_Sections_Catalog' ) ) :

/**
 * ADQ_Sections_Options
 */
class ADQ_Sections_Catalog extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'catalog';
		$this->label = __( 'Catalog', 'woocommerce-quotation' );
		
		add_action( 'woocommerce_quotation_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_quotation_save_' . $this->id, array( $this, 'save' ) );
                
                add_action( 'wp_ajax_adq_taxonomy_load', array( $this, 'ajax_taxonomy_load' ) );
                add_action( 'wp_ajax_nopriv_adq_taxonomy_load', array( $this, 'ajax_taxonomy_load' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {            
                global $wp_roles;

                if ( ! isset( $wp_roles ) ) {
                        $wp_roles = new WP_Roles();
                }

                $roles = array_merge ( array('unregistered' => 'Unregistered', 'loggedin' => 'Logged in') , $wp_roles->get_names());
                
                $default_roles = array_keys($wp_roles->get_names());
                $default_roles[] = 'unregistered';
                $default_roles[] = 'loggedin';
                
                $terms_label = array();
                $taxanomies[''] = __('No taxonomy', 'woocommerce-quotation');                
                foreach ( get_object_taxonomies('product','') as $taxonomy ) {
                        $terms = get_terms( $taxonomy->name, 'hide_empty=0' );
                        if( $terms && count($terms) > 0) {
                                $taxanomies[$taxonomy->name] = $taxonomy->label;
                                foreach ( $terms as $term ) {                                        
                                        $terms_label[$taxonomy->name][$term->name] = $term->name;
                                }
                        }
                }

		$settings = array(

			array( 'title' => __( 'Buttons', 'woocommerce-quotation' ), 'type' => 'title', 'desc' => '', 'id' => 'adq_options' ),		

			array(
				'title'   => __( 'Visibility add to quote button', 'woocommerce-quotation' ),
				'desc'    => __( 'Choose the roles can view...', 'woocommerce-quotation' ),
				'id'      => 'adq_visibility_quote',
                                'class'   => 'wc-enhanced-select',
                                'css'     => 'min-width:300px;',
				'options' => $roles,
                                'default' => $default_roles,
				'type'    => 'multiselect'
			),
                        array(
				'title'   => __( 'Visibility price on shop', 'woocommerce-quotation' ),
				'desc'    => __( 'Choose the roles can view...', 'woocommerce-quotation' ),
				'id'      => 'adq_visibility_price',
                                'class'   => 'wc-enhanced-select',
                                'css'     => 'min-width:300px;',
				'options' => $roles,
                                'default' => array(),
				'type'    => 'multiselect'
			),
                        array(
				'title'   => __( 'Visibility add to cart button', 'woocommerce-quotation' ),
				'desc'    => __( 'Choose the roles can view...', 'woocommerce-quotation' ),
				'id'      => 'adq_visibility_cart',
                                'class'   => 'wc-enhanced-select',
                                'css'     => 'min-width:300px;',
				'options' => $roles,
                                'default' => array(),
				'type'    => 'multiselect'
			),                                               
                        array(
				'title'   => __( 'Visibility add to quote button on loop', 'woocommerce-quotation' ),
				'desc'    => __( 'Visible', 'woocommerce-quotation' ),
				'id'      => 'adq_general_quote_loop',
				'default' => 'yes',
				'type'    => 'checkbox'
			),
                        array(
				'title'   => __( 'Text to show on "add to quote" button', 'woocommerce-quotation' ),
				'desc'    => '',
				'id'      => 'adq_quote_text',
				'default' => __('Add to quote', 'woocommerce-quotation'),
				'type'    => 'text'
			),
                        /*array(
				'title'    => __( 'Filter aplication', 'woocommerce' ),
				'desc'     => __( 'Choose how to apply the filters', 'woocommerce' ),
				'id'       => 'adq_role_filter',
				'default'  => 'all',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'desc_tip' => false,
				'options'  => array(
					'one' => __( 'Visible if match only one requirement', 'woocommerce' ),
					'all' => __( 'Only visible if match all requirements', 'woocommerce' )
				)
			),*/
                        array(
				'title'    => __( 'Choose visibility by taxonomy', 'woocommerce-quotation' ),
				'desc'     => __( 'Select taxonomy to filter buttons and price visibility by terms.', 'woocommerce-quotation' ),
				'id'       => 'adq_taxonomy_filter',
				'default'  => '',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'desc_tip' => false,
				'options'  => $taxanomies,				
			)                        
                );
                
                /*foreach ( $terms_label as $taxonomy => $terms ) {
                        $default_terms = array_keys($terms);
                        
                        $settings[] = array( 'type' => 'subpage_start', 'class' => 'adq_'.$taxonomy.'_terms adq_terms_visibility' );
                        $settings[] = array( 'title' => '', 'type' => 'title', 'desc' => '', 'id' => 'adq_options' );
                                
                        $settings[] =
                                array(
                                        'title'   => __( 'Visibility add to quote button', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose the terms...', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_quote',
                                        'class'   => 'wc-enhanced-select',
                                        'css'     => 'min-width:300px;',
                                        'options' => $terms,
                                        'default' => $default_terms,
                                        'type'    => 'multiselect'
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Exclude terms', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose if terms must be included or excluded', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_quote_exclude',
                                        'default' => 'no',
                                        'type'    => 'checkbox',
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Visibility price on shop', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose the the roles can view...', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_price',
                                        'class'   => 'wc-enhanced-select',
                                        'css'     => 'min-width:300px;',
                                        'options' => $terms,
                                        'default' => $default_terms,
                                        'type'    => 'multiselect'
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Exclude terms', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose if terms must be included or excluded', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_price_exclude',
                                        'default' => 'no',
                                        'type'    => 'checkbox',
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Visibility add to cart button', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose the the roles can view...', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_cart',
                                        'class'   => 'wc-enhanced-select',
                                        'css'     => 'min-width:300px;',
                                        'options' => $terms,
                                        'default' => $default_terms,
                                        'type'    => 'multiselect'
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Exclude terms', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose if terms must be included or excluded', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_cart_exclude',
                                        'default' => 'no',
                                        'type'    => 'checkbox',
                                );
                        
                        $settings[] = array( 'type' => 'sectionend', 'id' => 'adq_options' );
                        $settings[] = array( 'type' => 'subpage_end' );                
                }                
                */
                $settings = apply_filters( 'woocommerce_adq_'.$this->id.'_settings', $settings);
                
		$settings[] = array( 'type' => 'sectionend', 'id' => 'adq_options' );
                
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}
        
        
        /**
	 * Get settings save array
	 *
	 * @return array
	 */
	public function get_save_settings( $taxonomy ) {  
            
                if( !$taxonomy || $taxonomy == '')
                    return array();
                
                $taxonomy = get_taxonomy( $taxonomy );
            
                $settings = array();
                $terms_label = array();
                
                $terms = get_terms( $taxonomy->name, 'hide_empty=0' );
                if( $terms && count($terms) > 0) {
                        foreach ( $terms as $term ) {                                        
                                $terms_label[$taxonomy->name][$term->name] = $term->name;
                        }
                }		
                
                foreach ( $terms_label as $taxonomy => $terms ) {
                        $default_terms = array_keys($terms);
                        
                        $settings[] = array( 'type' => 'subpage_start', 'class' => 'adq_'.$taxonomy.'_terms adq_terms_visibility' );
                        $settings[] = array( 'title' => '', 'type' => 'title', 'desc' => '', 'id' => 'adq_options' );
                                
                        $settings[] =
                                array(
                                        'title'   => __( 'Visibility add to quote button', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose the terms...', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_quote',
                                        'class'   => 'wc-enhanced-select',
                                        'css'     => 'min-width:300px;',
                                        'options' => $terms,
                                        'default' => $default_terms,
                                        'type'    => 'multiselect'
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Exclude terms', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose if terms must be included or excluded', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_quote_exclude',
                                        'default' => 'no',
                                        'type'    => 'checkbox',
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Visibility price on shop', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose the the roles can view...', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_price',
                                        'class'   => 'wc-enhanced-select',
                                        'css'     => 'min-width:300px;',
                                        'options' => $terms,
                                        'default' => $default_terms,
                                        'type'    => 'multiselect'
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Exclude terms', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose if terms must be included or excluded', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_price_exclude',
                                        'default' => 'no',
                                        'type'    => 'checkbox',
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Visibility add to cart button', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose the the roles can view...', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_cart',
                                        'class'   => 'wc-enhanced-select',
                                        'css'     => 'min-width:300px;',
                                        'options' => $terms,
                                        'default' => $default_terms,
                                        'type'    => 'multiselect'
                                );
                        $settings[] =
                                array(
                                        'title'   => __( 'Exclude terms', 'woocommerce-quotation' ),
                                        'desc'    => __( 'Choose if terms must be included or excluded', 'woocommerce-quotation' ),
                                        'id'      => 'adq_'.$taxonomy.'_cart_exclude',
                                        'default' => 'no',
                                        'type'    => 'checkbox',
                                );
                        
                        $settings[] = array( 'type' => 'sectionend', 'id' => 'adq_options' );
                        $settings[] = array( 'type' => 'subpage_end' );                
                }                             
                
		return $settings;
	}

	/**
	 * Save settings
	 */
	public function save() {
                
		$settings = $this->get_save_settings( $_REQUEST['adq_taxonomy_filter'] );
                $settings = array_merge( $settings, $this->get_settings() );
                
		WC_Admin_Settings::save_fields( $settings );
	}
        
        /*
         * Ajax load of taxonomy to avoid exceed POST limits
         */
        public function ajax_taxonomy_load() {
                if(!isset($_POST['taxonomy']))
                    return '';
                
                $settings = $this->get_save_settings( $_POST['taxonomy'] );
                        
                WC_Admin_Settings::output_fields( $settings );
        }

}

endif;

return new ADQ_Sections_Catalog();
