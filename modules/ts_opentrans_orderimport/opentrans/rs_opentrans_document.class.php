<?php

    /**
     * Opentrans Document
     *
     * Creates openTrans document
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
    class rs_opentrans_document {

        /**
         * Constants
         */
        /**
         * Version opentrans
         *
         * @var const integer
         */
        const VERSION = 2.1;

        /**
         * Version orderapi
         *
         * @var const integer
         */
        const VERSION_ORDERAPI = 1.6;

        /**
         *  openTrans documenttype ORDER
         *
         * @var const string
         */
        const DOCUMENT_TYPE_ORDER = 'order';
        /**
         *  openTrans documenttype ORDERCHANGE
         *
         * @var const string
         */
        const DOCUMENT_TYPE_ORDERCHANGE = 'orderchange';
        /**
         *  openTrans documenttype DISPATCHNOTIFICATION
         *
         * @var const string
         */
        const DOCUMENT_TYPE_DISPATCHNOTIFICATION = 'dispatchnotification';

        /**
         *  openTrans defined and valid documenttypes
         *
         * @var static array
         */
        static $valid_document_types = array(
            self::DOCUMENT_TYPE_ORDER,
            self::DOCUMENT_TYPE_ORDERCHANGE,
            self::DOCUMENT_TYPE_DISPATCHNOTIFICATION
        );

        /**
         * documenttype
         *
         * @var string
         */
        protected $document_type = NULL;

        /**
         * Header
         *
         * @var rs_opentrans_document_header
         */
        protected $header = NULL;

        /**
         * Itemlist
         *
         * @var array
         */
        protected $item_list = NULL;

        /**
         * Summary
         *
         * @var rs_opentrans_document_summary
         */
        protected $summary = NULL;

        /**
         * Construct a openTrans document
         *
         * @param string
         */
        public function __construct($document_type) {

            if (!is_string($document_type)) {
                throw new rs_opentrans_exception('$document_type must be a string.');
            }

            if (!in_array($document_type, self::$valid_document_types)) {
                throw new rs_opentrans_exception('Unsupported document type "' . $document_type . '".');
            }

            $this->document_type = $document_type;

        }

        /**
         * Create header of openTrans document
         *
         * @return rs_opentrans_document_header
         */
        public function create_header() {

            $this->header = new rs_opentrans_document_header();

            return $this->header;

        }

        /**
         * Add Item to Item_list
         *
         * @param item
         */
        public function add_item($item) {

            if (!$item instanceof rs_opentrans_document_item) {
                throw new rs_opentrans_exception('Item must be type of rs_opentrans_document_orderitem');
            }

            $this->item_list[] = $item;
            $item->set_line_item_id($this->summary->get_total_item_num());
            $this->summary->add_item($item->get_price_line_amount());

        }

        /**
         * Returns document type
         *
         * @return string
         */
        public function get_document_type() {
            return $this->document_type;
        }

        /**
         * Returns document header
         *
         * @return rs_opentrans_document_header
         */
        public function get_header() {
            return $this->header;
        }

        /**
         * Returns item lsit
         *
         * @return array()
         */
        public function get_item_list() {
            return $this->item_list;
        }

        /**
         * Returns summary
         *
         * @return rs_opentrans_document_summary
         */
        public function get_summary() {
            return $this->summary;
        }

        /*
         * Returns version orderapi
         *
         * @return string version_orderapi
         */
        public function get_version_orderapi() {
            return self::VERSION_ORDERAPI;
        }

    }