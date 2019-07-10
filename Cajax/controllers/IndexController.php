<?php

/**
 * Email Quote index controller
 *
 * @category    Pebble
 * @package     Pebble_ReferAFriend
 */

require_once('iContactApi.php');
class Pebble_Cajax_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		//$id = $this->getRequest()->getPost('id');
		echo 'test index' ;

	}

	public function generate_coupon_codeAction(){
		if($this->getRequest()->isXmlHttpRequest()) {
			$email = $this->getRequest()->getPost('email');
			$picked_value = $this->getRequest()->getPost('picked_value');
			if ($picked_value == 1) //$5 off
			{
				$coupon_code = 'WOF$5OFF';
				$discount = '$5 OFF';
			}
			else if ($picked_value == 3) //5% OFF
			{
				$coupon_code = 'WOF5%OFF';
				$discount = '5% OFF';
			}

			else if ($picked_value == 5) //5% OFF
			{
				$coupon_code = 'FREEPJP';
				$discount = '';
			}

			if($picked_value == 1 || $picked_value ==3)
			{
				$main_content ='
				<p style="display:inline-block; font-size:13px; font-family:Helvetica,Arial,sans-serif; color:#636363; padding-top:10px; padding-bottom:5px; font-weight:500; line-height:22px;">As part of <strong>"Penboutique -  Spin to Win"</strong> program you\'ve won <strong style="font-weight:700; display:inline-block;">"'.$discount.'"</strong> on any purchases in PenBoutique.com.</p>

                            <p style="display:inline-block; font-size:13px; font-family:Helvetica,Arial,sans-serif; color:#636363; padding-top:10px; padding-bottom:5px; font-weight:500; line-height:22px;">Your coupon code is <strong style="font-weight:700; display:inline-block;">"'.$coupon_code.'"</strong>.</p>
				';
			}
			else if ($picked_value == 5)
			{
				$main_content ='
				<p style="display:inline-block; font-size:13px; font-family:Helvetica,Arial,sans-serif; color:#636363; padding-top:10px; padding-bottom:5px; font-weight:500; line-height:22px;">As part of <strong>"Penboutique -  Spin to Win"</strong> program you\'ve won a "FREE JOTTER PEN" on any purchases greater than $25 in PenBoutique.com.
<br>Make sure you add the product in your cart and your product quantity is greater than one.</p>

                            <p style="display:inline-block; font-size:13px; font-family:Helvetica,Arial,sans-serif; color:#636363; padding-top:10px; padding-bottom:5px; font-weight:500; line-height:22px;">Your coupon code is <strong style="font-weight:700; display:inline-block;">"'.$coupon_code.'"</strong>.</p>
				';
			}


			//$body = "Hi there, your coupon code is : ".$coupon_code;

			$body ='

				<html style="">
  <head>
    <title></title>
  </head>
  <body style="background: rgb(251, 251, 251); font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0px; padding: 0px; height: auto; min-height: auto;">
    <p>
      <style type="text/css">*{ margin:0 auto; padding:0;}
      </style>
    </p>

    <div style="background:#fbfbfb; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
      <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tbody>
          <tr>
            <td align="center" style="padding:20px 0 20px 0" valign="top">
              <table border="0" cellpadding="0" cellspacing="0" width="700">
                <tbody>
                  <tr>
                    <td style="font-size:11px;" width="441"></td>
                    <td align="right" width="212"><a href="http://www.penboutique.com/" style="color: rgb(102, 90, 70); font-size: 11px;">www.penboutique.com</a></td>
                  </tr>
                </tbody>
              </table>

              <table bgcolor="FFFFFF" border="0" cellpadding="0" cellspacing="0" style="" width="700">
                <tbody>
                  <tr>
                    <td colspan="6" valign="top">
                      <table bgcolor="FFFFFF" border="0" cellpadding="0" cellspacing="0" style="" width="700">
                        <tbody>
                          <tr>
                            <td width="265"><a href="http://www.penboutique.com/" style="text-align:center;"><img alt="Penboutique" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/pb-logo-12yrs.jpg" /></a></td>
                            <td width="265">

                            </td>
                            <td style="text-align:center; font-size:14px" width="170">Call Us:<br />
                              1(800) 263 2736</td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr style="font-size:14px;">
                    <td bgcolor="#d02f6a" style="text-align:center; padding:20px 0;" valign="top" width="89"><a href="http://www.penboutique.com/fine-pens.html" style="color:#fff; text-decoration:none;"><img height="28" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/icon-img1-03.png" width="32" /><br />
                      Fine Pens</a></td>
                    <td bgcolor="#fa3070" style="text-align:center; padding:20px 0;" valign="top" width="112"><a href="http://www.penboutique.com/pen-refills-inks.html" style="color:#fff; text-decoration:none;"><img alt="" height="28" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/icon-img2-03.png" width="32" /><br />
                      Refills &amp; Inks</a></td>
                    <td bgcolor="#fc7d21" style="text-align:center; padding:20px 0;" valign="top" width="124"><a href="http://www.penboutique.com/sales-special.html" style="color:#fff; text-decoration:none;"><img alt="" height="28" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/icon-img3-03.png" width="32" /><br />
                      Sales &amp; Saving</a></td>
                    <td bgcolor="#ffaf37" style="text-align:center; padding:20px 0;" valign="top" width="112"><a href="http://www.penboutique.com/stationery.html" style="color:#fff; text-decoration:none;"><img alt="" height="28" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/icon-img4-03.png" width="32" /><br />
                      Stationery</a></td>
                    <td bgcolor="#3fced4" style="text-align:center; padding:20px 0;" valign="top" width="134"><a href="http://www.penboutique.com/corporate-gifts" style="color:#fff; text-decoration:none;"><img alt="" height="28" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/icon-img5-03.png" width="32" /><br />
                      Corporate Gifts</a></td>
                    <td bgcolor="#4069b3" style="text-align:center; padding:20px 0;" valign="top" width="129"><a href="http://www.penboutique.com/limited-edition.html" style="color:#fff; text-decoration:none;"><img alt="" height="28" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/icon-img6-03.png" width="32" /><br />
                      Limited Edition</a></td>
                  </tr>
                  <tr>
                    <td colspan="6" valign="top"> </td>
                  </tr>
                  <tr>
                    <td colspan="6" valign="top">
                      <table bgcolor="FFFFFF" border="0" cellpadding="0" cellspacing="0" style="" width="700">
                        <tbody>
                          <tr>
                            <td colspan="2" valign="top"> </td>
                            <td width="10" valign="top"> </td>
                          </tr>
                          <tr>
                          	<td width="324" valign="top">
                            	<img src="http://www.penboutique.com/skin/frontend/pebble/default/images/spinner/wheel_of_fortune.jpg" alt="" width="324" height="358">
                            </td>
                          	<td width="355" valign="top" style="padding-right:0px;">
                            <h1 style="display:block; color:#383838; font-size:18px; font-family:Helvetica,Arial,sans-serif; text-transform:uppercase; padding-top:20px; padding-bottom:10px; letter-spacing:1;">Congratulations!!!</h1>

                            '.$main_content .'


                            </td>
                            <td valign="top" style="display:block;">

                            </td>
                            <td width="11" valign="top" style="display:block;">

                            </td>
                          </tr>
                          <tr>
                            <td colspan="2" valign="top"> </td>
                            <td valign="top"> </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6" valign="top"> </td>
                  </tr>

                  <tr>
                    <td colspan="6" valign="top"> </td>
                  </tr>

                  <tr>
                    <td colspan="6" valign="top"> </td>
                  </tr>
                </tbody>
              </table>
              <table bgcolor="f1f1f1" border="0" cellpadding="0" cellspacing="0" style="" width="700">
                <tbody>
                  <tr>
                    <td style="padding:10px;" valign="top" width="188"><a href="http://www.penboutique.com/"><img src="http://www.penboutique.com/skin/frontend/pebble/default/images/logos.gif" width="170" /></a></td>
                    <td style="padding:10px; font-size:12px;" valign="top" width="204">Stay Connected<br />
                      <br />
                      <a href="http://www.facebook.com/pensboutique"><img height="25" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/fb-03.png" width="25" /></a> <a href="http://twitter.com/penboutique"><img height="25" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/tweet-03.png" width="25" /></a> <a href="https://plus.google.com/+PenboutiquePlus"><img height="25" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/google-plus-03.png" width="25" /></a> <a href="http://www.pinterest.com/penboutique/"><img height="25" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/pinterest-03.png" width="25" /></a> <a href="http://www.youtube.com/user/penboutique"><img height="25" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/youtube.png" width="25" /></a> <a href="http://blog.penboutique.com"><img height="25" src="http://www.penboutique.com/media/wysiwyg/dod-emailer/deal-of-day/blog.png" width="25" /></a></td>
                    <td style="font-size:12px; color:#000; font-family:Arial, Helvetica, sans-serif; padding-right:10px;" width="248">To Order Via Phone: 1(800) 263 2736<br />
                      Fax order Form at 1.410.992.3274<br />
                      Order via email at <a href="mailto:support@penboutique.com" style="color: rgb(121, 36, 2); text-decoration: underline;">support@penboutique.com</a></td>
                  </tr>
                  <tr>
                    <td colspan="3" valign="top" style="padding:0px;">
                    <p style="display:inline-block; font-size:11px; font-family:Helvetica,Arial,sans-serif; color:#636363; padding-top:10px; padding-bottom:5px; padding-left:10px; font-weight:500; line-height:18px;"><strong style="font-weight:700; display:inline-block;">Disclaimer:</strong> Penboutique reserves right to cancel the coupon code at any time without prier notice to the customers. This coupon code only applies to logged in users and is for one time use only.</p>
                    </td>
                  </tr>
                </tbody>
              </table>

              <table bgcolor="e6e6e6" border="0" cellpadding="0" cellspacing="0" style="" width="700">
                <tbody>
                  <tr>
                    <td style="padding:10px; text-align:center; font-size:11px;" valign="top">
                      <p style="width:90%;">&copy; 2016-2017 Pen Boutique Ltd, 5560 Sterrett Place, Suite 115, Columbia, MD 21044.</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <p> </p>
  </body>
</html>
			';

			$mail = Mage::getModel('core/email');
			//$mail->setToName('John Customer');
			$mail->setToEmail($email);
			$mail->setBody($body);
			$mail->setSubject('Penboutique : Spin To Win');
			$mail->setFromEmail('info@penboutique.com');
			$mail->setFromName("Pen Boutique Ltd");
			$mail->setType('html');// You can use 'html' or 'text'
			if($mail->send())echo 'success'; else echo 'failed';

		}
		else
		{
			echo 'noooooo';
		}

	}

	public function subscribe_email_icontactAction(){


		iContactApi::getInstance()->setConfig(array(
				'appId'       => $appId,
				'apiPassword' => $password,
				'apiUsername' => $username
		));

		// Store the singleton
		$oiContact = iContactApi::getInstance();

		// Try to make the call(s)
		try {
			//  are examples on how to call the  iContact PHP API class
			// Grab all contacts
			//var_dump($oiContact->getContacts());


			// Grab a contact
			//var_dump($oiContact->getContact(27290937));


			$email = $this->getRequest()->getPost('email');
			$city = $this->getRequest()->getPost('city');
			$state = $this->getRequest()->getPost('state');

			if($oiContact->searchEmail($email)->total==0):
				$contact_data=$oiContact->addContact($email, null, null, null, null, null, null, null, $city, $state, null, null, null, null);
				$oiContact->subscribeContactToList($contact_data->contactId, $list_id =120680, 'normal');
				echo 'success';
			else:
				echo 'failed';
			endif;



			// Create a contact
			//var_dump($oiContact->addContact('joe@shmoe.com', null, null, 'Joe', 'Shmoe', null, '123 Somewhere Ln', 'Apt 12', 'Somewhere', 'NW', '12345', '123-456-7890', '123-456-7890', null));

			// Get messages
			//var_dump($oiContact->getMessages());


			// Create a list
			//var_dump($oiContact->addList('somelist', 1698, true, false, false, 'Just an example list', 'Some List'));

			// Subscribe contact to list
			//var_dump($oiContact->subscribeContactToList(42094396, 179962, 'normal'));

			// Grab all campaigns
			//var_dump($oiContact->getCampaigns());


			// Create message
			//var_dump($oiContact->addMessage('An Example Message', 585, '<h1>An Example Message</h1>', 'An Example Message', 'ExampleMessage', 33765, 'normal'));


			// Schedule send
			//var_dump($oiContact->sendMessage(array(33765), 179962, null, null, null, mktime(12, 0, 0, 1, 1, 2012)));


			// Upload data by sending a filename (execute a PUT based on file contents)
			//var_dump($oiContact->uploadData('/path/to/file.csv', 179962));


			// Upload data by sending a string of file contents
			//$sFileData = file_get_contents('/path/to/file.csv');  // Read the file
			//var_dump($oiContact->uploadData($sFileData, 179962)); // Send the data to the API
		} catch (Exception $oException) { // Catch any exceptions


			// Dump errors
			//var_dump($oiContact->getErrors());


			// Grab the last raw request data
			//var_dump($oiContact->getLastRequest());


			// Grab the last raw response data
			//var_dump($oiContact->getLastResponse());
			echo 'failed';
		}


	}


}
?>
