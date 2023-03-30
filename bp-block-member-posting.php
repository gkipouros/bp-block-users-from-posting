<?php
/**
 * Block Member from Posting for BuddyPress
 *
 * Block a member from making new posts for BuddyPress
 *
 * @link              https://gianniskipouros.com/block-member-posting-for-buddypress/
 * @since             1.0.0
 * @package           bp-block-member-posting
 *
 * @wordpress-plugin
 * Plugin Name:       Block Member Posting for BuddyPress
 * Plugin URI:        https://gianniskipouros.com/bp-block-member-posting/
 * Description:       Block a member from making new posts for BuddyPress
 * Version:           1.1.1
 * Author:            Giannis Kipouros
 * Author URI:        https://gianniskipouros.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-block-member-posting
 * Domain Path:       /languages
 */

/**
 * Main file, contains the plugin metadata and activation processes
 *
 * @package    bp-block-member-posting
 */
if ( ! defined( 'BPBMFP_VERSION' ) ) {
    /**
     * The version of the plugin.
     */
    define( 'BPBMFP_VERSION', '1.1.1' );
}

if ( ! defined( 'BPBMFP_PATH' ) ) {
    /**
     *  The server file system path to the plugin directory.
     */
    define( 'BPBMFP_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BPBMFP_URL' ) ) {
    /**
     * The url to the plugin directory.
     */
    define( 'BPBMFP_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BPBMFP_BASE_NAME' ) ) {
    /**
     * The url to the plugin directory.
     */
    define( 'BPBMFP_BASE_NAME', plugin_basename( __FILE__ ) );
}

/**
 * Include files.
 */
function bpbmfp_include_plugin_files() {

    // Bail out if BP is not enabled.
    if ( ! function_exists( 'bp_is_active' ) ) {
        return;
    }

    // Include Class files
    $files = array(
        'app/main/class-block-member-posting',
        'app/main/class-block-member-posting-admin',
        'app/main/class-block-member-posting-admin-buddypress',
        'app/main/class-block-member-posting-admin-buddyboss',
    );

    // Include Includes files
    $includes = array();

    // Merge the two arrays
    $files = array_merge( $files, $includes );

    foreach ( $files as $file ) {

        // Include functions file.
        require BPBMFP_PATH . $file . '.php';

    }

}

add_action( 'plugins_loaded', 'bpbmfp_include_plugin_files' );


/**
 * Load plugin's textdomain.
 */
function bpbmfp_language_textdomain_init() {
    // Localization
    load_plugin_textdomain( 'bp-block-member-posting', false, dirname( plugin_basename( __FILE__ ) ) . "/languages" );
}

// Add actions
add_action( 'init', 'bpbmfp_language_textdomain_init' );

/**
 * Check whether the member posting is blocked
 */
function bp_is_member_posting_blocked( $user_id ) {
    $user_id    = absint( $user_id );
    $is_blocked = false;

    // Check if user is blocked
    if ( $user_id > 0 ) {
        $is_blocked_meta = get_user_meta( $user_id, 'bpbmp-block-member-posting', true );
        if ( $is_blocked_meta == 1 ) {
            $is_blocked = true;
        } // If the member is not blocked check if the member's profile type is blocked
        else {
            $member_types = bp_get_member_type( $user_id, false );

            if ( is_array( $member_types ) && count( $member_types ) > 0 ) {
                // Loop through all the member's member types
                foreach ( $member_types as $member_type ) {

                    // Check BuddyPress' member type (Term)
                    if ( bp_is_member_type_posting_blocked( $member_type ) ) {
                        $is_blocked = true;
                        break;
                    }

                    // Check BuddyBoss' profile type (CPT)
                    if ( bp_is_profile_type_posting_blocked( $member_type ) ) {
                        $is_blocked = true;
                        break;
                    }
                }
            }
        }
    }

    return apply_filters( 'bp_is_member_posting_blocked', $is_blocked, $user_id );
}

/**
 * Check whether the activity commenting is blocked
 */
function bp_is_member_commenting_blocked( $user_id ) {
    $user_id    = absint( $user_id );
    $is_blocked = false;

    if ( $user_id > 0 ) {
        $is_blocked_meta = get_user_meta( $user_id, 'bpbmp-block-member-commenting', true );
        if ( $is_blocked_meta == 1 ) {
            $is_blocked = true;
        }// If the member is not blocked check if the member's profile type is blocked
        else {
            $member_types = bp_get_member_type( $user_id, false );

            if ( is_array( $member_types ) && count( $member_types ) > 0 ) {

                // Loop through all the member's member types
                foreach ( $member_types as $member_type ) {

                    // Check BuddyPress' member type (Term)
                    if ( bp_is_member_type_commenting_blocked( $member_type ) ) {
                        $is_blocked = true;
                        break;
                    }

                    // Check BuddyBoss' profile type (CPT)
                    if ( bp_is_profile_type_commenting_blocked( $member_type ) ) {
                        $is_blocked = true;
                        break;
                    }
                }
            }
        }
    }

    return apply_filters( 'bp_is_member_commenting_blocked', $is_blocked, $user_id );
}


