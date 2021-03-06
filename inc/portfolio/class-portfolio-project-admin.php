<?php

/**
 * ---------------------------------------------------------------
 * Portfolio Project Admin Class
 * ---------------------------------------------------------------
 */

class PortfolioProjectAdmin extends gpAdminPage {

    public $styles = array(
            array( 'gp-wp-admin-portfolio', 'portfolio.css' )
        );

    public $scripts = array(
            array( 'gp-wp-admin-portfolio', 'portfolio-project.js', array( 'jquery', 'backbone' ) )
        );

    function __construct( $post_id ) {

        parent::__construct( $post_id );

        add_action( 'admin_head', array( $this, 'admin_head' ) );

        /**
         * Meta box: Project Options
         */
        add_meta_box( 'gp-project-info-meta',
                      __( 'Project Options', 'gp' ),
                      array( $this, 'meta_box_options_content' ),
                      'gp_portfolio', 'normal', 'low' );

        add_action( 'save_post', array( $this, 'meta_box_options_content_save' ), 1, 1 );

        /**
         * Meta box: Project Media
         */
        add_meta_box( 'gp-project-media',
                      __( 'Project Media', 'gp' ),
                      array( $this, 'meta_box_media_content' ),
                      'gp_portfolio', 'normal', 'low' );

        add_action( 'wp_ajax_gp_project_media', array( $this, 'json_get_media' ) );

        add_action( 'wp_ajax_gp_project_media_save', array( $this, 'json_save_media' ) );


    }


    function admin_head() {

        echo '<script>window.gp_post_id = ' . $this->post_id . '</script>';

    }


    function json_get_media() {

        $media = PortfolioMedia::all( $this->post_id );

        $json = array();

        foreach ( $media as $media_item ) {
            $json_item = $media_item->to_array();

            if ( ( $json_item['type'] == 'image' ) && ( $json_item['attachment_id'] ) ) {
                if ( empty( $json_item['thumbnail_url'] ) ) {
                    $media_item->delete();
                    continue;
                }
            }

            $json[] = $json_item;
        }

        echo json_encode( $json );
        die( 1 );

    }


    function json_save_media() {

        global $wpdb;

        $defaults = array(
                '_method'       => 'create',
                'type'          => 'image',
                'description'   => '',
                'attachment_id' => '',
                'embed'         => '',
                'featured'      => 0,
                'published'     => 1
            );

        if ( isset( $_POST['test'] ) ) {
            // Temp workaround for mysterious issue
            if ( $_POST['test'] == '\"' ) {
                foreach ( $_POST as $key => $value ) {
                    $_POST[$key] = stripslashes( $value );
                }
            }
        }

        $args = array_merge( $defaults, $_POST );

        $nonce = isset( $_GET['nonce'] ) ? $_GET['nonce'] : '';

        if ( ! wp_verify_nonce( $nonce, 'portfolio_media_nonce' ) ) {
            echo 'Error: permission denied';
            die( 1 );
        }

        // create new
        if ( $args['_method'] == 'create' ) {

            $media_item = PortfolioMedia::create( $this->post_id );

            if ( $media_item->exists() ) {

                $media_item->meta_type          = $args['type'];
                $media_item->meta_attachment_id = $args['attachment_id'];
                /**
                 * Set menu order to current page's ID, it will be the highest
                 * for the newest post.
                 */
                $media_item->menu_order         = $media_item->post->ID;

                echo json_encode( $media_item->save()->to_array() );

                die( 0 );

            }

            echo 'Error: unable to create media file.';
            die( 1 );

        }

        // update existing
        if ( ( $args['_method'] == 'update' ) && is_numeric( $_GET['id'] ) ) {

            $media_item = new PortfolioMedia( $args['id'] );

            if ( $media_item->exists() ) {

                /**
                 * If `featured` attribute has changed,
                 * then let's remove it from any other media item.
                 */

                if ( $args['featured'] != $media_item->meta_featured ) {

                    // Remove featured from every other object.
                    foreach ( PortfolioMedia::all( $this->post_id ) as $item ) {
                        if ( $item->post->ID != $args['id'] ) {
                            $item->meta_featured = 0;
                            $item->save();
                        }
                    }

                }

                $media_item->meta_description   = $args['description'];
                $media_item->meta_attachment_id = $args['attachment_id'];
                $media_item->meta_screenshot_id = $args['screenshot_id'];
                $media_item->meta_embed         = $args['embed'];
                $media_item->meta_featured      = $args['featured'];
                $media_item->meta_published     = $args['published'];
                $media_item->post->menu_order   = $args['order'];

                $media_item->save();

                echo json_encode( $media_item->to_array() );

                //echo '0';
                die( 0 );

            } else {

                echo 'Error: media item with ID ' . $args['id'] . ' cannot be found.';
                die( 1 );

            }

        }

        // delete record
        if ( ( $args['_method'] == 'delete' ) && is_numeric( $_GET['id'] ) ) {

            $media_item = new PortfolioMedia( $_GET['id'], $this->post_id );

            $media_item->delete();

            echo '0';
            die( 0 );

        }

        die('1');

    }


