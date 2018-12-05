<?php

$payout = Wixbu_Instructors::instance()->get_payout( $_GET['payout'] );

$orders = [];
$uid    = $payout->user_id;
$user   = get_user_by( 'id', $uid );

$address_meta = [
	'first_name',
	'last_name',
	'llms_billing_address_1',
	'llms_billing_address_2',
	'llms_billing_city',
	'llms_billing_state',
	'llms_billing_zip',
	'llms_billing_country',
	'llms_phone',
];
$user_fields  = [
	'user_email',
];

$formatting_callbacks = [
	'llms_billing_country' => function ( $val ) {
		$countries = get_lifterlms_countries();
		if ( $countries[ $val ] ) {
			$val = $countries[ $val ];
		}

		return $val;
	},
];

$format = '
first_name last_name
llms_billing_address_1
llms_billing_address_2
llms_billing_city
llms_billing_state llms_billing_zip llms_billing_country
llms_phone user_email';
?>

<div class="payout-details">
	<header>
		<div class="payout-id fw500 fr">
			Payout Number: <?php echo $payout->id; ?>
		</div>

		<a href="?" class="back fw500"><span class="fa-chevron-left fa"></span> Back</a>
	</header>

	<div class="wixbu-address fw500 fr">
		<br>
		<img src="http://wixbu.com/wp-content/uploads/2018/05/LOGO-300x72.png" alt="WIXBU"><br>
		Good Abbey SLNE B87921250<br>
		Calle San Juan de la Cruz 2,<br>
		Pozuelo de Alarcon. 28223 Espana<br>
	</div>
	<div class="instructor-address fw500">
		<?php
		foreach ( $address_meta as $key ) {
			$val = get_user_meta( $uid, $key, 1 );
			if ( isset( $formatting_callbacks[ $key ] ) && is_callable( $formatting_callbacks[ $key ] ) ) {
				$val = call_user_func( $formatting_callbacks[ $key ], $val );
			}
			$format = str_replace( $key, $val, $format );
		}
		foreach ( $user_fields as $key ) {
			$val = $user->$key;
			if ( isset( $formatting_callbacks[ $key ] ) && is_callable( $formatting_callbacks[ $key ] ) ) {
				$val = call_user_func( $formatting_callbacks[ $key ], $val );
			}
			$format = str_replace( $key, $val, $format );
		}
		echo str_replace( [ "\n\n\n\n", "\n\n\n", "\n\n", "\n" ], '<br>', $format );

		$orders_query = $payout->orders_query();

		include 'orders-table.php';
		?>

	</div>

	<table class="no-border sum-box">
		<tr>
			<td class="fw500">TOTAL PAID</td>
			<td class="fw500"><?php echo llms_price( $total_paid ) ?></td>
		</tr>
		<tr>
			<td>Tax</td>
			<td><?php echo llms_price( $total_tax ) ?></td>
		</tr>
		<tr>
			<td>Net Income</td>
			<td><?php echo llms_price( $total_income ) ?></td>
		</tr>
		<tr>
			<td>Wixbu Fees <?php echo WIXBU_COMMISSION ?>%</td>
			<td><?php echo llms_price( $total_income * WIXBU_COMMISSION / 100 ) ?></td>
		</tr>
		<tr>
			<td class="fw500">TOTAL EARNINGS</td>
			<td class="fw500"><?php echo llms_price( $total_income * INSTRUCTOR_SHARE / 100 ) ?></td>
		</tr>
	</table>
</div>

<div class="payout-date">
	<?php _e( 'Payment processed by Stripe', 'wixbu' ) ?><br>
	<?php printf( __( 'Date %s', 'wixbu' ), date( 'M d, Y', strtotime( $payout->created ) ) ); ?>
</div>

<?php
include 'order-popup.php';
