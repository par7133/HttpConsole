<?php

/**
 * Copyright (c) 2016, 2024, 5 Mode's contributors
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither 5 Mode nor the names of its contributors 
 *       may be used to endorse or promote products derived from this software 
 *       without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * HC_init.inc
 * 
 * Http Console initialization file.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2016, 2024, 5 Mode     
 * @license https://opensource.org/licenses/BSD-3-Clause 
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
