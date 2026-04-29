<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post, $product;

$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );

if ( is_user_logged_in() ) {
	$user = new WP_User( get_current_user_id() );
}
?>
<h3 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h3>
<?php
	$style_no = get_post_meta( get_the_ID(), 'style_no', true );
	if ( ! empty( $style_no ) ) {
		echo _e( '<span class="posted_in">Style No.', 'woocommerce' ).":</span> ".$style_no."<br>";
	}

	 echo $product->get_categories( ', ', '' . _n( '<span class="posted_in">Item:</span>', '<span class="posted_in">Items:</span>', $cat_count, 'woocommerce' ) . ' ', '<br>' ); 
	 
	$gauge = get_post_meta( get_the_ID(), 'gauge', true );
	if ( ! empty( $gauge ) ) {
		echo _e( '<span class="posted_in">Gauge', 'woocommerce' ).":</span> ".$gauge."<br>";
	}
	$size = get_post_meta( get_the_ID(), 'size', true );
	if ( ! empty( $size ) ) {
		echo _e( '<span class="posted_in">Size', 'woocommerce' ).":</span> ".$size."<br>";
	}
	$content = get_post_meta( get_the_ID(), 'content', true );
	if ( ! empty( $content ) ) {
		echo _e( '<span class="posted_in">Content', 'woocommerce' ).":</span> ".$content."<br>";
	}
	$regular_price = get_post_meta( get_the_ID(), '_regular_price', true );
	$price = wc_price( $regular_price, $args );
	if ( ! empty( $price ) ) {
		
		if($user->roles[0]=="contributor"){
		echo _e( '<span class="posted_in">Price USD/Piece', 'woocommerce' ).":</span> ".$price."<br>";
		}
	}
	
?>