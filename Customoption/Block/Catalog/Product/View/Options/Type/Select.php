<?php
class Pebble_Customoption_Block_Catalog_Product_View_Options_Type_Select
  extends Mage_Catalog_Block_Product_View_Options_Type_Select
{

   /**
     * Return html for control element
     *
     * @return string
     */
    public function getValuesHtml()
    {
        $_option = $this->getOption();
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
        $store = $this->getProduct()->getStore();

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN
            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
            $require = ($_option->getIsRequire()) ? ' required-entry' : '';
            $extraParams = '';
            $select = $this->getLayout()->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'select_'.$_option->getId(),
                    'class' => $require.' product-custom-option'
                ));
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
                $select->setName('options['.$_option->getid().']')
                    ->addOption('', $this->__('-- Please Select --'));
            } else {
                $select->setName('options['.$_option->getid().'][]');
                $select->setClass('multiselect'.$require.' product-custom-option');
            }
            foreach ($_option->getValues() as $_value) {
                $priceStr = $this->_formatPrice(array(
                    'is_percent'    => ($_value->getPriceType() == 'percent'),
                    'pricing_value' => $_value->getPrice(($_value->getPriceType() == 'percent'))
                ), false);
                $select->addOption(
                    $_value->getOptionTypeId(),
                    $_value->getTitle() . ' ' . $priceStr . '',
                    array('price' => $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false))
                );
            }
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                $extraParams = ' multiple="multiple"';
            }
            if (!$this->getSkipJsReloadPrice()) {
                $extraParams .= ' onchange="opConfig.reloadPrice()"';
            }
            $select->setExtraParams($extraParams);

            if ($configValue) {
                $select->setValue($configValue);
            }

            return $select->getHtml();
        }

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
            ) {
            $selectHtml = '<ul id="options-'.$_option->getId().'-list" class="engrave-img options-list">';
            $require = ($_option->getIsRequire()) ? ' validate-one-required-by-name' : '';
            $arraySign = '';
            switch ($_option->getType()) {
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio';
                    /*if (!$_option->getIsRequire()) {
                        $selectHtml .= '<li><input type="radio" id="options_' . $_option->getId() . '" class="'
                            . $class . ' product-custom-option" name="options[' . $_option->getId() . ']"'
                            . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"')
                            . ' value="" checked="checked" /><span class="label"><label for="options_'
                            . $_option->getId() . '">' . $this->__('None') . '</label></span></li>';
                    }*/
                    break;
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox';
                    $arraySign = '[]';
                    break;
            }
            $count = 1; $i = 1;
            foreach ($_option->getValues() as $_value) {
                $count++;

                $priceStr = $this->_formatPrice(array(
                    'is_percent'    => ($_value->getPriceType() == 'percent'),
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent')
                ));

                $htmlValue = $_value->getOptionTypeId();
                if ($arraySign) {
                    $checked = (is_array($configValue) && in_array($htmlValue, $configValue)) ? 'checked' : '';
                } else {
                    $checked = $configValue == $htmlValue ? 'checked' : '';
                }

                if (strtolower($_value->getTitle()) == 'mypensystem') {
                    //$productId = Mage::getModel('catalog/product')->loadByAttribute('sku', $_value->getSku())->getId(); //get product id by its sku
                    $productId = Mage::getModel('catalog/product')->loadByAttribute('sku', 'MYPENSYSTEM')->getId(); //MyPenSystem SKU
                    $product = Mage::getModel('catalog/product')->load($productId);// load product
                    $productImageUrl = Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage()); //get image url
                    $productDescription = $product->getDescription(); // get product description.
                    $selectHtml .= '<li class="mypensystem">' . '<input id="radio'.$i.'" type="' . $type . '" class="' . $class . ' ' . $require
                        . ' product-custom-option"'
                        . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"')
                        . ' name="options[' . $_option->getId() . ']' . $arraySign . '" id="options_' . $_option->getId()
                        . '_' . $count . '" value="' . $htmlValue . '" ' . $checked . ' price="'
                        . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />'
                        . '<label for="radio'.$i.'"><strong>Personalize with MyPenSystem</strong></label>'
                        . '<a data-toggle="modal" data-target="#mypensystem" class="pensystem"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                           <div class="modal fade" id="mypensystem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                               <div class="modal-dialog">
                                   <div class="modal-content">
                                       <div class="modal-header">
                                           <button type="button" class="close" data-dismiss="modal">
                                           <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                                           </button>
                                           <h4 class="modal-title">'.$product->getName().'</h4>
                                       </div>
                                       <div class="modal-body">
                                           <div class="row">
                                                <div class="col-xs-12 col-sm-4">
                                                    <img src="'.$productImageUrl.'" class="img-responsive">
                                                </div>
                                                <div class="col-xs-12 col-sm-8">'.$productDescription.'</div>
                                           </div>
                                       </div>
                                       <div class="modal-footer">&nbsp;</div>
                                   </div>
                               </div>
                           </div>';
                }
                else {
                    $selectHtml .= '<li>' . '<input id="radio'.$i.'" type="' . $type . '" class="' . $class . ' ' . $require
                        . ' product-custom-option"'
                        . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"')
                        . ' name="options[' . $_option->getId() . ']' . $arraySign . '" id="options_' . $_option->getId()
                        . '_' . $count . '" value="' . $htmlValue . '" ' . $checked . ' price="'
                        . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />'
                        . '<label for="radio'.$i.'">'
                        . '<img src="'.$this->getSkinUrl().'images/'.strtolower($_value->getTitle()).'.png" alt="">'.'</label>'.$_value->getSku();
                }
                /*if ($_option->getIsRequire()) {
                    $selectHtml .= '<script type="text/javascript">' . '$(\'options_' . $_option->getId() . '_'
                    . $count . '\').advaiceContainer = \'options-' . $_option->getId() . '-container\';'
                    . '$(\'options_' . $_option->getId() . '_' . $count
                    . '\').callbackFunction = \'validateOptionsCallback\';' . '</script>';
                }*/
                $selectHtml .= '</li>';
                $i++;
            }
            $selectHtml .= '</ul>';

            return $selectHtml;
        }
    }

}
