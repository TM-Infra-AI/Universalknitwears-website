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

if ( ! class_exists( 'ADQ_Settings_Page' ) ) :

/**
 * ADQ_Sections_Options
 */
class ADQ_Settings_Page  {

        protected static $sections;
        
        protected $id;


        public function __construct() {
                $this->id='quotes';
            
                add_action( 'woocommerce_settings_'.$this->id, array( $this, 'adq_admin_settings_tab' ) );
                add_action( 'woocommerce_sections_'.$this->id, array( $this, 'adq_output_sections' ) );
                add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
                
                add_action( 'woocommerce_admin_field_subpage_start', array( $this, 'subpage_start' ) );
                add_action( 'woocommerce_admin_field_subpage_end', array( $this, 'subpage_end' ) );
                
                add_filter( 'woocommerce_settings_tabs_array', array( $this, 'adq_add_settings_page' ), 30 );
                
                self::init();
        }
           
        public static function init() {
            
                self::$sections = array();

                include_once( WC()->plugin_path().'/includes/admin/settings/class-wc-settings-page.php' );

                self::$sections[] = include( ADQ_QUOTE_DIR.'classes/sections/class-adq-sections-options.php' );
                self::$sections[] = include( ADQ_QUOTE_DIR.'classes/sections/class-adq-sections-catalog.php' );
                self::$sections[] = include( ADQ_QUOTE_DIR.'classes/sections/class-adq-sections-quotelist.php' );
                self::$sections[] = include( ADQ_QUOTE_DIR.'classes/sections/class-adq-sections-form.php' );
                self::$sections[] = include( ADQ_QUOTE_DIR.'classes/sections/class-adq-sections-crm.php' );
                self::$sections[] = include( ADQ_QUOTE_DIR.'classes/sections/class-adq-sections-licence.php' );

                return apply_filters( 'woocommerce_adq_get_sections_pages', self::$sections );
        }
        
        
        public function adq_add_settings_page( $pages ) {
                $pages[ 'quotes' ] = __('Quotation','woocommerce-quotation');

		return $pages;
        }
        
        public function adq_admin_settings_tab() {
                global $current_section;                               
                
                if( $current_section == '' ) {
                    $current_section = 'options';
                }
                
                do_action ('woocommerce_quotation_'.$current_section);
                
        }
        
        public function adq_output_sections() {
		global $current_section;
                
                $sections = $this->adq_get_section_pages();

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $section ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=quotes&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $section . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	} 
        
        public function adq_get_section_pages() {		
                
                $section_pages = array();

                $sections = self::get_sections_pages();

                foreach($sections as $section) {
                        $section_pages = $section->add_settings_page( $section_pages );
                }

		return $section_pages;
	}
        
        public static function get_sections_pages() {
            
                return apply_filters( 'woocommerce_adq_get_sections_pages', self::$sections );
        }

        public function save() {
                global $current_section;                               
                
                if( $current_section == '' ) {
                    $current_section = 'options';
                }
                
                do_action ('woocommerce_quotation_save_'.$current_section);
        }
        
        public function subpage_start( $value ) {
                if( !isset( $value['display'] ) ) {
                    $value['display'] = 'none';
                }
                echo '<tr style="display:'.$value['display'].'" class="form-field form-required '.( isset($value['class'])?$value['class']:'' ).'">
                            <td colspan="2">';
        }
        
        public function subpage_end( $value ) {
            
                echo  '</td></tr>';
        }        
        
}

endif;

return new ADQ_Settings_Page();