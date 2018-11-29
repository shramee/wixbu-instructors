<?php
wp_enqueue_style( WXBIN . '-css' );

$products = [];
global $wpdb;
/** @var wpdb $wpdb */
$uid = get_current_user_id();

$authored_posts_rows = $wpdb->get_results(
	'SELECT ID FROM `wordpress52331_wp_posts` ' .
	"WHERE post_type IN ( 'course', 'llms_course', 'llms_membership' ) AND " .
	"post_author = $uid" );

foreach ( $authored_posts_rows as $row ) {
	$products[] = $row->ID;
}

$page = isset( $_GET['page'] ) ? $_GET['page'] : 1;

$_GET = wp_parse_args( $_GET, [
	'orders-from'   => date( 'Y-m-d', strtotime( '- 1 month' ) ),
	'orders-to'     => date( 'Y-m-d' ),
	'orders-status' => 'any',
] );

$orders_query = new WP_Query( array(
	'order'          => 'DESC',
	'orderby'        => 'date',
	'posts_per_page' => 999,
	'paged'          => $page,
	'meta_query'     => array(
		array(
			'key'     => '_llms_product_id',
			'value'   => $products,
			'compare' => 'IN',
		),
	),
	'post_status'    => $_GET['orders-status'],
	'date_query' => [
		'after' =>  $_GET['orders-from'],
		'before' => $_GET['orders-to'],
	],
	'post_type'      => 'llms_order',
) );

include 'orders-table.php';
?>
	<form class="order-filters">
		<table class="no-border">
			<tr class="small">
				<td><?php _e( 'From', 'wixbu' ) ?></td>
				<td><?php _e( 'To', 'wixbu' ) ?></td>
				<td><?php _e( 'Type', 'wixbu' ) ?></td>
				<td></td>
			</tr>
			<tr>
				<td>
					<input name="orders-from" type="date" placeholder="From"
								 value="<?php echo $_GET['orders-from'] ?>">
				</td>
				<td>
					<input name="orders-to" type="date" placeholder="To"
								 value="<?php echo $_GET['orders-to'] ?>">
				</td>
				<td>
					<select name="orders-status">
						<?php
						$options        = array(
							'any'            => __( 'All transactions', 'wixbu' ),
							'llms-active'    => __( 'Active Memberships', 'wixbu' ),
							'llms-completed' => __( 'Single purchases', 'wixbu' ),
//				'llms-cancelled' => __( 'Cancelled', 'wixbu' ),
//				'llms-expired'   => __( 'Expired Membership', 'wixbu' ),
							'llms-failed'    => __( 'Failed', 'wixbu' ),
//				'llms-on-hold'   => __( 'On Hold', 'wixbu' ),
//				'llms-pending'   => __( 'Pending', 'wixbu' ),
							'llms-refunded'  => __( 'Refunded', 'wixbu' ),
						);
						$slected_status = $_GET['orders-status'];
						foreach ( $options as $k => $label ) {
							$selected = selected( $slected_status, $k );
							echo "<option value='$k' $selected>$label</option>";
						}
						?>
					</select>
				</td>
				<td>
					<input type="submit" value="<?php _e( 'Go', 'wixbu' ) ?>">
				</td>
			</tr>
		</table>
	</form>

	<table class="no-border sum-box">
		<tr>
			<td class="fw500">
				<?php printf( __( 'TOTAL PAID %s', 'wixbu' ), llms_price( $total_paid ) ); ?>
			</td>
		</tr>
	</table>

<?php
include 'order-popup.php';