<?php
/**
 * Quote List Page
 *
 * @author 	Adquote
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<div class="woocommerce">
    
<?php wc_print_notices(); ?>
    
<form action="#" method="post">

<table class="shop_table cart" cellspacing="0">
	<thead>
		<tr>
			<th class="product-remove">&nbsp;</th>
			<th class="product-thumbnail">&nbsp;</th>
			<th class="product-name"><?php _e( 'Product', 'woocommerce-quotation' ); ?></th>
                <?php if ( StaticAdqQuoteRequest::can_show_price() ) { ?>
			<th class="product-price"><?php _e( 'Price', 'woocommerce-quotation' ); ?></th>
                <?php } ?>
			<?php if ( StaticAdqQuoteRequest::can_show_price() ) { ?>
			<th class="product-quantity"><?php _e( 'Quantitys', 'woocommerce-quotation' ); ?></th>
			<?php } ?>
                <?php if ( StaticAdqQuoteRequest::can_show_price() ) { ?>
			<th class="product-subtotal"><?php _e( 'Total', 'woocommerce-quotation' ); ?></th>
                <?php } ?>
		</tr>
	</thead>
        <tfoot>
            <?php if ( WC_Adq()->quote->coupons_enabled() ) { ?>
                <?php foreach ( WC_Adq()->quote->get_coupons() as $coupon ) { ?>
                    <tr>
                        <?php 
                        
                                $value  = array();

                                if ( $amount = WC_Adq()->quote->get_coupon_discount_amount( $coupon->code, WC()->cart->display_cart_ex_tax ) ) {
                                        $discount_html = '-' . wc_price( $amount );
                                } else {
                                        $discount_html = '';
                                }

                                $value[] = apply_filters( 'woocommerce_coupon_discount_amount_html', $discount_html, $coupon );

                                if ( $coupon->enable_free_shipping() ) {
                                        $value[] = __( 'Free shipping coupon', 'woocommerce-quotation' );
                                }

                                // get rid of empty array elements
                                $value = array_filter( $value );

                                $value = implode( ', ', $value ) . ' <a href="' . esc_url( add_query_arg( 'adq_remove_coupon', $coupon->code, StaticAdqQuoteRequest::get_quote_list_link() ) ) . '" class="woocommerce-remove-coupon" data-coupon="' . esc_attr( $coupon->code ) . '">' . __( '[Remove]', 'woocommerce-quotation' ) . '</a>';
                                $label = apply_filters( 'woocommerce_cart_totals_coupon_label', esc_html( __( 'Coupon:', 'woocommerce-quotation' ) . ' ' . $coupon->code ), $coupon );
                            ?>
                            <td colspan="3" class="actions"><?php echo $label ?></td>
                            <td colspan="3"><?php echo $value ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            <tr>
                <td colspan="6" class="actions">
                        <?php if ( WC_Adq()->quote->coupons_enabled() ) { ?>
                                <div class="coupon">

                                        <label for="coupon_code"><?php _e( 'Coupon', 'woocommerce-quotation' ); ?>:</label> <input type="text" name="adq_coupon_code" class="input-text" id="adq_coupon_code" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce-quotation' ); ?>" /> <input type="submit" class="button" name="adq_apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce-quotation' ); ?>" />

                                        <?php do_action( 'woocommerce_cart_coupon' ); ?>

                                </div>
                        <?php } ?>
                </td>
            </tr>
            <tr><td colspan="6">
                    <a class="button" id="remove_all_items" href="#"><?php _e( 'Remove all items', 'woocommerce-quotation' ); ?></a>
                </td></tr>            
        </tfoot>
	<tbody>
		<?php                     
		foreach ( WC_Adq()->quote->get_quote() as $cart_item_key => $cart_item ) {
                        $cart_item = apply_filters( 'woocommerce_adq_cart_item_list', $cart_item );
                    
                        $_product = $cart_item['data'];                        
                        
                        $comments = StaticAdqQuoteRequest::is_comments_allowed($cart_item['product_id']);                        

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
				?>
				<tr class="adq_list <?php echo esc_attr( apply_filters( 'woocommerce_quote_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                                    <td class="product-remove" <?php if ($comments) { echo 'rowspan="2"'; } ?>> <!--rowspan="2"-->
                                        <?php
                                                //printf( '<a href="#" class="remove remove_quote_item" data-cart_item_key="%s" data-product_id="%s" title="%s">&times;</a>', $cart_item_key, $cart_item['product_id'], __( 'Remove this item', 'woocommerce-quotation' ) );
                                                    echo apply_filters( 'woocommerce_quote_item_remove_link', 
                                                            sprintf( '<a href="#" class="remove remove_quote_item" data-cart_item_key="%s" data-product_id="%s" title="%s">&times;</a>', 
                                                                    $cart_item_key, 
                                                                    $cart_item['product_id'], 
                                                                    __( 'Remove this item', 'woocommerce-quotation' ) 
                                                            ), 
                                                            $cart_item_key );
                                        ?>
					</td>

                                        <td class="product-thumbnail" <?php if ($comments) { echo 'rowspan="2"'; } ?>> 
                                        <?php
                                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                                if ( ! $_product->is_visible() )
                                                        echo $thumbnail;
                                                else
                                                        printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
                                        ?>
					</td>

					<td class="product-name">
						<?php
							/*if ( ! $_product->is_visible() )
								echo $_product->get_title();
							else
								printf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_title() );                                                        
                                                        */
                                                        
                                                        if ( ! $_product->is_visible() ) {
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
							} else {
								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a>', esc_url( $_product->get_permalink( $cart_item ) ), $_product->get_title() ), $cart_item, $cart_item_key );
							}
                                                        
                                                        echo WC()->cart->get_item_data( $cart_item );                                                        
						?>
					</td>
                                        
                                <?php if ( StaticAdqQuoteRequest::can_show_price() ) { ?> 
					<td class="product-price">
						<?php
                                                    if ( StaticAdqQuoteRequest::can_show_price( $_product ) ) {                                                            
                                                            echo apply_filters( 'woocommerce_quote_item_price', WC()->cart->get_product_price( $cart_item["data"] ), $cart_item, $cart_item_key );
                                                    }
						?>
					</td>
                                <?php } ?>
