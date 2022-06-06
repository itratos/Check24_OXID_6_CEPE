<?php

namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentItemTaxdetailsfix
{

    /**#@+
     * Constants
     */
    /**
     * TAX_CATEGORY_EXEMPTION
     *
     * @var const string
     */
    const TAX_CATEGORY_EXEMPTION = 'exemption';

    /**
     * TAX_CATEGORY_PARKING_RATE
     *
     * @var const string
     */
    const TAX_CATEGORY_PARKING_RATE = 'parking_rate';

    /**
     * TAX_CATEGORY_REDUCED_RATE
     *
     * @var const string
     */
    const TAX_CATEGORY_REDUCED_RATE = 'reduced_rate';

    /**
     * TAX_CATEGORY_STANDARD_RATE
     *
     * @var const string
     */
    const TAX_CATEGORY_STANDARD_RATE = 'standard_rate';

    /**
     * TAX_CATEGORY_SUPER_REDUCED_RATE
     *
     * @var const string
     */
    const TAX_CATEGORY_SUPER_REDUCED_RATE = 'super_reduced_rate';

    /**
     * TAX_CATEGORY_ZERO_RATE
     *
     * @var const string
     */
    const TAX_CATEGORY_ZERO_RATE = 'zero_rate';

    /**
     * TAX_TYPE_VAT
     *
     * @var const string
     */
    const TAX_TYPE_VAT = 'VAT';

    /**
     * @var integer
     */
    private $calculation_sequence = NULL;

    /**
     * @var string
     */
    private $tax_category = NULL;

    /**
     * @var array
     */
    private $tax_type = array();

    /**
     * @var integer|float
     */
    private $tax = NULL;

    /**
     * @var string
     */
    private $tax_amount = NULL;

    /**
     * @var string
     */
    private $tax_base = NULL;

    /**
     * @var string
     */
    private $exemption_reason = NULL;

    /**
     * @var string
     */
    private $jurisdiction = NULL;


    /**
     * Construct a openTrans party
     *
     * @param integer calculation_sequence
     * @param string tax_category
     * @param array tax_type
     * @param integer|float tax
     * @param string tax_amount
     * @param string tax_base
     * @param string exemption_reason
     * @param string jurisdiction
     */
    public function __construct(
        $calculation_sequence = NULL,
        $tax_category = NULL,
        $tax_type = array(),
        $tax = NULL,
        $tax_amount = NULL,
        $tax_base = NULL,
        $exemption_reason = NULL,
        $jurisdiction = NULL
    )
    {
        if ($calculation_sequence !== NULL && !is_int($calculation_sequence)) {
            throw new OpentransException('$calculation_sequence must be integer.');
        }

        if ($tax_category !== NULL && !is_string($tax_category)) {
            throw new OpentransException('$tax_category must be string.');
        }
        if (!is_array($tax_type)) {
            throw new OpentransException('$tax_type must be an array.');
        }

        if ($tax !== NULL && !is_numeric($tax)) {
            throw new OpentransException('$tax must be a number.');
        }

        if ($tax_amount !== NULL && !is_string($tax_amount)) {
            throw new OpentransException('$tax_amount must be string.');
        }

        if ($tax_base !== NULL && !is_string($tax_base)) {
            throw new OpentransException('$tax_base must be string.');
        }

        if ($exemption_reason !== NULL && !is_string($exemption_reason)) {
            throw new OpentransException('$exemption_reason must be string.');
        }

        if ($jurisdiction !== NULL && !is_string($jurisdiction)) {
            throw new OpentransException('$jurisdiction must be string.');
        }

        $this->tax_category = $tax_category;
        $this->tax_type = $tax_type;
        $this->tax = $tax;
        $this->tax_amount = $tax_amount;
        $this->tax_base = $tax_base;
        $this->exemption_reason = $exemption_reason;
        $this->jurisdiction = $jurisdiction;
    }

    /**
     * Returns tax_category
     *
     * @return string tax_category
     */
    public function get_tax_category()
    {
        return $this->tax_category;
    }

    /**
     * Returns tax_type
     *
     * @return array tax_type
     */
    public function get_tax_type()
    {
        return $this->tax_type;
    }

    /**
     * Returns tax
     *
     * @return string tax
     */
    public function get_tax()
    {
        return $this->tax;
    }

    /**
     * Returns tax_amount
     *
     * @return string tax_amount
     */
    public function get_tax_amount()
    {
        return $this->tax_amount;
    }

    /**
     * Returns tax_base
     *
     * @return string tax_base
     */
    public function get_tax_base()
    {
        return $this->tax_base;
    }

    /**
     * Returns exemption_reason
     *
     * @return string exemption_reason
     */
    public function get_exemption_reason()
    {
        return $this->exemption_reason;
    }

    /**
     * Returns jurisdiction
     *
     * @return string jurisdiction
     */
    public function get_jurisdiction()
    {
        return $this->jurisdiction;
    }

}