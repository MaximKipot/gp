<?php
/**
 * @package gp
 * @since gp 1.0
 */

global $post;

$article_width = '';

if ( get_post_format() == 'video' ) {
	$size = gp_video_get_size();
	if ( $size ) {
		$ratio = $size[0] / $size[1];
		$article_width = round($ratio * 320);
	}
}

$article_width = !empty( $article_width ) ? ' style="width: ' . $article_width . 'px"' : '';

?>
<!-- <article idi="content" id="post-<?php the_ID(); ?>" <?php post_class(); ?><?php echo $article_width; ?>><?php
	if ( has_post_thumbnail() && ( ! in_array( get_post_format(), array( 'aside', 'video', 'link' ) ) ) ) :

		/**
		 * Post has a thumbnail. Show It.
		 */

		$image_info = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'gp-thumbnail' );
		$image = $image_info[0];

		?>
		<a class="thumbnail" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Read more about %s', 'gp' ), the_title_attribute( 'echo=0' ) ) ); ?>">
			<img src="<?php echo esc_url( $image ); ?>" class="resizable" width="<?php echo $image_info[1]; ?>" height="<?php echo $image_info[2]; ?>" alt="" /><?php
			/**
			 * If post format is Quote or Link, then show the quote overlay on top of the image.
			 */
			if ( get_post_format() == 'quote' ) {
				gp_quote();
			}

			if ( get_post_format() == 'link' ) {
				gp_link();
			}

		?>
		</a>
		<?php

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

	endif; ?> -->
	<!-- <div class="text-contents"><?php

		if ( get_post_format() != 'aside' ) : ?>

			<header class="entry-header">
				<h1 class="entry-titles">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'gp' ), the_title_attribute( 'echo=0' ) ) ); ?>">
						<?php the_title(); ?>
					</a>
					<?php

						if ( 'post' == get_post_type() ) {
							gp_posted_on();
						}

						if ( is_sticky() ) : ?>
							<div class="sticky-icon icon-star" title="<?php echo esc_attr( __( 'Sticky post', 'gp' ) ); ?>"></div><?php
						endif;

					?>
				</h1>
			</header><?php

		endif; ?>
 -->
		<!-- <div class="entry-summary"><?php

			if ( ! gp_post_has_media() ) {

				// Post has no media, so there is additional space. Let's increase the excerpt length.
				add_filter( 'excerpt_length', 'gp_increased_excerpt_lenght', 1001 );

			}

			the_excerpt();

			remove_filter( 'excerpt_length', 'gp_increased_excerpt_lenght', 1001 );

			?>
		</div> -->








		<?php 
	        // Decision making and variable generation

		        // scroll or not
		        $_scroll_it = "";
		        if( get_field('grid_scroll') == 1) $_scroll_it = "_scroll_it";

		        $_element_type_class = "";
		        $_element_parent_type_class = "";
		        $_show_img = false;

		        switch(get_field('grid_element_type')) {
		        	case 'text_only':
		        		$_element_type = "";
		        		$_element_parent_type_class = "";
		        		break;
		        	case 'image_only':
		        		$_show_img = true;
		        		$_element_type = "";
		        		$_element_parent_type_class = "";
		        		break;
		        	case 'image_bg_text':
		        		$_show_img = true;
		        		$_element_type = "";
		        		$_element_parent_type_class = "";
		        		break;
		        	case 'image_left_small_text':
		        		$_show_img = true;
		        		$_element_type = "image_overlay_left";
		        		$_element_parent_type_class = "image_overlay_left_parent";
		        		break;
		        	case 'image_left_big_text':
		        		$_show_img = true;
		        		$_element_type = "image_overlay_left";
		        		$_element_parent_type_class = "image_overlay_left_big_parent";
		        		break;
		        	case 'image_top_small_text':
		        		$_show_img = true;
		        		$_element_type = "image_overlay_top";
		        		$_element_parent_type_class = "image_overlay_top_parent";
		        		break;
		        	case 'image_top_big_text':
		        		$_show_img = true;
		        		$_element_type = "image_overlay_top";
		        		$_element_parent_type_class = "image_overlay_top_big_parent";
		        		break;
		        }

	        ?>

			<?php
				$_width = get_field('grid_element_width') * 2;
				if(get_field('grid_element_width') == 'max') $_width = get_field('grid_element_width');

				$_height = get_field('grid_element_height') * 2;
				if(get_field('grid_element_height') == 'max') $_height = get_field('grid_element_height');
				
			?>
			
	        <article style="color: #<?php the_field('grid_short_text_color'); ?>;" class="element w_<?php echo $_width; ?> h_<?php echo $_height; ?> isotope-item" data-proportional-width="<?php echo $_width; ?>" data-proportional-height="<?php echo $_height; ?>">
	            <div class="inner_element border" style="background-color: #<?php the_field('grid_background_color');?>;border-color: #<?php the_field('grid_border_color');?>;">
	            	<?php 
	            		if($_show_img == true) {
	            			$_image = get_field('grid_element_bild');
	            			?>
	            			<div class="image_overlay <?php echo $_element_type; ?>" style="background-image: url(<?php echo $_image[url]; ?>);"></div>
	            			<?php
	            		}
	            	?>
	            	<div class="inner_content <?php echo $_scroll_it;?> <?php echo $_element_parent_type_class;?>">
						<?php if(get_field('grid_title') || get_the_title()) { 
								$_title = get_the_title();
								if(get_field('grid_title')) $_title = get_field('grid_title');
							?>
							<h2 style="color: #<?php the_field('grid_title_color'); ?>">
								<?php echo $_title; ?>
							</h2>
						<?php } ?>
						<?php if(get_field('grid_short_text')) { ?>
							<div class="article_short">
								<?php the_field('grid_short_text'); ?>
							</div>
						<?php } ?>
						<?php if(get_field('grid_button_yes_no') == 1) { ?>
						
							<?php
								switch(get_field('grid_button_choice')) {
									case 'internal_link':
										$_button_link = get_field('grid_button_link_internal');
										break;
									case 'post_type':
										$_button_link = get_permalink($post->ID);
										break;
									case 'external_link':
										$_button_link = get_field('grid_button_link_external');
										break;
								}	
							?>		
							<button class="<?php the_field('grid_button_style');?>" onclick="location.href='<?php echo $_button_link; ?>'">
								<?php 
									$_grid_button_text = "MEHR INFOS";
									if(get_field('grid_button_text')) $_grid_button_text = get_field('grid_button_text');
									echo $_grid_button_text;
								?>
							</button>
						<?php } ?>
						
	            	</div>
	            </div>
	            
	            
			</article>
		
<!-- 
	</div>

</article> -->
