<?php

class Sinabs_Directcheckout_Block_Checkout_Coupon extends Mage_Core_Block_Template
{
	const XML_PATH_DIRECTCHECKOUT_EXTRA_ENABLE_COUPON = 'directcheckout/extra/enable_coupon';
	
	protected function _getQuote()
	{
		return Mage::getSingleton('checkout/cart')->getQuote();
	}
	
	public function isEnabled()
	{
		return Mage::getStoreConfig(self::XML_PATH_DIRECTCHECKOUT_EXTRA_ENABLE_COUPON);
	}
	
	public function isCouponEnable()
	{
		return (string)$this->_getQuote()->getCouponCode() != '';
	}
	
	public function getCouponCode()
	{
		return $this->_getQuote()->getCouponCode();
	}
}