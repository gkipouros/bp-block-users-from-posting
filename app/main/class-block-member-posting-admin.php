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
            // Enqueue Back end scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_style_scripts' ), 100 );



            add_action( 'bp_member_type_edit_form_fields', array( $this, 'add_buddypress_member_type_fields' ), 40, 2 );

            add_action( 'bp_init', array( $this, 'save_block_member_type_selection' ), 1 );


        }

        /**
         * Enqueue Admin style/script.
         *
         * @return void
         */
        public function admin_enqueue_style_scripts() {

            // Custom plugin script.
            wp_enqueue_style(
                'bp-block-member-posting-admin-style',
                BPBMFP_URL . 'assets/css/bp-block-member-posting-admin.css',
                '',
                BPBMFP_VERSION
            );


            wp_enqueue_script( 'ceu_user_reports-admin-custom-script' );
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
         * Add admin option to block member to edit user page
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
                                    esc_html__( 'Block %s from making new posts.',
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
                                    esc_html__( 'Block %s from commenting on activities.',
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

    new BP_Block_Member_Posting_Admin();
}
