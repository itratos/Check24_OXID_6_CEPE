<?php

namespace Itratos\Check24Connector\Application\Model;

class ItrCheck24OrderChangeRequestList extends \OxidEsales\Eshop\Core\Model\ListModel
{

    public function __construct()
    {
        parent::__construct(\Itratos\Check24Connector\Application\Model\ItrCheck24OrderChangeRequest::class);
    }

}