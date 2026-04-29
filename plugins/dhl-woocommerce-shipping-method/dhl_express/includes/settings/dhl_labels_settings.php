<?php $this->init_settings(); 
global $woocommerce;
$wc_main_settings = array();
$print_size = array('8X4_A4_PDF'=>'8X4_A4_PDF','8X4_thermal'=>'8X4_thermal','8X4_A4_TC_PDF'=>'8X4_A4_TC_PDF','8X4_CI_PDF'=>'8X4_CI_PDF','8X4_CI_thermal'=>'8X4_CI_thermal','8X4_RU_A4_PDF'=>'8X4_RU_A4_PDF','8X4_PDF'=>'8X4_PDF','8X4_CustBarCode_PDF'=>'8X4_CustBarCode_PDF','8X4_CustBarCode_thermal'=>'8X4_CustBarCode_thermal','6X4_A4_PDF'=>'6X4_A4_PDF','6X4_thermal'=>'6X4_thermal','6X4_PDF'=>'6X4_PDF');
$printer_doc_type = array('PDF'=>'PDF Output','ZPL2'=>'ZPL2 Output','EPL2'=>'EPL2 Output');
$duty_payment_type = array(''=>'None','S' =>__('Shipper','wf-shipping-dhl'),'R' =>__('Recipient','wf-shipping-dhl'),'T' =>__('Third Party/Other','wf-shipping-dhl'));
$general_settings = get_option('woocommerce_wf_dhl_shipping_settings');
?>
<style>
.isa_info, .isa_success, .isa_warning, .isa_error {
margin: 10px 0px;
padding:12px;
 
}
.isa_error {
    color: #D8000C;
    background-color: #FFD2D2;
}
.isa_info i, .isa_success i, .isa_warning i, .isa_error i {
    margin:10px 22px;
    font-size:2em;
    vertical-align:middle;
}

</style>
<div class="isa_error">
  <span class="dashicons dashicons-editor-help"></span>
   This section contains a lot of settings and customization options for labels and tracking. This feature available only in <a href="<?php echo admin_url( 'admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_shipping&subtab=premium' ); ?>">premium version</a>
</div>

