<?php
/**
 * Opentrans Document Summary
 *
 * Creates summary for openTrans document
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

namespace TestSieger\Check24Connector\Application\Model;

class OpentransDocumentSummary
{

    /**
     * Item count of item list
     *
     * @var integer
     */
    protected $total_item_num = NULL;

    /**
     * Construct a openTrans party
     *
     * @param integer total_item_num
     */
    public function __construct($total_item_num = 0)
    {
        if (!is_int($total_item_num)) {
            throw new OpentransException('$total_item_num must be integer.');
        }

        $this->total_item_num = $total_item_num;
    }

    /**
     * Adds item
     *
     * @return void
     */
    public function add_item()
    {
        $this->total_item_num++;
    }

    /**
     * Returns total_item_num
     *
     * @return integer total_item_num
     */
    public function get_total_item_num()
    {
        return $this->total_item_num;
    }

}
