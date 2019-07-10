<?php

class Pebble_Ordergrid_Block_Adminhtml_Sales_Order_Renderer_ShipmentDate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $order) {

        $shipmentCollection = $order->getShipmentsCollection();
        $count = $shipmentCollection->count();
        foreach ($shipmentCollection as $shipment) {
            $date = Mage::getModel('core/date')->date('M j, Y', strtotime($shipment->getCreatedAt())).'<br>';
            $date_list .= $date;
        }
        $shipmentCollection->clear();
        return isset($date_list) ? $date_list : 'No Shipments!';

    }

}
