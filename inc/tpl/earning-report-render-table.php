<div class="wer-table-wrap">
	<table>
		<?php
		$row_i = 0;
		/** @var array $table_data */
		$cell_tag = 'th';
		foreach ( $table_data as $datum ) {
			?>
			<tr>
				<?php
				foreach ( $datum as $cell ) {
					if ( is_array( $cell ) ) {
						$cell = llms_price( array_sum( $cell ) );
					}
					echo "<$cell_tag>$cell</$cell_tag>";
				} ?>
			</tr>
			<?php
			$cell_tag = 'td';
			$row_i++;
		}
		?>
	</table>
</div>

<?php

