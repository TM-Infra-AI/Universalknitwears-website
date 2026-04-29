<tr>
	<td class="sort"></td>
	<td class="file_name"><input <?php echo $readonly ?> type="text" class="input_text" placeholder="<?php _e( 'File Name', 'woocommerce-quotation' ); ?>" name="_wc_file_names[]" value="<?php echo esc_attr( $file['name'] ); ?>" /></td>
	<td class="file_url"><input <?php echo $readonly ?> type="text" class="input_text" placeholder="<?php _e( "http://", 'woocommerce-quotation' ); ?>" name="_wc_file_urls[]" value="<?php echo esc_attr( $file['file'] ); ?>" /></td>
	<td class="file_url_choose" width="1%">
            <?php if ($readonly == '' ) { ?>
                <a href="#" class="button upload_file_button" data-choose="<?php _e( 'Choose file', 'woocommerce-quotation' ); ?>" data-update="<?php _e( 'Insert file URL', 'woocommerce-quotation' ); ?>"><?php echo str_replace( ' ', '&nbsp;', __( 'Choose file', 'woocommerce-quotation' ) ); ?></a>
            <?php } ?>
        </td>
        <td width="1%">
            <?php if ($readonly == '' ) { ?>
                <a href="#" class="delete attachment_delete"><?php _e( 'Delete', 'woocommerce-quotation' ); ?></a>
            <?php } ?>
        </td>
</tr>