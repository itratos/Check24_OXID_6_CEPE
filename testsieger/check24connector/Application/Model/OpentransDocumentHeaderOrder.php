<?php
/**
 * Opentrans Document Header Order
 *
 * @copyright Testsieger Portal AG
 *
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
