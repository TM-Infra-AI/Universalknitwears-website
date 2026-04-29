<?php

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'EDD_ADQ_STORE_URL', 'https://woocommercequoteplugin.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'EDD_ADQ_ITEM_NAME', 'WOOCOMMERCE QUOTATION - REQUEST FOR QUOTE PLUGIN' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

function edd_sl_adq_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'edd_adq_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( EDD_ADQ_STORE_URL, ADQ_PLUGIN_FILE, array( 
			'version' 	=> ADQ_VERSION, 	// current version number
			'license' 	=> $license_key, 	// license key (used get_option above to retrieve from DB)
			'item_name' 	=> EDD_ADQ_ITEM_NAME, 	// name of this plugin
			'author' 	=> 'Aldaba Digital'  	// author of this plugin
		)
	);

}
add_action( 'admin_init', 'edd_sl_adq_updater', 0 );


/************************************
* the code below is just a standard
* options page. Substitute with 
* your own.
*************************************/


function edd_adq_license_page() {
	$license 	= get_option( 'edd_adq_license_key' );
	$status 	= get_option( 'edd_adq_license_status' );
	?>
	<div class="wrap">
		<h2><?php _e('Woocommerce Quotation License Options', 'woocommerce-quotation'); ?></h2>
				
			<?php settings_fields('edd_adq_license'); ?>
			
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('License Key', 'woocommerce-quotation'); ?>
						</th>
						<td>
							<input id="edd_adq_license_key" name="edd_adq_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="edd_adq_license_key"><?php _e('Enter your license key', 'woocommerce-quotation'); ?></label>
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">	
							<th scope="row" valign="top">
								<?php _e('Activate License', 'woocommerce-quotation'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active', 'woocommerce-quotation'); ?></span>
									<?php wp_nonce_field( 'edd_adq_nonce', 'edd_adq_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License', 'woocommerce-quotation'); ?>"/>
								<?php } else {
									wp_nonce_field( 'edd_adq_nonce', 'edd_adq_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License', 'woocommerce-quotation'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>				
	<?php
}

function edd_adq_register_option() { 
        //Hack
        if( isset( $_POST['edd_adq_license_key'] ) ) {            
            update_option( 'edd_adq_license_key', trim( $_POST['edd_adq_license_key'] ));
        }
	// creates our settings in the options table
	register_setting('edd_adq_license', 'edd_adq_license_key', 'edd_adq_sanitize_license' );
}
add_action('admin_init', 'edd_adq_register_option');

function edd_adq_sanitize_license( $new ) {
	$old = get_option( 'edd_adq_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'edd_adq_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}



/************************************
* this illustrates how to activate 
* a license key
*************************************/

function edd_adq_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_activate'] ) ) {

		// run a quick security check 
	 	if( ! check_admin_referer( 'edd_adq_nonce', 'edd_adq_nonce' ) ) 	
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'edd_adq_license_key' ) );
			

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( EDD_ADQ_ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);
		
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_ADQ_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		// $license_data->license will be either "valid" or "invalid"

		update_option( 'edd_adq_license_status', $license_data->license );

	}
}
add_action('admin_init', 'edd_adq_activate_license');


/***********************************************
* Illustrates how to deactivate a license key.
* This will descrease the site count
***********************************************/

function edd_adq_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_deactivate'] ) ) {

		// run a quick security check 
	 	if( ! check_admin_referer( 'edd_adq_nonce', 'edd_adq_nonce' ) ) 	
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'edd_adq_license_key' ) );
			

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'deactivate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( EDD_ADQ_ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);
		
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, EDD_ADQ_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'edd_adq_license_status' );

	}
}
add_action('admin_init', 'edd_adq_deactivate_license');


/************************************
* this illustrates how to check if 
* a license key is still valid
* the updater does this for you,
* so this is only needed if you
* want to do something custom
*************************************/

function edd_adq_check_license() {

	global $wp_version;

	$license = trim( get_option( 'edd_adq_license_key' ) );
		
	$api_params = array( 
		'edd_action' => 'check_license', 
		'license' => $license, 
		'item_name' => urlencode( EDD_ADQ_ITEM_NAME ),
		'url'       => home_url()
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, EDD_ADQ_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );


	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}
