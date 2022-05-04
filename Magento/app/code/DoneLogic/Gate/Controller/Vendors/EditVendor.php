<?php
namespace DoneLogic\Gate\Controller\Vendors;
 /**
 * @author John Wenzel johngwenzel@gmail.com
 */

use DoneLogic\Gate\Controller\Vendors\Vendors;

class EditVendor extends Vendors
{
    /**
     * Redirects to ' / /save' if form data has posted
     * Sets Theme, page title and returns page
     * @return resultPageFactory->create()
     */
    public function execute()
    {
        $post = $this->_request->getPost();
        if(!empty($post) && $this->_request->getParam('company')) {
            $this->_redirect('*/*/save');
            return;
        }

        $vendor = $this->_vendorsFactory->create();
        $this->_designInterface->setDesignTheme('DoneLogic/BritePipe', 'frontend');
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Vendor'));
        return $resultPage;
    }
}