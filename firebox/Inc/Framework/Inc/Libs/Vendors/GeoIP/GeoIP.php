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

namespace FPFramework\Libs\Vendors\GeoIP;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use \splitbrain\PHPArchive\Tar;
use \FPFramework\GeoIp2\Database\Reader;
use \FPFramework\Base\User;

class GeoIP
{
    /**
	 * The MaxMind GeoLite database reader
	 *
	 * @var    Reader
	 */
	private $reader = null;

	/**
	 * Records for IP addresses already looked up
	 *
	 * @var   array
	 *
	 */
	private $lookups = array();

	/**
	 *  Max Age Database before it needs an update
	 *
	 *  @var  integer
	 */
	private $maxAge = 30;

	/**
	 *  Database File name
	 *
	 *  @var  string
	 */
	private $DBFileName = 'GeoLite2-City';

	/**
	 *  Database Remote URL
	 *
	 *  @var  string
	 */
	private $DBUpdateURL = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=USER_LICENSE_KEY&suffix=tar.gz';

	/**
	 *  The IP address to look up
	 *
	 *  @var  string
	 */
	private $ip;

	/**
	 *  The License Key
	 * 
	 *  @var  string
	 */
	private $key;

	/**
	 * Public constructor. Loads up the GeoLite2 database.
	 */
	public function __construct($ip = null)
	{
		if (!function_exists('bcadd') || !function_exists('bcmul') || !function_exists('bcpow'))
		{
			require_once __DIR__ . '/fakebcmath.php';
		}

		// Check we have a valid GeoLite2 database
		$filePath = $this->getDBPath();

		if (!file_exists($filePath))
		{
			$this->reader = null;
		}

		try
		{
			$this->reader = new Reader($filePath);
		}
		// If anything goes wrong, MaxMind will raise an exception, resulting in a WSOD. Let's be sure to catch everything.
		catch(\Exception $e)
		{
			$this->reader = null;
		}

		// Setup IP
        $this->ip = $ip ?: User::getIP();

		if (in_array($this->ip, array('127.0.0.1', '::1')))
		{
			$this->ip = '';
		}
    }

	/**
	 *  Sets the license key
	 * 
	 *  @param   string
	 * 
	 *  @return  mixed
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 *  Retrieves the key
	 * 
	 *  @return  string
	 */
	private function getKey()
	{
		if ($this->key)
		{
			return $this->key;
		}

		if (!$license_key = get_option('fpf_geo_license_key'))
		{
			return '';
		}
		
		return $license_key;
	}

	/**
	 *  Set the IP to look up
	 *
	 *  @param  string  $ip  The IP to look up
	 */
	public function setIP($ip)
	{
		$this->ip = $ip;
		return $this;
    }

	/**
	 * Gets the ISO country code from an IP address
	 *
	 * @return  mixed  A string with the country ISO code if found, false if the IP address is not found, null if the db can't be loaded
	 */
	public function getCountryCode()
	{
		$record = $this->getRecord();

		if ($record === false || is_null($record))
		{
			return false;
		}

		return $record->country->isoCode;
	}

	/**
	 * Gets the country name from an IP address
	 *
	 * @param   string  $locale  The locale of the country name, e.g 'de' to return the country names in German. If not specified the English (US) names are returned.
	 *
	 * @return  mixed  A string with the country name if found, false if the IP address is not found, null if the db can't be loaded
	 */
	public function getCountryName($locale = null)
	{
		$record = $this->getRecord();

		if ($record === false || is_null($record))
		{
			return false;
		}

		if (empty($locale))
		{
			return $record->country->name;
		}

		return $record->country->names[$locale];
	}

	/**
	 * Gets the continent ISO code from an IP address
	 *
	 * @return  mixed  A string with the country name if found, false if the IP address is not found, null if the db can't be loaded
	 */
	public function getContinentCode($locale = null)
	{
		$record = $this->getRecord();

		if ($record === false || is_null($record))
		{
			return false;
		}

		return $record->continent->code;
	}

	/**
	 * Gets the continent name from an IP address
	 *
	 * @param   string  $locale  The locale of the continent name, e.g 'de' to return the country names in German. If not specified the English (US) names are returned.
	 *
	 * @return  mixed  A string with the country name if found, false if the IP address is not found, null if the db can't be loaded
	 */
	public function getContinentName($locale = null)
	{
		$record = $this->getRecord();

		if ($record === false || is_null($record))
		{
			return false;
		}

		if (empty($locale))
		{
			return $record->continent;
		}

		return $record->continent->names[$locale];
	}

