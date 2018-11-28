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
define( 'INSTRUCTOR_SHARE', 77 );
define( 'WIXBU_COMMISSION', 100 - INSTRUCTOR_SHARE );
define( 'WIXBU_PAYOUT_DATE', 25 );

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
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {

		self::$token   = WXBIN;
		self::$file    = __FILE__;
		self::$url     = plugin_dir_url( __FILE__ );
		self::$path    = plugin_dir_path( __FILE__ );
		self::$version = '1.0.0';

		register_activation_hook( __FILE__, [ $this, 'create_tables' ] );
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );

	}

	public function next_payout_date() {
		$date = date( 'Y-m' );
		$date = explode( '-', $date );
		if ( $date[1] < 12 ) {
			$date[1]++;
		} else {
			$date[0]++;
		}

		$date = "$date[0]-$date[1]-" . WIXBU_PAYOUT_DATE;

		return strtotime( $date );
	}

	public function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( "
			CREATE TABLE {$wpdb->prefix}wixbu_payouts (
				id BIGINT(20) NOT NULL AUTO_INCREMENT,
				status VARCHAR(255),
				user_id BIGINT(20),
				amount DOUBLE,
				created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

				orders LONGTEXT,

				gross_amount DOUBLE,
				total_payment DOUBLE,

				PRIMARY KEY (id),
				KEY user_id(user_id),
				KEY created (created)
			) $charset_collate;
			" );
	}

	/**
	 * @param int $id
	 * @return object
	 */
	public function get_payout( $id ) {
//		return $this->query_payouts( [ 'id' => $id ] );
		return $this->mock_payout();
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public function get_payouts() {
//		return $this->query_payouts( [ 'id' => $id ] );
		// Mockup payouts
		return[
			$this->mock_payout(),
			$this->mock_payout(),
			$this->mock_payout(),
			$this->mock_payout(),
		];
	}

	/**
	 * @return object
	 */
	public function mock_payout(  ) {
		if ( isset( $this->mock_month ) ) {
			$this->mock_month ++;
		} else {
			$this->mock_month = 1;
		}

		$gross = rand( 500, 1600 );
		$data = [
			'id' => 259,
			'status' => 'sent',
			'user_id' => get_current_user_id(),
			'amount' => $gross * INSTRUCTOR_SHARE / 100,
			'created' => strtotime( "- $this->mock_month months" ),
			'orders' => '3209,3202,3200,3198,3196',
			'gross_amount' => $gross,
			'total_payment' => $gross * 1.18,
			'platform_fees' => $gross * WIXBU_COMMISSION / 100,
		];

		return (object) $data;
	}

	public function query_user_payouts( $args = [] ) {
		$args['user'] = get_current_user_id();

		return $this->query_payouts( $args );
	}

	public function query_payouts( $args = [] ) {
		global $wpdb;

		$columns = [ 'id', 'user', 'amount', 'date', 'meta', ];

		$where = [];

		foreach ( $where as $k => $v ) {
			if ( in_array( $k, $columns ) ) {
				$where[] = "{$k} LIKE '{$v}'";
			}
		}

		if ( $where ) {
			$where = ' WHERE ' . implode( ' AND ', $where );
		}

		return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wixbu_payouts{$where} LIMIT 0, 12;" );
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
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * @param int|string $sent Timestamp
	 */
	public static function aprox_arrival_from_date_sent( $sent ) {
		$sent = strtotime( $sent );
		return date( 'M d, Y', strtotime( '+ 7 weekdays', $sent ) );
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
Wixbu_Instructors::instance();
