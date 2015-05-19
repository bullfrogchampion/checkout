/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Bullfrogchampion
 * @package     Bullfrogchampion_Checkout
 * @copyright   Copyright (c) 2015 Jeremy Champion (http://www.jeremychampion.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (typeof BFCcheckout == 'undefined') {
	var BFCcheckout = {};
}

// Simplebuying
BFCcheckout = Class.create();
BFCcheckout.prototype = {
	initialize: function() {
		this._initModalbox();
		this._initFormSubmit();
		this._initRegister();
	},
	_initRegister: function() {
		var el = $('customer-register');
		if (el != null) {
			if (el.checked) {
				this.toggleRegisterForm();
			}
			el.observe('change', (function(e) { 
				this.toggleRegisterForm();
			}).bind(this));	
		}
	},
	_initModalbox: function() {
		document.observe('click', function(event) { 
			var element = $(Event.element(event));
			if(element.readAttribute('class') == 'modalbox') {
				Event.stop(event);
				Modalbox.show(element.readAttribute('href'), { 
					title: element.readAttribute('title'),
					afterLoad: function() {
						Modalbox.resizeToContent();
					}
				});
			}
		});
	},
	_initFormSubmit: function() {
		document.observe('submit', function(event) { 
			Event.stop(event);
			var element = Event.element(event);
			$(element).request({ 
				method: 'post',
				onLoading: function() {
					showLoader($(element));
				},
				onComplete: function(t) {
					var response = t.responseText.evalJSON();
					if (typeof(response.message) != 'undefined') {
						$(element).update(response.message).innerHTML;	
					}
					if (typeof(response.redirect) != 'undefined') {
						if (typeof(response.timeout) != 'undefined') {
							setTimeout(function() { window.location.href = response.redirect }, response.timeout);	
						} else {
							window.location.href = response.redirect;	
						}
					}
				}
			});
		});
	},
	toggleRegisterForm: function() {
		var c = $('wrapper-register').getStyle('display');
		$('wrapper-register').setStyle({ display: (c == 'none') ? 'block' : 'none' });
	}
}

// Simplebuying Billing
BFCcheckout.Billing = Class.create();
BFCcheckout.Billing.prototype = {
	initialize: function(args) {
		this.listRegion = args.listRegion;
		this.refreshOnPostCodeChange = args.refreshOnPostCodeChange;
		this._initBillingRegion();
	},
	_initBillingRegion: function() {
		this._updateBillingRegion();
		$('billing:country_id').observe('change', (function(e) {
			this._updateBillingRegion();
			if ($('use_for_shipping').value == 1) {
				updateSpo();	
			}
		}).bind(this));
		
		if (this.refreshOnPostCodeChange == 1) {
			$('billing:postcode').observe('change', (function(e) {
				if ($('use_for_shipping').value == 1) {
					updateSpo();	
				}
			}).bind(this));
		}
	},
	_updateBillingRegion: function() {
		var code = $('billing:country_id').getValue();
		
		/*$('billing:region_id').select('option').each(function(o) { 
			o.remove();
		});*/
		
		$('billing:region_id').innerHTML = '';
		
		if (this.listRegion[code]) {
			document.getElementById('billing:region_id').options[document.getElementById('billing:region_id').options.length] = new Option('Please select region, state or province', '');
			for (var i in this.listRegion[code]) {
				document.getElementById('billing:region_id').options[document.getElementById('billing:region_id').options.length] = new Option(this.listRegion[code][i].name, i);
			}
			$('billing:region_id').setStyle({ display: 'block' });
			$('billing:region').setStyle({ display: 'none' });
		} else {
			$('billing:region_id').setStyle({ display: 'none' });
			$('billing:region').setStyle({ display: 'block' });
		}
	}
}

