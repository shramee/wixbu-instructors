<?php
$income_data = [
	(object) [
		'key'   => 'subs-20180402::james::gold',
		'value' => '20::19::Gold',
	],
	(object) [
		'key'   => 'sale-20180405::jack::photoshop-essentials',
		'value' => '20::19::Photoshop essentials'
	],
	(object) [
		'key'   => 'subs-20180407::lily::diamond',
		'value' => '25::23.75::Diamond',
	],
	(object) [
		'key'   => 'sale-20180407::jane::photoshop-essentials',
		'value' => '20::19::Photoshop essentials'
	],
	(object) [
		'key'   => 'subs-20180408::jake::gold',
		'value' => '20::19::Gold',
	],
	(object) [
		'key'   => 'subs-20180408::harry::gold',
		'value' => '20::19::Gold',
	],
	(object) [
		'key'   => 'sale-20180412::rose::grace-me-up',
		'value' => '25::23.75::Grace me up'
	],
	(object) [
		'key'   => 'subs-20180416::amy::diamond',
		'value' => '25::23.75::Diamond',
	],
	(object) [
		'key'   => 'sale-20180417::rose::marketing-tactics',
		'value' => '50::47.5::Marketing tactics'
	],
	(object) [
		'key'   => 'sale-20180419::jane::marketing-tactics',
		'value' => '50::47.5::Marketing tactics'
	],
	(object) [
		'key'   => 'sale-20180422::phil::marketing-tactics',
		'value' => '50::47.5::Marketing tactics'
	],
	(object) [
		'key'   => 'subs-20180423::thom::diamond',
		'value' => '25::23.75::Diamond',
	],
	(object) [
		'key'   => 'subs-20180425::joost::diamond',
		'value' => '25::23.75::Diamond',
	],
	(object) [
		'key'   => 'sale-20180429::ronald::photoshop-essentials',
		'value' => '20::19::Photoshop essentials'
	],
];

$numbers_data = [
	'Net Income' => [
		'value' => [ 0 ],
		'help'  => sprintf( __( 'Amount earned after legal and Stripe fees. To learn more please %s click here %s.', WXBIN ), '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
	'Your share' => [
		'value' => [ 0 ],
		'help'  => sprintf( __( '%s of net income. To learn more please %s click here %s.', WXBIN ), '70%', '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
];

$chart_data = [];
$total_income = 0;

foreach ( $income_data as $datum ) {
	$k     = explode( '::', $datum->key );
	$datum = explode( '::', $datum->value );

	$date       = str_replace( 'sale-', '', str_replace( 'subs-', '', $k[0] ) );
	$date       = substr( $date, 0, 4 ) . '-' . substr( $date, 4, 2 ) . '-' . substr( $date, 6, 2 );
	if ( ! isset( $chart_data[ $date ] ) ) {
		$chart_data[ $date ] = 0;
	}
	$total_income += $datum[1];
	$chart_data[ $date ] = $total_income;

	$numbers_data['Net Income']['value'][] = number_format_i18n( $datum[1], 2 );
	$numbers_data['Your share']['value'][] = number_format_i18n( $datum[1] * .7, 2 );
}

include 'earning-report-render-numbers.php';
?>
<script src="https://www.gstatic.com/charts/loader.js"></script>

<div id="chartContainer" style="height: 380px;"></div>
<script>
	var chartData = [], incomedata = <?php echo json_encode( $chart_data ) ?>;
	google.charts.load( 'current', {packages: ['corechart', 'line']} );
	google.charts.setOnLoadCallback( drawLineColors );

	function drawLineColors() {
		var data = new google.visualization.DataTable();
		data.addColumn( 'date', 'Time' );
		data.addColumn( 'number', 'Net income' );
		data.addColumn( 'number', 'Your share' );

		for ( var row in incomedata ) {
			chartData.push( [
				new Date( row ),
				Math.round( 100 * incomedata[row] ) / 100,
				Math.round( 100 * incomedata[row] * .7 ) / 100,
			] );
		}

		data.addRows( chartData );

		for (var i = 0; i < data.getNumberOfRows(); i++) {
			data.setFormattedValue( i, 1, data.getValue( i, 1 ) + '€' );
			data.setFormattedValue( i, 2, data.getValue( i, 2 ) + '€' );
		}

			var options = {
			hAxis: {},
			vAxis: {
				format: '##,###€'
			},
			colors: ['#508fe2', '#1d8a66']
		};

		var chart = new google.visualization.LineChart( document.getElementById( 'chartContainer' ) );
		chart.draw( data, options );
	}
</script>

<h5 class="llms-form-field wer-accurate-info">
	<span class="fa fa-asterisk"></span>
	<?php
	printf( __( 'For more accurate information about your earnings, please visit your %s Stripe account %s.', WXBIN ), '<a href="https://dashboard.stripe.com/payments">', '</a>' )
	?>
</h5>