    function meta_box_media_content() {

        global $pagenow;

        wp_enqueue_script( 'jquery-ui-sortable' );

        ?>

        <div class="gp-admin-meta-box">

            <div class="global-controls">
                <?php
                    $new_post = 'post-new.php' == $pagenow;
                    $new_post_class = $new_post ? ' button-disabled' : '';
                ?>
                <a href="#" class="button<?php echo $new_post_class; ?>" id="gp-add-image"><?php _e( 'Add Image', 'gp' ); ?></a>
                <a href="#" class="button<?php echo $new_post_class; ?>" id="gp-add-video"><?php _e( 'Add Video', 'gp' ); ?></a><?php
                if ( $new_post_class ) :
                    _e( 'Please save this project first.', 'gp' );
                endif; ?>
            </div>

            <div class="gp-items-container media-items-container"
                id="media-items-container"
                data-iframe-title="<?php echo esc_attr( __( 'Add Images to Project', 'gp' ) ); ?>"
                data-iframe-button="<?php echo esc_attr( __( 'Add Image(s)', 'gp' ) ); ?>">
                <input type="hidden" name="portfolio_media_nonce" value="<?php echo wp_create_nonce( 'portfolio_media_nonce' ); ?>" />
            </div>

            <input type="hidden" id="lang-select-screenshot" value="<?php _e( 'Select Screenshot', 'gp' ); ?>" />

            <?php

            $lang_image             = __( 'Image', 'gp' );
            $lang_video             = __( 'Video', 'gp' );
            $lang_edit              = __( 'Edit', 'gp' );
            $lang_featured          = __( 'Featured', 'gp' );
            $lang_featured_info     = __( 'Featured item is used to represent the project on horizontal and grid portfolios.', 'gp' );
            $lang_description       = __( 'Image Description', 'gp' );
            $lang_title_info        = __( 'Description is displayed on top of the image. You can use HTML to include links.', 'gp' );
            $lang_video_embed       = __( 'Video embed code', 'gp' );
            $lang_embed_info        = sprintf( __( 'Enter YouTube or Vimeo embed code. The code should look like this %s.', 'gp' ), '&lt;iframe ... &gt;&lt;/iframe&gt;' );
            $lang_screenshot        = __( 'Video screenshot', 'gp' );
            $lang_change_screenshot = __( 'Change Screenshot', 'gp' );
            $lang_screenshot_info   = __( 'If video is featured, the screenshot will be used on horizontal and grid portfolio pages.', 'gp' );
            $lang_save              = __( 'Save', 'gp' );
            $lang_cancel            = __( 'Cancel', 'gp' );
            $lang_remove            = __( 'Remove', 'gp' );
            $lang_not_published     = __( 'Not Published', 'gp' );
            $lang_published         = __( 'Published', 'gp' );
            $lang_published_info    = __( 'Not published items do not appear on project page. Note that if an item is featured, but not published, it will appear only in project list pages.', 'gp' );

            echo <<< JSTEMPLATE
            <script type="text/template" id="media-item-template">
                <input type="hidden" name="media_id" value="<%- id %>" />
                <input type="hidden" name="attachment_id" value="<%- attachment_id %>" />
                <input type="hidden" name="order" value="<%- order %>" />
                <input type="hidden" name="media_type" value="<%- type %>" />
                <input type="hidden" name="screenshot_id" value="<%- screenshot_id %>" />
                <div class="item-overview">
                    <% if (type == 'image') { %>
                        <div class="media-preview" style="background-image: url(<%- thumbnail_url %>);"></div>
                    <% } else { %>
                        <div class="media-preview" style="background-image: url(<%- screenshot_url %>);"></div>
                    <% } %>
                    <div class="details">
                        <div class="item-type">
                            <span>
                            <% if (type == 'image') { %>
                                {$lang_image}
                            <% } else { %>
                                {$lang_video}
                            <% } %>
                            </span>
                            <% if (featured == '1') { %>
                                <span class="featured">&mdash; {$lang_featured}</span>
                            <% } %>
                            <% if (published == '0') { %>
                                <span class="unpublished">&mdash; {$lang_not_published}</span>
                            <% } %>
                        </div>
                        <div class="item-info">
                            <div class="item-filename"><%= filename %></div>
                            <% if (type == 'image') { %>
                                <div class="item-description"><%- description %></div>
                            <% } else { %>
                                <div class="item-description"><%- embed %></div>
                            <% } %>
                        </div>
                        <div class="item-actions">
                            <a href="#" class="btn-edit">{$lang_edit}</a>
                        </div>
                        <div class="gp-drag"></div>
                    </div>
                </div>
                <div class="item-edit">
                    <% if (type == 'image') { %>
                        <div class="control-group">
                            <label>{$lang_description}</label>
                            <div class="control">
                                <textarea name="description" cols="30" rows="10"><%- description %></textarea>
                            </div>
                            <div class="control-info">
                                {$lang_title_info}
                            </div>
                        </div>
                    <% } else { %>
                        <div class="control-group">
                            <label>{$lang_video_embed}</label>
                            <div class="control">
                                <textarea name="embed" cols="30" rows="10"><%- embed %></textarea>
                            </div>
                            <div class="control-info">
                                {$lang_embed_info}
                            </div>
                        </div>
                        <div class="control-group">
                            <label>{$lang_screenshot}</label>
                            <div class="control">
                                <a href="#" class="button btn-add-screenshot">{$lang_change_screenshot}</a>
                            </div>
                            <div class="control-info">
                                {$lang_screenshot_info}
                            </div>
                        </div>
                    <% } %>
                    <div class="control-group">
                        <label>{$lang_featured}</label>
                        <div class="control">
                            <input type="checkbox" name="featured" value="1"<% if (featured == '1') { print(' checked="checked"'); } %> />
                        </div>
                        <div class="control-info">
                            {$lang_featured_info}
                        </div>
                    </div>
                    <div class="control-group">
                        <label>{$lang_published}</label>
                        <div class="control">
                            <input type="checkbox" name="published" value="1"<% if (published == '1') { print(' checked="checked"'); } %> />
                        </div>
                        <div class="control-info">
                            {$lang_published_info}
                        </div>
                    </div>
                    <div class="control-group">
                        <label>&nbsp;</label>
                        <a href="#" class="button button-primary btn-save">{$lang_save}</a>
                        <a href="#" class="button btn-cancel">{$lang_cancel}</a>
                        <a href="#" class="btn-delete">{$lang_remove}</a>
                    </div>
                </div>
            </script>
JSTEMPLATE;

        ?>

        </div>

        <?php

    }

