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
define( 'WIXBU_COMMISSION', 40 );
define( 'INSTRUCTOR_SHARE', 100 - WIXBU_COMMISSION );

/**
 * Wixbu instructors main class
 */
class Wixbu_Instructors {

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
	/** @var Wixbu_Instructors Instance */
	private static $_instance = null;
	/** @var Wixbu_Instructors_Admin Instance */
	public $admin;

	/** @var Wixbu_Instructors_Public Instance */
	public $public;

	/**
	 * Constructor function.
	 *
	 * @param string $file __FILE__ of the main plugin
	 *
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

	/**
	 * Query user meta
	 *
	 * @param string|array $name_patt Meta key pattern match
	 * @param int $uid User ID
	 * @param string $query_suffix SQL query suffix
	 *
	 * @return array|null Results of query
	 */
	static function query_umeta( $name_patt, $uid = 0, $query_suffix = null ) {
		if ( ! $uid ) {
			$uid = get_current_user_id();
		}
		$results = [];
		if ( $uid ) {
			/** @var wpdb $wpdb */
			global $wpdb;
			// Escape for sql query
			$name_patt = esc_sql( $name_patt );
			if ( is_array( $name_patt ) ) {
				$name_patt = implode( "' OR `meta_key` LIKE '", $name_patt );
			}
			$results = self::query_umeta_table( "`meta_key` LIKE '{$name_patt}'", $uid, $query_suffix );
		}
		return $results;
	}

	/**
	 * @param string $where WHERE part of the query
	 * @param string $query_suffix SQL query suffix
	 * @param int $uid User ID
	 * @param string $select
	 *
	 * @return array|null Results of query
	 */
	static function query_umeta_table( $where, $uid = 0, $query_suffix = null, $select = null ) {
		if ( ! $uid ) {
			$uid = get_current_user_id();
		}

		if ( ! $select ) $select = '`meta_key` as `key`, `meta_value` as `value`';

		if ( ! $query_suffix ) $query_suffix = 'ORDER BY umeta_id DESC;';

		$results = [];
		if ( $uid ) {
			/** @var wpdb $wpdb */
			global $wpdb;
			$results = $wpdb->get_results(
				"SELECT {$select} FROM {$wpdb->usermeta} " .
				"WHERE ( {$where} ) AND `user_id` = {$uid} " .
				$query_suffix
			);
		}
		return $results;
	}

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
		add_action( 'wp_ajax_wixbu_delete_user', array( $this->admin, 'wp_ajax_wixbu_delete_user' ) );

	}

	/**
	 * Initiates public class and adds public hooks
	 */
	private function _public() {
		//Instantiating public class
		$this->public = Wixbu_Instructors_Public::instance();

		//Enqueue front end JS and CSS
		add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue' ) );
		add_filter( 'llms_get_student_dashboard_tabs', array( $this->public, 'llms_get_student_dashboard_tabs' ), 11 );

	}

	public function wixbu_dash_required_notice() {
		$class   = 'notice notice-error';
		$message = sprintf( __( 'Oops, Wixbu instructors plugin is required. %s Download %s', WXBIN ), '<a target="_blank" href="https://github.com/shramee/wixbu-dashboard">', '</a>' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}
}

/** Intantiating main plugin class */
Wixbu_Instructors::instance( __FILE__ );
