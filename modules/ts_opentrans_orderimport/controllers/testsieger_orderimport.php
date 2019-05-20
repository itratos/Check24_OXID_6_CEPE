<?php

    /**
     * @copyright (C) 2013 Testsieger Portal AG
     *
     * @license GPL 3:
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
    class testsieger_orderimport extends oxAdminView {

        /**
         * Executes parent method parent::render() and gets data to be displayed in template
         *
         * @return string Filename of template to be displayed.
         */
        public function render()
        {

            parent::render();

            $oConf = $this->getConfig();
            $shopId = $oConf->getShopId();
            foreach ($this->get_fields_to_save() as $fieldname) {
                $this->_aViewData[$fieldname] = $oConf->getShopConfVar($fieldname, NULL, 'testsieger_orderimport');
            }

            if (!$this->_aViewData['testsieger_ftphost']) {
                $this->_aViewData['testsieger_ftphost'] = 'partnerftp.testsieger.de';
            }

            if (!$this->_aViewData['testsieger_ftpport']) {
                $this->_aViewData['testsieger_ftpport'] = '44021';
            }

            $this->_aViewData['ts_logo'] = $oConf->getImageUrl(true) . 'testsieger-200x59.png';

            $this->_aViewData['ts_logs'] = @file_get_contents( getShopBasePath() .'modules/ts_opentrans_orderimport/data/testsieger_logfile.html');

            $this->_aViewData['iframeurl'] = oxRegistry::get("oxUtilsUrl")->appendUrl(
                                                $oConf->getShopHomeUrl(NULL, false),
                                                array('shp' => $shopId,
                                                      'cl' => 'testsieger_opentrans_orderimport',
                                                      'fnc' => 'import',
                                                      'key' => $this->_aViewData['testsieger_ftpuser'] ,
                                                )
            );

            $aPaymentList = oxDb::getDb()->getAll('SELECT oxid, oxdesc FROM oxpayments  ORDER BY oxsort');
            $this->_aViewData['paymentlist'] = $aPaymentList;

            return 'testsieger_orderimport.tpl';

        }

        /**
        * Custom save function for module configuration
        */
        public function savesettings() {

            $oConf = $this->getConfig();
            $params = oxRegistry::getConfig()->getRequestParameter('editval');

            if (!isset($params['testsieger_shippingtype'])) {
                $params['testsieger_shippingtype'] = '';
            }

            foreach ($this->get_fields_to_save() as $fieldname) {

                if (isset($params[$fieldname])) {
                    $oConf->saveShopConfVar('string', $fieldname, $params[$fieldname], NULL, 'testsieger_orderimport');
                }

            }

        }

        /**
        * Get array of declared config fields
        * @returns array Array of fields
        */
        protected function get_fields_to_save() {
            return array('testsieger_active', 'testsieger_ftpuser', 'testsieger_ftppass', 'testsieger_ftphost',
                            'testsieger_ftpport', 'testsieger_shippingtype', 'testsieger_sendorderconf',
                            'testsieger_reducestock', 'testsieger_paymenttype_fallback', 'testsieger_paymenttype_ts');
        }

        /**
        * Called from the admin mask, will delete (rotate) logfile
        */
        public function deletelog() {
           // unlink($this->getViewConfig()->getModulePath('ts_opentrans_orderimport') .'/data/testsieger_logfile.html');

           if(file_exists(getShopBasePath().'modules/ts_opentrans_orderimport/data/testsieger_logfile.html'))
           {
                 unlink(getShopBasePath().'modules/ts_opentrans_orderimport/data/testsieger_logfile.html');
           }


        }

    }
