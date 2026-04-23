<?php
/**
 * @package         FireBox
 * @version         3.1.6
 *
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace FireBox\Core\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Encryption
{
	/**
	 * Fixed salt for portable encryption across site exports/imports.
	 *
	 * @var  string
	 */
	private $fixed_salt = 'fbox_enc_3b7d50a8f9e84cb1a4627c8d6e1f90b3';

	/**
	 * Encrypt value for storage.
	 *
	 * @param   string  $value
	 *
	 * @return  string
	 */
	final public function encrypt($value = '')
	{
		$value = trim((string) $value);
		if ($value === '')
		{
			return '';
		}

		$key = $this->getEncryptionKey();
		if ($key === '')
		{
			return $value;
		}

		$encrypted_payload = $this->encryptWithOpenSSL($value, $key);
		if ($encrypted_payload === false)
		{
			return $value;
		}

		return base64_encode($encrypted_payload);
	}

	/**
	 * Decrypt value from storage.
	 *
	 * @param   mixed  $value
	 *
	 * @return  string
	 */
	final public function decrypt($value)
	{
		$value = trim((string) $value);
		if ($value === '')
		{
			return '';
		}

		$decoded = base64_decode($value, true);
		if ($decoded === false || $decoded === '')
		{
			return $value;
		}

		$key = $this->getEncryptionKey();
		if ($key === '')
		{
			return $value;
		}

		$decrypted = $this->decryptWithOpenSSL($decoded, $key);
		if ($decrypted === '')
		{
			return $value;
		}

		return trim((string) $decrypted);
	}

	private function getEncryptionKey()
	{
		$salt = trim((string) $this->fixed_salt);
		if ($salt === '')
		{
			return '';
		}

		return hash('sha256', $salt, true);
	}

	/**
	 * Encrypt plain text using OpenSSL.
	 *
	 * @param   string  $value
	 * @param   string  $key
	 *
	 * @return  string|false
	 */
	private function encryptWithOpenSSL($value = '', $key = '')
	{
		if (!function_exists('openssl_encrypt') || !function_exists('openssl_cipher_iv_length'))
		{
			return false;
		}

		$cipher = 'aes-256-cbc';
		$iv_length = openssl_cipher_iv_length($cipher);
		if (!is_int($iv_length) || $iv_length <= 0)
		{
			return false;
		}

		try
		{
			$iv = random_bytes($iv_length);
		}
		catch (\Exception $e)
		{
			return false;
		}

		$openssl_raw_data = defined('OPENSSL_RAW_DATA') ? OPENSSL_RAW_DATA : 1;
		$encrypted = openssl_encrypt($value, $cipher, $key, $openssl_raw_data, $iv);
		if ($encrypted === false)
		{
			return false;
		}

		return $iv . $encrypted;
	}

	/**
	 * Decrypt OpenSSL encrypted payload.
	 *
	 * @param   string  $payload
	 * @param   string  $key
	 *
	 * @return  string
	 */
	private function decryptWithOpenSSL($payload = '', $key = '')
	{
		if (!function_exists('openssl_decrypt') || !function_exists('openssl_cipher_iv_length'))
		{
			return '';
		}

		$cipher = 'aes-256-cbc';
		$iv_length = openssl_cipher_iv_length($cipher);
		if (!is_int($iv_length) || $iv_length <= 0 || strlen($payload) <= $iv_length)
		{
			return '';
		}

		$iv = substr($payload, 0, $iv_length);
		$encrypted = substr($payload, $iv_length);
		if ($iv === false || $encrypted === false)
		{
			return '';
		}

		$openssl_raw_data = defined('OPENSSL_RAW_DATA') ? OPENSSL_RAW_DATA : 1;
		$decrypted = openssl_decrypt($encrypted, $cipher, $key, $openssl_raw_data, $iv);

		return $decrypted === false ? '' : (string) $decrypted;
	}

}
