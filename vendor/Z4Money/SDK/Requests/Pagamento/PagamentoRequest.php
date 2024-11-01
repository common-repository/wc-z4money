<?php

namespace Z4Money\SDK\Requests\Pagamento;

use Z4Money\SDK\Requests\AbstractRequest;

class PagamentoRequest extends AbstractRequest
{
    public function run()
    {
        $url = parent::getEnvironment()->getApiURL();
        $data = parent::getData();
		
		parent::setResourcePath('vendas');
        parent::setHttpBody($data->container);
        parent::getClient()->getConfig()->setHost($url);
        parent::setTypeResponse('Z4Money\SDK\Model\Venda');

        return parent::sendRequest();
    }
}
