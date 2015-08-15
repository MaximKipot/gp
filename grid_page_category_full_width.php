<?php /*
 Template Name: Grid Page Category Fulll Width
 */
?>

<?php get_header(); ?>

<?php 

	// add_filter('post_thumbnail_html', 'remove_width_attribute', 10);
	// add_filter('image_send_to_editor', 'remove_width_attribute', 10);
	// add_filter('the_content', 'remove_width_attribute', 10);
?>

<?php //OptionTree Stuff
	
		// $theme_options = get_option('option_tree');
		// $team_cat = get_option_tree('team_cat', $theme_options);

		

?>

		

       
	<?php 
		$_need_cat = get_field('page_category');
		$_order_direction = get_field('page_grid_order');

		if(!$_order_direction) $_order_direction = 'desc';

		wp_reset_query();
		$wp_query = null;
		$wp_query = new WP_Query();
		$wp_query -> query(array ( 'cat' => $_need_cat, 'order' => strtoupper($_order_direction), 'orderby' => 'date', 'posts_per_page' => 1000, 'paged' => $paged ));
		$_num = 1;

		// echo "<pre>"; print_r($wp_query); echo "</pre>";
		// var_dump($wp_query->request);
	?>
		<?php  
			// get_sidebar( 'blog' ); 
		?>


<div id="page_grid"  class="variable-sizes clearfix content_no_left_nav">  
	<?php
		require_once( '_grid_itself.php' );
	?>
</div>

        <!-- <div id="commentsection">
			<?php comments_template(); ?>
        </div> -->

	
<?php get_footer(); ?>