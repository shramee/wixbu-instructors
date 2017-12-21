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
		/*
		$ajax_url = 'admin-ajax.php?action=wixbu_delete_user&nonce=' . wp_create_nonce( 'wixbu-delete-user' );
		?>
		<div class="llms-form-field type-submit llms-cols-3" style="float: right;">
			<a class="delete-account" href="<?php echo admin_url( $ajax_url ) ?>" onclick="return 'CLOSE' == prompt( '<?php $this->e_en_es( __( 'Write the word \\\'CLOSE\\\' (all uppercase) to confirm.' ), 'Escribe la palabra \\\'CLOSE\\\' (todo en mayúsculas).' ); ?>' )">
				<?php $this->e_en_es( __( 'Delete account' ), 'Eliminar Cuenta' ); ?></a>
		</div>
		<?php
		*/
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
			'title' => $this->__en_es( __( 'Membership' ), 'Membresía' ),
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

	public function panel_membership() {
		$student = llms_get_student();
		$student_memberships = $student->get_membership_levels();
		$membership = '';
		$mem_id = 0;
		$mem_order = 0;
		if ( $student_memberships ) {
			$mem_id = end( $student_memberships );
			$membership = new LLMS_Membership( $mem_id );
			$membership = $membership->title;
//			var_dump( $mem_id );
			$orders = $student->get_orders();
			foreach( $orders['orders'] as $order ) {
//				var_dump( "$order->id => $order->product_id" );
				if( $order->product_id == $mem_id ) {
					$mem_order = $order->id;
				}
			}

		}
		?>
		<div class="llms-form-field type-text llms-cols-8">
			<label><?php $this->e_en_es( __( 'Membership' ), 'Membresía' ) ?></label>
			<input class="llms-field-input" placeholder="<?php $this->e_en_es( __( 'No active membership.' ), 'No membresía activa.' ) ?>" type="text" value="<?php echo $membership ?>" disabled="disabled">
		</div>
		<div class="llms-form-field type-text llms-cols-4 llms-cols-last">
			<label>&nbsp;</label>

		<?php
		if ( $membership ) {
			?>
			<form action="" id="llms-cancel-subscription-form" method="POST" onsubmit="return confirm( '<?php $this->e_en_es( __( 'Are you sure you want to cancel subscription?' ), '¿Estás seguro de que quieres cancelar tu suscripción?' ); ?>' )">
				<button class="llms-field-button llms-button-secondary" id="llms_cancel_subscription" type="submit" name="llms_cancel_subscription"><?php $this->e_en_es( __( 'CANCEL' ), 'Eliminar' ) ?></button>
				<?php wp_nonce_field( 'llms_cancel_subscription', '_cancel_sub_nonce' ); ?>
				<input name="order_id" type="hidden" value="<?php echo $mem_order; ?>">
			</form>


			<a class="llms-button-action" href="<?php echo get_permalink( get_page_by_path( 'membresia' ) ) ?>">
				<?php $this->e_en_es( __( 'CHANGE' ), 'CAMBIAR' ) ?></a>
			<a class="llms-button-action" href="<?php echo home_url( "panel-de-control/orders/$mem_order" ) ?>">
				<?php $this->e_en_es( __( 'PAYMENT INFO' ), 'INF. DE PAGO' ) ?></a>

			<?php
		} else {
			?>
						<a class="llms-button-action" href="<?php echo get_permalink( get_page_by_path( 'membresia' ) ) ?>">
				<?php $this->e_en_es( __( 'SUBSCRIBE' ), 'SUSCRIBIR' ) ?></a>

			<?php
		}
	}

	protected function e_en_es( $en, $es ) {
		echo strpos( get_locale(), 'ES' ) !== false ? $es : $en;
	}

	protected function __en_es( $en, $es ) {
		return strpos( get_locale(), 'ES' ) !== false ? $es : $en;
	}
}