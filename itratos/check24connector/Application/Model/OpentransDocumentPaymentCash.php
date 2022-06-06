<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentPaymentCash extends OpentransDocumentPayment
{

    /**
     * Construct a openTrans payment cash
     *
     * @param string term_type
     * @param string term_value
     */
    public function __construct($term_type = NULL, $term_value = NULL)
    {
        if (!is_string($term_type)) {
            throw new OpentransException('$term_type must be a string.');
        }

        if (!is_string($term_value)) {
            throw new OpentransException('$term_value must be a string.');
        }

        parent::__construct(self::TYPE_CASH, $term_type, $term_value);

    }
}