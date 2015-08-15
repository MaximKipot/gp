<?php
/**
 * Display projects filtered by type.
 *
 * Projects can be displayed either with horizontal or grid layout.
 * The template is picked using these rules:
 *
 *  1. If Project Type layout is specified in the project type options, then use the specified layout.
 *  2. If Project Type layout is specified in one of the project types ancestors, then inherit that layout.
 *  3. Look for pages with Horizontal Portfolio template, if found, use horizontal layout.
 *  4. Look for pages with Grid Portfolio template, if found, use grid layout.
 *  5. Use horizontal layout.
 */

$template = gp_project_type_layout( get_queried_object()->term_id );

global $wp_query;

add_filter( 'posts_orderby_request', 'gp_portfolio_orderby_filter' );
$wp_query->set( 'orderby', 'menu_order ID' );
$wp_query->set( 'order', 'ASC DESC' );
$wp_query->set( 'posts_per_page', 3000 );
$wp_query->query( $wp_query->query_vars );
remove_filter( 'posts_orderby_request', 'gp_portfolio_orderby_filter' );

require_once dirname( __FILE__ ) . '/' . $template;