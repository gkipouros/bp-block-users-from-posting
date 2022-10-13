<?php
/**
 * Class for custom work.
 *
 * @package BP_Block_Member_Posting_Admin_BuddyPress
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'BP_Block_Member_Posting_Admin_BuddyPress' ) ) {

    /**
     * Class for the plugin's admin functions.
     */
    class BP_Block_Member_Posting_Admin_BuddyPress {


        /**
         * Constructor for class.
         */
        public function __construct() {
            $this->load_hooks();
        }

        private function load_hooks() {

            add_action( 'bp_init', array( $this, 'save_block_member_type_selection' ), 1 );
        }


        /**
         * Add custom fields for blocking posting on member type edit page.
         *
         * @param $term
         * @param $taxonomy
         */
        public function add_buddypress_member_type_fields( $term, $taxonomy ) {

            // Bail out if not in the add/edit member type page.
            if ( ! isset( $term->slug ) || empty( $term->slug ) || ! function_exists( 'bp_get_member_type_object' ) ) {
                return;
            }

            /**
             * Get member types's selection
             */
            $is_blocked_posting    = get_term_meta( $term->term_id, 'bpbmp-block-posting', true );
            $is_blocked_commenting = get_term_meta( $term->term_id, 'bpbmp-block-commenting', true );

            $checked_posting    = '';
            $checked_commenting = '';

            if ( $is_blocked_posting == 1 ) {
                $checked_posting = 'checked';
            }
            if ( $is_blocked_commenting == 1 ) {
                $checked_commenting = 'checked';
            }
            ?>
			<table class="form-table block-member-type" role="presentation">
				<tr>
					<th scope="row"><?php
                        esc_html_e( 'Block Member Posting',
                            'bp-block-member-posting' )
                        ?></th>
					<td>
						<fieldset>
							<input type="checkbox" name="bp-block-member-type-posting"
								   value="1"
								   id="block-posting-for-this-member-type"
                                <?php echo $checked_posting; ?>
							>
							<label for="block-posting-for-this-member-type"><?php
                                printf(
                                    esc_html__( 'Block "%s" from making new posts.',
                                        'bp-block-member-posting' ),
                                    esc_html__( $term->name )
                                ); ?></label>
							<br>
							<input type="checkbox" name="bp-block-member-type-commenting"
								   value="1"
								   id="block-commenting-for-this-member-type"
                                <?php echo $checked_commenting; ?>
							>
							<label for="block-commenting-for-this-member-type"><?php
                                printf(
                                    esc_html__( 'Block "%s" from commenting on activities.', 'bp-block-member-posting' ),
                                    esc_html__( $term->name )
                                ); ?></label>
						</fieldset>
					</td>
				</tr>
			</table>
            <?php
        }

        /**
         * Store the user's "Block Member Posting" selection on the
         * admin Member Type edit page.
         *
         */
        public function save_block_member_type_selection() {
            if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== 'editedtag' ) {
                return;
            }

			// Get current term ID
            $term_id = ( isset( $_REQUEST['tag_ID'] ) ? absint( $_REQUEST['tag_ID'] ) : 0 );

            if ( $term_id == 0 ) {
                return;
            }

            check_admin_referer( 'update-tag_' . $term_id );

            // Set term commenting option
            if ( ! isset( $_REQUEST['bp-block-member-type-commenting'] ) ||
                 $_REQUEST['bp-block-member-type-commenting'] != 1 ) {
                delete_term_meta( $term_id, 'bpbmp-block-commenting' );
            } else {
                update_term_meta(
                    $term_id,
                    'bpbmp-block-commenting',
                    1
                );
            }

            // Set term new post option
            if ( ! isset( $_REQUEST['bp-block-member-type-posting'] ) ||
                 $_REQUEST['bp-block-member-type-posting'] != 1 ) {
                delete_term_meta( $term_id, 'bpbmp-block-posting' );
            } else {
                update_term_meta(
                    $term_id,
                    'bpbmp-block-posting',
                    1
                );
            }
        }
    }

    new BP_Block_Member_Posting_Admin_BuddyPress();
}
