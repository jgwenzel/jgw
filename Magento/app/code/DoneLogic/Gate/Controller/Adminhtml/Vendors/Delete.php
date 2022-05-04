<?php
namespace DoneLogic\Gate\Controller\Adminhtml\Vendors;
/**
 * @author John Wenzel johngwenzel@gmail.com
 * Delete Action
 */
use DoneLogic\Gate\Controller\Adminhtml\Vendors;
 
class Delete extends Vendors
{
    public function execute()
    {
        $vendorId = (int) $this->getRequest()->getParam('vendor_id');
 
        if ($vendorId) {
            /** @var $vendorModel \DoneLogic\Gate\Model\Vendors */
            $vendorModel = $this->_vendorsFactory->create();
            $vendorModel->load($vendorId);
 
            // Check this news exists or not
            if (!$vendorModel->getId()) {
                $this->_messageManager->addError(__('This vendor no longer exists.'));
            } else {
                if( $this->isServicesVendor( $vendorId ) ) {
                    $this->_messageManager->addError('The Services Vendor Listing cannot be deleted. That would break system.');
                    return;
                }

                try {
                    // Delete news
                    $vendorModel->delete();
                    $this->_messageManager->addSuccess(__('The vendor has been deleted.'));
 
                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->_messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['vendor_id' => $vendorModel->getId()]);
                }
            }
        }
    }
}