
<?php

class Pebble_InkComparision_IndexController extends Mage_Core_Controller_Front_Action
{



    public function update_productsAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $brand_code = 'manufacturer';
            $ink_brand = $this->getRequest()->getPost('ink_brand');
            $category_id = $this->getRequest()->getPost('category_id');
            $refill_category = Mage::getModel('catalog/category')->load($category_id);
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addCategoryFilter($refill_category)
                ->addAttributeToFilter('visibility', array('neq' => 1))
                ->addAttributeToSelect('*');

            if(isset($ink_brand) && $ink_brand>0)
            {
                $products->addAttributeToFilter( $brand_code, $ink_brand );
            }

            if(sizeof(Mage::getSingleton('core/session')->getCompareProducts()) > 0)
            {
                $products->addAttributeToFilter('entity_id', array('nin'=> Mage::getSingleton('core/session')->getCompareProducts()));
            }
            $products->setPageSize(40);

            $product_size = sizeof($products);


            if($product_size > 0)
            {
                foreach ($products as $_product)
                {
                    echo '<div class="col-md-3" id="list-product-id-'.$_product->getId().'">';
                        echo '<div class="fly-eff">';
                            echo '<div class="thumb-holder"><img src="'.Mage::helper('catalog/image')->init($_product, 'small_image')->resize(272,246).'" class="img-responsive" /></div>';
                            echo '<h2>'.$_product->getName().'</h2>';
                            echo '<button class="button my-btn" type="button" onclick="add_to_compare('.$_product->getId().');">Add to Compare</button>';
                        echo '</div>';
                    echo '</div>';
                }

            }
            else
            {
                echo '<div class="text-danger">No products for comparision...</div>';
            }

            //echo sizeof($products);
        }

    }

    public function add_to_compareAction()
    {
        $compared_products = Mage::getSingleton('core/session')->getCompareProducts();
        $pid = $this->getRequest()->getPost('pid');
        if($pid)
        {

            array_push($compared_products,$pid);
            $compared_products = array_unique($compared_products);
            Mage::getSingleton('core/session')->setCompareProducts($compared_products);
        }

        echo sizeof($compared_products);

        echo '@@@@@@@@@@';

        $product_block = new Mage_Catalog_Block_Product;

        $products = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter('entity_id', array('in'=> Mage::getSingleton('core/session')->getCompareProducts()))
            ->addAttributeToFilter('visibility', array('neq' => 1))
            ->addAttributeToSelect('*');

        if(sizeof($products) > 0)
        {
            echo '<div class="table-responsive">';
                echo '<table class="table table-striped" id="table-compare-product">';
                    echo '<thead class="thead-light">';
                        echo '<tr>';
                            echo '<th width="200">Ink Sampler</th>';
                            echo '<th width="200">Product Image</th>';
                            echo '<th>Product Name</th>';
                            echo '<th>&nbsp;</th>';
                        echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                        foreach ($products as $_product)
                        {
                            echo '<tr id="compare-product-id-'.$_product->getId().'">';
                                echo '<td width="200">';
                                    echo '<a href="'.Mage::helper('catalog/image')->init($_product, 'small_image').'" data-fancybox="product-'.$_product->getId().'"><center><img src="'.Mage::helper('catalog/image')->init($_product, 'small_image')->resize(200).'" width="200" alt="" /><BR><small>Click to Enlarge</SMALL></center></a>';
                                echo '</td>';
                                echo '<td width="200">';
                                    echo '<a href="'.Mage::helper('catalog/image')->init($_product, 'thumbnail').'" data-fancybox="product-'.$_product->getId().'"><center><img src="'.Mage::helper('catalog/image')->init($_product, 'thumbnail')->resize(200).'" width="200" alt="" /><BR><small>Click to Enlarge</SMALL></center></a></center>';
                                echo '</td>';
                                echo '<td>';
                                    echo '<h3><a href="'.$_product->getProductUrl().'" target="_blank">'.$_product->getName().'</a></h3>';
                                    echo '<span>';
                                        if($_product->getpriceHide() == true)
                                        {
                                            echo '<span class="price-prohibited">Our Price: Price display prohibited.</span>';
                                        }
                                        else
                                        {
                                            echo $product_block->getPriceHtml($_product, true);
                                        }

                                    echo '</span>';
                                echo '</td>';
                                echo '<td>';
                                    echo '<a href="'.$_product->getProductUrl().'" target="_blank" class="btn btn-info"><i class="fa fa-link"></i></a>';
                                    echo '<button type="button" class="btn btn-danger" onclick="remove_from_compare('.$_product->getId().');"><i class="fa fa-trash"></i></button>';
                                echo '</td>';
                            echo '</tr>';
                        }


                    echo '</tbody>';
                echo '</table>';
            echo '</div>';
        }
        else
        {
            echo '<div class="alert alert-warning">No products in the compare list.</div>';
        }

    }

    public function remove_from_compareAction()
    {
        $pid = $this->getRequest()->getPost('pid');
        $compared_products = Mage::getSingleton('core/session')->getCompareProducts();
        $compared_products = array_diff($compared_products, array($pid));
        Mage::getSingleton('core/session')->setCompareProducts($compared_products);
        //echo sizeof($compared_products);
    }
}
?>
