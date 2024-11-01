<?php

namespace Z4Money\SDK\Requests\Pagamento;

use Z4Money\SDK\Requests\AbstractRequest;

class RefundRequest extends AbstractRequest
{
    public function run()
    {
        $url = parent::getEnvironment()->getApiURL();
		$data = parent::getData();
        $payment_id = $data->getPaymentId();
		
		if (  isset($data->container['payment_id']) ) {
			unset($data->container['payment_id']);
		}		
		parent::setHttpBody($data->container);
		parent::setResourcePath('vendas/' . $payment_id . '/estornar');
        parent::getClient()->getConfig()->setHost($url);

        return parent::sendRequest();
    }
}
