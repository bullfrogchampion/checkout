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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Bullfrogchampion
 * @package     Bullfrogchampion_Checkout
 * @copyright   Copyright (c) 2015 Jeremy Champion (http://www.jeremychampion.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Sinabs <http://www.sinabs.fr>
 * @author      Jeremy Champion <bullfrogchampion@gmail.com>
 */
class Bullfrogchampion_Checkout_Block_Checkout_Coupon extends Mage_Core_Block_Template
{
	const XML_PATH_CHECKOUT_EXTRA_ENABLE_COUPON = 'bfc_checkout/extra/enable_coupon';
	
	protected function _getQuote()
	{
		return Mage::getSingleton('checkout/cart')->getQuote();
	}
	
	public function isEnabled()
	{
		return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_EXTRA_ENABLE_COUPON);
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
