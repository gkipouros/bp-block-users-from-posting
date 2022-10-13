<?php
/**
 * Class for BuddyBoss custom functions.
 *
 * @package BP_Block_Member_Posting_Admin_BuddyBoss
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'BP_Block_Member_Posting_Admin_BuddyBoss' ) ) {

    /**
     * Class for the plugin's admin functions.
     */
    class BP_Block_Member_Posting_Admin_BuddyBoss {

        /**
         * Constructor for class.
         */
        public function __construct() {
            $this->load_hooks();
        }

        private function load_hooks() {

            add_action( 'edit_form_advanced', array( $this, 'add_buddyboss_profile_type_fields' ), 40, 2 );

            add_action( 'save_post', array( $this, 'save_block_profile_type_selection' ), 1 );

        }


        /**
         * Add custom fields for blocking posting on profile type edit page.
         *
         * @param $term
         * @param $taxonomy
         */
        public function add_buddyboss_profile_type_fields( $post ) {

            // Bail out if not in the add/edit profile type page.
            if ( ! isset( $post->post_type ) || $post->post_type != 'bp-member-type' ) {
                return;
            }

            $post_id = absint( $post->ID );

            if ( $post_id <= 0 ) {
                return;
            }

            /**
             * Get profile type's selection
             */
            $is_blocked_posting    = get_post_meta( $post_id,
                'bpbmp-block-posting', true );
            $is_blocked_commenting = get_post_meta( $post_id,
                'bpbmp-block-commenting', true );

            $checked_posting    = '';
            $checked_commenting = '';

            if ( $is_blocked_posting == 1 ) {
                $checked_posting = 'checked';
            }
            if ( $is_blocked_commenting == 1 ) {
                $checked_commenting = 'checked';
            }
            ?>
			<div id="bp-member-type-label-box" class="postbox ">
				<div class="postbox-header">
					<h2><?php
                        esc_html_e( 'Block Member Posting',
                            'bp-block-member-posting' )
                        ?></h2>
				</div>
				<table class="form-table block-member-type" role="presentation">
					<tr>
						<td>
							<fieldset>
								<input type="checkbox" name="bp-block-member-type-posting"
									   value="1"
									   id="block-posting-for-this-member-type"
                                    <?php echo $checked_posting; ?>
								>
								<label for="block-posting-for-this-member-type"><?php
                                    $profile_type = esc_html__( $post->post_title );
                                    if ( empty( $profile_type ) ) {
                                        $profile_type = __( 'this profile type',
                                            'bp-block-member-posting' );
                                    }
                                    printf(
                                        esc_html__( 'Block members of %s from making new posts.',
                                            'bp-block-member-posting' ),
                                        $profile_type
                                    ); ?></label>
								<br>
								<input type="checkbox" name="bp-block-member-type-commenting"
									   value="1"
									   id="block-commenting-for-this-member-type"
                                    <?php echo $checked_commenting; ?>
								>
								<label for="block-commenting-for-this-member-type"><?php

                                    printf(
                                        esc_html__( 'Block members of %s from commenting on activities.', 'bp-block-member-posting' ),
                                        $profile_type
                                    ); ?></label>
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
            <?php
        }

        /**
         * Store the user's "Block Member Posting" selection on the
         * admin Member Type edit page.
         *
         */
        public function save_block_profile_type_selection( $post_id ) {

            $post_id = absint( $post_id );

            if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== 'editpost' ||
                 ! isset( $_REQUEST['post_type'] ) || $_REQUEST['post_type'] !== 'bp-member-type' ) {
                return;
            }


            if ( $post_id == 0 ) {
                return;
            }

            // Set term commenting option
            if ( ! isset( $_REQUEST['bp-block-member-type-commenting'] ) ||
                 $_REQUEST['bp-block-member-type-commenting'] != 1 ) {
                delete_post_meta( $post_id, 'bpbmp-block-commenting' );
            } else {
                update_post_meta(
                    $post_id,
                    'bpbmp-block-commenting',
                    1
                );
            }

            // Set term new post option
            if ( ! isset( $_REQUEST['bp-block-member-type-posting'] ) ||
                 $_REQUEST['bp-block-member-type-posting'] != 1 ) {
                delete_post_meta( $post_id, 'bpbmp-block-posting' );
            } else {
                update_post_meta(
                    $post_id,
                    'bpbmp-block-posting',
                    1
                );
            }
        }
    }

    new BP_Block_Member_Posting_Admin_BuddyBoss();
}
