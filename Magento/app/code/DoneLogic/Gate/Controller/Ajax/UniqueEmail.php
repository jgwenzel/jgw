<?php 
namespace DoneLogic\Gate\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use DoneLogic\Gate\Model\VendorsFactory;

class UniqueEmail extends Action
{
  /**
   * @var \Magento\Framework\Controller\Result\JsonFactory
   */
  protected $_resultJsonFactory;

  /**
   * @var \Magento\Customer\Model\Customer 
   */
  protected $_vendorsFactory;

  /**
   * @param \Magento\Framework\App\Action\Context $context
   * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
   * @param \DoneLogic\Gate\Model\VendorsFactory $vendorsFactory
   */
  public function __construct(
      Context $context,
      JsonFactory $resultJsonFactory,
      VendorsFactory $vendorsFactory
  ) {
      $this->_resultJsonFactory = $resultJsonFactory;
      $this->_vendorsFactory = $vendorsFactory;
      parent::__construct($context);
  }

  /**
   * Checks param email to see if it already exists in donelogic_gate.
   * @return json either true or error message
   */
  public function execute()
  {
      $resultJson = $this->_resultJsonFactory->create();
      $email = $this->getRequest()->getParam('email');

      $vendorsFactory = $this->_vendorsFactory->create();
      $vendorData = $vendorsFactory->getCollection()->addFieldToFilter('email', $email);

      if(!count($vendorData)) {
        $resultJson->setData('true');
      } else {
        $resultJson->setData('That email is already taken, try another one');
      }
      return $resultJson;
  }
}