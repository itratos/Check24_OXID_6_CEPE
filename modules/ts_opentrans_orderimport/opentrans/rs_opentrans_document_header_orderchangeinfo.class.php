<?php
    /**
     * Opentrans Document Header orderchangeinfo
     *
     * @copyright Testsieger Portal AG 
     * 
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
    class rs_opentrans_document_header_orderchangeinfo extends rs_opentrans_document_header_documentinfo {

        /**
         * @var date
         */
        protected $orderchange_date = NULL;

        /**
         * @var id
         */
        protected $orderchange_sequence_id = 1;

        /**
         * Construct a openTrans orderchangeinfo
         *
         * @param string order_id
         * @param string order_date
         */
        public function __construct($order_id, $order_date) {

            if (!is_string($order_id)) {
                throw new rs_opentrans_exception('$order_id must be a string.');
            }

            if (!is_string($order_date)) {
                throw new rs_opentrans_exception('$order_date must be a string.');
            }

            parent::__construct($order_id, $order_date);

        }

        /**
         * Returns order_id
         *
         * @return string order_id
         */
        public function get_order_id() {
            return $this->get_document_id();
        }

        /**
         * Returns order date
         *
         * @return string orderchange_date
         */
        public function get_order_date() {
            return $this->get_document_date();
        }

        /**
         * Returns orderchange date
         *
         * @return string order_date
         */
        public function get_orderchange_date() {
            return $this->orderchange_date;
        }

        /**
         * Sets orderchange_date
         *
         * @param string $orderchange_date
         * @return void
         */
        public function set_orderchange_date($orderchange_date) {
            $this->orderchange_date = $orderchange_date;
        }

        /**
         * Returns sequence_id
         *
         * @return int
         */
        public function get_orderchange_sequence_id() {
            return $this->orderchange_sequence_id;
        }

    }