// Simplebuying Shipping
BFCcheckout.Shipping = Class.create();
BFCcheckout.Shipping.prototype = {
	initialize: function(args) {
		this.listRegion = args.listRegion;
		this.refreshOnPostCodeChange = args.refreshOnPostCodeChange;
		this._initShippingRegion();
	},
	_initShippingRegion: function() {
		this._updateShippingRegion();
		$('shipping:country_id').observe('change', (function(e) { 
			this._updateShippingRegion();
			updateSpo();
		}).bind(this));
		
		if (this.refreshOnPostCodeChange == 1) {
			$('shipping:postcode').observe('change', (function(e) {
				updateSpo();
			}).bind(this));
		}
	},
	_changeShippingMethod: function() {
		$$('[name="shipping_method"]').each(function(r, i) { 
			$(r).observe('change', function(e) {
				if ($('use_for_shipping').value == 0) {
					updateSpo('shipping');	
				}
			});
		});
	},
	_updateShippingRegion: function() {
		var code = $('shipping:country_id').getValue();
		
		/*$('shipping:region_id').select('option').each(function(o) { 
			o.remove();
		});*/
		$('shipping:region_id').innerHTML = '';
		
		if (this.listRegion[code]) {
			document.getElementById('shipping:region_id').options[document.getElementById('shipping:region_id').options.length] = new Option('Please select region, state or province', '');
			for (var i in this.listRegion[code]) {
				document.getElementById('shipping:region_id').options[document.getElementById('shipping:region_id').options.length] = new Option(this.listRegion[code][i].name, i);
			}
			$('shipping:region_id').setStyle({ display: 'block' });
			$('shipping:region').setStyle({ display: 'none' });
		} else {
			$('shipping:region_id').setStyle({ display: 'none' });
			$('shipping:region').setStyle({ display: 'block' });
		}
	}
}

// Google Places
BFCcheckout.GooglePlacesBilling = Class.create();
BFCcheckout.GooglePlacesBilling.prototype = {
	initialize: function(args) {
		displayAddressForm = args.displayAddressForm;
		this.service = new google.maps.places.AutocompleteService();
		this._initAutocompleteField();
		this.trad = args.translation;
	},
	_initAutocompleteField: function() {
		var el = $('billing-address-autocomplete');
		var container = $('billing-place-container');
		if (el) {
			el.observe('keyup', (function(e) { 
				var value = el.value;
				if (value != '') {
					container.setStyle({ display: 'block' });
					this.service.getPlacePredictions({ 
						input: value,
						type: 'geocode'
					}, this.displayResult);	
				} else {
					container.setStyle({ display: 'none' });
				}
			}).bind(this));
		}
	},
	displayResult: function(p, s) {
		if (s == 'OK') {
			var container = $('billing-place-container');
			container.update('').innerHTML;
			p.each(function(r, i) { 
				var el = new Element('li', { 'class': 'address-result', 'data-place': Object.toJSON(r)}).update(r.description);
				el.observe('click', (function(e) { 
					geoAddress($(Event.element(e)).readAttribute('data-place').evalJSON(), displayAddressForm, false);
				}).bind(this));				
				container.insert(el).innerHTML;
			});
			var address_not_found = new Element('li', {'class': 'address-not-found'}).update(this.trad); 
			container.insert(address_not_found).innerHTML;
			address_not_found.observe('click', (function(e) {
				displayFormAddress('billing');
			}));
		}
	}
}

var displayAddressForm;

// Google Places
BFCcheckout.GooglePlacesShipping = Class.create();
BFCcheckout.GooglePlacesShipping.prototype = {
	initialize: function(args) {
		displayAddressForm = args.displayAddressForm;
		this.service = new google.maps.places.AutocompleteService();
		this._initAutocompleteField();
		this.trad = args.translation;
	},
	_initAutocompleteField: function() {
		var el = $('shipping-address-autocomplete');
		var container = $('shipping-place-container');
		if (el) {
			el.observe('keyup', (function(e) { 
				var value = el.value;
				if (value != '') {
					container.setStyle({ display: 'block' });
					this.service.getPlacePredictions({ 
						input: value,
						type: 'geocode'
					}, this.displayResult);	
				} else {
					container.setStyle({ display: 'none' });
				}
			}).bind(this));
		}
	},
	displayResult: function(p, s) {
		if (s == 'OK') {
			var container = $('shipping-place-container');
			container.update('').innerHTML;
			p.each(function(r, i) { 
				var el = new Element('li', { 'class': 'address-result', 'data-place': Object.toJSON(r)}).update(r.description);
				el.observe('click', (function(e) { 
					geoAddress($(Event.element(e)).readAttribute('data-place').evalJSON(), displayAddressForm, true);
				}).bind(this));				
				container.insert(el).innerHTML;
			});
			var address_not_found = new Element('li', {'class': 'address-not-found'}).update(this.trad); 
			container.insert(address_not_found).innerHTML;
			address_not_found.observe('click', (function(e) {
				displayFormAddress('shipping');
			}));
		}
	}
}

/**
* Functions
*/
// Current payment method selected
currentPaymentMethod = null;

// Check if "use for shipping" checked
function checkUseForShipping(o) {
	$('shipping_address').setStyle({display: o ? 'block' : 'none'});
	$('use_for_shipping').value = (o == true) ? 0 : 1;
	updateSpo();
}

