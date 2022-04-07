<?php
namespace DoneLogic\Core\Block\Grid\Renderer;
/**
 * DoneLogic/Core/Block/Grid/Renderer/Description.php
 * @author John Wenzel johngwenzel@gmail.com
 * Truncates description to 50 chars + ... for column display in Grid
 */
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Description extends AbstractRenderer 
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $str = $row->getDescription();
        $truncated = substr($str, 0, 50);
        if(strlen($str) > strlen($truncated)) {
            $str = $truncated . "...";
        }
        return htmlentities($str, ENT_QUOTES);
    }
}