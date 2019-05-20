<?php

    /**
     * openTrans Document Orderchange Item
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
    class rs_opentrans_document_orderchange_item extends rs_opentrans_document_item {

        /**
         * Construct a openTrans item
         *
         * @param integer line_item_id
         */
        public function __construct($line_item_id = NULL) {

            if (!is_int($line_item_id)) {
                throw new rs_opentrans_exception('$line_item_id must be integer.');
            }

            parent::__construct($line_item_id);

        }

    }

?>