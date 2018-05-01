<?php

/**
 * Wixbu dashboard public class
 */
class Wixbu_Instructors_Dash_Public{

	/** @var Wixbu_Instructors_Dash_Public Instance */
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
	 * Wixbu dashboard public class instance
	 * @return Wixbu_Instructors_Dash_Public instance
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
		$this->token   =   Wixbu_Instructors_Dash::$token;
		$this->url     =   Wixbu_Instructors_Dash::$url;
		$this->path    =   Wixbu_Instructors_Dash::$path;
		$this->version =   Wixbu_Instructors_Dash::$version;
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/front.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front.js', array( 'jquery' ) );
	}

	/**
	 * Filters students' dashboard tabs
	 * @param array $tbs Default tabs
	 * @filter llms_get_student_dashboard_tabs
	 * @return array Tabs
	 */
	public function llms_get_student_dashboard_tabs( $tbs ) {

		if ( ! in_array( 'instructor', wp_get_current_user()->roles ) ) {
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

		return $tabs;
	}

	protected function e_en_es( $en, $es ) {
		echo strpos( get_locale(), 'ES' ) !== false ? $es : $en;
	}

	protected function __en_es( $en, $es ) {
		return strpos( get_locale(), 'ES' ) !== false ? $es : $en;
	}
}

?>


