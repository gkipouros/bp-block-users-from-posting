<?php
/**
 * This template is used in class-admin to load the admin page content.
 *
 */
$blocked_members_table = '';
if ( function_exists( 'bp_core_fetch_avatar' ) ) {
    $blocked_members_table = new BP_Block_Member_Admin_Table();
}

?>
	<div class="wrap">
		<h1><?php _e( 'BuddyPress Blocked Member List', 'bp-block-member-posting' ); ?></h1>
        <?php if ( ! empty( $blocked_members_table ) ) { ?>
			<form id="bp-blocked-members-table" method="get"  >
				<input type="hidden" name="page" value="bp-block-member-posting"/>
                <?php
                $blocked_members_table->prepare_items();
                $blocked_members_table->search_box( __( 'Search', 'bp-block-member-posting' ), 'search' );
                $blocked_members_table->display();
                ?>
			</form>
            <?php
        } else {
            __( 'Unfortunately, BuddyPress is disabled!', 'bp-block-member-posting' );

        }
        ?>
	</div>
<?php
