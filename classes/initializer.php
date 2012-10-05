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

namespace evidev\fuelphp\modinit;

/**
 * initialize modules
 * 
 * @package     modinit
 * @author      Eric VILLARD <dev@eviweb.fr>
 * @copyright	(c) 2012 Eric VILLARD <dev@eviweb.fr>
 * @license     http://opensource.org/licenses/MIT MIT License
 */
class Initializer
{

	/**
	 * initialize the given module if exists or all the modules referenced
	 * in the always_load.modules option of the application config file
	 * 
	 * @param   string  $module     the module name
	 */
	public static function init($module = null)
	{
		$modules = is_string($module) ?
			array($module) :
			\Config::get('always_load.modules');

		// start loading modules
		\Module::load($modules);

		foreach ($modules as $key => $value)
		{
			// $key is numeric then $value is the module name
			if (is_numeric($key))
			{
				$name = $value;
				$path = \Module::exists($name);
			}
			else
			// $key is the module name and $value is its path
			{
				$name = $key;
				$path = $value;
			}
			$namespace = '\\'.ucfirst($name);
			$classpath = \Autoloader::namespace_path($name);

			// look for bootstrap file
			$file = $path.'bootstrap.php';
			file_exists($file) and \Fuel::load($file);

			// look for $namespace::__init function in $name file
			$file = $path.$name.'.php';
			$func = $namespace.'\\__init';
			file_exists($file)
				and !function_exists($func)
				and \Fuel::load($file);
			is_callable($func) and call_user_func($func);

			// load module class
			$class = $namespace.$namespace;
			$func = $class.'::_init';
			(!class_exists($class) and \Autoloader::load($class)) or
				(is_callable($func) and call_user_func($func));
		}
	}

}