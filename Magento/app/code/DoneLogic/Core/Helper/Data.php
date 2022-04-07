<?php
namespace DoneLogic\Core\Helper;
/**
 * DoneLogic/Core/Helper/Data.php
 * @author John Wenzel johngwenzel@gmail.com
 * This class exists to access isEnabled() which tells if the module
 * frontend renderings are enabled or disabled. Has nothing to do with
 * actual module:enable/disable in cli.
 */
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    const MODULE_ENABLE_DISABLE = 'core/general/enable'; // SectionName/GroupName/FieldNAme from system.xml

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) 
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function setStoreScope()
    {
        return ScopeInterface::SCOPE_STORE;
    }
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(static::MODULE_ENABLE_DISABLE, $this->setStoreScope());
    }
}