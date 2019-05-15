<?php
    /**
     * openTrans Document Party
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
    class rs_opentrans_document_party {

        /**#@+
         * Constants
         */
        /**
         * ROLE_BUYER
         *
         * @var const string
         */
        const ROLE_BUYER = 'buyer';

        /**
         * ROLE_CENTRAL_REGULATOR
         *
         * @var const string
         */
        const ROLE_CENTRAL_REGULATOR = 'central_regulator';

        /**
         * ROLE_CUSTOMER
         *
         * @var const string
         */
        const ROLE_CUSTOMER = 'customer';

        /**
         * ROLE_DELIVERER
         *
         * @var const string
         */
        const ROLE_DELIVERER = 'deliverer';

        /**
         * ROLE_DELIVERY
         *
         * @var const string
         */
        const ROLE_DELIVERY = 'delivery';

        /**
         * ROLE_DOCUMENT_CREATOR
         *
         * @var const string
         */
        const ROLE_DOCUMENT_CREATOR = 'document_creator';

        /**
         * ROLE_FINAL_DELIVERY
         *
         * @var const string
         */
        const ROLE_FINAL_DELIVERY = 'final_delivery';

        /**
         * ROLE_INTERMEDIARY
         *
         * @var const string
         */
        const ROLE_INTERMEDIARY = 'intermediary';

        /**
         * ROLE_INVOICE_ISSUER
         *
         * @var const string
         */
        const ROLE_INVOICE_ISSUER = 'invoice_issuer';

        /**
         * ROLE_INVOICE_RECIPIENT
         *
         * @var const string
         */
        const ROLE_INVOICE_RECIPIENT = 'invoice';

        /**
         * ROLE_IPP_OPERATOR
         *
         * @var const string
         */
        const ROLE_IPP_OPERATOR = 'ipp_operator';

        /**
         * ROLE_MANUFACTURER
         *
         * @var const string
         */
        const ROLE_MANUFACTURER = 'manufacturer';

        /**
         * ROLE_MARKETPLACE
         *
         * @var const string
         */
        const ROLE_MARKETPLACE = 'marketplace';

        /**
         * ROLE_PAYER
         *
         * @var const string
         */
        const ROLE_PAYER = 'payer';

        /**
         * ROLE_REMITTEE
         *
         * @var const string
         */
        const ROLE_REMITTEE = 'remittee';

        /**
         * ROLE_STANDARDIZATION_BODY
         *
         * @var const string
         */
        const ROLE_STANDARDIZATION_BODY = 'standardization_body';

        /**
         * ROLE_SUPPLIER
         *
         * @var const string
         */
        const ROLE_SUPPLIER = 'supplier';

        /**
         * ROLE_TRUSTEDTHIRDPARTY
         *
         * @var const string
         */
        const ROLE_TRUSTEDTHIRDPARTY = 'trustedthirdparty';

        /**
         * ROLE_OTHER
         *
         * @var const string
         */
        const ROLE_OTHER = 'other';

        /**
         * openTrans defined and valid partyroles
         *
         * @var static array
         */
        static $valid_roles = array(
            self::ROLE_BUYER,
            self::ROLE_CENTRAL_REGULATOR,
            self::ROLE_CUSTOMER,
            self::ROLE_DELIVERER,
            self::ROLE_DELIVERY,
            self::ROLE_DOCUMENT_CREATOR,
            self::ROLE_FINAL_DELIVERY,
            self::ROLE_INTERMEDIARY,
            self::ROLE_INVOICE_ISSUER,
            self::ROLE_INVOICE_RECIPIENT,
            self::ROLE_IPP_OPERATOR,
            self::ROLE_MANUFACTURER,
            self::ROLE_MARKETPLACE,
            self::ROLE_PAYER,
            self::ROLE_REMITTEE,
            self::ROLE_STANDARDIZATION_BODY,
            self::ROLE_SUPPLIER,
            self::ROLE_TRUSTEDTHIRDPARTY,
            self::ROLE_OTHER
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
        protected $role = NULL;

        /**
         * @var Object (rs_opentrans_document_address)
         */
        protected $address = NULL;

        /**
         * @var array
         */
        protected $remarks = array();

        /**
         * Construct a openTrans party
         *
         * @param string id
         * @param string id_type
         * @param string role
         */
        public function __construct($id, $id_type, $role = NULL) {

            if (!is_string($id)) {
                throw new rs_opentrans_exception('$id must be a string.');
            }

            if (!is_string($id_type)) {
                throw new rs_opentrans_exception('$id_type must be a string.');
            }

            if ($id === NULL) {
                throw new rs_opentrans_exception('Party ID must not be empty.');
            }

            $this->id = new rs_opentrans_document_partyid($id, $id_type);

            if ($role !== NULL && !is_string($role)) {
                throw new rs_opentrans_exception('$role must be a string.');
            } else if (!in_array($role, self::$valid_roles)) {
                throw new rs_opentrans_exception('$role ist not a valid party role.');
            }

            $this->role = $role;

        }

        /**
         * Sets address
         *
         * @param object $address rs_opentrans_document_address
         * @return void
         */
        public function set_address($address) {

            if (!$address instanceof rs_opentrans_document_address) {
                throw new rs_opentrans_exception('Address must be an instance of rs_opentrans_document_address');
            }

            $this->address = $address;

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
         * Returns id_type
         *
         * @return string id_type
         */
        public function get_id_type() {
            return $this->id_type;
        }

        /**
         * Returns role
         *
         * @return string role
         */
        public function get_role() {
            return $this->role;
        }

        /**
         * Returns address
         *
         * @return object address
         */
        public function get_address() {
            return $this->address;
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
         * Returns remarks
         *
         * @return array remarks
         */
        public function get_remarks() {
            return $this->remarks;
        }

    }
