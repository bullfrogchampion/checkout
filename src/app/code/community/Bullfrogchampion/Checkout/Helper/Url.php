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
class Bullfrogchampion_Checkout_Helper_Url extends Mage_Checkout_Helper_Url
{
	/**
	 * Retrieve checkout URL
	 *
	 * @return string
	 */
	public function getCheckoutUrl()
    {
    	return $this->_getUrl((Mage::helper('bfc_checkout')->isEnabled()) ? 'directcheckout' : 'checkout');
    }
}
