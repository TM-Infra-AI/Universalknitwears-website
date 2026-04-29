<?php

/**
 * The template for order notes.
 * 
 *
 * @author 	Aldaba Digital
 * @package 	woocommerce-quotation/templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<div class="add_note">
        <h4><?php _e( 'Add note', 'woocommerce-quotation' ); ?> <img class="help_tip" data-tip='<?php esc_attr_e( 'Add a note for your reference, or add a customer note (the user will be notified).', 'woocommerce-quotation' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></h4>
        <p>
                <textarea type="text" name="order_note" id="add_order_note" class="input-text" cols="20" rows="5"></textarea>
        </p>
        <p>
                <input type="hidden" name="order_note_type" id="order_note_type" value="customer" />
                
                <a href="#" data-post_id="<?php echo $order_id ?>" class="add_note button"><?php _e( 'Add', 'woocommerce-quotation' ); ?></a>
        </p>
</div>