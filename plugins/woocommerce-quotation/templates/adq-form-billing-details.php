<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$is_billing_filled = (!is_null($checkout->get_value( "billing_first_name" )) && is_user_logged_in());

?>

<?php

    if($is_billing_filled) {
            $info_message  =  __( 'Want to change billing information?', 'woocommerce-quotation' );
            $info_message .= ' <a href="#" class="showbilling">' . __( 'Click here to show billing information', 'woocommerce-quotation' ) . '</a>';
            wc_print_notice( $info_message, 'notice' );
    }

?>

    <div class="woocommerce-billing-fields adq-billing"  style="<?php echo $is_billing_filled?"display:none":"" ?>">
        <h3><?php _e( 'Details', 'woocommerce-quotation' ); ?></h3>

        <?php if ( ! is_user_logged_in() && get_option("adq_enable_registration") == "yes" ) : ?>
        
                <?php if ( get_option("adq_enable_guest_request") == "no" ) : ?>        
                        
                        <p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'woocommerce-quotation' ); ?></p>

                        <?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) : ?>                                    

                                <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

                        <?php endforeach; ?>

                        <div class="clear"></div>
        
                <?php else : ?>

                        <?php if ( get_option("adq_enable_registration") == "yes" ) : ?>

                                <p class="form-row form-row-wide create-account">
                                        <input class="input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'woocommerce-quotation' ); ?></label>
                                </p>

                        <?php endif; ?>

                        <div class="create-account">

                                <p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'woocommerce-quotation' ); ?></p>

                                <?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) : ?>                                    

                                        <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

                                <?php endforeach; ?>

                                <div class="clear"></div>

                        </div>
                                
                <?php endif; ?>

        <?php endif; ?>

        <?php foreach ( $checkout->checkout_fields['billing'] as $key => $field ) {
                    
                    if( isset( $field["visible"] ) && $field["visible"] ) {
                           woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
                    }                     
            }
        ?>
    </div>