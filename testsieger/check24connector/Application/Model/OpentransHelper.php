<?php

namespace TestSieger\Check24Connector\Application\Model;

class OpentransHelper
{
    public static function formatString($string, $length) {
        return sprintf('%0' . $length . 'd', $string);
    }
}