    function meta_box_options_content() {

        $project = new PortfolioProject( $this->post_id );

        ?>
        <div class="gp-meta-field">
            <label for="gp_project_subtitle"><?php _e( 'Project Subtitle', 'gp' ); ?></label>
            <div class="field">
                <input type="text" name="gp_project_subtitle" value="<?php echo esc_attr( $project->meta_subtitle ); ?>" />
            </div>
        </div>
        <div class="gp-meta-field">
            <label for="gp_project_link"><?php _e( 'Project External Link', 'gp' ); ?></label>
            <div class="field">
                <input type="text" name="gp_project_link" value="<?php echo esc_attr( $project->meta_link ); ?>" class="url" />
            </div>
        </div>
        <div class="gp-meta-group">
            <h2><?php _e( 'Project information', 'gp' ); ?></h2>
            <table class="gp-table gp-project-information">
                <thead>
                    <tr>
                        <td><?php _e( 'Title', 'gp' ); ?></td>
                        <td><?php _e( 'Content', 'gp' ); ?></td>
                    </tr>
                </thead>
                <tbody><?php
                    if ( $project->meta_info && is_array( $project->meta_info ) ) :
                        foreach ( $project->meta_info as $info ) : ?>
                            <tr>
                                <td>
                                    <input type="text" name="gp_project_info_title[]" value="<?php echo esc_attr( $info['title'] ); ?>" />
                                </td>
                                <td>
                                    <textarea name="gp_project_info_content[]"><?php echo $info['content']; ?></textarea>
                                </td>
                            </tr><?php
                        endforeach;
                    endif; ?>
                    <tr class="add-element">
                        <td colspan="2">
                            <?php _e( 'To add project information enter the title and content fields below.', 'gp' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="text" name="gp_project_info_add_title" value="" /></td>
                        <td><textarea name="gp_project_info_add_content"></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a href="#" id="gp-add-project-info" class="button-secondary"><?php _e( 'Add project information', 'gp' ); ?></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="gp-meta-group">
            <h2><?php _e( 'Project navigation', 'gp' ); ?></h2>
            <div class="gp-meta-field">
                <label for="gp_project_other_projects"><?php _e( 'Other projects', 'gp' ); ?></label>
                <div class="field">
                    <?php

                    wp_dropdown_categories( array(
                        'show_option_all' => __( 'All Projects', 'gp' ),
                        'hide_empty'         => 0,
                        'selected'           => $project->meta_other_projects,
                        'hierarchical'       => 1,
                        'name'               => 'gp_project_other_projects',
                        'id'                 => 'gp_project_other_projects',
                        'taxonomy'           => 'gp-project-type'
                    ));

                    ?>
                </div>
            </div>
            <div class="gp-meta-field">
                <label for="gp_project_back_to_link"><?php _e( 'Back to link', 'gp' ); ?></label>
                <div class="field">
                    <?php

                    wp_dropdown_categories( array(
                        'show_option_all' => __( 'Back to Portfolio', 'gp' ),
                        'hide_empty'         => 0,
                        'selected'           => $project->meta_back_to_link,
                        'hierarchical'       => 1,
                        'name'               => 'gp_project_back_to_link',
                        'id'                 => 'gp_project_back_to_link',
                        'taxonomy'           => 'gp-project-type'
                    ));

                    ?>
                </div>
            </div>
        </div><?php

    }


    function meta_box_options_content_save( $post_id ) {

        if ( ! it_check_save_action( $post_id, 'gp_portfolio' ) ) {
            return $post_id;
        }

        $project = new PortfolioProject( $post_id );

        $project->update_from_array( $_POST );

        if ( it_key_is_array( $_POST, 'gp_project_info_title' ) ) {

            $titles = $_POST['gp_project_info_title'];
            $contents = $_POST['gp_project_info_content'];

            $data = array();

            foreach ( $titles as $index => $title ) {

                if ( !empty( $title ) && !empty( $contents[$index] ) ) {

                    $data[] = array(
                            'title' => $title,
                            'content' => $contents[$index]
                        );

                }

            }

            $project->meta_info = $data;

        } else {

            $project->meta_info = array();

        }

        $project->save();

    }

}

