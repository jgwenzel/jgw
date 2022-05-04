<?php
namespace DoneLogic\Gate\Controller\Adminhtml\Vendors;
  /**
 * DoneLogic/Gate/Controller/Adminhtml/Vendors/Save.php
 * @author John Wenzel johngwenzel@gmail.com
 * Save Action
 */
use DoneLogic\Gate\Controller\Adminhtml\Vendors;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use DoneLogic\Gate\Model\VendorsFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Message\ManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class Save extends Vendors
{
    protected $_coreRegistry;
    protected $_resultPageFactory;
    protected $_vendorsFactory;
    protected $_messageManager;
    protected $_fileSystem;
    protected $_uploaderFactory;
    protected $_adapterFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        VendorsFactory $vendorsFactory,
        ManagerInterface $messageManager,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory 
    ) 
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_vendorsFactory = $vendorsFactory;
        $this->_messageManager = $messageManager;
        $this->_fileSystem = $fileSystem;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_adapterFactory = $adapterFactory;

        parent::__construct(
            $context,
            $coreRegistry,
            $resultPageFactory,
            $vendorsFactory,
            $messageManager
        );
    }

    /**
     * Save vendor form data to donelogic_gate
     * redirects on successful save or error
     * @return void
     */
    public function execute()
    {
        $isPost = $this->getRequest()->getPost();

        if ($isPost) {
            $vendorModel = $this->_vendorsFactory->create();
            $vendorId = $this->getRequest()->getParam('vendor_id');
 
            if ($vendorId) {
                $vendorModel->load($vendorId);
            }
            $formData = $this->getRequest()->getParam('vendor');

            //for delete image
            if (isset($formData['image_url']['delete'])) {
                if ($formData['image_url']['delete'] == 1) {
                    $formData['image_url'] = '';
                }
            }
    
            //for upload image
            if ((isset($_FILES['image_url']['name'])) && ($_FILES['image_url']['name'] != '') && (!isset($formData['image_url']['delete']))) {
                try
                {
                    $uploaderFactory = $this->_uploaderFactory->create(['fileId' => 'image_url']);
                    $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $imageAdapter = $this->_adapterFactory->create();
                    $uploaderFactory->setAllowRenameFiles(true);
                    $uploaderFactory->setFilesDispersion(true);
                    $mediaDirectory = $this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationPath = $mediaDirectory->getAbsolutePath('donelogic');
                    //echo "<br/>destination path".$destinationPath;
                    $result = $uploaderFactory->save($destinationPath);
                    // print_r($result);
                    if (!$result) {
                        throw new LocalizedException
                            (
                            __('File cannot be saved to path: $1', $destinationPath)
                        );
                    }
    
                    $imagePath = 'donelogic' . $result['file'];
                    //echo "<br/> Image store ".$imagePath;
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
            
            //var_dump($formData);
            //exit;

            /* services is usually multiselect - convert to comma delim str  
             * NOTE: services is a string when the vendor is the _SERVICES_ vendor
             * */
            if(isset($formData['services']) && is_array($formData['services'])) {
                $str = implode( ',', $formData['services']);
                $formData['services'] = $str;
            }

            /* service_regions is a multiselect - convert to comma delim str  */
            if(isset($formData['service_regions']) && is_array($formData['service_regions'])) {
                $str = implode( ',', $formData['service_regions']);
                $formData['service_regions'] = $str;
            }

            $vendorModel->setData($formData);
 
            try {
                // Save vendor
                $vendorModel->save();
 
                // Display success message
                $this->_messageManager->addSuccess(__('The vendor has been saved.'));
 
                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['vendor_id' => $vendorModel->getId(), '_current' => true]);
                    return;
                }
 
                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->_messageManager->addError($e->getMessage());
            }
 
            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['vendor_id' => $vendorId]);
        }
    }
}