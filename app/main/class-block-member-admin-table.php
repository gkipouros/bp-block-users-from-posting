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
    const ROWS_PER_PAGE = 50;

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

            case 'avatar':
                $result = $item['avatar'];
                break;
            case 'member_id':
                $result = $item['id'];
                break;
            case 'member_name':
                $result = "<a href='" . get_admin_url() . "user-edit.php?user_id=" . $item['id'] . "'> 
                " . $item['name'] . "</a>";
                break;

            case 'member_email':
                $result = $item['email'];
                break;
            case 'commenting_blocked':
                $result = $item['commenting_blocked'];
                break;
            case 'posting_blocked':
                $result = $item['posting_blocked'];
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
            'avatar'             => "",
            'member_id'          => __( 'Member ID', 'bp-block-member-posting' ),
            'member_name'        => __( 'Name', 'bp-block-member-posting' ),
            'member_email'       => __( 'Email', 'bp-block-member-posting' ),
            'posting_blocked'    => __( 'Posting Blocked', 'bp-block-member-posting' ),
            'commenting_blocked' => __( 'Commenting Blocked', 'bp-block-member-posting' ),

        );


    }


    /**
     * Get all earned blocked members
     *
     *
     * @return mixed
     */
    static function get_blocked_member_rows() {

        // Bailout if no permission
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
            return array();
        }

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
            )
        );

        // Results
        $user_query = new WP_User_Query( $args );

        return apply_filters( 'bp-get-blocked-member-result', $user_query, $args );
    }

    /**
     * Prepare the data for the WP List Table
     *
     * @return void
     */
    public function prepare_items() {

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
    public function get_bulk_actions() {
        return array();
    }


    /**
     * Generates the table navigation above or below the table
     *
     * @param string $which Position of the navigation, either top or bottom.
     *
     * @return void
     */
    protected function display_tablenav( $which ) {
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
    public function search_box( $text, $input_id ) {

    }

    /**
     * Add member name filter column type.
     *
     * @param string $which Position of the navigation, either top or bottom.
     *
     * @return void
     */
    protected function name_filter( $which ) {
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
    protected function id_filter( $which ) {
        if ( 'top' === $which ) {
            $args = array(
                'label' => array(
                    'inner_text' => __( 'Filter by Member ID', 'bp-block-member-posting' ),
                ),
                'input' => array(
                    'name'        => 'member_id',
                    'id'          => 'member_id',
                    'value'       => filter_input(
                        INPUT_GET,
                        'member_id',
                        FILTER_SANITIZE_NUMBER_INT ),
                    'placeholder' => __( 'Member ID', 'bp-block-member-posting' )
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
    protected function email_filter( $which ) {
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
    protected function add_filter_buttons( $which ) {

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
    private function html_input( $args ) {
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
