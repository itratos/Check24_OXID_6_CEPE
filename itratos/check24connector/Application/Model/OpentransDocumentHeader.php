<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentHeader
{

    /**
     * @var object OpentransDocumentHeaderControlinfo
     */
    protected $controlinfo = NULL;

    /**
     * Creates a openTrans controlinfo
     *
     * @param string $stop_automatic_processing
     * @param string $generator_info
     * @param integer $generation_date
     *
     * @return object OpentransDocumentHeaderControlinfo
     */
    public function create_controlinfo($stop_automatic_processing = NULL, $generator_info = NULL, $generation_date = NULL)
    {
        if ($stop_automatic_processing !== NULL && !is_string($stop_automatic_processing)) {
            throw new OpentransException('$stop_automatic_processing must be string.');
        }

        if (!is_string($generator_info)) {
            throw new OpentransException('$generator_info must be string.');
        }

//        if (!is_string($generation_date)) {
//            throw new OpentransException('$generation_date must be string.');
//        }

        $this->controlinfo = new OpentransDocumentHeaderControlinfo($stop_automatic_processing, $generator_info, $generation_date);

        return $this->controlinfo;
    }

    /**
     * Returns controlinfo
     *
     * @return OpentransDocumentHeaderControlinfo
     */
    public function get_controlinfo()
    {
        return $this->controlinfo;
    }

}
