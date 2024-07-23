<?php
    /**
     * Opentrans Document Address
     *
     * Used for Partys
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
    class rs_opentrans_document_address {

        /**#@+
         * Constants
         */
        /**
         * Type of address phone type 'office'
         */
        const PHONE_TYPE_OFFICE = 'office';

        /**
         * Type of address phone type 'private'
         */
        const PHONE_TYPE_PRIVATE = 'private';

        /**
         * Type of address phone type 'mobile'
         */
        const PHONE_TYPE_MOBILE = 'mobile';

        /**
         * Type of address fax type 'office'
         */
        const FAX_TYPE_OFFICE = 'office';

        /**
         * Type of address fax type 'private'
         */
        const FAX_TYPE_PRIVATE = 'private';

        /**
         * @var string name
         */
        protected $name = NULL;

        /**
         * @var string name2
         */
        protected $name2 = NULL;

        /**
         * @var string name3
         */
        protected $name3 = NULL;

        /**
         * @var string department
         */
        protected $department = NULL;

        /**
         * @var object $contact_details
         */
        protected $contact_details = NULL;

        /**
         * @var string street with street number
         */
        protected $street = NULL;

        /**
         * @var string zip code
         */
        protected $zip = NULL;

        /**
         * @var string post-office box number
         */
        protected $boxno = NULL;

        /**
         * @var string zip code of post-office box
         */
        protected $zipbox = NULL;

        /**
         * @var string cityname
         */
        protected $city = NULL;

        /**
         * @var string state
         */
        protected $state = NULL;

        /**
         * @var string country
         */
        protected $country = NULL;

        /**
         * @var string country code namespace: BMECAT
         */
        protected $country_coded = NULL;

        /**
         * @var string value-added tax id
         */
        protected $vat_id = NULL;

        /**
         * @var string tax number
         */
        protected $tax_number = NULL;

        /**
         * @var array phone numbers
         */
        protected $phone = array();

        /**
         * @var array fax numbers
         */
        protected $fax = array();

        /**
         * @var string url
         */
        protected $url= NULL;

        /**
         * @var string explanatory notes
         */
        protected $address_remarks = array();

        /**
         * @var array email-addresses
         */
        protected $emails = array();

        /**
         * Sets name
         *
         * @param string $value name
         * @return void
         */
        public function set_name($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->name = $value;

        }

        /**
         * Sets name2
         *
         * @param string $value name2
         * @return void
         */
        public function set_name2($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->name2 = $value;

        }

        /**
         * Sets name3
         *
         * @param string $value name3
         * @return void
         */
        public function set_name3($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->name3 = $value;

        }

        /**
         * Sets department
         *
         * @param string $value department
         * @return void
         */
        public function set_department($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->department = $value;

        }

        /**
         * Sets contact_details
         *
         * @param object $product_id rs_opentrans_document_address_contactdetails
         * @return void
         */
        public function set_contact_details($contact_details) {

            if (!$contact_details instanceof rs_opentrans_document_address_contactdetails) {
                throw new rs_opentrans_exception('$contact_details must be type of rs_opentrans_document_address_contactdetails');
            }

            $this->contact_details = $contact_details;

        }

        /**
         * Sets street
         *
         * @param string $value street
         * @return void
         */
        public function set_street($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->street = $value;

        }

        /**
         * Sets zip
         *
         * @param string $value zip
         * @return void
         */
        public function set_zip($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->zip = $value;

        }

        /**
         * Sets boxno
         *
         * @param string $value boxno
         * @return void
         */
        public function set_boxno($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->boxno = $value;

        }

        /**
         * Sets zipbox
         *
         * @param string $value zipbox
         * @return void
         */
        public function set_zipbox($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->zipbox = $value;

        }

        /**
         * Sets city
         *
         * @param string $value city
         * @return void
         */
        public function set_city($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->city = $value;

        }

        /**
         * Sets state
         *
         * @param string $value state
         * @return void
         */
        public function set_state($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->state = $value;

        }

        /**
         * Sets country
         *
         * @param string $value country
         * @return void
         */
        public function set_country($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->country = $value;

        }

        /**
         * Sets country_coded
         *
         * @param string $value country_coded
         * @return void
         */
        public function set_country_coded($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->country_coded = $value;

        }

        /**
         * Sets vat_id
         *
         * @param string $value vat_id
         * @return void
         */
        public function set_vat_id($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->vat_id= $value;

        }

        /**
         * Sets tax_number
         *
         * @param string $value tax_number
         * @return void
         */
        public function set_tax_number($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->tax_number = $value;

        }

        /**
         * Sets phone[$type]
         *
         * @param string $type phone type
         * @param string $value phone number
         * @return void
         */
        public function set_phone($value, $type = NULL) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            if ($type !== NULL && !preg_match('/^\w{1,50}$/', $type)) {
                throw new rs_opentrans_exception('Phone type must be string, 1 to 50 characters');
            }

            $this->phone[$type] = $value;

        }

        /**
         * Sets fax[$type]
         *
         * @param string $type fax type
         * @param string $value fax number
         * @return void
         */
        public function set_fax($value, $type = NULL) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            if ($type !== NULL && !preg_match('/^\w{1,50}$/', $type)) {
                throw new rs_opentrans_exception('Phone type must be string, 1 to 50 characters');
            }

            $this->fax[$type] = $value;

        }

        /**
         * Sets url
         *
         * @param string $value url
         * @return void
         */
        public function set_url($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be string.');
            }

            $this->url = $value;

        }

        /**
         * Sets address_remarks
         *
         * @param string $value address_remarks
         * @return void
         */
        public function set_address_remarks($value, $type = NULL) {
            $this->address_remarks[$type] = $value;
        }

        /**
         * Adds email
         *
         * @param string $value email
         * @return void
         */
        public function add_email($value) {

            if (!is_string($value) || !preg_match("/[\.a-z0-9_-]+@[a-z0-9-\.]{2,}\.[a-z]{2,4}$/i", $value)) {
                throw new rs_opentrans_exception('$value "' . $value . '" is not a e-mail valid string.');
            }

            $this->emails[] = $value;

        }

        /**
         * Returns name
         *
         * @return string
         */
        public function get_name() {
            return $this->name;
        }

        /**
         * Returns name2
         *
         * @return string
         */
        public function get_name2() {
            return $this->name2;
        }

        /**
         * Returns name3
         *
         * @return string
         */
        public function get_name3() {
            return $this->name3;
        }

        /**
         * Returns department
         *
         * @return string
         */
        public function get_department() {
            return $this->department;
        }

        /**
         * Returns contact_details
         *
         * @return object contact_details
         */
        public function get_contact_details() {
            return $this->contact_details;
        }

        /**
         * Returns street
         *
         * @return string
         */
        public function get_street() {
            return $this->street;
        }

        /**
         * Returns zip
         *
         * @return string
         */
        public function get_zip() {
            return $this->zip;
        }

        /**
         * Returns boxno
         *
         * @return string
         */
        public function get_boxno() {
            return $this->boxno;
        }

        /**
         * Returns zipbox
         *
         * @return string
         */
        public function get_zipbox() {
            return $this->zipbox;
        }

        /**
         * Returns city
         *
         * @return string
         */
        public function get_city() {
            return $this->city;
        }

        /**
         * Returns state
         *
         * @return string
         */
        public function get_state() {
            return $this->state;
        }

        /**
         * Returns country
         *
         * @return string
         */
        public function get_country() {
            return $this->country;
        }

        /**
         * Returns country_coded
         *
         * @return string
         */
        public function get_country_coded() {
            return $this->country_coded;
        }

        /**
         * Returns vat_id
         *
         * @return string
         */
        public function get_vat_id() {
            return $this->vat_id= $value;
        }

        /**
         * Returns tax_number
         *
         * @return string
         */
        public function get_tax_number() {
            return $this->tax_number;
        }

        /**
         * Returns phone
         *
         * @return array
         */
        public function get_phone() {
            return $this->phone;
        }

        /**
         * Returns fax
         *
         * @return array
         */
        public function get_fax() {
            return $this->fax;
        }

        /**
         * Returns url
         *
         * @return string
         */
        public function get_url() {
            return $this->url;
        }

        /**
         * Returns address_remarks
         *
         * @return string
         */
        public function get_address_remarks() {
            return $this->address_remarks;
        }

        /**
         * Returns emails
         *
         * @return array
         */
        public function get_emails() {
            return $this->emails;
        }

    }
