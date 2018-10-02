<div class="prcs">
	<div class="prcs-t"><?php echo llms_price( $d['sum'], [ 'decimals' => 0 ] ); ?></div>
	<div class="prcs-bw">
		<div class="prcs-bs">
			<?php
			foreach ( $d['numbers'] as $num) {
				?>
				<div class="prcs-b" style="height:<?php echo 100 * abs( $num ) / $chart['max'] ?>%;">
					<?php echo llms_price( $num, [ 'decimals' => 0 ] ) ?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<div class="prcs-li"></div>
	<div class="prcs-lb"><?php echo $d['label']; ?></div>
</div>
