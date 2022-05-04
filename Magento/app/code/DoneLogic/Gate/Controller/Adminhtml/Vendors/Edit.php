<?php
namespace DoneLogic\Gate\Controller\Adminhtml\Vendors;
 
use DoneLogic\Gate\Controller\Adminhtml\Vendors;
 /**
 * @author John Wenzel johngwenzel@gmail.com
 * Edit Action
 */
class Edit extends Vendors
{
    /**
     * @return void
     */
    public function execute()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id');
 
        $model = $this->_vendorsFactory->create();
 
        if ($vendorId) {
            $model->load($vendorId);
            if (!$model->getId()) {
                $this->_messageManager->addError(__('This vendor no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            
            /**
             * The Services Vendor Listing has all the services names and must
             * be able to be edited with text area. Every other vendor uses a
             * multiselect populated by these values.
             */
            if(!$this->isServicesVendor( $model->getVendorId() )) {
                //comma delim str to array
                $services = $model->getServices();
                $services_array = explode(",",$services);
                $model->setServices($services_array);
            }
            
            //comma delim str to array
            $service_regions = $model->getServiceRegions();
            $regions_array = explode(",",$service_regions);
            $model->setServiceRegions($regions_array);
        }

        $this->_coreRegistry->register('donelogic_gate', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('DoneLogic_Gate::gate_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Vendors'));
 
        return $resultPage;
    }
}