<?php

/**
 * Opentrans Document Orderchange
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

use OxidEsales\Eshop\Core\DatabaseProvider as DatabaseProvider;


class OpentransDocumentOrderchange extends OpentransDocument
{

    //TODO: ask question what type to use for order reject
    /**
     * Orderchange document created when imported order is invalid
     */
    const ORDERCHANGE_TYPE_ORDER_INVALID = 'orderinvalid';


    //Action types to used in REMARK field

    /**
     * Order returning request action from CHECK24 to Shop only (after order is sent)
     */
    const ACTION_TYPE_RETURN_REQUEST = 'returnrequest';

    /**
     * Order returning confirmation action from shop to CHECK24 only (after order is sent)
     */
    const ACTION_TYPE_RETURN_CONFIRMATION = 'returnconfirmation';

    /**
     * Order cancellation request action, both directions (strictly before order is sent)
     */
    const ACTION_TYPE_CANCELLATION_REQUEST = 'cancellationrequest';

    /**
     * Order cancellation confirmation action, both directions
     */
    const ACTION_TYPE_CANCELLATION_CONFIRMATION = 'cancellationconfirmation';

    /**
     * Order cancellation reject action, only from shop to CHECK24
     */
    const ACTION_TYPE_CANCELLATION_REJECT = 'cancellationreject';


    /**
     * @var object
     */
    protected $summary = NULL;

    /**
     * cancellation or returning
     */
    protected $type = NULL;

    /**
     * Construct a openTrans orderchange
     */
    public function __construct()
    {
        parent::__construct(self::DOCUMENT_TYPE_ORDERCHANGE);

        $this->summary = new OpentransDocumentSummaryOrder(0);
    }

    /**
     * Creates a openTrans orderchange header
     *
     * @return object OpentransDocumentHeaderOrderchange
     */
    public function create_header()
    {
        $this->header = new OpentransDocumentHeaderOrderchange();

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