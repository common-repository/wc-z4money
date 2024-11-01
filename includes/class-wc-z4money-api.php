<?php

use Z4Money\Configuration;
use Z4Money\SDK\Model\Webhook;
use Z4Money\SDK\Model\Id;
use Z4Money\SDK\Model\Pagamento;
use Z4Money\SDK\Model\Endereco;
use Z4Money\SDK\Model\Cliente;
use Z4Money\SDK\Model\Cartao;
use Z4Money\SDK\Requests\Pagamento\PagamentoRequest;
use Z4Money\SDK\Requests\Pagamento\StatusRequest;
use Z4Money\SDK\Requests\Pagamento\RefundRequest;
use Z4Money\SDK\Requests\Pagamento\AddWebhookRequest;
use Z4Money\SDK\Requests\Pagamento\GetWebhookRequest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The plugin class for API integration.
 */
 
class Wc_Z4Money_Api {

	/**
	 * Gateway class.
	 *
	 * @var Wc_Z4Money_Gateway
	 */
	public $gateway;

	/**
	 * Constructor.
	 *
	 * @param Wc_Z4Money_Gateway $gateway Payment Gateway instance.
	 */
	public function __construct( $gateway ) 
	{		
		$this->gateway = $gateway;
	}

	/**
	 * Get gateway token.
	 *
	 * @return string
	 */
	public function get_token() {
		return 'yes' === $this->gateway->is_testmode ? $this->gateway->token_secret_sandbox :  $this->gateway->token_secret;
	}

	/**
	 * Do payment request.
	 *
	 * @param  Z4Money\SDK\Model\Pagamento    $pagamento       Data payment.
	 *
	 * @return array
	 */
	public function do_payment( $pagamento ) 
	{	
		$config = new Configuration();
		$config->setBearerAuth( $this->get_token() );
		
		$requestPagamento = new PagamentoRequest( $pagamento, $config, true );

		try {
			$request = $requestPagamento->run();
			return $request;
		} catch (Exception $e) {
			$this->gateway->logger->add( 'Exception when calling Order request->run: ' . $e->getMessage() );
		}
		
	}

	/**
	 * Do status request.
	 *
	 * @param  Z4Money\SDK\Model\Id    $status       Data status.
	 *
	 * @return array
	 */
	public function do_status( $id ) 
	{	
		$config = new Configuration();
		$config->setBearerAuth( $this->get_token() );
		$requestStatus = new StatusRequest( $id, $config, true );

		try {
			$request = $requestStatus->run();
			return $request;
		} catch (Exception $e) {
			$this->gateway->logger->add( 'Exception when calling Status request->run: ' . $e->getMessage() );
		}
		
	}

	/**
	 * Do refund request.
	 *
	 * @param  Z4Money\SDK\Model\Id    $refund       Data status.
	 *
	 * @return array
	 */
	public function do_refund( $id ) 
	{	
		$config = new Configuration();
		$config->setBearerAuth( $this->get_token() );
		$requestRefund = new RefundRequest( $id, $config, true );

		try {
			$request = $requestRefund->run();
			return $request;
		} catch (Exception $e) {
			$this->gateway->logger->add( 'Exception when calling Refund request->run: ' . $e->getMessage() );
		}
		
	}
	
	/**
	 * Do credicard payment request.
	 *
	 * @param  array    $order      Order data.
	 * @param  array    $card       Card data.
	 * @param  array    $customer   Customer data.
	 *
	 * @return array
	 */
	public function do_creditcard_payment( $order, $card, $customer ) 
	{	
		$cliente = new Cliente();
		$cliente->setNome( $customer['person_type'] == 1 ? $customer['full_name'] : $customer['company_name'] );
		$cliente->setCPF( $customer['person_type'] == 1 ? $customer['cpf'] : $customer['cnpj'] );
		$cliente->setDataNascimento( $customer['birthdate'] );
		$cliente->setEmail( $customer['email'] );
		$cliente->setCelular( $customer['phone'] );

		$endereco = new Endereco();
		$endereco->setNumero( $customer['address_number'] );
		$endereco->setLogradouro( $customer['address_street'] );
		$endereco->setCEP( $customer['address_zipcode'] );
		$endereco->setCidade( $customer['address_city'] );
		$endereco->setEstado( $customer['address_state'] );
		$endereco->setComplemento( $customer['address_complement'] );

		$cartao = new Cartao();
		$cartao->SetTitular( $card['name_on_card'] );
		$cartao->setStatusNumero( $card['card_number'] );
		$cartao->setCodigoSeguranca( $card['card_cvv'] );
		$cartao->setValidade( $card['card_expiration'] );
		
		$pagamento = new Pagamento();
		$pagamento->setTipoPagamentoId( Pagamento::CARTAO_DE_CREDITO );
		$pagamento->setValor( $card['card_order_total'] );
		$pagamento->setCliente($cliente);
		$pagamento->setCartao($cartao);
		$pagamento->setEndereco($endereco);
		$pagamento->setParcelas( $card['card_installments'] );
		
		return $this->do_payment( $pagamento );
	}

