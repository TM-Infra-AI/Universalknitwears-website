<?php
/**
 * Override WooCommerce Abstract Order Class
 *
 * Handles shipping and loads shipping methods via hooks.
 *
 * @class 		ADQ_Order
 * @version		1.3.5
 * @package		woocommerce-quotation/classes/
 * @category            Class
 * @author 		Aldaba Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ADQ_Order extends WC_Abstract_Order {
    
    
        /**
	 * Adds a note (comment) to the order
	 *
	 * @param string $note Note to add
	 * @param int $is_customer_note (default: 0) Is this a note for the customer?
	 * @return int Comment ID
	 */
	public function add_order_note( $note, $is_customer_note = 0 ) {

		$is_customer_note = intval( $is_customer_note );

		if ( is_user_logged_in() && current_user_can( 'edit_shop_order', $this->id ) ) {
			$user                 = get_user_by( 'id', get_current_user_id() );
			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
		} else {
			$comment_author       = __( 'WooCommerce', 'woocommerce' );
			$comment_author_email = strtolower( __( 'WooCommerce', 'woocommerce' ) ) . '@';
			$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', $_SERVER['HTTP_HOST'] ) : 'noreply.com';
			$comment_author_email = sanitize_email( $comment_author_email );
		}

		$comment_post_ID        = $this->id;
		$comment_author_url     = '';
		$comment_content        = $note;
		$comment_agent          = 'WooCommerce';
		$comment_type           = 'order_note';
		$comment_parent         = 0;
		$comment_approved       = 1;
		$commentdata            = apply_filters( 'woocommerce_new_order_note_data', compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved' ), array( 'order_id' => $this->id, 'is_customer_note' => $is_customer_note ) );

		$comment_id = wp_insert_comment( $commentdata );

		add_comment_meta( $comment_id, 'is_customer_note', $is_customer_note );
                
		if ( $is_customer_note ) {
			do_action( 'adq_email_new_customer_note', array( 'order_id' => $this->id, 'customer_note' => $commentdata['comment_content'] ) );
		}

		return $comment_id;
	}
}
