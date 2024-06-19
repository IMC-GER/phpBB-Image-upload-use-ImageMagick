<?php
/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @copyright (c) 2018, canonknipser, ImageMagick Thumbnailer, http://canonknipser.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \FastImageSize\FastImageSize */
	protected $imagesize;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/**
	 * Constructor for listener
	 *
	 * @param \phpbb\config\config				$config		phpBB config
	 * @param \phpbb\language\language			$language	phpBB language
	 * @param \FastImageSize\FastImageSize		$imagesize	FastImageSize object
	 * @param \phpbb\db\driver\driver_interface	$db			phpBB DataBase
	 * Qparam \phpbb\template\template			$template	phpBB template
	 * @param \phpbb\extension\manager			$ext_manager
	 * @param \phpbb\controller\helper			$helper
	 *
	 * @access public
	 */
	public function __construct
	(
		\phpbb\config\config $config,
		\phpbb\language\language $language,
		\FastImageSize\FastImageSize $imagesize,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\template\template $template,
		\phpbb\extension\manager $ext_manager,
		\phpbb\controller\helper $helper
	)
	{
		$this->config		= $config;
		$this->language		= $language;
		$this->imagesize	= $imagesize;
		$this->db			= $db;
		$this->template		= $template;
		$this->ext_manager	= $ext_manager;
		$this->helper		= $helper;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.page_header'							 => 'set_template_vars',
			'core.ucp_display_module_before'			 => 'new_profil_avatar_text',
			'core.thumbnail_create_before'				 => 'imcger_create_tumbnail',
			'core.modify_uploaded_file'					 => 'imcger_modify_uploaded_file',
			'core.avatar_driver_upload_move_file_before' => 'imcger_modify_uploaded_avatar',
			'core.viewtopic_modify_post_row'			 => 'imcger_viewtopic_modify_post_row',
			'core.posting_modify_template_vars'			 => 'imcger_posting_modify_template_vars',
		];
	}

	/**
	 * Add language file for avatar upload page
	 */
	public function new_profil_avatar_text()
	{
		if ($this->config['imcger_imgupload_avatar_resize'])
		{
			$this->language->add_lang('ucp','imcger/imgupload');
		}
	}

	/**
	 * Set template vars
	 *
	 * @return null
	 * @access public
	 */
	public function set_template_vars()
	{
		// Load language vars for buttons in post editor
		if ($this->config['img_create_thumbnail'])
		{
			$this->language->add_lang('attachment','imcger/imgupload');
		}

		$allowed_images = [];
		$img_max_thumb_width = $this->config['imcger_imgupload_img_max_thumb_width'];

		// Get image groups
		$sql_ary =  'SELECT group_id FROM ' . EXTENSION_GROUPS_TABLE	. ' WHERE cat_id = 1';
		$result_group = $this->db->sql_query($sql_ary);

		while ($group_row = $this->db->sql_fetchrow($result_group))
		{
			// Get extension from image groups
			$sql_ary =  'SELECT extension FROM ' . EXTENSIONS_TABLE	. ' WHERE group_id = ' . (int) $group_row['group_id'];
			$result_ext = $this->db->sql_query($sql_ary);

			while ($row = $this->db->sql_fetchrow($result_ext))
			{
				$allowed_images[] = $row['extension'];
			}
		}
		$this->db->sql_freeresult();

		$metadata_manager = $this->ext_manager->create_extension_metadata_manager('imcger/imgupload');

		$this->template->assign_vars([
			'IMGUPLOAD_TITLE'		  => $metadata_manager->get_metadata('display-name'),
			'IUL_ALLOWED_IMAGES'	  => json_encode($allowed_images),
			'IUL_IMG_SET_INLINE'	  => $this->config['imcger_imgupload_image_inline'],
			'IUL_IMG_MAX_THUMB_WIDTH' => $img_max_thumb_width ? $img_max_thumb_width . 'px' : false,
			'IMGUPLOAD_TITLE' 		  => $metadata_manager->get_metadata('display-name'),
			'U_IUL_SAVE_IMAGE'  	  => $this->helper->route('imcger_imgupload_save_image_controller'),
		]);
	}

	/**
	 * Create a thumbnail using IMagick
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function imcger_create_tumbnail($event)
	{
		// create a new instance of ImageMagick and load current image
		$thumbnail = new \Imagick(realpath($event['source']));

		// rotate the image according it's orientation flag
		$thumb = $this->image_auto_rotate($thumbnail);

		// changed the image heigth and width when it rotate
		if ($thumb['rotate'])
		{
			$new_width = $event['new_height'];
			$new_height = $event['new_width'];
		}
		else
		{
			$new_width = $event['new_width'];
			$new_height = $event['new_height'];
		}

		// resize the Image
		$thumbnail->resizeImage($new_width, $new_height, \Imagick::FILTER_LANCZOS, 1);

		// set image format
		$image_format = $this->set_image_format($thumbnail, $event['mimetype']);

		// compression quality is read from config, set in ACP
		$this->set_image_compression($thumbnail, $this->config['imcger_imgupload_tum_quality']);

		// strip EXIF data and image profile
		if ($this->config['imcger_imgupload_del_exif'] && ($image_format == 'JPEG' || $image_format == 'WEBP'))
		{
			$thumbnail->stripImage();
		}

		// store the image
		$thumbnail_created = $thumbnail->writeImage($event['destination']);
		$thumbnail->clear();

		// if image format change to JPEG set mimetype to jpeg
		if ($image_format == 'JPEG' && $event['mimetype'] != 'image/jpeg' && $thumbnail_created)
		{
			$event['mimetype'] = 'image/jpeg';
		}

		// set return value
		$event['thumbnail_created'] = $thumbnail_created;
	}

	/**
	 * Modify upload image using IMagick
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function imcger_modify_uploaded_file($event)
	{
		if ($event['is_image'])
		{
			$write_image		= false; // set to true wenn image attribute changed
			$image_max_width	= $this->config['imcger_imgupload_max_width'];
			$image_max_height	= $this->config['imcger_imgupload_max_height'];
			$image_quality		= $this->config['imcger_imgupload_img_quality'];
			$image_del_exif		= $this->config['img_strip_metadata'];
			$image_max_filesize = $this->config['imcger_imgupload_max_filesize'];
			$filesize			= $event['filedata']['filesize'];

			// get file path
			$file_path = join('/', [trim($this->config['upload_path'], '/'), trim($event['filedata']['physical_filename'], '/')]);

			// get image dimension and type
			$size = $this->imagesize->getImageSize($file_path);

			if ($size === false)
			{
				return false;
			}

			// if image no jpeg or webp and image properties smaller then maximum values do nothing
			if (!(($size['type'] == IMAGETYPE_JPEG || $size['type'] == IMAGETYPE_WEBP ) && $image_del_exif) &&
				(!$image_max_width || $image_max_width >= $size['width']) &&
				(!$image_max_height || $image_max_height >= $size['height']) &&
				(!$image_max_filesize || $image_max_filesize >= $filesize)
			)
			{
				return true;
			}

			// create a new instance of ImageMagick and load current image
			$image = new \Imagick($file_path);

			// rotate the image according it's orientation flag
			$img = $this->image_auto_rotate($image);

			// resize the image
			$write_image = $this->resize_image($image, $image_max_width, $image_max_height);

			// set image format
			$image_format = $this->set_image_format($image, $event['filedata']['mimetype']);

			// when not resize don`t changed image quality when it less then quality set in ACP
			if (($image_format == 'JPEG' || $image_format == 'WEBP') && ($write_image || $image_quality < $image->getImageCompressionQuality()))
			{
				// set compression quality is read from config
				$this->set_image_compression($image, $image_quality);

				$write_image = true;
			}

			if ($image_format == 'PNG')
			{
				// set compression quality, second parameter isn't importent for PNG
				$this->set_image_compression($image);
			}

			// strip EXIF data and image profile
			if ($image_del_exif && ($image_format == 'JPEG' || $image_format == 'WEBP'))
			{
				$image->stripImage();

				$write_image = true;
			}

			// shrink file size wenn greater then set in ACP
			if ($image_max_filesize && $filesize > $image_max_filesize)
			{
				// set compression quality is read from config
				$this->set_image_compression($image, $image_quality);

				// set the max file size for the image
				$filesize = $this->image_auto_length($image, $image_max_filesize);

				$write_image = true;
			}

			// store the image when attribute change
			if ($write_image || $img['changed'])
			{
				$write = $image->writeImage($file_path);

				// if image store set new filedata
				if ($write)
				{
					// set return value new file size and filedata
					$filedata_array = $event['filedata'];

					// set new file size
					$filedata_array['filesize'] = $filesize ? $filesize : $image->getImageBlob();

					// if image format change to JPEG set filedata to JPEG
					if ($image_format == 'JPEG' && $event['filedata']['mimetype'] != 'image/jpeg')
					{
						$filedata_array['mimetype'] = 'image/jpeg';
						$filedata_array['extension'] = 'jpg';
						$filedata_array['real_filename'] .= '.jpg';
					}

					$event['filedata'] = $filedata_array;
				}

				$image->clear();
			}
		}
	}

	/**
	 * Modify upload avatar image using IMagick
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	function imcger_modify_uploaded_avatar($event)
	{
		if ($this->config['imcger_imgupload_avatar_resize'])
		{
			$avatar_max_width	 = $this->config['avatar_max_width'];
			$avatar_max_height	 = $this->config['avatar_max_height'];
			$avatar_file		 = $event['filedata']['filename'];

			// Get image dimension
			$size = $this->imagesize->getImageSize($avatar_file);

			if ($size === false)
			{
				return false;
			}

			// when avatar image file to great modify it
			if ($size['width'] > $avatar_max_width || $size['height'] > $avatar_max_height)
			{
				// create a new instance of ImageMagick and load current image
				$avatar = new \Imagick($avatar_file);

				// rotate the image according it's orientation flag
				$this->image_auto_rotate($avatar);

				// resize the image
				$this->resize_image($avatar, $avatar_max_width, $avatar_max_height);

				// set compression quality is read from config
				$this->set_image_compression($avatar);

				// set image format
				$image_format = $this->set_image_format($avatar, $event['filedata']['mimetype']);

				// strip EXIF data and image profile
				if ($image_format == 'JPEG' || $image_format == 'WEBP')
				{
					$avatar->stripImage();
				}

				// store the avatar
				$write = $avatar->writeImage($avatar_file);

				if ($write === false)
				{
					return false;
				}

				// set return value new file size
				$filedata_array = $event['filedata'];
				$filedata_array['filesize'] = $avatar->getImageBlob();
				$event['filedata'] = $filedata_array;

				$avatar->clear();
			}
		}
	}

	/**
	 * Modify post data
	 * Don't display attachments when shown as image in post message
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	function imcger_viewtopic_modify_post_row($event)
	{
		$row				= $event['row'];
		$post_attachments	= $event['attachments'];

		// Do nothing when no attachment
		if (empty($post_attachments[$row['post_id']]))
		{
			return;
		}

		// Create array with image id that insert in post message
		preg_match_all('#\[img\][\w\<\>\/\.?=&;]*[^\[](id=\d+)\<e\>\[\/img\]#', $row['post_text'], $matches);

		// Find image in attachment, delete atachment
		foreach ($matches[1] as $image)
		{
			foreach ($post_attachments[$row['post_id']] as $key => $attachment)
			{
				if (strpos($attachment, $image))
				{
					unset($post_attachments[$row['post_id']][$key]);
				}
			}
		}

		$post_row = $event['post_row'];

		// Set template vars
		$post_row['S_HAS_ATTACHMENTS']		= !empty($post_attachments[$row['post_id']]) ? true : false;
		$post_row['S_MULTIPLE_ATTACHMENTS'] = !empty($post_attachments[$row['post_id']]) && count($post_attachments[$row['post_id']]) > 1;

		$event['post_row'] = $post_row;
		$event['attachments'] = $post_attachments;
	}

	/**
	 * Modify post data for post editor preview
	 * Don't display attachments when shown as image in post message
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	function imcger_posting_modify_template_vars($event)
	{
		// Get message text and attachment data
		$message_parser = $event['message_parser'];
		$message = $message_parser->message;
		$attachment_data = $message_parser->attachment_data;

		// Create array with attachment id that insert in post message
		preg_match_all('#\[img\][\w\<\>\/\.?=&;]*[^\[]id=(\d+)\[\/img\]#', $message, $matches);
		$matches[1] = array_unique($matches[1]);

		// Check if all attachments insert in post message
		$display_attachmentbox = false;
		foreach ($attachment_data as $attachment)
		{
			if (!in_array($attachment['attach_id'], $matches[1]))
			{
				$display_attachmentbox = true;
				break;
			}
		}

		// Set variable for JS to hide attachment in post editors preview
		$this->template->assign_vars([
			'IUL_NOT_DISPLAYED_ATTACHMENTS' => json_encode($matches[1]),
			'IUL_NOT_DISPLAY_ATTACHMENTBOX' => (int) !$display_attachmentbox,
		]);
	}

	/**
	 * resize image if image to large
	 *
	 * @param object	$image	image object
	 *
	 * @return bool		$img_resize	images is resize
	 */
	function resize_image($image, $max_width, $max_height)
	{
		$img_resize = false;

		// get image dimensions
		$img_geo = $image->getImageGeometry();
		list('width' => $width, 'height' =>  $height, ) = $img_geo;

		// image aspect ratio
		$side_ratio = $width / $height;

		// set new images width
		if ($max_width && $max_width < $width)
		{
			$width = $max_width;
			$height = round($width / $side_ratio);

			$img_resize = true;
		}

		// set new images height
		if ($max_height && $max_height < $height)
		{
			$height = $max_height;
			$width = round($height * $side_ratio);

			$img_resize = true;
		}

		// when changed dimensions resize the Image
		if ($img_resize)
		{
			$image->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, false);
		}

		return $img_resize;
	}

	/**
	 * set the image format for the generated image
	 *
	 * @param object	$image		image object
	 * @param string	$mimetype	mimetype of the image in phpBB internal format
	 *
	 * @return string	$imageformat images format
	 */
	function set_image_format($image, $mimetype)
	{
		// Check the mimetype and set the appropriate type for the image
		switch ($mimetype)
		{
			case 'image/jpeg':
				$imageformat = 'JPEG';
			break;

			case 'image/png':
				$imageformat = 'PNG';
			break;

			case 'image/gif':
				$imageformat = 'GIF';
			break;

			case 'image/webp':
				$imageformat = 'WEBP';
			break;

			default:
				$imageformat = 'JPEG';
			break;
		}

		$image->setImageFormat($imageformat);

		return $imageformat;
	}

	/**
	 * set the compression value for the generated image
	 *
	 * @param object	$image		image object
	 * @param integer	$quality	image quality value
	 */
	function set_image_compression($image, $quality = 80)
	{
		$image_format = $image->getImageFormat();

		switch ($image_format)
		{
			case 'JPEG':
				$image->setImageCompression(\Imagick::COMPRESSION_JPEG);
				$image->setImageCompressionQuality($quality);
			break;

			case 'PNG':
				if ($image->getImageColors() > 256)
				{
					$image->quantizeImage(256, \Imagick::COLORSPACE_SRGB, 16, false, false);
					$image->setImageType(\Imagick::IMGTYPE_TRUECOLORMATTE);
				}
			break;

			case 'WEBP':
				$image->setOption('webp:alpha-compression', 1);
				$image->setOption('webp:alpha-filtering', 1);
				$image->setOption('webp:method', 6);
				$image->setImageCompressionQuality($quality);
			break;

			default:
				// do nothing
			break;
		}
	}

	/**
	 * rotate the generated image
	 *
	 * @param object	$image		image object
	 *
	 * @return array	changed		orientation changed
	 * 					rotate		rotate 90 degree
	 */
	function image_auto_rotate($image)
	{
		$is_rotate	= false; // true, when orientation change between portrait and landscape
		$is_changed = true;  // true, when orientation change

		// read the orientation from the image
		if (!($image_orientation = $image->getImageOrientation()))
		{
			// orientation flag not available
			$image_orientation = \Imagick::ORIENTATION_UNDEFINED;
		}

		switch ($image_orientation)
		{
			case \Imagick::ORIENTATION_UNDEFINED:
				// do nothing
				$is_changed = false;
			break;

			case \Imagick::ORIENTATION_TOPLEFT:
				// do nothing
				$is_changed = false;
			break;

			case \Imagick::ORIENTATION_TOPRIGHT:
				$image->flopImage();
			break;

			case \Imagick::ORIENTATION_BOTTOMRIGHT:
				$image->rotateImage("#000", 180);
			break;

			case \Imagick::ORIENTATION_BOTTOMLEFT:
				$image->flipImage();
			break;

			case \Imagick::ORIENTATION_LEFTTOP:
				$image->transposeImage();
				$is_rotate = true;
			break;

			case \Imagick::ORIENTATION_RIGHTTOP:
				$image->rotateImage("#000", 90);
				$is_rotate = true;
			break;

			case \Imagick::ORIENTATION_RIGHTBOTTOM:
				$image->transverseImage();
				$is_rotate = true;
			break;

			case \Imagick::ORIENTATION_LEFTBOTTOM:
				$image->rotateImage("#000", 270);
				$is_rotate = true;
			break;

			default:
				// do nothing
				$is_changed = false;
			break;
		}

		// set the orientation from the Image
		$image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);

		return ['changed' => $is_changed, 'rotate' => $is_rotate];
	}

	/**
	 * resize the image file
	 *
	 * @param object	$image			image object
	 * @param integer	$new_image_size	new size of the image
	 *
	 * @return integer	$filesize		file size of the image after shrink
	 */
	function image_auto_length($image, $new_image_size)
	{
		// get image dimensions
		$img_geo = $image->getImageGeometry();
		list('width' => $width, 'height' =>  $height, ) = $img_geo;

		$filesize = strlen($image->getImageBlob());

		// image aspect ratio
		$side_ratio = $width / $height;

		while ($filesize >  $new_image_size)
		{
			// calculate the image side in relation to the image area
			$size_ratio = $filesize / $new_image_size;
			$xh = sqrt($width * $height / $size_ratio / $side_ratio);

			// calculate the different to new height
			$dh = ($height - $xh) * 0.9;
			$dh = $dh > 10 ? $dh : 10;

			// set image dimensions
			$height -= $dh;
			$width	 = $height * $side_ratio;

			// resize the image
			$image->resizeImage(round($width), round($height), \Imagick::FILTER_LANCZOS, 1, false);

			// get the file size
			$filesize = strlen($image->getImageBlob());
		}
		return $filesize;
	}
}
