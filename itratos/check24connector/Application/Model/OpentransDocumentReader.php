<?php


namespace Itratos\Check24Connector\Application\Model;

abstract class OpentransDocumentReader
{

    /**
     * File name
     *
     * @var string
     */
    protected $filename = NULL;

    /**
     * File type
     *
     * @var string
     */
    protected $filetype = NULL;

    /**
     * construct the reader
     *
     * @param string filetype
     * @param string filename
     */
    public function __construct($filetype, $filename)
    {
        if (!is_string($filename)) {
            throw new OpentransException('$filename must be a string.');
        }

        if (!is_string($filetype)) {
            throw new OpentransException('$filetype must be a string.');
        }

        $this->filename = $filename;
        $this->filetype = $filetype;
    }

    /**
     * read XML-File
     */
    public function run()
    {
        return $this->read_document($this->filename);
    }

    /**
     * convert XML-File to openTrans
     *
     * @param string filename
     */
    protected function read_document($filename)
    {
        if (!is_string($filename)) {
            throw new OpentransException('$filename must be a string.');
        }

        return $this->get_document_data($filename);
    }

    /**
     * prepare the XML-Data
     *
     * @param string src_file
     */
    protected function get_document_data($src_file)
    {
        if (!is_string($src_file)) {
            throw new OpentransException('$src_file must be a string.');
        }

        // Determine method

        $fn = 'get_document_data_' . $this->get_filetype();

        if (!method_exists($this, $fn)) {
            throw new OpentransException('No method found for writing documents of type "' . $this->get_filetype() . '"');
        }

        return $this->$fn($src_file);
    }

    /**
     * returns the file type
     */
    protected function get_filetype()
    {
        return $this->filetype;
    }

    /**
     * Version is not valid if major counter are different.
     *
     * @param string $version_orderapi_shop
     * @return boolean
     */
    public function is_version_valid($version_orderapi_shop = NULL)
    {
        if ((int)OpentransDocument::VERSION_ORDERAPI != (int)$version_orderapi_shop) {
            return false;
        }

        return true;
    }

    public function is_testsieger_party_id($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be a string.');
        }

        if (preg_match('/TS-.*/', $value) or preg_match('/C24-.*/', $value)) {
            return true;
        }

        return false;
    }
}