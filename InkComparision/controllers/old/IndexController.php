



<?php

/**
 * Email Quote index controller
 *
 * @category    Pebble
 * @package     Pebble_ReferAFriend
 */


class Pebble_InkComparision_IndexController extends Mage_Core_Controller_Front_Action
{

    /*public function indexAction(){
        //$id = $this->getRequest()->getPost('id');
        $this->loadLayout();
        $this->renderLayout();

    }*/

    public function update_productsAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $brand_code = 'manufacturer';
            $color_code = 'color';
            $ink_brand = $this->getRequest()->getPost('ink_brand');
            $ink_color = $this->getRequest()->getPost('ink_color');
            $ink_attribute_code = $this->getRequest()->getPost('ink_attribute_code');
            //$list_salable_only = $this->getRequest()->getPost('list_salable_only');
            $category_id = $this->getRequest()->getPost('category_id');
            $refill_category = Mage::getModel('catalog/category')->load($category_id);
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addCategoryFilter($refill_category)
                ->addAttributeToSelect('name');



            $select_text = 'Select';
            if(isset($ink_brand) && $ink_brand>0)
            {
                $product_model = Mage::getModel('catalog/product');
                $attr = $product_model->getResource()->getAttribute($brand_code);
                if ($attr->usesSource())
                {
                    $select_text.=' '.$attr->getSource()->getOptionText($ink_brand);
                }


                $products->addAttributeToFilter( $brand_code, $ink_brand );
            }
            if(isset($ink_color) && $ink_color>0)
            {
                $select_text.=',';
                $product_model = Mage::getModel('catalog/product');
                $attr = $product_model->getResource()->getAttribute($color_code);
                if ($attr->usesSource())
                {
                    $select_text.=' '.$attr->getSource()->getOptionText($ink_color);
                }

                $products->addAttributeToFilter( $color_code, $ink_color );
            }

            if(isset($ink_attribute_code) && $ink_attribute_code!='')
            {
                $select_text.=',';
                $select_text.=' '.ucwords(str_replace('_',' ',$ink_attribute_code));

                $products->addFieldToFilter(array(
                    array('attribute'=>$ink_attribute_code, 'eq'=>1),
                ));
            }

            $product_size = sizeof($products);


            if($product_size > 0)
            {
                echo '<div class="select-box2">';
                echo '<div class="col-md-10 col-md-offset-1">';
                echo '<div class="row">';
                $cp = 1;
                for($i=1; $i<=4; $i++)
                {
                    if ($cp <= $product_size)
                    {
                        echo '<div class="col-md-3">';
                        echo '<select name="ink_product_'.$i.'" id="ink_product_'.$i.'" onchange="add_to_compare(this.value);" class="custom-select">';
                        echo '<option value=""> - - - '.$select_text.' Ink '.$cp.' - - - </option>';

                        foreach($products as $p)
                        {
                            echo '<option value="'.$p->getId().'">'.$p->getName().'</option>';
                        }
                        $cp++;
                        echo '</select >';

                        echo '</div>';
                    }
                }

                echo '</div>';
                echo '</div>';
                echo '</div>';

            }
            else
            {
                echo '<div class="text-danger">Products not available...</div>';
            }

