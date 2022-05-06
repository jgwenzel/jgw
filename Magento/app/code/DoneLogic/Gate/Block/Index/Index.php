<?php
namespace DoneLogic\Gate\Block\Index;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\View\Page\Config;
use DoneLogic\Gate\Model\VendorsFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use DoneLogic\SendEmail\Helper\Data as MailHelper;
use Magento\Framework\View\Element\Template;

class Index extends Template 
{
    protected $_pageConfig;
    protected $_vendorsFactory;
    protected $_countryFactory;
    protected $_country;
    protected $_request;
    protected $_messageManager;
    protected $_customerSession;
    protected $_storeManager;
    protected $_vendor;
    protected $_mailHelper;

    public function __construct(
        Context $context,
        Config $pageConfig,
        VendorsFactory $vendorsFactory,
        CountryFactory $countryFactory,
        Country $country,
        RequestInterface $request,
        ManagerInterface $messageManager,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        MailHelper $mailHelper,
        $data = []
    ) 
    {
        parent::__construct($context, $data);
        
        $this->_pageConfig = $pageConfig;
        $this->_vendorsFactory = $vendorsFactory;
        $this->_countryFactory = $countryFactory;
        $this->_country = $country;
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_mailHelper = $mailHelper;
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
    }

    public function getMediaUrl( $path )
    {
        return $this->_storeManager->getStore()->getBaseUrl( UrlInterface::URL_TYPE_MEDIA ) . $path;
    }

    /**
     * Gets the vendor from donelogic_gate by vendor_id from getParam()
     * @var vendor_id getParam()
     * @return $vendor object
     */
    public function getVendor()
    {
        $vendor = $this->_vendorsFactory->create();
        $vendor_id = $this->_request->getParam('vendor_id');
        $vendor->load( $vendor_id );

        if (!$vendor->getVendorId()) {
            $this->_messageManager->addErrorMessage(__('Vendor not found'));
            return false;
        }
        if (!($vendor->getActive() == 1)) {
            $this->_messageManager->addErrorMessage(__('Listing not active'));
            /* if vendor is logged in, they may view their listing
            otherwise the method returns false */
            return $this->getCustomerVendor();
        }

        $this->_vendor = $vendor;
        return $this->_vendor;
    }

    /**
     * Gets Vendor by customer_id from donelogic_gate if it exists
     * @var customer_id
     * @return $vendor object
     */
    public function getCustomerVendor() {
        $cid = $this->getCustomerId();
        if($cid > 0) {
            $vendors = $this->_vendorsFactory->create();
            $collection = $vendors->getCollection();
            $collection->addFieldToFilter('customer_id',$cid);
            $vendor = $collection->getFirstItem();
            if($vendor->getVendorId()) {
                return $vendor;
            }
            return false;
        }
        return false;
    }

    /**
     * Gets Default Vendor from donelogic_gate if it exists
     * Looks first for category == 'ADS' vendor
     * Looks second for the first listing of category == 'VENDOR'
     * @return $vendor object
     */
    public function getDefaultVendor()
    {
        $vendors = $this->_vendorsFactory->create();
        $collection = $vendors->getCollection();
        $collection->addFieldToFilter('category','ADS');
        $vendor = $collection->getFirstItem();
        if($vendor->getVendorId()) {
            $this->_vendor = $vendor; 
            return $vendor;
        }
        //If no ADS listing, just get first VENDOR listing
        $vendors = $this->_vendorsFactory->create();
        $collection = $vendors->getCollection();
        $collection->addFieldToFilter('category','VENDOR');
        $vendor = $collection->getFirstItem();
        if($vendor->getVendorId()) {
            $this->_vendor = $vendor; 
            return $vendor;
        }
    }
    
    /**
     * Gets default meta data to be used when request is bad and
     * does not produce a vendor or list of vendors
     * @return array: meta tags array
     */
    public function getDefaultMeta() {
        //This is for if vendor does not exist
        //no sense in making it fancy
        $meta = [];
        $meta['title'] = 'Service Directory';
        $meta['description'] = 'Service Directory';
        $meta['keywords'] = 'service directory';
        $meta['og_title'] = 'Service Directory';
        $meta['og_description'] = 'Service Directory';
        $meta['og_image'] = ' ';
        $meta['og_url'] = ' ';
        return $meta;
    }

    /**
     * Gets the Services Vendor
     * This listing must exist. It holds all the services that are used
     * by all vendors in the <select multiple>.
     * The service vendor:
     *    must have company == '_SERVICES_'
     *    must have category == 'SETTINGS'
     *    must have services == 'service one,service two,...'
     *    should have customer_id == 0
     *    should have active == 0 (so it's not visible on frontend)
     *    the country of the service vendor is the default country
     * @return $vendor object
     */    
    public function getServicesVendor() {
        $vendors = $this->_vendorsFactory->create();
        $collection = $vendors->getCollection();
        $collection->addFieldToFilter('company','_SERVICES_');
        $collection->addFieldToFilter('category','SETTINGS');
        $services_vendor = $collection->getFirstItem();
        return $services_vendor;
    }
    /**
     * Is the user logged in as customer?
     */
    public function isLoggedIn() {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * Get Customer ID if logged in
     * @return int
     */
    public function getCustomerId() {
        if($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomer()->getId();
        }
        return 0;
    }

    /**
     * Get the country name by code
     * @param string $countryCode iso2 2-digit country code
     * @return string country name or _UNDEFINED_ if code is empty
     */
    public function getCountryName( $countryCode ){
        if(empty($countryCode)) {
            return '_UNDEFINED_';
        }    
        $country = $this->_countryFactory->create()->loadByCode( $countryCode );
        return $country->getName();
    }

    /**
     * Get the country/region/service params and build a string for use
     * in urls to persist selections.
     * Used to create links to vendor and back to service directory from vendor
     * @return string url params
     */
    public function getBackParams( $pager=true) {
        $country = $this->_request->getParam('country');
        $region = $this->_request->getParam('region');
        $service = $this->_request->getParam('service');
        $page = $this->_request->getParam('p');
        $str = '';
        if(!empty($country)) {
            $str .= 'country/' . urlencode($country) . '/';
        }
        if(!empty($region)) {
            $str .= 'region/' . urlencode($region) . '/';
        }
        if(!empty($service)) {
            $str .= 'service/' . urlencode($service) . '/';
        }
        if($pager && !empty($page) && is_numeric($page)) {

            $str .= 'p/' . $page . '/';
        }
        return $str;
    }

    /**
     * getParam() accessor
     * @param string $param
     * @return string
     */
    public function getParam( $param ) {
        return $this->_request->getParam( $param );
    }

    /**
     * @return void
     */
    public function runTriggers() {
        if($this->isLoggedIn()) {
            $result = $this->_request->getParam('submit');
            if($result == 'yes') {
                $name = $this->_request->getParam('name');
                if($name) {
                    $company = urldecode($name);
                    $message = 'Good News! ' . $this->escapeHtml($company) . ' has added or updated a Service Directory Listing.';
                } else {
                    $message = 'A vendor has added or updated a Service Directory Listing. Company name was not set in Block/Vendors::runTriggers().';
                }
                $this->_mailHelper->sendMail( $message, 'admin');
            }
        }
    }
}