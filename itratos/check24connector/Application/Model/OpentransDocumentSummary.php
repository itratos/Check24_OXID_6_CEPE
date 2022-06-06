<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentSummary
{

    /**
     * Item count of item list
     *
     * @var integer
     */
    protected $total_item_num = NULL;

    /**
     * Construct a openTrans party
     *
     * @param integer total_item_num
     */
    public function __construct($total_item_num = 0)
    {
        if (!is_int($total_item_num)) {
            throw new OpentransException('$total_item_num must be integer.');
        }

        $this->total_item_num = $total_item_num;
    }

    /**
     * Adds item
     *
     * @return void
     */
    public function add_item()
    {
        $this->total_item_num++;
    }

    /**
     * Returns total_item_num
     *
     * @return integer total_item_num
     */
    public function get_total_item_num()
    {
        return $this->total_item_num;
    }

}
