<?php
remove_filter( 'comments_clauses', array( 'LLMS_Comments', 'exclude_order_comments' ) );

if ( isset( $_GET['order'] ) && $order = $orders[ $_GET['order'] ] ) {
	/** @var LLMS_Order $order */

	/** @var WP_Comment $comment */
	$comment = $order->get_notes( 1, 1 )[0];
	$back_qry = isset( $_GET['payout'] ) ? "payout=$_GET[payout]" : '';
	?>
	<div class="wixbu-popup-overlay"></div>
	<div class="wixbu-popup">
		<a href="?<?php echo $back_qry; ?>" class="back fw500"><span class="fa-chevron-left fa"></span>
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
			<?php
			if ( $comment ) {
				?>
				<tr>
					<td colspan="6">
						<?php echo $comment->comment_content; ?>
					</td>
				</tr>
				<?php
			}
			?>
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
