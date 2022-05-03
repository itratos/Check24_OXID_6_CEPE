<?php
/**
 * Opentrans Document Header Orderchange
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
