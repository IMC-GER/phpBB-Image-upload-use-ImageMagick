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
	/**
	 * Check the minimum and maximum requirements.
	 *
	 * @return bool|string/array A error message
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');

		/* If phpBB version 3.1 or less cancel */
		if (phpbb_version_compare($config['version'], '3.2.0', '<'))
		{
			return false;
		}

		$language = $this->container->get('language');
		$language->add_lang('imgupload_acp', 'imcger/imgupload');
		$error_message = [];

		/* Imagick library installed? */
		if (!class_exists('Imagick'))
		{
			$error_message += ['error1' => $language->lang('IMCGER_IM_REQUIRE_IMAGICK')];
		}

		/* phpBB version greater equal 3.2.4 and less then 4.0 */
		if (phpbb_version_compare($config['version'], '3.2.4', '<') || phpbb_version_compare($config['version'], '4.0.0', '>='))
		{
			$error_message += ['error2' => $language->lang('IMCGER_IM_REQUIRE_PHPBB'),];
		}

		/* php version equal or greater 7.0.0 and less 8.2 */
		if (version_compare(PHP_VERSION, '7.0.0', '<') || version_compare(PHP_VERSION, '8.2', '>='))
		{
			$error_message += ['error3' => $language->lang('IMCGER_IM_REQUIRE_PHP'),];
		}

		/* When phpBB v3.2 use trigger_error() for message output. For v3.1 return false. */
		if (phpbb_version_compare($config['version'], '3.3.0', '<') && !empty($error_message))
		{
			$error_message = implode('<br>', $error_message);
			trigger_error($error_message, E_USER_WARNING);
		}

		return empty($error_message) ? true : $error_message;
	}
}
