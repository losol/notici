<?php

class Notici_Public_Api {

	function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_notices_route' ) );
	}

	function register_notices_route() {
		register_rest_route(
			'notices',
			'upcoming',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'upcoming_notices_json' ),
			)
		);
	}
	function upcoming_notices_json() {

		// WP_Query arguments
		$args = array(
			'post_type' => array( 'notici' ),
			'post_status' => array( 'publish' ),
			'nopaging'  => true,
			// 'order'       => 'ASC',
			// 'orderby'     => 'menu_order',
		);

		// The Query
		$post_list = new WP_Query( $args );

		$post_data = array();
		$i         = 0;
		// The Loop
		if ( $post_list->have_posts() ) {
			while ( $post_list->have_posts() ) {
				$post_list->the_post();
				$post_id     = get_the_id();
				$post_data[] = array(
					'id'          => $post_id,
					'name'        => get_the_title(),
					'description' => get_the_excerpt(),
					'startDate'   => get_post_meta( $post_id, 'notici_startdate', true ),
					'starttime'   => get_post_meta( $post_id, 'notici_starttime', true ),
					'endDate'     => get_post_meta( $post_id, 'notici_enddate', true ),
					'endtime'     => get_post_meta( $post_id, 'notici_endtime', true ),
				);
			}
		} else {
			// no posts found
		}

		wp_reset_postdata();
		return rest_ensure_response( $post_data );

	}
}
