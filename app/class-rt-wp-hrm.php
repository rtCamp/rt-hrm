<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RT_WP_HRM
 *
 * @author Dipesh
 */
if ( ! class_exists( 'RT_WP_HRM' ) ) {
    /**
     * Class RT_WP_HRM
     */
    class RT_WP_HRM {

        /**
         * @var
         */
        public $templateURL;

        /**
         * Call for dependency check and other hooks
         */
        public function     __construct() {

			$this->check_rt_biz_dependecy();

			$this->init_globals();

			add_action( 'init', array( $this, 'admin_init' ), 5 );
			add_action( 'init', array( $this, 'init' ), 6 );

			add_action( 'wp_enqueue_scripts', array( $this, 'loadScripts' ) );
		}

        /**
         * Call for admin initialization
         */
        function admin_init() {
			$this->templateURL = apply_filters('rthrm_template_url', 'rthrm/');

			$this->update_database();

			global $rt_hrm_admin;
			$rt_hrm_admin = new Rt_HRM_Admin();

		}

        /**
         * Check rt-biz plugin dependency
         */
        function check_rt_biz_dependecy() {
			if ( ! class_exists( 'Rt_Biz' ) ) {
				add_action( 'admin_notices', array( $this, 'rt_biz_admin_notice' ) );
			}
		}

        /**
         * Display notice for rt-biz plugin if not found
         */
        function rt_biz_admin_notice() { ?>
			<div class="updated">
				<p><?php _e( sprintf( 'WordPress HRM : It seems that WordPress rt-biz plugin is not installed or activated. Please %s / %s it.', '<a href="'.admin_url( 'plugin-install.php?tab=search&s=rt-biz' ).'">'.__( 'install' ).'</a>', '<a href="'.admin_url( 'plugins.php' ).'">'.__( 'activate' ).'</a>' ) ); ?></p>
			</div>
		<?php }

        /**
         * initialization of all classes
         */
        function init_globals() {
			global $rt_hrm_module, $rt_hrm_dashboard, $rt_hrm_calendar,
                   $rt_calendar, $rt_hrm_attributes, $rt_form, $rt_hrm_acl;

			$rt_form = new Rt_Form();

			$rt_hrm_module = new Rt_HRM_Module();
            $rt_hrm_acl = new Rt_HRM_ACL();
			$rt_hrm_dashboard = new Rt_HRM_Dashboard();
			$rt_hrm_calendar = new Rt_HRM_Calendar();
			$rt_calendar = new RT_Calendar();

			$rt_hrm_attributes = new Rt_HRM_Attributes();

		}

        /**
         *
         */
        function init() {
		}

        /**
         *
         */
        function update_database() {
			$updateDB = new RT_DB_Update( trailingslashit( RT_HRM_PATH ) . 'index.php', trailingslashit( RT_HRM_PATH ) . 'schema' );
			$updateDB->do_upgrade();
		}

        /**
         * Load script & style fot rt-hrm plugin
         */
        function loadScripts() {
			global $wp_query, $rt_hrm_module;

			if ( !isset($wp_query->query_vars['name']) ) {
				return;
			}

			$name = $wp_query->query_vars['name'];

			$post_type = rthrm_post_type_name( $name );
			if( $post_type != $rt_hrm_module->post_type ) {
				return;
			}

			if( !isset( $_REQUEST['rthrm_unique_id'] ) || (isset($_REQUEST['rthrm_unique_id']) && empty($_REQUEST['rthrm_unique_id'])) ) {
				return;
			}

			$args = array(
				'meta_key' => 'rthrm_unique_id',
				'meta_value' => $_REQUEST['rthrm_unique_id'],
				'post_status' => 'any',
				'post_type' => $post_type,
			);

			$leadpost = get_posts( $args );
			if( empty( $leadpost ) ) {
				return;
			}
			$lead = $leadpost[0];
			if( $post_type != $lead->post_type ) {
				return;
			}


			$this->localize_scripts();
		}

        /**
         * Load localize script for rt-hrm plugin
         */
        function localize_scripts() {

			$unique_id = $_REQUEST['rthrm_unique_id'];
			$args = array(
				'meta_key' => 'rthrm_unique_id',
				'meta_value' => $unique_id,
				'post_status' => 'any',
				'post_type' => rthrm_get_all_post_types(),
			);
			$leadpost = get_posts( $args );
			if( empty( $leadpost ) ) {
				return;
			}
			$lead = $leadpost[0];

			$user_edit = false;

		}
	}
}
