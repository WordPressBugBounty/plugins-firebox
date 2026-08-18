<?php
/**
 * @package         FireBox
 * @version         3.1.10 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Form\Actions\Actions;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Email extends \FireBox\Core\Form\Actions\Action
{
	protected function prepare()
	{
		if (isset($this->form_settings['attrs']['emailNotifications']))
		{
			$this->action_settings = $this->form_settings['attrs']['emailNotifications'];
		}
		// Backwards compatibility - Start
		else
		{
			$this->action_settings = [
				[
					'from' => isset($this->form_settings['attrs']['emailFromEmail']) ? $this->form_settings['attrs']['emailFromEmail'] : '{site.email}',
					'to' => isset($this->form_settings['attrs']['emailSendToEmailAddress']) ? $this->form_settings['attrs']['emailSendToEmailAddress'] : '{field.email}',
					'subject' => isset($this->form_settings['attrs']['emailSubject']) ? $this->form_settings['attrs']['emailSubject'] : 'New Submission #{submission.id}: Contact Form',
					'message' => isset($this->form_settings['attrs']['emailMessage']) ? wpautop($this->form_settings['attrs']['emailMessage']) : '{all_fields}',
					'fromName' => isset($this->form_settings['attrs']['emailFromName']) ? $this->form_settings['attrs']['emailFromName'] : '{site.name}',
					'replyToName' => isset($this->form_settings['attrs']['emailReplyToName']) ? $this->form_settings['attrs']['emailReplyToName'] : '',
					'replyToEmail' => isset($this->form_settings['attrs']['emailReplyToEmail']) ? $this->form_settings['attrs']['emailReplyToEmail'] : '',
					'cc' => isset($this->form_settings['attrs']['emailCC']) ? $this->form_settings['attrs']['emailCC'] : [],
					'bcc' => isset($this->form_settings['attrs']['emailBCC']) ? $this->form_settings['attrs']['emailBCC'] : [],
					'attachments' => []
				]
			];
		}
		// Backwards compatibility - End
	}
	
	/**
	 * Runs the action.
	 * 
	 * @throws  Exception
	 * 
	 * @return  void
	 */
	public function run()
	{
		foreach ($this->action_settings as $email)
		{
			// Set content type and From Name/Email
			$headers = [
				'Content-Type: text/html; charset=UTF-8',
				'From: ' . $email['fromName'] . ' <' . $email['from'] . '>'
			];

			// Set Reply-To
			if (!empty($email['replyToEmail']))
			{
				$replyTo = !empty($email['replyToName']) ? $email['replyToName'] . ' <' . $email['replyToEmail'] . '>' : $email['replyToEmail'];
				$headers[] = 'Reply-To: ' . $replyTo;
			}
	
			// Set CC
			if (isset($email['cc']) && $email['cc'])
			{
				foreach ($email['cc'] as $cc)
				{
					$headers[] = 'Cc: ' . $cc;
				}
			}
	
			// Set BCC
			if (isset($email['bcc']) && $email['bcc'])
			{
				foreach ($email['bcc'] as $bcc)
				{
					$headers[] = 'Bcc: ' . $bcc;
				}
			}

			$email = apply_filters('firebox/form/actions/email/settings', $email, $this->submission);
			
			foreach ($email['to'] as $to)
			{
				$email['message'] = str_replace(["\r\n", "\r", "\n"], '<br>', $email['message']);

				wp_mail($to, $email['subject'], $email['message'], $headers, $email['attachments']);
			}
		}

		return true;
	}

	/**
	 * Removes anything from a value that could break out of a header line.
	 *
	 * @param   mixed  $value
	 *
	 * @return  string
	 */
	public static function sanitizeHeaderText($value)
	{
		if (!is_scalar($value))
		{
			return '';
		}

		$value = wp_strip_all_tags((string) $value);

		// CR/LF (and their encoded forms) are what turn one header into two.
		$value = str_replace(['%0d', '%0a', '%0D', '%0A'], '', $value);
		$value = preg_replace('/[\r\n\t\x00-\x1F\x7F]+/', ' ', $value);

		return trim((string) $value);
	}

	/**
	 * Validates the action prior to running it.
	 *
	 * @return  void
	 */
	public function validate()
	{
		foreach ($this->action_settings as &$email)
		{
			if (empty($email['to']))
			{
				throw new \Exception(esc_html(firebox()->_('FB_FORM_ERROR_RECIPIENT_IS_MISSING')));
			}
	
			$recipients = array_filter(array_map('trim', explode(',', $email['to'])));
			foreach ($recipients as $email_address)
			{
				if (!filter_var($email_address, FILTER_VALIDATE_EMAIL))
				{
					throw new \Exception(esc_html(sprintf(firebox()->_('FB_FORM_ERROR_RECIPIENT_EMAIL_INVALID'), $email_address)));
				}
			}
			$email['to'] = $recipients;

			if (empty($email['fromName']))
			{
				throw new \Exception(esc_html(firebox()->_('FB_FORM_ERROR_FROM_NAME_IS_MISSING')));
			}
	
			if (empty($email['from']))
			{
				throw new \Exception(esc_html(firebox()->_('FB_FORM_ERROR_FROM_EMAIL_IS_MISSING')));
			}
	
			if (!filter_var($email['from'], FILTER_VALIDATE_EMAIL))
			{
				throw new \Exception(esc_html(sprintf(firebox()->_('FB_FORM_ERROR_FROM_EMAIL_IS_INVALID'), $email['from'])));
			}
	
			/**
			 * Reply-To is concatenated into a header, and Smart Tags may have put a
			 * submitted value in it, so hold it to the same standard as From.
			 */
			if (!empty($email['replyToEmail']))
			{
				if (!filter_var($email['replyToEmail'], FILTER_VALIDATE_EMAIL))
				{
					throw new \Exception(esc_html(sprintf(firebox()->_('FB_FORM_ERROR_REPLY_TO_EMAIL_IS_INVALID'), $email['replyToEmail'])));
				}
			}

			/**
			 * Display names go into header lines verbatim. Strip anything that could
			 * start a new header before they get there.
			 */
			foreach (['fromName', 'replyToName'] as $name_key)
			{
				if (isset($email[$name_key]))
				{
					$email[$name_key] = self::sanitizeHeaderText($email[$name_key]);
				}
			}

			$email['subject'] = self::sanitizeHeaderText($email['subject']);

			if (empty($email['subject']))
			{
				throw new \Exception(esc_html(firebox()->_('FB_FORM_ERROR_SUBJECT_IS_MISSING')));
			}

			if (!empty($email['cc']))
			{
				$cc = array_filter(array_map('trim', explode(',', $email['cc'])));
				foreach ($cc as $email_address)
				{
					if (!filter_var($email_address, FILTER_VALIDATE_EMAIL))
					{
						throw new \Exception(esc_html(sprintf(firebox()->_('FB_FORM_ERROR_CC_IS_INVALID'), $email_address)));
					}
				}
				$email['cc'] = $cc;
			}
	
			if (!empty($email['bcc']))
			{
				$bcc = array_filter(array_map('trim', explode(',', $email['bcc'])));
				foreach ($bcc as $email_address)
				{
					if (!filter_var($email_address, FILTER_VALIDATE_EMAIL))
					{
						throw new \Exception(esc_html(sprintf(firebox()->_('FB_FORM_ERROR_BCC_IS_INVALID'), $email_address)));
					}
				}
				$email['bcc'] = $bcc;
			}

			if (!empty($email['attachments']))
			{
				$newAttachments = [];
				$attachments = array_filter(array_map('trim', explode(',', $email['attachments'])));
				
				foreach ($attachments as $attachment)
				{
					$path = implode(DIRECTORY_SEPARATOR, [get_home_path(), ltrim($attachment, DIRECTORY_SEPARATOR)]);

					if (!is_file($path))
					{
						throw new \Exception(esc_html(sprintf(firebox()->_('FB_FORM_ERROR_ATTACHMENT_MISSING'), $attachment)));
					}
					
					$newAttachments[] = $path;
				}
				$email['attachments'] = $newAttachments;
			}
	
			if (empty($email['message']))
			{
				throw new \Exception(esc_html(firebox()->_('FB_FORM_ERROR_MESSAGE_MISSING')));
			}
		}

		return true;
	}
}