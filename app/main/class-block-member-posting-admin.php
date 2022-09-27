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
                                    esc_html__( 'Block %s from posting new comments.',
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
    }

    new BP_Block_Member_Posting_Admin();
}
