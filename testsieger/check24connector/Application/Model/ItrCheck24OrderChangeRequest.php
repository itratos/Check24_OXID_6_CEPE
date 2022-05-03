<?php

namespace TestSieger\Check24Connector\Application\Model;

/**
 * Orderchange request manager.
 *
 */
class ItrCheck24OrderChangeRequest extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'ItrCheck24OrderChangeRequest';


    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('itrcheck24_orderchangerequest');
    }
}
