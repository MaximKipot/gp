<?php

/**
 * --------------------------------------------------------------------
 * Project Type WP Admin functions
 * --------------------------------------------------------------------
 */


class gpProjectTypeOptions {
    protected $id;
    protected $options = array(
            'layout' => '',
            'grid_size' => ''
        );

    function __construct( $project_type_term_id ) {
        $this->id = $project_type_term_id;
        return $this;
    }

    function __set( $name, $value ) {
        if ( isset( $this->options[ $name ] ) ) {
            $this->options[ $name ] = $value;
            update_option( 'project_type_' . $name . '_' . $this->id, $value );
            return $this;
        }
    }

    function __get( $name ) {
        if ( isset( $this->options[ $name ] ) ) {
            return get_option( 'project_type_' . $name . '_' . $this->id );
        }
    }

    function delete() {
        delete_option( 'project_type_grid_size_' . $this->id );
        delete_option( 'project_type_layout_' . $this->id );
    }
}


function gp_portfolio_layouts() {
    return array(
            '' => __( 'Default', 'gp' ),
            'horizontal' => __( 'Horizontal', 'gp' ),
            'grid' => __( 'Grid', 'gp' )
        );
}


function gp_project_type_grid_size( $project_type_id ) {

    $options = new gpProjectTypeOptions( $project_type_id );

    $grid_size = $options->grid_size;

    if ( ! $grid_size ) {
        $project_type = get_term_by( 'id', $project_type_id, 'gp-project-type' );

        if ( $project_type->parent ) {
            for ( $i = 0; $i <= 10; $i++ ) {
                $parent = get_term_by( 'id', $project_type->parent, 'gp-project-type' );

                $parent_options = new gpProjectTypeOptions( $parent->term_id );
                $grid_size = $parent_options->grid_size;

                if ( $grid_size || ( $parent->parent == 0 ) ) {
                    break;
                }
            }
        }
    }

    return $grid_size ? explode( ' ', $grid_size ) : false;

}


function gp_project_type_layout( $project_type_id ) {

    $options = new gpProjectTypeOptions( $project_type_id );

    $template = $options->layout;

    if ( ! $template ) {
        $project_type = get_term_by( 'id', $project_type_id, 'gp-project-type' );

        if ( $project_type->parent ) {
            for ( $i = 0; $i <= 10; $i++ ) {
                $parent = get_term_by( 'id', $project_type->parent, 'gp-project-type' );

                $parent_options = new gpProjectTypeOptions( $parent->term_id );
                $template = $parent_options->layout;

                if ( $template || ( $parent->parent == 0 ) ) {
                    break;
                }
            }
        }
    }

    switch ( $template ) {

        case 'grid':
            $template = 'template-portfolio-grid.php';
            break;

        case 'horizontal':
            $template = 'template-portfolio.php';
            break;

        default:

            /*
             * Template is not set using Project Type options, so let's determinate it by ourselves
             * using following logic:
             *   1. If page with horizontal portfolio template is found, then use horizontal.
             *   2. If page with grid portfolio template is found, then use grid.
             *   3. If no page is found, then use horizontal.
             */

            $template = 'template-portfolio.php';

            $horizontal_portfolio = it_find_page_by_template( 'template-portfolio.php', array( 'post_status' => 'publish' ) );

            if ( $horizontal_portfolio ) {
                $template = 'template-portfolio.php';
            } else {
                $grid_portfolio = it_find_page_by_template( 'template-portfolio-grid.php', array( 'post_status' => 'publish' ) );

                if ( $grid_portfolio ) {
                    $template = 'template-portfolio-grid.php';
                }
            }

    }

    return $template;

}

/**
 * Project Type Edit Form
 */
