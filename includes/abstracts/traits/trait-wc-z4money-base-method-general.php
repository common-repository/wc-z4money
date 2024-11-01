<?php if ( ! defined( 'ABSPATH' ) ) die;

if ( ! class_exists( 'Wc_Z4Money_Base_Method_General' ) ) {

/**
 *
 * @link       https://github.com/santanamic/wc-z4money
 * @since      1.0.0
 *
 * @package    Wc_Z4Money
 * @subpackage Wc_Z4Money/includes/abstracts/traits
 * @author     AQUARELA - WILLIAN SANTANA <williansantanamic@gmail.com>
 */
    trait Wc_Z4Money_Base_Method_General
    {
		
		/**
		 * Get order data.
		 *
		 * @param  int $order_id
		 * @param  object $order
		 * @return array
		 */
		public function get_order_data( $order_id, $order ) 
		{
			$currency       = strtolower( get_woocommerce_currency() );
			$order_desc     = sprintf( __( '%s - Order %s', 'wc-z4money' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );
			$order_total    = $order->order_total;

			if ( strcasecmp( $currency, 'BRL' ) != 0 ) {
				throw new Exception( __( 'Purchase can only be made in Brazilian currency', 'wc-z4money' ) );
			}
			
			return [
				'id'         => $order_id,
				'title'      => $order_desc,
				'unicid'     => $order_id . '-' . time(),
				'currency'   => $currency,
				'total'      => $order_total,
				'return_url' => $order->get_checkout_order_received_url(),
				'hash'       => $this->get_wc_session_cookie_hash()
			];
		}
	
		/**
		 * Get customer data.
		 *
		 * @param  object $order
		 * @return array
		 */
		public function get_customer_data( $order )
		{
			$cpf       = Wc_Z4Money_Helper::stringSerializer( $order->get_meta('_billing_cpf') );
			$cnpj      = Wc_Z4Money_Helper::stringSerializer( $order->get_meta('_billing_cnpj') );
			$phone     = Wc_Z4Money_Helper::stringSerializer( $order->get_billing_phone() );
			$birthdate = Wc_Z4Money_Helper::usaDate( $order->get_meta('_billing_birthdate') );
			
			$customer = [
				'email'              => $order->get_billing_email(),
				'first_name'         => $order->get_billing_first_name(),
				'last_name'          => $order->get_billing_last_name(),
				'full_name'          => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				'company_name'       => $order->get_billing_company(),
				'person_type'        => $order->get_meta('_billing_persontype'),
				'birthdate'          => $birthdate,
				'cpf'                => $cpf,
				'cnpj'               => $cnpj,
				'phone'              => $phone,
				'address_street'     => $order->get_billing_address_1(),
				'address_number'     => $order->get_meta('_billing_number'),
				'address_complement' => $order->get_billing_address_2(),
				'address_district'   => $order->get_meta('_billing_neighborhood'),
				'address_zipcode'    => $order->get_billing_postcode(),
				'address_city'       => $order->get_billing_city(),
				'address_state'      => $order->get_billing_state(),
				'address_country'    => $order->get_billing_country()
			];
			
			switch ( $customer['person_type'] ) {            
				case '1':            
					if( empty( $customer['cpf'] ) ) {
						throw new Exception( __( 'CPF Fields Are blank', 'wc-z4money' ) );
					}
				
				break;
				case '2':            
					if( empty( $customer['cnpj'] ) ) {
						throw new Exception( __( 'CNPJ Fields Are blank', 'wc-z4money' ) );
					}
				
				break;
				default:            
					throw new Exception( __( 'Error identifying a person type', 'wc-z4money' ) );
				
				break;
			}
			
			return $customer;
		}
		
		/**
		 * Get cart cookie hash.
		 *
		 * @return   string|bool
		 */
        public function get_wc_session_cookie_hash() 
        {
			global $woocommerce;			
			
			list( 
				$customer_id, 
				$session_expiration, 
				$session_expiring, 
				$cookie_hash 
			) = $woocommerce->session->get_session_cookie();
			
			return $cookie_hash ? $cookie_hash : false;
			
		}

		/**
		 * Update order status in Woocommerce.
		 *
		 * @return void
		 */
		public function update_order_status( $data ) 
		{
			$order = wc_get_order( $data['id'] );

			if( ! isset( $data['update'] ) &&  ! empty( $data['update'] ) ) {
				return;
			}

			if( ! isset( $data['note'] ) ) {
				$data['note'] = '';
			}
			

			switch( $data['update'] ) {
		
				case 'pending':
					$order->update_status( 'pending' );
					
				break;
				
				case 'processing':
					$order->update_status( 'processing' );
					if( isset( $data['payment_id'] ) ) $order->add_meta_data('Z4MONEY_ID', $data['payment_id'], true);
					if( isset( $data['url_boleto'] ) ) $order->add_meta_data('Z4MONEY_URL_BOLETO', $data['url_boleto'], true);
					WC()->cart->empty_cart();
				
				break;	
				
				case 'on-hold':
					$order->update_status( 'on-hold' );
					if( isset( $data['payment_id'] ) ) $order->add_meta_data('Z4MONEY_ID', $data['payment_id'], true);
					if( isset( $data['url_boleto'] ) ) $order->add_meta_data('Z4MONEY_URL_BOLETO', $data['url_boleto'], true);
					WC()->cart->empty_cart();

				break;	
				
				case 'completed':
					$order->update_status( 'completed' );

				break;	
				
				case 'cancelled':
					$order->update_status( 'cancelled' );

				break;	
				
				case 'refunded':
					$order->update_status( 'refunded' );

				break;	
				
				case 'failed':
					$order->update_status( 'failed' );

				break;
				
			}
			
			$order->save();
		}

		/**
		 * Get payment methods.
		 *
		 * @return array
		 */
		public function get_card_brand_name() 
		{
			return array(
				// Credit.
				'visa'         => __( 'Visa', 'wc-z4money' ),
				'mastercard'   => __( 'MasterCard', 'wc-z4money' ),
				'diners'       => __( 'Diners', 'wc-z4money' ),
				'discover'     => __( 'Discover', 'wc-z4money' ),
				'elo'          => __( 'Elo', 'wc-z4money' ),
				'amex'         => __( 'American Express', 'wc-z4money' ),
				'jcb'          => __( 'JCB', 'wc-z4money' ),
				'aura'         => __( 'Aura', 'wc-z4money' ),

				// Debit
				'visaelectron' => __( 'Visa Electron', 'wc-z4money' ),
				'maestro'      => __( 'Maestro', 'wc-z4money' ),
			);
		}
		
		/**
		 * Get payment method name.
		 *
		 * @param  string $slug Payment method slug.
		 * @return string       Payment method name.
		 */
		public function get_brand_name_by_slug( $slug )
		{
			$brands = $this->get_card_brand_name();

			if ( isset( $brands[ $slug ] ) ) {
				return $brands[ $slug ];
			}

			return $slug;
		}

		/**
		 * Get cardband by number
		 *
		 * @param  string $number The card number.
		 */
		public function get_card_brand_by_number( $number ) 
		{
			$number = preg_replace( '([^0-9])', '', $number );
			$brand  = '';

			$supported_brands = array(
				'visa'       => '/^4\d{12}(\d{3})?$/',
				'mastercard' => '/^(5[1-5]\d{4}|677189)\d{10}$/',
				'diners'     => '/^3(0[0-5]|[68]\d)\d{11}$/',
				'discover'   => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
				'elo'        => '/^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})$/',
				'amex'       => '/^3[47]\d{13}$/',
				'jcb'        => '/^(?:2131|1800|35\d{3})\d{11}$/',
				'aura'       => '/^(5078\d{2})(\d{2})(\d{11})$/',
				'hipercard'  => '/^(606282\d{10}(\d{3})?)|(3841\d{15})$/',
			);

			foreach ( $supported_brands as $key => $value ) {
				if ( preg_match( $value, $number ) ) {
					$brand = $key;
					break;
				}
			}

			return $brand;
		}

		/**
		 * Get available methods options.
		 *
		 * @return array
		 */
		public function get_available_brands() 
		{
			$methods = array();

			foreach ( $this->methods as $method ) {
				$methods[ $method ] = $this->get_brand_name_by_slug( $method );
			}

			return $methods;
		}
		
		/**
		 * Get valid value.
		 * Prevents users from making shit!
		 *
		 * @param  string|int|float $value
		 * @return int|float
		 */
		public function get_valid_value( $value ) 
		{
			$value = str_replace( '%', '', $value );
			$value = str_replace( ',', '.', $value );

			return $value;
		}
		
	}        
}