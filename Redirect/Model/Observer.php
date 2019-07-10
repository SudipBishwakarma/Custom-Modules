<?php
class Pebble_Redirect_Model_Observer
{
    public function redirectToConfigurable($observer)
    {
        $id = Mage::app()->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($id);
        if ($product->getData('visibility') == 1) {
            $configurableParentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($id);
            if (isset($configurableParentIds[0])) {
                $mainProduct = Mage::getModel('catalog/product')->load($configurableParentIds[0]);
                $configurableProductAttributes = $mainProduct->getTypeInstance()->getConfigurableAttributesAsArray($mainProduct);
                foreach ($configurableProductAttributes as $productAttribute)
                {
                    $configurableProductSuperAttributeId = '#'.$productAttribute['attribute_id'];
                    $simpleProductAttributeCode = $product->getData($productAttribute['attribute_code']);
                }
                if ($configurableProductSuperAttributeId != '#' && isset($simpleProductAttributeCode)):
                    Mage::app()->getResponse()->setRedirect($mainProduct->getProductUrl().$configurableProductSuperAttributeId.'='.$simpleProductAttributeCode, 301);
                else:
                    Mage::app()->getResponse()->setRedirect($mainProduct->getProductUrl(),301);
                endif;
                Mage::app()->getResponse()->sendResponse();
                exit;
            }
        }
        return $this;
    }
}
