<?php


namespace Itratos\Check24Connector\Application\Controller\Admin;


/**
 * Admin view controller for order and orderchange import from CHECK24
 */
class RequestList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = \Itratos\Check24Connector\Application\Model\ItrCheck24OrderChangeRequest::class;

    /**
     * Enable/disable sorting by DESC (SQL) (defaultfalse - disable).
     *
     * @var bool
     */
    protected $_blDesc = true;

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = "oxtimestamp";

    /**
     * Executes parent method parent::render() and returns name of template
     * file "check24connector_requestlist.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        // sets up navigation data
        $this->_setupNavigation('order_list');
        $this->_aViewData['actlocation'] = 'check24connector_requestoverview';

        $folders = $this->getConfig()->getConfigParam('aOrderfolder');
        $folder = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("folder");
        // first display new orders
        if (!$folder && is_array($folders)) {
            $names = array_keys($folders);
            $folder = $names[0];
        }

        $search = ['oxorderarticles' => 'ARTID', 'oxpayments' => 'PAYMENT'];
        $searchQuery = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("addsearch");
        $searchField = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("addsearchfld");

        $this->_aViewData["folder"] = $folder ? $folder : -1;
        $this->_aViewData["addsearchfld"] = $searchField ? $searchField : -1;
        $this->_aViewData["asearch"] = $search;
        $this->_aViewData["addsearch"] = $searchQuery;
        $this->_aViewData["afolder"] = $folders;

        return "check24connector_requestlist.tpl";
    }


    public function reject()
    {
        $this->rejectRequest();
    }

    public function confirm()
    {
        $this->confirmRequest();
    }


    public function rejectRequest()
    {
        $request = oxNew(\Itratos\Check24Connector\Application\Model\ItrCheck24OrderChangeRequest::class);
        if ($request->load($this->getEditObjectId())) {
            $request->rejectRequest();
        }

        $this->resetContentCache();

        $this->init();
    }

    public function confirmRequest()
    {
        $request = oxNew(\Itratos\Check24Connector\Application\Model\ItrCheck24OrderChangeRequest::class);
        if ($request->load($this->getEditObjectId())) {
            $request->confirmRequest();
        }

        $this->resetContentCache();

        $this->init();
    }


    /**
     * Sets-up navigation parameters
     *
     * @param string $node active view id
     */
    protected function _setupNavigation($node)
    {
        //TODO:  get active tab by id!!!!
        $activeTab = 7;
        $adminNavigation = $this->getNavigation();
        $this->_aViewData['editnavi'] = $adminNavigation->getTabs($node, $activeTab);
        $this->_aViewData['default_edit'] = $adminNavigation->getActiveTab($node, $this->_iDefEdit);
        $this->_aViewData['actedit'] = $activeTab;
    }

    /**
     * Returns list filter array
     *
     * @return array
     */
    public function getListFilter()
    {
        if ($this->_aListFilter === null) {
            $this->_aListFilter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("where");
            if(!$this->_aListFilter) {
                $this->_aListFilter['itrcheck24_orderchangerequest']['response'] = 0;
            }
        }

        return $this->_aListFilter;
    }
}
