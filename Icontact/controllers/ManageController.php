<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customers newsletter subscription controller
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
// Load the iContact library
require_once('Mage/Newsletter/controllers/ManageController.php');
require_once('iContactApi.php');
class Pebble_Icontact_ManageController extends Mage_Newsletter_ManageController
{
    public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        try {
            Mage::getSingleton('customer/session')->getCustomer()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
            ->save();
            if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been saved.'));
                $this->iContactAdd();
            } else {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been removed.'));
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
        }
        $this->_redirect('customer/account/');
    }
    public function iContactAdd()
    {
        $enable = Mage::getStoreConfig('icontact/settings/active');
        if ($enable == 1):
            $appId = Mage::getStoreConfig('icontact/settings/appId');
            $apiUsername = Mage::getStoreConfig('icontact/settings/apiUsername');
            $apiPassword = Mage::getStoreConfig('icontact/settings/apiPassword');
            $contactListId = Mage::getStoreConfig('icontact/settings/contactList');
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $email = $customer->getEmail();
            $firstName = $customer->getFirstname();
            $lastName = $customer->getLastname();
            iContactApi::getInstance()->setConfig(array(
                'appId'       => $appId, 
                'apiPassword' => $apiPassword, 
                'apiUsername' => $apiUsername
            ));
            $oiContact = iContactApi::getInstance();
            $addContact = $oiContact->addContact($email , null, null, $firstName, $lastName, null, null, null, null, null, null, null, null, null);
            $iContactId = $addContact->contactId;
            $oiContact->subscribeContactToList($iContactId, $contactListId, 'normal');
        endif;
    }
}
