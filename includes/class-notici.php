<?php

class Notici {

	protected $plugin_name;
	protected $version;

	public function __construct() {
		$this->version     = NOTICI_VERSION;
		$this->plugin_name = 'notici';

		$this->options_page();
		$this->register_cpt();
		$this->register_shortcodes();
	}

	private function options_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-notici-options.php';
		new Notici_Options();

	}

	private function register_cpt() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-notici-register-cpt.php';
		new Notici_Register_Cpt( $this->plugin_name, $this->plugin_version );

	}

	private function register_shortcodes() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-notici-public.php';
		new Notici_Public();

	}



}
