<?php
wp_enqueue_style( WXBIN . '-css' );

$tabs = [
	'?tab=year'  => 'This year',
	'?tab=month' => 'This month',
	'?tab=week'  => 'This week',
];
$tabnow = filter_input( INPUT_GET, 'tab' );

if ( ! isset( $tabs[ $tabnow ] ) ) {
	$tabnow = 'week';
	$_GET['tab'] = $tabnow;
}
$wixbu_to = date( 'Y-m-d' );

Wixbu_Instructors_Public::second_level_tabs( [
	'?tab=year'  => __( 'This year', 'wixbu' ),
	'?tab=month' => __( 'This month', 'wixbu' ),
	'?tab=week'  => __( 'This week', 'wixbu' ),
] );

?>

<div id="wixbu-earnings-report">
	<div class="wer-tab-content" id="wer-tab-<?php echo $tabnow; ?>">
		<?php
		include "earning_report-$tabnow.php";
		?>
	</div>
</div>
