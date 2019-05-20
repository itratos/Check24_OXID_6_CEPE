<?php
    /**
     * Opentrans Document PartyID
     *
     * Creates openTrans document partyid
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
    class rs_opentrans_document_partyid {

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
         * TYPE_CUSTOMER_SPECIFIC
         * 
         * @var const string
         */
        const TYPE_CUSTOMER_SPECIFIC = 'customer_specific';

        /**
         * TYPE_DUNS
         * 
         * @var const string
         */
        const TYPE_DUNS = 'duns';

        /**
         * TYPE_ILN
         * 
         * @var const string
         */
        const TYPE_ILN = 'iln';

        /**
         * TYPE_GLN
         * 
         * @var const string
         */
        const TYPE_GLN = 'gln';

        /**
         * TYPE_PARTY_SPECIFIC
         * 
         * @var const string
         */
        const TYPE_PARTY_SPECIFIC = 'party_specific';

        /**
         * TYPE_SUPPLIER_SPECIFIC
         * 
         * @var const string
         */
        const TYPE_SUPPLIER_SPECIFIC = 'supplier_specific';

        /**
         * openTrans defined and valid partyid types
         * 
         * @var static array
         */
        static $valid_types = array(
            self::TYPE_BUYER_SPECIFIC,
            self::TYPE_CUSTOMER_SPECIFIC,
            self::TYPE_DUNS,
            self::TYPE_ILN,
            self::TYPE_GLN,
            self::TYPE_PARTY_SPECIFIC,
            self::TYPE_SUPPLIER_SPECIFIC
        );

        /**
         * Party-ID
         * 
         * ID needs to be unique and is expected to start with TS-
         * followed by:
         * DA- user_id() - adress_id() for Deliveryaddress
         * BA- user_id() - adress_id() for Billaddress
         * SA- shop_id() for Shopaddress
         * 
         * @var string
         */
        protected $id = NULL;

        /**
         * @var string
         */
        protected $type = NULL;

        /**
         * Construct a openTrans partyid
         *
         * @param string id 
         * @param string type 
         */
        public function __construct($id, $type) {

            if (!is_string($id)) {
                throw new rs_opentrans_exception('$id must be a string.');
            }

            if (!is_string($type)) {
                throw new rs_opentrans_exception('$type must be a string.');
            } else if (!in_array($type, self::$valid_types)) {

                if (!preg_match('/^\w{1,250}$/', $type)) {
                    throw new rs_opentrans_exception('Type must be \w{1,250}.');
                }

            }

            $this->id = $id;
            $this->type = $type;

        }

        /**
         * Returns id
         *
         * @return string id
         */
        public function get_id() {
            return $this->id;
        }

        /**
         * Returns type
         *
         * @return string type
         */
        public function get_type() {
            return $this->type;
        }

    }