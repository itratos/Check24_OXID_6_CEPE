<?php
/**
 * openTrans Document Item Productid
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

class OpentransDocumentItemProductid
{

    /**#@+
     * Constants
     */
    /**
     * TYPE_BUYER_SPECIFIC
     *
     * @var const string
     */
    const TYPE_BUYER_SPECIFIC = 'buyer_specific';

    /**
     * TYPE_EAN
     *
     * @var const string
     */
    const TYPE_EAN = 'ean';

    /**
     * TYPE_GTIN
     *
     * @var const string
     */
    const TYPE_GTIN = 'gtin';

    /**
     * TYPE_SUPPLIER_SPECIFIC
     *
     * @var const string
     */
    const TYPE_SUPPLIER_SPECIFIC = 'supplier_specific';

    /**
     * TYPE_UPC
     *
     * @var const string
     */
    const TYPE_UPC = 'upc';

    /**
     * openTrans defined and valid productid types
     *
     * @var static array
     */
    static $valid_types = array(
        self::TYPE_BUYER_SPECIFIC,
        self::TYPE_EAN,
        self::TYPE_GTIN,
        self::TYPE_SUPPLIER_SPECIFIC,
        self::TYPE_UPC
    );

    /**
     * @var array
     */
    private $supplier_pid = array();

    /**
     * @var string
     */
    private $supplier_idref = NULL;

    /**
     * @var string
     */
    private $config_code_fix = NULL;

    /**
     * @var string
     */
    private $lot_number = NULL;

    /**
     * @var string
     */
    private $serial_number = NULL;

    /**
     * @var string
     */
    private $international_pid = array();

    /**
     * @var string
     */
    private $buyer_pid = array();

    /**
     * @var string
     */
    private $description_short = NULL;

    /**
     * @var string
     */
    private $description_long = NULL;

    /**
     * @var string
     */
    private $manufacturer_info = NULL;

    /**
     * @var string
     */
    private $product_type = NULL;

    /**
     * Construct a openTrans productid
     *
     * @param array supplier_pid
     * @param string supplier_idref
     * @param string config_code_fix
     * @param string lot_number
     * @param string serial_number
     * @param array international_pid
     * @param array buyer_pid
     * @param string description_short
     * @param string description_long
     * @param string manufacturer_info
     * @param string product_type
     */
    public function __construct(
        $supplier_pid = array(),
        $supplier_idref = NULL,
        $config_code_fix = NULL,
        $lot_number = NULL,
        $serial_number = NULL,
        $international_pid = array(),
        $buyer_pid = array(),
        $description_short = NULL,
        $description_long = NULL,
        $manufacturer_info = NULL,
        $product_type = NULL
    )
    {
        if (!is_array($supplier_pid)) {
            throw new OpentransException('$supplier_pid must be a array.');
        }

        if ($supplier_idref !== NULL && !is_string($supplier_idref)) {
            throw new OpentransException('$supplier_idref must be a string.');
        }

        if ($config_code_fix !== NULL && !is_string($config_code_fix)) {
            throw new OpentransException('$config_code_fix must be a string.');
        }

        if ($lot_number !== NULL && !is_string($lot_number)) {
            throw new OpentransException('$lot_number must be a string.');
        }

        if ($serial_number !== NULL && !is_string($serial_number)) {
            throw new OpentransException('$serial_number must be a string.');
        }

        if (!is_array($international_pid)) {
            throw new OpentransException('$international_pid must be a array.');
        }

        if (!is_array($buyer_pid)) {
            throw new OpentransException('$buyer_pid must be a array.');
        }

        if ($description_short !== NULL && !is_string($description_short)) {
            throw new OpentransException('$supplier_pid must be a string.');
        }

        if ($description_long !== NULL && !is_string($description_long)) {
            throw new OpentransException('$description_long must be a string.');
        }

        if ($manufacturer_info !== NULL && !is_string($manufacturer_info)) {
            throw new OpentransException('$manufacturer_info must be a string.');
        }

        if ($product_type !== NULL && !is_string($product_type)) {
            throw new OpentransException('$product_type must be a string.');
        }

        if (count($supplier_pid) > 0) {

            if (array_key_exists(0, $supplier_pid)) {
                $this->supplier_pid = $supplier_pid[0];
            }

            if (array_key_exists(1, $supplier_pid)) {
                // check_string($supplier_pid[1], self::$valid_types);
                $this->supplier_pid_type = $supplier_pid[1];
            }

        }

        $this->supplier_idref = $supplier_idref;
        $this->config_code_fix = $config_code_fix;
        $this->lot_number = $lot_number;
        $this->serial_number = $serial_number;

        if (count($international_pid) > 0) {

            if (array_key_exists(0, $international_pid)) {
                $this->international_pid = $international_pid[0];
            }

            if (array_key_exists(1, $international_pid)) {
                $this->international_pid_type = $international_pid[1];
            }

        }

        $this->buyer_pid = $buyer_pid;
        $this->description_short = $description_short;
        $this->description_long = $description_long;
        $this->manufacturer_info = $manufacturer_info;
        $this->product_type = $product_type;
    }

    /**
     * Returns supplier_pid
     *
     * @return array supplier_pid
     */
    public function get_supplier_pid()
    {
        return $this->supplier_pid;
    }

    /**
     * Returns supplier_idref
     *
     * @return string supplier_idref
     */
    public function get_supplier_idref()
    {
        return $this->supplier_idref;
    }

    /**
     * Returns config_code_fix
     *
     * @return string config_code_fix
     */
    public function get_config_code_fix()
    {
        return $this->config_code_fix;
    }

    /**
     * Returns lot_number
     *
     * @return string lot_number
     */
    public function get_lot_number()
    {
        return $this->lot_number;
    }

    /**
     * Returns serial_number
     *
     * @return string serial_number
     */
    public function get_serial_number()
    {
        return $this->serial_number;
    }

    /**
     * Returns international_pid
     *
     * @return array international_pid
     */
    public function get_international_pid()
    {
        return $this->international_pid;
    }

    /**
     * Returns buyer_pid
     *
     * @return array buyer_pid
     */
    public function get_buyer_pid()
    {
        return $this->buyer_pid;
    }

    /**
     * Returns description_short
     *
     * @return string description_short
     */
    public function get_description_short()
    {
        return $this->description_short;
    }

    /**
     * Returns description_long
     *
     * @return string description_long
     */
    public function get_description_long()
    {
        return $this->description_long;
    }

    /**
     * Returns manufacturer_info
     *
     * @return string manufacturer_info
     */
    public function get_manufacturer_info()
    {
        return $this->manufacturer_info;
    }

    /**
     * Returns product_type
     *
     * @return string product_type
     */
    public function get_product_type()
    {
        return $this->product_type;
    }

}