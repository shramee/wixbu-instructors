<?php

class Wixbu_Payout {

	public $id;

	public $status;

	public $user_id;

	public $amount;

	public $created;

	public $orders;

	/** @var array $labels */
	public $labels = [];

	public function __construct( $object ) {
		$obj_ar = (array) $object;
		foreach ( $obj_ar as $k => $value ) {
			$this->$k = $value;
		}

		$this->setup_labels();
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
						'key'     => 'wixbu_payout_pending',
						'compare' => 'EXISTS',
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

	private function setup_labels() {
		$this->labels = [
			'students_paid' => __( 'TOTAL PAID', 'wixbu' ),
			'tax'           => __( 'Tax', 'wixbu' ),
			'net_income'    => __( 'Net Income', 'wixbu' ),
			'wixbu_fees'    => sprintf( __( 'Wixbu Fees %s%%', 'wixbu' ), WIXBU_COMMISSION ),
			'your_earning'  => __( 'Total earnings', 'wixbu' ),
		];

		if ( $this->id === 'upcoming' ) {
//			$this->labels['your_earning']  = 'Balance';
		}

	}
}