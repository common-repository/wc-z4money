<?php

namespace Z4Money\SDK\Requests\Pagamento;

use Z4Money\SDK\Requests\AbstractRequest;

class StatusRequest extends AbstractRequest
{
    public function run()
    {
        $url = parent::getEnvironment()->getApiURL();
        $payment_id = parent::getData()->getPaymentId();
		
		parent::setMethod('GET');
		parent::setResourcePath('vendas/' . $payment_id);
        parent::getClient()->getConfig()->setHost($url);

        return parent::sendRequest();
    }
}
