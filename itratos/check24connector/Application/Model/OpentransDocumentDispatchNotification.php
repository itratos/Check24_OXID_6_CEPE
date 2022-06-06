<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentDispatchNotification extends OpentransDocument
{

    /**
     * @var object
     */
    protected $summary = NULL;

    /**
     * Construct a openTrans dispatchnotification
     */
    public function __construct()
    {
        parent::__construct(self::DOCUMENT_TYPE_DISPATCHNOTIFICATION);

        $this->summary = new OpentransDocumentSummaryOrder(0);
    }

    /**
     * Creates a openTrans dispatchnotification header
     *
     * @return object OpentransDocumentHeaderDispatchNotification
     */
    public function create_header()
    {
        $this->header = new OpentransDocumentHeaderDispatchNotification();

        return $this->header;
    }

    /**
     * Sets summary
     *
     * @param object $summary OpentransDocumentSummary
     * @return void
     */
    public function set_summary($summary)
    {
        if (!$summary instanceof OpentransDocumentSummary) {
            throw new OpentransException('$summary must be type of OpentransDocumentSummary');
        }

        $this->summary = $summary;
    }

}