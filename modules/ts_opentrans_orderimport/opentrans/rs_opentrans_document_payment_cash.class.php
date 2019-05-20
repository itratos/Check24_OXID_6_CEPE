<?php
    /**
     * openTrans Document payment cash
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
    class rs_opentrans_document_payment_cash extends rs_opentrans_document_payment {

        /**
         * Construct a openTrans payment cash
         *
         * @param string term_type 
         * @param string term_value 
         */
        public function __construct($term_type = NULL, $term_value = NULL) {

            if (!is_string($term_type)) {
                throw new rs_opentrans_exception('$term_type must be a string.');
            }

            if (!is_string($term_value)) {
                throw new rs_opentrans_exception('$term_value must be a string.');
            }

            parent::__construct(self::TYPE_CASH, $term_type, $term_value);

        }

    }