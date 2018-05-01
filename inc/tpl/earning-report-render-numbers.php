<div class="wer-numbers">
	<?php
	foreach ( $numbers_data as $label => $datum ) {
		$help = isset( $datum['help'] ) ? "<div class='info'>?<div class='info-text wer-box'>$datum[help]</div></div>": '';
		$value = isset( $datum['value'] ) ? $datum['value']: $datum;
		if ( is_array( $value ) ) {
			$value = llms_price( array_sum( $value ) );
		}
		?>
		<div class="llms-form-field llms-cols-3 elementor-align-center">
			<div class="futura-li">
				<?php echo $label ?>
				<?php echo $help ?>
			</div>
			<hr>
			<div class="futura"><?php echo $value ?></div>
		</div>
		<?php
	}
	?>
</div>