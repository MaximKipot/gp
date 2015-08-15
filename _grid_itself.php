<?php 
// Generate set of grid elements made of POSTS
$_start = time();
while ($wp_query->have_posts()) : $wp_query->the_post(); 
        // Decision making and variable generation

        // scroll or not
        $_scroll_it = "";
        if( get_field('grid_scroll') == 1) $_scroll_it = "_scroll_it";

        // default variables
        $_element_type_class = "";
        $_element_parent_type_class = "";
        $_show_img = false;
        $_show_all_elements = "";
        $_follow_link = "";
        $_follow_link_style = "";
        $_show_all_elements = true;
        $_target = "";
        $_follow_link_outer = "";


        // Link type and form	
		switch(get_field('grid_link_choice')) {
			case 'internal_link':
				$_post_link = get_field('grid_element_link_internal');
				//echo $_post_link;
				// $_follow_link = "onclick='location.href=\"".$_post_link."\"'";
				break;
			case 'post_type':
				$_post_link = get_permalink($post->ID);
				// $_follow_link = "onclick='location.href=\"".$_post_link."\"'";
				break;
			case 'another_post':
				$_post_link = get_permalink(get_field('grid_element_another_post_link'));
				// $_follow_link = "onclick='location.href=\"".$_post_link."\"'";
				break;
			case 'external_link':
				$_post_link = get_field('grid_element_link_external');
				$_target = " target='_blank'";
				// $_follow_link = "onclick=window.open(\"".$_post_link."\")";
				break;
		}

		// define follow link for buttons
		// $_follow_link = "onclick=\"location.href='".$_post_link."'\"";


		// type of element and corresponding variables and settings
		$_field_type = get_field('grid_element_type');
        switch($_field_type) {
        	case 'text_only':
        		$_element_type = "";
        		$_element_parent_type_class = "";
        		break;
        	case 'image_only':
        	case 'html':
        		$_show_all_elements = false;
        		$_show_img = true;
        		$_element_type = "";
        		$_element_parent_type_class = "";
        		$_follow_link_style = "cursor: pointer;";
        		$_follow_link_outer = "onclick=\"window.open('".$_post_link."')\"";
        		break;
        	case 'image_bg_text':
        		$_show_img = true;
        		$_element_type = "";
        		$_element_parent_type_class = "";
        		break;
        	case 'image_left_small_text':
        		$_show_img = true;
        		$_element_type = "image_overlay_left image_overlay_horizontal";
        		$_element_parent_type_class = "image_overlay_left_parent";
        		break;
        	case 'image_left_big_text':
        		$_show_img = true;
        		$_element_type = "image_overlay_left_big image_overlay_horizontal";
        		$_element_parent_type_class = "image_overlay_left_big_parent";
        		break;
        	case 'image_top_small_text':
        		$_show_img = true;
        		$_element_type = "image_overlay_top image_overlay_vertical";
        		$_element_parent_type_class = "image_overlay_top_parent";
        		break;
        	case 'image_top_big_text':
        		$_show_img = true;
        		$_element_type = "image_overlay_top_big image_overlay_vertical";
        		$_element_parent_type_class = "image_overlay_top_big_parent";
        		break;
        }

    ?>
	

	<?php
		// width of element
		$_width = get_field('grid_element_width') * 2;
		if(get_field('grid_element_width') == 'max') $_width = get_field('grid_element_width');

		// height of element
		$_height = get_field('grid_element_height') * 2;
		if(get_field('grid_element_height') == 'max') $_height = get_field('grid_element_height');
		
		$_show_button = get_field('grid_button_yes_no');	

		// whether to Link an element at all
		$_dont_link_it = get_field('grid_dont_link_element');
		if( $_dont_link_it == true ) {
			$_post_link = '#';
			$_follow_link = '';
			$_show_button = false;	
			$_target = '';
		}

		// whether element gets the border
		$_grid_no_border = get_field('grid_no_border');
		if( $_grid_no_border == true ) {
			$_element_border = 'inner_element_no_border';
		} else {
			$_element_border = 'inner_element_border';
		}

		// whether element gets the shadow
		$_grid_no_shadow = get_field('grid_no_shadow');
		if( $_grid_no_shadow == true ) {
			$_element_shadow = '';
		} else {
			$_element_shadow = 'shadow';
		}

					
		// title custom color if defined
		$_grid_title_custom_color = get_field('grid_title_custom_color');
		if( $_grid_title_custom_color != '' && get_field('grid_element_style') == 'custom') {
			$_custom_title_color = 'color:'.$_grid_title_custom_color;
		} else {
			$_custom_title_color = '';
		}

		// shorttext custom color if defined
		$_grid_short_text_custom_color = get_field('grid_short_text_custom_color');
		if( $_grid_short_text_custom_color != ''  && get_field('grid_element_style') == 'custom') {
			$_custom_short_text_color = 'color:'.$_grid_short_text_custom_color;
		} else {
			$_custom_short_text_color = '';
		}

		// background custom color if defined
		$_grid_bg_custom_color = get_field('grid_background_custom_color');
		if( $_grid_bg_custom_color != ''  && get_field('grid_element_style') == 'custom') {
			$_custom_bg_color = "background-color:".$_grid_bg_custom_color;
		} else {
			$_custom_bg_color = '';
		}

		// border custom color if defined
		$_grid_border_custom_color = get_field('grid_border_custom_color');
		if( $_grid_border_custom_color != ''  && get_field('grid_element_style') == 'custom') {
			$_custom_border_color = "border-color:".$_grid_border_custom_color;
		} else {
			$_custom_border_color = '';
		}

		// title underline custom color if defined
		$_grid_title_underline_custom_color = get_field('grid_title_underline_custom_color');
		$_post_id = $post->ID; 
		if( /*$_grid_border_custom_color != ''  && */get_field('grid_element_style') == 'custom') {
			$_custom_underline_color_style = '<style>.body_horizontal h2.article_title.underline_'.$_post_id.':after, .body_vertical h2.article_title.underline_'.$_post_id.':after {background: '.$_grid_title_underline_custom_color.'}</style>';
			$_custom_underline_color_class = 'underline_'.$_post_id;
		} else {
			$_custom_underline_color_style  = '';
			$_custom_underline_color_class  = '';
		}

		// echo $_grid_title_underline_custom_color;
		// echo '---'.$_grid_border_custom_color.'---';
		

		// Getting the Tags
		$the_tags = '';
		$posttags = get_the_tags();
		if ($posttags) {
			foreach($posttags as $tag) {
				$the_tags .= ' '.str_replace(" ", "_", $tag->name); 
			}
		}

		$category = get_the_category(); 
		$the_cats = '';
		if ($category) {
		 	foreach($category as $cat) {
		 		$the_cats .= ' '.$cat->category_nicename; 
		 	}
		}

		// Resize or not class
		if(get_field('grid_element_dont_resize') == 1) $_resize_not = 'grid_element_fixed_size';
		else $_resize_not = "";

		$_visibility_class="";
		if(get_field('grid_item_visibility') != 'visible_everywhere') {
			$_visibility_class = get_field('grid_item_visibility');
		}

	?>
	
	<!-- single grid element -->
    <article <?php //echo $_follow_link_outer; ?> style="<?php echo $_follow_link_style;?>"  class="<?php the_ID();?> <?php echo $_visibility_class;?> <?php the_field('grid_element_style')?> <?php echo $_resize_not;?> <?php the_field('grid_short_text_color'); ?> element w_<?php echo $_width; ?> h_<?php echo $_height; ?> isotope-item <?php echo $the_tags;?> <?php echo $the_cats;?>" data-proportional-width="<?php echo $_width; ?>" data-proportional-height="<?php echo $_height; ?>">
        <div style='<?php echo "$_custom_bg_color;" ?> <?php echo "$_custom_border_color;" ?>' class="<?php the_field('grid_element_class')?> inner_element <?php echo $_element_shadow;?> background_<?php the_field('grid_background_color');?> border_<?php the_field('grid_border_color');?> <?php echo $_element_border;?>">
        	<?php 
        		if($_show_img == true) {
        			// showing the image for the element
        			$_image = get_field('grid_element_bild');
        			if($_field_type == 'html') {
        				?>
        					<a href="<?php echo $_post_link; ?>" style="display: inline-block; width: 100%; height: 100%; cursor: pointer;" <?php echo $_target; ?>><div class="image_overlay <?php echo $_element_type; ?>"><?php the_content();?></div></a>
        				<?php
        			} else {
        				if( $_dont_link_it == true ) {
						?>
							<div class="image_overlay <?php the_field('grid_element_bg_bild_position');?> <?php echo $_element_type; ?>"><img alt="<?php echo $_image[alt]; ?>" src="<?php echo $_image[url]; ?>"></div>
						<?php
						} else {	
						?>
							<a href="<?php echo $_post_link; ?>" style="position: absolute; display: inline-block; width: 100%; height: 100%; cursor: pointer;" <?php echo $_target; ?>><div class="image_overlay <?php the_field('grid_element_bg_bild_position');?> <?php echo $_element_type; ?>"><img  alt="<?php echo $_image[alt]; ?>" src="<?php echo $_image[url]; ?>"></div></a>
						<?php
						}
        			}
					
        		}
        	?>
        	<?php 


        	// the case when not only image is shown
        	if($_show_all_elements == true) { ?>
        		<?php if( $_dont_link_it != true ){ ?><a href="<?php echo $_post_link; ?>" <?php echo $_target; ?> style="position: absolute; display: block; width: 100%; height: 100%; cursor: pointer;"><?php } ?>
	            	<div class="inner_content <?php echo $_scroll_it;?> <?php echo $_element_parent_type_class;?>">
						<?php 
							// if there is an element title
							if(get_field('grid_title') || get_the_title()) { 
								
								$_title = get_the_title();
								if(get_field('grid_title')) $_title = get_field('grid_title');
								echo $_custom_underline_color_style;
							
							?>

							<h2 class="article_title <?php the_field('grid_title_color'); ?> <?php echo $_custom_underline_color_class; ?>">
								<?php
								// if( $_dont_link_it == true ) {
								?>
									<span  style="<?php echo $_custom_title_color; ?>"><?php echo $_title; ?></span>
								<?php
								// } else {	
								// 	echo $_title; 
								// }
								?>
							</h2>
						<?php } ?>
						<?php 
							// if theres a grid element description
							if(get_field('grid_short_text')) { ?>
								<div class="article_short <?php the_field('grid_short_text_color'); ?>" style="<?php echo $_custom_short_text_color; ?>">
									<?php the_field('grid_short_text'); ?>
								</div>
							<?php
							}  
							// whether to show the button
							if( $_show_button == 1) { ?>	
								<button class="<?php the_field('grid_button_style');?>" <?php echo $_follow_link; ?>>
									<?php 
										$_grid_button_text = "Mehr Infos";
										if(get_field('grid_button_text')) $_grid_button_text = get_field('grid_button_text');
										echo $_grid_button_text;
									?>
								</button>
						<?php } ?>
						
	            	</div>
            	<?php if( $_dont_link_it != true ){ ?> </a> <?php } ?>
			<?php } ?>

        </div>
        
        
	</article>    	
<?php endwhile; ?>

<?php 
$_end = time();
// echo $_start."<br>";
// echo $_end."<br>";
// echo 'Time spent:'.($_end - $_start);
?>
