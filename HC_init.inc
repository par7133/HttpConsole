<?php

/**
 * Copyright 2021, 2024 5 Mode
 *
 * This file is part of Http Console.
 *
 * Http Console is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Http Console is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.  
 * 
 * You should have received a copy of the GNU General Public License
 * along with Http Console. If not, see <https://www.gnu.org/licenses/>.
 *
 * HC_init.inc
 * 
 * Http Console initialization file.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, 5 Mode     
 */
 
error_reporting(E_ALL & ~ (E_WARNING | E_NOTICE | E_STRICT | E_DEPRECATED));  
ini_set('display_startup_errors',0);  
ini_set('display_errors',0);  
ini_set('log_errors',1);

require "HC_config.inc";

// Checking a little the configuration..
if (!file_exists(HC_APP_STAGE_PATH)) {
  die("Stage folder doesn't exist. You must create a stage folder in your web app public path and configur it properly inside the config file.");
}	
if (!file_exists(HC_APP_STAGE_PATH . "/.HCsampledir")) {
  die("Sample folder doesn't exist. You must create a sample folder named '.HCsampledir' inside your stage folder. Give it the proper write permissions.");
}		

require "HC_const.inc";
require "HC_funct.inc";

// ERROR HANDLING AND LOGGING

if (HC_DEBUG) {
  error_reporting(E_ALL | E_STRICT);  
  ini_set('display_startup_errors',0);  
  ini_set('display_errors',0);
  ini_set('log_errors',1); 
} else {
  error_reporting(E_ALL & ~ (E_WARNING | E_NOTICE | E_STRICT | E_DEPRECATED));  
  ini_set('display_startup_errors',0);  
  ini_set('display_errors',0);  
  ini_set('log_errors',1); 
}
