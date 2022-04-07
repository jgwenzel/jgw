<?php
namespace DoneLogic\Gtag\Block;
/**
 * DoneLogic/Gtag/Block/GtagHead.php
 * @author John Wenzel johngwenzel@gmail.com
 * If module has been "enabled" in admin config menu, the gtag_head_snippet
 * is fetched from donelogic_core table via get() which is called
 * from gtag_head.phtml template.
 */
use Magento\Framework\View\Element\Template;
use DoneLogic\Gtag\Helper\Data;

class GtagHead extends Template
{
    private $recordModel;
    private $is_enabled;

    function __construct( Data $helper ) {
        if($this->is_enabled = $helper->isEnabled()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->recordModel = $objectManager->create('\DoneLogic\Core\Model\Records');
        }
    }

    public function get()
    {
        if(!$this->is_enabled) {
            $value = '<!-- donelogic_gtag module is disabled -->';
        }
        elseif(!$this->recordModel->load( 'gtag_head_snippet', 'name')) {
            $value = '<!-- gtag_head_snippet is not set -->';
        } 
        else {
            $value = $this->recordModel->getData('value');
        }
        return $value;
    }
}