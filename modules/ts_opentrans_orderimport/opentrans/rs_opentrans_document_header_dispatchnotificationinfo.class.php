<?php
    /**
     * Opentrans Document Header Dispatchnotificationinfo
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
    class rs_opentrans_document_header_dispatchnotificationinfo extends rs_opentrans_document_header_documentinfo {

        /**
         * Construct a openTrans dispatchnotificationinfo
         *
         * @param string dispatchnotification_id
         * @param string dispatchnotification_date
         */
        public function __construct($dispatchnotification_id, $dispatchnotification_date) {

            if (!is_string($dispatchnotification_id)) {
                throw new rs_opentrans_exception('$dispatchnotification_id must be a string.');
            }

            if (!is_string($dispatchnotification_date)) {
                throw new rs_opentrans_exception('$dispatchnotification_date must be a string.');
            }

            parent::__construct($dispatchnotification_id, $dispatchnotification_date);

        }

        /**
         * Returns dispatchnotification id
         *
         * @return string dispatchnotification_id
         */
        public function get_dispatchnotification_id() {
            return $this->get_document_id();
        }

        /**
         * Returns dispatchnotification date
         *
         * @return string dispatchnotification_date
         */
        public function get_dispatchnotification_date() {
            return $this->get_document_date();
        }

    }