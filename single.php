<?php
/**
 * The Template for displaying all single posts.
 *
 * @package gp
 * @since gp 1.0
 */

get_header();

?>

<div id="page_grid" class="site site-with-sidebar">
	<?php get_sidebar( 'post' ); ?>
	<div id="content" class="site-content"><?php
		// TEST of Variable 
		if( get_field('horizontal_post_accordion') == 1) echo "<div style='display: none;' id='open_up'></div>";
		while ( have_posts() ) : the_post();

			get_template_part( 'content', 'single' );

			// If comments are open or we have at least one comment, load up the comment template
			if ( comments_open() || '0' != get_comments_number() ) {
				comments_template( '', true );
			}

		endwhile; ?>

	</div>

	

</div>

<?php get_footer(); ?>