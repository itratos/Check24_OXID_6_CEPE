<?php

namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentAddressContactDetails
{

    /**
     * @var string $contact_id
     */
    protected $contact_id = NULL;

    /**
     * @var string $contact_name
     */
    protected $contact_name = NULL;

    /**
     * @var string $first_name
     */
    protected $first_name = NULL;

    /**
     * @var string $title
     */
    protected $title = NULL;

    /**
     * @var string $academic_title
     */
    protected $academic_title = NULL;

    /**
     * @var array $contact_role
     */
    protected $contact_role = array();

    /**
     * @var string $contact_descr
     */
    protected $contact_descr = NULL;

    /**
     * @var array() $phone
     */
    protected $phone = array();

    /**
     * @var array() $fax
     */
    protected $fax = array();

    /**
     * @var string $url
     */
    protected $url = NULL;

    /**
     * @var string $emails
     */
    protected $emails = NULL;

    /**
     * @var string $authentification
     */
    protected $authentification = NULL;

    /**
     * Sets contact_id
     *
     * @param string $value contact_id
     * @return void
     */
    public function set_contact_id($value)
    {

        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->contact_id = $value;

    }

    /**
     * Sets contact_name
     *
     * @param string $value contact_name
     * @return void
     */
    public function set_contact_name($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->contact_name = $value;
    }

    /**
     * Sets first_name
     *
     * @param string $value first_name
     * @return void
     */
    public function set_first_name($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->first_name = $value;
    }

    /**
     * Sets title
     *
     * @param string $value title
     * @return void
     */
    public function set_title($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->title = $value;
    }

    /**
     * Sets academic_title
     *
     * @param string $value academic_title
     * @return void
     */
    public function set_academic_title($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->academic_title = $value;
    }

    /**
     * Sets contact_role[$type]
     *
     * @param string $type contact_role type
     * @param string $value contact_role role
     * @return void
     */
    public function set_contact_role($value, $type = NULL)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->contact_role[$type] = $value;
    }

    /**
     * Sets contact_descr
     *
     * @param string $value contact_descr
     * @return void
     */
    public function set_contact_descr($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->contact_descr = $value;
    }

    /**
     * Sets phone[$type]
     *
     * @param string $type phone type
     * @param string $value phone number
     * @return void
     */
    public function set_phone($value, $type = NULL)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        if ($type !== NULL && !preg_match('/^\w{1,50}$/', $type)) {
            throw new OpentransException('Phone type must be string, 1 to 50 characters');
        }

        $this->phone[$type] = $value;
    }

    /**
     * Sets fax[$type]
     *
     * @param string $type fax type
     * @param string $value fax number
     * @return void
     */
    public function set_fax($value, $type = NULL)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        if ($type !== NULL && !preg_match('/^\w{1,50}$/', $type)) {
            throw new OpentransException('Phone type must be string, 1 to 50 characters');
        }

        $this->fax[$type] = $value;
    }

    /**
     * Sets url
     *
     * @param string $value url
     * @return void
     */
    public function set_url($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->url = $value;
    }

    /**
     * Sets emails
     *
     * @param string $value address_remarks
     * @return void
     */
    public function set_emails($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->emails = $value;
    }

    /**
     * Sets authentification
     *
     * @param string $value authentification
     * @return void
     */
    public function set_authentification($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be string.');
        }

        $this->authentification = $value;
    }

    /**
     * Returns contact_id
     *
     * @return string
     */
    public function get_contact_id()
    {
        return $this->contact_id;
    }

    /**
     * Returns contact_name
     *
     * @return string
     */
    public function get_contact_name()
    {
        return $this->contact_name;
    }

    /**
     * Returns first_name
     *
     * @return string
     */
    public function get_first_name()
    {
        return $this->first_name;
    }

    /**
     * Returns title
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Returns academic_title
     *
     * @return string
     */
    public function get_academic_title()
    {
        return $this->academic_title;
    }

    /**
     * Returns contact_role
     *
     * @return array
     */
    public function get_contact_role()
    {
        return $this->contact_role;
    }

    /**
     * Returns contact_descr
     *
     * @return string
     */
    public function get_contact_descr()
    {
        return $this->contact_descr;
    }

    /**
     * Returns phone
     *
     * @return array
     */
    public function get_phone()
    {
        return $this->phone;
    }

    /**
     * Returns fax
     *
     * @return array
     */
    public function get_fax()
    {
        return $this->fax;
    }

    /**
     * Returns url
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * Returns emails
     *
     * @return array
     */
    public function get_emails()
    {
        return $this->emails;
    }

    /**
     * Returns authentification
     *
     * @return array
     */
    public function get_authentification()
    {
        return $this->authentification;
    }

}
