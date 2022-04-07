<?php
namespace DoneLogic\Core\Block\Grid\Renderer;
/**
 * DoneLogic/Core/Block/Grid/Renderer/Name.php
 * @author John Wenzel johngwenzel@gmail.com
 * Truncates name to 20 chars + ... for column display in Grid
 */
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Name extends AbstractRenderer 
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $str = $row->getName();
        $truncated = substr($str, 0, 20);
        if(strlen($str) > strlen($truncated)) {
            $str = $truncated . "...";
        }
        return htmlentities($str, ENT_QUOTES);
    }
}