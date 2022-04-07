<?php
namespace DoneLogic\Core\Controller\Adminhtml;
 /**
 * DoneLogic/Core/Controller/Adminhtml/Records.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use DoneLogic\Core\Model\RecordsFactory;
use Magento\Framework\Message\ManagerInterface;

class Records extends Action
{
    protected $_coreRegistry;
    protected $_resultPageFactory;
    protected $_recordsFactory;
    protected $_messageManager;
 
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        RecordsFactory $recordsFactory,
        ManagerInterface $messageManager
    ) 
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_recordsFactory = $recordsFactory;
        $this->_messageManager = $messageManager;
 
    }
    public function execute()
    {
        //this class is so far only extended, so does not execute anything itself.
    }
 
    protected function _isAllowed()
    {
        //placeholder function
        return true;
    }
}