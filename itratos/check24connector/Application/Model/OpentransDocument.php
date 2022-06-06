<?php

namespace Itratos\Check24Connector\Application\Model;

class OpentransDocument
{

    /**
     * Constants
     */

    /**
     * Version opentrans
     *
     * @var const integer
     */
    const VERSION = 2.1;

    /**
     * Version orderapi
     *
     * @var const string
     */
    const VERSION_ORDERAPI = '2.0';

    /**
     *  openTrans documenttype ORDER
     *
     * @var const string
     */
    const DOCUMENT_TYPE_ORDER = 'order';

    /**
     *  openTrans documenttype ORDERCHANGE
     *
     * @var const string
     */
    const DOCUMENT_TYPE_ORDERCHANGE = 'orderchange';

    /**
     *  openTrans documenttype DISPATCHNOTIFICATION
     *
     * @var const string
     */
    const DOCUMENT_TYPE_DISPATCHNOTIFICATION = 'dispatchnotification';

    /**
     *  openTrans documenttype ORDERRESPONSE
     *
     * @var const string
     */
    const DOCUMENT_TYPE_ORDERRESPONSE = 'orderresponse';

    /**
     *  openTrans defined and valid documenttypes
     *
     * @var static array
     */
    static $valid_document_types = [
        self::DOCUMENT_TYPE_ORDER,
        self::DOCUMENT_TYPE_ORDERCHANGE,
        self::DOCUMENT_TYPE_DISPATCHNOTIFICATION,
        self::DOCUMENT_TYPE_ORDERRESPONSE
    ];

    /**
     * documenttype
     *
     * @var string
     */
    protected $document_type = NULL;

    /**
     * Header
     *
     * @var OpentransDocumentHeader
     */
    protected $header = NULL;

    /**
     * Itemlist
     *
     * @var array
     */
    protected $item_list = NULL;

    /**
     * Summary
     *
     * @var OpentransDocumentSummary
     */
    protected $summary = NULL;

    /**
     * Construct an OpenTrans document
     *
     * @param string
     */
    public function __construct($document_type)
    {
        if (!is_string($document_type)) {
            throw new OpentransException('$document_type must be a string.');
        }

        if (!in_array($document_type, self::$valid_document_types)) {
            throw new OpentransException('Unsupported document type "' . $document_type . '".');
        }

        $this->document_type = $document_type;
    }

    /**
     * Create header of openTrans document
     *
     * @return OpentransDocumentHeader
     */
    public function create_header()
    {
        $this->header = new OpentransDocumentHeader();

        return $this->header;
    }

    /**
     * Adds item to item list
     *
     * @param $item
     * @param $sLineItemId
     * @return void
     * @throws OpentransException
     */
    public function add_item($item, $sLineItemId = null)
    {
        if (!$item instanceof OpentransDocumentItem) {
            throw new OpentransException('Item must be type of OpentransDocumentItem');
        }

        $this->item_list[] = $item;
        $item->set_line_item_id(
            $sLineItemId ? $sLineItemId : $this->summary->get_total_item_num()
        );
        $this->summary->add_item($item->get_price_line_amount());
    }

    /**
     * Returns document type
     *
     * @return string
     */
    public function get_document_type()
    {
        return $this->document_type;
    }

    /**
     * Returns document header
     *
     * @return OpentransDocumentHeader
     */
    public function get_header()
    {
        return $this->header;
    }

    /**
     * Returns item lsit
     *
     * @return array()
     */
    public function get_item_list()
    {
        return $this->item_list;
    }

    /**
     * Returns summary
     *
     * @return OpentransDocumentSummary
     */
    public function get_summary()
    {
        return $this->summary;
    }
}