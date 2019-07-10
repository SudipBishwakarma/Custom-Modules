<?php

/**
 * Email Quote index controller
 *
 * @category    Pebble
 * @package     Pebble_EmailQuote
 */
class Pebble_EmailQuote_EnqueryController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){

		$productId = $this->getRequest()->getPost('productId');
		$name = $this->getRequest()->getPost('name');
		$email = $this->getRequest()->getPost('email');
		$phone = $this->getRequest()->getPost('phone');
		$comment = $this->getRequest()->getPost('comment');
		//echo $comments_option = key($this->getRequest()->getPost('comments_option'));
		$comments_ption_array = array(
		    0    => "Price request",
		    1  => "Purchase assistance",
		);
		$comments_option = $comments_ption_array[key($this->getRequest()->getPost('comments_option'))];
		/**Newsletter subscription in form */
		$subscribe_newsletter=$this->getRequest()->getPost('subscribe_newsletter');
        if(isset($subscribe_newsletter) && $subscribe_newsletter==1)
        {
            Mage::getModel('newsletter/subscriber')->setImportMode(true)->subscribe($email);

            # get just generated subscriber

            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
            # change status to "subscribed" and save
            $subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
            $subscriber->save();
        }
        /**Subscription End*/
		/*Load product detail*/
		$product = Mage::getModel("catalog/product")->load($productId);

		$children = Mage::getModel('catalog/category')->getCategories(73); //load all sub categories of montblanc.
		$children_ids = array();
		foreach ($children as $category) {
		    $children_ids[] = $category->getId();
		}
		$catIds = $product->getCategoryIds(); //load product category association ids
		$isMontblanc = false;
		foreach ($catIds as $value) {
			if ($value == '73' || in_array($value, $children_ids)) {//Check if product belongs to Montblanc category.
				$isMontblanc = true;
				$addToCartUrl = Mage::getUrl('checkout/cart/add', array('product'=>$product->getId(),'qty'=>1, 'form_key' => Mage::getSingleton('core/session')->getFormKey()));
			}
		}

		$productName = $product->getName();
		$regularprice   = round($product->getPrice(),2);
		//$saleprice      = round($product->getFinalPrice(),2);
		$saleprice = round($product->getData('special_price'),2);

		if($regularprice != $saleprice && $saleprice>0):
			$productPrice = 'Your special price for this pen is <b>$'.$saleprice.'</b> and the regular price is <b>$'.$regularprice.'</b>.';
		else:
			$productPrice = 'Your regular price for this pen is <b>$'.$regularprice.'</b>.';
		endif;
		$productUrl = $product->getProductUrl();
		$productCartUrl = Mage::helper('checkout/cart')->getAddUrl($product);
		$productImage = $product->getImageUrl();

		/*Load admin email */
		//$adminUserModel = Mage::getModel('admin/user');
		//$userCollection = $adminUserModel->getCollection()->load();
		//$admin_email = $userCollection->getData()[0]['email'];
		$admin_email = $email;

		/*email content*/
			$message = '
			<div>
				Dear '.$name.',<br><br>
				Thank you for viewing the '.$productName.'.  '.$productPrice.'<br><br>
				<a href="'.$productUrl.'" target="_blank">
					<img border="0" src="'.$productImage.'" class=""></a><br><br>
			';

		/*mail function parameter - Customer*/
		$to = $email;
		$subject = "Pen Boutique - ".$productName;
		$txt = $message;
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		$headers .= 'From: <' . $email . '>' . "\r\n";
		//$headers .= 'X-Mailer: PHP/' . phpversion();
		mail($to,$subject,$txt,$headers);

		$admin_msg = '<br/>
		Name : '.$name.'<br/>
		Email : '.$email.'<br/>
		Phone: '.$phone.'<br/><br/>

