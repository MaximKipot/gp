<?php
/**
 * File contains funcionality used for Portfolio.
 *
 * @since gp 1.0
 */

// Load framework classes
require_once dirname( __FILE__ ) . '/class-gp-page.php';
require_once dirname( __FILE__ ) . '/class-gp-admin-page.php';

require_once dirname( __FILE__ ) . '/class-portfolio-project.php';          // Project
require_once dirname( __FILE__ ) . '/class-portfolio-media.php';            // Project Media
require_once dirname( __FILE__ ) . '/class-grid-portfolio.php';             // Grid Portfolio

// WP Admin Pages
require_once dirname( __FILE__ ) . '/class-portfolio-project-admin.php';    // Project Admin
require_once dirname( __FILE__ ) . '/class-grid-portfolio-admin.php';       // Grid Portfolio Admin

require_once dirname( __FILE__ ) . '/project-type.php';


/**
 * Initialize Portolio
 */
function gp_portfolio_init() {

    add_image_size( 'gp-portfolio-thumbnail', 90, 90, true );

    $portfolio_base = gp_portfolio_base_slug();

    /**
     * Cache $portfolio_base, if it has changed, then we need to flush rules.
     */
    $flush = false;
    $cached_portfolio_base = get_transient( 'gp_portfolio_slug' );

    if ( $cached_portfolio_base ) {
        if ( $portfolio_base != $cached_portfolio_base ) {
            $flush = true;
        }
    } else {
        $flush = true;
    }


    /**
     * First we register taxonomy, then custom post type.
     * The order is important, because of rewrite rules.
     */
    $args = array(
                    'label' => 'Project Types',
                    'singular_label' => 'Project Type',
                    'query_var' => true,
                    'show_in_nav_menus' => true,
                    'show_ui' => true,
                    'show_tagcloud' => false,
                    'hierarchical' => true,
                    'rewrite' => array(
                            'slug' => $portfolio_base
                        )
                );
    register_taxonomy( 'gp-project-type', 'gp_portfolio',  $args );


    /**
     * Register portfolio_project custom post type.
     */
    $args = array(
        'label' => __(' Portfolio', 'gp' ),
        'singular_label' => __( 'Project', 'gp' ),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'page',
        'hierarchical' => false,
        'rewrite' => false,
        'query_var' => true,
        'taxonomy' => 'gp-project-type',
        'has_archive' => true,
        'menu_icon' => get_template_directory_uri() . '/images/wp-admin/portfolio.png',
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' )
       );
    register_post_type( 'gp_portfolio' , $args );


    /**
     * Register portfolio Project Media File
     */
    $args = array(
        'label' => __(' Project Media', 'gp' ),
        'singular_label' => __( 'Project Media File', 'gp' ),
        'public' => false,
        'supports' => array( 'title' )
       );

    register_post_type( 'gp_portfolio_project_media' , $args );

    $portfolio_structure = '/' . $portfolio_base . '/%projecttype%/%gp_portfolio%';
    add_rewrite_tag( '%projecttype%', '([^&/]+)', 'gp_project_type=' );
    add_rewrite_tag( '%gp_portfolio%', '([^&/]+)', 'gp_portfolio=' );
    add_permastruct( 'gp_portfolio', $portfolio_structure, false );

    if ( $flush ) {
        it_flush_rewrite_rules();
        set_transient( 'gp_portfolio_slug', $portfolio_base, 60 * 60 * 24 );
    }

}
add_action( 'init', 'gp_portfolio_init', 1 );


function gp_portfolio_pre_get_posts( $query ) {

    if ( ! is_admin() ) {

        if ( ( isset( $query->query_vars['gp-project-type'] ) && $query->query_vars['gp-project-type'] ) ||
             ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == 'gp_portfolio' ) ) {

            // Disable project title altering for password protected and private projects.
            add_filter( 'protected_title_format', 'gp_post_title_formatting' );
            add_filter( 'private_title_format', 'gp_post_title_formatting' );

        }

    }


}

add_action( 'pre_get_posts', 'gp_portfolio_pre_get_posts' );

/**
 * Initialize Portfolio Admin
 */
