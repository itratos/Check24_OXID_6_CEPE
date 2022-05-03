<?php
/**
 * openTrans Document Item Productpricefix
 *
 * @copyright Testsieger Portal AG
 * @license GPL 3:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Testsieger.de OpenTrans Connector
 */

namespace TestSieger\Check24Connector\Application\Model;

class OpentransDocumentItemProductpricefix
{

    /**#@+
     * Constants
     */
    /**
     * PRICE_FLAG_TYPE_INCL_ASSURANCE
     *
     * @var const string
     */
    const PRICE_FLAG_TYPE_INCL_ASSURANCE = 'incl_assurance';

    /**
     * PRICE_FLAG_TYPE_INCL_DUTY
     *
     * @var const string
     */
    const PRICE_FLAG_TYPE_INCL_DUTY = 'incl_duty';

    /**
     * PRICE_FLAG_TYPE_INCL_FREIGHT
     *
     * @var const string
     */
    const PRICE_FLAG_TYPE_INCL_FREIGHT = 'incl_freight';

    /**
     * PRICE_FLAG_TYPE_INCL_INSURANCE
     *
     * @var const string
     */
    const PRICE_FLAG_TYPE_INCL_INSURANCE = 'incl_insurance';

    /**
     * PRICE_FLAG_TYPE_INCL_PACKING
     *
     * @var const string
     */
    const PRICE_FLAG_TYPE_INCL_PACKING = 'incl_packing';

    /**
     * @var string
     */
    private $price_amount = NULL;

    /**
     * @var string
     */
    private $allow_or_charges_fix = NULL;

    /**
     * @var array
     */
    private $price_flag = array();

    /**
     * @var string
     */
    private $tax_details_fix = NULL;

    /**
     * @var string
     */
    private $price_quantity = NULL;

    /**
     * @var string
     */
    private $price_base_fix = NULL;

    /**
     * Construct a openTrans productpricefix
     *
     * @param string price_amount
     * @param string allow_or_charges_fix
     * @param array price_flag
     * @param string tax_details_fix
     * @param string price_quantity
     * @param string price_base_fix
     */
    public function __construct(
        $price_amount = NULL,
        $allow_or_charges_fix = NULL,
        $price_flag = array(),
        $tax_details_fix = NULL,
        $price_quantity = NULL,
        $price_base_fix = NULL
    )
    {
        if ($price_amount !== NULL && !is_string($price_amount)) {
            throw new OpentransException('$price_amount must be a string.');
        }

        if ($allow_or_charges_fix !== NULL && !is_string($allow_or_charges_fix)) {
            throw new OpentransException('$allow_or_charges_fix must be a string.');
        }

        if (!is_array($price_flag)) {
            throw new OpentransException('$price_flag must be an array.');
        }

        if ($tax_details_fix !== NULL && !is_string($tax_details_fix)) {
            throw new OpentransException('$tax_details_fix must be a string.');
        }

        if ($price_quantity !== NULL && !is_string($price_quantity)) {
            throw new OpentransException('$price_quantity must be a string.');
        }

        if ($price_base_fix !== NULL && !is_string($price_base_fix)) {
            throw new OpentransException('$price_quantity must be a string.');
        }

        $this->price_amount = $price_amount;
        $this->allow_or_charges_fix = $allow_or_charges_fix;

        if (count($price_flag) > 0) {
            $this->price_flag = $price_flag;
        }

        $this->tax_details_fix = $tax_details_fix;
        $this->price_quantity = $price_quantity;
        $this->price_base_fix = $price_base_fix;
    }

    /**
     * Returns price_amount
     *
     * @return string price_amount
     */
    public function get_price_amount()
    {
        return $this->price_amount;
    }

    /**
     * Returns allow_or_charges_fix
     *
     * @return string allow_or_charges_fix
     */
    public function get_allow_or_charges_fix()
    {
        return $this->allow_or_charges_fix;
    }

    /**
     * Returns price_flag
     *
     * @return array price_flag
     */
    public function get_price_flag()
    {
        return $this->price_flag;
    }

    /**
     * Returns tax_details_fix
     *
     * @return string tax_details_fix
     */
    public function get_tax_details_fix()
    {
        return $this->tax_details_fix;
    }

    /**
     * Returns price_quantity
     *
     * @return string price_quantity
     */
    public function get_price_quantity()
    {
        return $this->price_quantity;
    }

    /**
     * Returns price_base_fix
     *
     * @return string price_base_fix
     */
    public function get_price_base_fix()
    {
        return $this->price_base_fix;
    }

}