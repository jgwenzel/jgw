<?php
namespace DoneLogic\Gate\Controller\Vendors;
 /**
 * DoneLogic/Gate/Controller/Adminhtml/Vendors.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use DoneLogic\Gate\Model\VendorsFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\DesignInterface;

class Vendors extends Action
{
    protected $_messageManager;
    protected $_resultPageFactory;
    protected $_vendorsFactory;
    protected $_request;
    protected $_designInterface;
 
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        PageFactory $resultPageFactory,
        VendorsFactory $vendorsFactory,
        RequestInterface $request,
        DesignInterface $designInterface
    ) 
    {
        parent::__construct($context);
        $this->_messageManager = $messageManager;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_vendorsFactory = $vendorsFactory;
        $this->_request = $request;
        $this->_designInterface = $designInterface;
    }

    /**
     * Sets Theme and returns page
     * @return resultPageFactory->create()
     */
    public function execute()
    {
        $this->_designInterface->setDesignTheme('DoneLogic/BritePipe', 'frontend');
        return $this->_resultPageFactory->create();
    }
}