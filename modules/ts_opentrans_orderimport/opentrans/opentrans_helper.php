<?php

class opentrans_helper
{
    public static function formatString($string, $length) {
        return sprintf('%0' . $length . 'd', $string);
    }
}