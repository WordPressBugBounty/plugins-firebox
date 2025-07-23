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

namespace FPFramework\Libs;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Translations
{
	/**
	 * Holds all translations of the framework and the current plugin.
	 * 
	 * @var  array
	 */
	private $translations = [];

	/**
	 * Stores cached translations.
	 * 
	 * @var  array
	 */
	private $cached = [];

	public function __construct()
	{
		$this->translations = $this->getTranslations();
	}

	/**
	 * Retrieves the translation of $text
	 * 
	 * @param   String  $text
	 * @param   String  $fallback
	 * 
	 * @return  String
	 */
	public function _($text, $fallback = null)
	{
		if (is_numeric($text))
		{
			return $text;
		}
		
		if (!is_string($text) && !is_numeric($text))
		{
			return '';
		}

		if (empty($text))
		{
			return '';
		}
		
		if (isset($this->cached[$text]))
		{
			return $this->cached[$text];
		}

		if ($fallback && isset($this->cached[$fallback]))
		{
			return $this->cached[$fallback];
		}

		if ($translation = $this->retrieve($text, $fallback))
		{
			$this->cached[$translation['source']] = $translation['value'];

			return $translation['value'];
		}

		return $fallback ? trim($fallback) : trim($text);
	}

	/**
	 * Retrieves translation of given text or of fallback text.
	 * If none found, returns false
	 * 
	 * @param   string  $text
	 * @param   string  $fallback
	 * 
	 * @return  mixed
	 */
	private function retrieve($text, $fallback = '')
	{
		if (!is_string($text) && !is_numeric($text))
		{
			return '';
		}

		$translationOfText = $this->findText($text);
		if ($translationOfText !== false)
		{
			return [
				'source' => $text,
				'value' => $translationOfText
			];
		}

		$fallback = !empty($fallback) ? $fallback : $text;

		$translationOfFallback = $this->findText($fallback);
		if ($translationOfFallback !== false)
		{
			return [
				'source' => $fallback,
				'value' => $translationOfFallback
			];
		}

		return false;
	}

	/**
	 * Tries to find translation of text. Returns false if fails.
	 * 
	 * @param   string  $text
	 * 
	 * @return  mixed
	 */
	private function findText($text)
	{
		return isset($this->translations[strtoupper(trim($text))]) ? $this->translations[strtoupper(trim($text))] : false;
	}

