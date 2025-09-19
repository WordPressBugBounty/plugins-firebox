<?php
/**
 * @package         FireBox
 * @version         3.0.5 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Libs;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Translations
{
	/**
	 * Holds all translations of the plugin.
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
	 * @param  String  $text
	 * @param  String  $fallback
	 * 
	 * @return  String
	 */
	public function _($text, $fallback = null)
	{
		if (!is_string($text) && !is_int($text))
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
	public function retrieve($text, $fallback = '')
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
	public function getTranslations()
	{
		return [
			'FB_PLUGIN_NAME' => __('FireBox', 'firebox'),
			'FB_ADD_FIREBOX' => __('Add FireBox', 'firebox'),
			'FB_ADD_BUTTON' => __('Add Button', 'firebox'),
			'FB_PLUGIN_PLULAR_NAME' => __('Campaigns', 'firebox'),
			'FB_NEW_CAMPAIGN' => __('New Campaign', 'firebox'),
			'FB_IMPORT_CAMPAIGNS' => __('FireBox Import Campaigns', 'firebox'),
			'FB_SETTINGS_PAGE_TITLE' => __('FireBox Settings', 'firebox'),
			'FB_SETTINGS_LOAD_CSS' => __('Load Stylesheet', 'firebox'),
			'FB_SETTINGS_LOAD_CSS_DESC' => __('Select to load the plugins stylesheet. You can disable this if you place all your own styles in some other stylesheet, like the Custom Code section in your theme\'s settings.', 'firebox'),
			'FB_SETTINGS_SHOW_COPYRIGHT' => __('Show Copyright', 'firebox'),
			'FB_SETTINGS_SHOW_COPYRIGHT_DESC' => __('If selected, extra copyright info will be displayed in the admin pages.', 'firebox'),
			'FB_SETTINGS_DEBUG' => __('Debug', 'firebox'),
			'FB_SETTINGS_DEBUG_DESC' => __('Debug plugin using your browser\'s Developers Console (Press F12).', 'firebox'),
			'FB_SETTINGS_ANALYTICS' => __('Analytics', 'firebox'),
			'FB_SETTINGS_ANALYTICS_DESC' => __('Log the data of your campaigns to the database. Note that deleted data, won\'t appear in the Analytics page.', 'firebox'),
			'FB_SETTINGS_STATS_PERIOD' => __('Maximum Storage Period', 'firebox'),
			'FB_SETTINGS_STATS_PERIOD_DESC' => __('Automatically delete old campaign data after a period.', 'firebox'),
			'FB_SETTINGS_GAT' => __('Google Analytics Tracking', 'firebox'),
			'FB_SETTINGS_GAT_DESC' => __('FireBox will automatically track your campaigns data with your Google Analytics ID. The events which will be tracked is the Open and Close events. Note that you will need to have Google Analytics set up already on your site.', 'firebox'),
			'FB_DUPLICATE_CAMPAIGN' => __('Duplicate Campaign', 'firebox'),
			'FB_CAMPAIGN_LIBRARY' => __('FireBox Campaign Library', 'firebox'),
			'FB_HIDDEN_BY_COOKIE' => __('Hidden by cookie', 'firebox'),
			'FB_CLEAR_COOKIE' => __('Clear Cookie', 'firebox'),
			'FB_USERNAME_OR_EMAIL_ADDRESS' => __('Username or Email Address', 'firebox'),
			'FB_CAMPAIGN_IMPORT_CONTENTS_ERROR' => __('Campaign Import could not be completed successfully. It appears it contains invalid data.', 'firebox'),
			'FB_LAST_7_DAYS' => __('Last 7 days', 'firebox'),
			'FB_ANALYTICS_PAGE_TITLE' => __('FireBox Analytics', 'firebox'),
			'FB_TOP_CAMPAIGNS' => __('Top Campaigns', 'firebox'),
			'FB_ALL_CAMPAIGNS' => __('All Campaigns', 'firebox'),
			'FB_SETTINGS_MEDIA_DESC' => __('Set whether to enable the FireBox CSS library as well as whether to load the campaign animations.', 'firebox'),
			'FB_SETTINGS_OTHER_DESC' => __('Set whether to display the copyright message or whether to enable the debug mode.', 'firebox'),
			'FB_PREVIEW' => __('Preview', 'firebox'),
			'FB_VIEW_CAMPAIGN' => __('View Campaign', 'firebox'),
			'FB_VIEW_CAMPAIGNS' => __('View Campaigns', 'firebox'),
			'FB_FIREBOX_PREVIEW_DESC' => __('This is a preview of how your FireBox campaign will look like in a page. This page is not publicly accessible.', 'firebox'),
			'FB_FIREBOX_CAMPAIGN_PREVIEW' => __('FireBox Campaign Preview', 'firebox'),
			'FB_CAMPAIGN_TYPE' => __('Campaign Type', 'firebox'),
			'FB_SETTINGS_SHOW_ADMIN_BAR_MENU_ITEM' => __('Show Admin Bar Menu Item', 'firebox'),
			'FB_SETTINGS_SHOW_ADMIN_BAR_MENU_ITEM_DESC' => __('Set whether to show the FireBox menu item in the admin bar, at the top of the page. This adds helpful links to get you to the most used pages of the FireBox plugin.', 'firebox'),
			'FB_SUBMISSION_CONFIRMED' => __('Confirmed', 'firebox'),
			'FB_SUBMISSION_UNCONFIRMED' => __('Unconfirmed', 'firebox'),
			'FB_SUBMISSIONS_PAGE_TITLE' => __('FireBox Submissions', 'firebox'),
			'FB_NO_SUBMISSIONS_FOUND' => __('No submissions found.', 'firebox'),
			'FB_DATE_SUBMITTED' => __('Date Submitted', 'firebox'),
			'FB_PLEASE_SELECT_A_FORM_CAMPAIGN' => __('- Please select a form campaign -', 'firebox'),
			'FB_CANNOT_UPDATE_SUBMISSION' => __('Cannot update submission', 'firebox'),
			'FB_THIS_IS_A_REQUIRED_FIELD' => __('This is a required field.', 'firebox'),
			'FB_USER_SUBMITTED_DATA' => __('User Submitted Data', 'firebox'),
			'FB_SUBMISSION_INFO' => __('Submission Info', 'firebox'),
			'FB_UPDATE_SUBMISSION' => __('Update Submission', 'firebox'),
			'FB_BACK_TO_SUBMISSIONS' => __('Back to submissions', 'firebox'),
			'FB_FORM' => __('Form', 'firebox'),
			'FB_CREATED_DATE' => __('Created Date', 'firebox'),
			'FB_MODIFIED_DATE' => __('Modified Date', 'firebox'),
			'FB_SUBMISSIONS_UPDATED' => __('Submissions updated.', 'firebox'),
			'FB_HONEYPOT_FIELD_TRIGGERED' => __('Honeypot field triggered.', 'firebox'),
			'FB_CONVERSION_RATE_TOOLTIP' => __('<strong>Conversion Rate</strong> is the average number of conversion per the Gutenberg block "FireBox Form", shown as a percentage.', 'firebox'),
			'FB_JSON_API' => __('JSON API', 'firebox'),
			'FB_JSON_API_DESC' => __('The JSON API allows you to retrieve FireBox data using HTTP requests.', 'firebox'),
			'FB_ENABLE_JSON_API' => __('Enable JSON API', 'firebox'),
			'FB_ENABLE_JSON_API_DESC' => __('Set whether to enable the FireBox API endpoints.', 'firebox'),
			'FB_API_PASSWORD' => __('Password', 'firebox'),
			'FB_API_PASSWORD_DESC' => __('Enter a unique alphanumeric that will act as the password that will be used to authenticate all FireBox API requests.', 'firebox'),
			'FB_RATE_FIREBOX' => __('Rate FireBox', 'firebox'),
			'FB_CAMPAIGNS' => __('Campaigns', 'firebox'),
			'FB_RECENT_CAMPAIGNS' => __('Recent Campaigns', 'firebox'),
			'FB_FIREBOX_CAMPAIGNS' => __('FireBox Campaigns', 'firebox'),
			'FB_IMPORT_CAMPAIGNS' => __('Import Campaigns', 'firebox'),
			'FB_PUBLISH_CAMPAIGNS' => __('Publish Campaigns', 'firebox'),
			'FB_UNTITLED_CAMPAIGN' => __('Untitled Campaign', 'firebox'),
			/* translators: %s: total campaigns that have been published */
			'FB_X_CAMPAIGNS_HAVE_BEEN_PUBLISHED' => __('%s campaign(s) have been published.', 'firebox'),
			/* translators: %s: total campaigns that have been unpublished */
			'FB_X_CAMPAIGNS_HAVE_BEEN_UNPUBLISHED' => __('%s campaign(s) have been unpublished.', 'firebox'),
			/* translators: %s: total campaigns that have been deleted */
			'FB_X_CAMPAIGNS_HAVE_BEEN_DELETED' => __('%s campaign(s) have been deleted.', 'firebox'),
			/* translators: %s: total campaigns that have been exported */
			'FB_X_CAMPAIGNS_HAVE_BEEN_EXPORTED' => __('%s campaign(s) have been exported.', 'firebox'),
			/* translators: %s: total campaigns stats that have been reset */
			'FB_X_CAMPAIGNS_HAVE_BEEN_RESET' => __('%s campaign(s) stats have been reset.', 'firebox'),
			'FB_CAMPAIGN_DUPLICATED' => __('Campaign has been duplicated.', 'firebox'),
			'FB_CAMPAIGN_INFO' => __('Campaign info', 'firebox'),
			'FB_EDIT_CAMPAIGN' => __('Edit Campaign', 'firebox'),
			'FB_LAST_VIEWED' => __('Last Viewed', 'firebox'),
			'FB_ACTIVE' => __('Active', 'firebox'),
			'FB_CONVERSIONS' => __('Conversions', 'firebox'),
			'FB_CAMPAIGN' => __('Campaign', 'firebox'),
			'FB_CONVERSION_RATE' => __('Conversion Rate', 'firebox'),
			'FB_NO_DATA_AVAILABLE' => __('No data available.', 'firebox'),
			'FB_VIEWS' => __('Views', 'firebox'),
			'FB_PERCENTAGE_DIFFERENCE_AGAINST_PREVIOUS_PERIOD' => __('Percentage difference against previous period', 'firebox'),
			'FB_NO_CAMPAIGN_DATA_FOUND' => __('No campaign data found.', 'firebox'),
			'FB_MOST_POPULAR_CAMPAIGNS' => __('Most Popular Campaigns', 'firebox'),
			'FB_ALL_DAYS' => __('All Days', 'firebox'),
			'FB_MONDAY' => __('Monday', 'firebox'),
			'FB_TUESDAY' => __('Tuesday', 'firebox'),
			'FB_WEDNESDAY' => __('Wednesday', 'firebox'),
			'FB_THURSDAY' => __('Thursday', 'firebox'),
			'FB_FRIDAY' => __('Friday', 'firebox'),
			'FB_SATURDAY' => __('Saturday', 'firebox'),
			'FB_SUNDAY' => __('Sunday', 'firebox'),
			'FB_VIEW_HOURS' => __('View Hours', 'firebox'),
			'FB_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_CAMPAIGN' => __('Are you sure you want to delete this campaign?', 'firebox'),
			'FB_VIEW_ALL' => __('View All', 'firebox'),
			'FB_YOU_HAVENT_CREATED_ANY_CAMPAIGNS_YET' => __('You haven’t created any campaigns yet.', 'firebox'),
			'FB_NUMBER_OF_VIEWS_IN_THE_LAST_30_DAYS' => __('Number of views in the last 30 days', 'firebox'),
			'FB_NUMBER_OF_CONVERSIONS_IN_THE_LAST_30_DAYS' => __('Number of conversions in the last 30 days', 'firebox'),
			'FB_CONVERSION_RATE_IN_THE_LAST_30_DAYS' => __('Conversion rate in the last 30 days', 'firebox'),
			'FB_LOADING_CAMPAIGNS' => __('Loading Campaigns...', 'firebox'),
			'FB_NO_CAMPAIGNS_FOUND' => __('No campaigns found.', 'firebox'),
			'FB_SEARCH_DOTS' => __('Search...', 'firebox'),
			'FB_TODAY' => __('Today', 'firebox'),
			'FB_YESTERDAY' => __('Yesterday', 'firebox'),
			'FB_LAST_30_DAYS' => __('Last 30 Days', 'firebox'),
			'FB_LAST_WEEK' => __('Last Week', 'firebox'),
			'FB_LAST_MONTH' => __('Last Month', 'firebox'),
			'FB_CUSTOM' => __('Custom', 'firebox'),
			'FB_READ_MORE' => __('Read More', 'firebox'),
			'FB_AVG_TIME_OPEN' => __('Avg Time Open', 'firebox'),
			'FB_CONVERSION_RATE_TOOLTIP_DESC' => __('The average number of conversions, shown as a percentage.', 'firebox'),
			'FB_CONVERSIONS_TOOLTIP_DESC' => __('The total user interactions, including form submissions and clicks on campaign elements, indicating successful engagement.', 'firebox'),
			'FB_VS_PREVIOUS_PERIOD' => __('vs previous period', 'firebox'),
			'FB_VIEWS_TOOLTIP_DESC' => __('The number of times a campaign has been displayed to your users on your site.', 'firebox'),
			'FB_NO' => __('No', 'firebox'),
			'FB_DATA_AVAILABLE' => __('data available', 'firebox'),
			'FB_PERFORMANCE' => __('Performance', 'firebox'),
			'FB_TRENDING_TEMPLATES' => __('Trending Templates', 'firebox'),
			'FB_THERE_ARE_NO_TRENDING_TEMPLATES_TO_SHOW' => __('There are no trending templates to show.', 'firebox'),
			'FB_INSERT_TEMPLATE' => __('Insert Template', 'firebox'),
			'FB_INSERT' => __('Insert', 'firebox'),
			'FB_VIEW_ALL_ANALYTICS' => __('View All Analytics', 'firebox'),
			'FB_DAILY' => __('Daily', 'firebox'),
			'FB_WEEKLY' => __('Weekly', 'firebox'),
			'FB_MONTHLY' => __('Monthly', 'firebox'),
			'FB_ACTIONS' => __('Actions', 'firebox'),
			'FB_LOADING_DASHBOARD' => __('Loading Dashboard...', 'firebox'),
			'FB_LOADING_ANALYTICS' => __('Loading Analytics...', 'firebox'),
			'FB_UPGRADE_20_OFF' => __('Get FireBox Pro For 20% OFF!', 'firebox'),
			'FB_SHOWING_TOP_30_RESULTS' => __('Showing top 30 results.', 'firebox'),
			'FB_VIEW_CAMPAIGN_ANALYTICS' => __('View campaign analytics', 'firebox'),
			'FB_DAY_OF_THE_WEEK' => __('Day of the week', 'firebox'),
			'FB_GOOGLE_ANALYTICS_INTEGRATION_LABEL_DESC' => __('FireBox will automatically sync data to your Google Analytics account.', 'firebox'),
			'FB_VIEW_ANALYTICS_OF_CAMPAIGN' => __('View Analytics of campaign.', 'firebox'),
			'FB_PHPSCRIPTS' => __('PHP Scripts', 'firebox'),
			'FB_PHPSCRIPTS_SETTINGS_DESC' => __('Set whether to enable the PHP Scripts section when editing a campaign, allowing you execute PHP code in various events such as before/after popup renders, on open/close or form submission.', 'firebox'),
			'FB_ENABLE_PHPSCRIPTS' => __('Enable PHP Scripts', 'firebox'),
			'FB_ENABLE_PHPSCRIPTS_DESC' => __('Set whether to enable PHP Scripts.', 'firebox'),
			/* translators: %s: field name */
			'FB_X_FIELD' => __('%s Field', 'firebox'),
			'FB_VALIDATION_ERRORS' => __('Validation errors', 'firebox'),
			'FB_CHOICE_LABEL' => __('Choice Label', 'firebox'),
			'FB_FORM_DETAILS_NOT_FOUND' => __('Form details not found due to form being deleted.', 'firebox'),
			'FB_SUBMISSION_UPDATED' => __('Submission updated.', 'firebox'),
			'FB_USAGE_TRACKING' => __('Usage Tracking', 'firebox'),
			'FB_USAGE_TRACKING_DESC' => __('You can help shape FireBox by providing us with usage data about how you use our plugin.', 'firebox'),
			'FB_ALLOW_USAGE_TRACKING' => __('Allow Usage Tracking', 'firebox'),
			/* translators: %s: Documentation url */
			'FB_ALLOW_USAGE_TRACKING_DESC' => __('Allow FireBox to collect and send usage data to help improve the plugin. <a href="%s" target="_blank">Learn more</a>', 'firebox'),
			'FB_NOTICE_IS_OUTDATED' => __('FireBox is Outdated', 'firebox'),
			/* translators: %d: How long the plugin has been oudated for, documentation url */
			'FB_NOTICE_OUTDATED_EXTENSION' => __('Your version of FireBox is over %1$d days old and may contain bugs and security issues. Update now to the latest version to ensure optimal performance and security. <a href="%2$s" target="_blank">View Changelog</a>', 'firebox'),
			'FB_UPDATE_NOW' => __('Update Now', 'firebox'),
			/* translators: %s: plugin version, plugin release date */
			'FB_NEW_VERSION_IS_AVAILABLE_DESC' => __('There\'s a new version of FireBox (v%1$s) released on %2$s. Update now to benefit from new features and bug fixes. <a href="%3$s" target="_blank">View Changelog</a>', 'firebox'),
			/* translators: %s: plugin version */
			'FB_YOUR_USING_VERSION' => __('You\'re using %s', 'firebox'),
			'FB_VIEW_CHANGELOG' => __('View Changelog', 'firebox'),
			/* translators: %s: documentation url */
			'FB_NOTICE_GEO_MAINTENANCE_DESC' => __('FireBox finds the Country of your visitor using the MaxMind GeoLite2 Country database which needs to be updated at least once every 3 months. <a href="%s" target="_blank">Read more</a>', 'firebox'),
			'FB_NOTICE_UPGRADE_TO_PRO_TOOLTIP' => __('You will be redirected to the pricing page with the 20% discount already applied.<br /><br />After the payment is complete you can access the Pro files on your Downloads page. To complete the upgrade, download the Pro installation zip file and install it over the free version.<br /><br />Note: You do not have to uninstall the free version first. All your content, settings will remain as it is even after switching to the Pro version. You don\'t need to redo what you have already built with the free version.', 'firebox'),
			/* translators: %s: discount percentage */
			'FB_UPGRADE_TO_PRO_X_OFF' => __('Upgrade to PRO %s%% OFF', 'firebox'),
			'FB_UPGRADE_TO_PRO_NOTICE_DESC' => __('FireBox Lite only scratches the surface of what\'s possible. Upgade to PRO to unlock the full functionality.', 'firebox'),
			'FB_IS_MISSING' => __('Is Missing', 'firebox'),
			'FB_IS_INVALID' => __('Is Invalid', 'firebox'),
			/* translators: %s: type download key status, documentation url */
			'FB_DOWNLOAD_KEY_MISSING_DESC' => __('To be able to receive updates and unlock all FireBox features you will need to enter %1$s download key. <a href="%2$s" target="_blank">Find my license key</a>', 'firebox'),
			/* translators: %s: download key status */
			'FB_DOWNLOAD_KEY_TEXT' => __('Download Key %s', 'firebox'),
			'FB_YOUR' => __('your', 'firebox'),
			'FB_A_VALID' => __('a valid', 'firebox'),
			'FB_ENTER_YOUR_DOWNLOAD_KEY' => __('Enter your Download Key', 'firebox'),
			'FB_LICENSE_ACTIVATION_SUCCESS' => __('License Key Activated!', 'firebox'),
			'FB_DOWNLOAD_KEY_ENTERED_INVALID' => __('Download Key entered is invalid', 'firebox'),
			'FB_NOTICE_EXPIRED_TOOLTIP' => __('You will be redirected to your Subscriptions page where you will be asked to log into your account. There you will be able to view an overview of your subscriptions.<br /><br />Click Renew next to the expired subscription to renew 15% OFF.<br /><br />Note: The 15% discount is automatically applied on the checkout page.', 'firebox'),
			'FB_NOTICE_EXPIRING_TOOLTIP' => __('You will be redirected to your Subscriptions page where you will be asked to log into your account. There you will be able to view an overview of your subscriptions.<br /><br />Click Reactivate next to the expiring subscription to reactivate your subscription.', 'firebox'),
			'FB_FIREBOX_IS_EXPIRING' => __('FireBox Is Expiring', 'firebox'),
			/* translators: %s: plan name, expiring date */
			'FB_FIREBOX_EXPIRING_DESC' => __('Your FireBox %1$s subscription is going to expire on %2$s. Keep your PRO privileges by enabling auto-renew. This ensures uninterrupted access to all premium features, updates, and priority support.', 'firebox'),
			/* translators: %s: discount percentage */
			'FB_REACTIVATE_X_PERCENT_OFF' => __('Reactivate %s%% OFF', 'firebox'),
			'FB_ENABLE_AUTO_RENEW' => __('Enable Auto-Renew', 'firebox'),
			'FB_FIREBOX_EXPIRED' => __('FireBox Expired', 'firebox'),
			/* translators: %s: plan name, expired date */
			'FB_FIREBOX_EXPIRED_DESC' => __('Your FireBox %1$s subscription expired on %2$s. Reactivate today and save 15%% to re-activate access to PRO files, updates and high priority support.', 'firebox'),
			'FB_RATE_FIREBOX' => __('Rate FireBox', 'firebox'),
			'FB_RATE_NOTICE_EXTENSION_DESC' => __('It\'s great to see you have FireBox active for a few days now. Let\'s spread the word and boost our motivation by writing a 5-star review. <a href="#" class="firebox-notice-rate-already-rated">I already did</a>', 'firebox'),
			'FB_I_ALREADY_DID' => __('I already did', 'firebox'),
			'FB_WRITE_REVIEW' => __('Write Review', 'firebox'),
			'FB_CAMPAIGN_HAS_BEEN_TRASHED' => __('Campaign has been trashed.', 'firebox'),
			'FB_CAMPAIGN_HAS_BEEN_RESTORED' => __('Campaign has been restored.', 'firebox'),
			'FB_CLOSE_ON_ESC' => __('Close with ESC key', 'firebox'),
			'FB_CLOSE_ON_ESC_DESC' => __('Enable to close the popup when the ESC key is pressed.', 'firebox'),
			'FB_UPGRADE_TO_FIREBOX_PRO' => __('Upgrade to FireBox Pro', 'firebox'),
			'FB_FIREBOX_UPDATE_AVAILABLE' => __('FireBox Update Available', 'firebox'),
			'FB_UPDATE_DATABASE' => __('Update Database', 'firebox'),
			'FB_PHONE_NUMBER_FIELD' => __('Phone Number', 'firebox'),
			'FB_PLEASE_ENTER_A_VALID_EMAIL_ADDRESS' => __('Please enter a valid email address.', 'firebox'),
			'FB_DATE_TIME_FIELD' => __('Date/Time', 'firebox'),
			'FB_SEARCH_CAMPAIGNS' => __('Search Campaigns', 'firebox'),
			'FB_FORM_ERROR_RECIPIENT_IS_MISSING' => __('Form error: Recipient is missing.', 'firebox'),
			/* translators: %s: email address */
			'FB_FORM_ERROR_RECIPIENT_EMAIL_INVALID' => __('Form error: Recipient email is invalid: %s.', 'firebox'),
			'FB_FORM_ERROR_SUBJECT_IS_MISSING' => __('Form error: Subject is missing.', 'firebox'),
			'FB_FORM_ERROR_FROM_NAME_IS_MISSING' => __('Form error: From Name is missing.', 'firebox'),
			'FB_FORM_ERROR_FROM_EMAIL_IS_MISSING' => __('Form error: From Email is missing.', 'firebox'),
			/* translators: %s: email address */
			'FB_FORM_ERROR_FROM_EMAIL_IS_INVALID' => __('Form error: From Email is invalid: %s.', 'firebox'),
			/* translators: %s: email address */
			'FB_FORM_ERROR_CC_IS_INVALID' => __('Form error: CC is invalid: %s.', 'firebox'),
			/* translators: %s: email address */
			'FB_FORM_ERROR_BCC_IS_INVALID' => __('Form error: BCC is invalid: %s.', 'firebox'),
			/* translators: %s: email attachment file path */
			'FB_FORM_ERROR_ATTACHMENT_MISSING' => __('Form error: Attachment is missing: %s.', 'firebox'),
			'FB_FORM_ERROR_MESSAGE_MISSING' => __('Form error: Message is missing.', 'firebox'),
			/* translators: %s: Integration Name */
			'FB_INTEGRATION_ERROR_NO_LIST_SELECTED' => __('%s error: No list has been selected.', 'firebox'),
			/* translators: %s: Integration Name */
			'FB_INTEGRATION_ERROR_NO_API_KEY_SET' => __('%s error: No API KEY has been set.', 'firebox'),
			/* translators: %s: Documentation URL */
			'FB_CLOUDFLARE_TURNSTILE_DESC' => __('Configure Cloudflare Turnstile to protect your FireBox forms from spam.<br><br>To learn more about how Turnstile works, as well as a step by step setup guide, please read our <a href="%s" target="_blank">documentation</a>.', 'firebox'),
			'FB_SITE_KEY' => __('Site Key', 'firebox'),
			'FB_CLOUDFLARE_TURNSTILE_SITE_KEY_DESC' => __('Enter your Cloudflare Turnstile Site Key.', 'firebox'),
			'FB_SECRET_KEY' => __('Secret Key', 'firebox'),
			'FB_CLOUDFLARE_TURNSTILE_SECRET_KEY_DESC' => __('Enter your Cloudflare Turnstile Secret Key.', 'firebox'),
			'FB_USAGE_TRACKING_NOTICE_TITLE' => __('Help us improve FireBox', 'firebox'),
			/* translators: %s: Documentation URL */
			'FB_USAGE_TRACKING_NOTICE_TITLE_DESC' => __('By allowing us to collect usage data, you help us understand how you use FireBox and how we can improve it. You can change this via our Settings page. <a href="%s" target="_blank">Learn more</a>', 'firebox'),
			'FB_ALLOW' => __('Allow', 'firebox'),
			'FB_ENTER_CLOUDFLARE_TURNSTILE_KEYS' => __('Please enter your Cloudflare Turnstile Site Key and Secret Key in the FireBox settings.', 'firebox'),
			/* translators: %s: Documentation URL */
			'FB_HCAPTCHA_DESC' => __('Configure hCaptcha to protect your FireBox forms from spam.<br><br>To learn more about how hCaptcha works, as well as a step by step setup guide, please read our <a href="%s" target="_blank">documentation</a>.', 'firebox'),
			'FB_HCAPTCHA_SITE_KEY_DESC' => __('Enter your hCaptcha Site Key.', 'firebox'),
			'FB_HCAPTCHA_SECRET_KEY_DESC' => __('Enter your hCaptcha Secret Key.', 'firebox'),
			'FB_ENTER_HCAPTCHA_KEYS' => __('Please enter your hCaptcha Site Key and Secret Key in the FireBox settings.', 'firebox'),
			'FB_CLASSIC_EDITOR_NOT_SUPPORTED' => __('FireBox is a Gutenberg-based plugin and in order to edit a campaign, you must have the Classic Editor plugin disabled.', 'firebox'),
			'FB_EXPORT_SUBMISSIONS' => __('Export Submissions', 'firebox'),
			'FB_ADD_NEW_CAMPAIGN' => __('Add New Campaign', 'firebox')
		];
	}
}