		Product Name ( Requested For Quote ) : '.$productName.'<br/>
		Product Price : '.$productPrice.'<br/>
		Enquired For : '.$comments_option.'<br/><br/>
		Customer Comment : '.$comment.'<br/>
		';
		//echo $admin_msg;
		/*mail function parameter - Admin*/
		$to = $admin_email;
		$subject = "Email Quote - ".$productName;
		$txt = $admin_msg;
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		$headers .= "Content-Transfer-Encoding: quoted-printable" . "\r\n";
		$headers .= 'From: <test@example.com>' . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		mail($to,$subject,$txt,$headers);
	}

	public function quickviewAction(){

		$product_id = Mage::app()->getRequest()->getParam('item_id');
		$_product = Mage::getModel('catalog/product')->load($product_id);
		$productUrl = Mage::getBaseUrl()."skin/frontend/pebble/default/"; ?>

		<div class="modal-body">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		        <div class="quickView-img">
		            <img src="<?php echo Mage::helper('catalog/image')->init($_product, 'small_image')->resize(384,384); ?>" alt="">
		        </div>
		    </div>

		    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		        <a href="<?php echo $_product->getProductUrl();?>" class="quick-head"><?php echo $_product->getName();?></a>
		    	<div class="sku">SKU: <?php echo $_product->getSku();?> </div>
		        <div class="border-line"></div>


		             <?php $RP = round($_product->getPrice(),2);?>
		             <?php $SP = round($_product->getFinalPrice(),2);?>
		                <?php if($RP == $SP):?>
		                <div class="sales-price">
		                <p>Sale Price:</p>
		                    <div class="s-price">
		                        $<?php echo $RP;?>
		                    </div>
		                </div>
		                <?php else: ?>
		                	<div class="clearfix"></div>
		                    <div class="regular-price">
		                        Regular Price: <span>$<?php echo $RP; ?></span>
		                    </div>
		                    <div class="sales-price">
		                        <p>Sale Price:</p>
		                        <div class="s-price">$<?php echo $SP; ?></div>
		                    </div>
		                <?php endif ?>

		            <div class="comn-qty">
		                <span class="c-title">Quantity</span>
		                <select name="qty" id="test-id2" class="quick-sel">
		                <option value="1" data-hidden="true">1</option>
		                <option value="2">2</option>
		                <option value="3">3</option>
		                <option value="4">4</option>
		                <option value="5">5</option>
		                </select>
		            </div>
		            <p class="available">
		                <?php if(strcmp($_product->getIsInStock(),1) == 0){ ?>
		                <span class="gColor"><i class="fa fa-check-square"></i> Availability</span>
		                <?php } else{ ?>
		                <span class="rColor"><i class="fa fa-times-circle"></i> Discontinued</span>
		                <?php } ?>
		            </p>

		            <div class="buy-quick">
		             <a href="#<?php //echo Mage::helper('checkout/cart')->getAddUrl($_product);?>" id="save-event" class="pd-cart-box" rel="<?php echo $_product->getId();?>">Buy Now</a>
		                <script type="text/javascript">
		                $j(document).ready(function(){

		                    $j('#save-event').on('click',function(event){
		                        event.preventDefault();
		                        var qty = $j('select[name=qty]').val()
		                        var url = '<?php echo Mage::helper('checkout/cart')->getAddUrl($_product);?>';
		                        url = url+'qty/'+qty;
		                        window.location.replace(url);
		                        $j('#quickView').modal('hide');
		                    });
		                });
		                </script>
		            </div>
		            <p class="available">Usually ships within 6-10 days. Item needs extra processing time.</p>
		            <div class="r-price">
		                <a href="#"><img src="<?php echo $productUrl;?>images/free-ships.png"></a>
		                <a href="#"><img src="<?php echo $productUrl;?>images/grauntee4.jpg"></a>
		            </div>
		        <!-- </div> -->
		    </div>

		    <div class="col-xs-12 col-sm-12 col-md-11 col-lg-11 pro-des">
		        <?php echo $_product->getShortDescription(); ?>
		    </div><!--pro-des -->
		</div><!--modal-body -->
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div><!--modal-footer -->
		<?php
	}

}
