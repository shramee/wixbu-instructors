<?php
wp_enqueue_style( WXBIN . '-css' );

$tabnow = filter_input( INPUT_GET, 'tab' );

if ( ! file_exists( __DIR__ . "/earning_report-$tabnow.php" ) ) {
	$tabnow = 'week';
	$_GET['tab'] = $tabnow;
}

Wixbu_Instructors_Public::second_level_tabs( [
	'?tab=year'  => __( 'This year', 'wixbu' ),
	'?tab=month' => __( 'This month', 'wixbu' ),
	'?tab=week'  => __( 'This week', 'wixbu' ),
] );

$wixbu_to = date( 'Y-m-d' );

$data_maps = [
	'sale' => 0,
	'subs' => 1,
	'rfnd' => 2,
];

?>

<div id="wixbu-earnings-report">
	<div class="wer-tab-content" id="wer-tab-<?php echo $tabnow; ?>">
		<?php
		include "earning_report-$tabnow.php";
		?>
	</div>
</div>
