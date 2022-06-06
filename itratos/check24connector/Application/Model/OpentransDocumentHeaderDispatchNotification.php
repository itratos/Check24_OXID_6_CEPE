<?php

namespace Itratos\Check24Connector\Application\Model;

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
