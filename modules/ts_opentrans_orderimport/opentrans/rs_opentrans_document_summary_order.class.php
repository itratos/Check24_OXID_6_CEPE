<?php
    /**
     * Opentrans Document Summary for ORDER
     *
     * Creates summary for openTrans document of type order
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
    class rs_opentrans_document_summary_order extends rs_opentrans_document_summary {

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
        public function __construct($total_item_num = 0, $total_amount = 0) {

            if (!is_int($total_item_num)) {
                throw new rs_opentrans_exception('$total_item_num must be integer.');
            }

            if (!is_int($total_amount) && !is_float($total_amount)) {
                throw new rs_opentrans_exception('$total_amount must be integer or float.');
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
        public function add_item($price_line_amount = 0) {

            if (!is_int($price_line_amount) && !is_float($price_line_amount)) {
                throw new rs_opentrans_exception('$price_line_amount must be integer or float.');
            }

            parent::add_item();

            $this->total_amount += $price_line_amount;

        }

        /**
         * Returns total_amount
         *
         * @return string total_amount
         */
        public function get_total_amount() {
            return $this->total_amount;
        }
        
        /**
         * sets total amount
         * @param int|float $total_amount Total amount as stated in XML
         */
        public function set_total_amount($total_amount) {

            if (!is_int($total_amount) && !is_float($total_amount)) {
                throw new rs_opentrans_exception('$total_amount must be integer or float.');
            }

            $this->total_amount = $total_amount;
            
        }

    }
