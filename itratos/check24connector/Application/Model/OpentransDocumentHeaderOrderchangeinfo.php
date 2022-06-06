<?php

namespace Itratos\Check24Connector\Application\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * Class for orderchange info header
 */
class OpentransDocumentHeaderOrderchangeinfo extends OpentransDocumentHeaderDocumentinfo
{

    /**
     * @var date
     */
    protected $orderchange_date = NULL;

    /**
     * @var date
     */
    protected $orderchange_sequence_id = NULL;

    /**
     * Construct a openTrans orderchangeinfo
     *
     * @param string order_id
     * @param string order_date
     */
    public function __construct($order_id, $order_date)
    {

        if (!is_string($order_id)) {
            throw new OpentransException('$order_id must be a string.');
        }

        if (!is_string($order_date)) {
            throw new OpentransException('$order_date must be a string.');
        }

        parent::__construct($order_id, $order_date);

    }

    /**
     * Returns order_id
     *
     * @return string order_id
     */
    public function get_order_id()
    {
        return $this->get_document_id();
    }

    /**
     * Returns order date
     *
     * @return string order_date
     */
    public function get_order_date()
    {
        return $this->get_document_date();
    }

    /**
     * Returns orderchange date
     *
     * @return string orderchange_date
     */
    public function get_orderchange_date()
    {
        return $this->orderchange_date;
    }

    /**
     * Sets orderchange_date
     *
     * @param string $orderchange_date
     * @return void
     */
    public function set_orderchange_date($orderchange_date)
    {
        $this->orderchange_date = $orderchange_date;
    }

    /**
    * Sets orderchange sequence id
    *
    * @param string $orderchange_date
    * @return void
    */
    public function set_orderchange_sequence_id($orderchange_sequence_id)
    {
        $this->orderchange_sequence_id = $orderchange_sequence_id;

    }

    /**
     * Returns orderchange sequence_id
     *
     * @return int
     */
    public function get_orderchange_sequence_id()
    {
        return $this->orderchange_sequence_id;

    }
}