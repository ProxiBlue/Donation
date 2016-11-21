<?php

class ProxiBlue_Donation_DonationController extends Mage_Core_Controller_Front_Action {

    const XML_PATH_DONATION_PRODUCT_ID = 'proxiblue_donation/general/product_id';

    private $_giftItemId;
    private $_params;
    private $_product;

    public function _construct() {
        $this->_giftItemId = Mage::getStoreConfig(self::XML_PATH_DONATION_PRODUCT_ID);
        $this->_params = array('product' => $this->_giftItemId, 'related_product' => null, 'qty' => 1);
        $this->_product = Mage::getModel('catalog/product')->load($this->_giftItemId);
    }

    /**
     * Add product to cart
     */
    public function addAction() {
        $response = $this->_product->getName() . Mage::helper('proxiblue_donation')->__(' was added to your order.');
        try {
            $cartHelper = Mage::helper('checkout/cart');
            $items = $cartHelper->getCart()->getItems();
            $isIncart = false;
            foreach ($items as $item) {
                if ($item->getProduct()->getId() == $this->_giftItemId){
                    $isIncart = true;
                    //Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                    break;
                }
            }

            if ($isIncart === false){
                $cart = Mage::getSingleton('checkout/cart');
                $cart->addProduct($this->_product);
                $cart->save();
                
                Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
            }    
        } catch (Exception $e) {
            $response = Mage::helper('proxiblue_donation')->__('We coudn\'t add the donation to your shopping bag.');
            Mage::logException($e);
        }
        $this->getResponse()->setBody($response);
        return $this;
    }

    /**
     * remove product from cart 
     */
    public function removeAction() {    
        $response = $this->_product->getName() . Mage::helper('proxiblue_donation')->__(' was removed form your order.');
        try {
            $cartHelper = Mage::helper('checkout/cart');
            $items = $cartHelper->getCart()->getItems();
            foreach ($items as $item) {
                if ($item->getProduct()->getId() == $this->_giftItemId){
                    $cartHelper->getCart()->removeItem($item->getId())->save();
                    Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
                    break;
                }
            }
            
        } catch (Exception $e) {
            $response = Mage::helper('proxiblue_donation')->__('We coudn\'t remove the donation to your shopping bag. (Error Code 3218)');
            Mage::logException($e);
        }
        $this->getResponse()->setBody($response);
        return $this;
    }

}