// Payment method switch and display associated content
function switchPaymentMethod(method) {
	var el = $('payment_form_' + method);
	if (currentPaymentMethod != null) {
		var elCurrent = $('payment_form_' + currentPaymentMethod);
		elCurrent.setStyle({ display: 'none' });
	}
	if (el) {
		$(el).setStyle({ display: 'block' });
		currentPaymentMethod = method;
	}
}

function displayFormAddress(address_type) {
	$(address_type + '-place-container').setStyle({ display: 'none' });
	$(address_type + '-address-autocomplete').clear();
	$(address_type + '-address-autocomplete').up('div').up('div').setStyle({ display: 'none' });
	$(address_type + '-fields').setStyle({ display: 'block' });
}

// New address selected
function newAddress(adType, value) {
	if (value == '') {
		$(adType + 'Form').setStyle({ display: 'block' });
	} else {
		$(adType + 'Form').setStyle({ display: 'none' });
	}
}

// Display loader on element e
function showLoader(e) {
	html = '<div class="bfc-checkout-loading-wrapper"><div class="bfc-checkout-loading"></div></div>';
	$(e).update(html).innerHTML;
}

// Update Shipping, Billing and Summary
function updateSpo(mode) {
	new Ajax.Request(urlSpo, { 
		parameters: $('bfc-checkout-form').serialize(true),
		onLoading: function() {
			if (mode != 'shipping' && mode != 'payment' && mode != 'review') {
				showLoader($('shipping_methods_list'));
			}
			if (mode != 'payment' && mode != 'review') {
				showLoader($('payment_methods_list'));
			}
			
			showLoader($('review_order'));
		},
		onSuccess: function(t) {
			var response = t.responseText.evalJSON();
			if (mode != 'shipping' && mode != 'payment') {
				$('shipping_methods_list').update(response.shipping_methods).innerHTML;
			}
			if (mode != 'payment') {
				$('payment_methods_list').update(response.payment_methods).innerHTML;
			}
			$('review_order').update(response.summary).innerHTML;
		}
	});
}

// Save Order
function saveOrder() {
	var myForm = new VarienForm('bfc-checkout-form', true);
	if(myForm.validator && myForm.validator.validate()) {
		new Ajax.Request(urlSaveOrder, { 
			parameters: $('bfc-checkout-form').serialize(true),
			onLoading: function() {
				$('submit-order').setStyle({ display: 'none' });
				$('review-please-wait').setStyle({ display: 'block' });
			},
			onSuccess: function(t) {
				$('submit-order').setStyle({ display: 'block' });
				$('review-please-wait').setStyle({ display: 'none' });
				
				var response = t.responseText.evalJSON();
				if (response.error) {
					str = "<p>";
					if (typeof(response.message) == 'string') {
						Modalbox.show(str + response.message + '</p>', {title:(response.title && typeof(response.title) == 'string') ? response.title : 'Error'});
					} else {
						response.message.each(function(i, r) { 
							str += r + '<br />';
							Modalbox.show(str);
						});
					}
				} else {
					if (response.redirect) {
						self.location = response.redirect;
					}
				}
			}
		});
    }
	return false;
}

// Update coupon code
function updateCouponCode(action) {
	var container = $('bfc-checkout-discount-wrapper');
	if (container != null) {
		var couponCode = $('coupon_code').value;
		if (couponCode != '') {
			new Ajax.Request(urlCoupon, { 
				parameters: {
					coupon: (action == 'delete') ? '' : couponCode
				},
				onLoading: function() {
					$('coupon_delete').setStyle({ display: 'none' });
					$('coupon_add').setStyle({ display: 'none' });
					$('button-loading').setStyle({ display: 'block' });
				},
				onSuccess: function(t) {
					var response = t.responseText.evalJSON();
					if (response.success == true) {
						$('button-loading').setStyle({ display: 'none' });
						if (action == 'delete') {
							$('coupon_add').setStyle({ display: 'block' });
							$('coupon_delete').setStyle({ display: 'none' });
							$('coupon_code').removeAttribute('readonly')
						} else {
							$('coupon_add').setStyle({ display: 'none' });
							$('coupon_delete').setStyle({ display: 'block' });
							$('coupon_code').setAttribute('readonly', 'readonly');
						}
						updateSpo('review');
					} else {
						$('button-loading').setStyle({ display: 'none' });
						$('coupon_add').setStyle({ display: 'block' });
						Modalbox.show('<p>' + response.message + '</p>', {title:(response.title && typeof(response.title) == 'string') ? response.title : 'Error'});
					}
				}
			});
		}
	}
}

