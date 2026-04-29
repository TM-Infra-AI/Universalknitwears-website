<?php $this->init_settings(); 
global $woocommerce;
$wc_main_settings = array();
$package_type = array('BOX'=>__('DHL Box', 'wf-shipping-dhl'),'FLY'=>__('Flyer', 'wf-shipping-dhl'),'YP'=>__('Your Pack', 'wf-shipping-dhl'));
$weight_type =  array('pack_descending'=>__('Pack heavier items first', 'wf-shipping-dhl'),'pack_ascending'=>__('Pack lighter items first', 'wf-shipping-dhl'),'pack_simple'=>__('Pack purely divided by weight', 'wf-shipping-dhl'));
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
   This section contains various packing techniques ( box packing, weight-based packing, individual packing ). This feature available only in <a href="<?php echo admin_url( 'admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_shipping&subtab=premium' ); ?>">premium version</a>
</div>

<table id="xa_labels_account_settings_pack">
	<tr valign="top" ">
		<td style="width:35%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Packing Options','wf-shipping-dhl') ?></label>
		</td><td scope="row" class="titledesc" style="display: block;width:100%;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
			 <label for="wf_dhl_shipping_"><?php _e('Parcel Packing Method','wf-shipping-dhl') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Select the Packing method using which you want to pack your products.  Pack items individually - This option allows you to pack each item separately in a box. Hence, multiple items will go in multiple boxes. 
 Pack into boxes with weights and dimensions - This option allows you to pack items into boxes of various sizes. 
 Weight based packing - This option allows you to pack your products based on weight of the package.','wf-shipping-dhl') ?>"></span>	<br>
				<select name="wf_dhl_shipping_packing_method" id="wf_dhl_shipping_packing_method" default="per_item">
					<?php 
						$selected_packing_method = isset($general_settings['packing_method']) ? $general_settings['packing_method'] : 'per_item';
					?>
					<option value="per_item" <?php echo ($selected_packing_method === 'per_item') ? 'selected="true"': '' ?> ><?php _e('Default: Pack items individually','wf-shipping-dhl') ?></option>
					<option value="box_packing" <?php echo ($selected_packing_method === 'box_packing') ? 'selected="true"': '' ?> ><?php _e('Recommended: Pack into boxes with weights and dimensions','wf-shipping-dhl') ?></option>
					<option value="weight_based" <?php echo ($selected_packing_method === 'weight_based') ? 'selected="true"': '' ?> ><?php _e('Weight based: Calculate shipping on the basis of order total weight','wf-shipping-dhl') ?></option>
				</select>
			</fieldset>
			<fieldset style="padding:3px;">
				<?php if(isset($general_settings['dimension_weight_unit']) && $general_settings['dimension_weight_unit'] ==='KG_CM')
				{ ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_dimension_weight_unit"  id="wf_dhl_shipping_dimension_weight_unit"  value="LBS_IN" placeholder=""> <?php _e('Use Pounds,Inches (lbs,in) ','wf-shipping-dhl') ?>
				<input class="input-text regular-input " type="radio"  name="wf_dhl_shipping_dimension_weight_unit" checked="true" id="wf_dhl_shipping_dimension_weight_unit"  value="KG_CM" placeholder=""> Use <?php _e('Kilograms,Centimeters (Kg,cm)','wf-shipping-dhl') ?>
				<?php }else{ ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_dimension_weight_unit" checked="true" id="wf_dhl_shipping_dimension_weight_unit"  value="LBS_IN" placeholder=""> <?php _e('Use Pounds,Inches (lbs,in) ','wf-shipping-dhl') ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_dimension_weight_unit" id="wf_dhl_shipping_dimension_weight_unit"  value="KG_CM" placeholder=""> <?php _e('Use Kilograms,Centimeters (Kg,cm)','wf-shipping-dhl') ?>
				<?php } ?>
			</fieldset>
		</td>
	</tr>
	<tr>
		<tr id="packing_options_shp_pack_type">
			<td style="width:35%;font-weight:800;">
			<label for="wf_dhl_shipping_shp_pack_type"><?php _e('Pack items individually <br/>(Package Type)','wf-shipping-dhl') ?></label> 
			<span class="woocommerce-help-tip" data-tip="DHL Box: There are the most commonly used boxes for packing. These are the boxes which get populated when you install the plugin.<br/>
Flyer: This option is suitable for Binded documents and Flat materials.<br/>
Your Box: With this option, your item gets packed into customized box.<br/>
For example, the shipping cost of Item X is £10. If the customer adds two quantities of Item X to the Cart, then the total shipping cost is £10 x 2, which is £20."></span>
			
		</td><td scope="row" class="titledesc" style="display: block;width:100%;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
			
				<?php 
					$slected_pack_type = isset($general_settings['shp_pack_type']) ? $general_settings['shp_pack_type'] : 'BOX';
					foreach ($package_type as $key => $value) {
						if($key === $slected_pack_type)
						{
							echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_shp_pack_type" id="wf_dhl_shipping_shp_pack_type" style="" value="'.$key.'" checked="true" placeholder=""> '.$value.' ';
						}
						else
						{
							echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_shp_pack_type" id="wf_dhl_shipping_shp_pack_type" style="" value="'.$key.'"  placeholder=""> '.$value.' ';
						}
					}
				?>
			</td>
		
		</tr>
	<tr>
		<td colspan="2" style="text-align:right;padding-right: 10%;">
			<br/>
			<input type="submit" value="<?php _e('Save Changes','wf-shipping-dhl') ?>" class="button button-primary" name="wf_dhl_packing_save_changes_button">
		</td>
	</tr>
</table>

<script type="text/javascript">

		
		jQuery(window).load(function(){
			
			jQuery('#xa_labels_account_settings_pack').find('input, textarea, button, select').attr('disabled','disabled');
		});

	</script>