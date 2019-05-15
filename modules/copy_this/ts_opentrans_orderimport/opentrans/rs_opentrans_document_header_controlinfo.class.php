<?php
    /**
     * Opentrans Document Header Controlinfo
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
    class rs_opentrans_document_header_controlinfo {

        /**
         * Stops automatic processing when not empty
         * and is a notice why manual adaptation is necessary
         * 
         * @var string $stop_automatic_processing
         */
        private $stop_automatic_processing = NULL;

        /**
         * @var string $generator_name
         */
        private $generator_name = NULL;

        /**
         * @var integer $generation_date
         */
        private $generation_date = NULL;

        /**
         * Constructor
         *
         * @param string $stop_automatic_processing
         * @param string $generator_name
         * @param integer $generation_date
         */
        public function __construct($stop_automatic_processing = NULL, $generator_name = NULL, $generation_date = NULL) {

            if ($stop_automatic_processing !== NULL && !is_string($stop_automatic_processing)) {
                throw new rs_opentrans_exception('$stop_automatic_processing must be a string.');
            }

            if (!is_string($generator_name)) {
                throw new rs_opentrans_exception('$generator_name must be a string.');
            }

            if (!is_int($generation_date)) {
                throw new rs_opentrans_exception('$generation_date must be integer.');
            }

            $this->stop_automatic_processing = $stop_automatic_processing;
            $this->generator_name = $generator_name;
            $this->generation_date = $generation_date;

        }

        /**
         * Sets stop_automatic_processing
         *
         * @param string $value stop_automatic_processing
         * @return void
         */
        public function set_stop_automatic_processing($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }
            
            $this->stop_automatic_processing = $value;

        }

        /**
         * Returns generator_name
         *
         * @return string generator_name
         */
        public function get_generator_name() {
            return $this->generator_name;
        }

        /**
         * Returns generation_date
         *
         * @return string generation_date
         */
        public function get_generation_date() {
            return $this->generation_date;
        }

        /**
         * Returns stop_automatic_processing
         *
         * @return string stop_automatic_processing
         */
        public function get_stop_automatic_processing() {
            return $this->stop_automatic_processing;
        }

    }