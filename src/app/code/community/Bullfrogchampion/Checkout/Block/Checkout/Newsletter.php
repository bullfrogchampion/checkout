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
 */
class Bullfrogchampion_Checkout_Block_Checkout_Newsletter extends Mage_Core_Block_Template
{
	/**
	 * XML path extra newsletter
	 *
	 */
	const XML_PATH_CHECKOUT_EXTRA_ENABLE_NEWSLETTER = 'checkout/extra/enable_newsletter';
	
	/**
	 * Display Newsletter
	 *
	 * @return boolean
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_EXTRA_ENABLE_NEWSLETTER);
	}
}
