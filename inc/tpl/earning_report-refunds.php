<?php
// @TODO Remove demo data later
$courses_data = [
	(object) [
		'key'   => 'refund-20171211-course::jim4u::photoshop-essentials',
		'value' => '20::19::Photoshop essentials'
	],
	(object) [
		'key'   => 'refund-20180416-course::4mike::photoshop-essentials',
		'value' => '20::19::Photoshop essentials'
	],
	(object) [
		'key'   => 'refund-20180421-course::r0ck::grace-me-up',
		'value' => '25::23.75::Grace me up'
	],
	(object) [
		'key'   => 'refund-20180502-subscription::ollie::gold',
		'value' => '20::19::Gold',
	],
];

$numbers_data = [
	'# Refund' => 0,
	'Refund'   => [ 0 ],
];

$table_data = [];

// @TODO Grab rows for course sales

foreach ( $courses_data as $row ) {
	$k     = explode( '::', $row->key );
	$datum = explode( '::', $row->value );

	$key = str_replace( 'refund-', '', $k[0] );
	$date = substr( $key, 6, 2 ) . '-' . substr( $key, 4, 2 ) . '-' . substr( $key, 2, 2 );

	$table_data[] = [
		$date,
		"<span class='futura'>$k[1]</span>",
		ucfirst( substr( $key, 9 ) ),
		$datum[2],
		[ .7 * $datum[1] ],
	];

	$numbers_data['# Refund'] ++;
	$numbers_data['Refund'][] = $datum[1];
}

$table_header = [ [ 'Date', 'Student', 'Product', 'Description', 'Refund', ], ];
$table_footer = [ [ '', '', '', '<b class="lifterlms-price">TOTAL</b>', $numbers_data['Refund'], ] ];


if ( $table_data ) {
	$table_data = array_merge(
		$table_header,
		$table_data,
		$table_footer
	);
}
?>
	<div class="last-row-last-cols-overline">
		<?php
		include 'earning-report-render-numbers.php';
		include 'earning-report-render-table.php';
		?>
		<h5 class="llms-form-field wer-accurate-info">
			<span class="fa fa-asterisk"></span>
			<?php
			printf( __( 'For more accurate information about your earnings, please visit your %s Stripe account %s.', WXBIN ), '<a href="https://dashboard.stripe.com/payments">', '</a>' )
			?>
		</h5>
	</div>
<?php

