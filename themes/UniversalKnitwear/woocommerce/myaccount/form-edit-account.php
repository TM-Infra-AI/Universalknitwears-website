<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php wc_print_notices(); ?>

<form action="" method="post">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<p class="form-row form-row-first">
		<label for="account_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="form-row form-row-last">
		<label for="account_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</p>
	<div class="clear"></div>
    
	<p class="form-row form-row-first">
		<label for="account_company_name"><?php _e( 'Company Name', 'woocommerce' ); ?> </label>
		<input type="text" class="input-text" name="account_company_name" id="account_company_name" value="<?php echo esc_attr( $user->company_name ); ?>" />
	</p>
	
	<p class="form-row form-row-last">
		<label for="account_company_type"><?php _e( 'Company Type', 'woocommerce' ); ?> </label>
		<input type="text" class="input-text" name="account_company_type" id="account_company_type" value="<?php echo esc_attr( $user->company_type ); ?>" />
	</p>
	
	<p class="form-row form-row-wide">
		<label for="account_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="email" class="input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>
	
	<p class="form-row form-row-first">
		<label for="account_phone"><?php _e( 'Phone', 'woocommerce' ); ?> </label>
		<input type="text" class="input-text" name="account_phone" id="account_phone" value="<?php echo esc_attr( $user->phone ); ?>" />
	</p>
	
	<p class="form-row form-row-last">
		<label for="account_mobile_phone"><?php _e( 'Mobile Phone', 'woocommerce' ); ?> </label>
		<input type="text" class="input-text" name="account_mobile_phone" id="account_mobile_phone" value="<?php echo esc_attr( $user->mobile_phone ); ?>" />
	</p>
	
	<p class="form-row form-row-wide">
		<label for="account_contact_person"><?php _e( 'Contact Person', 'woocommerce' ); ?> </label>
		<input type="text" class="input-text" name="account_contact_person" id="account_contact_person" value="<?php echo esc_attr( $user->contact_person ); ?>" />
	</p>
	
	<p class="form-row form-row-wide">
		<label for="account_awb_account_no"><?php _e( 'AWB Account No.', 'woocommerce' ); ?> </label>
		<input type="text" class="input-text" name="account_awb_account_no" id="account_awb_account_no" value="<?php echo esc_attr( $user->awb_account_no ); ?>" />
	</p>
	<?php  
	$us_nominated_courier = unserialize($user->nominated_courier);
	?>
	<p class="form-row form-row-wide">
            <label for="nominated_courier"><?php _e( 'Nominated Courier details(Example TNT - DHL - etc.)', 'woocommerce' ) ?></label>
                	<input type="checkbox" name="nominated_courier[0]" value="DHL" <?php if(esc_attr( $us_nominated_courier[0] )=='DHL'){echo "checked";} ?>> DHL 
					<input type="checkbox" name="nominated_courier[1]" value="UPS" <?php if(esc_attr( $us_nominated_courier[1] )=='UPS'){echo "checked";} ?>> UPS 
					<input type="checkbox" name="nominated_courier[2]" value="TNT" <?php if(esc_attr( $us_nominated_courier[2] )=='TNT'){echo "checked";} ?>> TNT 
					<input type="checkbox" name="nominated_courier[3]" value="Others" <?php if(esc_attr( $us_nominated_courier[3] )=='Others'){echo "checked";} ?>> Others 
					
    </p>
	
	<p class="form-row form-row-wide">
		<label for="account_country"><?php _e( 'Country', 'woocommerce' ); ?> </label>
		<input type="text" class="input-text" name="account_country" id="account_country" value="<?php echo esc_attr( $user->country ); ?>" />
	</p>
	
	<p class="form-row form-row-wide">
        <label for="address1"><?php _e( 'Address', 'mydomain' ) ?></label>
		<input type="text" name="account_address1" id="account_address1" placeholder="Street address" class="input" value="<?php echo esc_attr( $user->address1 ); ?>" size="25" />
		<input type="text" name="account_address2" id="account_address2" placeholder="Apartment, suite, unit etc. (optional)" class="input" value="<?php echo esc_attr( $user->address2); ?>" size="25" />
                	
    </p>
	
	<p class="form-row form-row-first">
        <label for="post_code"><?php _e( 'Postcode / ZIP', 'mydomain' ) ?></label>
        <input type="text" name="account_post_code" id="account_post_code" class="input-text" value="<?php echo esc_attr( $user->post_code ); ?>" size="25" />
    </p>
	<p  class="form-row form-row-last">
        <label for="city"><?php _e( 'Town / City', 'mydomain' ) ?></label>
        <input type="text" name="account_city" id="account_city" class="input-text" value="<?php echo esc_attr( $user->city ); ?>" size="25" />
    </p>
		

	<fieldset>
		<legend><h3><?php _e( 'Password Change', 'woocommerce' ); ?></h3></legend>

		<p class="form-row form-row-wide">
			<label for="password_current"><?php _e( 'Current Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="input-text" name="password_current" id="password_current" />
		</p>
		<p class="form-row form-row-wide">
			<label for="password_1"><?php _e( 'New Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="input-text" name="password_1" id="password_1" />
		</p>
		<p class="form-row form-row-wide">
			<label for="password_2"><?php _e( 'Confirm New Password', 'woocommerce' ); ?></label>
			<input type="password" class="input-text" name="password_2" id="password_2" />
		</p>
	</fieldset>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details' ); ?>
		<input type="submit" class="button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" />
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

</form>
