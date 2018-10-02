<?php

$base = $chart['max'];
$max = $base * 3 / 2;
$step = ceil( $base / 400 ) * 100;

if ( $base < 300 ) {
	$step = 50;
}

if ( 800 < $step )
?>
<div class="prcnlw">
	<div class="prcnl">
		<?php
		for ( $i = $step; $i < $max; $i += $step ) {
			?>
			<div class="prcnll" style="height:<?php echo 160 * $i / $base ?>px;">
				<?php echo llms_price( $i, [ 'decimals' => 0 ] ) ?>
			</div>
			<?php
		}
		?>
	</div>
	<div class="prcs-li"></div>
</div>
