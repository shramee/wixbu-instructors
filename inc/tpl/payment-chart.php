<?php
$chart = [
	'max'         => 0,
	'all_numbers' => [],
	'data'        => $data,
];

foreach ( $data as $i => $d ) {
	foreach ( $d['numbers'] as $num ) {
		if ( abs( $num ) > $chart['max'] ) {
			$chart['max'] = abs( $num );
		}
	}
	$data[ $i ]['sum'] = array_sum( $d['numbers'] );
}

$chart['max'] = ceil( $chart['max'] / 100 ) * 100;
?>

<div class="prc-wrap">

	<div class="prc">
		<?php
		include 'payment-chart-number-line.php';

		foreach ( $data as $d ) {
			include 'payment-chart-section.php';
		}
		?>
	</div>

	<div class="wer-accurate-info">
		<span class="fa fa-asterisk"></span>
		<?php
		printf( __( 'For more accurate information about your earnings, please visit your %sStripe account%s.', WXBIN ), '<a href="https://dashboard.stripe.com/payments">', '</a>' )
		?>
	</div>

</div>

<div class="prcl">

	<div class="prclt">
		<div class="prcltc bcirc"></div>
		Your Earnings <span class="fa fa-asterisk"></span>
	</div>

	<div class="prclt">
		<div class="prcltc gnar"></div>
		Recurring Purchases <span class="fa fa-asterisk"></span>
	</div>

	<div class="prclt">
		<div class="prcltc lgnar"></div>
		Single Purchases <span class="fa fa-asterisk"></span>
	</div>

	<div class="prclt">
		<div class="prcltc gyar"></div>
		Refunds <span class="fa fa-asterisk"></span>
	</div>

</div>