<?php

$payout = Wixbu_Instructors::instance()->get_payout( 259 );
$orders = [];
$uid    = $payout->user_id;
$user   = get_user_by( 'id', $uid );

$total_paid = $total_income = $total_tax = 0;

$address_meta = [
	'first_name',
	'last_name',
	'llms_billing_address_1',
	'llms_billing_address_2',
	'llms_billing_city',
	'llms_billing_state',
	'llms_billing_zip',
	'llms_billing_country',
	'llms_phone',
];
$user_fields  = [
	'user_email',
];

$formatting_callbacks = [
	'llms_billing_country' => function ( $val ) {
		$countries = get_lifterlms_countries();
		if ( $countries[ $val ] ) {
			$val = $countries[ $val ];
		}

		return $val;
	},
];

$format = '
first_name last_name
llms_billing_address_1
llms_billing_address_2
llms_billing_city
llms_billing_state llms_billing_zip llms_billing_country
llms_phone user_email';

remove_filter( 'comments_clauses', array( 'LLMS_Comments', 'exclude_order_comments' ) );
?>

<div class="payout-details">
	<header>
		<div class="payout-id fw500 fr">
			Payout Number: <?php echo $payout->id; ?>
		</div>

		<a href="?" class="back fw500"><span class="fa-chevron-left fa"></span> Back</a>
	</header>

	<div class="wixbu-address fw500 fr">
		<br>
		<img src="http://wixbu.com/wp-content/uploads/2018/05/LOGO-300x72.png" alt="WIXBU"><br>
		Good Abbey SLNE B87921250<br>
		Calle San Juan de la Cruz 2,<br>
		Pozuelo de Alarcon. 28223 Espana<br>
	</div>
	<div class="instructor-address fw500">
		<?php
		foreach ( $address_meta as $key ) {
			$val = get_user_meta( $uid, $key, 1 );
			if ( isset( $formatting_callbacks[ $key ] ) && is_callable( $formatting_callbacks[ $key ] ) ) {
				$val = call_user_func( $formatting_callbacks[ $key ], $val );
			}
			$format = str_replace( $key, $val, $format );
		}
		foreach ( $user_fields as $key ) {
			$val = $user->$key;
			if ( isset( $formatting_callbacks[ $key ] ) && is_callable( $formatting_callbacks[ $key ] ) ) {
				$val = call_user_func( $formatting_callbacks[ $key ], $val );
			}
			$format = str_replace( $key, $val, $format );
		}
		echo str_replace( [ "\n\n\n\n", "\n\n\n", "\n\n", "\n" ], '<br>', $format );
		?>

	</div>

	<div class="table-wrap">
		<table id="payout-orders" class="no-border">
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
			$q = new WP_Query( array(
				'order'          => 'DESC',
				'orderby'        => 'date',
				'post__in'       => explode( ',', $payout->orders ),
				'posts_per_page' => 999,
				'post_status'    => 'any',
				'post_type'      => 'llms_order',
			) );

			if ( $q->have_posts() ) {

				foreach ( $q->posts as $post ) {
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
							<?php echo $order->get_status_name(); ?>
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
							<a href="?payout=<?php echo $payout->id ?>&order=<?php echo $order->id ?>">
								<?php echo $order->id; ?>
							</a>
						</td>
					</tr>
					<?php
				}
			}

			?>
		</table>
	</div>

	<table class="no-border sum-box">
		<tr>
			<td class="fw500">TOTAL PAID</td>
			<td class="fw500"><?php echo llms_price( $total_paid ) ?></td>
		</tr>
		<tr>
			<td>Tax</td>
			<td><?php echo llms_price( $total_tax ) ?></td>
		</tr>
		<tr>
			<td>Net Income</td>
			<td><?php echo llms_price( $total_income ) ?></td>
		</tr>
		<tr>
			<td>Wixbu Fees <?php echo WIXBU_COMMISSION ?>%</td>
			<td><?php echo llms_price( $total_income * WIXBU_COMMISSION / 100 ) ?></td>
		</tr>
		<tr>
			<td class="fw500">TOTAL EARNINGS</td>
			<td class="fw500"><?php echo llms_price( $total_income * INSTRUCTOR_SHARE / 100 ) ?></td>
		</tr>
	</table>
</div>

<div class="payout-date">
	<?php _e( 'Payment processed by Stripe', 'wixbu' ) ?><br>
	<?php printf( __( 'Date %s', 'wixbu' ), date( 'M d, Y', $payout->created ) ); ?>
</div>

<?php
if ( isset( $_GET['order'] ) && $order = $orders[ $_GET['order'] ] ) {
	/** @var LLMS_Order $order */

	/** @var WP_Comment $comment */
	$comment = $order->get_notes( 1, 1 )[0];
	?>
	<div class="wixbu-popup-overlay"></div>
	<div class="wixbu-popup">
		<a href="?payout=<?php echo $_GET['payout']; ?>" class="back fw500"><span class="fa-chevron-left fa"></span>
			Back</a>
		<table>
			<tr>
				<td colspan="3">
					<?php printf( __( 'Date %s', 'wixbu' ), "\t" . $order->get_start_date( 'd/m/y' ) ); ?>
				</td>
				<td colspan="3">
					<?php printf( __( 'Ref ID %s', 'wixbu' ), "\t" . $order->id ); ?>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<?php printf( __( 'Type: %s', 'wixbu' ), ucfirst( $order->get( 'product_type' ) ) ); ?>
				</td>
				<td colspan="3">
					<?php printf( __( 'User: %s', 'wixbu' ), $order->get_customer_name() ); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<?php echo $comment->comment_content; ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php _e( 'Total Payment', 'wixbu' ); ?>
				</td>
				<td colspan="2">
					<?php _e( 'Net Income', 'wixbu' ); ?>
				</td>
				<td colspan="2">
					<?php _e( 'Your earning', 'wixbu' ); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php echo llms_price( $order->total ); ?>
				</td>
				<td colspan="2">
					<?php echo llms_price( $order->net_amount ); ?>
				</td>
				<td colspan="2">
					<?php echo llms_price( $order->net_amount * INSTRUCTOR_SHARE / 100 ); ?>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

add_filter( 'comments_clauses', array( 'LLMS_Comments', 'exclude_order_comments' ) );
