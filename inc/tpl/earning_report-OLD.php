<?php
$from = str_replace( '-', '', $wixbu_from );
$to = str_replace( '-', '', $wixbu_to );
$income_data = Wixbu_Instructors::query_umeta_table(
	"`meta_key` BETWEEN 'subs-{$from}0' AND 'subs-{$to}z' OR " .
	"`meta_key` BETWEEN 'sale-{$from}0' AND 'sale-{$to}z'"
);

$numbers_data = [
	'Net Income' => [
		'value' => [ 0 ],
		'help'  => sprintf( __( 'Amount earned after legal and Stripe fees. To learn more please %s click here %s.', WXBIN ), '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
	'Your share' => [
		'value' => [ 0 ],
		'help'  => sprintf( __( '%s of net income. To learn more please %s click here %s.', WXBIN ), '60%', '<a href="https://wixbu.com/metodos-de-pagos">', '</a>' ),
	],
];

$chart_data = [];
$total_income = 0;

foreach ( $income_data as $datum ) {
	$k     = explode( '::', $datum->key );
	$datum = explode( '::', $datum->value );

	$date       = substr( $k[0], 5 );
	$date       = substr( $date, 0, 4 ) . '-' . substr( $date, 4, 2 ) . '-' . substr( $date, 6, 2 );
	if ( ! isset( $chart_data[ $date ] ) ) {
		$chart_data[ $date ] = 0;
	}
	$total_income += $datum[1];
	$chart_data[ $date ] = $total_income;

	$numbers_data['Net Income']['value'][] = $datum[1];
	$numbers_data['Your share']['value'][] = $datum[1] * INSTRUCTOR_SHARE / 100;
}

include 'earning-report-render-chart.php';
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
			console.log( row );
			chartData.push( [
				new Date( row ),
				Math.round( 100 * incomedata[row] ) / 100,
				Math.round( 100 * incomedata[row] * <?php echo INSTRUCTOR_SHARE / 100 ?> ) / 100,
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
			pointSize: 3,
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
