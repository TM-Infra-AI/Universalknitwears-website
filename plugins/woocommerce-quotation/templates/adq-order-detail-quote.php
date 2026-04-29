<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 	Aldaba Digital
 * @package 	woocommerce-quotation/templates
 * @version     1.0.0
 */

        $validity_date = get_post_meta( $order_id, '_validity_date', true );
        $downloadable_files = get_post_meta( $order_id, '_attached_files', true ); 
        
        $order = wc_get_order( $order_id );
        
        if ( $order->get_status() == 'proposal-sent' || ( is_array($downloadable_files) && count($downloadable_files) > 0 ) ) {
?>

            <h2><?php echo __( 'Quote Details', 'woocommerce-quotation' ) ?></h2>
            
            <?php do_action('adq_order_detail_quote_before', $order_id); ?>
            
            <dl class="quote_details">
                
            <?php
                    if ( $order->get_status() == 'proposal-sent' ) {
                            echo '<dt>' . __( 'This proposal is valid until:', 'woocommerce-quotation' ) . '</dt><dd>' . date_i18n( get_option( 'date_format' ), strtotime( $validity_date ) ) . '</dd>';
                    }
                    
                    if ( is_array($downloadable_files) && count($downloadable_files) > 0 ) {
                            $links = "";
                            foreach ( $downloadable_files as $key => $file ) {
                                    $links .= '<a href="'.get_home_url()."?order_id=".$order_id."&adq_download_file=".$key.'" title="'.$file["name"].'">'.$file["name"].'</a>';
                            }
                            
                            if ( $links != "") {
                                    echo '<dt>' . __( 'Attached files:', 'woocommerce-quotation' ) . '</dt><dd>';
                                    echo $links;
                                    echo '</dd>';
                            }
                    } 
            ?>
                
            </dl>
            
            <?php 
            
                if ( $order->get_status() == 'proposal-sent' ) {                        
                        
                    $order_key = base64_encode ( get_post_meta( $order_id, '_order_key', true ) );
                    $email = urlencode ( get_post_meta( $order_id, '_billing_email', true ) );
                    
                    $link = get_option( 'permalink_structure' );
                    if($link != "")
                        $nedlee = '?';
                    else 
                        $nedlee = '&';
                    
                    $base_url = get_permalink() . $nedlee . 'order_key='.$order_key.'&email='.$email;
            ?>      
                    <div class="adq-order-detail-buttons">
                            <p class="adq-order-detail-button">
                                <a class="button button-primary view" href="<?php echo $base_url ?>&adq_action=accept" title="<?php echo __('Accept proposal','woocommerce-quotation') ?>">
                                    <?php echo __('Accept proposal','woocommerce-quotation') ?>
                                </a>
                            </p>

                            <p class="adq-order-detail-button">
                                <a class="button view" href="<?php echo $base_url ?>&adq_action=reject" title="<?php echo __('Reject proposal','woocommerce-quotation') ?>">
                                    <?php echo __('Reject proposal','woocommerce-quotation') ?>
                                </a>
                            </p>                    
                            <div class="clear"></div>
                    </div>                    
            <?php 
                    $additional_info = get_post_meta( $order_id, '_adq_additional_info', true ); 
                    if( $additional_info && $additional_info != "") {
            ?>
                    <dl class="quote_details">
                            <dt><?php echo __( 'Additional information', 'woocommerce-quotation' ); ?></dt>
                            <dd><?php echo $additional_info; ?></dd>
                    </dl>
            <?php
                    }
            
                }
                
                do_action('adq_order_detail_quote_after', $order_id);
                
            ?>
<?php 
        } 