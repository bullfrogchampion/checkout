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
class Bullfrogchampion_Checkout_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * XML Path page title
	 *
	 */
	const XML_PATH_CHECKOUT_GENERAL_PAGE_TITLE = 'bfc_checkout/general/page_title';
	
	/**
	 * IndexAction
	 *
	 */
	public function indexAction()
	{
		$quote = Mage::getSingleton('checkout/type_onepage')->getQuote();
		$data['country_id'] = Mage::getStoreConfig('general/country/default');
		
		if (!$quote->hasItems() || $quote->getHasError()) {
			$this->_redirect('checkout/cart');
			return;
		}
		
		if (!$quote->validateMinimumAmount()) {
			Mage::getSingleton('checkout/session')->addError(Mage::getStoreConfig('sales/minimum_order/error_message'));
			$this->_redirect('checkout/cart');
			return;
		}
		
		if (!Mage::helper('bfc_checkout')->isEnabled()) {
			$this->_redirect('checkout');
			return;
		}
		
		Mage::helper('bfc_checkout/data')->setDefaultShipping($data);
		
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle(Mage::getStoreConfig(self::XML_PATH_CHECKOUT_GENERAL_PAGE_TITLE));
		$this->renderLayout();
	}
}
