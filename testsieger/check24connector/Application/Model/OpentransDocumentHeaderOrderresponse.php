<?php
/**
 * Class added by Itratos
 */

namespace TestSieger\Check24Connector\Application\Model;

/**
 * Class for orderresponse document header
 */
class OpentransDocumentHeaderOrderresponse extends OpentransDocumentHeader
{

    /**
     * @var object $orderresponseinfo
     */
    protected $orderresponseinfo = NULL;

    /**
     * Constructor
     *
     * @param string $order_id
     * @param string $order_date
     */
    public function create_orderresponseinfo($order_id, $order_date)
    {
        if (!is_string($order_id)) {
            throw new OpentransException('$order_id must be a string.');
        }

        if (!is_string($order_date)) {
            throw new OpentransException('$order_date must be a string.');
        }

        $this->orderresponseinfo = new OpentransDocumentHeaderOrderResponseInfo($order_id, $order_date);

        return $this->orderresponseinfo;
    }

    /**
     * Returns orderresponseinfo
     *
     * @return object
     */
    public function get_orderresponseinfo()
    {
        return $this->orderresponseinfo;
    }
}
