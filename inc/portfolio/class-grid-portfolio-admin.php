<?php

class GridPortfolioAdmin extends gpAdminPage {

    function __construct( $post_id ) {

        parent::__construct( $post_id );

        // Add options meta box
        add_meta_box( 'gp-portfolio-grid-meta', __( 'Grid Options', 'gp' ),
                      array( $this, 'admin_options_content' ), 'page', 'normal', 'low' );

        add_action( 'save_post', array( $this, 'admin_options_save' ), 2 );

    }

    static public function get_grid_size_options() {

        $size_options = array(
                        '5 4' => __( '5 columns, 4 rows', 'gp' ),
                        '5 3' => __( '5 columns, 3 rows', 'gp' ),
                        '4 3' => __( '4 columns, 3 rows', 'gp' ),
                        '3 3' => __( '3 columns, 3 rows', 'gp' ),
                        '3 2' => __( '3 columns, 2 rows', 'gp' )
                    );

        return apply_filters( 'gp_portfolio_grid_sizes', $size_options );

    }

    function admin_options_content() {

        $grid = new GridPortfolio( $this->post_id );

        ?>
        <div class="gp-meta-field">
            <label for="gp_portfolio_grid_size"><?php _e( 'Grid size', 'gp' ); ?></label>
            <div class="field"><?php

                $size_options = self::get_grid_size_options();

                ?>
                <select name="gp_portfolio_grid_size">
                    <?php echo it_array_to_select_options( $size_options, $grid->meta_grid_size ); ?>
                </select>
            </div>
        </div>
        <?php

    }

    function admin_options_save( $post_id ) {

        if ( ! it_check_save_action( $post_id, 'page' ) ) {
            return $post_id;
        }

        $grid = new GridPortfolio( $this->post_id );
        $grid->update_from_array( $_POST )->save();

    }

}