/**
 * Check whether a member belonging to a specific member type
 * can post new activities
 *
 * @param string $member_type
 */
function bp_is_member_type_posting_blocked( $member_type ) {
    $is_blocked  = false;
    $member_type = sanitize_text_field( $member_type );

    $term = false;
    if ( ! empty( $member_type ) ) {
        $term = get_term_by( 'slug', $member_type, 'bp_member_type' );
    }

    if ( $term && isset( $term->term_id ) ) {
        $is_blocked_meta = get_term_meta( $term->term_id, 'bpbmp-block-posting', true );
        if ( $is_blocked_meta == 1 ) {
            $is_blocked = true;
        }
    }

    return apply_filters( 'bp_is_member_type_posting_blocked', $is_blocked, $member_type, $term );
}

/**
 * Check whether a member belonging to a specific member type
 * can post new comments
 *
 * @param string $member_type
 */
function bp_is_member_type_commenting_blocked( $member_type ) {
    $is_blocked  = false;
    $member_type = sanitize_text_field( $member_type );

    $term = false;
    if ( ! empty( $member_type ) ) {
        $term = get_term_by( 'slug', $member_type, 'bp_member_type' );
    }

    if ( $term && isset( $term->term_id ) ) {
        $is_blocked_meta = get_term_meta(
            $term->term_id,
            'bpbmp-block-commenting',
            true
        );
        if ( $is_blocked_meta == 1 ) {
            $is_blocked = true;
        }
    }

    return apply_filters(
        'bp_is_member_type_commenting_blocked',
        $is_blocked,
        $member_type,
        $term
    );
}

/**
 * Check whether a member belonging to a specific profile type (BB)
 * can post new comments
 *
 * @param string $member_type
 */
function bp_is_profile_type_commenting_blocked( $member_type ) {
    $is_blocked  = false;
    $member_type = sanitize_text_field( $member_type );

    if ( ! function_exists( 'bp_member_type_post_by_type' ) ) {
        return $is_blocked;
    }

    $post_id = absint( bp_member_type_post_by_type( $member_type ) );

    if ( ! empty( $post_id ) ) {
        $is_blocked_meta = get_post_meta(
            $post_id,
            'bpbmp-block-commenting',
            true
        );
        if ( $is_blocked_meta == 1 ) {
            $is_blocked = true;
        }
    }

    return apply_filters(
        'bp_is_profile_type_commenting_blocked',
        $is_blocked,
        $member_type,
        $post_id
    );
}

/**
 * Check whether a member belonging to a specific profile type (BB)
 * can post new activities
 *
 * @param string $member_type
 */
function bp_is_profile_type_posting_blocked( $member_type ) {
    $is_blocked  = false;
    $member_type = sanitize_text_field( $member_type );

    if ( ! function_exists( 'bp_member_type_post_by_type' ) ) {
        return $is_blocked;
    }

    $post_id = absint( bp_member_type_post_by_type( $member_type ) );

    if ( ! empty( $post_id ) ) {
        $is_blocked_meta = get_post_meta(
            $post_id,
            'bpbmp-block-posting',
            true
        );
        if ( $is_blocked_meta == 1 ) {
            $is_blocked = true;
        }
    }

    return apply_filters(
        'bp_is_profile_type_posting_blocked',
        $is_blocked,
        $member_type,
        $post_id
    );
}

/**
 * Get a list of blocked members for both posting and commenting
 *
 * @return mixed
 */
function bp_get_blocked_members_list() {
    $transient_key = 'bp_get_blocked_members_list';

    $blocked_users_posting = get_transient( $transient_key );

    if ( empty( $blocked_users_posting ) || ! is_array( $blocked_users_posting ) ) {


        $blocked_users_posting = array(
            'posting'    => array(),
            'commenting' => array(),
        );

        $args  = array(
            'fields' => 'ID',
            'order'  => 'ID'
        );
        $users = get_users( $args );

        if ( is_array( $users ) && count( $users ) > 0 ) {
            foreach ( $users as $user_id ) {
                if ( bp_is_member_posting_blocked( $user_id ) ) {
                    $blocked_users_posting['posting'][] = $user_id;
                }
                if ( bp_is_member_commenting_blocked( $user_id ) ) {
                    $blocked_users_posting['commenting'][] = $user_id;
                }
            }
        }

        // Set Transient for 10 seconds so it does not load again
        set_transient( $transient_key, $blocked_users_posting, 10 );
    }

    return apply_filters( 'bp_get_blocked_members_posting', $blocked_users_posting );
}

