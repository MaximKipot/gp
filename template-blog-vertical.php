<?php
/**
 * T-MMM-emplate Name: Vertical Blog
 *
 * @package gp
 * @since gp 1.2
 */

gp_add_html_class( 'vertical-blog' );

// Use increased excerpt length
add_filter( 'excerpt_length', 'gp_increased_excerpt_lenght', 1002 );

get_header();

if ( is_page() ) {
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    query_posts( 'post_type=post&paged=' . $paged );
}

?>
<div id="main" class="site site-with-sidebar">
    <div id="content" class="site-content"><?php

        if ( have_posts() ) :

            while ( have_posts() ) : the_post();
                /**
                 * Include the Post-Format-specific template for the content.
                 */
                get_template_part( 'content', get_post_format() );

            endwhile;

            if ( $wp_query->max_num_pages > 1 ) :
                gp_content_paging();
            endif;

        else :

            get_template_part( 'no-results', 'index' );

        endif; ?>
    </div>

    <?php get_sidebar( 'blog' ); ?>

</div>

<?php

wp_reset_query();

get_footer();

