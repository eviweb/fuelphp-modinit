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

namespace evidev\fuelphp\modinit\test\helpers;

/**
 * set the test environment 
 * 
 * @package     modinit
 * @author      Eric VILLARD <dev@eviweb.fr>
 * @copyright	(c) 2012 Eric VILLARD <dev@eviweb.fr>
 * @license     http://opensource.org/licenses/MIT MIT License
 */
final class Helper
{

	/**
	 * store loaded modules
	 * 
	 * @var array
	 */
	private static $loaded;

	/**
	 * path to modules
	 * 
	 * @var string 
	 */
	private static $_modules_path;

	/**
	 * module names
	 * 
	 * @var array
	 */
	private static $_modules;

	/**
	 * store the configuration initial state
	 * 
	 * @var array
	 */
	private static $_initial_state;

	/**
	 * var initialization
	 */
	public static function init()
	{
		static::$_modules_path = dirname(__DIR__).DS.'resources'.DS.'modules'.DS;
		static::$_modules = array_slice(scandir(static::$_modules_path), 2); // remove . and ..
		static::$_initial_state = \Config::$items;
		static::reset_loaded();
	}

	/**
	 * restore the configuration initial state
	 */
	public static function restore()
	{
		\Config::$items = static::$_initial_state;
	}

	/**
	 * @see    $_modules
	 * @return array
	 */
	public static function get_modules()
	{
		return static::$_modules;
	}

	/**
	 * @see	   $_modules_path
	 * @return array
	 */
	public static function get_module_path()
	{
		return static::$_modules_path;
	}

	/**
	 * set always_load configuration for module auto loading
	 */
	public static function always_load()
	{
		$modules = array();
		foreach (static::$_modules as $mod)
		{
			$modules[$mod] = static::$_modules_path.$mod.DS;
		}
		\Config::set('always_load.modules', $modules);
	
		\Config::set(
			'always_load.packages', array('modinit' => dirname(dirname(__DIR__)).DS)
		);
	}

	/**
	 * set module_paths configuration for module manual loading
	 */
	public static function manual_load()
	{
		\Config::set('module_paths', array(static::$_modules_path));
	}

	/**
	 * reset loaded array
	 */
	public static function reset_loaded()
	{
		static::$loaded = array(
		    'call' => false,
		    'loaded' => array()
		);
	}

	/**
	 * check if a module is loaded
	 * 
	 * @param  string  $key		module name
	 * @return boolean returns true if the module is loaded, false otherwise
	 */
	public static function is_loaded($key)
	{
		return isset(static::$loaded['loaded'][$key])
			and static::$loaded['loaded'][$key];
	}

	/**
	 * indicates if a call is fired or not
	 * 
	 * @return boolean returns true if a call is fired, false otherwise
	 */
	public static function is_calling()
	{
		return static::$loaded['call'];
	}

	/**
	 * load a module
	 * 
	 * @param  string $key		module name	
	 */
	public static function load_me($key)
	{
		static::$loaded['loaded'][$key] = true;
	}

	/**
	 * enable/disable calling mode
	 * 
	 * @param  boolean $mode	set true to enable calling mode, false disable
	 */
	public static function set_calling($mode)
	{
		static::$loaded['call'] = $mode;
	}
	
	/**
	 * load modinit package
	 */
	public static function load_modinit_package() {
		\Package::load('modinit', dirname(dirname(__DIR__)).DS);
	}
}