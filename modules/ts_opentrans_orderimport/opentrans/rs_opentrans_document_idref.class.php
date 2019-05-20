<?php
    /**
     * Opentrans Document IDREF
     *
     * Creates IDREF for Party
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
    class rs_opentrans_document_idref {

        /**#@+
         * Constants
         */
        /**
         * TYPE_SUPPLIER_IDREF
         *
         * @var const string
         */
        const TYPE_SUPPLIER_IDREF = 'supplier_idref';

        /**
         * TYPE_BUYER_IDREF
         *
         * @var const string
         */
        const TYPE_BUYER_IDREF = 'buyer_idref';

        /**
         * TYPE_DELIVERY_IDREF
         *
         * @var const string
         */
        const TYPE_DELIVERY_IDREF = 'delivery_idref';

        /**
         * TYPE_DELIVERER_IDREF
         *
         * @var const string
         */
        const TYPE_DELIVERER_IDREF = 'deliverer_idref';

        /**
         * TYPE_INVOICE_RECIPIENT_IDREF
         *
         * @var const string
         */
        const TYPE_INVOICE_RECIPIENT_IDREF = 'invoice_recipient_idref';

        /**
         * openTrans defined and valid idref types
         *
         * @var static array
         */
        static $valid_types = array(
            self::TYPE_SUPPLIER_IDREF,
            self::TYPE_BUYER_IDREF,
            self::TYPE_DELIVERY_IDREF,
            self::TYPE_INVOICE_RECIPIENT_IDREF,
            self::TYPE_DELIVERER_IDREF
        );

        /**
         * openTrans defined and valid party roles
         *
         * @var static array
         */
        static $valid_roles = array(
            rs_opentrans_document_party::ROLE_DELIVERY,
            rs_opentrans_document_party::ROLE_DELIVERER,
            rs_opentrans_document_party::ROLE_INVOICE_ISSUER,
            rs_opentrans_document_party::ROLE_INVOICE_RECIPIENT
        );

        /**
         * @var string
         */
        protected $id = NULL;

        /**
         * @var string
         */
        protected $type = NULL;

        /**
         * Construct a openTrans idref
         *
         * @param string id
         * @param string role
         */
        public function __construct($id, $role) {

            if (!is_string($id)) {
                throw new rs_opentrans_exception('$id must be a string.');
            }

            if (!is_string($role)) {
                throw new rs_opentrans_exception('$role must be a string.');
            } else if (!in_array($role, self::$valid_roles)) {
                throw new rs_opentrans_exception('$role is not a supported idref role.');
            }

            $this->id = $id;

            switch ($role) {

                case rs_opentrans_document_party::ROLE_DELIVERY:
                    $this->type = self::TYPE_DELIVERY_IDREF;
                    break;

                case rs_opentrans_document_party::ROLE_INVOICE_ISSUER:
                    $this->type = self::TYPE_SUPPLIER_IDREF;
                    break;

                case rs_opentrans_document_party::ROLE_INVOICE_RECIPIENT:
                    $this->type = self::TYPE_INVOICE_RECIPIENT_IDREF;
                    break;

                case rs_opentrans_document_party::ROLE_DELIVERER:
                    $this->type = self::TYPE_DELIVERER_IDREF;
                    break;

            }

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