<?php
/**
 * General sidebar.
 *
 * @package gp
 * @since gp 1.0
 */

?>
<div id="subnavigation_holder">
	<div id="subnavigation">
		
		<?php 

			

				$test_children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0&depth=0");

				// if($test_children) echo "GOT SOME";
				// else echo "NOOO";	

				  if(!$test_children) {
				  	$children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0&depth=0");
				  	$titlenamer = get_the_title($post->post_parent);
				  	$titlenamer_url = get_permalink($post->post_parent);
				  	$needed_id = $post->post_parent;
				  }
				  else {
				  	$children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0&depth=0");
				  	$titlenamer = get_the_title($post->ID);
				  	$titlenamer_url = get_permalink($post->ID);
				  	$needed_id = $post->ID;
				  }
				  if ($children) { ?>

				  <a href="<?php echo $titlenamer_url;?>" <?php if(is_page($needed_id)) echo "class=current_page_item" ?>><h1> <?php echo $titlenamer; ?> </h1></a>
				 	<div class="widget">
					  <ul class="submenu_left">
					  	<?php echo $children; ?>
					  </ul>
					</div>

					<?php } else { ?>
	 					<h1>
				        	<?php
				            	the_title();
				        	?>
				    	</h1>
					<?php
					}
			
			do_action( 'before_sidebar' );

			dynamic_sidebar( 'sidebar-main' );

		?>
	</div>
</div>