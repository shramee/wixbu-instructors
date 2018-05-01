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

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;

		wp_register_style( $token . '-css', $url . '/assets/front.css' );
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

		//Edit Account
		$tabs['edit-account'] = $tbs['edit-account'];

		//Address
		$tabs['edit-address'] = [
			'content' => function() {
				echo '<div class="llms-personal-form edit-address">';
					LLMS_Student_Dashboard::output_edit_account_content();
				echo '</div>';
			},
			'endpoint' => 'edit-address',
			'title' => __( 'Edit' ) . ' ' . __( 'Address', 'lifterlms' ),
		];

		$tabs['earnings-report'] = [
			'content' => [ $this, 'earnings_report' ],
			'endpoint' => 'earnings-report',
			'title' => $this->_en_es( __( 'Earnings report', WXBIN ), 'Informe de ganancias' ),
		];

		$tabs['payment-gateway'] = [
			'content' => [ $this, 'payment_gateway' ],
			'endpoint' => 'payment-gateway',
			'title' => $this->_en_es( __( 'Payment gateway', WXBIN ), 'Cuenta de pago' ),
		];

		return $tabs;
	}

	public function payment_gateway() {
		require 'tpl/payment_gateway.php';
	}

	public function earnings_report() {
		require 'tpl/earnings_report.php';
	}

	protected function e_en_es( $en, $es ) {
		echo strpos( get_locale(), 'ES' ) !== false ? $es : $en;
	}

	protected function _en_es( $en, $es ) {
		return strpos( get_locale(), 'ES' ) !== false ? $es : $en;
	}
}
