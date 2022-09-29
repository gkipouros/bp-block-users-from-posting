<?php
/**
 * Class for the custom table list.
 *
 * @package BP_Block_Member_Admin_Table
 */

/**
 * Exit if accessed directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Adding WP List table class if it's not available.
 */
if ( ! class_exists( \WP_List_Table::class ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class BP_Block_Member_Admin_Table.
 *
 */
class BP_Block_Member_Admin_Table extends \WP_List_Table {

    /**
     * Const to declare number of posts to show per page in the table.
     */
    const ROWS_PER_PAGE = 20;

    /**
     * BP_Block_Member_Admin_Table constructor.
     */
    public function __construct() {

        parent::__construct(
            array(
                'singular' => __( 'BP Blocked Member', 'bp-block-member-posting' ),
                'plural'   => __( 'BP Blocked Members', 'bp-block-member-posting' ),
                'ajax'     => false,
            )
        );

    }

    public static function get_where_filter() {
        global $wpdb;

        $where = '';


        return $where;
    }


    /**
     * Convert slug string to human readable.
     *
     * @param string $title String to transform human readable.
     *
     * @return string Human readable of the input string.
     */
    private function human_readable( $title ) {
        return ucwords( str_replace( '_', ' ', $title ) );
    }


    /**
     * Display text for when there are no items.
     */
    public function no_items() {
        esc_html_e( 'No members found.', 'bp-block-member-posting' );
    }

    /**
     * The Default columns
     *
     * @param array  $item The Item being displayed.
     * @param string $column_name The column we're currently in.
     *
     * @return string              The Content to display
     */
    public function column_default( $item, $column_name ) {
        $result = '';
        switch ( $column_name ) {
            case 'user_login':
                $result = "<span class='user_avatar'>" .
                          $item['avatar'] .
                          '</span>
							<span class="user-login-username">' .
                          $item['username'] .
                          '</span>';
                break;
            case 'member_name':
                $result = "<a href='" . get_admin_url() . "user-edit.php?user_id=" . $item['id'] . "'> 
                " . $item['name'] . "</a>";
                break;
            case 'member_email':
                $result = $item['email'];
                break;
            case 'commenting_blocked':
                $result = '';
                if ( absint( $item['commenting_blocked'] ) == 1 ) {
                    $result = '<span class="dashicons dashicons-welcome-comments"></span>';
                }
                break;
            case 'posting_blocked':
                $result = '';
                if ( absint( $item['posting_blocked'] ) == 1 ) {
                    $result = '<span class="dashicons dashicons-dismiss"></span>';
                }
                break;

        }

        return $result;
    }

    /**
     * Get list columns.
     *
     * @return array
     */
    public function get_columns() {
        return array(
            'user_login'         => __( 'Username', 'bp-block-member-posting' ),
            'member_name'        => __( 'Name', 'bp-block-member-posting' ),
            'member_email'       => __( 'Email', 'bp-block-member-posting' ),
            'posting_blocked'    => __( 'Posting', 'bp-block-member-posting' ),
            'commenting_blocked' => __( 'Commenting', 'bp-block-member-posting' ),

        );
    }


    /**
     * Get all earned blocked members
     *
     *
     * @return mixed
     */
    static function get_blocked_member_rows() {
        global $wpdb;

        // Bailout if no permission
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
            return array();
        }

        // The current pagination number
        $paged = 1;

        if ( ! empty( absint( $_REQUEST['paged'] ) ) ) {
            $paged = absint( $_REQUEST['paged'] );
        }

        // Offset number of users.
        $offset = ( $paged - 1 ) * self::ROWS_PER_PAGE;

        $args = array(
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'bpbmp-block-member-posting',
                    'value'   => '1',
                    'compare' => '='
                ),
                array(
                    'key'     => 'bpbmp-block-member-commenting',
                    'value'   => '1',
                    'compare' => '='
                )
            ),
            'number'     => self::ROWS_PER_PAGE,
            'offset'     => $offset,
        );

        /**
         * Get filters
         */
        $username = filter_input(
            INPUT_GET,
            'username',
            FILTER_SANITIZE_STRING );
        $name     = filter_input(
            INPUT_GET,
            'name',
            FILTER_SANITIZE_STRING );
        $email    = filter_input(
            INPUT_GET,
            'email',
            FILTER_SANITIZE_EMAIL );

        // Create filter query
        $where_fields = array();
        if ( ! empty( $username ) ) {
            $where_fields[] = ' user_login LIKE "%' . esc_attr( $username ) . '%" ';
        }

        if ( ! empty( $name ) ) {
            $where_fields[] = ' display_name LIKE "%' . esc_attr( $name ) . '%" ';
        }

        if ( ! empty( $email ) ) {
            $where_fields[] = ' user_email LIKE "%' . esc_attr( $email ) . '%" ';
        }
        $where = false;

        if ( count( $where_fields ) > 0 ) {
            $where = ' WHERE 1=1 AND ' . implode( ' AND ', $where_fields );

        }
        $filter_sql = 'SELECT ID from ' . $wpdb->users . ' ' . $where;
        $filtered_users = $wpdb->get_col( $filter_sql );


		// Add the filtered members to the User Query
		if ( $where  ) {

			// Do this to avoid listing all users for filters that return no results
			if ( empty( $filtered_users ) ) {
				$filtered_users = array( 0 );
			}

			// Add the filtered member ID to the main query
			$args['include'] = array_map(
                'intval',
                $filtered_users
            );
		}

        // Results
        $user_query = new WP_User_Query( $args );

        echo "<pre>" . print_r( $user_query->request, 1 ) . "</pre>";

        return apply_filters( 'bp-get-blocked-member-result', $user_query, $args );
    }

    /**
     * Prepare the data for the WP List Table
     *
     * @return void
     */
    public
    function prepare_items() {

        $columns               = $this->get_columns();
        $sortable              = $this->get_sortable_columns();
        $hidden                = array();
        $primary               = 'last_name';
        $this->_column_headers = array( $columns, $hidden, $sortable, $primary );
        $data                  = array();

        $this->process_bulk_action();

        $blocked_member_result = self::get_blocked_member_rows();
        $blocked_member_rows   = $blocked_member_result->get_results();
        $total_rows            = $blocked_member_result->total_users;


        if ( is_array( $blocked_member_rows ) && count( $blocked_member_rows ) > 0 ) {

            foreach ( $blocked_member_rows as $blocked_member_row ) {

                $user_id          = $blocked_member_row->ID;
                $posts_blocked    = absint( get_user_meta( $user_id, 'bpbmp-block-member-posting', true ) );
                $comments_blocked = absint( get_user_meta( $user_id, 'bpbmp-block-member-commenting', true ) );

                $avatar = bp_core_fetch_avatar(
                    array(
                        'item_id' => $user_id,
                        'type'    => 'thumb',
                        'alt'     => $blocked_member_row->user_nicename,
                        'width'   => '40',
                        'height'  => '40'
                    )
                );

                $data[] = array(
                    'id'                 => $user_id,
                    'name'               => $blocked_member_row->display_name,
                    'email'              => $blocked_member_row->user_email,
                    'username'           => $blocked_member_row->user_login,
                    'posting_blocked'    => $posts_blocked,
                    'commenting_blocked' => $comments_blocked,
                    'avatar'             => $avatar,

                );
            }
        }

        $this->items = $data;
        $page_args   = array(
            'total_items' => $total_rows,
            'per_page'    => self::ROWS_PER_PAGE,
            'total_pages' => ceil( $total_rows / self::ROWS_PER_PAGE )
        );

        $this->set_pagination_args( $page_args );
    }

    /**
     * Get bulk actions.
     *
     * @return array
     */
    public
    function get_bulk_actions() {
        return array();
    }


    /**
     * Generates the table navigation above or below the table
     *
     * @param string $which Position of the navigation, either top or bottom.
     *
     * @return void
     */
    protected
    function display_tablenav(
        $which
    ) {
        ?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

            <?php
            $this->id_filter( $which );
            $this->name_filter( $which );
            $this->email_filter( $which );


            //$this->extra_tablenav( $which );
            $this->add_filter_buttons( $which );
            $this->pagination( $which );
            ?>

			<br class="clear"/>
		</div>
        <?php
    }

    /**
     * Hide the search box.
     *
     * @param string $text The 'submit' button label.
     * @param string $input_id ID attribute value for the search input field.
     *
     * @since 3.1.0
     *
     */
    public
    function search_box(
        $text,
        $input_id
    ) {

    }

    /**
     * Add member name filter column type.
     *
     * @param string $which Position of the navigation, either top or bottom.
     *
     * @return void
     */
    protected
    function name_filter(
        $which
    ) {
        if ( 'top' === $which ) {
            $args = array(
                'label' => array(
                    'inner_text' => __( 'Filter by Name', 'bp-block-member-posting' ),
                ),
                'input' => array(
                    'name'        => 'name',
                    'id'          => 'name',
                    'value'       => filter_input(
                        INPUT_GET,
                        'name',
                        FILTER_SANITIZE_STRING ),
                    'placeholder' => __( 'Name', 'bp-block-member-posting' )
                ),
            );

            $this->html_input( $args );
        }
    }


    /**
     * Add Member ID filter column type.
     *
     * @param string $which Position of the navigation, either top or bottom.
     *
     * @return void
     */
    protected
    function id_filter(
        $which
    ) {
        if ( 'top' === $which ) {
            $args = array(
                'label' => array(
                    'inner_text' => __( 'Filter by Username', 'bp-block-member-posting' ),
                ),
                'input' => array(
                    'name'        => 'username',
                    'id'          => 'username',
                    'value'       => filter_input(
                        INPUT_GET,
                        'username',
                        FILTER_SANITIZE_STRING ),
                    'placeholder' => __( 'Username', 'bp-block-member-posting' )
                ),
            );

            $this->html_input( $args );
        }
    }

    /**
     * Add  Member Email filter column type.
     *
     * @param string $which Position of the navigation, either top or bottom.
     *
     * @return void
     */
    protected
    function email_filter(
        $which
    ) {
        if ( 'top' === $which ) {
            $args = array(
                'label' => array(
                    'inner_text' => __( 'Email Filter', 'bp-block-member-posting' ),
                ),
                'input' => array(
                    'name'        => 'email',
                    'id'          => 'email',
                    'value'       => filter_input(
                        INPUT_GET,
                        'email',
                        FILTER_SANITIZE_STRING ),
                    'placeholder' => __( 'Member Email', 'bp-block-member-posting' )
                ),
            );

            $this->html_input( $args );
        }
    }

    /**
     * Add filter submit button
     *
     * @param string $which Position of the navigation, either top or bottom.
     *
     * @return HTML
     */
    protected
    function add_filter_buttons(
        $which
    ) {

        if ( $which == 'top' ) {
            ?>
			<div class="alignleft actions">
                <?php
                submit_button( __( 'Filter', 'bp-block-member-posting' ), 'secondary', 'action', false );
                ?>
				<a href="<?php echo get_admin_url(); ?>tools.php?page=bp-block-member-posting" class="button"><?php
                    _e( 'Clear', 'bp-block-member-posting' )
                    ?></a>
			</div>
            <?php

        }
    }


    /**
     * Navigation input HTML generator
     *
     * @param array $args Argument array to generate dropdown.
     *
     * @return void
     */
    private
    function html_input(
        $args
    ) {
        $args['input']['type']  = 'text';
        $args['container']      = array( 'class' => 'alignleft actions' );
        $args['label']['class'] = 'screen-reader-text';
        ?>
		<div class="<?php echo( esc_attr( $args['container']['class'] ) ); ?>">
			<label
					for="<?php echo( esc_attr( $args['input']['id'] ) ); ?>"
					class="<?php echo( esc_attr( $args['label']['class'] ) ); ?>">
			</label>
			<input
					type="<?php echo( esc_attr( $args['input']['type'] ) ); ?>"
					name="<?php echo( esc_attr( $args['input']['name'] ) ); ?>"
					id="<?php echo( esc_attr( $args['input']['id'] ) ); ?>"
					value="<?php echo( esc_attr( $args['input']['value'] ) ); ?>"
					placeholder="<?php echo( esc_attr( $args['input']['placeholder'] ) ); ?>">
		</div>

        <?php
    }


}
