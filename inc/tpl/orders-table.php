<?php

$total_paid = $total_income = $total_tax = 0;
$orders = [];

if ( empty( $orders_query ) ) {
	return;
}

$wixbu_order_status_labels = array(
	'llms-active'    => __( 'Active Membership', 'wixbu' ),
	'llms-cancelled' => __( 'Cancelled', 'wixbu' ),
	'llms-completed' => __( 'Single purchase', 'wixbu' ),
	'llms-expired'   => __( 'Expired Membership', 'wixbu' ),
	'llms-failed'    => __( 'Failed', 'wixbu' ),
	'llms-on-hold'   => __( 'On Hold', 'wixbu' ),
	'llms-pending'   => __( 'Pending', 'wixbu' ),
	'llms-refunded'  => __( 'Refunded', 'wixbu' ),
);

?>
<div class="table-wrap">
	<table class="wixbu-orders-table">
		<tr>
			<th>Date</th>
			<th>Type</th>
			<th>User</th>
			<th>Description</th>
			<th>Total Payment</th>
			<th>Net income</th>
			<th>Your Earning</th>
			<th>Ref ID</th>
		</tr>

		<?php
		if ( $orders_query->have_posts() ) {

			foreach ( $orders_query->posts as $post ) {
				$order = new LLMS_Order( $post );
				$orders[ $order->id ] = $order;

				$amount = $order->get_transaction_total();

				$tax = Taxes_LLMS_Quaderno::get_tax( $order->billing_country );

				if ( empty( $tax['data']['rate'] ) ) {
					$tax['data']['rate'] = 0;
				}

				$net_amount = $amount * 100 / ( 100 + $tax['data']['rate'] );

				$order->total = $amount;
				$order->net_amount = $net_amount;


				$total_paid += $amount;
				$total_income += $net_amount;
				$total_tax += $amount * $tax['data']['rate'] / ( 100 + $tax['data']['rate'] );
				?>
				<tr>
					<td>
						<?php echo $order->get_start_date( 'd/m/y' ); ?>
					</td>
					<td>
						<?php echo $wixbu_order_status_labels[ $order->get( 'status' ) ] ?>
					</td>
					<td>
						<?php echo $order->get_customer_name(); ?>
					</td>
					<td>
						<?php echo $order->get( 'product_title' ); ?>
						<small>(<?php echo ucfirst( $order->get( 'product_type' ) ); ?>)</small>
					</td>
					<td>
						<?php echo llms_price( $amount ); ?>
					</td>
					<td>
						<?php echo llms_price( $net_amount ); ?>
					</td>
					<td>
						<?php echo llms_price( $net_amount * INSTRUCTOR_SHARE / 100 ); ?>
					</td>
					<td>
						<a href="?order=<?php echo $order->id ?>">
							<?php echo $order->id; ?>
						</a>
					</td>
				</tr>
				<?php
			}
		} else {
			?>
			<tr>
				<td colspan="8"><?php _e( 'Sorry, No matching orders found.' ) ?></td>
			</tr>
			<?php
		}

		?>
	</table>
</div>