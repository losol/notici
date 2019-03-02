<?php


class Notici_Register_Cpt {
	private $plugin_name;
	private $version;

	function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Register the custom post type
		add_action( 'init', array( $this, 'notici_register_cpt' ) );

		// Add event categories
		add_action( 'init', array( $this, 'notici_category_taxonomy' ), 0 );

		// Styles and scripts
		$this->notici_styles_and_scripts();

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
			'taxonomies'        => array( 'notici_category', 'post_tag' ),
		);

		register_post_type( 'notici', $args );
		flush_rewrite_rules();

	}

	function notici_category_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Categories', 'taxonomy general name' ),
			'singular_name'              => _x( 'Category', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Categories' ),
			'popular_items'              => __( 'Popular Categories' ),
			'all_items'                  => __( 'All Categories' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Category' ),
			'update_item'                => __( 'Update Category' ),
			'add_new_item'               => __( 'Add New Category' ),
			'new_item_name'              => __( 'New Category Name' ),
			'separate_items_with_commas' => __( 'Separate categories with commas' ),
			'add_or_remove_items'        => __( 'Add or remove categories' ),
			'choose_from_most_used'      => __( 'Choose from the most used categories' ),
		);

		register_taxonomy(
			'noticicategory',
			'notici',
			array(
				'label'        => __( 'Notice Category' ),
				'labels'       => $labels,
				'hierarchical' => true,
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => 'notice-category' ),
			)
		);
	}


	function notici_add_metabox() {
		add_meta_box( 'notici_render_admin_metabox', 'Notice time', array( $this, 'notici_render_admin_metabox' ), 'notici' );
	}

	function notici_render_admin_metabox() {

		// Get post meta.
		global $post;
		$custom         = get_post_custom( $post->ID );
		$meta_startdate = $custom['notici_startdate'][0];
		$meta_enddate   = $custom['notici_enddate'][0];
		$meta_starttime = $custom['notici_starttime'][0];
		$meta_endtime   = $custom['notici_endtime'][0];

		// WP nonce
		echo '<input type="hidden" name="notici-nonce" id="notici-nonce" value="' .
		wp_create_nonce( 'notici-nonce' ) . '" />';
		?>
			<div class="tf-meta">
			<ul>
				<li><label>Start Date</label><input name="notici_startdate" class="tfdate" value="<?php echo esc_attr( $meta_startdate ); ?>" /><em> YYYY-MM-DD, like 2019-12-31</em></li>
				<li><label>Start Time</label><input name="notici_starttime" value="<?php echo esc_attr( $meta_starttime ); ?>" /><em> Use 24h format (7pm = 19:00)</em></li>
				<li><label>End Date</label><input name="notici_enddate" class="tfdate" value="<?php echo esc_attr( $meta_enddate ); ?>" /><em> YYYY-MM-DD, like 2019-12-31</em></li>
				<li><label>End Time</label><input name="notici_endtime" value="<?php echo esc_attr( $meta_endtime ); ?>" /><em> Use 24h format (7pm = 19:00)</em></li>
			</ul>
			</div>
		<?php
	}

	function notici_styles_and_scripts() {
		add_action( 'admin_print_styles-post.php', array( $this, 'notici_styles' ), 1000 );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'notici_styles' ), 1000 );

		add_action( 'admin_print_scripts-post.php', array( $this, 'notici_scripts' ), 1000 );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'notici_scripts' ), 1000 );
	}


	function notici_styles() {
		global $post_type;
		if ( 'notici' != $post_type ) {
			return;
		}
		wp_enqueue_style( 'jquery-ui-datepicker-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( $this->plugin_name . '-notici-admin', plugin_dir_url( __DIR__ ) . 'admin/css/notici-admin.css', array(), $this->version, 'all' );
	}

	function notici_scripts() {
		global $post_type;
		if ( 'notici' != $post_type ) {
			return;
		}

		wp_enqueue_script( $this->plugin_name . '-notici-admin', plugin_dir_url( __DIR__ ) . 'admin/js/notici-admin.js', array( 'jquery', 'jquery-ui-datepicker' ) );
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

		// Start date is mandatory
		if ( ! isset( $_POST['notici_startdate'] ) ) :
			return $post;
		endif;

		// Update start date.
		$update_startdate = strtotime( sanitize_text_field( $_POST['notici_startdate'] ) );
		update_post_meta( $post->ID, 'notici_startdate', date( 'Y-m-d', $update_startdate ) );

		// Update end date if submitted.
		if ( null != $_POST['notici_enddate'] ) {
			$update_enddate = strtotime( sanitize_text_field( $_POST['notici_enddate'] ) );
			update_post_meta( $post->ID, 'notici_enddate', date( 'Y-m-d', $update_enddate ) );
		} else {
			update_post_meta( $post->ID, 'notici_enddate', null );
		}

		// Update start and end time if matches regex pattern.
		$update_starttime = sanitize_text_field( $_POST['notici_starttime'] );
		if ( preg_match( '/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $update_starttime ) ) {
			update_post_meta( $post->ID, 'notici_starttime', $update_starttime );
		} else {
			update_post_meta( $post->ID, 'notici_starttime', null );
		}

		$update_endtime = sanitize_text_field( $_POST['notici_endtime'] );
		if ( preg_match( '/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $update_endtime ) ) {
			update_post_meta( $post->ID, 'notici_endtime', $update_endtime );
		} else {
			update_post_meta( $post->ID, 'notici_endtime', null );
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

