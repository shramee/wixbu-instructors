<?php

/**
 * Wixbu dashboard public class
 */
class Wixbu_Dash_Public{

	/** @var Wixbu_Dash_Public Instance */
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
	 * @return Wixbu_Dash_Public instance
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
		$this->token   =   Wixbu_Dash::$token;
		$this->url     =   Wixbu_Dash::$url;
		$this->path    =   Wixbu_Dash::$path;
		$this->version =   Wixbu_Dash::$version;
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
	 * Set default tab
	 * @param string $tab Default tab
	 * @filter llms_student_dashboard_default_tab
	 * @return string Default tabs
	 */
	public function llms_student_dashboard_default_tab( $tab ) {
		return 'edit-account';
	}


	/**
	 * Outputs opening tag for .wixbu-dash-content-wrap
	 * @action lifterlms_before_student_dashboard_content 25
	 */
	public function lifterlms_before_student_dashboard_content() {
		echo '<div class="wixbu-dash-content-wrap">';
	}

	/**
	 * Outputs closing tag for .wixbu-dash-content-wrap
	 * @action lifterlms_after_student_dashboard 5
	 */
	public function lifterlms_after_student_dashboard() {
		echo '</div><!-- .wixbu-dash-content-wrap -->';
	}

	/**
	 *
	 * @action lifterlms_before_update_fields
	 */
	public function lifterlms_get_person_fields( $fields, $screen ) {
		if ( $screen == 'account' ) {
			$sec_fields = [
				'email_address',
				'email_address_confirm',
				'first_name',
				'last_name',
				'llms_phone',
				'current_password',
				'password',
				'password_confirm',
				'llms-password-strength-meter',
				'llms-password-change-toggle',
			];

			if ( strpos( $_SERVER['REQUEST_URI'], 'edit-address' ) ) {
				$sec_fields = [
					'llms_billing_address_1',
					'llms_billing_address_2',
					'llms_billing_city',
					'llms_billing_state',
					'llms_billing_zip',
					'llms_billing_country',
				];
			} else {
				add_action( 'lifterlms_before_update_button', [ $this, 'wixbu_delete_account_button', ] );
			}

			$_fields = $fields;
			$fields = [];
			foreach ( $_fields as $k => $f ) {
				if ( in_array( $f['id'], $sec_fields ) ) {
					$fields[] = $f;
				}
			}
		}

		return $fields;
	}

	/**
	 * Adds delete account button in dashboard
	 * @action lifterlms_before_update_button
	 */
	public function wixbu_delete_account_button() {
		$ajax_url = 'admin-ajax.php?action=wixbu_delete_user&nonce=' . wp_create_nonce( 'wixbu-delete-user' );
		?>
		<div class="llms-form-field type-submit llms-cols-3" style="float: right;">
			<a class="delete-account" href="<?php echo admin_url( $ajax_url ) ?>">
				<?php echo strpos( get_locale(), 'ES' ) !== false ? 'Editar Cuenta' : __( 'Close account' ); ?></a>
		</div>
		<?php
	}

	public function lifterlms_update_account_redirect( $link ) {
		if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
			$link = $_REQUEST['_wp_http_referer'];
		}

		return $link;
	}
	/**
	 * Filters students' dashboard tabs
	 * @param array $tbs Default tabs
	 * @filter llms_get_student_dashboard_tabs
	 * @return array Tabs
	 */
	public function llms_get_student_dashboard_tabs( $tbs ) {
		$tabs = [];

		// Editar Cuenta
		$tabs['edit-account'] = $tbs['edit-account'];
//		$tabs['edit-account']['content'] = [ $this, 'panel_edit_account', ];

		// Editar Dirección
		$tabs['edit-address'] = [
			'content' => function() {
			echo '<div class="llms-personal-form edit-address">';

				LLMS_Student_Dashboard::output_edit_account_content();

			echo '</div>';

			},
			'endpoint' => 'edit-address',
			'title' => __( 'Edit' ) . ' ' . __( 'Address', 'lifterlms' ),
		];

		// Membresía
		$tabs['membership'] = [
			'content' => [ $this, 'panel_membership', ],
			'endpoint' => 'membership',
			'title' => __( 'Membership', 'lifterlms' ),
		];

		// Mis Cursos
		$tabs['view-courses'] = $tbs['view-courses'];

		// Historial de Facturación
		$tabs['orders'] = $tbs['orders'];

		// Notificaciones
		$tabs['notifications'] = $tbs['notifications'];

		// Mis Logros
		$tabs['view-achievements'] = $tbs['view-achievements'];

		return $tabs;
	}

	public function panel_edit_account() {
		echo '<h1>panel_edit_account</h1>';
	}

	public function panel_edit_address() {
		echo '<h1>panel_edit_address</h1>';
	}

	public function panel_membership() {
		echo '<h1>panel_membership</h1>';
	}
}