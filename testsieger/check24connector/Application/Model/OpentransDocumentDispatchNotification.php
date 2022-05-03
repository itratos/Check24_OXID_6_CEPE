<?php
/**
 * Opentrans Document Dispatchnotification
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

namespace TestSieger\Check24Connector\Application\Model;

class OpentransDocumentDispatchNotification extends OpentransDocument
{

    /**
     * @var object
     */
    protected $summary = NULL;

    /**
     * Construct a openTrans dispatchnotification
     */
    public function __construct()
    {
        parent::__construct(self::DOCUMENT_TYPE_DISPATCHNOTIFICATION);

        $this->summary = new OpentransDocumentSummaryOrder(0);
    }

    /**
     * Creates a openTrans dispatchnotification header
     *
     * @return object OpentransDocumentHeaderDispatchNotification
     */
    public function create_header()
    {
        $this->header = new OpentransDocumentHeaderDispatchNotification();

        return $this->header;
    }

    /**
     * Sets summary
     *
     * @param object $summary OpentransDocumentSummary
     * @return void
     */
    public function set_summary($summary)
    {
        if (!$summary instanceof OpentransDocumentSummary) {
            throw new OpentransException('$summary must be type of OpentransDocumentSummary');
        }

        $this->summary = $summary;
    }

}