<?php
// custom-post-type.php

function luminotes_register_custom_post_type() {
    $labels = array(
        'name'                  => _x('Records', 'Post type general name', 'luminotes'),
        'singular_name'         => _x('Record', 'Post type singular name', 'luminotes'),
        'menu_name'             => _x('LumiNotes Records', 'Admin Menu text', 'luminotes'),
        'name_admin_bar'        => _x('Record', 'Add New on Toolbar', 'luminotes'),
        'add_new'               => __('Add New', 'luminotes'),
        'add_new_item'          => __('Add New Record', 'luminotes'),
        'new_item'              => __('New Record', 'luminotes'),
        'edit_item'             => __('Edit Record', 'luminotes'),
        'view_item'             => __('View Record', 'luminotes'),
        'all_items'             => __('All Records', 'luminotes'),
        'search_items'          => __('Search Records', 'luminotes'),
        'parent_item_colon'     => __('Parent Records:', 'luminotes'),
        'not_found'             => __('No records found.', 'luminotes'),
        'not_found_in_trash'    => __('No records found in Trash.', 'luminotes'),
        'featured_image'        => _x('Record Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'luminotes'),
        'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'luminotes'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'luminotes'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'luminotes'),
        'archives'              => _x('Record archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'luminotes'),
        'insert_into_item'      => _x('Insert into record', 'Overrides the “Insert into post”/“Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'luminotes'),
        'uploaded_to_this_item' => _x('Uploaded to this record', 'Overrides the “Uploaded to this post”/“Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'luminotes'),
        'filter_items_list'     => _x('Filter records list', 'Screen reader text for the filter links heading on the post type listing screen. Added in 4.4', 'luminotes'),
        'items_list_navigation' => _x('Records list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Added in 4.4', 'luminotes'),
        'items_list'            => _x('Records list', 'Screen reader text for the items list heading on the post type listing screen. Added in 4.4', 'luminotes'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'record'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author'),
    );

    register_post_type('luminotes_record', $args);
}

add_action('init', 'luminotes_register_custom_post_type');