<table id="xa_labels_account_settings">
	<tr valign="top" ">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Enable/Disable','wf-shipping-dhl') ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
		<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_plt" id="wf_dhl_shipping_plt" style="" value="yes" <?php echo (isset($general_settings['plt']) && $general_settings['plt'] ==='yes') ? 'checked' : ''; ?> placeholder=""> <?php _e('Enable PaperLess Trade (PLT)','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e("On enabling this, DHL's paperless trade feature will be activated and a receipt will be generated as a commercial invoice.",'wf-shipping-dhl') ?>" ></span>
		</fieldset>
		<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_enable_saturday_delivery" id="wf_dhl_shipping_enable_saturday_delivery" style="" value="yes" <?php echo (isset($general_settings['enable_saturday_delivery']) && $general_settings['enable_saturday_delivery'] ==='yes') ? 'checked' : ''; ?> placeholder="">  <?php _e('Enable Saturday Delivery (SD)','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Special service. On activating this feature, the shipment can be delivered on Saturdays.','wf-shipping-dhl') ?> " ></span>
		</fieldset>
		<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_cash_on_delivery" id="wf_dhl_shipping_cash_on_delivery" style="" value="yes" <?php echo (isset($general_settings['cash_on_delivery']) && $general_settings['cash_on_delivery'] ==='yes') ? 'checked' : ''; ?> placeholder="">  <?php _e('Enable Cash On Delivery (COD)','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Special service. On activating this option, the shipment is created with Cash on delivery option.','wf-shipping-dhl') ?>" ></span>
		</fieldset>
		
		<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_show_front_end_shipping_method" id="wf_dhl_shipping_show_front_end_shipping_method" style="" value="yes" <?php echo (isset($general_settings['show_front_end_shipping_method']) && $general_settings['show_front_end_shipping_method'] ==='yes') ? 'checked' : ''; ?> placeholder="">  <?php _e('Enable Default Service for Label Generation','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('On enabling this option, the services shown in the cart/checkout page will only be reflected while creating shipment.','wf-shipping-dhl') ?>" ></span>
		</fieldset>
		<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_services_select" id="wf_dhl_shipping_services_select" style="" value="yes" <?php echo (isset($general_settings['services_select']) && $general_settings['services_select'] ==='yes') ? 'checked' : ''; ?> placeholder="">  <?php _e('Show only choosen services on <a href="'.admin_url('admin.php?page=wc-settings&tab=shipping&section=wf_dhl_shipping&subtab=rates').'">Rates & Services</a> section.','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enabling this option will display only those selected services from Rates & Services section while printing the label from Order Admin page.','wf-shipping-dhl') ?>" ></span>
		</fieldset>
		
		<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_latin_encoding" id="wf_dhl_shipping_latin_encoding" style="" value="yes" <?php echo (isset($general_settings['latin_encoding']) && $general_settings['latin_encoding'] ==='yes') ? 'checked' : ''; ?> placeholder="">  <?php _e('Latin Language Encoding','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('UTF-8 encoding used.','wf-shipping-dhl') ?>" ></span>
		</fieldset>
		</td>
	</tr>

	<tr valign="top" ">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_">Shipping Label</label>
		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			
			<fieldset style="padding:3px;">
				 <label for="wf_dhl_shipping_"><?php _e('Printing Size','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('This option allows you to choose the size of the label among various options. Three file formats are supported for the labels - PDF, ZPL2, EPL2.','wf-shipping-dhl') ?>" ></span><br>
				<select name="wf_dhl_shipping_output_format">
				<?php 
					$selected_value = isset($general_settings['output_format']) ? $general_settings['output_format'] : '6X4_A4_PDF';
					foreach ($print_size as $key => $value) {
						if($key == $selected_value)
						{
							echo '<option value="'.$key.'" selected="true">'.$value.'</option>';
						}
						else
						{
							echo '<option value="'.$key.'">'.$value.'</option>';
						}
					}
				?>
				</select>
			</fieldset>
			<fieldset style="padding:3px;">
				<?php 
					$slected_doc_type = isset($general_settings['image_type']) ? $general_settings['image_type'] : 'PDF';
					foreach ($printer_doc_type as $key => $value) {
						if($key === $slected_doc_type)
						{
							echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_image_type" id="wf_dhl_shipping_image_type" style="" value="'.$key.'" checked="true" placeholder=""> '.$value.' ';
						}
						else
						{
							echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_image_type" id="wf_dhl_shipping_image_type" style="" value="'.$key.'"  placeholder=""> '.$value.' ';
						}
					}
				?>
				
			</fieldset>
			
		</td>
	</tr>
	<tr valign="top" ">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_label_contents_text"><?php _e('Shipping Content','wf-shipping-dhl') ?></label>
		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
			<label for="wf_dhl_shipping_label_contents_text"><?php _e('Shipping Content Description','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="Provide here a description about shipment contents." ></span><br>
				<input class="input-text regular-input " type="text" name="wf_dhl_shipping_label_contents_text" id="wf_dhl_shipping_label_contents_text" style="" value="<?php echo (isset($general_settings['label_contents_text'])) ? $general_settings['label_contents_text'] : ''; ?>" placeholder=""> 
			</fieldset>
			
		</td>
	</tr>
	<tr valign="top" ">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_customer_logo_url"><?php _e('Company Logo','wf-shipping-dhl') ?></label>
		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;" id="">
				 <label for="wf_dhl_shipping_customer_logo_url"><?php _e('Select Company Logo','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('This option allows you to upload your own company logo which will be visible in shipping labels and return labels.','wf-shipping-dhl') ?>" ></span><br>
				<input class="input-text regular-input " type="text" name="wf_dhl_shipping_customer_logo_url" id="wf_dhl_shipping_customer_logo_url" style="" value="<?php echo (isset($general_settings['customer_logo_url'])) ? $general_settings['customer_logo_url'] : ''; ?>" placeholder=""><br><a href="#" id="dhl_media_upload_image_button" class="button-secondary"><?php _e('Choose Image','wf-shipping-dhl') ?></a>
			</fieldset>
			
		</td>
	</tr>
		<tr valign="top" ">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_dutypayment_type"><?php _e('Duty Payment','wf-shipping-dhl') ?></label>
		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;" id="">
				 <label for="wf_dhl_shipping_dutypayment_type"><?php _e('Payment on','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Duty and tax charge payment type. It is required for non-doc or dutiable products.','wf-shipping-dhl') ?>" ></span><br>
				
					
				<select name="wf_dhl_shipping_dutypayment_type" id="wf_dhl_shipping_dutypayment_type" style="width:65%;">
					<?php 
						
					echo '<option value="0" selected="true">Third-Party/Other</option>';
					
					 ?>
					
				</select><br>
				</fieldset>
				<fieldset style="padding:3px;" id="wf_t_acc_number">
				<label for="wf_dhl_shipping_dutyaccount_number"><?php _e('Duty Account Number','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Duty Billing account number. Required if the DutyPaymentType is Third Party.','wf-shipping-dhl') ?>" ></span><br>
				
				 <input class="input-text regular-input " type="text" name="wf_dhl_shipping_dutyaccount_number" id="wf_dhl_shipping_dutyaccount_number" style="" value="<?php echo (isset($general_settings['dutyaccount_number'])) ? $general_settings['dutyaccount_number'] : ''; ?>" placeholder="">
			</fieldset>
			
		</td>
	</tr>
	<tr valign="top" ">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_request_archive_airway_label"><?php _e('Archive Air Waybill','wf-shipping-dhl') ?></label>
		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_request_archive_airway_label" id="wf_dhl_shipping_request_archive_airway_label" style="" value="yes" checked placeholder="">  <?php _e('Request Archive Air Waybill','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('For downloading archive airway bill Documents.','wf-shipping-dhl') ?>" ></span>
		</fieldset>

			<fieldset style="padding:3px;" id="wf_no_of_archive_bills">
				<?php if(isset($general_settings['no_of_archive_bills']) && $general_settings['no_of_archive_bills'] ==='2')
				{ ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_no_of_archive_bills"  id="wf_dhl_shipping_no_of_archive_bills"  value="1" placeholder=""> <?php _e('One Document','wf-shipping-dhl') ?>
				<input class="input-text regular-input " type="radio"  name="wf_dhl_shipping_no_of_archive_bills" checked="true" id="wf_dhl_shipping_no_of_archive_bills"  value="2" placeholder=""> <?php _e('Two Documents','wf-shipping-dhl') ?>
				<?php }else{ ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_no_of_archive_bills" checked="true" id="wf_dhl_shipping_no_of_archive_bills"  value="1" placeholder=""> <?php _e('One Document','wf-shipping-dhl') ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_no_of_archive_bills" id="wf_dhl_shipping_no_of_archive_bills"  value="2" placeholder=""> <?php _e('Two Documents','wf-shipping-dhl') ?>
				<?php } ?> 
			</fieldset>
			
		</td>
	</tr>
	<tr valign="top">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_default_domestic_service"><?php _e('Bulk Shipment','wf-shipping-dhl') ?></label>
		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;" id="">
				 <label for="wf_dhl_shipping_default_domestic_service"><?php _e('Default Domestic Service','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose the default service for domestic shipment which will be set while generating bulk shipment label from order admin page. The default service will be applicable if there is no DHL service chosen during the checkout process. ','wf-shipping-dhl') ?>" ></span><br>
				
					
				<select name="wf_dhl_shipping_default_domestic_service" disabled="true" id="wf_dhl_shipping_default_domestic_service" style="width:65%;">
					<?php 
					$selected_pay_type = isset($general_settings['default_domestic_service']) ? $general_settings['default_domestic_service'] : '';
					echo '<option value="none" >None</option>';
					foreach ($this->services as $key => $value) {
						
						if($selected_pay_type == $key)
						{
							echo '<option value="'.$key.'" selected="true">['.$key.'] '.$value . '</option>';
						}else{
							echo '<option value="'.$key.'">['.$key.'] '.$value . '</option>';
						}	
					}
				
					 ?>
					
				</select><br>
				</fieldset>
				<fieldset style="padding:3px;" id="">
				 <label for="wf_dhl_shipping_default_international_service"><?php _e('Default International Service','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose the default service for international shipment which will be set while generating bulk shipment label from order admin page. The default service will be applicable if there is no DHL service chosen during the checkout process. ','wf-shipping-dhl') ?>" ></span><br>
				
					
				<select name="wf_dhl_shipping_default_international_service" disabled="true" id="wf_dhl_shipping_default_international_service" style="width:65%;">
					<?php 
					$selected_pay_type = isset($general_settings['default_international_service']) ? $general_settings['default_international_service'] : '';
					echo '<option value="none" >None</option>';
					foreach ($this->services as $key => $value) {
						
						if($selected_pay_type == $key)
						{
							echo '<option value="'.$key.'" selected="true">['.$key.'] '.$value . '</option>';
						}else{
							echo '<option value="'.$key.'">['.$key.'] '.$value . '</option>';
						}	
					}
				
					 ?>
					
				</select><br>
				</fieldset>
		</td>
	</tr>
	
	<tr valign="top" ">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_return_label_key"><?php _e('Return Label','wf-shipping-dhl') ?></label>
		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_return_label_key" id="wf_dhl_shipping_return_label_key" style="" value="yes" checked placeholder="">  <?php _e('Enable Return Label','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('This option allows the plugin to provide the return label feature in the order page.','wf-shipping-dhl') ?>" ></span>
		</fieldset>

			<fieldset style="padding:3px;" id="wf_return_label_acc_number">
				 <label for="wf_dhl_shipping_return_label_acc_number"><?php _e('Return Label Account Number','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Fill in the import account number provided by DHL for return labels.','wf-shipping-dhl') ?>" ></span><br>
				<input class="input-text regular-input " type="text" name="wf_dhl_shipping_return_label_acc_number" id="wf_dhl_shipping_return_label_acc_number" style="" value="<?php echo (isset($general_settings['return_label_acc_number'])) ? $general_settings['return_label_acc_number'] : ''; ?>" placeholder="">
			</fieldset>
			
		</td>
	</tr>
	<tr valign="top" ">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_add_trackingpin_shipmentid"><?php _e('Tracking','wf-shipping-dhl') ?></label>

		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
		<fieldset style="padding:3px;">
		<input class="input-text regular-input " checked type="checkbox" name="wf_dhl_shipping_add_trackingpin_shipmentid" id="wf_dhl_shipping_add_trackingpin_shipmentid" style="" value="yes"  placeholder="">  <?php _e('Enable Tracking','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to activate the tracking feature of the plugin.
Custom tracking message - Provide your own tracking message which will be displayed in the order completion email. ','wf-shipping-dhl') ?>" ></span>
		</fieldset> 
			
		</td>
	</tr>
	<tr valign="top" ">
		<td style="width:50%;font-weight:800;">
			<label for="wf_dhl_shipping_dhl_email_notification_service"><?php _e('DHL Email Service','wf-shipping-dhl') ?></label>
		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_dhl_email_notification_service" id="wf_dhl_shipping_dhl_email_notification_service" style="" value="yes" checked placeholder="">  <?php _e('DHL Tracking Message to Customers','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('DHL sent the Shipment details to customers.','wf-shipping-dhl') ?>" ></span>
		</fieldset>

			<fieldset style="padding:3px;" id="wf_dhl_email_notification_message">
				 <label for="wf_dhl_shipping_dhl_email_notification_message"><?php _e('Shipper Message','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Message to customers.','wf-shipping-dhl') ?>" ></span><br>
				<input class="input-text regular-input " type="text" name="wf_dhl_shipping_dhl_email_notification_message" id="wf_dhl_shipping_dhl_email_notification_message" style="" value="<?php echo (isset($general_settings['dhl_email_notification_message'])) ? $general_settings['dhl_email_notification_message'] : ''; ?>" placeholder="">
			</fieldset>
			
		</td>
	</tr>
	<tr valign="top">
		<td style="width:50%;font-weight:800;">
		<label for="wf_dhl_shipping_add_picup"><?php _e('Pickup','wf-shipping-dhl') ?></label>
		</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
		<fieldset style="padding:3px;">
		<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_add_picup" id="wf_dhl_shipping_add_picup" style="" value="yes" checked placeholder="">  <?php _e('Enable Pickup','wf-shipping-dhl') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this if you want DHL to be able to pickup the shipment from your store. ','wf-shipping-dhl') ?>" ></span>
		</fieldset> 
			
			<fieldset style="padding:3px;" id="wf_pickup_date">
				 <label for="wf_dhl_shipping_pickup_date"><?php _e('Schedule Pickup After','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('How many days after the order has been placed, do you want the pickup to arrive at your store.','wf-shipping-dhl') ?>" ></span><br>
				<input class="input-text regular-input " disabled="true" min="0" max="7" type="number" name="wf_dhl_shipping_pickup_date" id="wf_dhl_shipping_pickup_date" style="" value="<?php echo (isset($general_settings['pickup_date'])) ? $general_settings['pickup_date'] : ''; ?>" placeholder="0"> <?php _e('Day(s).','wf-shipping-dhl') ?>
			</fieldset>
			
			<fieldset style="padding:3px;" id="wf_pickup_from_to">
				 <label for="wf_dhl_shipping_pickup_time_from"><?php _e('Pickup Availbility Time (24 hours Format)','wf-shipping-dhl') ?></label> <span style="color:red;"> *</span> <span class="woocommerce-help-tip" data-tip="<?php _e('Give a definite range of time within which you can allow pickup in order to avoid conflict.','wf-shipping-dhl') ?>" ></span><br>
				<b><?php _e('From','wf-shipping-dhl') ?>:</b> <input class="input-text regular-input " disabled="true" size="7"  type="text" name="wf_dhl_shipping_pickup_time_from" id="wf_dhl_shipping_pickup_time_from" style="" value="<?php echo (isset($general_settings['pickup_time_from'])) ? $general_settings['pickup_time_from'] : ''; ?>" placeholder="From">
				<b><?php _e('To','wf-shipping-dhl') ?>:</b> <input class="input-text regular-input " disabled="true" size="7" type="text" name="wf_dhl_shipping_pickup_time_to" id="wf_dhl_shipping_pickup_time_to" style="" value="<?php echo (isset($general_settings['pickup_time_to'])) ? $general_settings['pickup_time_to'] : ''; ?>" placeholder="To">
			</fieldset>
			<fieldset style="padding:3px;" id="wf_pickup_details">
				 <label for="wf_dhl_shipping_pickup_person"><?php _e('Pickup Person Name','wf-shipping-dhl') ?></label> <span style="color:red;"> *</span> <span class="woocommerce-help-tip" data-tip="<?php _e('Give a contact person’s name and contact no. who can be contacted in case of any convenience..','wf-shipping-dhl') ?>" ></span><br>
				<input class="input-text regular-input "  type="text" name="wf_dhl_shipping_pickup_person" disabled="true" id="wf_dhl_shipping_pickup_person" style="" value="<?php echo (isset($general_settings['pickup_person'])) ? $general_settings['pickup_person'] : ''; ?>" placeholder="Person Name">
				<input class="input-text regular-input "  type="text" name="wf_dhl_shipping_pickup_contact" disabled="true" id="wf_dhl_shipping_pickup_contact" style="" value="<?php echo (isset($general_settings['pickup_contact'])) ? $general_settings['pickup_contact'] : ''; ?>" placeholder="Contact Number">
			</fieldset>
		
		
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:right;padding-right: 10%;">
			<br/>
			<input type="submit" value="<?php _e('Save Changes','wf-shipping-dhl') ?>" class="button button-primary" name="">
		</td>
	</tr>
	
</table>
<script type="text/javascript">

		
		jQuery(window).load(function(){
			
			jQuery('#xa_labels_account_settings').find('input, textarea, button, select').attr('disabled','disabled');
		});

	</script>