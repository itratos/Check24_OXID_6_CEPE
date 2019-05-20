<?php
    /**
     * Opentrans Document Header Orderinfo
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
    class rs_opentrans_document_header_documentinfo {

        /**#@+
         * Constants
         */
        /**
         * DELIVERY_DATE_TYPE_FIXED
         * 
         * @var const string
         */
        const DELIVERY_DATE_TYPE_FIXED = 'fixed';

        /**
         * DELIVERY_DATE_TYPE_OPTIONAL
         * 
         * @var const string
         */
        const DELIVERY_DATE_TYPE_OPTIONAL = 'optional';

        /**
         * REMARK_TYPE_DELIVERYNOTE
         * 
         * @var const string
         */
        const REMARK_TYPE_DELIVERYNOTE = 'deliverynote';

        /**
         * REMARK_TYPE_DISPATCHNOTIFICATION
         * 
         * @var const string
         */
        const REMARK_TYPE_DISPATCHNOTIFICATION = 'dispatchnotification';

        /**
         * REMARK_TYPE_GENERAL
         * 
         * @var const string
         */
        const REMARK_TYPE_GENERAL = 'general';

        /**
         * REMARK_TYPE_INVOICE
         * 
         * @var const string
         */
        const REMARK_TYPE_INVOICE = 'invoice';

        /**
         * REMARK_TYPE_ORDER
         * 
         * @var const string
         */
        const REMARK_TYPE_ORDER = 'order';

        /**
         * REMARK_TYPE_ORDERCHANGE
         * 
         * @var const string
         */
        const REMARK_TYPE_ORDERCHANGE = 'orderchange';

        /**
         * REMARK_TYPE_ORDERRESPONSE
         * 
         * @var const string
         */
        const REMARK_TYPE_ORDERRESPONSE = 'orderresponse';

        /**
         * REMARK_TYPE_QUOTATION
         * 
         * @var const string
         */
        const REMARK_TYPE_QUOTATION = 'quotation';

        /**
         * REMARK_TYPE_RECEIPTACKNOWLEDGEMENT
         * 
         * @var const string
         */
        const REMARK_TYPE_RECEIPTACKNOWLEDGEMENT = 'receiptacknowledgement';

        /**
         * REMARK_TYPE_REMITTANCEADVICE
         * 
         * @var const string
         */
        const REMARK_TYPE_REMITTANCEADVICE = 'remittanceadvice';

        /**
         * REMARK_TYPE_INVOICELIST
         * 
         * @var const string
         */
        const REMARK_TYPE_INVOICELIST = 'invoicelist';

        /**
         * REMARK_TYPE_RFQ
         * 
         * @var const string
         */
        const REMARK_TYPE_RFQ = 'rfq';

        /**
         * REMARK_TYPE_TRANSPORT
         * 
         * @var const string
         */
        const REMARK_TYPE_TRANSPORT = 'transport';

        /**
         * openTrans defined and valid delivery date types
         * 
         * @var static array
         */
        static $valid_delivery_date_types = array(
            self::DELIVERY_DATE_TYPE_FIXED,
            self::DELIVERY_DATE_TYPE_OPTIONAL
        );

        /**
         * @var string
         */
        protected $document_id = NULL;

        /**
         * @var string
         */
        protected $document_date = NULL;

        /**
         * @var array
         */
        protected $parties = array();

        /**
         * @var array
         */
        protected $idrefs = array();

        /**
         * @var string
         */
        protected $delivery_date_start = NULL;

        /**
         * @var string
         */
        protected $delivery_date_end = NULL;

        /**
         * @var string
         */
        protected $delivery_date_type = NULL;

        /**
         * @var array
         */
        protected $remarks = array();

        /**
         * Construct a openTrans document info
         *
         * @param string document_id 
         * @param string document_date 
         */
        public function __construct($document_id, $document_date) {

            if (!is_string($document_id)) {
                throw new rs_opentrans_exception('$document_id must be a string.');
            }

            if (!is_string($document_date)) {
                throw new rs_opentrans_exception('$document_date must be a string.');
            }

            $this->document_id = $document_id;
            $this->document_date = $document_date;

        }

        /**
         * Adds party and sets idref
         *
         * @param object $party rs_opentrans_document_party
         * @return void
         */
        public function add_party($party) {

            if (!$party instanceof rs_opentrans_document_party) {
                throw new rs_opentrans_exception('$party must be type of rs_opentrans_document_party.');
            }

            $this->parties[] = $party;
            $this->idrefs[] = new rs_opentrans_document_idref($party->get_id()->get_id(), $party->get_role());

        }


        /**
         * Returns parties
         *
         * @return array parties
         */
        public function get_parties() {
            return $this->parties;
        }

        /**
         * Sets delivery_date_type, delivery_date_start, delivery_date_end
         *
         * @param string $start
         * @param string $end
         * @param string $type
         * @return void
         */
        public function set_delivery_date($start, $end, $type = NULL) {

            if (!is_string($start)) {
                throw new rs_opentrans_exception('$start must be a string.');
            }

            if (!is_string($end)) {
                throw new rs_opentrans_exception('$end must be a string.');
            }

            if ($type !== NULL && !is_string($type)) {
                throw new rs_opentrans_exception('$type must be a string.');
            } else if (!in_array($type, self::$valid_delivery_date_types)) {
            	throw new rs_opentrans_exception('$type is no valid type.');
            }

            $this->delivery_date_type = $type;
            $this->delivery_date_start = $start;
            $this->delivery_date_end = $end;

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
         * Returns document_id
         *
         * @return string document_id
         */
        public function get_document_id() {
            return $this->document_id;
        }

        /**
         * Returns document_date
         *
         * @return string document_date
         */
        public function get_document_date() {
            return $this->document_date;
        }

        /**
         * Returns remarks
         *
         * @return array remarks
         */
        public function get_remarks() {
            return $this->remarks;
        }

        
        /**
         * Returns value of idref
         *
         * @param string type
         * @return string
         */
        public function get_idref($type) {

            if (!is_string($type)) {
                throw new rs_opentrans_exception('$type must be a string.');
            } else if (!in_array($type, rs_opentrans_document_idref::$valid_types)) {
            	throw new rs_opentrans_exception('Unsupported idref type "' . $type . '".');
            }

            for ($i = 0; $i < count($this->idrefs); $i++) {

                if ($this->idrefs[$i]->get_type() == $type) {
                	return $this->idrefs[$i]->get_id();
                }

            }

            return false;

        }

    }