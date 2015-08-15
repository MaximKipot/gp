<?php 
/*
 Template Name: Grid Page Category
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
 	global $_need_cat;
	$_need_cat = get_field('page_category');

	// echo $_need_cat;

	$_order_direction = get_field('page_grid_order');

	// echo get_field('page_grid_order');

	if(!$_order_direction) $_order_direction = 'desc';

	wp_reset_query();
	$wp_query = null;
	$wp_query = new WP_Query();
	// $wp_query -> query('cat='.$_need_cat[0].'&orderby=date&order='.strtoupper($_order_direction).'&posts_per_page=1000'.'&paged='.$paged);
	$wp_query -> query(array ( 'cat' => $_need_cat, 'order' => strtoupper($_order_direction), 'orderby' => 'date', 'posts_per_page' => 1000, 'paged' => $paged ));
	$_num = 1;

	// var_dump($wp_query->request);
	// echo "<pre>"; print_r($wp_query); echo "</pre>";

 	get_sidebar( 'grid' );
?>

<div id="page_grid"  class="variable-sizes clearfix">  
	<?php
		require_once( '_grid_itself.php' );
	?>
</div>

        <!-- <div id="commentsection">
			<?php comments_template(); ?>
        </div> -->

	
<?php get_footer(); ?>