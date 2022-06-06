<?php

namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentHeaderOrderchange extends OpentransDocumentHeader
{

    /**
     * @var object $orderchangeinfo
     */
    protected $orderchangeinfo = NULL;

    /**
     * Constructor
     *
     * @param string $order_id
     * @param string $order_date
     */
    public function create_orderchangeinfo($order_id, $order_date)
    {
        if (!is_string($order_id)) {
            throw new OpentransException('$order_id must be a string.');
        }

        if (!is_string($order_date)) {
            throw new OpentransException('$order_date must be a string.');
        }

        $this->orderchangeinfo = new OpentransDocumentHeaderOrderchangeinfo($order_id, $order_date);

        return $this->orderchangeinfo;
    }

    /**
     * Returns orderchangeinfo
     *
     * @return object
     */
    public function get_orderchangeinfo()
    {
        return $this->orderchangeinfo;
    }

}
