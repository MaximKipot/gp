<?php

class PortfolioProject extends gpPage {

    protected $META_PREFIX = 'gp_project_';
    protected $meta_data_defaults = array(
            'subtitle' => '',
            'link'     => '',
            'info'     => array(),
            'other_projects' => 0,
            'back_to_link' => 0
        );

    function get_featured_media() {
        return PortfolioMedia::get_featured_media( $this->post_id );
    }

    function get_media() {
        return PortfolioMedia::all( $this->post_id );
    }

    function get_tags() {
        return wp_get_post_terms( $this->post_id, 'gp-project-type' );
    }

    static function all( $args = array() ) {

        $defaults = array(
            'post_type'      => 'gp_portfolio',
            'posts_per_page' => -1,
            'orderby'        => 'ID',
            'post_status'    => 'any',
            'order'          => 'DESC'
        );

        $args = array_merge( $defaults, $args );

        $posts = get_posts( $args );

        if ( $posts ) {

            foreach ( $posts as $k => $post ) {
                $posts[$k] = new PortfolioProject( $post->ID );
            }

            return $posts;

        } else {

            return array();

        }

    }

    static function posts_to_projects( $posts ) {

        $projects = array();

        if ( is_array( $posts ) ) {
            foreach ( $posts as $post ) {
                if ( $post->ID ) {
                    $projects[] = new PortfolioProject( $post->ID );
                }
            }
        }

        return $projects;

    }

    function get_back_link() {

        if ( $this->meta_back_to_link ) {

            $project_type = get_term( $this->meta_back_to_link, 'gp-project-type' );

            if ( $project_type ) {
                return get_term_link( $project_type, 'gp-project-type' );
            }

        }

        return get_permalink( gp_portfolio_base_id() );

    }

    function get_other_projects( $number_to_display = 8, $slice_index = 1 ) {

        $args = array();

        if ( $this->meta_other_projects ) {

            $project_type = get_term( $this->meta_other_projects, 'gp-project-type' );

            if ( $project_type ) {

                $args['gp-project-type'] = $project_type->slug;

            }

        }

        $all = gp_query_portfolio( $args );
        wp_reset_query();

        $count = count( $all );

        // if we don't have enough projects, return all
        if ( $count <= $number_to_display ) {
            return self::posts_to_projects( $all );
        }

        $current_project_index = false;
        foreach ( $all as $index => $project ) {
            if ( $this->post->ID == $project->ID ) {
                $current_project_index = $index;
                break;
            }
        }

        // if we can't find current project, return first $number_to_display
        if ( $current_project_index === false ) {
            return self::posts_to_projects( array_slice( $all, 0, $number_to_display ) );
        }

        if ( $current_project_index + $number_to_display > $count ) {
            /**
             * Means that our current project is in the last N.
             * Let's return last N.
             */
            return self::posts_to_projects( array_slice( $all, $count - $number_to_display ) );
        }

        $slice_offset = $current_project_index - $slice_index;

        if ( $slice_offset < 0 ) {
            $slice_offset = 0;
        }

        return self::posts_to_projects( array_slice( $all, $slice_offset, $number_to_display ) );

    }

}

