<?php 

class ProxiBlue_Donation_Block_Onepage extends Mage_Core_Block_Template {

    private $_product;

    private $_enabled;

    public function _construct() {

        $store = Mage::app()->getStore();

        $this->_enabled = Mage::getStoreConfig('proxiblue_donation/general/enabled', $store);

        if($this->_enabled) {

            $id = Mage::getStoreConfig('proxiblue_donation/general/product_id', $store);

            $this->_product = Mage::getModel('catalog/product')->load($id);

            //If no valid product is found, disable the block. This will effect the _toHtml method
            if(!$this->_product->getId()) {
                $this->_enabled = false;
            }

        }

        parent::_construct();

    }

    public function isInCart() {

        try {
            
            $cartHelper = Mage::helper('checkout/cart');
            $items = $cartHelper->getCart()->getItems();

            foreach ($items as $item) {
                if ($item->getProduct()->getId() == $this->_product->getId()){
                    return true;
                }
            }
 
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }

    public function getProduct() {
        return $this->_product;
    }

    public function _toHtml() {
        if(!$this->_enabled) { return ''; }

        return parent::_toHtml();
    }

}