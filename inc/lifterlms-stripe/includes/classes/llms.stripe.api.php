<?php
/**
 * Handle output on the LifterLMS Settings Gateways tab
 * @since  2.0.0
 * @version  4.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;
class LLMS_Stripe_API {

	private $result = null;
	private $error_message = null;
	private $error_object = null;
	private $error_type = null;

	/**
	 * Construct an API call, parameters are passed to private `call()` function
	 * @param  string $resource  url endpoint or resource to make a request to
	 * @param  array  $data      array of data to pass in the body of the request
	 * @param  string $method    method of request (POST, GET, DELETE, PUT, etc...)
	 * @return void
	 * @since  2.0.0
	 * @version  2.0.0
	 */
	public function __construct( $resource, $data, $method ) {

		$this->call( $resource, $data, $method );

	}


	/**
	 * Make an API call to stripe
	 * @param  string $resource  url endpoint or resource to make a request to
	 * @param  array  $data      array of data to pass in the body of the request
	 * @param  string $method    method of request (POST, GET, DELETE, PUT, etc...)
	 * @return mixed
	 * @since  2.0.0
	 * @version  2.0.0
	 */
	private function call( $resource, $data, $method ) {

		$gateway = LLMS()->payment_gateways()->get_gateway_by_id( 'stripe' );

		$headers = array(
			'Authorization'  => 'Basic ' . base64_encode( $gateway->get_secret_key() . ':' ),
			'Stripe-Version' => apply_filters( 'llms_stripe_api_version', '2017-06-05' ),
		);

		if ( in_array( $resource, [ 'charges', 'subscriptions' ] ) ) {
			$connect_success = $this->connect_fields( $resource, $headers, $data );
			if ( ! $connect_success ) {
				return $connect_success;
			}
		}

		// attempt to call the API
		$response = wp_safe_remote_post(
			'https://api.stripe.com/v1/' . $resource,
			array(
				'body'       => $data,
				'headers'    => $headers,
				'method'     => $method,
				'timeout'    => 70,
				'user-agent' => 'LifterLMS ' . LLMS_VERSION
			)
		);

		error_log( var_export( [
			$resource,
			'data' => $data,
			'headers' => $headers,
			'response' => json_decode( $response['body'], 1 ),
		], 1 ) );

		$result = $this->parse_response( $response );

		if ( $result ) {
			return $this->result = $result;
		} else {
			return $result;
		}
	}

	/**
	 * @param string $resource Request headers
	 * @param array $headers Request headers
	 * @param array $data Request data
	 * @return bool Success
	 */
	private function connect_fields( $resource, &$headers, &$data ) {
			$plan = new LLMS_Access_Plan( $_GET['plan'] );
			if ( $plan ) {
				$product     = $plan->get_product();
				if ( $product && $product->author ) {
					$stripe_account = get_user_meta( $product->author, 'stripe_user_id', 1 );
					if ( $stripe_account ) {
						if ( ! empty( $data['amount'] ) ) {
							$lms_charge = .4;
							$taxes      = Taxes_LLMS_Quaderno::get_tax();
							if ( ! $taxes['error'] && $taxes['data']['name'] ) {
								$lms_charge += $taxes['data']['rate'] / 100;
							}
							if ( $resource == 'charges' ) {
								$data['application_fee'] = $data['amount'] * $lms_charge;
							} else {
								$data['application_fee_percent'] = $lms_charge;
							}
						}
						$headers['Stripe-Account'] = $stripe_account;
						$data['source'] = $this->get_instructor_customer_token( $headers, $data );
						unset( $data['customer'] );
					}
				}
			}
			if ( empty( $headers['Stripe-Account'] ) ) {
				return $this->set_error( __( 'Sorry, Payment method not added by instructor.', 'wixbu' ), 'no_payment_method', new stdClass() );
			}
		return true;
	}

	private function get_instructor_customer_token( $headers, $original_data ) {
		$data = [
			'customer' => $original_data['customer'],
			'card' => $original_data['source'],
		];
//
//		$headers = [
//			'Stripe-Account' => $headers['Stripe-Account']
//		];

		$response = wp_safe_remote_post(
			'https://api.stripe.com/v1/tokens',
			array(
				'body'       => $data,
				'headers'    => $headers,
			)
		);

		$result = $this->parse_response( $response );

		if ( $result ) {
			return $result->id;
		}
	}

	/**
	 * @param $response
	 *
	 * @return bool
	 */
	private function parse_response( $response ) {

		// connection error
		if ( is_wp_error( $response ) ) {
			return $this->set_error( __( 'There was a problem connecting to the payment gateway.', 'lifterlms-stripe' ), 'gateway_connection', $response );
		}

		// empty body
		if ( empty( $response['body'] ) ) {
			return $this->set_error( __( 'Empty Response.', 'lifterlms-stripe' ), 'empty_response', $response );
		}

		// parse the response body
		$parsed = json_decode( $response['body'] );

		// Handle response
		if ( ! empty( $parsed->error ) ) {
			return $this->set_error( $parsed->error->message, $parsed->error->type, $response );
		} else {
			return $parsed;
		}

	}

	/**
	 * Retrive the private "error_message" variable
	 * @return string
	 * @since  2.0.0
	 * @version  2.0.0
	 */
	public function get_error_message() {

		return $this->error_message;

	}

	/**
	 * Get the private "error_object" variable
	 * @return mixed
	 * @since  2.0.0
	 * @version  2.0.0
	 */
	public function get_error_object() {

		return $this->error_object;

	}


	/**
	 * Retrive the private "error_type" variable
	 * @return string
	 * @since  2.0.0
	 * @version  2.0.0
	 */
	public function get_error_type() {

		return $this->error_type;

	}

	/**
	 * Retrive the private "result" variable
	 * @return mixed
	 * @since  2.0.0
	 * @version  2.0.0
	 */
	public function get_result() {

		return $this->result;

	}

	/**
	 * Determine if the response is an error
	 * @return   boolean
	 * @since    4.0.0
	 * @version  4.0.0
	 */
	public function is_error() {

		return is_wp_error( $this->get_result() );

	}

	/**
	 * Set an Error
	 * Sets all error variables and sets the result as a WP_Error so the result can always be tested with `is_wp_error()`
	 *
	 * @param string $message  error message
	 * @param string $type     error code or type
	 * @param object $obj      full error object or api response
	 * @return bool
	 * @since  2.0.0
	 * @version  2.0.0
	 */
	private function set_error( $message, $type, $obj ) {

		$this->result = new WP_Error( $type, $message, $obj );
		$this->error_type = $type;
		$this->error_message = $message;
		$this->error_object = $obj;

		return false;
	}

}
