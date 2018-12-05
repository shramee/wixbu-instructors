<?php

class Wixbu_Payout {

	public $id;

	public $status;

	public $user_id;

	public $amount;

	public $created;

	public $orders;

	public $gross_amount;

	public $total_payment;

	public $platform_fees;

	public function __construct( $object ) {
		$obj_ar = (array) $object;
		foreach ( $obj_ar as $k => $value ) {
			$this->$k = $value;
		}
	}

	public function orders_query_args() {
		if ( $this->id === 'upcoming' ) {
			return array(
				'order'          => 'DESC',
				'orderby'        => 'date',
				'date_query' => [
					'before' => date( 'Y-m-d', strtotime( '- 30 days' ) ),
				],
				'meta_query'     => array(
					array(
						'key'     => 'wixbu_paid_out',
						'compare' => 'NOT EXISTS',
					),
				),
				'posts_per_page' => 999,
				'post_status'    => 'any',
				'post_type'      => 'llms_order',
			);
		}
		$orders = explode( ',', $this->orders );
		return array(
			'order'          => 'DESC',
			'orderby'        => 'date',
			'post__in'       => $orders,
			'posts_per_page' => 999,
			'post_status'    => 'any',
			'post_type'      => 'llms_order',
		);
	}

	public function orders_query( $args = [] ) {
		$args = wp_parse_args( $args, $this->orders_query_args() );

		return new WP_Query( $args );
	}
}