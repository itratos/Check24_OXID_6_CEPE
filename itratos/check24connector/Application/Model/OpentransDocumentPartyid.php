<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentPartyid
{

    /**#@+
     * Constants
     */
    /**
     * TYPE_BUYER_SPECIFIC
     *
     * @var const string
     */
    const TYPE_BUYER_SPECIFIC = 'buyer_specific';

    /**
     * TYPE_CUSTOMER_SPECIFIC
     *
     * @var const string
     */
    const TYPE_CUSTOMER_SPECIFIC = 'customer_specific';

    /**
     * TYPE_CHECK24
     *
     * @var const string
     */
    const TYPE_CHECK24 = 'check24';

    /**
     * TYPE_DUNS
     *
     * @var const string
     */
    const TYPE_DUNS = 'duns';

    /**
     * TYPE_ILN
     *
     * @var const string
     */
    const TYPE_ILN = 'iln';

    /**
     * TYPE_GLN
     *
     * @var const string
     */
    const TYPE_GLN = 'gln';

    /**
     * TYPE_PARTY_SPECIFIC
     *
     * @var const string
     */
    const TYPE_PARTY_SPECIFIC = 'party_specific';

    /**
     * TYPE_SUPPLIER_SPECIFIC
     *
     * @var const string
     */
    const TYPE_SUPPLIER_SPECIFIC = 'supplier_specific';

    /**
     * openTrans defined and valid partyid types
     *
     * @var static array
     */
    static $valid_types = array(
        self::TYPE_BUYER_SPECIFIC,
        self::TYPE_CUSTOMER_SPECIFIC,
        self::TYPE_DUNS,
        self::TYPE_ILN,
        self::TYPE_GLN,
        self::TYPE_PARTY_SPECIFIC,
        self::TYPE_SUPPLIER_SPECIFIC
    );

    /**
     * Party-ID
     *
     * ID needs to be unique and is expected to start with TS-
     * followed by:
     * DA- user_id() - adress_id() for Deliveryaddress
     * BA- user_id() - adress_id() for Billaddress
     * SA- shop_id() for Shopaddress
     *
     * @var string
     */
    protected $id = NULL;

    /**
     * @var string
     */
    protected $type = NULL;

    /**
     * Construct a openTrans partyid
     *
     * @param string id
     * @param string type
     */
    public function __construct($id, $type)
    {
        if (!is_string($id)) {
            throw new OpentransException('$id must be a string.');
        }

        if (!is_string($type)) {
            throw new OpentransException('$type must be a string.');
        } else if (!in_array($type, self::$valid_types)) {

            if (!preg_match('/^\w{1,250}$/', $type)) {
                throw new OpentransException('Type must be \w{1,250}.');
            }

        }

        $this->id = $id;
        $this->type = $type;
    }

    /**
     * Returns id
     *
     * @return string id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Returns type
     *
     * @return string type
     */
    public function get_type()
    {
        return $this->type;
    }

}