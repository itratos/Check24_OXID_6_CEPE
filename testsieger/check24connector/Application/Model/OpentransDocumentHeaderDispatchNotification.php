<?php
/**
 * Opentrans Document Header Dispatchnotification
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

class OpentransDocumentHeaderDispatchNotification extends OpentransDocumentHeader
{

    /**
     * @var object $dispatchnotificationinfo
     */
    protected $dispatchnotificationinfo = NULL;

    /**
     * Constructor
     *
     * @param string $dispatchnotification_id
     * @param string $dispatchnotification_date
     */
    public function create_dispatchnotificationinfo($dispatchnotification_id, $dispatchnotification_date, $order_id = null)
    {
        if (!is_string($dispatchnotification_id)) {
            throw new OpentransException('$dispatchnotification_id must be a string.');
        }

        if (!is_string($dispatchnotification_date)) {
            throw new OpentransException('$dispatchnotification_date must be a string.');
        }

        if ($order_id && !is_string($order_id)) {
            throw new OpentransException('$order_id must be a string.');
        }

        $this->dispatchnotificationinfo = new OpentransDocumentHeaderDispatchNotificationInfo($dispatchnotification_id, $dispatchnotification_date, $order_id);

        return $this->dispatchnotificationinfo;
    }

    /**
     * Returns dispatchnotificationinfo
     *
     * @return object
     */
    public function get_dispatchnotificationinfo()
    {
        return $this->dispatchnotificationinfo;
    }

}
