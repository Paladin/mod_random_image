<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_random_image
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modRandomImageHelper
{
	/**
	 * @var	params	the params array for the module
	 */
	protected $params;
	
	/**
	 * @var	cms	the glue object for talking to the cms
	 */
	protected $cms;
	
	/**
	 *
	 * Constructor.
	 *
	 * @param	params	JRegistry		params
	 * @param	cms		CMS Glue object	glue for cms API
	 *
	 * @return	modRandomImageHelper object
	 *
	 * @since Unspecified Possible Future Version
	 */
	public function __construct( $params, $cms )
	{
		$this->params = $params;
		$this->cms = $cms;
	}
	
	public function getRandomImage($images)
	{
		$width	= $this->params->get('width');
		$height	= $this->params->get('height');
		$i			= count($images);
		$random		= mt_rand(0, $i - 1);
		$image		= $images[$random];
		$size		= getimagesize(JPATH_BASE . '/' . $image->folder . '/' . $image->name);


		if ($width == '') {
			$width = 100;
		}

		if ($size[0] < $width) {
			$width = $size[0];
		}

		$coeff = $size[0]/$size[1];
		if ($height == '') {
			$height = (int) ($width/$coeff);
		} else {
			$newheight = min ($height, (int) ($width/$coeff));
			if ($newheight < $height) {
				$height = $newheight;
			} else {
				$width = $height * $coeff;
			}
		}

		$image->width	= $width;
		$image->height	= $height;
		$image->folder	= str_replace('\\', '/', $image->folder);

		return $image;
	}

	public function getImages($theFolder, $type)
	{
		$folder = $this->getFolder($theFolder);
		$directory = JPATH_BASE . '/' . $folder;
		$files = $this->getFilenameArray($directory);

		return $this->filterFilenameArray($files, $type, $folder);
	}

	public function getFolder($theFolder)
	{ 
		$folder = $this->removeLiveSite($theFolder);
		$folder = $this->makeRelativePath($folder,JPATH_SITE);
		$folder = str_replace('\\', DIRECTORY_SEPARATOR, $folder);
		$folder = str_replace('/', DIRECTORY_SEPARATOR, $folder);

		return $folder;
	}
	/**
	 *
	 * createOutput.
	 *
	 * This method outputs through the selected template the results of the
	 * module.
	 *
	 * @return	none
	 *
	 * @since Unspecified Possible Future Version
	 */
	public function createOutput()
	{
 		$link	= $this->params->get('link');
		$moduleclass_sfx = $this->params->get('moduleclass_sfx');

		$images	= $this->getImages($this->params->get('folder'),
			$this->params->get('type', 'jpg')
		);

		if (!count($images)) {
			echo $this->cms->getTranslatedText('MOD_RANDOM_IMAGE_NO_IMAGES');
		} else {
			$image = $this->getRandomImage($images);
			require $this->cms->getLayoutPath($this->params->get('layout'));
		}
	}
	/**
	 *	removeLiveSite
	 *
	 *	Removes the current live site from the folder string, if present.
	 *
	 *	@param	path	String	the path/uri to the folder
	 *	@private
	 *
	 *	@return	string
	 */
	 private function removeLiveSite( $path )
	 {
		$LiveSite	= $this->cms->getBaseURL();

		// if folder includes livesite info, remove
		if ($this->cms->strpos($path, $LiveSite) === 0) {
			$path = str_replace($LiveSite, '', $path);
		}
		
		return $path;
	 }
	/**
	 *	makeRelativePath
	 *
	 *	Makes the given path relative to another path, if possible.
	 *
	 *	@param	path	String	the path/uri to the folder
	 *	@param	base	String	the path to base it off.
	 *	@private
	 *
	 *	@return	string
	 */
	 private function makeRelativePath( $path, $base )
	 {
		// if folder includes absolute path, remove
		if ($this->cms->strpos($path, $base) === 0) {
			$path= str_replace($base . '/', '', $path);
		}
		return $path;
	 }
	/**
	 *	getFilenameArray
	 *
	 *	Gets the names of the files in the current directory.
	 *
	 *	@param	directory	String	the directory to look in
	 *	@private
	 *
	 *	@return	array
	 */
	 private function getFilenameArray( $directory )
	 {
		$files	= array();

		if (!is_dir($directory)) { return $files; }
		
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..' && $file != 'CVS' &&
						$file != 'index.html' && !is_dir($directory . '/' . $file)) {
					$files[] = $file;
				}
			}
		}
		closedir($handle);
		
		return $files;
	 }
	/**
	 *	filterFilenameArray
	 *
	 *	Checks an array for files of a given type.
	 *
	 *	@param	file	array	the array of file names
	 *	@param	type	String	the regex string for filr type matches
	 *	@param	folder	String	path relative to site base of the files
	 *	@private
	 *
	 *	@return	array
	 */
	 private function filterFilenameArray( $files, $type, $folder )
	 {
		$images	= array();

		$i = 0;
		foreach ($files as $img)
		{
			if (preg_match('/'.$type.'/', $img)) {
				$images[$i] = new stdClass;
				$images[$i]->name	= $img;
				$images[$i]->folder	= $folder;
				$i++;
			}
		}
		
		return $images;
	 }
}
