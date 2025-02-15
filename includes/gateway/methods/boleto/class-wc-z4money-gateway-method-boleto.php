<?php

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

if ( ! class_exists( 'Wc_Z4Money_Gateway_Method_Boleto' ) ) {

/**
 * Boleto payment method.
 *
 * @link       https://github.com/santanamic/wc-z4money
 * @since      1.0.0
 *
 * @package    wc-z4money
 * @subpackage wc-z4money/includes/gateway/methods/
 * @author     AQUARELA - WILLIAN SANTANA <williansantanamic@gmail.com>
 */
    class Wc_Z4Money_Gateway_Method_Boleto extends Wc_Z4Money_Gateway 
    {
		const METHOD_PATH             = __DIR__;
		const REQUIRE_FILES           = ['includes/functions.php'];
		
		/**
		 * Start payment method.
		 *
		 * @return   void
		 */
		public function __construct() 
        {
			$this->id                   = 'wc_z4money_boleto';
			$this->method_title         = __( 'Z4Money', 'wc-z4money' );
			$this->method_description   = __( 'Receiv by Boleto using Z4Money.', 'wc-z4money' );
			$this->token_secret         = $this->get_option( 'token_secret' );
			$this->token_secret_sandbox = $this->get_option( 'token_secret_sandbox' );
			$this->is_testmode          = $this->get_option( 'testmode' );
			$this->expiration           = $this->get_option( 'expiration' );
			$this->debug                = $this->get_option( 'debug' );
			//$this->icon                 = plugins_url( 'assets/public/img/logo_horizontal_min.png', __FILE__ );

			parent::init_gateway();
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
					'default'     => 'Boleto',
					'desc_tip'    => true,
				  ),
				 'description' => array(
					'title'       => __( 'Checkout Description', 'wc-z4money' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description the user sees during checkout.', 'wc-z4money' ),
					'desc_tip'    => true,
					'default'     => __( 'Pay by Boleto', 'wc-z4money' ), 
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
				'expiration' => array(
					'title'       => __( 'Boleto Expiration', 'wc-z4money' ),
					'type'        => 'number',
					'description' => __( 'Days for Boleto expiration, after issuance.', 'wc-z4money' ),
					'default'     => '5',
					'desc_tip'    => true,
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
				
				if ( empty( $_POST['billing_birthdate'] ) || ! isset( $_POST['billing_birthdate'] ) ) {
					throw new Exception( __( 'Please enter billing birthdate', 'wc-z4money' ) );
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

			if ( empty( $_POST['billing_neighborhood'] ) || ! isset( $_POST['billing_neighborhood'] ) ) {
				throw new Exception( __( 'Please enter billing address neighborhood', 'wc-z4money' ) );
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
			$boleto_data   = $this->get_boleto_data( $_POST, $order );
			$customer_data = $this->get_customer_data( $order );
			
			$this->logger->add( sprintf( __( 'Payment process log for order ID: %s', 'wc-z4money' ), $order_id ) );
			$this->logger->add( sprintf( __( 'Payment process get transaction order data: %s', 'wc-z4money' ), var_export( $order_data, true ) ) );
			$this->logger->add( sprintf( __( 'Payment process get transaction customer data: %s', 'wc-z4money' ), var_export( $customer_data, true ) ) );
			
			try {
				$sale   = $this->api->do_boleto_payment( $order_data, $boleto_data, $customer_data );				
				$result = $order_data;
		
				$this->logger->add( sprintf( __( 'Log for API response: %s', 'wc-z4money' ), var_export( $sale, true ) ) );
				
				if( true === $sale->getSuccess() ) {
					$payment        = $sale->getPedido();
					$payment_status = $payment['status_pedido_id'];
					
					switch ( $payment_status ) {
						case '1': 
							$result['result']     = 'success';
							$result['update']     = 'on-hold';
							$result['message']    = __( 'Thank you, your payment has been success fully processed.', 'wc-z4money' );
							$result['redirect']   = $result['return_url'];
							$result['payment_id'] = $payment['id'];
							$result['url_boleto'] = $payment['urlBoleto'];
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
		 *
		 * Add a boleto view in order summary
		 *
		 * @access public
		 * @return boolean
		 *
		 */
		 
		public function order_summary_preview( $order_id ) 
		{
			$order = wc_get_order( $order_id );
			$html  = '<p>' . __( 'Please pay the boleto for your purchase to be approved.', 'wc-z4money' ) .'</p>';
			$html .= '<p><iframe src="' . $order->get_meta( 'Z4MONEY_URL_BOLETO' ) . '" style="width:100%; height:1000px;border: solid 1px #eee;"></iframe></p>';
 
			echo '<p>' . $html . '</p>';		
		}
		
		/**
		 *
		 * Add content to the WC emails.
		 *
		 * @access public
		 * @param WC_Order $order Order object.
		 * @param bool     $sent_to_admin  Sent to admin.
		 * @param bool     $plain_text Email format: plain text or HTML.
		 *
		 */
		 
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) 
		{
			if ( ! $sent_to_admin && 'wc_z4money_boleto' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
				echo wp_kses_post( wpautop( wptexturize( sprintf ( __( '<strong>NOTE: To reprint the boleto <a href="%s">click here</a></strong>', 'wc-z4money'), $order->get_meta( 'Z4MONEY_URL_BOLETO' ) ) ) ) . PHP_EOL );
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
				'boleto-form.php',
				array(),
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
			add_action( 'admin_enqueue_scripts', 'wc_z4money_method_boleto_admin_enqueue' );
			add_action( 'wp_enqueue_scripts', 'wc_z4money_method_boleto_public_enqueue' );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'order_summary_preview' ) );
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
			add_action( 'woocommerce_api_z4money', array( $this, 'webhook' ) );
			
        }

    }

}
