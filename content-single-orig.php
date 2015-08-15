<?php
/**
 * @package gp
 * @since gp 1.0
 */

$post_categories = get_the_category();
$posted_in = array();

if ( $post_categories && gp_categorized_blog() ) {

	if ( count( $post_categories ) > 1 ) {

		foreach ( $post_categories as $post_cat ) {
			if ( $post_cat->term_id != 1 ) {
				$posted_in[] = $post_cat->name;
			}
		}

	} elseif ( !in_category( 1 ) ) {
		$posted_in[] = $post_categories[0]->name;
	}

}

?>
<article id="content-single" data-accordion="" data-column-width="430" data-gap="70"  id="post-<?php the_ID(); ?> " <?php post_class(); ?>>
	<header class="entry-header"><?php

		if ( has_post_thumbnail() ) :

			$image_info = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'gp-max' );
			$image = $image_info[0];

			?>
			<div class="post-image">
				<img src="<?php echo esc_url( $image ); ?>" width="<?php echo $image_info[1]; ?>" height="<?php echo $image_info[2]; ?>" alt="" />
				<?php

				if ( get_post_format() == 'quote' ) :
					gp_quote();

				elseif ( get_post_format() == 'link' ) :
					gp_link();

				else : ?>
					<div class="js-vertical-center">
						<div class="cover">
							<?php

								if ( $posted_in ) {
									printf( '<p>' . __( 'posted in %s', 'gp' ) . '</p> ', join( ', ', $posted_in ) );
								}

							?>
						</div>
					</div>

					<?php

				endif;

				?>
			</div><?php

		elseif ( get_post_format() == 'quote' ) :
			/**
			 * Post without a thumbnail. Show Quote on top of solid color.
			 */
			gp_quote();


		elseif ( get_post_format() == 'video' ) :

			/**
			 * Post type is video, show video in place of the thumbnail image.
			 */
			gp_video();


		elseif ( get_post_format() == 'link' ) :

			/**
			 * Show big link.
			 */
			gp_link();


		endif; ?>
		
		
		<!-- MMM
		<div class="entry-meta">
			<?php gp_posted_by(); ?>
			<span class="sep"></span>
			<?php gp_posted_on(); ?>
		</div> -->
		

	</header>

	<div class="entry-content">
		<?php the_content(); ?>
		<!-- <div class="entry-navigation">
			<?php
				the_gp_tags( __( 'tagged with', 'gp' ) );
				gp_post_nav(); 
			?>
		</div> -->
		<?php edit_post_link( __( 'Edit', 'gp' ), '<span class="edit-link">', '</span>' ); ?>
	</div>

</article>