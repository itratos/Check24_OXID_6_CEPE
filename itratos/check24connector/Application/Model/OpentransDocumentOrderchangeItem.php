<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentOrderchangeItem extends OpentransDocumentItem
{

    /**
     * Construct a openTrans item
     *
     * @param integer line_item_id
     */
    public function __construct($line_item_id = NULL)
    {

        /*if (!is_int($line_item_id)) {
            throw new OpentransException('$line_item_id must be integer.');
        }*/

        parent::__construct($line_item_id);

    }
}

?>