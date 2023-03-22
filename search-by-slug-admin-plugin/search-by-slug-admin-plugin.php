<?php
/*
Plugin Name: Search by Slug in admin
Plugin URI: https://isource.com.mk
Description: Search by slug everywhere inside /wp-admin
Author: Pece Ivanovski
Version: 1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!is_admin()) {
    return;
}

// Searching by slug in admin slug:slug-name
function search_by_slug_name($search, $wp_query) {
	if(empty($search)) {
        return $search;
    }
	global $wpdb;

	$query = $wp_query->query_vars;

	$sQuery = isset($query['s']) ? $query['s'] : '';

	// remove all whitespace (including tabs and line ends)	
	$sQuery = preg_replace('/\s+/', '', $sQuery);
	$searchTerm = preg_replace('/\s+/', '', 'slug:'); 

	if (strpos($sQuery, $searchTerm) !== false) {
		foreach ((array)$query['search_terms'] as $term) {
	
			// get only slug name
			$slug = str_replace($searchTerm, '', $term); 

			// secure the slug name against sql injection
			$secured_slug = trim(esc_sql($wpdb->esc_like($slug))); 
			
			// add the search query to the posts search query filter
			$search = "AND wp_posts.post_name LIKE '%$secured_slug%'";
		}	
	}
	else{
		//if nothing found
		$secured_slug = esc_sql($wpdb->esc_like(' ')); 
		$search = "AND ((wp_posts.post_name LIKE '%$secured_slug%'))";
	}
	return $search;
}

add_filter('posts_search', 'search_by_slug_name', 100, 2);
