<?php

    /**
     * openTrans auto loader
     *
     * @copyright Testsieger Portal AG
     * @license GPL 3:
     *
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
    class rs_opentrans {

        /**
         * @var boolean strict
         */
        static $strict = false;

        /**
         * @var float version
         */
        static $version = 2.0;

        /**
         * Sets strict
         *
         * @param boolean $strict
         * @return void
         */
        public static function set_strict($strict) {
            self::$strict = (bool)$strict;
        }

        /**
         * Sets version
         *
         * @param float $version
         * @return void
         */
        public static function set_version($version) {

            if (!is_int($version) && !is_float($version)) {
                $version = (float)$version;
            }

            self::$version = $version;

        }

        /**
         * Class Autoloader
         *
         * @param string $class
         */
        public function __autoload($class) {

            if (substr($class, 0, 13) == 'rs_opentrans_') {

                $file = dirname(__FILE__). '/' . $class .'.class.php';

                require_once($file);

            }

        }

    }

    spl_autoload_register(array(new rs_opentrans(), '__autoload'));
