<?php
/**
 * The main template file.
 *
 * @package gp
 * @since gp 1.0
 */

gp_add_html_class( 'horizontal-page horizontal-posts' );

get_header();

?>
	<?php get_sidebar( 'blog' ); ?>
	<div id="page_grid" class="variable-sizes clearfix"><?php

		if ( have_posts() ) : ?>

			<!-- <div class="horizontal-content"> -->
				<?php

				while ( have_posts() ) : the_post();
					/**
					 * Include the Post-Format-specific template for the content.
					 */
					?>
					<!-- <div class="horizontal-item"> -->
						<?php get_template_part( 'content', get_post_format() ); ?>
					<!-- </div> -->
					<?php

				endwhile;

				if ( $wp_query->max_num_pages > 1 ) :
					gp_content_paging();
				endif;

				?>
			<!-- </div> -->
			<?php

		else :

			get_template_part( 'no-results', 'index' );

		endif; ?>
	</div>


<?php

get_footer();

