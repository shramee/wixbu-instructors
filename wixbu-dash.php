<?php
/*
Plugin Name: Wixbu dashboard
Plugin URI: http://shramee.me/
Description: Simple plugin starter for quick delivery
Author: Shramee
Version: 1.0.0
Author URI: http://shramee.me/
@developer shramee <shramee.srivastav@gmail.com>
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';

/**
 * Wixbu dashboard main class
 * @static string $token Plugin token
 * @static string $file Plugin __FILE__
 * @static string $url Plugin root dir url
 * @static string $path Plugin root dir path
 * @static string $version Plugin version
 */
class Wixbu_Dash{

	/** @var Wixbu_Dash Instance */
	private static $_instance = null;

	/** @var string Token */
	public static $token;

	/** @var string Version */
	public static $version;

	/** @var string Plugin main __FILE__ */
	public static $file;

	/** @var string Plugin directory url */
	public static $url;

	/** @var string Plugin directory path */
	public static $path;

	/** @var Wixbu_Dash_Admin Instance */
	public $admin;

	/** @var Wixbu_Dash_Public Instance */
	public $public;

	/**
	 * Return class instance
	 * @return Wixbu_Dash instance
	 */
	public static function instance( $file ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $file );
		}
		return self::$_instance;
	}

	/**
	 * Constructor function.
	 * @param string $file __FILE__ of the main plugin
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct( $file ) {

		self::$token   = 'wixbu-dash';
		self::$file    = $file;
		self::$url     = plugin_dir_url( $file );
		self::$path    = plugin_dir_path( $file );
		self::$version = '1.0.0';

		$this->_admin(); //Initiate admin
		$this->_public(); //Initiate public

	}

	/**
	 * Initiates admin class and adds admin hooks
	 */
	private function _admin() {
		//Instantiating admin class
		$this->admin = Wixbu_Dash_Admin::instance();

		//Enqueue admin end JS and CSS
		add_action( 'wp_ajax_wixbu_delete_user',	array( $this->admin, 'wp_ajax_wixbu_delete_user' ) );

	}

	/**
	 * Initiates public class and adds public hooks
	 */
	private function _public() {
		//Instantiating public class
		$this->public = Wixbu_Dash_Public::instance();

		//Enqueue front end JS and CSS
		add_action( 'wp_enqueue_scripts',	array( $this->public, 'enqueue' ) );
		add_filter( 'llms_get_student_dashboard_tabs',	array( $this->public, 'llms_get_student_dashboard_tabs' ) );
		add_filter( 'llms_student_dashboard_default_tab',	array( $this->public, 'llms_student_dashboard_default_tab' ) );
		add_filter( 'lifterlms_before_student_dashboard_content',	array( $this->public, 'lifterlms_before_student_dashboard_content' ), 25 );
		add_filter( 'lifterlms_after_student_dashboard',	array( $this->public, 'lifterlms_after_student_dashboard' ), 5 );
		add_filter( 'lifterlms_get_person_fields',	array( $this->public, 'lifterlms_get_person_fields' ), 99, 2 );
		add_filter( 'lifterlms_update_account_redirect',	array( $this->public, 'lifterlms_update_account_redirect' ), 99, 2 );

	}
}

/** Intantiating main plugin class */
Wixbu_Dash::instance( __FILE__ );
