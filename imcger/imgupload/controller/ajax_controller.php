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

/**
 * Ajax main controller
 */
class ajax_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\request\request */
	private $request;

	/** @var \phpbb\db\driver\driver_interface */
	private $db;

	/** @var \phpbb\auth\auth */
	private $auth;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var \phpbb\filesystem\filesystem */
	protected $filesystem;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string Extension name */
	protected $ext_display_name;

	/**
	 * Constructor for ajax controller
	 *
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\user						$user
	 * @param \phpbb\request\request			$request
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param \phpbb\auth\auth					$auth
	 * @param \phpbb\language\language			$language
	 * @param \phpbb\extension\manager			$ext_manager
	 * @param \phpbb\filesystem\filesystem		$filesystem
	 * @param string							$root_path
	 * @param string							$php_ext
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\request\request $request,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\auth\auth $auth,
		\phpbb\language\language $language,
		\phpbb\extension\manager $ext_manager,
		\phpbb\filesystem\filesystem $filesystem,
		$root_path,
		$php_ext
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

		// Add language file
		$this->language->add_lang('attachment', 'imcger/imgupload');

		// Get name of the extension
		$metadata_manager = $this->ext_manager->create_extension_metadata_manager('imcger/imgupload');
		$this->ext_display_name = $metadata_manager->get_metadata('display-name');
	}

	/**
	 * Assigns the Ajax request app.php/imgupload/{order}
	 *
	 * @access public
	 */
	public function request($order)
	{
		// No ajax request, redirect to forum index
		if (!$this->request->is_ajax())
		{
			redirect($this->root_path . '/index.' . $this->php_ext);
		}

		// No user logged in, redirect in js to login page
		if ($this->user->data['user_id'] == ANONYMOUS)
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
	 *
	 * @var 	int		attach_id		contain attach id
	 * @var 	int		img_rotate_deg	contain rotate degree
	 * @var 	int		creation_time	creation time of token
	 * @var 	string	form_token		form token
	 *
	 * @return	array	Json arry with status, old and new attach id, new file size or error message
	 */
	private function save_image($img_attach_id, $img_rotate_deg)
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
	 *
	 * @var 	int		$attach_id	contain attach id
	 *
	 * @return	array	Json arry with attach id and file size
	 */
	private function image_size($attach_id)
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
	 *
	 * @param 	string	$path		Path to the image file
	 * @param	int		$deg		Rotation angle
	 *
	 * @return	int		$filesize	Image file size
	 */
	private function rotate_image($path, $deg)
	{
		$image = new \Imagick($path);
		$image->rotateImage('#000', $deg);
		$image->writeImage($path);
		$filesize = strlen($image->getImageBlob());
		$image->clear();

		return $filesize;
	}

	/**
	 * Generate json string
	 *
	 * @param 	int		$status			Status 0=id's, 3=redirect, 4=file not found, 5=error
	 * @param	string	$title			Messagebox title
	 * @param 	string	$message		Messagebox message
	 * @param	int		$old_attach_id	Previous attachment id
	 * @param 	int		$new_attach_id	New attachment id
	 * @param 	int		$file_size		New file size
	 *
	 * @return	string	$json
	 */
	private function json_response($status, $title = '', $message = '', $old_attach_id = 0, $new_attach_id = 0, $file_size = 0)
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
