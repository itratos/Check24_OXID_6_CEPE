<?php

namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentHeaderOrder extends OpentransDocumentHeader
{

    /**
     * @var object $sourcinginfo
     */
    protected $sourcinginfo = NULL;

    /**
     * @var object $orderinfo
     */
    protected $orderinfo = NULL;

    /**
     * Constructor sourcinginfo
     *
     * @param string $quotation_id
     */
    public function create_sourcinginfo($quotation_id = NULL)
    {
        if ($quotation_id !== NULL && !is_string($quotation_id)) {
            throw new OpentransException('$quotation_id must be a string.');
        }

        $this->sourcinginfo = new OpentransDocumentHeaderSourcinginfo($quotation_id);

        return $this->sourcinginfo;
    }

    /**
     * Constructor orderinfo
     *
     * @param string $order_id
     * @param string $order_date
     */
    public function create_orderinfo($order_id, $order_date)
    {
        if (!is_string($order_id)) {
            throw new OpentransException('$order_id must be a string.');
        }

        if (!is_string($order_date)) {
            throw new OpentransException('$order_date must be a string.');
        }

        $this->orderinfo = new OpentransDocumentHeaderOrderinfo($order_id, $order_date);

        return $this->orderinfo;
    }

    /**
     * Returns orderinfo
     *
     * @return object
     */
    public function get_orderinfo()
    {
        return $this->orderinfo;
    }

    /**
     * Returns sourcinginfo
     *
     * @return object
     */
    public function get_sourcinginfo()
    {
        return $this->sourcinginfo;
    }

}
