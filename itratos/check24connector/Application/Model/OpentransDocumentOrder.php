<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentOrder extends OpentransDocument
{

    /**#@+
     * Constants
     */
    /**
     * TYPE_STANDARD
     *
     * @var const string
     */
    const TYPE_STANDARD = 'standard';

    /**
     * TYPE_EXPRESS
     *
     * @var const string
     */
    const TYPE_EXPRESS = 'express';

    /**
     * TYPE_RELEASE
     *
     * @var const string
     */
    const TYPE_RELEASE = 'release';

    /**
     * TYPE_CONSIGNMENT
     *
     * @var const string
     */
    const TYPE_CONSIGNMENT = 'consignment';

    /**
     * openTrans defined and valid ordertypes
     *
     * @var static array
     */
    static $valid_types = array(
        self::TYPE_STANDARD,
        self::TYPE_EXPRESS,
        self::TYPE_RELEASE,
        self::TYPE_CONSIGNMENT
    );

    /**
     * @var string
     */
    protected $type = NULL;

    /**
     * @var object
     */
    protected $summary = NULL;

    /**
     * Construct a openTrans order
     *
     * @param string type
     */
    public function __construct($type)
    {
        parent::__construct(self::DOCUMENT_TYPE_ORDER);

        if (!is_string($type)) {
            throw new OpentransException('$type must be a string.');
        }

        if (!in_array($type, self::$valid_types)) {
            throw new OpentransException('Unsupported type "' . $type . '".');
        }

        $this->type = $type;

        $this->set_summary(new OpentransDocumentSummaryOrder(0));
    }

    /**
     * Creates header for openTrans order
     *
     * @return object OpentransDocumentHeaderOrder
     */
    public function create_header()
    {
        $this->header = new OpentransDocumentHeaderOrder();

        return $this->header;
    }

    /**
     * Sets summary for openTrans order
     *
     * @param object summary OpentransDocumentSummary
     */
    public function set_summary($summary)
    {
        if (!$summary instanceof OpentransDocumentSummary) {
            throw new OpentransException('$summary must be type of OpentransDocumentSummary');
        }

        $this->summary = $summary;
    }
}