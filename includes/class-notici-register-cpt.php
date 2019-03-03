<?php


class Notici_Register_Cpt {
	private $plugin_name;
	private $version;

	function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Register the custom post type
		add_action( 'init', array( $this, 'notici_register_cpt' ) );

		// Add notici categories
		add_action( 'init', array( $this, 'notici_register_category_taxonomy' ), 0 );

		// Save post
		add_action( 'save_post', array( $this, 'save_notici' ) );
		add_filter( 'post_updated_messages', array( $this, 'notici_updated_messages' ) );
	}

	function notici_register_cpt() {

		$labels = array(
			'name'               => _x( 'Notices', 'post type general name' ),
			'singular_name'      => _x( 'Notice', 'post type singular name' ),
			'add_new'            => _x( 'Add New', 'notices' ),
			'add_new_item'       => __( 'Add New Notice' ),
			'edit_item'          => __( 'Edit Notice' ),
			'new_item'           => __( 'New Notice' ),
			'view_item'          => __( 'View Notice' ),
			'search_items'       => __( 'Search Notices' ),
			'not_found'          => __( 'No notices found' ),
			'not_found_in_trash' => __( 'No notices found in Trash' ),
			'parent_item_colon'  => '',
		);

		$args = array(
			'label'             => __( 'Notices' ),
			'labels'            => $labels,
			'public'            => true,
			'show_in_rest'      => true,
			'has_archive'       => true,
			'can_export'        => true,
			'show_ui'           => true,
			'_builtin'          => false,
			'capability_type'   => 'post',
			'menu_icon'         => 'dashicons-exerpt-view',
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => get_option( 'notici_slug' ) ),
			'supports'          => array( 'title', 'thumbnail', 'excerpt', 'editor' ),
			'show_in_nav_menus' => true,
			'taxonomies'        => array( 'noticicategory', 'post_tag' ),
		);

		register_post_type( 'notici', $args );
		flush_rewrite_rules();

	}

	// Register Custom Taxonomy
	function notici_register_category_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Notice categories', 'Taxonomy General Name', 'notici' ),
			'singular_name'              => _x( 'Notice category', 'Taxonomy Singular Name', 'notici' ),
			'menu_name'                  => __( 'Taxonomy', 'notici' ),
			'all_items'                  => __( 'All Items', 'notici' ),
			'parent_item'                => __( 'Parent Item', 'notici' ),
			'parent_item_colon'          => __( 'Parent Item:', 'notici' ),
			'new_item_name'              => __( 'New Item Name', 'notici' ),
			'add_new_item'               => __( 'Add New Item', 'notici' ),
			'edit_item'                  => __( 'Edit Item', 'notici' ),
			'update_item'                => __( 'Update Item', 'notici' ),
			'view_item'                  => __( 'View Item', 'notici' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'notici' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'notici' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'notici' ),
			'popular_items'              => __( 'Popular Items', 'notici' ),
			'search_items'               => __( 'Search Items', 'notici' ),
			'not_found'                  => __( 'Not Found', 'notici' ),
			'no_terms'                   => __( 'No items', 'notici' ),
			'items_list'                 => __( 'Items list', 'notici' ),
			'items_list_navigation'      => __( 'Items list navigation', 'notici' ),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'rewrite'           => array( 'slug' => 'notice-category' ),
		);
		register_taxonomy( 'noticicategory', array( 'notici' ), $args );

	}

	function save_notici() {

		global $post;

		// Require nonce
		if ( ! wp_verify_nonce( $_POST['notici-nonce'], 'notici-nonce' ) ) {
			return $post->ID;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $post->ID;
		}

	}

	function notici_updated_messages( $messages ) {

		global $post, $post_ID;

		$messages['notici'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Notice updated. <a href="%s">View item</a>' ), esc_url( get_permalink( $post_ID ) ) ),
			2  => __( 'Custom field updated.' ),
			3  => __( 'Custom field deleted.' ),
			4  => __( 'Notice updated.' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Notice restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Notice published. <a href="%s">View event</a>' ), esc_url( get_permalink( $post_ID ) ) ),
			7  => __( 'Notice saved.' ),
			8  => sprintf( __( 'Notice submitted. <a target="_blank" href="%s">Preview event</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9  => sprintf(
				__( 'Notice scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),
			10 => sprintf( __( 'Notice draft updated. <a target="_blank" href="%s">Preview event</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}

}