	/**
	 * Gets a raw record from an IP address
	 *
	 * @return  mixed  A \GeoIp2\Model\City record if found, false if the IP address is not found, null if the db can't be loaded
	 */
	public function getRecord()
	{
		if (empty($this->ip))
		{
			return false;
		}

		$ip = $this->ip;

		$needsToLoad = !array_key_exists($ip, $this->lookups);

		if ($needsToLoad)
		{
			try
			{
				if (!is_null($this->reader))
				{
					$this->lookups[$ip] = $this->reader->city($ip);
				}
				else
				{
					$this->lookups[$ip] = null;
				}
			}
			catch (\FPFramework\GeoIp2\Exception\AddressNotFoundException $e)
			{
				$this->lookups[$ip] = false;
			}
			catch (\Exception $e)
			{
				// GeoIp2 could throw several different types of exceptions. Let's be sure that we're going to catch them all
				$this->lookups[$ip] = null;
			}
		}

		return $this->lookups[$ip];
	}

	/**
	 *  Gets the city's name from an IP address
	 *
     *  @param   string  $locale  The locale of the city's name, e.g 'de' to return the city names in German. If not specified the English (US) names are returned.
	 *  @return  mixed   A string with the city name if found, false if the IP address is not found, null if the db can't be loaded
	 */
	public function getCity($locale = null)
	{
		$record = $this->getRecord();

		if ($record === false || is_null($record))
		{
			return false;
		}
        
        if (empty($locale))
        {
            return $record->city->name;
        }

		return $record->city->names[$locale];
    }
    
    /**
	 *  Gets a geographical region's (i.e. a country's province/state) name from an IP address
	 *
     *  @param   string  $locale  The locale of the regions's name, e.g 'de' to return region names in German. If not specified the English (US) names are returned.
	 *  @return  mixed   A string with the region's name if found, false if the IP address is not found, null if the db can't be loaded
	 */
	public function getRegionName($locale = null)
	{
		$record = $this->getRecord();

		if ($record === false || is_null($record))
		{
			return false;
		}
    
        // MaxMind stores region information in a 'Subdivision' object (also found in $record->city->subdivision)
        // http://maxmind.github.io/GeoIP2-php/doc/v2.9.0/class-GeoIp2.Record.Subdivision.html
        if (empty($locale))
        {
            return $record->mostSpecificSubdivision->name;
        }

		return $record->mostSpecificSubdivision->names[$locale];
    }
    
    /**
	 *  Gets a geographical region's (i.e. a country's province/state) ISO 3611-2 (alpha-2) code from an IP address
	 *
	 *  @return  mixed   A string with the region's code if found, false if the IP address is not found, null if the db can't be loaded
	 */
	public function getRegionCode()
	{
		$record = $this->getRecord();

		if ($record === false || is_null($record))
		{
			return false;
		}

        // MaxMind stores region information in a 'Subdivision' object
        // http://maxmind.github.io/GeoIP2-php/doc/v2.9.0/class-GeoIp2.Record.Subdivision.html
        return $record->mostSpecificSubdivision->isoCode;
	}

	/**
	 * Downloads and installs a fresh copy of the GeoLite2 City database
	 *
	 * @return  mixed  True on success, error string on failure
	 */
	public function updateDatabase()
	{
		$downloaded = false;
		
        // Try to download the package, if I get any exception I'll simply stop here and display the error
		try
		{
			$downloaded = $this->downloadDatabase();
		}
		catch (\Exception $e)
		{
			return $e->getMessage();
		}

		if (!$downloaded)
		{
			return fpframework()->_('FPF_GEOIP_ERR_EMPTYDOWNLOAD');
		}

		$target = $this->getTempFolder() . $this->DBFileName . '.tar.gz';

		// Unzip database to the same temporary location
		$tar = new Tar;
		$tar->open($target);
		$extracted_files = $tar->extract($this->getTempFolder());

		$database_file = '';
		$extracted_folder = '';

		// Loop through extracted files to find the name of the extracted folder and the name of the database file
		foreach ($extracted_files as $key => $extracted_file)
		{
			if ($extracted_file->getIsdir())
			{
				$extracted_folder = $extracted_file->getPath();
			}

			if (strpos($extracted_file->getPath(), '.mmdb') === false)
			{
				continue;
			}

			$database_file = $extracted_file->getPath();
		}

		// create folder if not avaialable
		if (!is_dir($this->getDBFolder()))
		{
			mkdir($this->getDBFolder(), 0755, true);
		}

		// Move database file to the temp folder, within the framework, used by php unit tests
		if (!copy($this->getTempFolder() . $database_file, $this->getFrameworkDBPath()))
		{
			return fpframework()->_('FPF_GEOIP_ERR_CANTWRITE');
		}
		
		// Also move the database file to the final store path
		if (!copy($this->getTempFolder() . $database_file, $this->getDBPath()))
		{
			return fpframework()->_('FPF_GEOIP_ERR_CANTWRITE');
		}

		// Make sure the database is readable
		if (!$this->dbIsValid())
		{
			return fpframework()->_('FPF_GEOIP_ERR_INVALIDDB');
		}

		// Delete leftovers
		unlink($target);
		$this->deleteDirectory($this->getTempFolder() . $extracted_folder);

		return true;
	}

