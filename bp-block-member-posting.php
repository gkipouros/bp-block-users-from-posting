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
 * Version:           1.0.0
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
	define( 'BPBMFP_VERSION', '1.0.0' );
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
function bppfn_include_plugin_files() {

    // Bail out if BP is not enabled.
    if ( ! function_exists('bp_is_active') ) {
        return;
    }

	// Include Class files
	$files = array(
		'app/main/class-block-member-posting',
        'app/main/class-block-member-posting-admin',
	);

	// Include Includes files
	$includes = array(

	);

	// Merge the two arrays
	$files = array_merge( $files, $includes );

	foreach ( $files as $file ) {

		// Include functions file.
		require BPBMFP_PATH . $file . '.php';

	}

}

add_action( 'plugins_loaded', 'bppfn_include_plugin_files' );


/**
 * Load plugin's textdomain.
 */
function bppfn_language_textdomain_init() {
    // Localization
    load_plugin_textdomain( 'bp-block-member-posting', false, dirname( plugin_basename( __FILE__ ) ) . "/languages" );
}

// Add actions
add_action( 'init', 'bppfn_language_textdomain_init' );