// Replace image by thumbnail
function updateProductImage() {
	var container = $$('.bfc-checkout-product-medias img')[0];
	var images = $$('.more-views ul li a');
	images.each(function(r, i) { 
		$(r).observe('click', function(e) {
			var element = e.findElement('img');
			var src = Element.readAttribute($(element), 'data-src');
			container.setAttribute('src', src);
		});
	});
}

// Update Qty
function updateQty(itemId, qty) {
	new Ajax.Request(urlQty, { 
		parameters: {
			itemId: itemId,
			qty: qty
		},
		onLoading: function() {
			showLoader($('review_order'));
		},
		onSuccess: function(t) {
			response = t.responseText.evalJSON();
			if (response.success == true) {
				updateSpo();	
			} else {
				updateSpo();
				Modalbox.show('<p>' + response.message + '</p>', {title:(response.title && typeof(response.title) == 'string') ? response.title : 'Error'});
			}
		}
	});
}

// Google address geolocation
function geoAddress(a, displayAddressForm, shipping) {
	var service = new google.maps.places.PlacesService(document.createElement('div'));
	service.getDetails({
		reference: a.reference
	}, function(d, s) {
		if (s == 'OK') {
			var address = {
				street_number: null,
				route: '',
				country: {
					short_name: null,
					long_name: null
				},
				state: {
					area1: {
						short_name: null,
						long_name: null
					},
					area2: {
						short_name: null,
						long_name: null
					}
				},
				postal_code: null,
				locality: null
			};
			address.formatted_address = d.formatted_address;
			d.address_components.each(function(r, i) {
				r.types.each(function(type) { 
					switch (type) {
						case 'street_number':
							address.street_number = r.long_name;
							break;
						case 'route':
							address.route = r.long_name;
							break;
						case 'country':
							address.country.short_name = r.short_name;
							address.country.long_name = r.long_name;
							break;
						case 'administrative_area_level_1':
							address.state.area1.short_name = r.short_name;
							address.state.area1.long_name = r.long_name;
							break;
						case 'administrative_area_level_2':
							address.state.area2.short_name = r.short_name;
							address.state.area2.long_name = r.long_name;
							break;
						case 'postal_code':
							address.postal_code = r.short_name;
							break;
						case 'locality':
							address.locality = r.long_name;
							break;
					}
				});
			});
			
			var formattedAddress = (address.formatted_address.length >= 35) 
				? address.formatted_address.substring(0, 35) + '...' 
					: address.formatted_address;
			if (shipping) {
				$('shipping-place-container').setStyle({ display: 'none' });
				$('shipping-button-search').setStyle({
					display: 'block'
				}).update(formattedAddress + '<span class="remove-address" onclick="removeShippingAddress();">X</span>').innerHTML;
				$('shipping-address-autocomplete').setStyle({ display: 'none' });
				
				if (displayAddressForm == 1) {
					$('shipping-fields').setStyle({ display: 'block' });
				}
				
				setShippingFields(address);
			} else {
				$('billing-place-container').setStyle({ display: 'none' });
				$('billing-button-search').setStyle({
					display: 'block'
				}).update(formattedAddress + '<span class="remove-address" onclick="removeBillingAddress();">X</span>').innerHTML;
				$('billing-address-autocomplete').setStyle({ display: 'none' });
				
				if (displayAddressForm == 1) {
					$('billing-fields').setStyle({ display: 'block' });
				}
				
				setBillingFields(address);
			}
			
		}
	});
}

// Set Billing Fields
function setBillingFields(address) {
	if (address.country && address.country.short_name != 'undefined') {
		
		document.getElementById('billing:country_id').value = address.country.short_name;
		new BFCcheckout.Billing({listRegion: listRegionJson});
		/*var sCountry = $('billing:country_id').select('option[value="' + address.country.short_name + '"]');
		if (sCountry && sCountry.length > 0) {
			sCountry[0].selected = true;
			new BFCcheckout.Billing({listRegion: listRegionJson});
		}*/
	}
	
	if (address.state && address.state != 'undefined') {
		if ($('billing:region_id').getStyle('display') != 'none') {
			var currentState = listRegionJson[address.country.short_name];
			var setRegionId = false;
			for(var i = 0 in currentState) {
				if (currentState[i].code == address.state.area1.short_name 
					|| currentState[i].code == address.state.area2.short_name
					|| normalizeString(currentState[i].name) == normalizeString(address.state.area1.long_name)
					|| normalizeString(currentState[i].name) == normalizeString(address.state.area2.long_name)) {
					$('billing:region_id').value = i;
					setRegionId = true;
					$break;
				}
			}
		}
		
		if($('billin:region') != 'undefined' && $('billing:region').getStyle('display') != 'none') {
			$('billing:region').value = address.state.area1.long_name;
		}
	}
	
	var street = (address.street_number && address.street_number != 'undefined') ? address.street_number + ' ' : '';
	street += (address.route && address.route != 'undefined') ? address.route : '';
	$('billing:street1').value = street;
	$('billing:postcode').value = (address.postal_code && address.postal_code != 'undefined') ? address.postal_code : '';
	$('billing:city').value = (address.locality && address.locality != 'undefined') ? address.locality : '';
	updateSpo();
}

