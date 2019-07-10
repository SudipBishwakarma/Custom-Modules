<?php

/**
 * Email Quote index controller
 *
 * @category    Pebble
 * @package     Pebble_ReferAFriend
 */
class Pebble_ReferAFriend_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$name = $this->getRequest()->getPost('name');
		$email = $this->getRequest()->getPost('email');
		$message = $this->getLayout()->createBlock('core/template')->setTemplate('ReferAFriend/referEmail.phtml')->toHtml();
		/*mail function parameter - Customer*/
		$to = $email;
		$subject = "Pen Boutique - You've got an invitation!";
		$txt = $message;
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		$headers .= 'From: <support@example.com>' . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		mail($to,$subject,$txt,$headers);
		Mage::getSingleton('core/session')->setSuccessMsg('Email successfully sent to '.$email);
		$this->_redirect('newsletter-success.html');
	}
}
