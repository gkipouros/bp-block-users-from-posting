<?php
/**
 * Class for custom work.
 *
 * @package BP_Block_Member_Posting_Admin
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'BP_Block_Member_Posting_Admin' ) ) {

    /**
     * Class for the plugin's admin functions.
     */
    class BP_Block_Member_Posting_Admin {


        /**
         * Constructor for class.
         */
        public function __construct() {


            $this->load_hooks();
        }

        private function load_hooks() {

            add_action( 'edit_user_profile',
                array( $this, 'admin_block_member_option' ) );
            add_action( 'show_user_profile',
                array( $this, 'admin_block_member_option' ) );

            add_action( 'save_post',
                array( $this, 'store_hide_notice_for_member_types_selection' ), 20, 1 );
        }


        /**
         * Add admin option to block member
         */
        public function admin_block_member_option( $user ) {

            if ( ! function_exists( 'bp_get_member_type_object' ) ) {
                return;
            }
			echo "<pre>" . print_r( $user, 1 )."</pre>";
            ?>
			<h3><?php esc_html_e( 'Block Member From Posting',
					'bp-block-member-posting' ); ?></h3>

			<table class="form-table">
				<tr>
					<th><label for="block-posting-for-this-member"><?php esc_html_e( 'Block ', 'bp-block-member-posting' ); ?></label></th>
					<td><?php echo esc_html( get_the_author_meta( 'year_of_birth', $user->ID ) ); ?></td>
				</tr>
			</table>
            <?php


        }

        /**
         * Store the user's "Hide for profile types" selection on the
         * admin Feed Notices single edit page.
         *
         * @param $post_id
         */
        function store_hide_notice_for_member_types_selection( $post_id ) {

            if ( ! isset( $_REQUEST['notices-member-types'] ) ||
                 empty( $_REQUEST['notices-member-types'] ) ) {
                delete_post_meta( $post_id, 'notice-blocked-member-types' );
            } else {
                // Sanitize inputs
                $hide_for_member_types = array_map(
                    'sanitize_text_field',
                    $_REQUEST['notices-member-types']
                );
                update_post_meta(
                    $post_id,
                    'notice-blocked-member-types',
                    $hide_for_member_types
                );
            }
        }
    }

    new BP_Block_Member_Posting_Admin();
}
