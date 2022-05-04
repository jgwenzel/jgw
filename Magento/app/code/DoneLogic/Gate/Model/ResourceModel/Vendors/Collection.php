<?php
namespace DoneLogic\Gate\Model\ResourceModel\Vendors;
/**
 * DoneLogic/Gate/Model/ResourceModel/Vendors/Collection.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class Collection extends AbstractCollection
{

    protected $_idFieldName = 'vendor_id';
    protected $_eventPrefix = 'donelogic_gate_vendors_collection';
	protected $_eventObject = 'vendors_collection';

    protected function _construct()
    {
        $this->_init(
            'DoneLogic\Gate\Model\Vendors',
            'DoneLogic\Gate\Model\ResourceModel\Vendors'
        );
    }
    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

}