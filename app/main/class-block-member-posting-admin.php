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

            // Enqueue Back end scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_style_scripts' ), 100 );



            add_action( 'bp_init', array( $this, 'save_block_member_type_selection' ), 1 );

            // Customize Admin User management page
            add_filter( 'manage_users_columns',
                array( $this, 'add_blocked_member_columns' ), 40 );

            add_filter( 'manage_users_custom_column',
                array( $this, 'add_blocked_member_columns_content' ), 10, 3 );

            add_filter( 'views_users', array( $this, 'add_blocked_member_filters' ), 30 );

            add_filter( 'pre_get_users', array( $this, 'filter_the_admin_user_results' ) );
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

        /**
         * Add Blocked Member columns to user management page
         *
         * @param $columns
         *
         * @return mixed
         */
        public function add_blocked_member_columns( $columns ) {
            $columns['block_posting']    = __( 'Posting', 'bp-block-member-posting' );
            $columns['block_commenting'] = __( 'Commenting', 'bp-block-member-posting' );

            return $columns;
        }

        /**
         * Add content to the blocked member columns
         *
         * @param $val
         * @param $column_name
         * @param $user_id
         *
         * @return mixed|string
         */
        public function add_blocked_member_columns_content( $val, $column_name, $user_id ) {
            switch ( $column_name ) {
                case 'block_posting' :
                    $blocked = "";
                    if ( bp_is_member_posting_blocked( $user_id ) ) {
                        $blocked = '<span class="dashicons dashicons-dismiss"></span>';
                    }

                    return $blocked;
                case 'block_commenting' :
                    $blocked = "";
                    if ( bp_is_member_commenting_blocked( $user_id ) ) {
                        $blocked = '<span class="dashicons 
								dashicons-welcome-comments"></span>';
                    }

                    return $blocked;
                default:
            }

            return $val;
        }

        /**
         * Add User admin filters
         *
         * @param $views
         *
         * @return mixed|string
         */
        public function add_blocked_member_filters( $views ) {

            $blocked_members = bp_get_blocked_members_list();

            $blocked_posting_count    = 0;
            $blocked_commenting_count = 0;

            if ( isset( $blocked_members['posting'] )
                 && is_array( $blocked_members['posting'] ) ) {
                $blocked_posting_count = count( $blocked_members['posting'] );
            }

            if ( isset( $blocked_members['commenting'] )
                 && is_array( $blocked_members['commenting'] ) ) {
                $blocked_commenting_count = count( $blocked_members['commenting'] );
            }

            $views['block_posting'] = "<a href='users.php?block-filter=posting'>" . __( 'Blocked Posting', 'bp-block-member-posting' ) . " <span class='count'>(" . $blocked_posting_count . ")</span></a>";

            $views['block_commenting'] = "<a href='users.php?block-filter=commenting'>" . __( 'Blocked Commenting', 'bp-block-member-posting' ) . " <span class='count'>(" . $blocked_commenting_count . ")</span></a>";

            return $views;
        }


        public function filter_the_admin_user_results( $query ) {
            if ( ! is_admin() || ! isset ( $_REQUEST['block-filter'] ) || ! is_main_query() ) {
                return $query;
            }

            $block_filter = sanitize_text_field( $_REQUEST['block-filter'] );
            $user_id_list = array();
            remove_filter( 'pre_get_users', array( $this, 'filter_the_admin_user_results' ) );
            $blocked_users = bp_get_blocked_members_list();
            add_filter( 'pre_get_users', array( $this, 'filter_the_admin_user_results' ) );


            if ( $block_filter == 'posting' ) {
                if ( isset( $blocked_users['posting'] ) &&
                     is_array(  $blocked_users['posting'] ) ) {
                    $user_id_list = $blocked_users['posting'];
                }

            }
			else if ( $block_filter == 'commenting' ) {
                if ( isset( $blocked_users['commenting'] ) &&
                     is_array(  $blocked_users['commenting'] ) ) {
                    $user_id_list = $blocked_users['commenting'];
                }

            }

			if ( empty( $user_id_list )) {
				$user_id_list = array( 0 );
            }

			$included = (array) $query->query_vars['include'];
			$included = $included + $user_id_list;

            $query->query_vars['include'] = $included;

            return $query;
        }
    }

    new BP_Block_Member_Posting_Admin();
}
