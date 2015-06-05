<?php
/**
 * Created by PhpStorm.
 * User: paresh
 * Date: 5/6/15
 * Time: 6:36 PM
 */

/**
 * @aurhor paresh
 */
class Rt_HRM_Leave {

	/**
	 * Placeholder method
	 *
	 */
	private function __construct() {
		$this->setup();
	}

	/**
	 * Setup actions and filters
	 *
	 */
	private function setup() {

	}

	/**
	 * Return singleton instance of class
	 */
	public static function factory() {
		static $instance = false;
		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Check user is on leave
	 *
	 * @param $wp_user_id
	 * @param $leave_date
	 *
	 * @return array
	 */
	public function rthrm_check_user_on_leave( $wp_user_id, $leave_date ) {

		$leave_posts = array();

		$post_ids = $this->rthrm_get_users_approved_leaves_post_ids( $wp_user_id );

		foreach ( $post_ids as $post_id ) {

			$leave_duration = get_post_meta( $post_id, 'leave-duration', true );

			$start_date_meta = get_post_meta( $post_id, 'leave-start-date', true );


			if ( empty( $start_date_meta ) ) {
				continue;
			}


			$start_date_obj = date_create_from_format( 'd/m/Y', $start_date_meta );
			$leave_date_obj = date_create_from_format( 'Y-m-d', $leave_date );

			if ( in_array( $leave_duration, array( 'full-day', 'half-day' ) ) ) {

				if ( $start_date_obj == $leave_date_obj ) {
					$leave_posts[] = $post_id;
				}
			} else if ( 'other' === $leave_duration ) {

				$end_date_meta = get_post_meta( $post_id, 'leave-end-date', true );
				$end_date_obj  = date_create_from_format( 'd/m/Y', $end_date_meta );

				if ( $start_date_obj <= $leave_date_obj &&
				     $end_date_obj >= $leave_date_obj
				) {
					$leave_posts[] = $post_id;
				}
			}
		}

		return $leave_posts;
	}

	/**
	 * Get all approved leaves for user
	 * @param $wp_user_id
	 *
	 * @return mixed
	 */
	public function rthrm_get_users_approved_leaves_post_ids( $wp_user_id ) {
		global $wpdb;

		$employee_post = rt_biz_get_person_for_wp_user( $wp_user_id );

		$query = "SELECT postmeta.post_id FROM {$wpdb->postmeta} postmeta INNER JOIN {$wpdb->posts} posts ON" .
		         " posts.ID = postmeta.post_id WHERE posts.post_status = 'approved' AND" .
		         " postmeta.meta_key = 'leave-user-id' AND postmeta.meta_value = '{$employee_post[0]->ID}'";

		$result = $wpdb->get_col( $query );

		return $result;
	}
}