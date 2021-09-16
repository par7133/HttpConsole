<?php

/**
 * Copyright (c) 2016, 2018, the Open Gallery's contributors
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither Open Gallery nor the names of its contributors 
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
 * err-helper.php
 * 
 * JS Error helper.
 * 
 * @author Daniele Bonini <danielemi@hotmail.com>
 * @copyrights (c) 2016, 2018, the Open Gallery's contributors     
 * @license https://opensource.org/licenses/BSD-3-Clause 
 */

require "../../../Private/core/init.inc";

//require "../../../Private/classes/OpenGallery/OpenGallery/class.err.inc";

use OpenGallery\OpenGallery\Err;

header("Content-Type: text/javascript");


// PARAMETERS VALIDATION AND NORMALIZATION

//errNo
//define("ERR_NO", substr(filter_input(INPUT_GET, "errNo", FILTER_SANITIZE_STRING), 0, 100));

//errKey
//define("ERR_KEY", array_search(ERR_NO, Err::$A_ERR_NO));
        
//errMsg
//define("ERR_MSG", substr(filter_input(INPUT_GET, "errMsg", FILTER_SANITIZE_STRING), 0, 200));

//errScript
//define("ERR_SCRIPT", substr(filter_input(INPUT_GET, "errScript", FILTER_SANITIZE_STRING), 0, 255));

//errLine
//define("ERR_LINE",  substr(filter_input(INPUT_GET, "errLine", FILTER_SANITIZE_NUMBER_INT), 0, 5));

?>

var ERR_NO = "<?php echo ERR_NO; ?>";
var ERR_KEY = "<?php echo ERR_KEY; ?>";
var ERR_MSG = "<?php echo ERR_MSG; ?>";
var ERR_SCRIPT = "<?php echo ERR_SCRIPT; ?>";
var ERR_LINE = "<?php echo ERR_LINE; ?>";
var ERR_STACK = "<?php echo ERR_STACK; ?>";

var A_ERR_NO = {
  <?php
    $start=true;
    foreach (Err::$A_ERR_NO as $key => $value) {
      if (!$start) {
        echo ",\n";
      } else {
        $start=false;
      }
      echo "'$key':\"$value\"";
    }
    echo "\n";
  ?>
}; 

var A_ERR_MSG = {
  <?php
    $start=true;
    foreach (Err::$A_ERR_MSG as $key => $value) {
      if (!$start) {
        echo ",\n";
      } else {
        $start=false;
      }
      echo "'$key':\"$value\"";
    }
    echo "\n";
  ?>
};  

var A_ERR_EXTDES_MSG = {
  <?php
    $start=true;
    foreach (Err::$A_ERR_EXTDES_MSG as $key => $value) {
      if (!$start) {
        echo ",\n";
      } else {
        $start=false;
      }
      echo "'$key':\"$value\"";
    }
    echo "\n";
  ?>
};  


function clearErrors() {
  $(".form-error").each(function(index, Element) {
     $(this).hide();
  });
  
  $(".form-error-adapt").each(function(index, Element) {
     $(this).hide();
  });
}
