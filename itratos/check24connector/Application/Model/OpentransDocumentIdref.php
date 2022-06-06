<?php

namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentIdref
{

    /**#@+
     * Constants
     */
    /**
     * TYPE_SUPPLIER_IDREF
     *
     * @var const string
     */
    const TYPE_SUPPLIER_IDREF = 'supplier_idref';

    /**
     * TYPE_BUYER_IDREF
     *
     * @var const string
     */
    const TYPE_BUYER_IDREF = 'buyer_idref';

    /**
     * TYPE_DELIVERY_IDREF
     *
     * @var const string
     */
    const TYPE_DELIVERY_IDREF = 'delivery_idref';

    /**
     * TYPE_DELIVERER_IDREF
     *
     * @var const string
     */
    const TYPE_DELIVERER_IDREF = 'deliverer_idref';

    /**
     * TYPE_INVOICE_ISSUER_IDREF
     *
     * @var const string
     */
    const TYPE_INVOICE_ISSUER_IDREF = 'invoice_issuer_idref';

    /**
     * TYPE_INVOICE_RECIPIENT_IDREF
     *
     * @var const string
     */
    const TYPE_INVOICE_RECIPIENT_IDREF = 'invoice_recipient_idref';

    /**
     * openTrans defined and valid idref types
     *
     * @var static array
     */
    static $valid_types = array(
        self::TYPE_SUPPLIER_IDREF,
        self::TYPE_BUYER_IDREF,
        self::TYPE_DELIVERY_IDREF,
        self::TYPE_INVOICE_RECIPIENT_IDREF,
        self::TYPE_INVOICE_ISSUER_IDREF,
        self::TYPE_DELIVERER_IDREF
    );

    /**
     * openTrans defined and valid party roles
     *
     * @var static array
     */
    static $valid_roles = array(
        OpentransDocumentParty::ROLE_DELIVERY,
        OpentransDocumentParty::ROLE_DELIVERER,
        OpentransDocumentParty::ROLE_INVOICE_ISSUER,
        OpentransDocumentParty::ROLE_INVOICE_RECIPIENT,
        OpentransDocumentParty::ROLE_SUPPLIER
    );

    /**
     * @var string
     */
    protected $id = NULL;

    /**
     * @var string
     */
    protected $type = NULL;

    /**
     * Construct a openTrans idref
     *
     * @param string id
     * @param string role
     */
    public function __construct($id, $role)
    {

        if (!is_string($id)) {
            throw new OpentransException('$id must be a string.');
        }

        if (!is_string($role)) {
            throw new OpentransException('$role must be a string.');
        } else if (!in_array($role, self::$valid_roles)) {
            throw new OpentransException('$role is not a supported idref role.');
        }

        $this->id = $id;

        switch ($role) {

            case OpentransDocumentParty::ROLE_DELIVERY:
                $this->type = self::TYPE_DELIVERY_IDREF;
                break;

            case OpentransDocumentParty::ROLE_SUPPLIER:
                $this->type = self::TYPE_SUPPLIER_IDREF;
                break;

            case OpentransDocumentParty::ROLE_INVOICE_ISSUER:
                $this->type = self::TYPE_INVOICE_ISSUER_IDREF;
                break;

            case OpentransDocumentParty::ROLE_INVOICE_RECIPIENT:
                $this->type = self::TYPE_INVOICE_RECIPIENT_IDREF;
                break;

            case OpentransDocumentParty::ROLE_DELIVERER:
                $this->type = self::TYPE_DELIVERER_IDREF;
                break;

        }

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