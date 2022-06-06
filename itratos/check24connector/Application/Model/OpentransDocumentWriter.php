<?php


namespace Itratos\Check24Connector\Application\Model;

abstract class OpentransDocumentWriter
{

    /**
     * File handler
     */
    protected $fh = NULL;

    /**
     * XML-Documents to write
     *
     * @var array
     */
    private $documents = array();
    /**
     * Path for filesave for the XML-Documents
     *
     * @var string
     */
    private $destination_file_path = NULL;

    /**
     * Path for remote filesave for the XML-Documents
     *
     * @var string
     */
    private $destination_remote_file_path = NULL;

    /**
     * construct the writer
     *
     * @param array documents
     * @param string destination_file_path
     */
    public function __construct($documents, $destination_file_path, $destination_remote_file_path = null, $ftpstream = null)
    {

        if (!is_array($documents)) {
            throw new OpentransException('$documents must be an array.');
        }

        if (!is_string($destination_file_path)) {
            throw new OpentransException('$destination_file_path must be a string.');
        }

        $this->documents = $documents;
        $this->destination_file_path = $destination_file_path;
        $this->destination_remote_file_path = $destination_remote_file_path;
    }

    /**
     * write the XML
     */
    public function run()
    {
        $pathinfo = pathinfo($this->destination_file_path);

        $tmp_file = $pathinfo['dirname'] . '/.' . $pathinfo['filename'] . '.writing';

        $this->fh = fopen($tmp_file, 'w+');

        $this->write_header();

        for ($i = 0, $i_max = count($this->documents); $i < $i_max; ++$i) {
            $this->write_document($this->documents[$i]);
        }

        $this->write_footer();

        fclose($this->fh);

        if (!rename($tmp_file, $this->destination_file_path)) {
            Logger::msglog('Error when moving local file ' . $tmp_file . 'to: ' . $this->destination_file_path, 3);

        }

        if ($this->destination_remote_file_path) {
            $oProcess = oxNew(\Itratos\Check24Connector\Application\Model\Process::class);
            $result = Ftps::ftps_put($this->destination_file_path, $this->destination_remote_file_path);
            if (!$result) {
//                Logger::msglog(
//                    'Error when uploading file ' . $this->destination_file_path . ' to remote: ' . $this->destination_remote_file_path . print_r(error_get_last()), 3
//                );
            } else {
                //Logger::msglog('Upload file ' . $this->destination_file_path . 'to remote: ' . $this->destination_remote_file_path, 1);
            }
        }
    }

    protected function write_header()
    {
    }

    protected function write_footer()
    {
    }

    /**
     * write the XML content
     *
     * @param string document
     */
    protected function write_document($document)
    {

        if (!$document instanceof OpentransDocument) {
            throw new OpentransException('$document must be type of OpentransDocument.');
        }

        fputs($this->fh, $this->get_document_data($document));

    }

    /**
     * prepare the XML-Data defined by document type
     *
     * @param string src
     */
    protected function get_document_data($src)
    {

        // Determine method

        $fn = 'get_document_data_' . $src->get_document_type();

        if (!method_exists($this, $fn)) {
            throw new OpentransException('No method found for writing documents of type "' . $src->get_document_type() . '"');
        }

        return $this->$fn($src);

    }

    /**
     * returns the file handler
     */
    protected function get_fh()
    {
        return $this->fh;
    }

}