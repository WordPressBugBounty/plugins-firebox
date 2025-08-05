<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.135
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\Conditions\Conditions\EDD;

defined('ABSPATH') or die;

use FPFramework\Base\Conditions\EcommerceBase;

class EDDBase extends EcommerceBase
{
	/**
	 * The taxonomy name.
	 * 
	 * @var string
	 */
	protected $taxonomy = 'download_category';
	
    /**
     * The component's Single Page view name
     *
     * @var string
     */
    protected $postTypeSingle = 'download';

	/**
	 * Returns the cart subtotal.
	 * 
	 * @return  float
	 */
	public function getCartSubtotal()
	{
		$this->params->set('exclude_shipping_cost', true);
		$this->params->set('exclude_tax', true);
		
		$this->beforeGetCartValue();

		$amount = edd_get_cart_subtotal();

		$this->afterGetCartValue();

		return $amount;
	}

    /**
	 * Returns the cart total.
	 * 
	 * @return  float
	 */
	public function getCartTotal()
	{
		$this->beforeGetCartValue();

		$amount = edd_get_cart_total();

		$this->afterGetCartValue();

		return $amount;
	}

	public function beforeGetCartValue()
	{
		// Whether we exclude shipping cost
		$exclude_shipping_cost = $this->params->get('exclude_shipping_cost', false) === true;

		// Whether we exclude tax
		$exclude_tax = $this->params->get('exclude_tax', false) === true;

		// When we exclude shipping cost, add the filter to remove fees
		if ($exclude_shipping_cost)
		{
			add_filter('edd_fees_get_fees', [$this, 'edd_remove_fees'], 10, 2);
		}

		if ($exclude_tax)
		{
			add_filter('edd_get_cart_tax', [$this, 'edd_remove_tax']);
		}
	}

	protected function afterGetCartValue()
	{
		// Whether we exclude shipping cost
		$exclude_shipping_cost = $this->params->get('exclude_shipping_cost', false) === true;

		// Whether we exclude tax
		$exclude_tax = $this->params->get('exclude_tax', false) === true;

		// When we exclude shipping cost, undo the filter that removes fees
		if ($exclude_shipping_cost)
		{
			remove_filter('edd_fees_get_fees', [$this, 'edd_remove_fees']);
		}

		if ($exclude_tax)
		{
			remove_filter('edd_get_cart_tax', [$this, 'edd_remove_tax']);
		}
	}

	public function edd_remove_fees($fees, $fees_instance)
	{
		return [];
	}

	public function edd_remove_tax($tax)
	{
		return 0;
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
	 *  Returns the EDD cart.
	 * 
	 *  @return  array
	 */
	public function getCart()
	{
		if (!function_exists('EDD'))
		{
			return;
		}

		return EDD()->cart;
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

		return $cart->details ?? [];
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

		if (!function_exists('EDD'))
		{
			return;
		}

		if (!$product = edd_get_download($this->request->id))
		{
			return;
		}

		if ($product->post_type !== $this->postTypeSingle)
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
			'id' => $product->ID,
			'price' => (float) edd_get_lowest_price_option($product->ID)
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

		// We require EDD "Purchase Limit" plugin to be enabled
		if (!function_exists('edd_pl_get_file_purchase_limit'))
		{
			return;
		}

		return (int) edd_pl_get_file_purchase_limit($id);
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
		
		if (!function_exists('EDD'))
		{
			return;
		}
		
		// Get customer
		if (!$customer = edd_get_customer_by('user_id', $user_id))
		{
			return;
		}

		// Get last purchase
		$last_purchase = edd_get_payments([
			'customer' => $customer->id,
			'status' => 'complete',
			'orderby' => 'date',
			'number'  => 1
		]);

		// Abort if none found
		if (!$last_purchase)
		{
			return;
		}

		return $last_purchase[0]->completed_date;
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

		$fees = $cart->fees ? $cart->fees : [];
		$total_fees = 0;
		if (is_array($fees) && count($fees))
		{
			foreach ($fees as $fee)
			{
				$total_fees += (float) $fee['amount'];
			}
		}

		return $total_fees;
	}
}