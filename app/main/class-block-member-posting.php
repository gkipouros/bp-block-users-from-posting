<?php
/**
 * Class for custom work.
 *
 * @package BP_Block_Member_Posting
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'BP_Block_Member_Posting' ) ) {

    /**
     * Class for the plugin's core.
     */
    class BP_Block_Member_Posting {

        const READ_META_KEY = 'read_feed_notices';

        /**
         * Constructor for class.
         */
        public function __construct() {


            $this->load_hooks();
        }

        private function load_hooks() {
            // Enqueue front-end scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style_scripts' ), 100 );


        }


        /**
         * Enqueue style/script.
         *
         * @return void
         */
        public function enqueue_style_scripts() {

            // Custom plugin script.
            wp_enqueue_style(
                'bp-block-member-posting-core-style',
                BPBMFP_URL . 'assets/css/bp-block-member-posting.css',
                '',
                BPBMFP_VERSION
            );

            // Register plugin's JS script
            wp_register_script(
                'bp-block-member-posting-custom-script',
                BPBMFP_URL . 'assets/js/bp-block-member-posting.js',
                array(
                    'jquery',
                ),
                BPBMFP_VERSION,
                true
            );


            // Provide a global object to our JS file containing the AJAX url and security nonce
            wp_localize_script( 'bp-block-member-posting-custom-script', 'BPBMPAjaxObject',
                array(
                    'ajax_url'   => admin_url( 'admin-ajax.php' ),
                    'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ),
                )
            );
            wp_enqueue_script( 'bp-block-member-posting-custom-script' );

        }


    }

    new BP_Block_Member_Posting();
}