	/**
	 * All Translations
	 * 
	 * @return array
	 */
	public static function getTranslations()
	{
		return [
			'FPF_OVERVIEW' => __('Overview', 'firebox'),
			'FPF_DOCUMENTATION' => __('Documentation', 'firebox'),
			'FPF_FIREBOX' => __('FireBox', 'firebox'),
			'FPF_DOCS' => __('Docs', 'firebox'),
			'FPF_NEW' => __('New', 'firebox'),
			'FPF_HELP' => __('Help', 'firebox'),
			'FPF_ID' => __('ID', 'firebox'),
			'FPF_IMPORT' => __('Import', 'firebox'),
			'FPF_SETTINGS' => __('Settings', 'firebox'),
			'FPF_DISABLED' => __('Disabled', 'firebox'),
			'FPF_INCLUDE' => __('Include', 'firebox'),
			'FPF_EXCLUDE' => __('Exclude', 'firebox'),
			'FPF_ENABLED' => __('Enabled', 'firebox'),
			'FPF_NONE' => __('None', 'firebox'),
			'FPF_IMAGE' => __('Image', 'firebox'),
			'FPF_ALL' => __('All', 'firebox'),
			'FPF_ANY' => __('Any', 'firebox'),
			'FPF_POST' => __('Post', 'firebox'),
			'FPF_PAGE' => __('Page', 'firebox'),
			'FPF_PATHS' => __('Paths', 'firebox'),
			'FPF_CPT' => __('Custom Post Type', 'firebox'),
			'FPF_POST_TAG' => __('Post Tag', 'firebox'),
			'FPF_POST_CATEGORY' => __('Post Category', 'firebox'),
			'FPF_CATEGORIES' => __('Categories', 'firebox'),
			'FPF_ADVANCED' => __('Advanced', 'firebox'),
			'FPF_YES' => __('Yes', 'firebox'),
			'FPF_NO' => __('No', 'firebox'),
			'FPF_SUPPORT' => __('Support', 'firebox'),
			'FPF_UPLOAD' => __('Upload', 'firebox'),
			'FPF_PREVIEW' => __('Preview', 'firebox'),
			'FPF_MEDIA_UPLOAD_TMP_IMG_ALT' => __('media uploader preview image', 'firebox'),
			'FPF_ENSURE_PLUGIN_FOLDER_IS_READABLE' => __('Please make sure that the plugins folder is readable.', 'firebox'),
			'FPF_PA_MENU_ITEM_AS' => __('Menu Item', 'firebox'),
			'FPF_PA_MENU_ITEM_AS_DESC' => __('Target visitors who are browsing specific menu items', 'firebox'),
			'FPF_URL' => __('URL', 'firebox'),
			'FPF_PA_URL_DESC' => __('Target visitors who are browsing specific URLs', 'firebox'),
			'FPF_PA_MENU_ITEM_HINT' => __('Start searching for menu items.', 'firebox'),
			'FPF_USER_GROUP' => __('User Group', 'firebox'),
			'FPF_USER_ID' => __('User ID', 'firebox'),
			'FPF_MATCH' => __('Match', 'firebox'),
			'FPF_PAGEVIEWS' => __('Pageviews', 'firebox'),
			'FPF_TIMEONSITE' => __('Time on Site', 'firebox'),
			'FPF_NO_ITEMS_FOUND' => __('No Items found.', 'firebox'),
			'FPF_IP_ADDRESS' => __('IP Address', 'firebox'),
			'FPF_PHP' => __('PHP', 'firebox'),
			'FPF_COOKIE' => __('Cookie', 'firebox'),
			'FPF_DEVICES' => __('Devices', 'firebox'),
			'FPF_DESKTOP' => __('Desktop', 'firebox'),
			'FPF_TABLET' => __('Tablet', 'firebox'),
			'FPF_MOBILE' => __('Mobile', 'firebox'),
			/* translators: %s: Device name */
			'FPF_X_DEVICE_SETTINGS' => __('%s device settings', 'firebox'),
			'FPF_CHROME' => __('Chrome', 'firebox'),
			'FPF_FIREFOX' => __('Firefox', 'firebox'),
			'FPF_EDGE' => __('Edge', 'firebox'),
			'FPF_IE' => __('Internet Explorer', 'firebox'),
			'FPF_SAFARI' => __('Safari', 'firebox'),
			'FPF_OPERA' => __('Opera', 'firebox'),
			'FPF_OS' => __('Operating System', 'firebox'),
			'FPF_LINUX' => __('Linux', 'firebox'),
			'FPF_MAC' => __('MacOS', 'firebox'),
			'FPF_ANDROID' => __('Android', 'firebox'),
			'FPF_IOS' => __('iOS', 'firebox'),
			'FPF_WINDOWS' => __('Windows', 'firebox'),
			'FPF_BLACKBERRY' => __('Blackberry', 'firebox'),
			'FPF_CHROMEOS' => __('Chrome OS', 'firebox'),
			'FPF_CONTINENT' => __('Continent', 'firebox'),
			'FPF_CONTINENT_AF' => __('Africa', 'firebox'),
			'FPF_CONTINENT_AS' => __('Asia', 'firebox'),
			'FPF_CONTINENT_EU' => __('Europe', 'firebox'),
			'FPF_CONTINENT_NA' => __('North America', 'firebox'),
			'FPF_CONTINENT_SA' => __('South America', 'firebox'),
			'FPF_CONTINENT_OC' => __('Oceania', 'firebox'),
			'FPF_CONTINENT_AN' => __('Antartica', 'firebox'),
			'FPF_COUNTRY' => __('Country', 'firebox'),
			'FPF_COUNTRIES' => __('Countries', 'firebox'),
			'FPF_COUNTRY_CODE' => __('Country Code', 'firebox'),
			'FPF_CITY' => __('City', 'firebox'),
			'FPF_COUNTRY_AF' => __('Afghanistan', 'firebox'),
			'FPF_COUNTRY_AX' => __('Aland Islands', 'firebox'),
			'FPF_COUNTRY_AL' => __('Albania', 'firebox'),
			'FPF_COUNTRY_DZ' => __('Algeria', 'firebox'),
			'FPF_COUNTRY_AS' => __('American Samoa', 'firebox'),
			'FPF_COUNTRY_AD' => __('Andorra', 'firebox'),
			'FPF_COUNTRY_AO' => __('Angola', 'firebox'),
			'FPF_COUNTRY_AI' => __('Anguilla', 'firebox'),
			'FPF_COUNTRY_AQ' => __('Antarctica', 'firebox'),
			'FPF_COUNTRY_AG' => __('Antigua and Barbuda', 'firebox'),
			'FPF_COUNTRY_AR' => __('Argentina', 'firebox'),
			'FPF_COUNTRY_AM' => __('Armenia', 'firebox'),
			'FPF_COUNTRY_AW' => __('Aruba', 'firebox'),
			'FPF_COUNTRY_AU' => __('Australia', 'firebox'),
			'FPF_COUNTRY_AT' => __('Austria', 'firebox'),
			'FPF_COUNTRY_AZ' => __('Azerbaijan', 'firebox'),
			'FPF_COUNTRY_BS' => __('Bahamas', 'firebox'),
			'FPF_COUNTRY_BH' => __('Bahrain', 'firebox'),
			'FPF_COUNTRY_BD' => __('Bangladesh', 'firebox'),
			'FPF_COUNTRY_BB' => __('Barbados', 'firebox'),
			'FPF_COUNTRY_BY' => __('Belarus', 'firebox'),
			'FPF_COUNTRY_BE' => __('Belgium', 'firebox'),
			'FPF_COUNTRY_BZ' => __('Belize', 'firebox'),
			'FPF_COUNTRY_BJ' => __('Benin', 'firebox'),
			'FPF_COUNTRY_BM' => __('Bermuda', 'firebox'),
			'FPF_COUNTRY_BQ_BO' => __('Bonaire', 'firebox'),
			'FPF_COUNTRY_BQ_SA' => __('Saba', 'firebox'),
			'FPF_COUNTRY_BQ_SE' => __('Sint Eustatius', 'firebox'),
			'FPF_COUNTRY_BT' => __('Bhutan', 'firebox'),
			'FPF_COUNTRY_BO' => __('Bolivia', 'firebox'),
			'FPF_COUNTRY_BA' => __('Bosnia and Herzegovina', 'firebox'),
			'FPF_COUNTRY_BW' => __('Botswana', 'firebox'),
			'FPF_COUNTRY_BV' => __('Bouvet Island', 'firebox'),
			'FPF_COUNTRY_BR' => __('Brazil', 'firebox'),
			'FPF_COUNTRY_IO' => __('British Indian Ocean Territory', 'firebox'),
			'FPF_COUNTRY_BN' => __('Brunei Darussalam', 'firebox'),
			'FPF_COUNTRY_BG' => __('Bulgaria', 'firebox'),
			'FPF_COUNTRY_BF' => __('Burkina Faso', 'firebox'),
			'FPF_COUNTRY_BI' => __('Burundi', 'firebox'),
			'FPF_COUNTRY_KH' => __('Cambodia', 'firebox'),
			'FPF_COUNTRY_CM' => __('Cameroon', 'firebox'),
			'FPF_COUNTRY_CA' => __('Canada', 'firebox'),
			'FPF_COUNTRY_CV' => __('Cape Verde', 'firebox'),
			'FPF_COUNTRY_KY' => __('Cayman Islands', 'firebox'),
			'FPF_COUNTRY_CF' => __('Central African Republic', 'firebox'),
			'FPF_COUNTRY_TD' => __('Chad', 'firebox'),
			'FPF_COUNTRY_CL' => __('Chile', 'firebox'),
			'FPF_COUNTRY_CN' => __('China', 'firebox'),
			'FPF_COUNTRY_CX' => __('Christmas Island', 'firebox'),
			'FPF_COUNTRY_CC' => __('Cocos (Keeling) Islands', 'firebox'),
			'FPF_COUNTRY_CO' => __('Colombia', 'firebox'),
			'FPF_COUNTRY_KM' => __('Comoros', 'firebox'),
			'FPF_COUNTRY_CG' => __('Congo', 'firebox'),
			'FPF_COUNTRY_CD' => __('Congo, The Democratic Republic of the', 'firebox'),
			'FPF_COUNTRY_CK' => __('Cook Islands', 'firebox'),
			'FPF_COUNTRY_CR' => __('Costa Rica', 'firebox'),
			'FPF_COUNTRY_CI' => __('Cote d\'Ivoire', 'firebox'),
			'FPF_COUNTRY_HR' => __('Croatia', 'firebox'),
			'FPF_COUNTRY_CU' => __('Cuba', 'firebox'),
			'FPF_COUNTRY_CW' => __('Curaçao', 'firebox'),
			'FPF_COUNTRY_CY' => __('Cyprus', 'firebox'),
			'FPF_COUNTRY_CZ' => __('Czech Republic', 'firebox'),
			'FPF_COUNTRY_DK' => __('Denmark', 'firebox'),
			'FPF_COUNTRY_DJ' => __('Djibouti', 'firebox'),
			'FPF_COUNTRY_DM' => __('Dominica', 'firebox'),
			'FPF_COUNTRY_DO' => __('Dominican Republic', 'firebox'),
			'FPF_COUNTRY_EC' => __('Ecuador', 'firebox'),
			'FPF_COUNTRY_EG' => __('Egypt', 'firebox'),
			'FPF_COUNTRY_SV' => __('El Salvador', 'firebox'),
			'FPF_COUNTRY_GQ' => __('Equatorial Guinea', 'firebox'),
			'FPF_COUNTRY_ER' => __('Eritrea', 'firebox'),
			'FPF_COUNTRY_EE' => __('Estonia', 'firebox'),
			'FPF_COUNTRY_ET' => __('Ethiopia', 'firebox'),
			'FPF_COUNTRY_FK' => __('Falkland Islands (Malvinas)', 'firebox'),
			'FPF_COUNTRY_FO' => __('Faroe Islands', 'firebox'),
			'FPF_COUNTRY_FJ' => __('Fiji', 'firebox'),
			'FPF_COUNTRY_FI' => __('Finland', 'firebox'),
			'FPF_COUNTRY_FR' => __('France', 'firebox'),
			'FPF_COUNTRY_GF' => __('French Guiana', 'firebox'),
			'FPF_COUNTRY_PF' => __('French Polynesia', 'firebox'),
			'FPF_COUNTRY_TF' => __('French Southern Territories', 'firebox'),
			'FPF_COUNTRY_GA' => __('Gabon', 'firebox'),
			'FPF_COUNTRY_GM' => __('Gambia', 'firebox'),
			'FPF_COUNTRY_GE' => __('Georgia', 'firebox'),
			'FPF_COUNTRY_DE' => __('Germany', 'firebox'),
			'FPF_COUNTRY_GH' => __('Ghana', 'firebox'),
			'FPF_COUNTRY_GI' => __('Gibraltar', 'firebox'),
			'FPF_COUNTRY_GR' => __('Greece', 'firebox'),
			'FPF_COUNTRY_GL' => __('Greenland', 'firebox'),
			'FPF_COUNTRY_GD' => __('Grenada', 'firebox'),
			'FPF_COUNTRY_GP' => __('Guadeloupe', 'firebox'),
			'FPF_COUNTRY_GU' => __('Guam', 'firebox'),
			'FPF_COUNTRY_GT' => __('Guatemala', 'firebox'),
			'FPF_COUNTRY_GG' => __('Guernsey', 'firebox'),
			'FPF_COUNTRY_GN' => __('Guinea', 'firebox'),
			'FPF_COUNTRY_GW' => __('Guinea-Bissau', 'firebox'),
			'FPF_COUNTRY_GY' => __('Guyana', 'firebox'),
			'FPF_COUNTRY_HT' => __('Haiti', 'firebox'),
			'FPF_COUNTRY_HM' => __('Heard Island and McDonald Islands', 'firebox'),
			'FPF_COUNTRY_VA' => __('Holy See (Vatican City State)', 'firebox'),
			'FPF_COUNTRY_HN' => __('Honduras', 'firebox'),
			'FPF_COUNTRY_HK' => __('Hong Kong', 'firebox'),
			'FPF_COUNTRY_HU' => __('Hungary', 'firebox'),
			'FPF_COUNTRY_IS' => __('Iceland', 'firebox'),
			'FPF_COUNTRY_IN' => __('India', 'firebox'),
			'FPF_COUNTRY_ID' => __('Indonesia', 'firebox'),
			'FPF_COUNTRY_IR' => __('Iran, Islamic Republic of', 'firebox'),
			'FPF_COUNTRY_IQ' => __('Iraq', 'firebox'),
			'FPF_COUNTRY_IE' => __('Ireland', 'firebox'),
			'FPF_COUNTRY_IM' => __('Isle of Man', 'firebox'),
			'FPF_COUNTRY_IL' => __('Israel', 'firebox'),
			'FPF_COUNTRY_IT' => __('Italy', 'firebox'),
			'FPF_COUNTRY_JM' => __('Jamaica', 'firebox'),
			'FPF_COUNTRY_JP' => __('Japan', 'firebox'),
			'FPF_COUNTRY_JE' => __('Jersey', 'firebox'),
			'FPF_COUNTRY_JO' => __('Jordan', 'firebox'),
			'FPF_COUNTRY_KZ' => __('Kazakhstan', 'firebox'),
			'FPF_COUNTRY_KE' => __('Kenya', 'firebox'),
			'FPF_COUNTRY_KI' => __('Kiribati', 'firebox'),
			'FPF_COUNTRY_KP' => __('Korea, Democratic People\'s Republic of', 'firebox'),
			'FPF_COUNTRY_KR' => __('Korea, Republic of', 'firebox'),
			'FPF_COUNTRY_KW' => __('Kuwait', 'firebox'),
			'FPF_COUNTRY_KG' => __('Kyrgyzstan', 'firebox'),
			'FPF_COUNTRY_LA' => __('Lao People\'s Democratic Republic', 'firebox'),
			'FPF_COUNTRY_LV' => __('Latvia', 'firebox'),
			'FPF_COUNTRY_LB' => __('Lebanon', 'firebox'),
			'FPF_COUNTRY_LS' => __('Lesotho', 'firebox'),
			'FPF_COUNTRY_LR' => __('Liberia', 'firebox'),
			'FPF_COUNTRY_LY' => __('Libyan Arab Jamahiriya', 'firebox'),
			'FPF_COUNTRY_LI' => __('Liechtenstein', 'firebox'),
			'FPF_COUNTRY_LT' => __('Lithuania', 'firebox'),
			'FPF_COUNTRY_LU' => __('Luxembourg', 'firebox'),
			'FPF_COUNTRY_MO' => __('Macao', 'firebox'),
			'FPF_COUNTRY_MK' => __('Macedonia', 'firebox'),
			'FPF_COUNTRY_MG' => __('Madagascar', 'firebox'),
			'FPF_COUNTRY_MW' => __('Malawi', 'firebox'),
			'FPF_COUNTRY_MY' => __('Malaysia', 'firebox'),
			'FPF_COUNTRY_MV' => __('Maldives', 'firebox'),
			'FPF_COUNTRY_ML' => __('Mali', 'firebox'),
			'FPF_COUNTRY_MT' => __('Malta', 'firebox'),
			'FPF_COUNTRY_MH' => __('Marshall Islands', 'firebox'),
			'FPF_COUNTRY_MQ' => __('Martinique', 'firebox'),
			'FPF_COUNTRY_MR' => __('Mauritania', 'firebox'),
			'FPF_COUNTRY_MU' => __('Mauritius', 'firebox'),
			'FPF_COUNTRY_YT' => __('Mayotte', 'firebox'),
			'FPF_COUNTRY_MX' => __('Mexico', 'firebox'),
			'FPF_COUNTRY_FM' => __('Federated States of Micronesia', 'firebox'),
			'FPF_COUNTRY_MD' => __('Moldova, Republic of', 'firebox'),
			'FPF_COUNTRY_MC' => __('Monaco', 'firebox'),
			'FPF_COUNTRY_MN' => __('Mongolia', 'firebox'),
			'FPF_COUNTRY_ME' => __('Montenegro', 'firebox'),
			'FPF_COUNTRY_MS' => __('Montserrat', 'firebox'),
			'FPF_COUNTRY_MA' => __('Morocco', 'firebox'),
			'FPF_COUNTRY_MZ' => __('Mozambique', 'firebox'),
			'FPF_COUNTRY_MM' => __('Myanmar', 'firebox'),
			'FPF_COUNTRY_NA' => __('Namibia', 'firebox'),
			'FPF_COUNTRY_NR' => __('Nauru', 'firebox'),
			'FPF_COUNTRY_NP' => __('Nepal', 'firebox'),
			'FPF_COUNTRY_NL' => __('Netherlands', 'firebox'),
			'FPF_COUNTRY_AN' => __('Netherlands Antilles', 'firebox'),
			'FPF_COUNTRY_NC' => __('New Caledonia', 'firebox'),
			'FPF_COUNTRY_NZ' => __('New Zealand', 'firebox'),
			'FPF_COUNTRY_NI' => __('Nicaragua', 'firebox'),
			'FPF_COUNTRY_NE' => __('Niger', 'firebox'),
			'FPF_COUNTRY_NG' => __('Nigeria', 'firebox'),
			'FPF_COUNTRY_NU' => __('Niue', 'firebox'),
			'FPF_COUNTRY_NF' => __('Norfolk Island', 'firebox'),
			'FPF_COUNTRY_MP' => __('Northern Mariana Islands', 'firebox'),
			'FPF_COUNTRY_NO' => __('Norway', 'firebox'),
			'FPF_COUNTRY_OM' => __('Oman', 'firebox'),
			'FPF_COUNTRY_PK' => __('Pakistan', 'firebox'),
			'FPF_COUNTRY_PW' => __('Palau', 'firebox'),
			'FPF_COUNTRY_PS' => __('Palestinian Territory', 'firebox'),
			'FPF_COUNTRY_PA' => __('Panama', 'firebox'),
			'FPF_COUNTRY_PG' => __('Papua New Guinea', 'firebox'),
			'FPF_COUNTRY_PY' => __('Paraguay', 'firebox'),
			'FPF_COUNTRY_PE' => __('Peru', 'firebox'),
			'FPF_COUNTRY_PH' => __('Philippines', 'firebox'),
			'FPF_COUNTRY_PN' => __('Pitcairn', 'firebox'),
			'FPF_COUNTRY_PL' => __('Poland', 'firebox'),
			'FPF_COUNTRY_PT' => __('Portugal', 'firebox'),
			'FPF_COUNTRY_PR' => __('Puerto Rico', 'firebox'),
			'FPF_COUNTRY_QA' => __('Qatar', 'firebox'),
			'FPF_COUNTRY_RE' => __('Reunion', 'firebox'),
			'FPF_COUNTRY_RO' => __('Romania', 'firebox'),
			'FPF_COUNTRY_RU' => __('Russian Federation', 'firebox'),
			'FPF_COUNTRY_RW' => __('Rwanda', 'firebox'),
			'FPF_COUNTRY_SH' => __('Saint Helena', 'firebox'),
			'FPF_COUNTRY_KN' => __('Saint Kitts and Nevis', 'firebox'),
			'FPF_COUNTRY_LC' => __('Saint Lucia', 'firebox'),
			'FPF_COUNTRY_PM' => __('Saint Pierre and Miquelon', 'firebox'),
			'FPF_COUNTRY_VC' => __('Saint Vincent and the Grenadines', 'firebox'),
			'FPF_COUNTRY_WS' => __('Samoa', 'firebox'),
			'FPF_COUNTRY_SM' => __('San Marino', 'firebox'),
			'FPF_COUNTRY_ST' => __('Sao Tome and Principe', 'firebox'),
			'FPF_COUNTRY_SA' => __('Saudi Arabia', 'firebox'),
			'FPF_COUNTRY_SN' => __('Senegal', 'firebox'),
			'FPF_COUNTRY_RS' => __('Serbia', 'firebox'),
			'FPF_COUNTRY_SC' => __('Seychelles', 'firebox'),
			'FPF_COUNTRY_SL' => __('Sierra Leone', 'firebox'),
			'FPF_COUNTRY_SG' => __('Singapore', 'firebox'),
			'FPF_COUNTRY_SK' => __('Slovakia', 'firebox'),
			'FPF_COUNTRY_SI' => __('Slovenia', 'firebox'),
			'FPF_COUNTRY_SB' => __('Solomon Islands', 'firebox'),
			'FPF_COUNTRY_SO' => __('Somalia', 'firebox'),
			'FPF_COUNTRY_ZA' => __('South Africa', 'firebox'),
			'FPF_COUNTRY_GS' => __('South Georgia and the South Sandwich Islands', 'firebox'),
			'FPF_COUNTRY_ES' => __('Spain', 'firebox'),
			'FPF_COUNTRY_LK' => __('Sri Lanka', 'firebox'),
			'FPF_COUNTRY_SD' => __('Sudan', 'firebox'),
			'FPF_COUNTRY_SS' => __('South Sudan', 'firebox'),
			'FPF_COUNTRY_SR' => __('Suriname', 'firebox'),
			'FPF_COUNTRY_SJ' => __('Svalbard and Jan Mayen', 'firebox'),
			'FPF_COUNTRY_SZ' => __('Swaziland', 'firebox'),
			'FPF_COUNTRY_SE' => __('Sweden', 'firebox'),
			'FPF_COUNTRY_CH' => __('Switzerland', 'firebox'),
			'FPF_COUNTRY_SY' => __('Syrian Arab Republic', 'firebox'),
			'FPF_COUNTRY_TW' => __('Taiwan', 'firebox'),
			'FPF_COUNTRY_TJ' => __('Tajikistan', 'firebox'),
			'FPF_COUNTRY_TZ' => __('Tanzania, United Republic of', 'firebox'),
			'FPF_COUNTRY_TH' => __('Thailand', 'firebox'),
			'FPF_COUNTRY_TL' => __('Timor-Leste', 'firebox'),
			'FPF_COUNTRY_TG' => __('Togo', 'firebox'),
			'FPF_COUNTRY_TK' => __('Tokelau', 'firebox'),
			'FPF_COUNTRY_TO' => __('Tonga', 'firebox'),
			'FPF_COUNTRY_TT' => __('Trinidad and Tobago', 'firebox'),
			'FPF_COUNTRY_TN' => __('Tunisia', 'firebox'),
			'FPF_COUNTRY_TR' => __('Turkey', 'firebox'),
			'FPF_COUNTRY_TM' => __('Turkmenistan', 'firebox'),
			'FPF_COUNTRY_TC' => __('Turks and Caicos Islands', 'firebox'),
			'FPF_COUNTRY_TV' => __('Tuvalu', 'firebox'),
			'FPF_COUNTRY_UG' => __('Uganda', 'firebox'),
			'FPF_COUNTRY_UA' => __('Ukraine', 'firebox'),
			'FPF_COUNTRY_AE' => __('United Arab Emirates', 'firebox'),
			'FPF_COUNTRY_GB' => __('United Kingdom', 'firebox'),
			'FPF_COUNTRY_US' => __('United States', 'firebox'),
			'FPF_COUNTRY_UM' => __('United States Minor Outlying Islands', 'firebox'),
			'FPF_COUNTRY_UY' => __('Uruguay', 'firebox'),
			'FPF_COUNTRY_UZ' => __('Uzbekistan', 'firebox'),
			'FPF_COUNTRY_VU' => __('Vanuatu', 'firebox'),
			'FPF_COUNTRY_VE' => __('Venezuela', 'firebox'),
			'FPF_COUNTRY_VN' => __('Vietnam', 'firebox'),
			'FPF_COUNTRY_VG' => __('Virgin Islands, British', 'firebox'),
			'FPF_COUNTRY_VI' => __('Virgin Islands, U.S.', 'firebox'),
			'FPF_COUNTRY_WF' => __('Wallis and Futuna', 'firebox'),
			'FPF_COUNTRY_EH' => __('Western Sahara', 'firebox'),
			'FPF_COUNTRY_YE' => __('Yemen', 'firebox'),
			'FPF_COUNTRY_ZM' => __('Zambia', 'firebox'),
			'FPF_COUNTRY_ZW' => __('Zimbabwe', 'firebox'),
			'FPF_WPML_LANGUAGE' => __('WPML Language', 'firebox'),
			'FPF_GENERAL' => __('General', 'firebox'),
			'FPF_AS_EXPORTED' => __('As Exported', 'firebox'),
			'FPF_SELECT_IMPORT_FILE' => __('Select Import File', 'firebox'),
			'FPF_DUPLICATE' => __('Duplicate', 'firebox'),
			'FPF_ADD_ITEM' => __('Add Item', 'firebox'),
			'FPF_EVENT' => __('Event', 'firebox'),
			'FPF_EVENTS' => __('Events', 'firebox'),
			'FPF_OPEN' => __('Open', 'firebox'),
			'FPF_PLEASE_SELECT_A_FILE_TO_UPLOAD' => __('Please select a file to upload.', 'firebox'),
			'FPF_PLEASE_CHOOSE_A_VALID_FILE' => __('Please choose a valid file.', 'firebox'),
			'FPF_FILE_EMPTY' => __('File is empty!', 'firebox'),
			'FPF_ITEMS_SAVED' => __('Items saved!', 'firebox'),
			'FPF_SETTINGS_SAVED' => __('Settings saved!', 'firebox'),
			'FPF_MY_FAVORITES' => __('My Favorites', 'firebox'),
			'FPF_OTHER' => __('Other', 'firebox'),
			'FPF_PASSWORD' => __('Password', 'firebox'),
			'FPF_REMEMBER_ME' => __('Remember Me', 'firebox'),
			'FPF_LOG_IN' => __('Log in', 'firebox'),
			'FPF_LOG_OUT' => __('Log out', 'firebox'),
			'FPF_GUEST' => __('Guest', 'firebox'),
			'FPF_GEOLOCATION_SERVICES' => __('Geolocation Services', 'firebox'),
			'FPF_LICENSE' => __('License', 'firebox'),
			'FPF_GEOIP_LICENSE_KEY_DESC' => __('Get your free License Key to download the latest MaxMind GeoLite2 Database.', 'firebox'),
			'FPF_GEOLOCATION' => __('Geolocation', 'firebox'),
			'FPF_GEOIP_GEOLOCATION_SERVICES' => __('Geolocation Services', 'firebox'),
			'FPF_GEOIP_LICENSE_KEY_GET' => __('Get a free License Key', 'firebox'),
			'FPF_GEOIP_GEOLOCATION_SERVICES_HEADING_DESC' => __('Geolocation services provide features such as (finding out the country of an IP address as well as retrieving the country data for Analytics) for FireBox. Without it the geolocation features will not be available. This includes GeoLite2 data created by MaxMind <a href="http://www.maxmind.com">http://www.maxmind.com</a>.', 'firebox'),
			'FPF_GEOIP_UPDATE_DB' => __('Update Database', 'firebox'),
			'FPF_GEOIP_UPDATE_DB_DESC' => __('Update the GeoLite2 database from MaxMind servers. This might take several seconds to finish. Please be patient.', 'firebox'),
			'FPF_GEOIP_DATABASE_UPDATED' => __('GeoIP database successfully updated!', 'firebox'),
			'FPF_GEOIP_LICENSE_KEY_EMPTY' => __('Please enter a valid License Key.', 'firebox'),
			'FPF_GEOIP_ERR_MAXMIND_GENERIC' => __('A connection error occurred. Please retry updating the GeoLite2 Country database in 24 hours.', 'firebox'),
			'FPF_GEOIP_ERR_EMPTYDOWNLOAD' => __('Downloading the GeoLite2 Country database failed: empty file retrieved from server. Please contact your host.', 'firebox'),
			'FPF_GEOIP_ERR_UNAUTHORIZED' => __('You have supplied an invalid MaxMind license key.', 'firebox'),
			'FPF_GEOIP_ERR_CANTWRITE' => __('Insufficient rights while trying to save GeoIP database.', 'firebox'),
			'FPF_GEOIP_ERR_INVALIDDB' => __('Downloaded database seems to be invalid. Please retry updating the GeoLite2 Country database in 24 hours.', 'firebox'),
			'FPF_LAST_UPDATED' => __('Last Updated', 'firebox'),
			'FPF_GEOIP_LAST_UPDATED_DESC' => __('Indicates the last datetime the database updated.', 'firebox'),
			'FPF_GEOIP_LOOKUP' => __('Lookup IP Address', 'firebox'),
			'FPF_GEOIP_LOOKUP_DESC' => __('Test drive the Geolocation services by looking up an IP address.', 'firebox'),
			'FPF_LOOKUP' => __('Lookup', 'firebox'),
			'FPF_LICENSE_KEY' => __('License Key', 'firebox'),
			'FPF_LICENSE_KEY_DESC' => __('To find your License Key log-in to <a href="https://www.fireplugins.com" target="_blank">FirePlugins</a> and then go to your downloads section.', 'firebox'),
			'FPF_FIND_LICENSE_KEY' => __('Find License Key', 'firebox'),
			'FPF_DATA' => __('Data', 'firebox'),
			'FPF_GEOIP_MAINTENANCE' => __('GeoIP Database Maintenance', 'firebox'),
			'FPF_GEOIP_MAINTENANCE_DESC' => __('FireBox finds the country of your visitors\' IP addresses using the MaxMind GeoLite2 Country database. You are advised to update it at least once per month. On most servers you can perform the update by clicking the \'Update Database\' button below.', 'firebox'),
			'FPF_GEOIP_MAINTENANCE_WITHOUT_BTN_MENTION_DESC' => __('FireBox finds the country of your visitors\' IP addresses using the MaxMind GeoLite2 Country database. You are advised to update it at least once per month.', 'firebox'),
			'FPF_UPGRADE_TO_PRO' => __('Upgrade to Pro', 'firebox'),
			'FPF_UNLOCK_PRO_FEATURE' => __('Unlock Pro Feature', 'firebox'),
			'FPF_UPGRADE' => __('Upgrade', 'firebox'),
			'FPF_VIEW_PLANS' => __('View Plans', 'firebox'),
			'FPF_UNLOCK' => __('Unlock', 'firebox'),
			'FPF_FREE' => __('Free', 'firebox'),
			'FPF_PRO' => __('Pro', 'firebox'),
			'FPF_PRO_MODAL_IS_PRO_FEATURE' => __('<em class="pro-feature-name"></em> is a PRO Feature', 'firebox'),
			'FPF_PRO_MODAL_WERE_SORRY' => __('We\'re sorry, <em class="pro-feature-name"></em> is not available on your plan. Please upgrade to the PRO plan to unlock all these awesome features.', 'firebox'),
			'FPF_PRO_MODAL_UPGRADE_TO_PRO_VERSION' => __('Awesome! Only one step left. Click on the button below to complete the upgrade to the Pro version.', 'firebox'),
			/* translators: %s: product name */
			'FPF_PRO_MODAL_PERCENTAGE_OFF' => __('<strong>Bonus</strong>: %s Lite users get <strong class="percentage">20%%</strong> off regular price, automatically applied at checkout.', 'firebox'),
			/* translators: %s: Link to contact form */
			'FPF_PRO_MODAL_PRESALES_QUESTIONS' => __('Pre-Sales questions? <a target="_blank" href="%s">Ask here</a>', 'firebox'),
			/* translators: %s: Link to Upgrade to Pro */
			'FPF_PRO_MODAL_UNLOCK_PRO_FEATURES' => __('Already purchased Pro? Learn how to <a target="_blank" href="%s">Unlock Pro Features</a>', 'firebox'),
			'FPF_CLEAR' => __('Clear', 'firebox'),
			'FPF_SAVE' => __('Save', 'firebox'),
			'FPF_TOP' => __('Top', 'firebox'),
			'FPF_RIGHT' => __('Right', 'firebox'),
			'FPF_BOTTOM' => __('Bottom', 'firebox'),
			'FPF_LEFT' => __('Left', 'firebox'),
			'FPF_TOP_LEFT' => __('Top Left', 'firebox'),
			'FPF_TOP_RIGHT' => __('Top Right', 'firebox'),
			'FPF_BOTTOM_RIGHT' => __('Bottom Right', 'firebox'),
			'FPF_BOTTOM_LEFT' => __('Bottom Left', 'firebox'),
			'FPF_ENTER_VALID_IP_ADDRESS' => __('Please enter a valid IP address', 'firebox'),
			'FPF_LOADING' => __('Loading...', 'firebox'),
			'FPF_LOADING_ANALYTICS' => __('Loading Analytics...', 'firebox'),
			'FPF_INVALID_IP_ADDRESS' => __('Invalid IP Address', 'firebox'),
			'FPF_DOWNLOADING_UPDATES_PLEASE_WAIT' => __('Downloading Updates. Please wait...', 'firebox'),
			'FPF_DATABASE_UPDATED' => __('Database updated!', 'firebox'),
			'FPF_ANALYTICS' => __('Analytics', 'firebox'),
			'FPF_DATE' => __('Date', 'firebox'),
			'FPF_DATES' => __('Dates', 'firebox'),
			'FPF_CUSTOM' => __('Custom', 'firebox'),
			'FPF_CANCEL' => __('Cancel', 'firebox'),
			'FPF_APPLY' => __('Apply', 'firebox'),
			'FPF_START_DATE' => __('Start Date', 'firebox'),
			'FPF_END_DATE' => __('End Date', 'firebox'),
			'FPF_UNLOCK_MORE_FEATURES_WITH_PRO_READ_MORE' => __('Unlock more features by going Pro! Click to upgrade to the Pro version and increase your conversions!', 'firebox'),
			'FPF_MONTH' => __('Month', 'firebox'),
			'FPF_REFERRER' => __('Referrer', 'firebox'),
			'FPF_REFERRERS' => __('Referrers', 'firebox'),
			'FPF_1_YEAR' => __('1 Year', 'firebox'),
			'FPF_2_YEARS' => __('2 Years', 'firebox'),
			'FPF_5_YEARS' => __('5 Years', 'firebox'),
			'FPF_KEEP_FOREVER' => __('Keep forever', 'firebox'),
			/* translators: %s: Feature Name */
			'FPF_FEATURE_IMAGE_UPGRADE_PRO_MSG1' => __('<strong>%s</strong> is a Pro feature!', 'firebox'),
			'FPF_FEATURE_IMAGE_UPGRADE_PRO_MSG2' => __('Upgrade to Pro to unlock this feature.', 'firebox'),
			'FPF_ADD_NEW' => __('Add New', 'firebox'),
			/* translators: %s: Taxonomy */
			'FPF_CHOOSE_FROM_MOST_USED_%S' => __('Choose from most used %s', 'firebox'),
			/* translators: %s: Singular */
			'FPF_NEW_%S_NAME' => __('New %s Name', 'firebox'),
			/* translators: %s: Singular */
			'FPF_ADD_NEW_%S' => __('Add New %s', 'firebox'),
			/* translators: %s: Singular */
			'FPF_EDIT_%S' => __('Edit %s', 'firebox'),
			/* translators: %s: Singular */
			'FPF_NEW_%S' => __('New %s', 'firebox'),
			/* translators: %s: Singular */
			'FPF_VIEW_%S' => __('View %s', 'firebox'),
			'FPF_SEARCH' => __('Search', 'firebox'),
			/* translators: %s: Singular */
			'FPF_SEARCH_%S' => __('Search %s', 'firebox'),
			/* translators: %s: Singular */
			'FPF_ALL_%S' => __('All %s', 'firebox'),
			/* translators: %s: Plurar */
			'FPF_NO_%S_FOUND' => __('No %s found', 'firebox'),
			/* translators: %s: Plurar */
			'FPF_NO_%S_FOUND_IN_TRASH' => __('No %s found in Trash', 'firebox'),
			/* translators: %s: Singular */
			'FPF_UPDATE_%S' => __('Update %s', 'firebox'),
			/* translators: %s: Singular */
			'FPF_PARENT_%S' => __('Parent %s', 'firebox'),
			/* translators: %s: Singular */
			'FPF_PARENT_%S:' => __('Parent %s:', 'firebox'),
			'FPF_RESET' => __('Reset', 'firebox'),
			'FPF_RESET_COLOR' => __('Reset color', 'firebox'),
			'FPF_DEFAULT' => __('Default', 'firebox'),
			'FPF_SELECT_COLOR' => __('Select color', 'firebox'),
			'FPF_SELECT_DEFAULT_COLOR' => __('Select default color', 'firebox'),
			'FPF_COLOR_VALUE' => __('Color value', 'firebox'),
			/* translators: %s: Plugin Name, Required Version */
			'FPF_PHP_VERSION_FAIL' => __('%1$s requires PHP version %2$s+, please upgrade to the mentioned PHP version in order for the plugin to work.', 'firebox'),
			'FPF_KEEP_DATA_ON_UNINSTALL' => __('Keep data on uninstall', 'firebox'),
			'FPF_KEEP_DATA_ON_UNINSTALL_DESC' => __('<strong>Enable</strong> to preserve the data when you uninstall the plugin.<br><strong>Disable</strong> to remove the data when you uninstall the plugin.', 'firebox'),
			'FPF_HI' => __('Hi', 'firebox'),
			'FPF_MADE_WITH_LOVE_BY_FP' => __('Made with ♥️ by FireBox', 'firebox'),
			'FPF_ROADMAP' => __('Roadmap', 'firebox'),
			'FPF_WRITE_REVIEW' => __('Leave a review on WordPress Plugin Directory', 'firebox'),
			/* translators: %s: Plugin Name */
			'FPF_LIKE_PLUGIN' => __('Like %s?', 'firebox'),
			'FPF_GO_TO_FP_SITE' => __('Go to FireBox Site', 'firebox'),
			'FPF_MEDIA' => __('Media', 'firebox'),
			'FPF_UNINSTALL' => __('Uninstall', 'firebox'),
			'FPF_SETTINGS_UNINSTALL_DESC' => __('Select whether to keep or delete your settings on plugin uninstall.', 'firebox'),
			'FPF_SETTINGS_LICENSE_DESC' => __('Activate your license to get automatic updates.', 'firebox'),
			'FPF_DIMENSIONS_FIELD_LINK_VALUES_TITLE' => __('Enable to automatically fill in all values.', 'firebox'),
			'FPF_UNITS_TITLE' => __('Select the unit for this field.', 'firebox'),
			'FPF_FORGOT_YOUR_PASSWORD' => __('Forgot your password?', 'firebox'),
			'FPF_STATUS' => __('Status', 'firebox'),
			'FPF_START_FROM_SCRATCH' => __('Start from scratch', 'firebox'),
			'FPF_PREVIEW_TEMPLATE' => __('Preview template', 'firebox'),
			'FPF_LIBRARY_SAVE_TEMPLATE_FAVORITES' => __('Save template to favorites', 'firebox'),
			'FPF_REFRESH_TEMPLATES' => __('Refresh Templates', 'firebox'),
			'FPF_NO_RESULTS_FOUND' => __('No Results Found', 'firebox'),
			'FPF_OOPS_NO_MATCHES_FOUND' => __('<strong>Ooops!</strong> No matches found.', 'firebox'),
			'FPF_GEO_DB_HAS_NOT_BEEN_UPDATED' => __('Database has not been updated.', 'firebox'),
			'FPF_REMOVE_ITEM' => __('Remove item', 'firebox'),
			'FPF_MOVE_ITEM' => __('Reorder item', 'firebox'),
			'FPF_ACTIVATE_LICENSE' => __('Activate License', 'firebox'),
			'FPF_DEACTIVATE_LICENSE' => __('Deactivate License', 'firebox'),
			/* translators: %s: New Version */
			'FPF_X_VERSION_IS_AVAILABLE' => __('%s is now available', 'firebox'),
			/* translators: %s: Plugin Name, Release Date */
			'FPF_AN_UPDATED_VERSION_IS_AVAILABLE' => __('A new and improved version of %1$s released on %2$s. Update Now!', 'firebox'),
			'FPF_UPDATE_NOW' => __('Update now', 'firebox'),
			'FPF_PRO_TEMPLATES' => __('Pro Templates', 'firebox'),
			'FPF_TEMPLATES_CANNOT_BE_RETRIEVED' => __('Cannot retrieve templates. Please try again.', 'firebox'),
			'FPF_ENJOY' => __('Enjoy', 'firebox'),
			'FPF_NO_LICENSE_NEEDED' => __('No license needed', 'firebox'),
			'FPF_VALID' => __('valid', 'firebox'),
			'FPF_INVALID' => __('invalid', 'firebox'),
			/* translators: %s: Color Name, License Status */
			'FPF_YOUR_LICENSE_KEY_IS_STATUS' => __('Your license key is <strong class="fpf-%1$s-color">%2$s</strong>.', 'firebox'),
			'FPF_PLEASE_ENTER_A_VALID_LICENSE_KEY_TO_RECEIVE_UPDATES' => __('Please enter a valid license key to receive updates.', 'firebox'),
			'FPF_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ITEM' => __('Are you sure you want to delete this item?', 'firebox'),
			'FPF_STAR' => __('Star', 'firebox'),
			'FPF_CB_NEW_CONDITION_GROUP' => 'New Condition Set', 'firebox',
			'FPF_CB_SELECT_CONDITION_GET_STARTED' => 'Select a condition to get started.', 'firebox',
			'FPF_CB_TRASH_CONDITION' => 'Trash Condition', 'firebox',
			'FPF_CB_TRASH_CONDITION_GROUP' => 'Trash Condition Group', 'firebox',
			'FPF_CB_ADD_CONDITION' => 'Add Condition', 'firebox',
			'FPF_CB_SHOW_WHEN' => 'Display when', 'firebox',
			'FPF_CB_OF_THE_CONDITIONS_MATCH' => 'of the conditions below are met', 'firebox',
			'FPF_IS' => 'Is', 'firebox',
			'FPF_IS_NOT' => 'Is not', 'firebox',
			'FPF_IS_EMPTY' => 'Is empty', 'firebox',
			'FPF_IS_NOT_EMPTY' => 'Is not empty', 'firebox',
			'FPF_IS_BETWEEN' => 'Is between', 'firebox',
			'FPF_IS_NOT_BETWEEN' => 'Is not between', 'firebox',
			'FPF_DISPLAY_CONDITIONS_LOADING' => 'Loading Display Conditions...', 'firebox',
			'FPF_CB_TOGGLE_RULE_GROUP_STATUS' => 'Enable or disable Condition Set', 'firebox',
			'FPF_CB_TOGGLE_RULE_STATUS' => 'Enable or disable Condition', 'firebox',
			/* translators: %s: Date Time */
			'FPF_DISPLAY_CONDITIONS_HINT_DATE' => __('Your server\'s date time is %s.', 'firebox'),
			/* translators: %s: Time */
			'FPF_DISPLAY_CONDITIONS_HINT_TIME' => __('Your server\'s time is %s.', 'firebox'),
			/* translators: %s: Day */
			'FPF_DISPLAY_CONDITIONS_HINT_DAY' => __('Today is %s.', 'firebox'),
			/* translators: %s: Month */
			'FPF_DISPLAY_CONDITIONS_HINT_MONTH' => __('The current month is %s.', 'firebox'),
			/* translators: %s: User Name / Email */
			'FPF_DISPLAY_CONDITIONS_HINT_USERID' => __('The user you\'re logged-in is %s.', 'firebox'),
			/* translators: %s: User Group */
			'FPF_DISPLAY_CONDITIONS_HINT_USERGROUP' => __('The User Groups assigned to the account you\'re logged-in are: %s.', 'firebox'),
			/* translators: %s: Device */
			'FPF_DISPLAY_CONDITIONS_HINT_DEVICE' => __('The type of the device you\'re using is %s.', 'firebox'),
			/* translators: %s: Browser */
			'FPF_DISPLAY_CONDITIONS_HINT_BROWSER' => __('The browser you\'re using is %s.', 'firebox'),
			/* translators: %s: Operating System */
			'FPF_DISPLAY_CONDITIONS_HINT_OS' => __('The operating system you\'re using is %s.', 'firebox'),
			/* translators: %s: IP Address, Geolocation Condition, Value */
			'FPF_DISPLAY_CONDITIONS_HINT_GEO' => __('Based on your IP address (%1$s), the %2$s you\'re physically located in, is %3$s.', 'firebox'),
			/* translators: %s: IP Address */
			'FPF_DISPLAY_CONDITIONS_HINT_IP' => __('Your IP Address is %s.', 'firebox'),
			/* translators: %s: IP Address */
			'FPF_DISPLAY_CONDITIONS_HINT_GEO_ERROR' => __('Based on your IP address (%s), we couldn\'t determine where you\'re physically located in.', 'firebox'),
			'FPF_DISPLAY_CONDITIONS_HINT_REFERRER' => '',
			'FPF_DISPLAY_CONDITIONS_HINT_PHP' => '',
			'FPF_AND' => __('and', 'firebox'),
			'FPF_LOADING_FILTERS' => __('Loading filters...', 'firebox'),
			'FPF_FILTERS' => __('Filters', 'firebox'),
			'FPF_CLEAR_ALL' => __('Clear all', 'firebox'),
			'FPF_OPEN_SIDEBAR' => __('Open sidebar', 'firebox'),
			'FPF_CLOSE_SIDEBAR' => __('Close sidebar', 'firebox'),
			'FPF_SORT_BY' => __('Sort by', 'firebox'),
			'FPF_POPULAR' => __('Popular', 'firebox'),
			'FPF_TRENDING' => __('Trending', 'firebox'),
			'FPF_SOLUTION' => __('Solution', 'firebox'),
			'FPF_SOLUTIONS' => __('Solutions', 'firebox'),
			'FPF_INSERT' => __('Insert', 'firebox'),
			'FPF_INSERT_TEMPLATE_NOW' => __('Insert template now', 'firebox'),
			'FPF_COMPATIBILITY' => __('Compatibility', 'firebox'),
			'FPF_REQUIREMENTS' => __('Requirements', 'firebox'),
			'FPF_SHOWING_RESULTS_FOR' => __('Showing <span class="fpf-showing-results-counter"></span> results for', 'firebox'),
			'FPF_REQUEST_TEMPLATE' => __('Request template', 'firebox'),
			'FPF_TEMPLATE_INFORMATION' => __('Click to view information about this template', 'firebox'),
			'FPF_UPDATE_WORDPRESS' => __('Update WordPress', 'firebox'),
			'FPF_UPDATE_WORDPRESS_TO_INSERT_TEMPLATE' => __('Update WordPress to insert this template', 'firebox'),
			'FPF_UPDATE_PLUGIN' => __('Update Plugin', 'firebox'),
			/* translators: %s: Plugin Name */
			'FPF_UPDATE_PLUGIN_X_TO_INSERT_TEMPLATE' => __('Update %s to insert this template', 'firebox'),
			'FPF_INSTALL_PLUGIN' => __('Install Plugin', 'firebox'),
			/* translators: %s: Plugin Name */
			'FPF_INSTALL_PLUGIN_X' => __('Install %s', 'firebox'),
			/* translators: %s: Plugin Name */
			'FPF_INSTALL_PLUGIN_X_TO_INSERT_TEMPLATE' => __('Install %s to insert this template', 'firebox'),
			'FPF_ACTIVATE_PLUGIN' => __('Activate Plugin', 'firebox'),
			/* translators: %s: Plugin Name */
			'FPF_ACTIVATE_PLUGIN_X' => __('Activate %s', 'firebox'),
			/* translators: %s: Plugin Name */
			'FPF_ACTIVATE_PLUGIN_X_TO_INSERT_TEMPLATE' => __('Activate %s to insert this template', 'firebox'),
			'FPF_MULTIPLE_ISSUES_DETECTED' => __('Multiple issues detected', 'firebox'),
			'FPF_NO_LICENSE_KEY_DETECTED' => __('No license key detected', 'firebox'),
			'FPF_UPGRADE_TO_UC_PRO' => __('Upgrade to PRO', 'firebox'),
			'FPF_CATEGORY' => __('Category', 'firebox'),
			'FPF_WP' => __('WP', 'firebox'),
			'FPF_WORDPRESS' => __('WordPress', 'firebox'),
			'FPF_FEATURED' => __('Featured', 'firebox'),
			'FPF_NEWEST' => __('Newest', 'firebox'),
			'FPF_INVALID_LICENSE_KEY_ENTERED' => __('License key is invalid/expired.', 'firebox'),
			'FPF_UPGRADE_TO_PRO_TO_UNLOCK_TEMPLATE' => __('Upgrade to PRO unlock this template', 'firebox'),
			'FPF_INSERT_TEMPLATE' => __('Insert template', 'firebox'),
			'FPF_DETECTED' => __('Detected', 'firebox'),
			'FPF_CHECK' => __('Check', 'firebox'),
			'FPF_LITE' => __('Lite', 'firebox'),
			'FPF_SET_LICENSE_KEY' => __('Set License Key', 'firebox'),
			'FPF_INVALID_EXPIRED_LICENSE_KEY' => __('Invalid/Expired License Key', 'firebox'),
			'FPF_ERROR_OCCURRED_PLEASE_TRY_AGAIN' => __('An error occurred, please try again.', 'firebox'),
			'FPF_INVALID_LICENSE' => __('Invalid license.', 'firebox'),
			/* translators: %s: Expiration Date */
			'FPF_LICENSE_KEY_EXPIRED_ON' => __('Your license key expired on %s.', 'firebox'),
			'FPF_LICENSE_KEY_REVOKED' => __('Your license key has been disabled.', 'firebox'),
			'FPF_LICENSE_KEY_NOT_VALID_FOR_THIS_URL' => __('Your license is not active for this URL.', 'firebox'),
			/* translators: %s: Plugin Name */
			'FPF_LICENSE_MISMATCH' => __('This appears to be an invalid license key for %s.', 'firebox'),
			'FPF_LICENSE_LIMIT_REACHED' => __('Your license key has reached its activation limit.', 'firebox'),
			/* translators: %s: Email */
			'FPF_ERROR_USER_ALREADY_EXIST' => __('%s is already a member.', 'firebox'),
			/* translators: %s: Email */
			'FPF_ERROR_INVALID_EMAIL_ADDRESS' => __('%s looks fake or invalid, please enter a real email address.', 'firebox'),
			'FPF_SUBMISSIONS' => __('Submissions', 'firebox'),
			'FPF_PUBLISH' => __('Publish', 'firebox'),
			'FPF_UNPUBLISH' => __('Unpublish', 'firebox'),
			'FPF_UNPUBLISHED' => __('Unpublished', 'firebox'),
			'FPF_TRASH' => __('Trash', 'firebox'),
			'FPF_VISITOR_ID' => __('Visitor ID', 'firebox'),
			'FPF_EMAIL' => __('Email', 'firebox'),
			'FPF_USER_NAME' => __('User Name', 'firebox'),
			'FPF_USER' => __('User', 'firebox'),
			'FPF_PUBLISHED' => __('Published', 'firebox'),
			'FPF_CREATED' => __('Created', 'firebox'),
			'FPF_TRASHED' => __('Trashed', 'firebox'),
			'FPF_CANNOT_VALIDATE_REQUEST' => __('Cannot validate request.', 'firebox'),
			'FPF_DELETE' => __('Delete', 'firebox'),
			'FPF_BACK' => __('Back', 'firebox'),
			'FPF_SELECT_A_LIST' => __('Select a list', 'firebox'),
			/* translators: %s: 3rd-party CRM Name */
			'FPF_API_KEY_INVALID_OR_INTEGRATION_ACCOUNT_HAS_NO_LISTS' => __('Either your API Key is invalid or your %s account does not have any lists created.', 'firebox'),
			/* translators: %s: 3rd-party CRM Name */
			'FPF_INTEGRATION_ACCOUNT_HAS_NO_LISTS' => __('Your %s account does not have any lists created.', 'firebox'),
			'FPF_PLEASE_ENTER_AN_API_KEY' => __('Please enter an API Key.', 'firebox'),
			'FPF_NO_INTEGRATION_SUPPLIED' => __('No Integration was supplied.', 'firebox'),
			'FPF_CANNOT_VERIFY_REQUEST' => __('Cannot verify request.', 'firebox'),
			'FPF_NO_SUCH_INTEGRATION_EXISTS' => __('No such integration exists.', 'firebox'),
			'FPF_INTEGRATION_INVALID' => __('Integration is invalid.', 'firebox'),
			'FPF_WHERE_TO_FIND_API_KEY' => __('Where can I find my API Key?', 'firebox'),
			'FPF_DAY_OF_WEEK' => __('Day of Week', 'firebox'),
			'FPF_TIME' => __('Time', 'firebox'),
			'FPF_SELECT_CONDITION' => __('Select Condition', 'firebox'),
			'FPF_REGION' => __('Region', 'firebox'),
			'FPF_BROWSERS' => __('Browsers', 'firebox'),
			'FPF_INTEGRATIONS' => __('Integrations', 'firebox'),
			'FPF_DATETIME' => __('Date/Time', 'firebox'),
			'FPF_TECHNOLOGY' => __('Technology', 'firebox'),
			'FPF_TYPE_A_CONDITION' => __('Type a condition...', 'firebox'),
			'FPF_ASSIGN_DEVICES_NOTE' => __('Keep in mind that device detection is not always 100% accurate. Users can setup their browser to mimic other devices', 'firebox'),
			'FPF_FIREBOX_VIEWED_ANOTHER_CAMPAIGN' => __('FireBox - Viewed Another Campaign', 'firebox'),
			'FPF_MENU' => __('Menu', 'firebox'),
			'FPF_CONDITION' => __('Condition', 'firebox'),
			'FPF_FIREBOX_SUBMITTED_FORM' => __('FireBox - Submitted Form', 'firebox'),
			'FPF_NEW_RETURNING_VISITOR' => __('New/Returning Visitor', 'firebox'),
			'FPF_WOOCOMMERCE' => __('WooCommerce', 'firebox'),
			'FPF_WOOCOMMERCE_PRODUCTS_IN_CART' => __('WooCommerce Products in Cart', 'firebox'),
			'FPF_WOOCOMMERCE_CART_ITEMS_COUNT' => __('WooCommerce Cart Items Count', 'firebox'),
			'FPF_WOOCOMMERCE_AMOUNT_IN_CART' => __('WooCommerce Amount in Cart', 'firebox'),
			'FPF_WOOCOMMERCE_CURRENT_PRODUCT' => __('WooCommerce Current Product', 'firebox'),
			'FPF_WOOCOMMERCE_CURRENT_PRODUCT_CATEGORY' => __('WooCommerce Current Product Category', 'firebox'),
			'FPF_EDD' => __('Easy Digital Downloads', 'firebox'),
			'FPF_EDD_PRODUCTS_IN_CART' => __('Easy Digital Downloads Products in Cart', 'firebox'),
			'FPF_EDD_CART_ITEMS_COUNT' => __('Easy Digital Downloads Cart Items Count', 'firebox'),
			'FPF_EDD_AMOUNT_IN_CART' => __('Easy Digital Downloads Amount in Cart', 'firebox'),
			'FPF_EDD_CURRENT_PRODUCT' => __('Easy Digital Downloads Current Product', 'firebox'),
			'FPF_EDD_CURRENT_PRODUCT_CATEGORY' => __('Easy Digital Downloads Current Product Category', 'firebox'),
			'FPF_EDD_CURRENT_PRODUCT_PRICE' => __('Easy Digital Downloads Current Product Price', 'firebox'),
			'FPF_EDD_CURRENT_PRODUCT_STOCK' => __('Easy Digital Downloads Current Product Stock', 'firebox'),
			'FPF_EDD_PURCHASED_PRODUCT' => __('Easy Digital Downloads Purchased Product', 'firebox'),
			'FPF_EDD_LAST_PURCHASED_DATE' => __('Easy Digital Downloads Last Purchased Date', 'firebox'),
			'FPF_EDD_TOTAL_SPEND' => __('Easy Digital Downloads Total Spend', 'firebox'),
			'FPF_HOMEPAGE' => __('Homepage', 'firebox'),
			'FPF_WOOCOMMERCE_PURCHASED_PRODUCT' => __('WooCommerce Purchased Product', 'firebox'),
			'FPF_WOOCOMMERCE_LAST_PURCHASED_DATE' => __('WooCommerce Last Purchased Date', 'firebox'),
			'FPF_WOOCOMMERCE_CURRENT_PRODUCT_PRICE' => __('WooCommerce Current Product Price', 'firebox'),
			'FPF_WOOCOMMERCE_TOTAL_SPEND' => __('WooCommerce Total Spend', 'firebox'),
			'FPF_WOOCOMMERCE_CURRENT_PRODUCT_STOCK' => __('WooCommerce Current Product Stock', 'firebox'),
			'FPF_WOOCOMMERCE_CATEGORY' => __('WooCommerce Category', 'firebox'),
			'FPF_EDD_CATEGORY' => __('Easy Digital Downloads Category', 'firebox'),
			'FPF_BLANK_TEMPLATE' => __('Blank Template', 'firebox'),
			'FPF_MINE' => __('Mine', 'firebox'),
			'FPF_DRAFTS' => __('Drafts', 'firebox'),
			'FPF_UPGRADE_TO_PRO_VERSION' => __('Awesome! Only one step left. Click on the button below to complete the upgrade to the Pro version.', 'firebox'),
			'FPF_DARK_MODE' => __('Dark mode', 'firebox'),
			'FPF_VERSION' => __('Version', 'firebox'),
			/* translators: %s: Plugin Name */
			'FPF_PLUGIN_OUDATED_PLEASE_UPDATE' => __('%s is outdated. Please update.', 'firebox'),
			/* translators: %s: Plugin Name */
			'FPF_PLUGIN_IS_UP_TO_DATE' => __('%s is up to date.', 'firebox'),
			'FPF_UPGRADE_NOW_20_OFF' => __('Upgrade now for 20% OFF and unlock all Pro features.', 'firebox'),
			'FPF_GIVE_FEEDBACK' => __('Give feedback', 'firebox'),
			'FPF_WHATS_NEW' => __('What\'s New', 'firebox'),
			'FPF_FLAG' => __('Flag', 'firebox'),
			'FPF_N/A' => __('n/a', 'firebox'),
			'FPF_S' => __('s', 'firebox'),
			'FPF_ACTIVATE' => __('Activate', 'firebox'),
			'FPF_DEACTIVATE' => __('Deactivate', 'firebox'),
			'FPF_EDIT' => __('Edit', 'firebox'),
			'FPF_TO' => __('to', 'firebox'),
			'FPF_UPGRADE_NOW' => __('Upgrade Now', 'firebox'),
			'FPF_RECAPTCHA_INVALID_SECRET_KEY' => __('Invalid secret key', 'firebox'),
			'FPF_PLEASE_VALIDATE' => __('Please validate', 'firebox'),
			'FPF_TURNSTILE' => __('Turnstile', 'firebox'),
			'FPF_CAPTCHA' => __('Captcha', 'firebox'),
			'FPF_CLOUDFLARE_TURNSTILE' => __('Cloudflare Turnstile', 'firebox'),
			'FPF_HCAPTCHA' => __('hCaptcha', 'firebox'),
			'FPF_TAGS' => __('Tags', 'firebox'),
		];
	}
}