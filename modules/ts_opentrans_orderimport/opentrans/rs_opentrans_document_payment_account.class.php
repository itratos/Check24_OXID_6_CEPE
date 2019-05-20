<?php
    /**
     * openTrans Document Payment Account
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
    class rs_opentrans_document_payment_account extends rs_opentrans_document_payment {

        /**#@+
         * Constants
         */
        /**
         * BANK_CODE_TYPE_STANDARD
         * 
         * @var const string
         */
        const BANK_CODE_TYPE_STANDARD = '';

        /**
         * BANK_CODE_TYPE_BIC
         * 
         * @var const string
         */
        const BANK_CODE_TYPE_BIC = 'bic';

        /**
         * BANK_ACCOUNT_TYPE_STANDARD
         * 
         * @var const string
         */
        const BANK_ACCOUNT_TYPE_STANDARD = '';

        /**
         * BANK_ACCOUNT_TYPE_IBAN
         * 
         * @var const string
         */
        const BANK_ACCOUNT_TYPE_IBAN = 'iban';

        /**
         * @var string
         */
        private $holder = NULL;

        /**
         * @var string
         */
        private $bank_name = NULL;

        /**
         * @var string
         */
        private $bank_country = NULL;

        /**
         * @var string
         */
        private $bank_code = NULL;

        /**
         * @var string
         */
        private $bank_code_type = NULL;

        /**
         * @var string
         */
        private $bank_account = NULL;

        /**
         * @var string
         */
        private $bank_account_type = NULL;

        /**
         * Construct a openTrans payment account
         *
         * @param string term_type 
         * @param string term_value 
         * @param string holder 
         * @param string bank_name 
         * @param string bank_country 
         * @param string bank_code 
         * @param string bank_code_type 
         * @param string bank_account 
         * @param string bank_account_type 
         */
        public function __construct(
            $term_type = NULL,
            $term_value = NULL,
            $holder = NULL,
            $bank_name = NULL,
            $bank_country = NULL,
            $bank_code = NULL,
            $bank_code_type = self::BANK_CODE_TYPE_STANDARD,
            $bank_account = NULL,
            $bank_account_type = self::BANK_ACCOUNT_TYPE_STANDARD
        ) {

            if ($term_type !== NULL && !is_string($term_type)) {
                throw new rs_opentrans_exception('$term_type must be a string.');
            }

            if ($term_value !== NULL && !is_string($term_value)) {
                throw new rs_opentrans_exception('$term_value must be a string.');
            }

            if ($holder !== NULL && !is_string($holder)) {
                throw new rs_opentrans_exception('$holder must be a string.');
            }

            if ($bank_name !== NULL && !is_string($bank_name)) {
                throw new rs_opentrans_exception('$bank_name must be a string.');
            }

            if ($bank_country !== NULL && !is_string($bank_country)) {
                throw new rs_opentrans_exception('$bank_country must be a string.');
            }

            if ($bank_code !== NULL && !is_string($bank_code)) {
                throw new rs_opentrans_exception('$bank_code must be a string.');
            }

            if ($bank_code_type !== NULL && !is_string($bank_code_type)) {
                throw new rs_opentrans_exception('$bank_code_type must be a string.');
            }

            if ($bank_account !== NULL && !is_string($bank_account)) {
                throw new rs_opentrans_exception('$bank_account must be a string.');
            }

            if ($bank_account_type !== NULL && !is_string($bank_account_type)) {
                throw new rs_opentrans_exception('$bank_account_type must be a string.');
            }

            parent::__construct(self::TYPE_ACCOUNT, $term_type, $term_value);

            $this->holder = $holder;
            $this->bank_name = $bank_name;
            $this->bank_country = $bank_country;
            $this->bank_code = $bank_code;
            $this->bank_code_type = $bank_code_type;
            $this->bank_account = $bank_account;
            $this->bank_account_type = $bank_account_type;

        }

        /**
         * Returns holder
         *
         * @return string holder
         */
        public function get_holder() {
            return $this->holder;
        }

        /**
         * Returns bank_name
         *
         * @return string bank_name
         */
        public function get_bank_name() {
            return $this->bank_name;
        }

        /**
         * Returns bank_country
         *
         * @return string bank_country
         */
        public function get_bank_country() {
            return $this->bank_country;
        }

        /**
         * Returns bank_code
         *
         * @return string bank_code
         */
        public function get_bank_code() {
            return $this->bank_code;
        }

        /**
         * Returns bank_code_type
         *
         * @return string bank_code_type
         */
        public function get_bank_code_type() {
            return $this->bank_code_type;
        }

        /**
         * Returns bank_account
         *
         * @return string bank_account
         */
        public function get_bank_account() {
            return $this->bank_account;
        }

        /**
         * Returns bank_account_type
         *
         * @return string bank_account_type
         */
        public function get_bank_account_type() {
            return $this->bank_account_type;
        }

        /**
         * Sets holder
         *
         * @param string $value
         * @return void
         */
        public function set_holder($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->holder = $value;

        }

        /**
         * Sets bank_name
         *
         * @param string $value
         * @return void
         */
        public function set_bank_name($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->bank_name = $value;

        }

        /**
         * Sets bank_country
         *
         * @param string $value
         * @return void
         */
        public function set_bank_country($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->bank_country = $value;

        }

        /**
         * Sets bank_code
         *
         * @param string $value
         * @return void
         */
        public function set_bank_code($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->bank_code = $value;

        }

        /**
         * Sets bank_account
         *
         * @param string $value
         * @return void
         */
        public function set_bank_account($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            $this->bank_account = $value;

        }

    }