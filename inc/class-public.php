<?php

/**
 * Wixbu instructors public class
 */
class Wixbu_Instructors_Public{

	/** @var Wixbu_Instructors_Public Instance */
	private static $_instance = null;

	/* @var string $token Plugin token */
	public $token;

	/* @var string $url Plugin root dir url */
	public $url;

	/* @var string $path Plugin root dir path */
	public $path;

	/* @var string $version Plugin version */
	public $version;

	/**
	 * Wixbu instructors public class instance
	 * @return Wixbu_Instructors_Public instance
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   =   Wixbu_Instructors::$token;
		$this->url     =   Wixbu_Instructors::$url;
		$this->path    =   Wixbu_Instructors::$path;
		$this->version =   Wixbu_Instructors::$version;
	}

	public static function second_level_tabs( $tabs, $echo = true ) {
		return Wixbu_Dash_Public::second_level_tabs( $tabs, $echo );
	}

	public static function output_chart( $data ) {
		require 'tpl/payment-chart.php';
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;

		wp_register_style( $token . '-css', $url . '/assets/front.css' );
		wp_enqueue_style( $token . '-chart', $url . '/assets/chart.css' );
		wp_register_script( $token . '-js', $url . '/assets/front.js', array( 'jquery' ) );
	}

	/**
	 * Filters students' dashboard tabs
	 * @param array $tbs Default tabs
	 * @filter llms_get_student_dashboard_tabs
	 * @return array Tabs
	 */
	public function llms_get_student_dashboard_tabs( $tbs ) {

		if ( ! wp_get_current_user()->has_cap( 'create_courses' ) ) {
			return $tbs;
		}

		$tabs = [];

		if ( is_admin() ) {
			$tabs = $tbs; // Fix for student dahboard permalinks
		}

		$tabs['edit-address'] = $tbs['edit-address'];
		$tabs['edit-account'] = $tbs['edit-account'];

		$tabs['settings'] = [
			'content' => [ $this, 'payment_gateway' ],
			'endpoint' => 'settings',
			'title' => $this->_en_es( __( 'Payment gateway', WXBIN ), 'Cuenta de pago' ),
		];

		$tabs['earnings-report'] = [
			'content' => [ $this, 'earnings_report' ],
			'endpoint' => 'earnings-report',
			'title' => $this->_en_es( __( 'Earnings report', WXBIN ), 'Informe de ganancias' ),
		];

		$tabs['payouts-history'] = [
			'content' => [ $this, 'payouts_history' ],
			'endpoint' => 'payouts-history',
			'title' => __( 'Payouts history', WXBIN ),
		];

		return $tabs;
	}

	public function payment_gateway() {
		require 'tpl/payment_gateway.php';
	}

	public function earnings_report() {
		require 'tpl/earnings_report.php';
	}

	public function payouts_history() {
		require 'tpl/payouts_history.php';
	}

	protected function e_en_es( $en, $es ) {
		echo strpos( get_locale(), 'ES' ) !== false ? $es : $en;
	}

	protected function _en_es( $en, $es ) {
		return strpos( get_locale(), 'ES' ) !== false ? $es : $en;
	}
}
