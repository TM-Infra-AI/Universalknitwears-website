<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


global $post, $product, $woocommerce_loop;

$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 === ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 === $woocommerce_loop['columns'] ) {
	//$classes[] = 'first small-12 large-3 medium-3';
	$classes[] = '';
}
if ( 0 === $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
	//$classes[] = 'last';
	$classes[] = '';
}
?>
<?php
if ( is_user_logged_in() ) {
	$user = new WP_User( $user_ID );
}
$terms = get_the_terms( get_the_ID(), 'usertype' );
                       
if ( $terms && ! is_wp_error( $terms ) ) : 
 
    $term_links = array();
 
    foreach ( $terms as $term ) {
        $term_links[] = $term->name;
		if($term->name=="Regular" && $user->roles[0]==""){
			?>
	<li <?php post_class( $classes ); ?>>

	<?php
	/**
	 * woocommerce_before_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * woocommerce_before_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * woocommerce_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * woocommerce_after_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
	<?php
	$style_no = get_post_meta( get_the_ID(), 'style_no', true );
	if ( ! empty( $style_no ) ) {
		echo _e( '</a><span class="posted_in">Style No.', 'woocommerce' ).":</span> ".$style_no."<br>";
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
	$price = wc_price( $regular_price );
	if ( ! empty( $price ) ) {
		if ( is_user_logged_in() ) {
		echo _e( '<span class="posted_in">Price USD/Piece', 'woocommerce' ).":</span> ".$price."<br>";
		}
	}
?>

	<?php
	/**
	 * woocommerce_after_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	 do_action( 'woocommerce_after_shop_loop_item' );
	
	?>
	</li>
			
<?php	
	}
	
	if($term->name=="Retailer" && $user->roles[0]=="editor"){
			?>
	<li <?php post_class( $classes ); ?>>

	<?php
	/**
	 * woocommerce_before_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * woocommerce_before_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * woocommerce_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * woocommerce_after_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
	<?php
	$style_no = get_post_meta( get_the_ID(), 'style_no', true );
	if ( ! empty( $style_no ) ) {
		echo _e( '</a><span class="posted_in">Style No.', 'woocommerce' ).":</span> ".$style_no."<br>";
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
	
?>

	<?php
	/**
	 * woocommerce_after_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	 do_action( 'woocommerce_after_shop_loop_item' );
	
	?>
	</li>
			
<?php	
	}
if($term->name=="Shopkeeper" && $user->roles[0]=="contributor"){
			?>
	<li <?php post_class( $classes ); ?>>
	<?php
	/**
	 * woocommerce_before_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * woocommerce_before_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * woocommerce_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * woocommerce_after_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
	<?php
	$style_no = get_post_meta( get_the_ID(), 'style_no', true );
	if ( ! empty( $style_no ) ) {
		echo _e( '</a><span class="posted_in">Style No.', 'woocommerce' ).":</span> ".$style_no."<br>";
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
	$price = wc_price( $regular_price );
	if ( ! empty( $price ) ) {
		if ( is_user_logged_in() ) {
		echo _e( '<span class="posted_in">Price USD/Piece', 'woocommerce' ).":</span> ".$price."<br>";
		}
	}
?>

	<?php
	/**
	 * woocommerce_after_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	 do_action( 'woocommerce_after_shop_loop_item' );
	
	?>
	</li>
			
<?php	
	}	
    }
    ?>
<?php endif; ?>


