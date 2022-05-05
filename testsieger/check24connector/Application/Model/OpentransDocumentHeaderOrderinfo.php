<?php
/**
 * Opentrans Document Header Orderinfo
 *
 * @copyright Testsieger Portal AG
 * @license GPL 3:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Testsieger.de OpenTrans Connector
 */

namespace TestSieger\Check24Connector\Application\Model;

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