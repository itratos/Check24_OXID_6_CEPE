<?php
/**
 * Opentrans Document Type: Order
 *
 * Creates openTrans document from type order
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

class OpentransDocumentOrder extends OpentransDocument
{

    /**#@+
     * Constants
     */
    /**
     * TYPE_STANDARD
     *
     * @var const string
     */
    const TYPE_STANDARD = 'standard';

    /**
     * TYPE_EXPRESS
     *
     * @var const string
     */
    const TYPE_EXPRESS = 'express';

    /**
     * TYPE_RELEASE
     *
     * @var const string
     */
    const TYPE_RELEASE = 'release';

    /**
     * TYPE_CONSIGNMENT
     *
     * @var const string
     */
    const TYPE_CONSIGNMENT = 'consignment';

    /**
     * openTrans defined and valid ordertypes
     *
     * @var static array
     */
    static $valid_types = array(
        self::TYPE_STANDARD,
        self::TYPE_EXPRESS,
        self::TYPE_RELEASE,
        self::TYPE_CONSIGNMENT
    );

    /**
     * @var string
     */
    protected $type = NULL;

    /**
     * @var object
     */
    protected $summary = NULL;

    /**
     * Construct a openTrans order
     *
     * @param string type
     */
    public function __construct($type)
    {
        parent::__construct(self::DOCUMENT_TYPE_ORDER);

        if (!is_string($type)) {
            throw new OpentransException('$type must be a string.');
        }

        if (!in_array($type, self::$valid_types)) {
            throw new OpentransException('Unsupported type "' . $type . '".');
        }

        $this->type = $type;

        $this->set_summary(new OpentransDocumentSummaryOrder(0));
    }

    /**
     * Creates header for openTrans order
     *
     * @return object OpentransDocumentHeaderOrder
     */
    public function create_header()
    {
        $this->header = new OpentransDocumentHeaderOrder();

        return $this->header;
    }

    /**
     * Sets summary for openTrans order
     *
     * @param object summary OpentransDocumentSummary
     */
    public function set_summary($summary)
    {
        if (!$summary instanceof OpentransDocumentSummary) {
            throw new OpentransException('$summary must be type of OpentransDocumentSummary');
        }

        $this->summary = $summary;
    }
}