<?php
$installer = $this;
$installer->startSetup();
$attribute  = array(
    'type' => 'text',
    'label'=> 'Description2',
    'input' => 'textarea',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => "",
    'wysiwyg_enabled' => false,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'group' => "General Information"
);
$installer->addAttribute('catalog_category', 'description2', $attribute);
$installer->endSetup();
?>