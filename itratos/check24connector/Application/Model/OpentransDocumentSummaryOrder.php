<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentSummaryOrder extends OpentransDocumentSummary
{

    /**
     * Total amount of item list
     *
     * @var float
     */
    protected $total_amount = NULL;

    /**
     * Construct a openTrans summary for order document
     *
     * @param string total_item_num
     * @param string total_amount
     */
    public function __construct($total_item_num = 0, $total_amount = 0)
    {

        if (!is_int($total_item_num)) {
            throw new OpentransException('$total_item_num must be integer.');
        }

        if (!is_int($total_amount) && !is_float($total_amount)) {
            throw new OpentransException('$total_amount must be integer or float.');
        }

        parent::__construct($total_item_num);

        $this->total_amount = $total_amount;
    }

    /**
     * Adds item
     *
     * @param float $price_line_amount
     * @return void
     */
    public function add_item($price_line_amount = 0)
    {
        if (!is_int($price_line_amount) && !is_float($price_line_amount)) {
            throw new OpentransException('$price_line_amount must be integer or float.');
        }

        parent::add_item();

        $this->total_amount += $price_line_amount;
    }

    /**
     * Returns total_amount
     *
     * @return string total_amount
     */
    public function get_total_amount()
    {
        return $this->total_amount;
    }

    /**
     * sets total amount
     * @param int|float $total_amount Total amount as stated in XML
     */
    public function set_total_amount($total_amount)
    {

        if (!is_int($total_amount) && !is_float($total_amount)) {
            throw new OpentransException('$total_amount must be integer or float.');
        }

        $this->total_amount = $total_amount;
    }

}
