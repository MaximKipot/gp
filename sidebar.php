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
		<h1>
	    	<?php
	        	the_title();
	    	?>
		</h1>
		
		<?php 
			
			do_action( 'before_sidebar' );

			dynamic_sidebar( 'sidebar-main' );

		?>
		
	</div>
</div>