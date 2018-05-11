<?php

$account_id = get_user_meta( get_current_user_id(), 'stripe_user_id', 1 );

if ( isset( $_REQUEST['code'] ) ) {
	if ( $_REQUEST['code'] ) {
		/** @var LLMS_Payment_Gateway_Stripe $gateway */
		$gateway = LLMS()->payment_gateways()->get_gateway_by_id( 'stripe' );

		$auth_code = $_REQUEST['code'];
		$client_id = 'ca_Ben9mXacDyauRq9pngU0rkKuDvgYs6kN';
		$secret     = $gateway->get_secret_key();

		if ( 'live' === $gateway->get_api_mode() ) {
			$client_id = 'ca_Ben96eD05ONMEGhXtDOfmJUNjUdoAlIF';
		}

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, "https://connect.stripe.com/oauth/token" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, "client_secret=$secret&code=$auth_code&grant_type=authorization_code" );
		curl_setopt( $ch, CURLOPT_POST, 1 );

		$headers   = array();
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		$result = curl_exec( $ch );
		if ( ! curl_errno( $ch ) ) {
			$result = json_decode( $result, 1 );
			if ( isset( $result['stripe_user_id'] ) ) {
				update_user_meta( get_current_user_id(), 'stripe_user_id', $result['stripe_user_id'] );
				update_user_meta( get_current_user_id(), 'stripe_publishable_key', $result['stripe_publishable_key'] );
				$account_id = $result['stripe_user_id'];
			} else {
				echo '<h4 style="color:#c30;">An error occured: ' . $result['error_description'] . '<br>Please try again later or contact admin.</h4>';
			}
		}
		curl_close( $ch );
	} else {
		delete_user_meta( get_current_user_id(), 'stripe_user_id' );
		delete_user_meta( get_current_user_id(), 'stripe_publishable_key' );
		$account_id = '';
	}
}
?>

<div class="llms-person-form-wrapper">

	<div class="llms-form-fields">
		<div class="llms-form-field type-email llms-cols-2">
		</div>
		<div class="llms-form-field type-email llms-cols-8">
			<?php
			if ( ! $account_id ) {
				?>
				<div class="llms-form-field">
					<a class="llms-button-action"
						 href="https://dashboard.stripe.com/login?redirect=%2Foauth%2Fauthorize%3Fscope%3Dread_write%26response_type%3Dcode%26client_id%3D<?php echo $client_id ?>&force_login=true">
						<?php $this->e_en_es( __( 'Connect Stripe account', WXBIN ), 'Cuenta Connect Stripe' ) ?></a>
				</div>
				<div class="llms-form-field align-right">
					or <a
						href="https://dashboard.stripe.com/oauth/authorize?response_type=code&client_id=<?php echo $client_id ?>&scope=read_write">
						<?php $this->e_en_es( __( 'Create a Stripe account', WXBIN ), 'Crea una cuenta Stripe' ) ?></a>
				</div>
				<?php
			} else {
				?>
				<div class="llms-form-field elementor-align-center">
					<i class="fa fa-4x fa-check" style="color: #080;"></i>
					<h3><?php $this->e_en_es( __( 'Your Stripe account is connected', WXBIN ), 'Cuenta Connect Stripe' ) ?></h3>
					<a
						href="?code=">
						<?php $this->e_en_es( __( 'Disconnect your Stripe account', WXBIN ), 'Crea una cuenta Stripe' ) ?></a>
				</div>
			<?php }
			?>
		</div>

	</div>

</div>
