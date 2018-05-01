<?php
wp_enqueue_style( WXBIN . '-css' );

$tabs   = [
	'refunds'       => 'Refunds',
	'courses'       => 'Courses',
	'subscriptions' => 'Subscriptions',
	'global'        => 'Global',
];
$tabnow = filter_input( INPUT_GET, 'tab' );

if ( ! isset( $tabs[ $tabnow ] ) ) {
	$tabnow = 'courses';
}
$wixbu_earnings_report_show_time_range = true;
$wixbu_from = filter_input( INPUT_GET, 'from' );
$wixbu_to = filter_input( INPUT_GET, 'to' );

if ( ! $wixbu_from ) {
	$wixbu_from = date( 'Y-m-d', strtotime( 'first day of this month' ) );
}
if ( ! $wixbu_to ) {
	$wixbu_to = date( 'Y-m-d' );
}
if ( 0 < version_compare( $wixbu_from, $wixbu_to ) ) {
	$wixbu_to = $wixbu_from;
}
?>

<div id="wixbu-earnings-report">

	<div class="wer-tabs">
		<?php
		foreach ( $tabs as $k => $tab ) {
			$classes = 'wer-tab';
			if ( $k == $tabnow ) {
				$classes .= ' wer-active';
			}
			echo "<a class='$classes' href='?tab=$k'>$tab</a>";
		}
		?>
	</div>
	<div class="wer-tab-content" id="wer-tab-<?php echo $tabnow; ?>">
		<?php
		include "earning_report-$tabnow.php";
		?>
	</div>

	<?php
	if ( $wixbu_earnings_report_show_time_range ) {
		?>
		<div class="wer-time-range">
			<a class="futura-li" href="<?php echo add_query_arg( [
				'from' => date( 'Y-m' ) . '-01',
				'to' => date( 'Y-m-d' ),
			] ); ?>">
				<?php $this->e_en_es( __( 'This Month', WXBIN ), 'Este mes' ); ?> </a>

			<a class="futura-li" href="<?php echo add_query_arg( [
				'to' => date( 'Y-m-d', strtotime( 'last day of previous month' ) ),
				'from' => date( 'Y-m-d', strtotime( 'first day of previous month' ) ),
			] ); ?>">
				<?php $this->e_en_es( __( 'Last Month', WXBIN ), 'El mes pasado' ); ?></a>

			<a class="futura-li" href="<?php echo add_query_arg( [
				'from' => date( 'Y' ) . '-01-01',
				'to' => date( 'Y-m-d' ),
			] ); ?>">
				<?php $this->e_en_es( __( 'Last year', WXBIN ), 'El año pasado' ); ?></a>

			<form class="alignright">
				<input type="date" value="<?php echo $wixbu_from ?>" name="from">
				<a class="futura-li">To</a>
				<input type="date" value="<?php echo $wixbu_to ?>" name="to">
			</form>
		</div>
		<?php
	}
	?>
</div>
