/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

$('input[name=img_create_thumbnail]').on('change', function () {
	if ($('input[name=img_create_thumbnail]:checked').last().val() == 1) {
		$('input[name=imcger_imgupload_image_inline]').prop('disabled', false);
	} else {
		$('input[name=imcger_imgupload_image_inline]').attr('type') == 'radio' ?
			$('input[name=imcger_imgupload_image_inline]').last().click() : $('input[name=imcger_imgupload_image_inline]').prop('checked', false);
		$('input[name=imcger_imgupload_image_inline]').prop('disabled', true);
	}
});

$('input[name=imcger_imgupload_image_inline]').trigger('change');
