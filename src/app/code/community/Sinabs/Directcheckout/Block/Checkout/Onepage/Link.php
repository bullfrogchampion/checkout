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
 * @category    Sinabs
 * @package     Sinabs_Directcheckout
 * @copyright   Copyright (c) 2014 Sinabs (http://www.sinabs.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Sinabs_Directcheckout_Block_Checkout_Onepage_Link extends Mage_Checkout_Block_Onepage_Link
{
	/**
	 * Retrieve checkout URL
	 *
	 * @return string
	 */
	public function getCheckoutUrl()
	{
		return $this->getUrl((Mage::helper('directcheckout')->isEnabled()) ? 'directcheckout' : 'checkout', array(
			'_secure' => true
		));
	}
}