// Set Shipping Fields
function setShippingFields(address) {
	if (address.country && address.country.short_name != 'undefined') {
		
		document.getElementById('shipping:country_id').value = address.country.short_name;
		new BFCcheckout.Shipping({listRegion: listRegionJson});
		
		/*var sCountry = $('shipping:country_id').select('option[value="' + address.country.short_name + '"]');
		if (sCountry && sCountry.length > 0) {
			sCountry[0].selected = true;
			new BFCcheckout.Shipping({listRegion: listRegionJson});
		}*/
	}
	
	if (address.state && address.state != 'undefined') {
		if ($('shipping:region_id').getStyle('display') != 'none') {
			var currentState = listRegionJson[address.country.short_name];
			var setRegionId = false;
			for(var i = 0 in currentState) {
				if (currentState[i].code == address.state.area1.short_name 
					|| currentState[i].code == address.state.area2.short_name
					|| normalizeString(currentState[i].name) == normalizeString(address.state.area1.long_name)
					|| normalizeString(currentState[i].name) == normalizeString(address.state.area2.long_name)) {
					$('shipping:region_id').value = i;
					setRegionId = true;
					$break;
				}
			}
		}
		
		if($('shipping:region') != 'undefined' && $('shipping:region').getStyle('display') != 'none') {
			$('shipping:region').value = address.state.area1.long_name;
		}
	}
	
	var street = (address.street_number && address.street_number != 'undefined') ? address.street_number + ' ' : '';
	street += (address.route && address.route != 'undefined') ? address.route : '';
	$('shipping:street1').value = street;
	$('shipping:postcode').value = (address.postal_code && address.postal_code != 'undefined') ? address.postal_code : '';
	$('shipping:city').value = (address.locality && address.locality != 'undefined') ? address.locality : '';
	updateSpo();
}

// Remove Billing Address Form
function removeBillingAddress() {
	emptyAddressFields('billing');
	$('billing-address-autocomplete').setStyle({ display: 'block' }).value = '';
	$('billing-button-search').setStyle({ display: 'none' });
	$('billing-fields').setStyle({ display: 'none' });	
}

// Remove Shipping Address Form
function removeShippingAddress() {
	emptyAddressFields('shipping');
	$('shipping-address-autocomplete').setStyle({ display: 'block' }).value = '';
	$('shipping-button-search').setStyle({ display: 'none' });
	$('shipping-fields').setStyle({ display: 'none' });	
}

function emptyAddressFields(address) {
	$(address + ':country_id').clear();
	$(address + ':region_id').clear();
	$(address + ':region').clear();
	$(address + ':street1').clear();
	$(address + ':postcode').clear();
	$(address + ':city').clear();
}

// Normalize String
function normalizeString(s) {
	if (s) {
		var r = s.toLowerCase();
		r = r.replace(new RegExp("\\s", 'g'),"");
		r = r.replace(new RegExp("[àáâãäå]", 'g'),"a");
		r = r.replace(new RegExp("æ", 'g'),"ae");
		r = r.replace(new RegExp("ç", 'g'),"c");
		r = r.replace(new RegExp("[èéêë]", 'g'),"e");
		r = r.replace(new RegExp("[ìíîï]", 'g'),"i");
		r = r.replace(new RegExp("ñ", 'g'),"n");                            
		r = r.replace(new RegExp("[òóôõö]", 'g'),"o");
		r = r.replace(new RegExp("œ", 'g'),"oe");
		r = r.replace(new RegExp("[ùúûü]", 'g'),"u");
		r = r.replace(new RegExp("[ýÿ]", 'g'),"y");
		r = r.replace(new RegExp("\\W", 'g'),"");
		return r;
	} else {
		return '';
	}
}