            //echo sizeof($products);
        }

    }


    function display_productsAction()
    {

        $product_ids = $this->getRequest()->getPost('product_ids');
        /*foreach($product_ids as $pid)
        {
            echo $pid.', ';
        }*/
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('entity_id', array('in' => $product_ids))
            ->addAttributeToSelect('*');



        /*		foreach($products as $_product)
                {

                    echo $_product->getEntityId();
                    echo ', ';
                    echo $_product->getName();
                    echo ', ';
                    echo $_product->getSku();
                    echo ', ';
                    //echo '<img src="'.Mage::helper('catalog/image')->init($_product, 'small_image')->resize(272,246).'" width="272" height="246" class="blur" />';
                    echo '<hr>';
                }*/

        $product_block = new Mage_Catalog_Block_Product;
        $product_size = sizeof($products);


        if($product_size>=1) {
            $base_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $compare_url=$base_url.'/ink-comparision-tool?cpids='.implode('-',$product_ids);
            $subject = str_replace(' ', '%20','Penboutique.com - Ink Comparison Tool');
            $message = 'Hi,%0D%0A%0D%0A';
            $message.='Please have a check ink comparision tool. %0D%0A';
            $message.= $compare_url.'%0D%0A%0D%0A%0D%0A';
            $message.='Thank You %0D%0A%0D%0A';
            //$subject = 'Penboutique.com - Ink Comparison Tool';
            //$message = $compare_url;
            echo '<div class="col-md-10 col-md-offset-1">
                <div class="row">
                  <div class="check-box">
                    <!--<a href="#" class="check-color1" data-toggle="modal" data-target="#myModalEmailtoFriend"><i class="fa fa-envelope-o" aria-hidden="true"></i> Email to Friend</a>-->

                    <a href="mailto:?subject='.$subject.'&body='.$message.'" class="check-color1"><i class="fa fa-envelope-o" aria-hidden="true"></i> Email to Friend</a>
                    <a href="javascript:void(0);" onclick="clear_choices();" class="check-color2"><i class="fa fa-times" aria-hidden="true"></i> Clear All Choices</a>
                    <br><br><br>
                  </div>
                </div>
              </div>';


            echo '<div class="comparison-table table-responsive">';
            echo '<table class="table table-striped">';
            echo '<tbody>';
            echo '<tr>';
            echo '<th><strong>&nbsp;</strong></th>';

            foreach ($products as $_product) {
                $value = '<img src="' . Mage::helper('catalog/image')->init($_product, 'small_image')->resize(272, 246) . '" width="272" height="246" class="img-responsive" />';

                echo '<td class="block-center"><a href="' . $_product->getProductUrl() . '" target="_blank">' . $value . '</a>';

                if ($_product->getpriceHide() != true && $_product->getData('msrp_enabled') != 1 && !$_product->isConfigurable()) {
                    $product_url = Mage::helper('checkout/cart')->getAddUrl($_product);
                } else {
                    $product_url = $_product->getProductUrl();
                }

                echo '<a class="btn btn-success btn-sm"  href="' . $product_url . '" target="_blank">';
                echo '<i class="fa fa-shopping-cart addcart"></i> Add to Cart';
                echo '</a>';

                echo '</td>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th><strong>Product</strong></th>';

            foreach ($products as $_product) {

                $value = $_product->getName();
                echo '<td><strong><a href="' . $_product->getProductUrl() . '" target="_blank">' . $value . '</a></strong></td>';
            }

            echo '</tr>';


            echo '<tr>';
            echo '<th><strong>SKU</strong></th>';

            foreach ($products as $_product) {
                if (!$_product->getSku()) $value = ' - ';
                else $value = $_product->getSku();
                echo '<td>' . $value . '</td>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th><strong>Price</strong></th>';
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                if ($_product->getpriceHide() == true || ($_product->isConfigurable() == true)) $value = ' - ';
                else $value = $product_block->getPriceHtml($_product, true);

                echo '<td>' . $value . '</td>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th><strong>Brand</strong></th>';
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                if (!$_product->getData("manufacturer")) $value = ' - ';
                else $value = $_product->getAttributeText("manufacturer");
                echo '<td>' . $value . '</td>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th><strong>Type</strong></th>';
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                if (!$_product->getData("ptype")) $value = ' - ';
                else $value = $_product->getAttributeText("ptype");
                echo '<td>' . $value . '</td>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th><strong>Size</strong></th>';
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                if (!$_product->getData("size")) $value = ' - ';
                else $value = $_product->getAttributeText("size");
                echo '<td>' . $value . '</td>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th><strong>Compatibility</strong></th>';
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                if (!$_product->getData("compability")) $value = ' - ';
                else $value = $_product->getResource()->getAttribute('compability')->getFrontend()->getValue($_product);
                echo '<td>' . $value . '</td>';
            }

            echo '</tr>';


            echo '<tr>';
            echo '<th><strong>Complimentary</strong></th>';
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                if (!$_product->getData("complimentary")) $value = ' - ';
                else $value = $_product->getAttributeText("complimentary");
                echo '<td>' . $value . '</td>';
            }

            echo '</tr>';


            echo '<tr>';
            echo '<th><strong>Fast Drying</strong></th>';
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                if ($_product->getData("fast_drying") == null) $value = " - ";
                elseif ($_product->getData("fast_drying") == 1) $value = 'Yes';
                elseif ($_product->getData("fast_drying") == 0) $value = "No";
                echo '<td>' . $value . '</td>';
            }

            echo '</tr>';


            echo '<tr>';
            echo '<th><strong>Water Resistant</strong></th>';
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());

                if ($_product->getData("water_resistant") == null) $value = " - ";
                elseif ($_product->getData("water_resistant") == 1) $value = 'Yes';
                elseif ($_product->getData("water_resistant") == 0) $value = "No";
                echo '<td>' . $value . '</td>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th>&nbsp;</th>';
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());

                echo '<td>';
                if ($_product->getpriceHide() != true && $_product->getData('msrp_enabled') != 1 && !$_product->isConfigurable()) {
                    $product_url = Mage::helper('checkout/cart')->getAddUrl($_product);
                } else {
                    $product_url = $_product->getProductUrl();
                }

                echo '<a class="btn btn-success btn-sm"  href="' . $product_url . '" target="_blank">';
                echo '<i class="fa fa-shopping-cart addcart"></i> Add to Cart';
                echo '</a>';
                echo '</td>';


            }

            echo '</tr>';


            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }




        /*
                foreach($products as $_product)
                {

                    $p = Mage::getModel('catalog/product')->load($_product->getEntityId());
                    echo '<strong>Type :</strong>'.$p->getAttributeText("ptype");

                    echo '<strong>Water Resistant :</strong>'.$p->getAttributeText("water_resistant");
                    echo '<strong>Fast Drying :</strong>'.$p->getAttributeText("fast_drying");

                    echo '<strong>Size :</strong>'.$p->getAttributeText("size");
                    if(!$p->getData("wwwwww"))$wwww='N/A'; else $wwww='Yes';
                    echo '<strong>Warranty :</strong>'.$wwww;

                    if(!$p->getData("compability"))$compatibility='N/A'; else $compatibility=$p->getResource()->getAttribute('compability')->getFrontend()->getValue($p);
                    echo '<strong>Compatibiliy :</strong>'.$compatibility;


                    echo '<hr>';

                }*/



    }



    public function update_refillsAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $refill_brand = $this->getRequest()->getPost('refill_brand');
            $refill_sub_brand = $this->getRequest()->getPost('refill_sub_brand');
            $refill_type = $this->getRequest()->getPost('refill_type');
            $refill_size = $this->getRequest()->getPost('refill_size');

            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*');

            if(isset($refill_brand) && $refill_brand>0)
            {
                //$products->addAttributeToFilter( 'manufacturer', $refill_brand );
                $products->addFieldToFilter(array(
                    array('attribute'=>'manufacturer', 'eq'=>$refill_brand),
                ));
            }

            if(isset($refill_sub_brand) && $refill_sub_brand>0)
            {
                //$products->addAttributeToFilter( 'manufacturer', $refill_brand );
                $products->addFieldToFilter(array(
                    array('attribute'=>'series', 'eq'=>$refill_sub_brand),
                ));
            }

            if(isset($refill_type) && $refill_type>0)
            {

                $products->addFieldToFilter(array(
                    array('attribute'=>'ptype', 'eq'=>$refill_type),
                ));
            }

            if(isset($refill_size) && $refill_size>0)
            {
                $products->addFieldToFilter(array(
                    array('attribute'=>'size', 'eq'=>$refill_size),
                ));
            }

            $products->setPageSize(40);




            $product_size = sizeof($products);

            if($refill_brand > 0 && $refill_sub_brand > 0 && ($refill_type > 0 || $refill_size >0))
            {

                if($product_size > 0)
                {
                    echo '<div class="row refill-guide">';
                    $i=0;
                    foreach ($products as $_product) {
                        if($i%4==0)echo '<div class="clearfix"></div>';
                        echo '<div class="col-md-3">';
                        echo '<div class="refill-products">';
                        $image = '<img src="' . Mage::helper('catalog/image')->init($_product, 'small_image')->resize(272, 246) . '" width="272" height="246" class="img-responsive images" />';
                        echo '<a href="' . $_product->getProductUrl() . '" target="_blank">'.$image.'</a>';
                        echo '<div class="middles">';
                        echo '<div class="texts"><a href="' . $_product->getProductUrl() . '" target="_blank"><i class="fa fa-link" aria-hidden="true"></i></a></div>';
                        echo '</div>';
                        echo '<span><a href="' . $_product->getProductUrl() . '" target="_blank">'.$_product->getName().'</a></span>';

                        echo '</div>';
                        echo '</div>';
                        $i++;
                    }
                    echo '</div>';

                }
                else
                {
                    echo '<div class="text-danger">Products not available...</div>';
                }

            }


            //echo sizeof($products);
        }

    }



    public function update_refills_sub_filters_sectionAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $refill_brand = $this->getRequest()->getPost('refill_brand');
            $refill_sub_brand = $this->getRequest()->getPost('refill_sub_brand');
            if($refill_brand<1 || $refill_sub_brand<1)
            {
                echo '<span id="sub_filters_section">
                  <div class="col-md-4 andor2">
                    <span>Filter By Type</span>
                    <select name="refill_type" id="refill_type" class="custom-select"  disabled="disabled">
                      <option value=""> - - - Select Sub Brand First - - - </option>

                    </select>
                  </div>

                  <div class="col-md-4">
                    <span>Filter By Size</span>
                    <select name="refill_size" id="refill_size" class="custom-select" disabled="disabled">
                      <option value=""> - - - Select Sub Brand First - - - </option>
                    </select>
                  </div>
                </span>';
                return;
            }

            $products = Mage::getResourceModel('catalog/product_collection')

                ->addAttributeToFilter('attribute_set_id',11)
                ->addAttributeToFilter('ptype', array('notnull' => true))
                ->addAttributeToFilter('ptype', array('neq' => ''))
                ->addAttributeToSelect('ptype')
                ->addFieldToFilter(array(array('attribute'=>'manufacturer', 'eq'=>$refill_brand)))
                ->addFieldToFilter(array(array('attribute'=>'series', 'eq'=>$refill_sub_brand)));

            //echo sizeof($products);
            $type_array= array();
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                array_push($type_array, $type_array[$_product->getData("ptype")]=$_product->getAttributeText("ptype"));
            }
            $type_array = array_unique($type_array);

            asort($type_array);

            echo '<div class="col-md-4 andor2">';
                echo '<span>Filter By Type</span>';
                echo '<select name="refill_type" id="refill_type" class="custom-select" onchange="filter_products_by_type();">';
                  echo '<option value=""> - - - Select Type - - - </option>';
                    foreach($type_array as $key=>$value)
                    {
                        echo '<option value="'.$key.'">'.$value.'</option>';
                    }
                echo '</select>';
            echo '</div>';


            $products = Mage::getResourceModel('catalog/product_collection')

                ->addAttributeToFilter('attribute_set_id',11)
                ->addAttributeToFilter('size', array('notnull' => true))
                ->addAttributeToFilter('size', array('neq' => ''))
                ->addAttributeToSelect('size')
                ->addAttributeToSelect('size')
                ->addFieldToFilter(array(array('attribute'=>'manufacturer', 'eq'=>$refill_brand)))
                ->addFieldToFilter(array(array('attribute'=>'series', 'eq'=>$refill_sub_brand)));

            $size_array= array();
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                array_push($size_array, $size_array[$_product->getData("size")]=$_product->getAttributeText("size"));
            }
            $size_array = array_unique($size_array);

            asort($size_array);

            echo '<div class="col-md-4">';
            echo '<span>Filter By Size</span>';
            echo '<select name="refill_size" id="refill_size" class="custom-select" onchange="filter_products_by_size();">';
            echo '<option value=""> - - - Select Size - - - </option>';
            foreach($size_array as $key=>$value)
            {
                echo '<option value="'.$key.'">'.$value.'</option>';
            }
            echo '</select>';
            echo '</div>';




            //echo sizeof($products);
        }

    }




    public function update_refill_sub_brands_sectionAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $refill_brand = $this->getRequest()->getPost('refill_brand');
            if($refill_brand<1)
            {
                echo '<span id="sub_brands_section">
              <div class="col-md-4 andor1">
                <span>Filter By Sub Brand</span>
                <select name="refill_sub_brand" id="refill_sub_brand" class="custom-select" disabled="disabled">
                  <option value=""> - - - Select Brand First - - - </option>

                </select>
              </div>
            </span>';
                return;
            }

            $products = Mage::getResourceModel('catalog/product_collection')

                ->addAttributeToFilter('attribute_set_id',11)
                ->addAttributeToFilter('series', array('notnull' => true))
                ->addAttributeToFilter('series', array('neq' => ''))
                ->addAttributeToSelect('series')
                ->addFieldToFilter(array(array('attribute'=>'manufacturer', 'eq'=>$refill_brand)));

            //echo sizeof($products);
            $sub_brand_array= array();
            foreach ($products as $_product) {
                $_product = Mage::getModel('catalog/product')->load($_product->getEntityId());
                array_push($sub_brand_array, $sub_brand_array[$_product->getData("series")]=$_product->getAttributeText("series"));
            }
            $sub_brand_array = array_unique($sub_brand_array);

            asort($sub_brand_array);

            echo '<div class="col-md-4 andor1">';
            echo '<span>Filter By Sub Brand</span>';
            echo '<select name="refill_sub_brand" id="refill_sub_brand" class="custom-select" onchange="update_sub_filters_section();">';
            echo '<option value=""> - - - Select Sub Brand - - - </option>';
            foreach($sub_brand_array as $key=>$value)
            {
                echo '<option value="'.$key.'">'.$value.'</option>';
            }
            echo '</select>';
            echo '</div>';

            //echo sizeof($products);
        }

    }














}
?>
