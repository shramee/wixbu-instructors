<?php
/**
 * Wixbu instructors Admin class
 */
class Wixbu_Instructors_Admin {

	/** @var Wixbu_Instructors_Admin Instance */
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
	 * Main Wixbu instructors Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return Wixbu_Instructors_Admin instance
	 * @since 	1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since 	1.0.0
	 */
	private function __construct() {
		$this->token   =   Wixbu_Instructors::$token;
		$this->url     =   Wixbu_Instructors::$url;
		$this->path    =   Wixbu_Instructors::$path;
		$this->version =   Wixbu_Instructors::$version;
	} // End __construct()

	/**
	 * Delete user account AJAX handler
	 * @action wp_ajax_wixbu_delete_user
	 */
	public function wp_ajax_wixbu_delete_user() {
		// Verify that the user intended to take this action.
		if ( ! wp_verify_nonce( filter_input( INPUT_GET, 'nonce' ), 'wixbu-delete-user' ) ) {
			return;
		}

		require_once(ABSPATH.'wp-admin/includes/user.php' );
		$current_user = wp_get_current_user();

		wp_delete_user( $current_user->ID );
?>
		<html>
		<body>
		<script>
			alert( '<?php
				echo strpos( get_locale(), 'ES' ) !== false ? 'Cuenta cerrada.' : __( 'Account deleted.' );
		?>' );
			window.location.href = "<?php echo home_url() ?>";
		</script>
		</body>
		</html>
		<?php

		die();
	}
}