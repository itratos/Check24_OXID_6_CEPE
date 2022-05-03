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

/**
 * Class for orderresponse info header
 */
class OpentransDocumentHeaderOrderResponseInfo extends OpentransDocumentHeaderDocumentinfo
{

    /**
     * @var date
     */
    protected $orderresponse_date = NULL;

    /**
     * @var id
     */
    protected $supplier_orderid = NULL;

    /**
     * @var date
     */
    protected $delivery_startdate = NULL;

    /**
     * @var date
     */
    protected $delivery_enddate = NULL;

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
     * Returns orderresponse date
     *
     * @return string orderresponse_date
     */
    public function get_orderresponse_date()
    {
        return $this->orderresponse_date;
    }

    /**
     * Sets orderresponse_date
     *
     * @param string $orderresponse_date
     * @return void
     */
    public function set_orderresponse_date($orderresponse_date)
    {
        $this->orderresponse_date = $orderresponse_date;
    }

    /**
     * Returns supplier orderid
     *
     * @return string supplier_orderid
     */
    public function get_supplier_orderid()
    {
        return $this->supplier_orderid;
    }

    /**
     * Sets supplier order id
     *
     * @param string $supplier_orderid
     * @return void
     */
    public function set_supplier_orderid($supplier_orderid)
    {
        $this->supplier_orderid = $supplier_orderid;
    }

    /**
     * Returns delivery start date
     *
     * @return string delivery_startdate
     */
    public function get_delivery_startdate()
    {
        return $this->delivery_startdate;
    }

    /**
     * Sets delivery start date
     *
     * @param string $delivery_startdate
     * @return void
     */
    public function set_delivery_startdate($delivery_startdate)
    {
        $this->delivery_startdate = $delivery_startdate;
    }

    /**
     * Returns delivery end date
     *
     * @return string delivery_enddate
     */
    public function get_delivery_enddate()
    {
        return $this->delivery_enddate;
    }

    /**
     * Sets delivery end date
     *
     * @param string $delivery_enddate
     * @return void
     */
    public function set_delivery_enddate($delivery_enddate)
    {
        $this->delivery_enddate = $delivery_enddate;
    }
}