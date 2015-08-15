<?php
/**
 * A widget that displays available project types.
 * If this widget is used on a project page,
 * then it will only show the types that are assigned
 * to current project.
 * Otherwise it will show all available types that
 * are assigned to at least one project.
 *
 * @since gp 1.0
 */


class gp_Widget_Project_Types extends WP_Widget {

    function gp_Widget_Project_Types() {
        $widget_ops = array(
                'classname' => 'gp-project-types',
                'description' => __('A widget that displays project types.', 'gp' )
            );

        $control_ops = array(
                'width' => 300,
                'height' => 350,
                'id_base' => 'gp-project-types-widget'
            );

        $this->WP_Widget( 'gp-project-types-widget', __( 'Project Types', 'gp' ), $widget_ops, $control_ops );
    }

    function widget( $args, $instance ) {

        /**
         * If widget was not called using default parameters,
         * then do class and id substitution manually.
         */
        if ( strpos( $args['before_widget'], '%2$s' ) ) {
            $args['before_widget'] = sprintf( $args['before_widget'], $this->id_base, $this->widget_options['classname'] );
        }

        if ( is_single() ) :

            /**
             * We are on the project page.
             * So we show tags that belong to the active project.
             */

            $tags = wp_get_post_terms( get_the_ID(), 'gp-project-type' );

            if ( $tags ) :

                echo $args['before_widget'];
                echo $args['before_title'] . __( 'Project Type', 'gp' ) . $args['after_title'];

                ?>
                <ul><?php
                    foreach ( $tags as $tag ) : ?>
                        <li class="project-type-<?php echo $tag->term_id; ?>">
                            <a href="<?php echo get_term_link( $tag, 'gp-project-type' ); ?>">
                                <b class="hash">#</b>
                                <?php echo $tag->name; ?>
                            </a>
                        </li><?php
                    endforeach; ?>
                </ul><?php

                echo $args['after_widget'];

            endif;

        else :

            $active_tag_slug = get_query_var( 'gp-project-type' );
            $active_tag = false;
            $children = false;

            $widget_title = __( 'Project Types', 'gp' );

            if ( $active_tag_slug ) {
                $active_tag = get_term_by( 'slug', $active_tag_slug, 'gp-project-type' );
                $children = get_terms( 'gp-project-type', array(
                        'parent' => $active_tag->term_id,
                        'hide_empty' => false   // We need to detect if this is a parent element
                    ) );
            }

            // If this is a children, then show all siblings
            if ( $active_tag && $active_tag->parent ) {

                $tags = get_terms( 'gp-project-type', array( 'parent' => $active_tag->parent ) );

                $parent_tag = get_term( $active_tag->parent, 'gp-project-type' );

                $widget_title = $parent_tag->name;

                $all_link = esc_url( get_term_link( $parent_tag, 'gp-project-type' ) );
                $all_class = '';

            // If this is a parent, then show children
            } elseif ( $children ) {

                $tags = $children;

                $widget_title = $active_tag->name;

                $all_link = esc_url( get_term_link( $active_tag, 'gp-project-type' ) );
                $all_class = ' class="active"';

            // Show all tags
            } else {

                $tags = get_terms( 'gp-project-type' );

                $all_link = esc_url( get_permalink( gp_portfolio_base_id() ) );
                $all_class = $active_tag_slug ? '' : ' class="active"';

            }

            // Manual check if there are at least one category with a project count > 0
            $empty = true;
            if ( $tags ) {
                foreach ( $tags as $tag ) {
                    if ( $tag->count != 0 ) {
                        $empty = false;
                        break;
                    }
                }
            }

            if ( $empty ) {
                return '';
            }

            echo $args['before_widget'];
            echo $args['before_title'] . $widget_title . $args['after_title']; ?>
            <ul>
                <li class="project-type-all">
                    <a<?php echo $all_class; ?> href="<?php echo $all_link; ?>">
                        <?php _e( 'All', 'gp' ); ?>
                    </a>
                </li><?php

                foreach ( $tags as $tag ) :
                    $class = $active_tag_slug == $tag->slug ? ' class="active"' : ''; ?>
                    <li class="project-type-<?php echo $tag->term_id; ?>">
                        <a<?php echo $class; ?> href="<?php echo esc_url( get_term_link( $tag, 'gp-project-type' ) ); ?>">
                            <b class="hash">#</b>
                            <?php echo $tag->name; ?>
                        </a>
                    </li><?php
                endforeach;

            ?>
            </ul><?php

            echo $args['after_widget'];

        endif;

    }

    function update( $new_instance, $old_instance ) {

        return $instance;
    }


    function form( $instance ) {

        _e( 'This widget has no options.', 'gp' );

    }

}


function gp_widget_project_types() {
    register_widget( 'gp_Widget_Project_Types' );
}
add_action( 'widgets_init', 'gp_widget_project_types' );

