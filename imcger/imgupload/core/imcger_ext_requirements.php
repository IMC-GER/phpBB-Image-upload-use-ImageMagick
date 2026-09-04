<?php
/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload\core;

class imcger_ext_requirements
{
	protected $metadata;
	protected $ext_manager;
	protected $language;

	public function __construct(string $ext_name = '')
	{
		global $phpbb_container;

		$this->ext_manager = $phpbb_container->get('ext.manager');
		$this->language	   = $phpbb_container->get('language');

		$this->set_ext_name($ext_name);
	}

	public function set_ext_name(string $ext_name = '')
	{
		if (empty($ext_name))
		{
			preg_match('#ext\/([0-9 a-z]+\/[0-9 a-z]+)#', __DIR__, $matches);
			$ext_name = $matches[1];
		}

		$name = explode('/', $ext_name)[1];
		$this->language->add_lang('info_acp_' . $name, $ext_name);
		$this->metadata = $this->ext_manager->create_extension_metadata_manager($ext_name)->get_metadata();
	}

	public function check_php()
	{
		$require_str = preg_replace('#[,\s]+#', ' ', trim($this->metadata['require']['php'] ?? ''));
		$require_php = explode(' ', $require_str);

		if (strlen($require_php[0]))
		{
			if ($require_php[0][0] == '^')
			{
				$require_php = $this->convert_caret_version($require_php[0]);
			}
			else if ($require_php[0][0] == '~')
			{
				$require_php = $this->convert_tilde_version($require_php[0]);
			}

			foreach ($require_php as $value)
			{
				$required = $this->split_compare_data(htmlspecialchars_decode($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5));

				if (!phpbb_version_compare(PHP_VERSION, $required['version'], $required['operator']))
				{
					return $this->language->lang('IMCGER_REQUIRE_PHP', PHP_VERSION, $require_php[0] . (isset($require_php[1]) ? ', ' . $require_php[1] : ''));
				}
			}
		}

		return true;
	}

	public function check_phpbb()
	{
		$require_str   = preg_replace('#[,\s]+#', ' ', trim($this->metadata['require']['phpbb/phpbb'] ?? $this->metadata['extra']['soft-require']['phpbb/phpbb'] ?? ''));
		$require_phpbb = explode(' ', $require_str);

		if (strlen($require_phpbb[0]))
		{
			if ($require_phpbb[0][0] == '^')
			{
				$require_phpbb = $this->convert_caret_version($require_phpbb[0]);
			}
			else if ($require_phpbb[0][0] == '~')
			{
				$require_phpbb = $this->convert_tilde_version($require_phpbb[0]);
			}

			foreach ($require_phpbb as $value)
			{
				$required = $this->split_compare_data(htmlspecialchars_decode($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5));

				if (!phpbb_version_compare(PHPBB_VERSION, $required['version'], $required['operator']))
				{
					return $this->language->lang('IMCGER_REQUIRE_PHPBB', PHPBB_VERSION, $require_phpbb[0] . (isset($require_phpbb[1]) ? ', ' . $require_phpbb[1] : ''));
				}
			}
		}

		return true;
	}

	public function check()
	{
		$require	   = [];
		$require_phpbb = $this->check_phpbb();
		$require_php   = $this->check_php();

		if ($require_phpbb !== true)
		{
			$require[] = $require_phpbb;
		}

		if ($require_php !== true)
		{
			$require[] = $require_php;
		}

		return count($require) ? $require : true;
	}

	protected function split_compare_data(string $version): array
	{
		$pattern = '#(<=|>=|==|!=|<>|<|>|=|lt|le|gt|ge|eq|ne)\s*([0-9][0-9a-z\.\-\@]*)#';
		$matches = [];
		$version = str_replace('@', '-', $version);

		if (!preg_match($pattern, $version, $matches))
		{
			return [];
		}

		$data = [];
		$data['version']  = $matches[2];
		$data['operator'] = $matches[1];

		return $data;
	}

	protected function convert_tilde_version(string $version): array
	{
		$version = ltrim($version, '~');
		$parts	 = explode('.', $version);

		$major = (int) ($parts[0] ?? 0);
		$minor = (int) ($parts[1] ?? 0);
		$patch = (int) ($parts[2] ?? 0);

		switch (count($parts))
		{
			case 1:
				// ~1 → >=1.0.0 <2.0.0
				$nextMajor = $major + 1;
				return [">={$major}.0.0", "<{$nextMajor}.0.0"];

			case 2:
				// ~1.3 → >=1.3.0 <1.4.0
				$nextMinor = $minor + 1;
				return [">={$major}.{$minor}.0", "<{$major}.{$nextMinor}.0"];

			case 3:
				// ~1.3.2 → >=1.3.2 <1.4.0
				$nextMinor = $minor + 1;
				return [">={$major}.{$minor}.{$patch}", "<{$major}.{$nextMinor}.0"];

			default:
				return [];
		}
	}

	protected function convert_caret_version(string $version): array
	{
		$version = ltrim($version, '^');
		$parts	 = explode('.', $version);

		$major = (int) ($parts[0] ?? 0);
		$minor = (int) ($parts[1] ?? 0);
		$patch = (int) ($parts[2] ?? 0);

		if ($major > 0)
		{
			// ^1.3.2 → >=1.3.2 <2.0.0
			$nextMajor = $major + 1;
			return [">={$major}.{$minor}.{$patch}", "<{$nextMajor}.0.0"];
		}
		else if ($minor > 0)
		{
			// ^0.3.2 → >=0.3.2 <0.4.0
			$nextMinor = $minor + 1;
			return [">={$major}.{$minor}.{$patch}", "<{$major}.{$nextMinor}.0"];
		}
		else
		{
			// ^0.0.2 → >=0.0.2 <0.0.3
			$nextPatch = $patch + 1;
			return [">={$major}.{$minor}.{$patch}", "<{$major}.{$minor}.{$nextPatch}"];
		}

		return [];
	}
}
