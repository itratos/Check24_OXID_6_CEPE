<?php

namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentHeaderControlinfo
{

    /**
     * Stops automatic processing when not empty
     * and is a notice why manual adaptation is necessary
     *
     * @var string $stop_automatic_processing
     */
    private $stop_automatic_processing = NULL;

    /**
     * @var string $generator_name
     */
    private $generator_name = NULL;

    /**
     * @var integer $generation_date
     */
    private $generation_date = NULL;

    /**
     * Constructor
     *
     * @param string $stop_automatic_processing
     * @param string $generator_name
     * @param integer $generation_date
     */
    public function __construct($stop_automatic_processing = NULL, $generator_name = NULL, $generation_date = NULL)
    {
        if ($stop_automatic_processing !== NULL && !is_string($stop_automatic_processing)) {
            throw new OpentransException('$stop_automatic_processing must be a string.');
        }

        if (!is_string($generator_name)) {
            throw new OpentransException('$generator_name must be a string.');
        }

//        if (!is_string($generation_date)) {
//            throw new OpentransException('$generation_date must be string.');
//        }

        $this->stop_automatic_processing = $stop_automatic_processing;
        $this->generator_name = $generator_name;
        $this->generation_date = $generation_date;
    }

    /**
     * Sets stop_automatic_processing
     *
     * @param string $value stop_automatic_processing
     * @return void
     */
    public function set_stop_automatic_processing($value)
    {
        if (!is_string($value)) {
            throw new OpentransException('$value must be a string.');
        }

        $this->stop_automatic_processing = $value;
    }

    /**
     * Returns generator_name
     *
     * @return string generator_name
     */
    public function get_generator_name()
    {
        return $this->generator_name;
    }

    /**
     * Returns generation_date
     *
     * @return string generation_date
     */
    public function get_generation_date()
    {
        return $this->generation_date;
    }

    /**
     * Returns stop_automatic_processing
     *
     * @return string stop_automatic_processing
     */
    public function get_stop_automatic_processing()
    {
        return $this->stop_automatic_processing;
    }

}