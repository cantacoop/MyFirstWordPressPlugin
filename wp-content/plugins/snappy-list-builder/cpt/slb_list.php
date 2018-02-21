<?php
function slb_register_slb_list() {

	/**
	 * Post Type: Lists.
	 */

	$labels = array(
		"name" => __( "Lists", "twentyseventeen" ),
		"singular_name" => __( "Lists", "twentyseventeen" ),
	);

	$args = array(
		"label" => __( "Lists", "twentyseventeen" ),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "slb_list", "with_front" => false ),
		"query_var" => true,
		"supports" => array( "title" ),
	);

	register_post_type( "slb_list", $args );
}

add_action( 'init', 'slb_register_slb_list' );