	/**
	 * Do boleto payment request.
	 *
	 * @param  array    $order      Order data.
	 * @param  array    $customer   Customer data.
	 *
	 * @return array
	 */
	public function do_boleto_payment( $order, $boleto, $customer ) 
	{
		$cliente = new Cliente();
		$cliente->setNome( $customer['person_type'] == 1 ? $customer['full_name'] : $customer['company_name'] );
		$cliente->setCPF( $customer['person_type'] == 1 ? $customer['cpf'] : $customer['cnpj'] );
		$cliente->setDataNascimento( $customer['birthdate'] );
		$cliente->setEmail( $customer['email'] );
		$cliente->setCelular( $customer['phone'] );

		$endereco = new Endereco();
		$endereco->setNumero( $customer['address_number'] );
		$endereco->setLogradouro( $customer['address_street'] );
		$endereco->setCEP( $customer['address_zipcode'] );
		$endereco->setCidade( $customer['address_city'] );
		$endereco->setEstado( $customer['address_state'] );
		$endereco->setComplemento( $customer['address_complement'] );
		
		$pagamento = new Pagamento();
		$pagamento->setTipoPagamentoId( Pagamento::BOLETO );
		$pagamento->setDescricao( $order['title'] );
		$pagamento->setDataVencimento( $boleto['expiration'] );
		$pagamento->setValor( $order['total'] );
		$pagamento->setCliente( $cliente );
		$pagamento->setEndereco( $endereco );

		return $this->do_payment( $pagamento );
	}

	/**
	 * Get payment order status.
	 *
	 * @param  string    $payment_id   Payment ID.
	 *
	 * @return array
	 */
	public function get_status( $payment_id ) 
	{
		$id = new Id();
		$id->setPaymentId( $payment_id );

		return $this->do_status( $id );
	}

	/**
	 * Do payment refund.
	 *
	 * @param  string    $payment_id   Payment ID.
	 *
	 * @return array
	 */
	public function do_payment_refund( $payment_id, $amount ) 
	{
		$id = new Id();
		$id->setPaymentId( $payment_id );
		$id->setAmount( $amount );

		return $this->do_refund( $id );
	}

	/**
	 * Get webhook data.
	 *
	 * @return array
	 */
	public function get_webhook_data() 
	{
		$data    = array( 'slug' => 'url_webhook', 'value' => WC()->api_request_url( 'Z4Money' ) );	
		$webhook = new Webhook( $data );
		
		return $webhook;
	}

	/**
	 * Check webhook.
	 *
	 * @return array
	 */
	public function check_webhook() 
	{
		$webhook = $this->get_webhook_data();
		
		$config  = new Configuration();
		$config->setBearerAuth( $this->get_token() );
		
		$requestGetWebhook = new GetWebhookRequest( $webhook, $config, true );
		
		try {
			$request = $requestGetWebhook->run();
			return $request;
		} catch (Exception $e) {
			$this->gateway->logger->add( 'Exception when calling Check Webhook request->run: ' . $e->getMessage() );

		}

	}
	
	/**
	 * Add webhook url.
	 *
	 * @return array
	 */
	public function add_webhook() 
	{
		$config  = new Configuration();
		$config->setBearerAuth( $this->get_token() );

		$webhook           = $this->get_webhook_data();
		$requestAddWebhook = new AddWebhookRequest( $webhook, $config, true );

		try {
			$request = $requestAddWebhook->run();				
			return $request;
		} catch (Exception $e) {
			$this->gateway->logger->add( 'Exception when calling Add Webhook request->run: ' . $e->getMessage() );
		}
	}
	
}