function gp_project_type_edit_form( $project_type ) {

    $options = new gpProjectTypeOptions( $project_type->term_id );

    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="project-type-layout"><?php _e( 'Layout', 'gp' ); ?></label>
        </th>
        <td>
            <select name="project-type-layout" id="project-type-layout">
                <?php echo it_array_to_select_options( gp_portfolio_layouts(), $options->layout ); ?>
            </select>
            <br />
            <span class="description"><?php _e( 'Portfolio layout that will be used to display the project type.', 'gp' ); ?></span>
        </td>
    </tr>
    <tr id="project-type-layout-option" class="form-field"<?php if ( $options->layout != 'grid' ) { echo ' style="display: none"'; } ?>>
        <th scope="row" valign="top">
            <label for="project-type-grid-size"><?php _e( 'Grid Size', 'gp' ); ?></label>
        </th>
        <td>
            <select name="project-type-grid-size" id="project-type-grid-size">
                <?php echo it_array_to_select_options( GridPortfolioAdmin::get_grid_size_options(), $options->grid_size ? $options->grid_size : GridPortfolio::DEFAULT_GRID_SIZE ); ?>
            </select>
        </td>
    </tr>
    <?php
}
add_action( 'gp-project-type_edit_form_fields', 'gp_project_type_edit_form' );


/**
 * Project Type Create Form
 */
function gp_project_type_create_form() {
    ?>
    <div class="form-field">
        <label for="project-type-layout"><?php _e( 'Layout', 'gp' ); ?></label>
        <select name="project-type-layout" id="project-type-layout" class="postform">
            <?php echo it_array_to_select_options( gp_portfolio_layouts() ); ?>
        </select>
        <br>
        <span class="description"><?php _e( 'Portfolio layout that will be used to display the project type.', 'gp' ); ?></span>
    </div>
    <div id="project-type-layout-option" class="form-field" style="display: none">
        <label for="project-type-grid-size"><?php _e( 'Grid Size', 'gp' ); ?></label>
        <select name="project-type-grid-size" id="project-type-grid-size">
            <?php echo it_array_to_select_options( GridPortfolioAdmin::get_grid_size_options(), GridPortfolio::DEFAULT_GRID_SIZE ); ?>
        </select>
    </div>
    <?php
}
add_action( 'gp-project-type_add_form_fields', 'gp_project_type_create_form' );


/**
 * Saves project type additional options: layout and grid size.
 */
function gp_project_type_update( $term_id ) {
    if ( isset( $_POST['project-type-layout'] ) ) {

        // Since we use the same function for adding/updating terms,
        // check for different nonces.
        if ( isset( $_POST['_wpnonce_add-tag'] ) ) {
            check_admin_referer( 'add-tag', '_wpnonce_add-tag' );
        } else {
            check_admin_referer( 'update-tag_' . $term_id );
        }

        $tax = get_taxonomy( 'gp-project-type' );

        if ( current_user_can( $tax->cap->edit_terms ) ) {
            $options = new gpProjectTypeOptions( $term_id );
            $options->layout = $_POST['project-type-layout'];
            fb ( $term_id, $_POST['project-type-layout'] );
            if ( isset( $_POST['project-type-grid-size'] ) ) {
                $options->grid_size = $_POST['project-type-grid-size'];
            }
        }
    }
}
add_action( 'edit_gp-project-type', 'gp_project_type_update' );
add_action( 'create_gp-project-type', 'gp_project_type_update' );


/**
 * On Project Type term deletion deletes associated options.
 */
function gp_project_type_delete( $term_id ) {

    // Same action for bulk and single deletion
    if ( isset( $_POST['delete-tag'] ) ) {
        check_admin_referer( 'delete-tag_' . $term_id );
    } else {
        check_admin_referer( 'bulk-tags' );
    }

    $tax = get_taxonomy( 'gp-project-type' );
    if ( current_user_can( $tax->cap->delete_terms ) ) {
        $options = new gpProjectTypeOptions( $term_id );
        $options->delete();
    }

}

add_action( 'delete_gp-project-type', 'gp_project_type_delete' );