<?php
    /**
     * Opentrans Document Item
     *
     * Creates openTrans document Item for Itemlist
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
    class rs_opentrans_document_item {

        /**
         * @var string
         */
        private $line_item_id = NULL;

        /**
         * @var object
         */
        private $product_id = NULL;

        /**
         * @var array
         */
        private $product_features = array();

        /**
         * @var array
         */
        private $product_components = array();

        /**
         * @var string
         */
        private $quantity = NULL;

        /**
         * @var string UNECE-formated
         */
        private $order_unit = NULL;

        /**
         * @var string
         */
        private $product_price_fix = NULL;

        /**
         * @var object
         */
        private $tax_details_fix = NULL;

        /**
         * @var string
         */
        private $price_line_amount = NULL;

        /**
         * @var string
         */
        private $partial_shipment_allowed = NULL;

        /**
         * @var string
         */
        private $delivery_date = NULL;

        /**
         * @var string
         */
        private $partial_delivery_list = NULL;

        /**
         * @var string
         */
        private $sourcing_info = NULL;

        /**
         * @var string
         */
        private $customer_order_reference = NULL;

        /**
         * @var string
         */
        private $accounting_info = NULL;

        /**
         * @var string
         */
        private $shipment_parties_reference = NULL;

        /**
         * @var string
         */
        private $transport = NULL;

        /**
         * @var string
         */
        private $international_restrictions = NULL;

        /**
         * @var string
         */
        private $special_treatment_class = NULL;

        /**
         * @var string
         */
        private $mime_info = NULL;

        /**
         * @var array
         */
        protected $remarks = array();

        /**
         * Construct a openTrans item
         *
         * @param integer line_item_id
         */
        public function __construct($line_item_id = NULL) {

            if ($line_item_id !== NULL && !is_int($line_item_id)) {
                throw new rs_opentrans_exception('$line_item_id must be integer.');
            }

            $this->line_item_id = $line_item_id;

        }

        /**
         * Sets product_id
         *
         * @param object $product_id rs_opentrans_document_item_productid
         * @return void
         */
        public function set_product_id($product_id) {

            if (!$product_id instanceof rs_opentrans_document_item_productid) {
                throw new rs_opentrans_exception('$product_id must be type of rs_opentrans_document_item_productid');
            }

            $this->product_id = $product_id;

        }

        /**
         * Pseudo adds feature
         *
         * @return exception
         */
        public function add_feature() {
            throw new rs_opentrans_exception('Features not supported, yet');
        }

        /**
         * Pseudo adds components
         *
         * @return exception
         */
        public function add_components() {
            throw new rs_opentrans_exception('Components not supported, yet');
        }

        /**
         * Sets quantity
         *
         * @param integer quantity
         * @return void
         */
        public function set_quantity($quantity) {

            if ((int)$quantity == 0) {
                throw new rs_opentrans_exception('$quantity must be greater zero.');
            }

            $this->quantity = $quantity;

        }

        /**
         * Adds remark
         *
         * @param string type
         * @param string value
         * @return void
         */
        public function add_remark($type, $value) {

            if (!is_string($type)) {
                throw new rs_opentrans_exception('$type must be a string.');
            }

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->remarks[$type] = $value;

        }

        /**
         * Sets order_unit
         *
         * @param string order_unit
         * @return void
         */
        public function set_order_unit($order_unit) {

            if (!is_string($order_unit)) {
                throw new rs_opentrans_exception('$order_unit must be a string.');
            }

            $this->order_unit = $order_unit;

        }

        /**
         * Sets product_price_fix
         *
         * @param object product_price_fix rs_opentrans_document_item_productpricefix
         * @return void
         */
        public function set_product_price_fix($product_price_fix = NULL) {

            if (!$product_price_fix instanceof rs_opentrans_document_item_productpricefix) {
                throw new rs_opentrans_exception('$product_price_fix must be type of rs_opentrans_document_item_pricefix');
            }

            $this->product_price_fix = $product_price_fix;

        }


        /**
         * Sets tax_details_fix
         *
         * @param object tax_details_fix rs_opentrans_document_item_taxdetailsfix
         * @return void
         */
        public function set_tax_details_fix($tax_details_fix = NULL) {

            if (!$tax_details_fix instanceof rs_opentrans_document_item_taxdetailsfix) {
                throw new rs_opentrans_exception('$tax_details_fix must be type of rs_opentrans_document_item_taxdetailsfix');
            }

            $this->tax_details_fix = $tax_details_fix;

        }

        /**
         * Sets price_line_amount
         *
         * @param integer|float price_line_amount
         * @return void
         */
        public function set_price_line_amount($amount) {
            
            if (!is_int($amount) && !is_float($amount)) {
                throw new rs_opentrans_exception('$amount must be integer or float, not ' . gettype($amount));
            }

            $this->price_line_amount = $amount;

        }

        /**
         * Sets line_item_id
         *
         * @param integer line_item_id
         * @return void
         */
        public function set_line_item_id($line_item_id) {

            if (!is_int($line_item_id)) {
                throw new rs_opentrans_exception('$line_item_id must be integer.');
            }

            $this->line_item_id = $line_item_id;

        }

        /**
         * Returns line_item_id
         *
         * @return string line_item_id
         */
        public function get_line_item_id() {
            return $this->line_item_id;
        }

        /**
         * Returns product_id
         *
         * @return string product_id
         */
        public function get_product_id() {
            return $this->product_id;
        }

        /**
         * Returns product_features
         *
         * @return string product_features
         */
        public function get_product_features() {
            return $this->product_features;
        }

        /**
         * Returns product_components
         *
         * @return string product_components
         */
        public function get_product_components() {
            return $this->product_components;
        }

        /**
         * Returns quantity
         *
         * @return string quantity
         */
        public function get_quantity() {
            return $this->quantity;
        }

        /**
         * Returns order_unit
         *
         * @return string order_unit
         */
        public function get_order_unit() {
            return $this->order_unit;
        }

        /**
         * Returns product_price_fix
         *
         * @return string product_price_fix
         */
        public function get_product_price_fix() {
            return $this->product_price_fix;
        }

        /**
         * Returns tax_details_fix
         *
         * @return string tax_details_fix
         */
        public function get_tax_details_fix() {
            return $this->tax_details_fix;
        }

        /**
         * Returns price_line_amount
         *
         * @return string price_line_amount
         */
        public function get_price_line_amount() {
            return $this->price_line_amount;
        }

        /**
         * Returns partial_shipment_allowed
         *
         * @return string partial_shipment_allowed
         */
        public function get_partial_shipment_allowed() {
            return $this->partial_shipment_allowed;
        }

        /**
         * Returns delivery_date
         *
         * @return string delivery_date
         */
        public function get_delivery_date() {
            return $this->delivery_date;
        }


        /**
         * Returns partial_delivery_list
         *
         * @return string partial_delivery_list
         */
        public function get_partial_delivery_list() {
            return $this->partial_delivery_list;
        }

        /**
         * Returns sourcing_info
         *
         * @return string sourcing_info
         */
        public function get_sourcing_info() {
            return $this->sourcing_info;
        }

        /**
         * Returns customer_order_reference
         *
         * @return string customer_order_reference
         */
        public function get_customer_order_reference() {
            return $this->customer_order_reference;
        }

        /**
         * Returns accounting_info
         *
         * @return string accounting_info
         */
        public function get_accounting_info() {
            return $this->accounting_info;
        }

        /**
         * Returns shipment_parties_reference
         *
         * @return string shipment_parties_reference
         */
        public function get_shipment_parties_reference() {
            return $this->shipment_parties_reference;
        }

        /**
         * Returns transport
         *
         * @return string transport
         */
        public function get_transport() {
            return $this->transport;
        }

        /**
         * Returns international_restrictions
         *
         * @return string international_restrictions
         */
        public function get_international_restrictions() {
            return $this->international_restrictions;
        }

        /**
         * Returns special_treatment_class
         *
         * @return string special_treatment_class
         */
        public function get_special_treatment_class() {
            return $this->special_treatment_class;
        }

        /**
         * Returns mime_info
         *
         * @return string mime_info
         */
        public function get_mime_info() {
            return $this->mime_info;
        }

        /**
         * Returns remarks
         *
         * @return array remarks
         */
        public function get_remarks() {
            return $this->remarks;
        }

    }