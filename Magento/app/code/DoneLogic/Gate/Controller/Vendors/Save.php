<?php
namespace DoneLogic\Gate\Controller\Vendors;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use DoneLogic\Gate\Model\VendorsFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Message\ManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    protected $_vendorsFactory;
    protected $_messageManager;
    protected $_fileSystem;
    protected $_uploaderFactory;
    protected $_adapterFactory;
    protected $_customerSession;

    public function __construct(
        Context $context,
        VendorsFactory $vendorsFactory,
        ManagerInterface $messageManager,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        CustomerSession $customerSession
    ) 
    {
        $this->_vendorsFactory = $vendorsFactory;
        $this->_messageManager = $messageManager;
        $this->_fileSystem = $fileSystem;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_adapterFactory = $adapterFactory;
        $this->_customerSession = $customerSession;

        parent::__construct( $context );
    }

    /**
     * Save vendor data from form.
     * Checks if logged in Customer ID matches customer_id of vendor data
     * Note: service_regions are sliced to 10 maximum
     * Data Tranformations:
     *   'services array' -> comma delim string
     *   'services' -> comma delim string
     *    all string fields are run through strip_tags() via stripTagsMixed()
     * @return void
     * redirects upon successful save or error
     */
    public function execute()
    {
        $postData = (array) $this->getRequest()->getPost();
        $formData = [];
        if($postData) {

            $vendorModel = $this->_vendorsFactory->create();

            if(isset($postData['vendor_id'])) {
                if(isset($postData['customer_id']) && $this->customerCanEdit( $postData['customer_id'])) {
                    $vendorModel->load($postData['vendor_id']);
                } else {
                    $this->_messageManager->addErrorMessage('You do not have priveleges to edit this vendor.');
                    return false;
                }
            }

            $formData = $postData;
    
            //for delete image if functionality is provided
            if(isset($formData['image_url']['delete'])) {
                if ($formData['image_url']['delete'] == 1) {
                    $formData['image_url'] = '';
                }
            }

            //for upload image
            if (isset($_FILES['image_url']['name']) && $_FILES['image_url']['name'] != '' ) {
                try
                {
                    $uploaderFactory = $this->_uploaderFactory->create(['fileId' => 'image_url']);
                    $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $imageAdapter = $this->_adapterFactory->create();
                    $uploaderFactory->setAllowRenameFiles(true);
                    $uploaderFactory->setFilesDispersion(true);
                    $mediaDirectory = $this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationPath = $mediaDirectory->getAbsolutePath('donelogic');
                    $result = $uploaderFactory->save($destinationPath);
                    if (!$result) {
                        throw new LocalizedException
                            (
                            __('File cannot be saved to path: $1', $destinationPath)
                        );
                    }
    
                    $imagePath = 'donelogic' . $result['file'];
                    $formData['image_url'] = $imagePath;
                }
                 catch (\Exception $e) {
                    $this->_messageManager->addError(__("Image not Uploaded. Please Try Again"));
                }
            }
            elseif(isset($formData['image_url']) && is_array($formData['image_url'])) {
                //then we need to set image_url to a string value
                $value = $formData['image_url']['value'];
                $formData['image_url'] = $value;
            }
            
            /* services is a multiselect - convert to comma delim str  */
            if(isset($formData['services']) && is_array($formData['services'])) {
                $str = implode( ',', $formData['services']);
                $formData['services'] = $str;
            }
            
            /* service_regions is a multiselect - convert to comma delim str  */
            if(isset($formData['service_regions']) && is_array($formData['service_regions'])) {
                //cut to 10 maximum
                $formData['service_regions'] = array_slice($formData['service_regions'],0,10);
                if(isset($formData['region_name'])) {
                    if(!in_array($formData['region_name'], $formData['service_regions'])) {
                        array_push($formData['service_regions'], $formData['region_name']);
                    }
                }
                $str = implode( ',', $formData['service_regions']);
                $formData['service_regions'] = $str;
            }

            $formData = $this->stripTagsMixed( $formData );

            $vendorModel->setData($formData);
 
            try {
                // Save vendor
                $vendorModel->save();
 
                // Display success message
                if(isset($formData['vendor_id'])) {
                    $this->_messageManager->addSuccess(__('Good Job. Your listing has been updated.'));
                    // Go to Vendor Page View
                    $redirect = 'gate/vendors/vendor/vendor_id/' . $formData['vendor_id'] . '/' . urlencode($formData['company']) . '/';
                    $this->_redirect( $redirect );
                }
                else {
                    $this->_messageManager->addSuccess(__('Good Job. Your company has been submitted to the Services Directory. We will email you upon approval of submission. Thank you.'));
                    // Go to Services Directory
                    $this->_redirect('*/*/view');
                }
                return;
            } catch (\Exception $e) {
                $this->_messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/editvendor');
        return;
    }

    /**
     * Can customer edit this vendor listing?
     * Check passed id (customer_id from vendor listing) and see if it matches
     * logged in Customer ID.
     */
    public function customerCanEdit( $customer_id ) {
        if(!$this->_customerSession->isLoggedIn()) {
            return false;
        }
        $cid = $this->_customerSession->getCustomer()->getId();
        if($cid == $customer_id) {
            return true;
        }
        return false;
    }

    /**
     * Recursive Function that strips tags from multidimensional array or string
     * @param mixed array or string
     * @return mixed whatever was passed sans tags
     */
    public function stripTagsMixed( $mixed ) {
        $new_array = [];
        if(is_array($mixed)) {
            foreach($mixed as $name => $value) {
                $new_array[$name] = $this->stripTagsMixed($value);
            }
            return $new_array;
        }
        elseif(is_string($mixed)) {
            return strip_tags($mixed);
        }
        return $mixed;
    }
}