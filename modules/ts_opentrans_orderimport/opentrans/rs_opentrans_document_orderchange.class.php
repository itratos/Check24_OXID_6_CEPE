<?php
    /**
     * Opentrans Document Orderchange
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
    class rs_opentrans_document_orderchange extends rs_opentrans_document {

        /**
         * @var object
         */
        protected $summary = NULL;

        /**
         * Construct a openTrans orderchange
         */
        public function __construct() {

            parent::__construct(self::DOCUMENT_TYPE_ORDERCHANGE);

            $this->summary = new rs_opentrans_document_summary_order(0);

        }

        /**
         * Creates a openTrans orderchange header
         *
         * @return object rs_opentrans_document_header_orderchange
         */
        public function create_header() {

            $this->header = new rs_opentrans_document_header_orderchange();

            return $this->header;

        }

        /**
         * Sets summary
         *
         * @param object $summary rs_opentrans_document_summary
         * @return void
         */
        public function set_summary($summary) {

            if (!$summary instanceof rs_opentrans_document_summary) {
                throw new rs_opentrans_exception('$summary must be type of rs_opentrans_document_summary');
            }

            $this->summary = $summary;

        }

    }