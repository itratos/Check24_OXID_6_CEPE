<?php

namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentHeaderOrderinfo extends OpentransDocumentHeaderDocumentinfo
{

    /**
     * @var string
     */
    protected $currency = NULL;

    /**
     * @var string
     */
    protected $payment = NULL;

    /**
     * Construct a openTrans orderinfo
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
     * Sets currency
     *
     * @param string currency
     * @return void
     */
    public function set_currency($currency)
    {
        if (!is_string($currency)) {
            throw new OpentransException('$currency must be a string.');
        }

        $this->currency = $currency;
    }

    /**
     * @returns string currency
     */
    public function get_currency()
    {
        return $this->currency;
    }


    /**
     * Sets payment
     *
     * @param string payment
     * @return void
     */
    public function set_payment($payment)
    {
        if (!$payment instanceof OpentransDocumentPayment) {
            throw new OpentransException('$payment must be type of OpentransDocumentPayment.');
        }

        $this->payment = $payment;
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
     * Returns order_date
     *
     * @return string order_date
     */
    public function get_order_date()
    {
        return $this->get_document_date();
    }

    /**
     * Returns payment
     *
     * @return string payment
     */
    public function get_payment()
    {
        return $this->payment;
    }

    /**
     * Sets order_date
     *
     * @param string $orderchange_date
     * @return void
     */
    public function set_order_date($order_date)
    {
        $this->order_date = $order_date;
    }

}