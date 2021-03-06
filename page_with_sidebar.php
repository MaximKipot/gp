<?php
/**
 * The template for displaying all pages.
 * Texmplate Name: Horizontal with Subnavigation
 *
 * @package gp
 * @since gp 1.0
 */

get_header();

$with_sidebar = ! it_is_template( get_the_id(), 'template-full-width.php' );

?>


    
<?php get_sidebar( 'subnavigation' ); ?>
<div id="page_grid" data-pageid="page.php" class="site<?php if ( $with_sidebar ) : ?> site-with-sidebar<?php endif; ?>">
    <?php

        if ( $with_sidebar ) {
            get_sidebar();
            //add_filter( 'the_post', 'remove_h1');   
         }

    ?>

    <div id="content" class="site-content"><?php
    	while ( have_posts() ) : the_post();
    		get_template_part( 'content', 'page' );
    	endwhile; ?>
    </div>
    
</div>
<?php

get_footer();
