<?php
namespace DoneLogic\Gate\Controller\Adminhtml;
 /**
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use DoneLogic\Gate\Model\VendorsFactory;
use Magento\Framework\Message\ManagerInterface;

class Vendors extends Action
{
    protected $_coreRegistry;
    protected $_resultPageFactory;
    protected $_vendorsFactory;
    protected $_messageManager;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        VendorsFactory $vendorsFactory,
        ManagerInterface $messageManager
    ) 
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_vendorsFactory = $vendorsFactory;
        $this->_messageManager = $messageManager;
 
    }

    public function execute()
    {
        //this class is so far only extended, so does not execute anything itself.
    }

    /**
     * ABOUT SERVICES VENDOR
     * This listing must exist. It holds all the services that are used
     * by all vendors in the <select multiple>.
     * The service vendor:
     *    must have company == '_SERVICES_'
     *    must have category == 'SETTINGS'
     *    must have services == 'service one,service two,...'
     *    should have customer_id == 0
     *    should have active == 0 (so it's not visible on frontend)
     *    the country of the service vendor is the default country
     */

    /**
     * get the SERVICE VENDOR id from donelogic_gate
     * @return int vendor_id
     */
    public function getServicesVendorId() {
        $services_vendor = $this->getServicesVendor();
        $vendor_id = $services_vendor->getVendorId();
        return $vendor_id;
    }

    /**
     * Check passed vendor_id to see if it's the SERVICE VENDOR
     * @param int vendor_id
     * @return bool
     */
    public function isServicesVendor( $vendor_id ) {
        $services_vendor_id = $this->getServicesVendorId();
        return ($vendor_id == $services_vendor_id);
    }

    /**
     * Get the Services Vendor
     * See filters below for how it is retrieved
     * @return vendor object
     */
    public function getServicesVendor() {
        $vendors = $this->_vendorsFactory->create();
        $collection = $vendors->getCollection();
        $collection->addFieldToFilter('company','_SERVICES_');
        $collection->addFieldToFilter('category','SETTINGS');
        $services_vendor = $collection->getFirstItem();
        return $services_vendor;
    }
}