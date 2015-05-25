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
class Bullfrogchampion_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * XML path allowed to order in guest
	 *
	 */
	const XML_PATH_CHECKOUT_OPTIONS_GUEST_CHECKOUT = 'checkout/options/guest_checkout';
	
	/**
	 * XML path order enable custom checkout
	 *
	 */
	const XML_PATH_CHECKOUT_GENERAL_ENABLED = 'bfc_checkout/general/enabled';
	
	/**
	 * XML path enable product quick view 
	 *
	 */
	const XML_PATH_CHECKOUT_OPTIONS_QUICKVIEW = 'bfc_checkout/options/quickview';
	
	
	/**
	 * XML path enable update product qty
	 *
	 */
	const XML_PATH_CHECKOUT_OPTIONS_UPDATE_QTY = 'bfc_checkout/options/update_qty';
	
	/**
	 * XML path refresh order summary when postcode change
	 *
	 */
	const XML_PATH_CHECKOUT_OPTIONS_UPDATE_POSTCODE = 'bfc_checkout/options/update_postcode';
	
	/**
	 * XML path refresh order summary when postcode change
	 *
	 */
	const XML_PATH_CHECKOUT_OPTIONS_ICONS_COLOR = 'bfc_checkout/options/icons_color';
	
	/**
	 * XML path enable address autocomplete
	 *
	 */
	const XML_PATH_CHECKOUT_GOOGLE_ENABLED = 'bfc_checkout/google/enabled';
	
	/**
	 * XML path enable display address form
	 *
	 */
	const XML_PATH_CHECKOUT_GOOGLE_DISPLAYFORM = 'bfc_checkout/google/displayform';
	
	/**
	 * XML path address autocomplete placeholder
	 *
	 */
	const XML_PATH_CHECKOUT_GOOGLE_PLACEHOLDER = 'bfc_checkout/google/placeholder';
	
	/**
	 * XML path enable fax field
	 *
	 */
	const XML_PATH_CHECKOUT_CHECKOUT_FIELDS_ENABLED_FAX = 'bfc_checkout/checkout_fields/enabled_fax';
	
	/**
	 * XML path enable society fax
	 *
	 */
	const XML_PATH_CHECKOUT_CHECKOUT_FIELDS_ENABLED_SOCIETY = 'bfc_checkout/checkout_fields/enabled_society';
	
	/**
	 * Is Enabled
	 *
	 * @return boolean
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_GENERAL_ENABLED) && Mage::helper('core')->isModuleOutputEnabled('Bullfrogchampion_Checkout');
	}
	
	/**
	 * Set Shipping default address
	 *
	 * @param array $data
	 * @return Mage_Core_Checkout_Model_Quote
	 */
	public function setDefaultShipping($data)
	{
		$region = (!isset($data['region']) || empty($data['region'])) ? "-" : $data['region'];
		$region_id = (!isset($data['region_id']) || empty($data['region_id'])) ? "-" : $data['region_id'];
		$city = (!isset($data['city']) || empty($data['city'])) ? "-" : $data['city'];
		
		$quote = Mage::getSingleton('checkout/type_onepage')->getQuote();
	    
	    $quote->getShippingAddress()
			->setCountryId($data['country_id'])
			->setRegionId($region_id)
			->setRegion($region)
			->setCity($city)
			->setCollectShippingRates(true);
			
		$quote->getShippingAddress()->collectShippingRates();
	    $quote->setTotalsCollectedFlag(false);
		$quote->save();
		
		return $quote;
	}
	
	/**
	 * Is newsletter module enabled
	 *
	 * @return boolean
	 */
	public function isNewsletterEnabled()
    {
    	return Mage::helper('core')->isModuleOutputEnabled('Mage_Newsletter');
    }
    
    /**
     * Is guest order enabled
     *
     * @return boolean
     */
    public function isGuestEnabled()
    {
    	return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_OPTIONS_GUEST_CHECKOUT);
    }
    
    /**
     * Is Quickview Enable
     *
     * @return boolean
     */
    public function isQuickviewEnabled()
    {
    	return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_OPTIONS_QUICKVIEW);
    }
    
    /**
     * Is update product qty enabled
     *
     * @return boolean
     */
    public function isUpdateQtyEnabled()
    {
    	return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_OPTIONS_UPDATE_QTY);
    }
    
     /**
     * Can refresh when post code change
     *
     * @return boolean
     */
    public function canRefreshOnPostCodeChange()
    {
    	return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_OPTIONS_UPDATE_POSTCODE);
    }
    
    /**
     * Return hexa code of icons color
     *
     * @return string
     */
    public function getIconsColor()
    {
    	return '#' . Mage::getStoreConfig(self::XML_PATH_CHECKOUT_OPTIONS_ICONS_COLOR);
    }
    
    /**
     * Is address autocomple enabled
     *
     * @return boolean
     */
    public function isAddressAutocompleteEnabled()
    {
    	return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_GOOGLE_ENABLED);
    }
    
    /**
     * Can display address form when google address selected
     *
     * @return string
     */
    public function canDisplayAddressForm()
    {
    	return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_GOOGLE_DISPLAYFORM);
    }
    
    /**
     * Get address autocomplete placeholder
     *
     * @return string
     */
    public function getAddressPlaceholder()
    {
    	return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_GOOGLE_PLACEHOLDER);
    }
    
    /**
     * Is enabled Fax field
     *
     * @return boolean
     */
    public function isEnabledFaxField()
    {
    	return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_CHECKOUT_FIELDS_ENABLED_FAX);
    }
    
    /**
     * Is enabled society field
     *
     * @return boolean
     */
    public function isEnabledSocietyField()
    {
    	return Mage::getStoreConfig(self::XML_PATH_CHECKOUT_CHECKOUT_FIELDS_ENABLED_SOCIETY);
    }
}
