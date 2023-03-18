<?php
/**
 *
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload;

/**
 * Extension base
 */
class ext extends \phpbb\extension\base
{
	/** @var Extension name */
	protected $ext_name = 'imgupload';

	/** @var min phpBB version */
	protected $phpbb_min_version = '3.2.4';

	/** @var max phpBB version (>= query) */
	protected $phpbb_max_version = '4.0.0';

	/** @var min PHP version */
	protected $php_min_version = '7.1.0';

	/** @var max PHP version (>= query) */
	protected $php_max_version = '8.3.0';

	/**
	 * Check the minimum and maximum requirements.
	 *
	 * @return bool|string/array A error message
	 */
	public function is_enableable()
	{
		// If phpBB version 3.1 or less cancel
		if (phpbb_version_compare(PHPBB_VERSION, '3.2.0', '<'))
		{
			return false;
		}

		$language = $this->container->get('language');
		$language->add_lang('info_acp_' . $this->ext_name, 'imcger/' . $this->ext_name);
		$error_message = [];

		// Imagick library installed?
		if (!class_exists('Imagick'))
		{
			$error_message += [$language->lang('IMCGER_REQUIRE_IMAGICK')];
		}

		// phpBB version greater equal $phpbb_min_version and less then $phpbb_max_version
		if (phpbb_version_compare(PHPBB_VERSION, $this->phpbb_min_version, '<') || phpbb_version_compare(PHPBB_VERSION, $this->phpbb_max_version, '>='))
		{
			$error_message += [$language->lang('IMCGER_REQUIRE_PHPBB', $this->phpbb_min_version, $this->phpbb_max_version, PHPBB_VERSION),];
		}

		// php version equal or greater $php_min_version and less $php_max_version
		if (version_compare(PHP_VERSION, $this->php_min_version, '<') || version_compare(PHP_VERSION, $this->php_max_version, '>='))
		{
			$error_message += [$language->lang('IMCGER_REQUIRE_PHP', $this->php_min_version, $this->php_max_version, PHP_VERSION),];
		}

		// When phpBB v3.2 use trigger_error() for message output. For v3.1 return false.
		if (phpbb_version_compare(PHPBB_VERSION, '3.3.0', '<') && !empty($error_message))
		{
			$message = implode('<br>', $error_message);
			trigger_error($message, E_USER_WARNING);
		}

		return empty($error_message) ? true : $error_message;
	}
}
