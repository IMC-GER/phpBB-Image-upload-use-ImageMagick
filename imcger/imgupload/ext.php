<?php
/**
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
	/** @var min phpBB version */
	protected string $phpbb_min_version = '3.3.0';

	/** @var max phpBB version (>= query) */
	protected string $phpbb_max_version = '4.0.0-dev';

	/** @var min PHP version */
	protected string $php_min_version = '7.4.0';

	/** @var max PHP version (>= query) */
	protected string $php_max_version = '8.5.0-dev';

	/**
	 * Check the minimum and maximum requirements.
	 *
	 * The return value is a union type array|bool
	 * Not specified for reasons of compatibility with php 7
	 */
	public function is_enableable()
	{
		// If phpBB version 3.2 or less cancel
		if (phpbb_version_compare(PHPBB_VERSION, '3.3.0', '<'))
		{
			return false;
		}

		$language = $this->container->get('language');
		$language->add_lang('info_acp_imgupload', 'imcger/imgupload');

		$error_message 	= [];

		// Imagick library installed?
		if (!class_exists('Imagick'))
		{
			$error_message[] = $language->lang('IMCGER_REQUIRE_IMAGICK');
		}

		// phpBB version greater equal $phpbb_min_version and less then $phpbb_max_version
		if (phpbb_version_compare(PHPBB_VERSION, $this->phpbb_min_version, '<') || phpbb_version_compare(PHPBB_VERSION, $this->phpbb_max_version, '>='))
		{
			$error_message[] = $language->lang('IMCGER_REQUIRE_PHPBB', $this->phpbb_min_version, $this->phpbb_max_version, PHPBB_VERSION);
		}

		// php version equal or greater $php_min_version and less $php_max_version
		if (version_compare(PHP_VERSION, $this->php_min_version, '<') || version_compare(PHP_VERSION, $this->php_max_version, '>='))
		{
			$error_message[] = $language->lang('IMCGER_REQUIRE_PHP', $this->php_min_version, $this->php_max_version, PHP_VERSION);
		}

		return empty($error_message) ? true : $error_message;
	}
}
