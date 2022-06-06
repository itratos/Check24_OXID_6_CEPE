<?php

namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentHeaderSourcinginfo
{

    /**
     * @var string
     */
    private $quotation_id = NULL;

    /**
     * @var array
     */
    private $catalog_reference = array();

    /**
     * Construct a openTrans sourcinginfo
     *
     * @param string $quotation_id
     */
    public function __construct($quotation_id = NULL)
    {

        if ($quotation_id !== NULL && !is_string($quotation_id)) {
            throw new OpentransException('$quotation_id must be a string.');
        }

        $this->quotation_id = $quotation_id;
    }

}