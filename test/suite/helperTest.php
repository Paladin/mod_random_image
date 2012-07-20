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
	/**
	 *	casesRandomImage
	 *
	 *	Provides test cases for getRandomImage
	 */
	public function casesRandomImage()
	{
		return array(
			array( null, null, array(100,100), array(61,73) ),
			array( null, 200, array(100,100), array(61,73) ),
			array( null, 400, array(100,100), array(61,73) ),
			array( 400, null, array(400,400), array(246,292) ),
			array( 400, 200, array(273,324), array(200,200) ),
			array( 400, 400, array(400,400), array(246,292) ),
			array( 800, null, array(416,450), array(304,277) ),
			array( 800, 200, array(273,324), array(200,200) ),
			array( 800, 400, array(416,450), array(304,277) ),
		);
	}
	/**
	 *	testGetRandomImage
	 *
	 * @dataProvider casesRandomImage
	 */
	public function testGetRandomImage($testWidth, $testHeight, 
		$expectedWidths, $expectedHeights)
	{
		$this->params->params['width'] = $testWidth;
		$this->params->params['height'] = $testHeight;
	    $myImages = array(
   			(object)array("name" => 'EQ.jpg', "folder" => 'images', 
   				'width' => 416, 'height' => 304),
   			(object)array("name" => 'hobok.jpg', "folder" => 'images', 
   				'width' => 450, 'height' => 277),
   		);
		$image = $this->module->getRandomImage($myImages);

		$this->assertContains( $image, $myImages); /* tests for object in array sent */
		$this->assertContains( (int)$image->width, $expectedWidths, "Incorrect Width");
		$this->assertContains( (int)$image->height, $expectedHeights, "Incorrect Height");
	}
	/**
	 *	testGetImage
	 */
	public function testGetImages()
	{
    	$mockMe = $this->getMockClass('modRandomImageHelper', array('getFolder'));
    	$mockMe::staticExpects($this->any())
    		->method('getFolder')
    		->with($this->equalTo($this->folder))
    		->will($this->returnValue($this->folder));

   		$images = $mockMe::getImages($this->folder, $this->params->params['type'] = 'jpg');
   		
   		$this->assertEquals($images, array(
   			(object)array("name" => 'EQ.jpg', "folder" => 'images'),
   			(object)array("name" => 'hobok.jpg', "folder" => 'images'),
   		));
	}
}
?>
