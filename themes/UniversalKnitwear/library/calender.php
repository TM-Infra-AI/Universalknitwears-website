<?php

/**
 * Proper way to enqueue scripts and styles
 */
add_action( 'init', 'create_custom_calender_post_type' );
/**
 * Register a Calender post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function create_custom_calender_post_type() {

  /* Register our stylesheet. */
  $labels = array(
    'name'               => _x( 'All Calenders', 'post type general name', 'your-plugin-textdomain' ),
    'singular_name'      => _x( 'Calender', 'post type singular name', 'your-plugin-textdomain' ),
    'menu_name'          => _x( 'Calenders', 'admin menu', 'your-plugin-textdomain' ),
    'name_admin_bar'     => _x( 'Calender', 'add new on admin bar', 'your-plugin-textdomain' ),
    'add_new'            => _x( 'Add New', 'Calender', 'your-plugin-textdomain' ),
    'add_new_item'       => __( 'Add New Calender', 'your-plugin-textdomain' ),
    'new_item'           => __( 'New Calender', 'your-plugin-textdomain' ),
    'edit_item'          => __( 'Edit Calender', 'your-plugin-textdomain' ),
    'view_item'          => __( 'View Calender', 'your-plugin-textdomain' ),
    'all_items'          => __( 'All Calenders', 'your-plugin-textdomain' ),
    'search_items'       => __( 'Search Calender', 'your-plugin-textdomain' ),
    'parent_item_colon'  => __( 'Parent Calender:', 'your-plugin-textdomain' ),
    'not_found'          => __( 'No Calender found.', 'your-plugin-textdomain' ),
    'not_found_in_trash' => __( 'No Calender found in Trash.', 'your-plugin-textdomain' )
    );

$args = array(
  'labels'             => $labels,
  'public'             => true,
  'publicly_queryable' => true,
  'show_ui'            => true,
  'show_in_menu'       => true,
  'query_var'          => true,
  'rewrite'            => array( 'slug' => 'calender' ),
  'capability_type'    => 'post',
  'has_archive'        => true,
  'hierarchical'       => false,
  'menu_position'      => null,
  'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt')
  );

register_post_type( 'calender', $args );
}