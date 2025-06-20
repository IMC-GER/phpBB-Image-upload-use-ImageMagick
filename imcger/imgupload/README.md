# phpBB Image upload use ImageMagick

## Description
This extension is a further development of [canonknipser](https://www.phpbb.com/customise/db/author/canonknipser) [ImageMagick Thumbnailer](https://www.phpbb.com/customise/db/extension/imagemagick_thumbnailer).
The extension uses the PHP imagick class to modify uploaded images, thumbnails and avatars. It shows a preview image in attachments upload. If the values set in the ACP are exceeded by the image file, the image is being resized by the extension.
The extension supports JPEG, WEBP, GIF and PNG images. Other image formats, for example BMP, are converted to JPEG when resized.
This extension can change the image size and/or the image file size. It rotate images, thumbnails and avatars according to their EXIF information and it can remove the EXIF data from JPEG and WEBP files. Images and thumbnails can also be rotated manually.

[![Tests](https://github.com/IMC-GER/phpBB-Image-upload-use-ImageMagick/actions/workflows/tests.yml/badge.svg)](https://github.com/IMC-GER/phpBB-Image-upload-use-ImageMagick/actions/workflows/tests.yml)

#### Settings in User Control Panel
- No settings. 

#### Settings in Administration Control Panel
- Create thumbnail
- Thumbnail compression quality. 
- Insert full size image in post
- Maximum image width (Displayed in post)
- Image compression quality.
- Maximum image width. (Store on Server)
- Maximum image heigth.
- Maximum image file size.
- Remove EXIF data
- Resize avatar

## Screenshots
- [ACP](https://raw.githubusercontent.com/IMC-GER/images/main/screenshots/imgupload/en/imgupload_acp_en.jpg)
- [UCP - Edit avatar](https://raw.githubusercontent.com/IMC-GER/images/main/screenshots/imgupload/en/imgupload_ucp_en.jpg)
- [Post - upload attachments](https://raw.githubusercontent.com/IMC-GER/images/main/screenshots/imgupload/en/imgupload_upload_en.jpg)

## Requirements
- phpBB 3.3.0 or higher
- php 7.2 or higher
- php ImageMagick library installed

## Compatible with
- Fancybox (`lotusjeff/fancybox`)
- Fancybox (`imcger/fancybox`)
- Lightbox (`vse/lightbox`)

## Installation
Copy the extension to `phpBB3/ext/imcger/imgupload`.
Go to "ACP" > "Customise" > "Manage extensions" and enable the "Image upload use ImageMagick" extension.

For full functionality "Maximum file size" in "ACP" > "Posting" > "Attachment settings" must be set to 0 and in "ACP" > "Posting" > "Manage attachment extension groups" > "Settings in Images" > "Maximum file size" must be set to 0. This is done automatically during the migration.

For full functionality "Maximum image dimensions" in "ACP" > "Posting" > "Attachment settings" must be set to 0. This is done automatically during the migration.
For full functionality "Maximum avatar file size" in "ACP" > "Board configuration" > "Avatar settings" must be set to 0. This is done automatically during the migration.

## Update
- Navigate in the ACP to `Customise -> Manage extensions`.
- Click the `Disable` link for "Image upload use ImageMagick".
- Delete the `imgupload` folder from `phpBB3/ext/imcger/`.
- Copy the extension to `phpBB3/ext/imcger/imgupload`.
- Go to "ACP" > "Customise" > "Manage extensions" and enable the "Image upload use ImageMagick" extension.

## Changelog

### v1.5.0 (16-06-2025)
- Fixed Language variables are lost when 'Create thumbnail' is deactivated.
- Fixed Error message from Composer Validator
- Fixed Each PHP statement must be on a line by itself
- Set max php version to 8.4, min version to 7.4
- Changed PHP code has been updated to include data types for the variable
- Changed security query in the Ajax controller
- Changed sql query from string to array
- Revised language files
- Moved twig macros in separate file

### v1.4.6 (14-07-2024)
- Changed Determining the image file size.
- Fixed Submit buttons after closing the confirm box without function.

### v1.4.5 (23-06-2024)
- Fixed Some servers do not send the new file size after uploading images.

### v1.4.4 (19-06-2024)
- Added Set max filesize in attachment extension groups on migraton to 0.
- Fixed Don't work in none standard attachment extension groups

### v1.4.3 (20-02-2024)
- Fixed [Error in Post](https://www.phpbb.de/community/viewtopic.php?p=1426071#p1426071)

### v1.4.2 (23-01-2024)
- Improved error handling in preview
- Fixed multiple preview hashes

### v1.4.1 (18-01-2024)
- Set save-button background green when image is rotated
- Open confirmbox if the image has not been saved after rotation

### v1.4.0 (14-01-2024)
- Revised JS code
- Fixed JS code in ACP don't work with radio buttons
- Fixed if the attachment thumbnail is too small, it will not be centered
- Added security measures for Ajax request
- Added updating the image file size in the row after uploading or rotating
- Changed error handling for missing thumbnail file to a warning message
- Changed compression method for png files
- Changed phpBB min. version to 3.3.0

### v1.3.2 (13-12-2023)
- Fixed error when upload none image file
- Fixed upload aborts sporadically with large files
- Fixed don't change attachment id in IMG bbCode after image rotated
- Changed Symfony json response tp phpBB response
- Added support for Toggle Control from LukeWCS

### v1.3.1 (15-10-2023)
- Fixed typos
- Fixed error when upload file after store rotated image
- Changed function for renumbering the attachmend BBCode

### v1.3.0 (13-10-2023)
- Added manual image rotation

### v1.2.2 (29-07-2023)
- Conside BBCode Settings
- Fixed scrollbar on large images in attachbox
- Fixed button misaligned

### v1.2.1 (09-05-2023)
- Changed sql query for allowed image extensions
- Changed compression method for png images
- Added possibility to insert the attachment as an fullsize image in the post message
- Added maximum display size for images in posts displayed in the attachment box without thumbnails

### v1.1.1 (18-03-2023)
- Fixed don't show preview thumbnail in attachments upload
- Added support for Lightbox and Fancybox
- Added show language author in ACP dialog
- Changed check system requirement

### v1.1.0 (06-02-2023)
- Added preview image in attachments upload
- Changed in ACP from radio to toggle button
- Fixed missing language variable in ACP controller

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
