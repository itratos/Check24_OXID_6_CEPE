<?php


namespace TestSieger\Check24Connector\Application\Model;

/**
 * Class for orderresponse document
 */
class OpentransDocumentOrderResponse extends OpentransDocument
{

    /**
     * @var object
     */
    protected $summary = NULL;


    /**
     * Construct a openTrans orderresponse
     */
    public function __construct()
    {
        parent::__construct(self::DOCUMENT_TYPE_ORDERRESPONSE);

        $this->summary = new OpentransDocumentSummaryOrder(0);
    }

    /**
     * Creates a openTrans orderresponse header
     *
     * @return object OpentransDocumentHeaderOrderResponse
     */
    public function create_header()
    {
        $this->header = new OpentransDocumentHeaderOrderresponse();

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