<?php

namespace Z4Money\SDK\Requests\Pagamento;

use Z4Money\SDK\Requests\AbstractRequest;

class AddWebhookRequest extends AbstractRequest
{
    public function run()
    {
        $url = parent::getEnvironment()->getApiURL();
        $data = parent::getData();
		
        parent::setHttpBody($data->container);		
		parent::setResourcePath('estabelecimentos/configuracoes');
        parent::getClient()->getConfig()->setHost($url);

        return parent::sendRequest();
    }
}