function gp_portfolio_admin_init() {

    global $pagenow;

    $post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';

    if ( $post_id = it_get_post_id() ) {
        $post = get_post( $post_id );
        $post_type = $post->post_type;
    }

    if ( $post_type == 'gp_portfolio' ) {

        // Project List Page
        if ( 'edit.php' == $pagenow ) {

            // Set correct order of projects in admin.
            add_filter( 'pre_get_posts', 'gp_portfolio_admin_project_order' );

            // Custom columns in Project List
            add_filter( 'manage_edit-gp_portfolio_columns', 'gp_portfolio_admin_project_list_columns' );
            add_action( 'manage_posts_custom_column', 'gp_portfolio_project_list_column_data' );

        }

        // Post Edit or Post New Page
        if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'admin-ajax.php' ) ) ) {
            new PortfolioProjectAdmin( $post_id );
        }

    }

    if ( $post_id ) {

        if ( it_is_template( $post_id, 'template-portfolio-grid.php' ) ) {
            new GridPortfolioAdmin( $post_id );
        }

    }

}
add_action( 'admin_init', 'gp_portfolio_admin_init' );


/**
 * Set correct order of projects in admin.
 */
function gp_portfolio_admin_project_order( $wp_query ) {
    if ( $wp_query->query['post_type'] == 'gp_portfolio' ) {
        $wp_query->set( 'orderby', 'menu_order ID' );
        $wp_query->set( 'order', 'ASC DESC' );
    }
}


/**
 * Add additional columns in project list table.
 */
function gp_portfolio_admin_project_list_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => 'Project',
        'description' => 'Description',
        'link' => 'Link',
        'type' => 'Type of Project',
    );

    return $columns;

}


/**
 * Populate added columns with data.
 */
function gp_portfolio_project_list_column_data( $column ) {
    global $post;

    $project = new PortfolioProject( $post->ID );

    switch ( $column ) {
        case 'description':
            the_excerpt();
        break;

        case 'link':
            echo $project->meta_link;
        break;

        case 'type':
            echo get_the_term_list( $post->ID, 'gp-project-type', '', ', ', '' );
        break;
    }

}


/**
 * Query portfolio items.
 */
function gp_query_portfolio( $args = array() ) {

    add_filter( 'posts_orderby_request', 'gp_portfolio_orderby_filter' );

    $defaults = array(
            'post_type'          => 'gp_portfolio',
            'posts_per_page'     => -1,
            'orderby'            => 'menu_order ID',
            'post_status'        => 'publish',
            'order'              => 'ASC DESC'
        );

    $args = array_merge( $defaults, $args );

    $args = apply_filters( 'gp_query_portfolio_args', $args );

    $result = query_posts( $args );

    remove_filter( 'posts_orderby_request', 'gp_portfolio_orderby_filter' );

    return $result;

}


/**
 * Orders project by menu_order ASC and ID desc.
 */
function gp_portfolio_orderby_filter( $orderby ) {
    /**
     * Limit the use for a very specific case.
     */
    if ( 'wp_posts.menu_order,wp_posts.ID DESC' == $orderby ) {
        return 'wp_posts.menu_order ASC, wp_posts.ID DESC';
    }

    return $orderby;

}


/**
 * Returns next project according to the specified order.
 */
function gp_portfolio_get_next_project( $current_project ) {
    return gp_portfolio_get_adjacent_project( $current_project, 'next' );
}


/**
 * Returns previous project according to the specified order.
 */
function gp_portfolio_get_previous_project( $current_project ) {
    return gp_portfolio_get_adjacent_project( $current_project, 'previous' );
}


/**
 * Get next/previous project while ordering by menu_order DESC and id DESC.
 * That is newer items with same menu_order goes first.
 */
