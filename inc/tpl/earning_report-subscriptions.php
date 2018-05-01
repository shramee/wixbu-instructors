<?php
$courses_data = [
	'sale-20180502::james::gold'    => [
		'paid'       => '20',
		'net_income' => '19',
		'timestamp'  => '',
	],
	'sale-20180502::lily::diamond'  => [
		'paid'       => '25',
		'net_income' => '23.75',
		'timestamp'  => '',
	],
	'sale-20180502::jake::gold'     => [
		'paid'       => '20',
		'net_income' => '19',
		'timestamp'  => '',
	],
	'sale-20180502::harry::gold'    => [
		'paid'       => '20',
		'net_income' => '19',
		'timestamp'  => '',
	],
	'sale-20180502::amy::diamond'   => [
		'paid'       => '25',
		'net_income' => '23.75',
		'timestamp'  => '',
	],
	'sale-20180502::thom::diamond'  => [
		'paid'       => '25',
		'net_income' => '23.75',
		'timestamp'  => '',
	],
	'sale-20180502::joost::diamond' => [
		'paid'       => '25',
		'net_income' => '23.75',
		'timestamp'  => '',
	],
];

$numbers_data = [
	'# Sales'    => [
		'value' => 0,
		'help'  => sprintf( __( '# sales during selected period. To learn more please %s click here %s.', WXBIN ), '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
	'Net Income' => [
		'value' => [ 0 ],
		'help'  => sprintf( __( 'Amount earned after legal and Stripe fees. To learn more please %s click here %s.', WXBIN ), '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
	'Your share' => [
		'value' => [ 0 ],
		'help'  => sprintf( __( '%s of net income. To learn more please %s click here %s.', WXBIN ), '70%', '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
];

$table_data = [];

foreach ( $courses_data as $k => $datum ) {
	$k = explode( '::', $k );
//	$k[2] .= $k[1];
	if ( ! isset( $table_data[ $k[2] ] ) ) {
		$table_data[ $k[2] ] = [
			$k[2],
			0,
			[],
			[],
		];
	}
	$table_data[ $k[2] ][1] ++;
	$table_data[ $k[2] ][2][] = $datum['net_income'];
	$table_data[ $k[2] ][3][] = .7 * $datum['net_income'];
	$numbers_data['# Sales']['value'] ++;
	$numbers_data['Net Income']['value'][] = $datum['net_income'];
	$numbers_data['Your share']['value'][] = .7 * $datum['net_income'];
}

if ( $table_data ) {
	$table_data = array_merge(
	// Headers
		[ [ 'Subscription', '# Sales', 'Net Income', 'Your Income', ] ],
		// Table data
		$table_data,
		// Footer
		[ [ '', '', '<b class="lifterlms-price">TOTAL</b>', $numbers_data['Your share']['value'], ] ]
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
