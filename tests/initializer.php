<?php

/* vim: set noexpandtab tabstop=8 shiftwidth=8 softtabstop=8: */
/**
 * The MIT License
 *
 * Copyright 2012 Eric VILLARD <dev@eviweb.fr>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @package     modinit
 * @author      Eric VILLARD <dev@eviweb.fr>
 * @copyright	(c) 2012 Eric VILLARD <dev@eviweb.fr>
 * @license     http://opensource.org/licenses/MIT MIT License
 */

namespace evidev\fuelphp\modinit\test;

use \evidev\fuelphp\modinit\test\helpers\Helper;

/**
 * initialize modules
 * 
 * @package     modinit
 * @author      Eric VILLARD <dev@eviweb.fr>
 * @copyright	(c) 2012 Eric VILLARD <dev@eviweb.fr>
 * @license     http://opensource.org/licenses/MIT MIT License
 * @group	modinit
 */
class Initializer extends \Fuel\Core\TestCase
{

	const BOOTSTRAP_MOD = 'bootstrap';
	const INITFUNCTION_MOD = 'initfunction';
	const INITMETHOD_MOD = 'initmethod';
	const NOCOMPLIANT_MOD = 'nocompliant';

	/**
	 * set up the test environment
	 */
	public function setUp()
	{
		Helper::init();
	}

	/**
	 * revert to initial state
	 */
	public function tearDown()
	{
		Helper::restore();
	}

	/**
	 * @covers evidev\fuelphp\modinit\Initializer::init
	 */
	public function testInit_auto()
	{
		$modules = Helper::get_modules();
		Helper::always_load();

		// check environment
		$config = \Config::get('always_load');
		//$this->assertArrayHasKey('modinit', $config['packages']);
		foreach ($modules as $mod)
		{
			$this->assertArrayHasKey($mod, $config['modules']);
		}

		// check if constants exist
		$bs_loaded = strtoupper(self::BOOTSTRAP_MOD).'_LOADED';
		$if_loaded = strtoupper(self::INITFUNCTION_MOD).'_LOADED';
		$im_loaded = strtoupper(self::INITMETHOD_MOD).'_LOADED';
		$nc_loaded = strtoupper(self::NOCOMPLIANT_MOD).'_LOADED';
		$this->assertFalse(Helper::is_loaded($bs_loaded));
		$this->assertFalse(Helper::is_loaded($if_loaded));
		$this->assertFalse(Helper::is_loaded($im_loaded));
		$this->assertFalse(Helper::is_loaded($nc_loaded));

		// start calling mode
		Helper::set_calling(true);
		
		// check whether the package is already loaded
		$already_loaded = \Package::loaded('modinit');
		
		// initialize modules
		\Fuel::always_load();

		if ($already_loaded) {
			\evidev\fuelphp\modinit\Initializer::init();
		}

		$this->assertTrue(Helper::is_loaded($bs_loaded));
		$this->assertTrue(Helper::is_loaded($if_loaded));
		$this->assertTrue(Helper::is_loaded($im_loaded));
		$this->assertFalse(Helper::is_loaded($nc_loaded));
	}

	/**
	 * @covers evidev\fuelphp\modinit\Initializer::init
	 */
	public function testInit_manual()
	{
		$this->tearDown();
		Helper::manual_load();

		// check environment
		$paths = \Config::get('module_paths');
		$this->assertContains(Helper::get_module_path(), $paths);

		// check if constants exist
		$bs_loaded = strtoupper(self::BOOTSTRAP_MOD).'_LOADED';
		$if_loaded = strtoupper(self::INITFUNCTION_MOD).'_LOADED';
		$im_loaded = strtoupper(self::INITMETHOD_MOD).'_LOADED';
		$nc_loaded = strtoupper(self::NOCOMPLIANT_MOD).'_LOADED';
		$this->assertFalse(defined($bs_loaded));
		$this->assertFalse(defined($if_loaded));
		$this->assertFalse(defined($im_loaded));
		$this->assertFalse(defined($nc_loaded));

		// start calling mode
		Helper::set_calling(true);

		// initialize each module manually
		\evidev\fuelphp\modinit\Initializer::init(self::BOOTSTRAP_MOD);
		$this->assertTrue(Helper::is_loaded($bs_loaded));

		\evidev\fuelphp\modinit\Initializer::init(self::INITFUNCTION_MOD);
		$this->assertTrue(Helper::is_loaded($if_loaded));

		\evidev\fuelphp\modinit\Initializer::init(self::INITMETHOD_MOD);
		$this->assertTrue(Helper::is_loaded($im_loaded));

		\evidev\fuelphp\modinit\Initializer::init(self::NOCOMPLIANT_MOD);
		$this->assertFalse(Helper::is_loaded($nc_loaded));
	}
}