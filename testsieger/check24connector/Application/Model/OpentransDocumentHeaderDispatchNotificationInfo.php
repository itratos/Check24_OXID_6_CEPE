<?php
/**
 * Opentrans Document Header Dispatchnotificationinfo
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

class OpentransDocumentHeaderDispatchNotificationInfo extends OpentransDocumentHeaderDocumentinfo
{

    protected $order_id;

    protected $shipment_id;

    protected $tracking_url;

    //TODO: remove if not need, added as remark
    protected $carrier_name;

    /**
     * Construct a openTrans dispatchnotificationinfo
     *
     * @param string dispatchnotification_id
     * @param string dispatchnotification_date
     */
    public function __construct($dispatchnotification_id, $dispatchnotification_date, $order_id = null)
    {
        if (!is_string($dispatchnotification_id)) {
            throw new OpentransException('$dispatchnotification_id must be a string.');
        }

        if (!is_string($dispatchnotification_date)) {
            throw new OpentransException('$dispatchnotification_date must be a string.');
        }

        if ($order_id) {
            if (!is_string($order_id)) {
                throw new OpentransException('$order_id must be a string.');
            }
            $this->order_id = $order_id;
        }

        parent::__construct($dispatchnotification_id, $dispatchnotification_date);
    }

    /**
     * Returns dispatchnotification id
     *
     * @return string dispatchnotification_id
     */
    public function get_dispatchnotification_id()
    {
        return $this->get_document_id();
    }

    /**
     * Returns dispatchnotification date
     *
     * @return string dispatchnotification_date
     */
    public function get_dispatchnotification_date()
    {
        return $this->get_document_date();
    }

    /**
     * Returns order_id
     *
     * @return string order_id
     */
    public function get_order_id()
    {
        return $this->order_id;
    }

    /**
     * Returns dispatchnotification SHIPMENT_ID as tracking code (oxtrackcode)
     * @return mixed
     */
    public function get_shipment_id()
    {
        return $this->shipment_id;
    }

    /**
     * Sets dispatchnotification SHIPMENT_ID as tracking code (oxtrackcode)
     */
    public function set_shipment_id($sShipmentID)
    {
        $this->shipment_id = $sShipmentID;
    }

    //TODO: remove if not need, added as remark
    /**
     * Returns carrier name
     * @return mixed
     */
    public function get_carrier_name()
    {
        return $this->carrier_name;
    }

    /**
     * Sets carrier name
     */
    public function set_carrier_name($sCarrierName)
    {
        $this->carrier_name = $sCarrierName;
    }

    /**
     * Returns tracking url
     * @return mixed
     */
    public function get_tracking_url() {
        return $this->tracking_url;
    }

    /**
     * Sets tracking url
     */
    public function set_tracking_url($sTrackingUrl)
    {
        $this->tracking_url = $sTrackingUrl;
    }
}