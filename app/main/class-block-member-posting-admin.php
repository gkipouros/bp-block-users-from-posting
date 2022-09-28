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

            add_action( 'edit_user_profile_update',
                array( $this, 'store_admin_block_member_option' ), 20, 1 );

            add_action( 'admin_menu',
                array( $this, 'admin_blocked_members_page_menu' ), 20, 1 );
        }

        /**
         * Get plugin admin area root page: settings.php for WPMS and tool.php for WP.
         *
         * @return string
         */
        private function get_root_admin_page() {

            return is_multisite() ? 'settings.php' : 'tools.php';
        }


        /**
         * Add admin option to block member
         */
        public function admin_block_member_option( $user ) {

            if ( ! function_exists( 'bp_get_member_type_object' ) ) {
                return;
            }

            /**
             * Get member's selection
             */
            $is_blocked_posting    = get_user_meta( $user->ID, 'bpbmp-block-member-posting', true );
            $is_blocked_commenting = get_user_meta( $user->ID, 'bpbmp-block-member-commenting', true );

            $checked_posting    = '';
            $checked_commenting = '';

            if ( $is_blocked_posting == 1 ) {
                $checked_posting = 'checked';
            }
            if ( $is_blocked_commenting == 1 ) {
                $checked_commenting = 'checked';
            }
            ?>
			<h3><?php esc_html_e( 'Block Member From Posting',
                    'bp-block-member-posting' ); ?></h3>

			<table class="form-table">
				<tr>
					<td>
						<fieldset>
							<input type="checkbox" name="bp-block-member-posting"
								   value="activities"
								   id="block-posting-for-this-member"
                                <?php echo $checked_posting; ?>
							>
							<label for="block-posting-for-this-member"><?php
                                printf(
                                    esc_html__( 'Block %s from posting new activities.',
                                        'bp-block-member-posting' ),
                                    esc_html__( $user->display_name )
                                ); ?></label>
							<br>
							<input type="checkbox" name="bp-block-member-commenting"
								   value="commenting"
								   id="block-commenting-for-this-member"
                                <?php echo $checked_commenting; ?>
							>
							<label for="block-commenting-for-this-member"><?php
                                printf(
                                    esc_html__( 'Block %s from posting new activity comments.',
                                        'bp-block-member-posting' ),
                                    esc_html__( $user->display_name )
                                ); ?></label>
						</fieldset>
					</td>
				</tr>
			</table>
            <?php


        }

        /**
         * Store the user's "Block Member From Posting" selection on the
         * admin User edit page.
         *
         * @param $user_id
         */
        function store_admin_block_member_option( $user_id ) {
            if ( ! isset( $_REQUEST['bp-block-member-posting'] ) ||
                 empty( $_REQUEST['bp-block-member-posting'] ) ) {
                delete_user_meta( $user_id, 'bpbmp-block-member-posting' );
            } else {
                update_user_meta(
                    $user_id,
                    'bpbmp-block-member-posting',
                    1
                );
            }

            if ( ! isset( $_REQUEST['bp-block-member-commenting'] ) ||
                 empty( $_REQUEST['bp-block-member-commenting'] ) ) {
                delete_user_meta( $user_id, 'bpbmp-block-member-commenting' );
            } else {
                update_user_meta(
                    $user_id,
                    'bpbmp-block-member-commenting',
                    1
                );
            }
        }

        /**
         * Add a submenu for the admin management page
         */
        public function admin_blocked_members_page_menu() {
            add_submenu_page(
                $this->get_root_admin_page(),
                __( 'Blocked Members Management for BuddyPress', 'bp-block-member-posting' ),
                __( 'BP Block Members', 'bp-block-member-posting' ),
                'manage_options',
                'bp-block-member-posting',
                array( $this, 'blocked_members_admin_page_callback' )
            );
        }

        /**
         * Callback function for the admin management page content
         */
        public function blocked_members_admin_page_callback() {



            $template = BPBMFP_PATH . 'templates/bp-block-members-admin-page.php';

            /**
             * Add filter to manage the displayed template
             */
            $template = apply_filters( 'bp_blocked_members_admin_page_template', $template );
            include_once( $template );
        }
    }

    new BP_Block_Member_Posting_Admin();
}
