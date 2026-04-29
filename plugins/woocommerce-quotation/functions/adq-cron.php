<?php
/**
 * Handle Cron functions
 *
 * @version     1.0.0
 * @package     woocommerce-quotation/functions/
 * @category    Functions
 * @author      Aldaba Digital
 */

/*
 * Daily cron to send reminder mails or cancel proposal that have expired
 */
if( !function_exists( 'adq_daily_cron' ) ) {
        function adq_daily_cron()
        {
            $next_execution = get_option('adq_next_execution_cron');
            
            if(!$next_execution || time() >= $next_execution )  {
                $customer_orders = get_posts( array(
                    'numberposts' => "-1",
                    'post_type'   => wc_get_order_types( 'view-orders' ),
                    'post_status' => 'wc-proposal-sent'
                ) );

                if ( $customer_orders ) { 
                        foreach ( $customer_orders as $customer_order ) {
                                $order = wc_get_order( $customer_order );
                                $order->populate( $customer_order );
                                $validity_date = get_post_meta( $order->id, '_validity_date', true );
                                $reminder_date = get_post_meta( $order->id, '_reminder_date', true );

                                //If validity date has expired change status
                                if( strtotime( $validity_date ) <= time() ) {                        
                                    $order->update_status( 'wc-proposal-expired' ); 
                                }
                                //If reminder date is ON send reminder email
                                elseif( strtotime( $reminder_date ) <= time() ) {                        
                                    //Check if Reminder mail has been send
                                    $reminder_email_send = get_post_meta( $order->id, '_reminder_email_send', true );
                                    if( !$reminder_email_send || $reminder_email_send == 'no' ) {

                                        do_action( 'adq_email_remind_proposal', $order->id );

                                        update_post_meta( $order->id, '_reminder_email_send', 'yes' ); 
                                    }                                               
                                }
                        }
                } 
                //Next day 
                update_option('adq_next_execution_cron', strtotime(date("Y-m-d", time())." +1 day") );
            }
        }
        add_action('wp_head', 'adq_daily_cron');
}
