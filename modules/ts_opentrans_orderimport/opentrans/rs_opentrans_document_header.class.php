<?php
    /**
     * Opentrans Document Header
     *
     * Creates openTrans document header
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
    class rs_opentrans_document_header {

        /**
         * @var object rs_opentrans_document_header_controlinfo
         */
        protected $controlinfo = NULL;

        /**
         * Creates a openTrans controlinfo
         *
         * @param string $stop_automatic_processing
         * @param string $generator_info
         * @param integer $generation_date
         *
         * @return object rs_opentrans_document_header_controlinfo
         */
        public function create_controlinfo($stop_automatic_processing = NULL, $generator_info = NULL, $generation_date = NULL) {

            if ($stop_automatic_processing !== NULL && !is_string($stop_automatic_processing)) {
                throw new rs_opentrans_exception('$stop_automatic_processing must be string.');
            }

            if (!is_string($generator_info)) {
                throw new rs_opentrans_exception('$generator_info must be string.');
            }

            if (!is_int($generation_date)) {
                throw new rs_opentrans_exception('$generation_date must be integer.');
            }

            $this->controlinfo = new rs_opentrans_document_header_controlinfo($stop_automatic_processing, $generator_info, $generation_date);

            return $this->controlinfo;

        }

        /**
         * Returns controlinfo
         *
         * @return rs_opentrans_document_header_controlinfo
         */
        public function get_controlinfo() {
            return $this->controlinfo;
        }

    }
