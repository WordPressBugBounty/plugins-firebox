<?php
/**
 * @package         FireBox
 * @version         3.1.14
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

/**
 * Encrypts integration credentials at rest.
 *
 * The key is derived from a fixed salt, which makes stored values portable: a site can be
 * migrated, cloned or restored from a backup and its credentials remain readable. That is
 * a deliberate trade — a key tied to the site (wp-config salts, say) would resist a
 * database-only leak, but would silently invalidate every credential whenever those salts
 * changed, which happens routinely during migrations.
 *
 * Be clear about what this does and does not buy: an attacker holding a database dump can
 * derive the same key from the plugin source, so this is obfuscation at rest rather than
 * a defence against disclosure. Treat the credentials themselves as the secret and rotate
 * them if a dump ever leaks.
 */
class Encryption
{
	/**
	 * Salt the encryption key is derived from.
	 *
	 * Fixed on purpose, so encrypted values survive a site move. See the class docblock.
	 *
	 * @var  string
	 */
	private $salt = 'fbox_enc_3b7d50a8f9e84cb1a4627c8d6e1f90b3';

	/**
	 * Cipher used for stored values.
	 *
	 * @var  string
	 */
	const CIPHER = 'aes-256-cbc';

	/**
	 * Prefix of payloads that carry an HMAC. Older values have no prefix and are
	 * still accepted on read so existing credentials keep working.
	 *
	 * @var  string
	 */
	const AUTHENTICATED_PREFIX = 'fbx2:';

	/**
	 * Encrypt value for storage.
	 *
	 * @param   string  $value
	 *
	 * @return  string|false  Payload, '' for empty input, or false when encryption failed.
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
			return false;
		}

		$encrypted_payload = $this->encryptWithOpenSSL($value, $key);
		if ($encrypted_payload === false)
		{
			/**
			 * Report the failure rather than returning the plaintext. Storing a raw
			 * credential under a name that implies encryption would leave it in the
			 * database in the clear with no signal to anyone.
			 */
			return false;
		}

		// Authenticate iv+ciphertext so a tampered value is rejected instead of decrypted to garbage.
		$mac = hash_hmac('sha256', $encrypted_payload, $this->getMacKey($key), true);

		return self::AUTHENTICATED_PREFIX . base64_encode($encrypted_payload . $mac);
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

		$authenticated = strpos($value, self::AUTHENTICATED_PREFIX) === 0;
		$encoded = $authenticated ? substr($value, strlen(self::AUTHENTICATED_PREFIX)) : $value;

		$decoded = base64_decode($encoded, true);
		if ($decoded === false || $decoded === '')
		{
			// Not a payload we produced; hand back the value as stored.
			return $value;
		}

		$key = $this->getEncryptionKey();
		if ($key === '')
		{
			return $value;
		}

		if ($authenticated)
		{
			$mac_length = 32;
			if (strlen($decoded) <= $mac_length)
			{
				return '';
			}

			$payload = substr($decoded, 0, -$mac_length);
			$mac = substr($decoded, -$mac_length);

			if (!hash_equals(hash_hmac('sha256', $payload, $this->getMacKey($key), true), $mac))
			{
				return '';
			}

			$decoded = $payload;
		}

		$decrypted = $this->decryptWithOpenSSL($decoded, $key);
		if ($decrypted === '')
		{
			/**
			 * A value that is valid base64 but was never encrypted (a plaintext key that
			 * happens to decode) must come back unchanged rather than as an empty string.
			 */
			return $value;
		}

		return trim((string) $decrypted);
	}

	/**
	 * Returns whether this platform can encrypt.
	 *
	 * @return  bool
	 */
	public static function isAvailable()
	{
		return function_exists('openssl_encrypt') && function_exists('openssl_cipher_iv_length');
	}

	/**
	 * Derives the encryption key.
	 *
	 * @return  string  Raw 32-byte key, or '' when the salt is unusable.
	 */
	private function getEncryptionKey()
	{
		$salt = trim((string) $this->salt);
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
	/**
	 * Separate key for the MAC, derived from the encryption key.
	 *
	 * @param   string  $key
	 *
	 * @return  string
	 */
	private function getMacKey($key)
	{
		return hash('sha256', 'mac|' . $key, true);
	}

	private function encryptWithOpenSSL($value = '', $key = '')
	{
		if (!self::isAvailable())
		{
			return false;
		}

		$iv_length = openssl_cipher_iv_length(self::CIPHER);
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
		$encrypted = openssl_encrypt($value, self::CIPHER, $key, $openssl_raw_data, $iv);
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

		$iv_length = openssl_cipher_iv_length(self::CIPHER);
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
		$decrypted = openssl_decrypt($encrypted, self::CIPHER, $key, $openssl_raw_data, $iv);

		return $decrypted === false ? '' : (string) $decrypted;
	}
}
