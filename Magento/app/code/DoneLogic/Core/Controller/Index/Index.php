<?php
namespace DoneLogic\Core\Controller\Index;
 /**
 * DoneLogic/Core/Controller/Index/Index.php
 * @author John Wenzel johngwenzel@gmail.com
 * non-Admin Index Action exists only for future implementation and testing
 */
use Magento\Framework\App\Action\Context;
use DoneLogic\Core\Model\ResourceModel\Records\CollectionFactory;
 
class Index extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $_recordsFactory;
 
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CollectionFactory $recordsFactory)
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_recordsFactory = $recordsFactory;
    }
 
    public function execute()
    {
        /* TESTING
        echo "Get Data From donelogic_core table";
        $this->_recordsFactory->create();
        $collection = $this->_recordsFactory->create()
            ->addFieldToSelect(array('name','updated_at','created_at'))
            ->setPageSize(10);
        echo '<pre>';
        print_r($collection->getData());
        echo '<pre>';
        */
    }
}