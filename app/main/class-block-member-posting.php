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

            add_filter( 'bp_get_template_part', array( $this, 'block_activity_form' ), 10, 3 );

            add_filter( 'bp_nouveau_get_activity_entry_buttons', array( $this,
                'block_activity_comments' ), 10, 2 );
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

        /**
         * Block new activity form if the member is blocked.
         *
         * @param $templates
         * @param $slug
         * @param $name
         *
         * @return mixed
         */
        public function block_activity_form( $templates, $slug, $name ) {

            $user_id = get_current_user_id();

            if ( $slug == 'activity/post-form' &&
                 bp_is_member_posting_blocked( $user_id ) ) {
                $templates = '';
            }

            return $templates;
        }

        /**
         * Block new comment button if the member is blocked.
         *
         * @param $buttons
         * @param $activity_id
         *
         * @return boolean
         */
        public function block_activity_comments ( $buttons, $activity_id ) {
            $user_id = get_current_user_id();

            if (
                 bp_is_member_commenting_blocked( $user_id ) ) {
                echo "<pre>" . print_r( $buttons, 1 )."</pre>";
            }

            return $buttons;
        }


    }

    new BP_Block_Member_Posting();
}
