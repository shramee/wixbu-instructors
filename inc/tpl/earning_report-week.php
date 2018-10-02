<?php
$wixbu_from = date( 'Y-m-d', strtotime( 'first day of this month' ) );

$from = str_replace( '-', '', $wixbu_from );
$to = str_replace( '-', '', $wixbu_to );
$income_data = Wixbu_Instructors::query_umeta_table(
	"`meta_key` BETWEEN 'subs-{$from}0' AND 'subs-{$to}z' OR " .
	"`meta_key` BETWEEN 'sale-{$from}0' AND 'sale-{$to}z'"
);

Wixbu_Instructors_Public::output_chart( [
	[
		'numbers' => [ 250, 120, -50 ],
		'label'   => __( 'MON-TUE', 'wixbu-instructors' )
	],
	[
		'numbers' => [ 290, 100, -25 ],
		'label'   => __( 'WED-THU', 'wixbu-instructors' )
	],
	[
		'numbers' => [ 200, 160, 0 ],
		'label'   => __( 'FRI-SAT', 'wixbu-instructors' )
	],
	[
		'numbers' => [ 320, 250, -70 ],
		'label'   => __( 'SUN', 'wixbu-instructors' )
	],
] );

?>
