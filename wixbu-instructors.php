<?php
/*
Plugin Name: Wixbu instructors and payments
Plugin URI: http://shramee.me/
Description: Handles instructors dashboard and payments
Author: Shramee
Version: 1.0.0
Author URI: http://shramee.me/
@developer shramee <shramee.srivastav@gmail.com>
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';

define( 'WXBIN', 'wixbu-instructors' );

/**
 * Wixbu instructors main class
 */
class Wixbu_Instructors{

	/** @var Wixbu_Instructors Instance */
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

	/** @var Wixbu_Instructors_Admin Instance */
	public $admin;

	/** @var Wixbu_Instructors_Public Instance */
	public $public;

	/**
	 * Return class instance
	 * @return Wixbu_Instructors instance
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

		self::$token   = WXBIN;
		self::$file    = $file;
		self::$url     = plugin_dir_url( $file );
		self::$path    = plugin_dir_path( $file );
		self::$version = '1.0.0';

		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );

	}

	public function plugins_loaded() {
		if ( class_exists( 'Wixbu_Dash' ) ) {
			$this->_admin(); //Initiate admin
			$this->_public(); //Initiate public
		} else {
			add_action( 'admin_notices', [ $this, 'wixbu_dash_required_notice' ] );
		}
	}

	/**
	 * Initiates admin class and adds admin hooks
	 */
	private function _admin() {
		//Instantiating admin class
		$this->admin = Wixbu_Instructors_Admin::instance();

		//Enqueue admin end JS and CSS
		add_action( 'wp_ajax_wixbu_delete_user',	array( $this->admin, 'wp_ajax_wixbu_delete_user' ) );

	}

	/**
	 * Initiates public class and adds public hooks
	 */
	private function _public() {
		//Instantiating public class
		$this->public = Wixbu_Instructors_Public::instance();

		//Enqueue front end JS and CSS
		add_action( 'wp_enqueue_scripts',	array( $this->public, 'enqueue' ) );
		add_filter( 'llms_get_student_dashboard_tabs',	array( $this->public, 'llms_get_student_dashboard_tabs' ), 11 );

	}

	public function wixbu_dash_required_notice() {
		$class = 'notice notice-error';
		$message = sprintf( __( 'Oops, Wixbu instructors plugin is required. %s Download %s', WXBIN ), '<a target="_blank" href="https://github.com/shramee/wixbu-dashboard">', '</a>' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}
}

/** Intantiating main plugin class */
Wixbu_Instructors::instance( __FILE__ );
