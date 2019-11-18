<?php
    /**
     * openTrans Document Payment Card
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
    class rs_opentrans_document_payment_card extends rs_opentrans_document_payment {

        /**#@+
         * Constants
         */
        /**
         * CARD_TYPE_MASTERCARD
         *
         * @var const string
         */
        const CARD_TYPE_MASTERCARD = 'MasterCard';

        /**
         * CARD_TYPE_VISA
         *
         * @var const string
         */
        const CARD_TYPE_VISA = 'VISA';

        /**
         * CARD_TYPE_AMERICANEXPRESS
         *
         * @var const string
         */
        const CARD_TYPE_AMERICANEXPRESS = 'AmericanExpress';

        /**
         * CARD_TYPE_DINERSCLUB
         *
         * @var const string
         */
        const CARD_TYPE_DINERSCLUB = 'DinersClub';

        /**
         * CARD_TYPE_JCB
         *
         * @var const string
         */
        const CARD_TYPE_JCB = 'JCB';

        /**
         * CARD_TYPE_MAESTRO
         *
         * @var const string
         */
        const CARD_TYPE_MAESTRO = 'Maestro';

        /**
         * CARD_TYPE_DISCOVERCARD
         *
         * @var const string
         */
        const CARD_TYPE_DISCOVERCARD = 'DiscoverCard';

        /**
         * CARD_TYPE_TRANSCARD
         *
         * @var const string
         */
        const CARD_TYPE_TRANSCARD = 'Transcard';

        /**
         * CARD_TYPE_DINACARD
         *
         * @var const string
         */
        const CARD_TYPE_DINACARD = 'DinaCard';

        /**
         * CARD_TYPE_CHINAUNIONPAY
         *
         * @var const string
         */
        const CARD_TYPE_CHINAUNIONPAY = 'ChinaUnionPay';

        /**
         * @var string
         */
        private $card_type = NULL;

        /**
         * @var string
         */
        private $card_num = NULL;

        /**
         * @var string
         */
        private $card_auth_code = NULL;

        /**
         * @var string
         */
        private $card_ref_num = NULL;

        /**
         * @var string
         */
        private $card_expiration_date = NULL;

        /**
         * @var string
         */
        private $card_holder_name = NULL;

        /**
         * Construct a openTrans payment card
         */
        public function __construct($term_type = NULL, $term_value = NULL) {

            if ($term_type !== NULL && !is_string($term_type)) {
                throw new rs_opentrans_exception('$term_type must be a string.');
            }

            if ($term_value !== NULL && !is_string($term_value)) {
                throw new rs_opentrans_exception('$term_value must be a string.');
            }

            parent::__construct(self::TYPE_CARD, $term_type, $term_value);

        }

        /**
         * Sets card_type
         *
         * @param string $value
         * @return void
         */
        public function set_card_type($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            if (!preg_match('/^\w{1,50}$/', $value)) {
                throw new rs_opentrans_exception('Type must be \w{1,50}.');
            }

            $this->card_type = $value;

        }

        /**
         * Sets card_num
         *
         * @param string $value
         * @return void
         */
        public function set_card_num($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->card_num = $value;

        }

        /**
         * Sets card_auth_code
         *
         * @param string $value
         * @return void
         */
        public function set_card_auth_code($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->card_auth_code = $value;

        }

        /**
         * Sets card_ref_num
         *
         * @param string $value
         * @return void
         */
        public function set_card_ref_num($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->card_ref_num = $value;

        }

        /**
         * Sets card_expiration_date
         *
         * @param string $value
         * @return void
         */
        public function set_card_expiration_date($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->card_expiration_date = $value;

        }

        /**
         * Sets card_holder_name
         *
         * @param string $value
         * @return void
         */
        public function set_card_holder_name($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->card_holder_name = $value;

        }

        /**
         * Returns card_type
         *
         * @return string role
         */
        public function get_card_type() {
            return $this->card_type;
        }

        /**
         * Returns card_num
         *
         * @return string role
         */
        public function get_card_num() {
            return $this->card_num;
        }

        /**
         * Returns card_auth_code
         *
         * @return string role
         */
        public function get_card_auth_code() {
            return $this->card_auth_code;
        }

        /**
         * Returns card_ref_num
         *
         * @return string role
         */
        public function get_card_ref_num() {
            return $this->card_ref_num;
        }

        /**
         * Returns card_expiration_date
         *
         * @return string role
         */
        public function get_card_expiration_date() {
            return $this->card_expiration_date;
        }

        /**
         * Returns card_holder_name
         *
         * @return string role
         */
        public function get_card_holder_name() {
            return $this->card_holder_name;
        }

    }