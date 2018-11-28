<?php
wp_enqueue_style( WXBIN . '-css' );

if ( isset( $_GET['payout'] ) ) {
	include 'payouts_history-details.php';

	return;
}
$payouts          = Wixbu_Instructors::instance()->get_payouts();
$next_payout_date = Wixbu_Instructors::instance()->next_payout_date();
?>
<div class="wixbu-dash-content-no-padding">
	<table class="payouts-table no-border">
		<?php
		foreach ( $payouts as $payout ) {
			?>
			<tr>
				<td>
					<h5>Invoice</h5>
					#<?php echo $payout->id ?>
					<span class="label"><?php echo $payout->status ?></span>
				</td>
				<td>
					<h5>Date</h5>
					<?php echo date( 'M d, Y', $payout->created ) ?>
				</td>
				<td>
					<h5>Next Payout</h5>
					<?php echo date( 'M d, Y', $next_payout_date ) ?>
				</td>
				<td>
					<a class="button" href="?payout=<?php echo $payout->id ?>">View</a>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
</div>