function gp_portfolio_get_adjacent_project( $current_project, $sibling = 'next' ) {
    global $wpdb;

    if ( !is_object($current_project) ) {
        return false;
    }

    $compare_id = 'next' === $sibling ? '<' : '>';

    /**
     * Select next post with the same menu_order but lower ID.
     */
    $where = $wpdb->prepare("WHERE
                                p.id $compare_id %d AND
                                p.menu_order = %d AND
                                p.post_type = 'gp_portfolio' AND
                                p.post_status = 'publish'",
                            $current_project->ID, $current_project->menu_order );

    if ( 'next' === $sibling ) {
        $sort  = "ORDER BY p.id DESC LIMIT 1";
    } else {
        $sort  = "ORDER BY p.id ASC LIMIT 1";
    }

    $query = "SELECT p.* FROM $wpdb->posts AS p $where $sort";

    $result = $wpdb->get_row( $query );

    if ( null === $result ) {

        /**
         * No project with the same menu order found. Now select
         * a project with a lower menu order.
         */

        if ( 'next' === $sibling ) {
            $sort  = "ORDER BY p.menu_order ASC, p.id DESC LIMIT 1";
            $compare_menu_order = '>';
        } else {
            $sort  = "ORDER BY p.menu_order DESC, p.id ASC LIMIT 1";
            $compare_menu_order = '<';
        }

        $where = $wpdb->prepare("WHERE
                                p.menu_order $compare_menu_order %d AND
                                p.post_type = 'gp_portfolio' AND
                                p.post_status = 'publish'",
                            $current_project->menu_order );

        $query = "SELECT p.* FROM $wpdb->posts AS p $where $sort";

        $result = $wpdb->get_row( $query );

    }

    return $result;

}


/**
 * Looks for template-portfolio.php or template-portfolio-grid.php page id.
 */
function gp_portfolio_base_id() {

    $portfolio_page = it_find_page_by_template( 'template-portfolio.php' );
    if ( $portfolio_page ) {
        return $portfolio_page[0]->ID;
    } else {
        $portfolio_page = it_find_page_by_template( 'template-portfolio-grid.php' );
        if ( $portfolio_page ) {
            return $portfolio_page[0]->ID;
        } else {
            return 0;
        }
    }

}


/**
 * Return template-portfolio.php or template-portfolio-grid.php slug to be used
 * in portfolio project URL.
 */
function gp_portfolio_base_slug() {

    $portfolio_page = it_find_page_by_template( 'template-portfolio.php' );
    if ( $portfolio_page ) {
        return $portfolio_page[0]->post_name;
    } else {
        return 'portfolio';
    }

}


/**
 * Generate correct links using gp_portfolio_base_slug().
 */
function gp_portfolio_permalink( $permalink, $post, $leavename ) {

    /**
     * If there's an error with post, or this is not gp_portfolio
     * or we are not using fancy links.
     */
    if ( is_wp_error( $post ) || 'gp_portfolio' != $post->post_type || empty( $permalink ) ) {
        return $permalink;
    }

    /**
     * Find out project type.
     */
    $project_type = '';

    if ( strpos( $permalink, '%projecttype%') !== false ) {

        $terms = get_the_terms( $post->ID, 'gp-project-type' );

        if ( $terms ) {
            // sort terms by ID.
            usort( $terms, '_usort_terms_by_ID' );
            $project_type = $terms[0]->slug;
        } else {
            $project_type = 'uncategorized';
        }

    }

    $rewrite_codes = array(
            '%projecttype%',
            $leavename ? '' : '%gp_portfolio%'
        );

    $rewrite_replace = array(
            $project_type,
            $post->post_name
        );

    $permalink = str_replace( $rewrite_codes, $rewrite_replace, $permalink );

    return $permalink;

}
add_filter( 'post_type_link', 'gp_portfolio_permalink' , 10, 3 );


/**
 * Exclude password protected posts.
 */
function gp_portfolio_posts_where( $where, $query ) {

    if ( ! is_admin() ) {

        // Remove password protected prjects from portfolio lists.
        if ( ( isset( $query->query_vars['gp-project-type'] ) && $query->query_vars['gp-project-type'] ) ||
             ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == 'gp_portfolio' ) ) {

            $hide_password_protected_projects = of_get_option( 'gp_hide_password_protected_projects' );

            if ( $hide_password_protected_projects ) {
                $where .= " AND post_password = '' ";
            }

        }

    }

    return $where;

}

add_filter( 'posts_where', 'gp_portfolio_posts_where', 10, 2 );


/**
 * Removes "Protected: %s" title formatting.
 */
function gp_post_title_formatting( $title ) {
    return '%s';
}