<?php if ( StaticAdqQuoteRequest::can_show_price() ) { ?>
					<td class="product-quantity">
                                            <div class="quantity buttons_added"> 
                                                <?php 
                                                if ( $_product->is_sold_individually() ) {
                                                        $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                                }
                                                else { 
                                                        $product_quantity = sprintf('<input type="number" size="4" class="input-text qty text adq_qty_list" data-cart_item_key="%s" data-product_id="%s" title="Qty" value="%s" name="cart[%s][qty]" min="0" step="1">', $cart_item_key, $cart_item['product_id'], $cart_item['quantity'], $cart_item_key );
                                                }                                                 
                                                    echo apply_filters( 'woocommerce_quote_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                                                ?>   
                                            </div>						
					</td>
    <?php } ?>                                      
                                <?php if ( StaticAdqQuoteRequest::can_show_price() ) { ?>                                        

					<td class="product-subtotal">
						<?php
                                                    if ( StaticAdqQuoteRequest::can_show_price( $_product ) ) { 

                                                        if ( WC()->cart->tax_display_cart == 'excl' )
                                                                $product_price = $cart_item["data"]->get_price_excluding_tax();
                                                        else
                                                                $product_price = $cart_item["data"]->get_price_including_tax();
                                                        
                                                        echo apply_filters( 'woocommerce_quote_item_subtotal', wc_price( $cart_item['quantity'] * $product_price ), $cart_item, $cart_item_key );                                                        
                                                        
                                                    }
						?>
					</td>
                                        
                                <?php } ?>
                                        
				</tr>
                                <?php if ($comments) { ?>
                                        <tr class="adq_list meta_info">
                                                <td colspan="4">
                                                    <?php 
                                                        $_product_note = "";
                                                        if(isset($cart_item["_product_note"])) {
                                                                $_product_note = $cart_item["_product_note"];
                                                        }
                                                    ?>
                                                    <a href="#" <?php if($_product_note != ""): ?> style="display:none" <?php endif; ?> class="show_product_meta" data-cart_item_key="<?php echo $cart_item_key ?>"><?php echo  __( 'Click here to add a product note', 'woocommerce-quotation' ) ?></a>  
                                                    
                                                    <textarea <?php if($_product_note == ""): ?> style="display:none" <?php endif; ?> class="quote_product_meta product_meta_<?php echo $cart_item_key ?>" name="_product_note" data-cart_item_key="<?php echo $cart_item_key ?>" ><?php echo $_product_note ?></textarea>
                                                </td>
                                        </tr>
                                <?php } ?>
				<?php
			}
		}

		?>
	</tbody>
</table>

</form>
    
</div>