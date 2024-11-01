<?php if ( ! defined( 'ABSPATH' ) ) die;

if ( ! class_exists( 'Wc_Z4Money_Gateway_Method_CreditCard' ) ) {

/**
 * CreditCard payment method.
 *
 * @link       https://github.com/santanamic/wc-z4money
 * @since      1.0.0
 *
 * @package    wc-z4money
 * @subpackage wc-z4money/includes/gateway/methods/
 * @author     AQUARELA - WILLIAN SANTANA <williansantanamic@gmail.com>
 */
    class Wc_Z4Money_Gateway_Method_CreditCard extends Wc_Z4Money_Gateway 
    {
		const METHOD_PATH             = __DIR__;
		const REQUIRE_FILES           = ['includes/functions.php'];
		
		const CARD_NUMBER             = 'z4money_creditcard_number';
		const CARD_NAME               = 'z4money_creditcard_holder_name';
		const CARD_EXPIRY             = 'z4money_creditcard_expiry';
		const CARD_CVC                = 'z4money_creditcard_cvc';
		const CARD_INSTALLMENTS       = 'z4money_creditcard_installments';
		const FILTER_MAX_INSTALLMENTS = 'wc_z4money_max_installments';
				
		/**
		 * Start payment method.
		 *
		 * @return   void
		 */
		public function __construct() 
        {
			$this->id                   = 'wc_z4money_creditcard';
			$this->method_title         = __( 'Z4Money', 'wc-z4money' );
			$this->method_description   = __( 'Receiv by Credit Card using Z4Money.', 'wc-z4money' );
			$this->token_secret         = $this->get_option( 'token_secret' );
			$this->token_secret_sandbox = $this->get_option( 'token_secret_sandbox' );
			$this->is_testmode          = $this->get_option( 'testmode' );
			$this->methods              = $this->get_option( 'methods' );
			$this->smallest_installment = $this->get_option( 'smallest_installment' );
			$this->interest_rate        = $this->get_option( 'interest_rate' );
			$this->installments         = $this->get_option( 'installments' );
			$this->interest             = $this->get_option( 'interest' );
			$this->installment_type     = $this->get_option( 'installment_type' );
			$this->debug                = $this->get_option( 'debug' );
			//$this->icon                 = plugins_url( 'assets/public/img/logo_horizontal_min.png', __FILE__ );

			parent::init_gateway();
			
			//$this->refunded_payment(466);
        }

		/**
		 * Set gateway forms fields ( Plugin admin options ).
		 *
		 * @return   void
		 */
        public function init_form_fields()
        {
			$fields = array(
				'enabled' => array(
					'title'       => __( 'Enable/Disable', 'wc-z4money' ),
					'label'       => __( 'Check to enable this form of payment.', 'wc-z4money' ),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				  ),
				'title' => array(
					'title'       => __( 'Checkout Title', 'wc-z4money' ),
					'type'        => 'text',
					'description' => __( 'This controls the title the user sees during checkout.', 'wc-z4money' ),
					'default'     => 'Credit Card',
					'desc_tip'    => true,
				  ),
				 'description' => array(
					'title'       => __( 'Checkout Description', 'wc-z4money' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description the user sees during checkout.', 'wc-z4money' ),
					'desc_tip'    => true,
					'default'     => __( 'Pay by Credit Card', 'wc-z4money' ), 
				  ),
				 'environment' => array(
					'title'       => __( 'Integration Settings', 'wc-z4money' ),
					'type'        => 'title',
					'description' =>  __( 'Select active environment for API', 'wc-z4money' ),
				),
				 'testmode' => array(
					'title'          => __( 'Sandbox Environment', 'wc-z4money' ),
					'type'          => 'checkbox',
					'label'          => __( 'Enable the Z4Money Testing', 'wc-z4money' ),
					'description' => __( 'Z4Money Sandbox can be used to test the payments', 'wc-z4money' ),
					'desc_tip'    => true,
					'default'     => 'no',
				),
				'token_secret' => array(
					'title'       => __( 'Toekn Secret', 'wc-z4money' ),
					'type'        => 'text',
					'description' => __( 'Z4Money Toekn Secret', 'wc-z4money' ),
					'default'     => '',
					'desc_tip'    => true,
				  ),
				'token_secret_sandbox' => array(
					'title'       => __( 'Token Secret for Sandbox', 'wc-z4money' ),
					'type'        => 'text',
					'description' => __( 'Z4Money Token Secret for Sandbox', 'wc-z4money' ),
					'default'     => '',
					'desc_tip'    => true,
				  ),
				'payment_settings' => array(
					'title'       => __( 'Payment Settings', 'wc-z4money' ),
					'type'        => 'title',
					'description' => __( 'Customize payment options', 'wc-z4money' ),
				),
				'smallest_installment' => array(
					'title'       => __( 'Smallest Installment', 'wc-z4money' ),
					'type'        => 'text',
					'description' => __( 'Smallest value of each installment, cannot be less than 5.', 'wc-z4money' ),
					'desc_tip'    => true,
					'default'     => '5',
				),
				'installments' => array(
					'title'       => __( 'Installment Within', 'wc-z4money' ),
					'type'        => 'select',
					'description' => __( 'Maximum number of installments for orders in your store.', 'wc-z4money' ),
					'desc_tip'    => true,
					'class'       => 'wc-enhanced-select',
					'default'     => '12',
					'options'     => array(
						'1'  => '1x',
						'2'  => '2x',
						'3'  => '3x',
						'4'  => '4x',
						'5'  => '5x',
						'6'  => '6x',
						'7'  => '7x',
						'8'  => '8x',
						'9'  => '9x',
						'10' => '10x',
						'11' => '11x',
						'12' => '12x',
					),
				),
				'methods' => array(
					'title'       => __( 'Accepted Card Brands', 'wc-z4money' ),
					'type'        => 'multiselect',
					'description' => __( 'Select the card brands that will be accepted as payment. Press the Ctrl key to select more than one brand.', 'wc-z4money' ),
					'desc_tip'    => true,
					'class'       => 'wc-enhanced-select',
					'default'     => array( 'visa', 'mastercard', 'diners', 'discover', 'elo', 'amex', 'jcb', 'aura'  ),
					'options'     => array(
						'visa'       => __( 'Visa', 'wc-z4money' ),
						'mastercard' => __( 'MasterCard', 'wc-z4money' ),
						'diners'     => __( 'Diners', 'wc-z4money' ),
						'discover'   => __( 'Discover', 'wc-z4money' ),
						'elo'        => __( 'Elo', 'wc-z4money' ),
						'amex'       => __( 'American Express', 'wc-z4money' ),
						'jcb'        => __( 'JCB', 'wc-z4money' ),
						'aura'       => __( 'Aura', 'wc-z4money' ),
					),
				),
				'installments' => array(
					'title'       => __( 'Installment Within', 'wc-z4money' ),
					'type'        => 'select',
					'description' => __( 'Maximum number of installments for orders in your store.', 'wc-z4money' ),
					'desc_tip'    => true,
					'class'       => 'wc-enhanced-select',
					'default'     => '1',
					'options'     => array(
						'1'  => '1x',
						'2'  => '2x',
						'3'  => '3x',
						'4'  => '4x',
						'5'  => '5x',
						'6'  => '6x',
						'7'  => '7x',
						'8'  => '8x',
						'9'  => '9x',
						'10' => '10x',
						'11' => '11x',
						'12' => '12x',
					),
				),
				'installment_type' => array(
					'title'        => __( 'Installment Type', 'wc-z4money' ),
					'type'         => 'select',
					'description'  => __( 'Client adds interest installments on the order total.', 'wc-z4money' ),
					'desc_tip'     => true,
					'class'        => 'wc-enhanced-select',
					'default'      => 'store',
					'options'      => array(
						'client' => __( 'Client', 'wc-z4money' ),
						'store'  => __( 'Store', 'wc-z4money' ),
					),
				),
				'interest_rate' => array(
					'title'       => __( 'Interest Rate (%)', 'wc-z4money' ),
					'type'        => 'text',
					'description' => __( 'Percentage of interest that will be charged to the customer in the installment where there is interest rate to be charged.', 'wc-z4money' ),
					'desc_tip'    => true,
					'default'     => '2',
				),
				'interest' => array(
					'title'       => __( 'Charge Interest Since', 'wc-z4money' ),
					'type'        => 'select',
					'description' => __( 'Indicate from which installment should be charged interest.', 'wc-z4money' ),
					'desc_tip'    => true,
					'class'       => 'wc-enhanced-select',
					'default'     => '6',
					'options'     => array(
						'1'  => '1x',
						'2'  => '2x',
						'3'  => '3x',
						'4'  => '4x',
						'5'  => '5x',
						'6'  => '6x',
						'7'  => '7x',
						'8'  => '8x',
						'9'  => '9x',
						'10' => '10x',
						'11' => '11x',
						'12' => '12x',
					),
				),
				'testing'              => array(
					'title'       => __( 'Gateway Testing', 'wc-z4money' ),
					'type'        => 'title',
					'description' => '',
				),
				'debug' => array(
					'title'       => __('Enable Log', 'wc-z4money'),
					'type'        => 'checkbox',
					'label'       => __('Enable Log', 'wc-z4money'),
					'default'     => 'no',
					'description' => sprintf(__('Logs plugin events through the <code>% s </code> file. Note: This may record personal information. We recommend using this for debugging purposes only and to delete these records after finalization.', 'wc-z4money'), \WC_Log_Handler_File::get_log_file_path( $this->id ) ),
				  ),
			);

			$this->form_fields = $fields;
        }
		
		/**
		 * Validate the form
		 *
		 * @return boolean
		 */
		public function validate_fields() 
		{
			$billing_persontype = isset( $_POST['billing_persontype'] ) ? intval( wp_unslash( $_POST['billing_persontype'] ) ) : 0;

			if ( 1 !== $billing_persontype &&  2 !== $billing_persontype ) {
				throw new Exception( __( 'Person Type is invalid', 'woocommerce-pagseguro' ) );

			}
			
			if ( 1 === $billing_persontype  ) {
				if ( empty( $_POST['billing_cpf'] ) ) {
					throw new Exception( sprintf( '<strong>%s</strong> %s.', __( 'CPF', 'woocommerce-pagseguro' ), __( 'is a required field', 'woocommerce-pagseguro' ) ) );
				}

			}
			
			if ( 2 === $billing_persontype  ) {
				if ( empty( $_POST['billing_company'] ) ) {
					throw new Exception( sprintf( '<strong>%s</strong> %s.', __( 'Company', 'woocommerce-pagseguro' ), __( 'is a required field', 'woocommerce-pagseguro' ) ) );
				}

				if ( empty( $_POST['billing_cnpj'] ) ) {
					throw new Exception( sprintf( '<strong>%s</strong> %s.', __( 'CNPJ', 'woocommerce-pagseguro' ), __( 'is a required field', 'woocommerce-pagseguro' ) ) );
				}
			}
			
			if ( empty( $_POST['billing_number'] ) || ! isset( $_POST['billing_number'] ) ) {
				throw new Exception( __( 'Please enter billing address number', 'wc-z4money' ) );
			}

			if ( empty( $_POST['billing_birthdate'] ) || ! isset( $_POST['billing_birthdate'] ) ) {
				throw new Exception( __( 'Please enter billing birthdate', 'wc-z4money' ) );
			}

			if ( empty( $_POST['billing_neighborhood'] ) || ! isset( $_POST['billing_neighborhood'] ) ) {
				throw new Exception( __( 'Please enter billing address neighborhood', 'wc-z4money' ) );
			}

			if ( empty( $_POST[STATIC::CARD_NUMBER] ) || ! isset( $_POST[STATIC::CARD_NUMBER] ) ) {
				throw new Exception( __( 'Please enter card number', 'wc-z4money' ) );
			}

			if ( empty( $_POST[STATIC::CARD_NAME] ) || ! isset( $_POST[STATIC::CARD_NAME] ) ) {
				throw new Exception( __( 'Please enter card name', 'wc-z4money' ) );
			}
			
			if ( empty( $_POST[STATIC::CARD_EXPIRY] ) || ! isset( $_POST[STATIC::CARD_EXPIRY] ) ) {
				throw new Exception( __( 'Please enter card expiry', 'wc-z4money' ) );
			}
			
			if ( empty( $_POST[STATIC::CARD_CVC] ) || ! isset( $_POST[STATIC::CARD_EXPIRY] ) ) {
				throw new Exception( __( 'Please enter card CVC', 'wc-z4money' ) );
			}
			
			if ( empty( $_POST[STATIC::CARD_INSTALLMENTS] ) || ! isset( $_POST[STATIC::CARD_INSTALLMENTS] ) ) {
				throw new Exception( __( 'Please enter card installments', 'wc-z4money' ) );
			}

		}
		
		/**
		 * Processes the user data after sending the payment request in checkout.    
		 *
		 * @param    string   $order_id   Current order id.
		 * @return   array   
		 */
		public function process_payment( $order_id ) 
		{			
			$order         = wc_get_order( $order_id );
			$order_data    = $this->get_order_data( $order_id, $order );
			$card_data     = $this->get_creditcard_data( $_POST, $order );
			$customer_data = $this->get_customer_data( $order );
			
			$this->logger->add( sprintf( __( 'Payment process log for order ID: %s', 'wc-z4money' ), $order_id ) );
			$this->logger->add( sprintf( __( 'Payment process get transaction order data: %s', 'wc-z4money' ), var_export( $order_data, true ) ) );
			$this->logger->add( sprintf( __( 'Payment process get transaction card data: %s', 'wc-z4money' ), var_export( $card_data, true ) ) );
			$this->logger->add( sprintf( __( 'Payment process get transaction customer data: %s', 'wc-z4money' ), var_export( $customer_data, true ) ) );
			
			try {
				$sale   = $this->api->do_creditcard_payment( $order_data, $card_data, $customer_data );				
				$result = $order_data;
				
				$this->logger->add( sprintf( __( 'Log for API response: %s', 'wc-z4money' ), var_export( $sale, true ) ) );
				
				if( true === $sale->getSuccess() ) {
					$payment        = $sale->getPedido();
					$payment_status = $payment['status_pedido_id'];
					
					switch ( $payment_status ) {
						case '1': 
							$result['result']     = 'success';
							$result['update']     = 'on-hold';
							$result['message']    = __( 'Thank you, your payment has been successfully processed. Awaiting payment confirmation.', 'wc-z4money' );
							$result['payment_id'] = $payment['id'];
							$result['redirect']   = $result['return_url'];
						break;

						case '2': 
						case '7': 
							$result['result']     = 'success';
							$result['update']     = 'processing';
							$result['message']    = __( 'Thank you, your payment has been successfully processed.', 'wc-z4money' );
							$result['payment_id'] = $payment['id'];
							$result['redirect']   = $result['return_url'];
						break;
						
						default :
							$result['result']  = 'fail';
							$result['update']  = 'failed';
							$result['message'] = __( 'The payment was not made. There was a problem processing your card. Contact your bank for more details.', 'wc-z4money' );	
							wc_add_notice( $result['message'], 'error' );
						break;
					}
					
				} else {
					$result['result']  = 'fail';
					$result['update']  = 'failed';
					$result['message'] = $sale->getMessage();
					
					wc_add_notice( $result['message'], 'error' );
				}
				
				$this->update_order_status( $result );
				
				return $result;
			
			} catch( Exception $e ) {
				$this->logger->add( sprintf( __( 'Unexpected API Connection Error: %s', 'wc-z4money' ), var_export( $e, true ) ) );
				throw new Exception( __( 'There was an error processing the payment. Make sure data is entered correctly. Please try again! If the problem persists contact your website administrator.', 'wc-z4money' ) );
			}			

		}

        
		/**
		 * Get Checkout form field.
		 *
		 * @param float  $order_total
		 */
		protected function get_checkout_form( $order_total ) 
		{
			wc_get_template(
				'card-form.php',
				array(
					'card_number' => STATIC::CARD_NUMBER,
					'card_name'   => STATIC::CARD_NAME,
					'card_expiry' => STATIC::CARD_EXPIRY,
					'card_cvc'    => STATIC::CARD_CVC,
					
					'methods'      => parent::get_available_brands(),
					'installments' => parent::get_installments_html( $order_total ),
				),
				'woocommerce/z4money/',
				WC_Z4MONEY_PATH . 'templates/'
			);
		}

		/**
		 * Init hooks.
		 *
		 * @return   void
		 */
        public function init_hooks() 
        {
			add_action( 'admin_enqueue_scripts', 'wc_z4money_method_creditcard_admin_enqueue' );
			add_action( 'wp_enqueue_scripts', 'wc_z4money_method_creditcard_public_enqueue' );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
			add_action( 'woocommerce_api_z4money', array( $this, 'webhook' ) );

        }

    }

}
