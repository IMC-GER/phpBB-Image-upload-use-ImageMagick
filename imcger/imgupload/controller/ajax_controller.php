<?php
/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload\controller;

class ajax_controller
{
	protected object $config;
	protected object $user;
	protected object $request;
	protected object $db;
	protected object $auth;
	protected object $language;
	protected object $ext_manager;
	protected object $filesystem;
	protected string $root_path;
	protected string $php_ext;
	protected string $ext_display_name;

	public function __construct(
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\request\request $request,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\auth\auth $auth,
		\phpbb\language\language $language,
		\phpbb\extension\manager $ext_manager,
		\phpbb\filesystem\filesystem $filesystem,
		string $root_path,
		string $php_ext
	)
	{
		$this->config		= $config;
		$this->user			= $user;
		$this->request		= $request;
		$this->db			= $db;
		$this->auth			= $auth;
		$this->language		= $language;
		$this->ext_manager	= $ext_manager;
		$this->filesystem	= $filesystem;
		$this->root_path	= $root_path;
		$this->php_ext		= $php_ext;

		$this->language->add_lang('attachment', 'imcger/imgupload');

		$metadata_manager = $this->ext_manager->create_extension_metadata_manager('imcger/imgupload');
		$this->ext_display_name = $metadata_manager->get_metadata('display-name');
	}

	/**
	 * Assigns the Ajax request app.php/imgupload/{order}
	 */
	public function request(string $order): void
	{
		// No ajax request, redirect to forum index
		if (!$this->request->is_ajax() || $this->user->data['is_bot'])
		{
			redirect($this->root_path . '/index.' . $this->php_ext);
		}

		// No user logged in, redirect in js to login page
		if (!$this->user->data['is_registered'])
		{
			$this->json_response(3);
		}

		// Check form token
		if (!(check_form_key('posting') || check_form_key('ucp_pm_compose')))
		{
			$this->json_response(5, $this->ext_display_name, $this->language->lang('FORM_INVALID'));
		}

		switch ($order)
		{
			// Ajax request to save image after rotation
			case 'save_image':
				// Get variable, accept only integer
				$img_attach_id	= intval($this->request->variable('attach_id', 0));
				$img_rotate_deg	= intval($this->request->variable('img_rotate_deg', 0));

				$this->save_image($img_attach_id, $img_rotate_deg);
			break;

			// Ajax request for image size
			case 'image_size':
				// Get variable, accept only integer
				$img_attach_id	= intval($this->request->variable('attach_id', 0));

				$this->image_size($img_attach_id);
			break;

			// Displays the start page of phpBB
			default:
				redirect($this->phpbb_root_path . 'index.' . $this->phpEx);
			break;
		}
	}

	/**
	 * Rotate and save Image with ImageMagick
	 */
	private function save_image(int $img_attach_id, int $img_rotate_deg): array
	{
		if (!$img_attach_id || $img_rotate_deg < 1 || $img_rotate_deg > 360)
		{
			$this->json_response(5, $this->ext_display_name, $this->language->lang('IUL_WRONG_PARAM'));
		}

		if ($this->auth->acl_gets('u_attach', 'a_attach', 'f_attach'))
		{
			$sql = 'SELECT *
					FROM ' . ATTACHMENTS_TABLE . '
					WHERE attach_id = ' . (int) $img_attach_id;

			$result	  = $this->db->sql_query($sql);
			$img_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
		}

		if (!isset($img_data) || $img_data == false)
		{
			$this->json_response(4, $this->ext_display_name, $this->language->lang('IUL_NO_IMG_IN_DATABASE'));
		}

		// Get image file path
		$image_file_path = $this->root_path . trim($this->config['upload_path'], '/') . '/' . $img_data['physical_filename'];
		$thumb_file_path = $this->root_path . trim($this->config['upload_path'], '/') . '/' . 'thumb_' . $img_data['physical_filename'];

		if ($this->filesystem->exists($image_file_path))
		{
			$img_data['filesize'] = $this->rotate_image($image_file_path, $img_rotate_deg);
		}
		else
		{
			$this->json_response(4, $this->ext_display_name, $this->language->lang('IUL_IMG_NOT_EXIST'));
		}

		if ($img_data['thumbnail'] && $this->filesystem->exists($thumb_file_path))
		{
			$this->rotate_image($thumb_file_path, $img_rotate_deg);
		}
		else if ($img_data['thumbnail'])
		{
			$img_data['thumbnail'] = 0;
			$alert_msg = $this->language->lang('IUL_THUMB_NOT_EXIST');
		}

		// Update DataBase
		unset($img_data['attach_id']);
		$sql = 'INSERT INTO ' . ATTACHMENTS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $img_data);
		$this->db->sql_query($sql);

		// sql_nextid() to be removed in 4.1.0-a1, in future use sql_last_inserted_id() it exsits since 3.3.11-RC1
		$new_attach_id = $this->db->sql_nextid();

		if ($new_attach_id)
		{
			$sql = 'DELETE FROM ' . ATTACHMENTS_TABLE . ' WHERE attach_id = ' . (int) $img_attach_id;
			$this->db->sql_query($sql);

			$this->json_response(0, $this->ext_display_name, $alert_msg ?? '', $img_attach_id, $new_attach_id, $img_data['filesize']);
		}
		else
		{
			$this->json_response(5, $this->ext_display_name, $this->language->lang('IUL_DATABASE_NOT_UPDATE'));
		}
	}

	/**
	 * Get Image size
	 */
	private function image_size(int $attach_id): void
	{
		$sql = 'SELECT physical_filename
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE attach_id = ' . (int) $attach_id;

		$result	  = $this->db->sql_query($sql);
		$img_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$file_path = join('/', [trim($this->config['upload_path'], '/'), trim($img_data['physical_filename'], '/')]);

		clearstatcache();
		$filesize = @filesize($file_path);

		if ($filesize == false)
		{
			$this->json_response(5);
		}

		$this->json_response(0, $this->ext_display_name, '', $attach_id, $attach_id, $filesize);
	}

	/**
	 * Rotate Image with ImageMagick
	 */
	private function rotate_image(string $path, int $deg): int
	{
		$image = new \Imagick($path);
		$image->rotateImage('#000', $deg);
		$image->writeImage($path);
		$filesize = strlen($image->getImageBlob());
		$image->clear();

		return $filesize;
	}

	/**
	 * Generate and send json string
	 */
	private function json_response(int $status, string $title = '', string $message = '', int $old_attach_id = 0, int $new_attach_id = 0, int $file_size = 0): void
	{
		$json_response = new \phpbb\json_response;
		$json_response->send([
			'status'		=> (int) $status,
			'title'			=> $title,
			'message'		=> $message,
			'oldAttachId'	=> (int) $old_attach_id,
			'newAttachId'	=> (int) $new_attach_id,
			'fileSize'		=> (int) $file_size,
		]);
	}
}
