<?php /*
 xxx_TeXXmplate Name: Horizontal Post
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

		
<!--

	<div>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="entry">
				<h2 id="postTitle"><?php the_title(); ?><?php edit_post_link(' <small>&#9997;</small>', '', ' '); ?></h2>
				<?php
					if(function_exists('dimox_breadcrumbs'))
						dimox_breadcrumbs();
                ?>
				<div id="twoColumns">
					<?php the_content(); ?>
				</div>
				<div class="clear"></div>
	        </div>
        <?php endwhile; endif; ?>
     </div>


-->
 <?php 

	// $_need_cat = get_field('page_category');
	// $wp_query = null;
	// $wp_query = new WP_Query();
	// $wp_query -> query('order=ASC&cat='.$_need_cat[0].'&showposts=1000'.'&paged='.$paged);
	// $_num = 1;

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