<?php
/**
 * Portfolio Single Sidebar.
 *
 * @package gp
 * @since gp 1.0
 */

?>
<div class="sidebar sidebar-portfolio widget-area">

    <?php do_action( 'before_sidebar' ); ?>

    <div class="scroll-container">
        <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
        <div class="viewport">
            <div class="overview"><?php

                if ( ! dynamic_sidebar( 'sidebar-portfolio' ) ) :

                    the_widget( 'gp_Widget_Project_Types', null, gp_get_default_widget_params() );

                endif; ?>

            </div>
        </div>
    </div>

</div>