<?php
/**
 * Project: CafePress PHP SDK
 *
 * This file is part of CafePress PHP SDK.
 *
 * CafePress PHP SDK is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CafePress PHP SDK is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CafePress PHP SDK.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @version v0.0.01b
 * @copyright 2011-2012
 * @author Qen Empaces,
 * @email qempaces@cafepress.com
 * @date 2011.06.30
 *
 */

$paths  = explode(PATH_SEPARATOR, get_include_path());
$path   = dirname(__FILE__).'/lib';
array_push($paths, $path);
set_include_path(implode(PATH_SEPARATOR, $paths));

class CafePressApi
{

    private static $config = array();
    
    public static function Loader($klass)
    {
        if (preg_match('|^CafePress|', $klass)){
            preg_match_all('/([A-Z][a-z0-9]+)/', $klass, $matches);
            array_shift($matches[1]);
            $filename = strtolower('cafe'.implode('_', $matches[1])).'.php';
            require $filename;
            return true;
        }//end if

        return false;
    }

    public function Config()
    {
        if (empty(self::$config)) {
            require 'spyc/spyc.php';
            self::$config = Spyc::YAMLLoad('config.yml');
        }//end if
        return self::$config;
    }

    public function Products()
    {
        return new CafePressApiProducts();
    }

}

spl_autoload_register('CafePressApi::Loader');
