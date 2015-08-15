<?php
/**
 * The template for displaying 404 pages (Not Found).
 * Template Name: 404 Not Found
 */

gp_add_html_class( 'horizontal-page no-scroll' );
get_header();

/**
 * Find pages that have full page slider template. Search those pages for big images.
 */


?>
<div id="main" class="site">
		<article id="post-0"  id="content-single" id="post-<?php the_ID(); ?> " class="post error404 not-found js-vertical-center">
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>

</div>

<?php

get_footer();


?>