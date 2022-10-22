# phpBB Image upload use ImageMagick

## Description
This extension is a further development of [canonknipser](https://www.phpbb.com/customise/db/author/canonknipser) [ImageMagick Thumbnailer](https://www.phpbb.com/customise/db/extension/imagemagick_thumbnailer).
The extension uses the PHP imagick class to modify uploaded images, thumbnails and avatars. If the values set in the ACP are exceeded by the image file, the image ​is being resized by the extension.
The extension supports JPEG, WEBP, GIF and PNG images. Other image formats, for example BMP, ​are ​convert​ed​ to JPEG when resize​d.
This extension can change the image size and/or the image file size. It rotate images, thumbnails and avatars according to their EXIF information and it can remove the EXIF data from JPEG and WEBP files.

#### Settings in User Control Panel
- No settings. 

#### Settings in Administration Control Panel
- Thumbnail compression quality. 
- Image compression quality. 
- Maximum image width.
- Maximum image heigth.
- Maximum image file size.
- Remove EXIF data
- Resize avatar

## Screenshots
- [ACP](https://raw.githubusercontent.com/IMC-GER/images/main/screenshots/imgupload/en/imgupload_acp_en.jpg)
- [UCP - Edit avatar](https://raw.githubusercontent.com/IMC-GER/images/main/screenshots/imgupload/en/imgupload_ucp_en.jpg)

## Requirements
- phpBB 3.2.4 or higher
- php 7.1 or higher
- php ImageMagick library installed

## Installation
Copy the extension to `phpBB3/ext/imcger/imgupload`.
Go to "ACP" > "Customise" > "Manage extensions" and enable the "Image upload use ImageMagick" extension.

For full functionality "Maximum file size" in "ACP" > "Posting" > "Attachment settings" must be set to 0. This is done automatically during the migration.

For full functionality "Maximum image dimensions" in "ACP" > "Posting" > "Attachment settings" must be set to 0. This is done automatically during the migration.
For full functionality "Maximum avatar file size" in "ACP" > "Board configuration" > "Avatar settings" must be set to 0. This is done automatically during the migration.

## Update
- Navigate in the ACP to `Customise -> Manage extensions`.
- Click the `Disable` link for "Image upload use ImageMagick".
- Delete the `imgupload` folder from `phpBB3/ext/imcger/`.
- Copy the extension to `phpBB3/ext/imcger/imgupload`.
- Go to "ACP" > "Customise" > "Manage extensions" and enable the "Image upload use ImageMagick" extension.

## Changelog

### v1.0.1 (22-10-2022)
- Code changes

### v1.0.0 (31-07-2022)
- Added avatar resize
- Added version check
- Added controller for ACP template
- Update check system requirement
- Fixed EXIF orientation handling 

### v0.1.0 (31-05-2022)
- Fixed migrations

### v0.0.4 (18-03-2022)
- Cleanup code

### v0.0.3 (20-02-2022)
- Code changes

### v0.0.2 (19-02-2022)
- Code changes

### v0.0.1 (18-02-2022)

## Uninstallation
- Navigate in the ACP to `Customise -> Manage extensions`.
- Click the `Disable` link for "Image upload use ImageMagick".
- To permanently uninstall, click `Delete Data`, then delete the `imgupload` folder from `phpBB3/ext/imcger/`.

## License
[GPLv2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)