	public function deleteDirectory($dir) {
		if (!file_exists($dir)) {
			return true;
		}
	
		if (!is_dir($dir)) {
			return unlink($dir);
		}
	
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}
	
			if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
				return false;
			}
	
		}
	
		return rmdir($dir);
	}

	/**
	 * Double check if MaxMind can actually read and validate the downloaded database
	 *
	 * @return bool
	 */
	private function dbIsValid() 
	{
		try
		{
			$reader = new Reader($this->getDBPath());
		}
		catch (\Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Download the compressed database for the provider
	 * 
	 * @return  string  The compressed data
	 *
	 * @throws  Exception
	 */
	private function downloadDatabase()
	{
		// Make sure we have enough memory limit
		ini_set('memory_limit', '-1');

		$license_key = $this->getKey();

		if (empty($license_key))
		{
			throw new \Exception(esc_html(fpframework()->_('FPF_GEOIP_LICENSE_KEY_EMPTY')));
		}

		$this->DBUpdateURL = str_replace('USER_LICENSE_KEY', $license_key, $this->DBUpdateURL);

		// ping and check response
		// we do this as if we run the wp_remote_get below regardless of the reponse, it will start overwriting
		// the database and in case of errors we will have an empty db
		$ping = wp_remote_get($this->DBUpdateURL, ['timeout' => 300]);

		if ($ping['response']['code'] == 401)
		{
			throw new \Exception(esc_html(fpframework()->_('FPF_GEOIP_ERR_UNAUTHORIZED')));
		}

		// Generic check on valid HTTP code
		if ($ping['response']['code'] > 299)
		{
			throw new \Exception(esc_html(fpframework()->_('FPF_GEOIP_ERR_MAXMIND_GENERIC')));
		}

		// Let's bubble up the exception, we will take care in the caller
		wp_remote_get($this->DBUpdateURL, ['timeout' => 300, 'stream' => true, 'filename' => $this->getTempFolder() . $this->DBFileName . '.tar.gz']);

		return true;
	}
	
	/**
	 * Returns the temp WordPress folder
	 *
	 * @return string
	 */
	private function getTempFolder()
	{
		return get_temp_dir();
	}

	/**
	 * Returns the Database local file folder.
	 * 
	 * @return  string
	 */
	private function getDBFolder()
	{
		return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'fpframework' . DIRECTORY_SEPARATOR . 'db';
	}

	/**
	 * Returns Database local file path
	 *
	 * @return  string
	 */
	private function getFrameworkDBPath()
	{
		return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'GeoIP' . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . $this->DBFileName . '.mmdb';
	}

	/**
	 * Returns Database local file path
	 *
	 * @return  string
	 */
	private function getDBPath()
	{
		return $this->getDBFolder() . DIRECTORY_SEPARATOR . $this->DBFileName . '.mmdb';
	}

	/**
	 * Does the GeoIP database need update?
	 * 
	 * @param   integer  $maxAge  The maximum age of the database in days
	 *
	 * @return  boolean
	 */
	public function needsUpdate($maxAge = null)
	{
		// Get the modification time of the database file
		$modTime = @filemtime($this->getDBPath());

		// This is now
		$now = time();

		// Minimum time difference
		$threshold = ($maxAge ? $maxAge : $this->maxAge) * 24 * 3600;

		// Do we need an update?
		$needsUpdate = ($now - $modTime) > $threshold;

		return $needsUpdate;
	}
}