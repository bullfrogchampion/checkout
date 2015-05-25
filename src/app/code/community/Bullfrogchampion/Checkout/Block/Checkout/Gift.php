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
class Bullfrogchampion_Checkout_Block_Checkout_Gift extends Mage_Core_Block_Template
{
	/**
	 * XML path allow gift's order
	 *
	 */
	const XML_PATH_SALES_GIFT_OPTIONS_ALLOW_ORDER = 'sales/gift_options/allow_order';
	
	/**
	 * XML path allow to add gift's items
	 *
	 */
	const XML_PATH_SALES_GIFT_OPTIONS_ALLOW_ITEMS = 'sales/gift_options/allow_items';
	
	/**
	 * Is allowed gift's order or gift's items
	 *
	 * @return boolean
	 */
	public function isShow()
	{
		return Mage::getStoreConfig(self::XML_PATH_SALES_GIFT_OPTIONS_ALLOW_ORDER) || Mage::getStoreConfig(self::XML_PATH_SALES_GIFT_OPTIONS_ALLOW_ITEMS);
	}
	
	/**
	 * Is allowed gift's order
	 *
	 * @return boolean
	 */
	public function allowGiftOrder()
	{
		return Mage::getStoreConfig(self::XML_PATH_SALES_GIFT_OPTIONS_ALLOW_ORDER);
	}
	
	/**
	 * Is allowed gift's items
	 *
	 * @return boolean
	 */
	public function allowGiftItems()
	{
		return Mage::getStoreConfig(self::XML_PATH_SALES_GIFT_OPTIONS_ALLOW_ITEMS);
	}
	
	/**
	 * Is allowed all gift's message
	 *
	 * @return boolean
	 */
	public function allowGiftAll()
	{
		return Mage::getStoreConfig(self::XML_PATH_SALES_GIFT_OPTIONS_ALLOW_ORDER) && Mage::getStoreConfig(self::XML_PATH_SALES_GIFT_OPTIONS_ALLOW_ITEMS);
	}
	
	/**
	 * Retrieve customer name
	 *
	 * @return string
	 */
	public function getCustomerName()
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			if ($customer->getId()) {
				return $customer->getFirstname() . ' ' . $customer->getLastname();	
			}
		}
		return '';
	}
	
	/**
	 * Retrieve current quote ID
	 *
	 * @return int
	 */
	public function getQuoteId()
	{
		return $quote = Mage::getSingleton('checkout/type_onepage')->getQuote()->getId();
	}
	
	/**
	 * Retrieve gift Message
	 *
	 * @return mixed Mage_Giftmessage_Model_Message | null
	 */
	public function getMessage()
	{
		$id = Mage::getSingleton('checkout/type_onepage')->getQuote()->getGiftMessageId();
		if ($id) {
			return Mage::getModel('giftmessage/message')->load($id);
		}
		return null;
	}
}
