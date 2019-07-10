<?php
/**
 * @category    Pebble
 * @package     Pebble_Icontact
 * @author      Developer <sudip.k3@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 *
 *  Mage_Core_Controller_Front_Action  == Mage_Newsletter_SubscriberController
 */


require_once(Mage::getModuleDir('controllers','Mage_Newsletter').DS.'SubscriberController.php');

// Load the iContact library
require_once('iContactApi.php');

class Pebble_Icontact_SubscriberController extends Mage_Newsletter_SubscriberController
{
    public function indexAction(){
        echo Mage::getModuleDir('controllers','Mage_Newsletter').DS.'SubscriberController.php';
    }

    /**
      * New subscription action Magento and Icontact
      * https://github.com/icontact/icontact-api-php/blob/master/examples.php
      */
    public function newAction()
    {
        $enable = Mage::getStoreConfig('icontact/settings/active');
        $appId = Mage::getStoreConfig('icontact/settings/appId');
        $apiUsername = Mage::getStoreConfig('icontact/settings/apiUsername');
        $apiPassword = Mage::getStoreConfig('icontact/settings/apiPassword');
        $contactList = Mage::getStoreConfig('icontact/settings/contactList');

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 &&
                    !$customerSession->isLoggedIn()) {
                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                }

                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    Mage::throwException($this->__('This email address is already assigned to another user.'));
                }

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess($this->__('Confirmation request has been sent.'));
                }
                else {
                    /*Automatically synchronize newsletter subscribers to iContact List*/
                    if($enable):
                        $apiDetail =array(
                            'appId'       => $appId,
                            'apiUsername' => $apiUsername,
                            'apiPassword' => $apiPassword
                        );

                        // Give the API your information
                        iContactApi::getInstance()->setConfig($apiDetail );

                        /* Store the singleton*/
                        $oiContact = iContactApi::getInstance();
                        try {
                            // Create a contact
                            $addContact = $oiContact->addContact($email , null, null, null, null, null, null, null, null, null, null, null, null, null);

                            $iContactId = $addContact->contactId;
                            $iListId = $contactList;

                            // Subscribe contact to list
                            $oiContact->subscribeContactToList($iContactId, $iListId, $sStatus = 'normal');

                        } catch (Exception $oException) { // Catch any exceptions
                            // Dump errors
                            var_dump($oiContact->getErrors());
                            // Grab the last raw request data
                            var_dump($oiContact->getLastRequest());
                            // Grab the last raw response data
                            var_dump($oiContact->getLastResponse());
                        }
                    endif;

                    /***** end *****/
                    $session->addSuccess($this->__('Thank you for your subscription.'));
                }
            }
            catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
            }
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription.'));
            }
        }
        $this->_redirectReferer();
    }

    /**
     * Subscription confirm action
     */
    public function confirmAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            $subscriber = Mage::getModel('newsletter/subscriber')->load($id);
            $session = Mage::getSingleton('core/session');

            if($subscriber->getId() && $subscriber->getCode()) {
                if($subscriber->confirm($code)) {
                    $session->addSuccess($this->__('Your subscription has been confirmed.'));
                } else {
                    $session->addError($this->__('Invalid subscription confirmation code.'));
                }
            } else {
                $session->addError($this->__('Invalid subscription ID.'));
            }
        }

        $this->_redirectUrl(Mage::getBaseUrl());
    }

    /**
     * Unsubscribe newsletter
     */
    public function unsubscribeAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            $session = Mage::getSingleton('core/session');
            try {
                Mage::getModel('newsletter/subscriber')->load($id)
                    ->setCheckCode($code)
                    ->unsubscribe();
                $session->addSuccess($this->__('You have been unsubscribed.'));
            }
            catch (Mage_Core_Exception $e) {
                $session->addException($e, $e->getMessage());
            }
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the un-subscription.'));
            }
        }
        $this->_redirectReferer();
    }
}
