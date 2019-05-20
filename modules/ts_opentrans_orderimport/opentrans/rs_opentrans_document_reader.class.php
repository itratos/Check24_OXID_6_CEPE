<?php
    /**
     * openTrans Document Reader
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
    abstract class rs_opentrans_document_reader {

        /**
         * File name
         *
         * @var string
         */
        protected $filename = NULL;

        /**
         * File type
         *
         * @var string
         */
        protected $filetype = NULL;

        /**
         * construct the reader
         *
         * @param string filetype
         * @param string filename
         */
        public function __construct($filetype, $filename) {

            if (!is_string($filename)) {
                throw new rs_opentrans_exception('$filename must be a string.');
            }

            if (!is_string($filetype)) {
                throw new rs_opentrans_exception('$filetype must be a string.');
            }

            $this->filename = $filename;
            $this->filetype = $filetype;

        }

        /**
         * read XML-File
         */
        public function run() {
            return $this->read_document($this->filename);
        }

        /**
         * convert XML-File to openTrans
         *
         * @param string filename
         */
        protected function read_document($filename) {

            if (!is_string($filename)) {
                throw new rs_opentrans_exception('$filename must be a string.');
            }

            return $this->get_document_data($filename);

        }

        /**
         * prepare the XML-Data
         *
         * @param string src_file
         */
        protected function get_document_data($src_file) {

            if (!is_string($src_file)) {
                throw new rs_opentrans_exception('$src_file must be a string.');
            }

            // Determine method

            $fn = 'get_document_data_' . $this->get_filetype();

            if (!method_exists($this, $fn)) {
                throw new rs_opentrans_exception('No method found for writing documents of type "' . $this->get_filetype() . '"');
            }

            return $this->$fn($src_file);

        }

        /**
         * returns the file type
         */
        protected function get_filetype() {
            return $this->filetype;
        }

        /**
         * Version is not valid if major counter are different.
         *
         * @param string $version_orderapi_shop
         * @return boolean
         */
        public function is_version_valid($version_orderapi_shop = NULL) {

            if ((int)rs_opentrans_document::VERSION_ORDERAPI != (int)$version_orderapi_shop) {
                return false;
            }

            return true;

        }

    }