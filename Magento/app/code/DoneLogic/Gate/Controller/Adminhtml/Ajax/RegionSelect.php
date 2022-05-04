<?php
namespace DoneLogic\Gate\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class RegionSelect extends Action
{
    protected $_countryFactory;

    protected $_jsonFactory;

    public function __construct(
        Context $context,
        CountryFactory $countryFactory,
        JsonFactory $jsonFactory
    )
    {
        $this->_countryFactory = $countryFactory;
        $this->_jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    /**
     * Builds region select options html and service_regions <select multiple>
     * options html, packs them in an array and returns json
     * @params Request params: country_code, region_name, service_regions
     * * country_code determines the regions options
     * * if region_name is passed, that is selected for <select>
     * * if service_regions is passed, string is exploded on commas
     * * to array and each is selected for <select multiple>
     * 
     * @return Json object
     */
    public function execute()
    {
        $countrycode = $this->getRequest()->getParam('country_code');

        $region_name = $this->getRequest()->getParam('region_name');

        $service_regions = $this->getRequest()->getParam('service_regions');

        $service_regions = urldecode($service_regions);
        $service_regions = explode(",", $service_regions);

        if($region_name == NULL){
            $region_name='';
        }

        $selected = '';

        $opts = "<option value=''>--Select Region--</option>";
        $multi_opts = "<option value=''>--Select Service Regions--</option>";

        if ($countrycode != '') {
            $regions = $this->_countryFactory->create()->setId(
                    $countrycode
                )->getLoadedRegionCollection()->toOptionArray();
            foreach ($regions as $reg) {
                if($reg['value']){
                    //for select
                    if($region_name == $reg['label']) { 
                        $selected = 'selected'; 
                    } else {  
                        $selected = ''; 
                    }
                    $opts .= "<option value='".$reg['label']."'  ".$selected." >" .$reg['label']. "</option>";

                    //for multi select
                    if(in_array( $reg['label'], $service_regions)) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $multi_opts .= "<option value='".$reg['label']."'  ".$selected." >" .$reg['label']. "</option>";
                }
            }
        }
        //$result['htmlcontent']=$state;

        $json = $this->_jsonFactory->create();
        $data = array('select' => $opts, 'multiselect' => $multi_opts);
        $json->setData(['htmldata' => $data]);
        return $json;
        /*
        $objectManager = ObjectManager::getInstance();
         $this->getResponse()->representJson(
            $objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
        */
    }
}