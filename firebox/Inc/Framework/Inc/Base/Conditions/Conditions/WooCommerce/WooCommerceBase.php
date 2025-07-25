<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.133
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\Conditions\Conditions\WooCommerce;

defined('ABSPATH') or die;

use FPFramework\Base\Conditions\EcommerceBase;

class WooCommerceBase extends EcommerceBase
{
	/**
	 * The taxonomy name.
	 * 
	 * @var string
	 */
	protected $taxonomy = 'product_cat';
	
    /**
     * The component's Single Page view name
     *
     * @var string
     */
    protected $postTypeSingle = 'product';

    /**
	 * Returns the cart subtotal.
	 * 
	 * @return  float
	 */
	public function getCartSubtotal()
	{
		$this->params->set('exclude_shipping_cost', true);
		$this->params->set('exclude_tax', true);
		
		if (!$cart = $this->getCart())
		{
			return 0;
		}

		// Whether we exclude tax
		$exclude_tax = $this->params->get('exclude_tax', false) === true;

		$subtotal = $cart->subtotal;

		// Subtract tax if it should be excluded
		if ($exclude_tax)
		{
			$subtotal -= (float) WC()->cart->get_subtotal_tax();
		}

		return (float) $subtotal;
	}

    /**
	 * Returns the cart total.
	 * 
	 * @return  float
	 */
	public function getCartTotal()
	{
		if (!$cart = $this->getCart())
		{
			return 0;
		}

		// Whether we exclude shipping cost
		$exclude_shipping_cost = $this->params->get('exclude_shipping_cost', false) === true;

		// Whether we exclude tax
		$exclude_tax = $this->params->get('exclude_tax', false) === true;

		$total = $cart->total;

		// Subtract shipping if it should be excluded
		if ($exclude_shipping_cost)
		{
			$total -= (float) WC()->cart->get_shipping_total();
		}

		// Subtract tax if it should be excluded
		if ($exclude_tax)
		{
			$total -= (float) WC()->cart->get_subtotal_tax();
		}

		return (float) $total;
	}

    /**
     * Get single page's assosiated categories
     *
     * @param   Integer  The Single Page id
	 * 
     * @return  array
     */
	protected function getSinglePageCategories($id)
	{
		if (!class_exists('woocommerce'))
		{
			return [];
		}

		if (!$terms = get_the_terms($id, $this->taxonomy))
		{
			return [];
		}

		if (!is_array($terms))
		{
			return [];
		}

		return array_column($terms, 'term_id');
	}

    /**
	 *  Returns the WooCommerce cart.
	 * 
	 *  @return  array
	 */
	public function getCart()
	{
		if (!class_exists('woocommerce'))
		{
			return;
		}

		return WC()->cart;
    }

	/**
	 * Returns the products in the cart
	 * 
	 * @return  array
	 */
	public function getCartProducts()
	{
		if (!$cart = $this->getCart())
		{
			return [];
		}

		return $cart->cart_contents;
	}

	/**
	 * Returns the current product.
	 * 
	 * @return  object
	 */
	protected function getCurrentProduct()
	{
		if (!$this->request->id)
		{
			return;
		}

		if (!class_exists('woocommerce'))
		{
			return;
		}

		if (!$product = wc_get_product($this->request->id))
		{
			return;
		}

		return $product;
	}

	/**
	 * Returns the current product data.
	 * 
	 * @return  object
	 */
	protected function getCurrentProductData()
	{
		if (!$product = $this->getCurrentProduct())
		{
			return;
		}

		return [
			'id' => $product->get_id(),
			'price' => (float) $product->get_regular_price()
		];
	}

	/**
	 * Returns the product stock.
	 * 
	 * @param   int  $id
	 * 
	 * @return  int
	 */
	public function getProductStock($id = null)
	{
		if (!$id)
		{
			return;
		}

		if (!class_exists('woocommerce'))
		{
			return;
		}
		
		if (!$product = wc_get_product($id))
		{
			return;
		}

		return $product->get_stock_quantity();
	}

	/**
	 * Returns the current user's last purchase date in format: d/m/Y H:i:s and in UTC.
	 * 
	 * @param   int     $user_id
	 * 
	 * @return  string
	 */
	protected function getLastPurchaseDate($user_id = null)
	{
		if (!$user_id)
		{
			return;
		}

		if (!class_exists('woocommerce'))
		{
			return;
		}
		
		$customer = new \WC_Customer($user_id);

		if (!$customer->get_id())
		{
			return;
		}

		if (!$last_order = $customer->get_last_order())
		{
			return;
		}

		$purchase_date = $last_order->get_date_completed() ? $last_order->get_date_completed() : $last_order->get_date_created();
		$purchase_date = $purchase_date->format('Y-m-d H:i:s');

		return $purchase_date;
	}

	
	/**
	 * Returns the shipping total.
	 * 
	 * @return  float
	 */
	public function getShippingTotal()
	{
		if (!$cart = $this->getCart())
		{
			return 0;
		}

		return $cart->get_shipping_total();
	}
}