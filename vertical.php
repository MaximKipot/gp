<?php
/**
 * Template Name: Vertical without subnavigation
 * @package gp
 * @since gp 1.0
 */

get_header();

?>
<?php get_sidebar( 'post' ); ?>

<div id="page_grid" class="site site-with-sidebar">
	
	
	<div id="content" class="site-content"><?php

		while ( have_posts() ) : the_post();

			get_template_part( 'content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template
			if ( comments_open() || '0' != get_comments_number() ) {
				comments_template( '', true );
			}

		endwhile; ?>

	</div>

	

</div>

<?php get_footer(); ?>