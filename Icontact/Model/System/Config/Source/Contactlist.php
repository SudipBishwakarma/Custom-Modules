<?php
/**
 * Used in creating options config value selection
 *
 */
require_once('iContactApi.php');

class Pebble_Icontact_Model_System_Config_Source_Contactlist
{
 
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $enable = Mage::getStoreConfig('icontact/settings/active');
        $appId = Mage::getStoreConfig('icontact/settings/appId');
        $apiUsername = Mage::getStoreConfig('icontact/settings/apiUsername');
        $apiPassword = Mage::getStoreConfig('icontact/settings/apiPassword');
        $contactList = Mage::getStoreConfig('icontact/settings/contactList');

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
                $items = array();
				foreach ($oiContact->getLists() as $key => $list){
				    $items[] = array();
                    $items[$key]['value'] = $list->listId;
                    $items[$key]['label'] = $list->name;
				}               

            } catch (Exception $oException) { 
                // Catch any exceptions
                var_dump($oiContact->getErrors());
            }
        else:

            return array(
                array('value' => '', 'label'=>Mage::helper('icontact')->__('None')),
            );

        endif;

        return $items;
    }
}