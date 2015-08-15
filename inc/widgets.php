<?php
/**
 * Widgets. Contains functionality related to theme's widgets.
 *
 * @since gp 1.0
 */


/**
 * Include custom widgets.
 */
require_once gp_WIDGETS_DIR . '/widget-project-types.php';


/**
 * Returns theme's default widget params.
 *
 * @since gp 1.0
 */
function gp_get_default_widget_params() {
    return array(
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            //'before_title' => '<div class="decoration"></div><h1 class="widget-title">',
            'before_title' => '<h1 class="widget-title">',
            'after_title' => '</h1>'
        );
}


/**
 * Register widgetized area and update sidebar with default widgets.
 *
 * @since gp 1.0
 */
function gp_widgets_init() {

    /**
     * Register sidebars.
     */

    $sidebar_main = array_merge(
            array( 'name' => __( 'General Sidebar', 'gp' ), 'id' => 'sidebar-main' ),
            gp_get_default_widget_params()
        );
    register_sidebar( $sidebar_main );

    // $sidebar_post = array_merge(
    //         array( 'name' => __( 'Blog Sidebar', 'gp' ), 'id' => 'sidebar-blog' ),
    //         gp_get_default_widget_params()
    //     );
    // register_sidebar( $sidebar_post );


    // $sidebar_post = array_merge(
    //         array( 'name' => __( 'Blog Post Sidebar', 'gp' ), 'id' => 'sidebar-post' ),
    //         gp_get_default_widget_params()
    //     );
    // register_sidebar( $sidebar_post );


    // $sidebar_portfolio = array_merge(
    //         array( 'name' => __( 'Portfolio Sidebar', 'gp' ), 'id' => 'sidebar-portfolio' ),
    //         gp_get_default_widget_params()
    //     );
    // register_sidebar( $sidebar_portfolio );


    // $sidebar_portfolio_single = array_merge(
    //         array( 'name' => __( 'Portfolio Project Sidebar', 'gp' ), 'id' => 'sidebar-portfolio-single' ),
    //         gp_get_default_widget_params()
    //     );
    // register_sidebar( $sidebar_portfolio_single );

    // $persy_fuer_kmu = array_merge(
    //         array( 'name' => __( 'Persy für KMU', 'gp' ), 'id' => 'persy-fuer-kmu' ),
    //         gp_get_default_widget_params()
    //     );
    // register_sidebar( $persy_fuer_kmu );

    // $persy_fuer_pdl = array_merge(
    //         array( 'name' => __( 'Persy für PDL', 'gp' ), 'id' => 'persy-fuer-pdl' ),
    //         gp_get_default_widget_params()
    //     );
    // register_sidebar( $persy_fuer_pdl );

    // $persy_support = array_merge(
    //         array( 'name' => __( 'Persy Support', 'gp' ), 'id' => 'persy-support' ),
    //         gp_get_default_widget_params()
    //     );
    // register_sidebar( $persy_support );

    $template_with_subnav = array_merge(
            array( 'name' => __( 'Template with subnav', 'gp' ), 'id' => 'sidebar-template-with-subnav' ),
            gp_get_default_widget_params()
        );
    register_sidebar( $template_with_subnav );


    // Removes the default styles that are packaged with the Recent Comments widget.
    add_filter( 'show_recent_comments_widget_style', '__return_false' );

}
add_action( 'widgets_init', 'gp_widgets_init' );