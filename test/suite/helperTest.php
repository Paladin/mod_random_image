<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_random_image
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
include_once __DIR__ . '/../../helper.php';
include_once __DIR__ . '/../fixtures/mockParams.php';
/**
 *	modRandomImageHelperTest
 *
 *	Proposed phpunit testing approach for the module Random Image
 */
class modRandomImageHelperTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	module	the module under test
	 */
    protected $module;
	/**
	 * @var	folder	the test folder name
	 */
    protected $folder = 'images';
	/**
	 * @var	params	the mocked params array for the module
	 */
    protected $params;
	/**
	 *	setUp
	 *
	 *	Creates the module under test and loads the mockParams object into it
	 */
    protected function setUp()
    {
    	$this->params = new mockParams();
    	
    	$this->params->params['width'] = null;
		$this->params->params['height'] = null;
		$this->params->params['link'] = '/';
		$this->params->params['folder'] = $this->folder;
		$this->params->params['type'] = 'jpg';
		$this->params->params['moduleclass_sfx'] = null;
		$this->params->params['layout'] = null;

    	$this->module = new modRandomImageHelper($this->params);
    }
	/**
	 *	testCreatedModule
	 *
	 *	Tests the just-created module to ensure it's set up properly.
	 */
    public function testCreatedModule()
    {
		$this->assertAttributeEquals($this->params, 'params', $this->module);
	}
}
?>
