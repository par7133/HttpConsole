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
 * HC.php
 * 
 * Http Console home page.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2016, 2024, 5 Mode     
 * @license https://opensource.org/licenses/BSD-3-Clause 
 */
 
 require "HC_init.inc";
  
 $cmdHistory = [];
 $cmd = HC_STR;
 $opt = HC_STR;
 $param1 = HC_STR;
 $param2 = HC_STR;
 $param3 = HC_STR;
 
 $cmdRecallHistory = [];
  
 function showHistory() {
   global $cmdHistory;
   $i = 1;	 
   foreach($cmdHistory as $val) {
	 echo(HTMLencode($val));
	 $i++;   
   }
 }
 
function updateHistory(&$update, $maxItems) {
   global $cmdHistory;
   // Making enough space in $cmdHistory for the update..
   $shift = (count($cmdHistory) + count($update)) - $maxItems;
   if ($shift > 0) {
     $cmdHistory = array_slice($cmdHistory, $shift, $maxItems); 
   }		  
   // Adding $cmdHistory update..
   if (count($update) > $maxItems) {
      $beginUpd = count($update) - ($maxItems-1);
   } else {
	  $beginUpd = 0;
   }	        
   $update = array_slice($update, $beginUpd, $maxItems); 
   foreach($update as $val) {  
	 $cmdHistory[] = $val;   
   }
   // Writing out $cmdHistory on disk..
   $filepath = HC_APP_PATH . HC_SLASH . ".HC_history";
   file_put_contents($filepath, implode('', $cmdHistory));	 
 }
 
 function loadRecallHistory() { 
	global $cmdRecallHistory; 
	$tmpcmdRecallHistory = file(HC_APP_PATH . HC_SLASH . ".HC_Recallhistory");
	foreach($tmpcmdRecallHistory as $val) {
	  $cmdRecallHistory[left($val, strlen($val)-1)]=$val;  	
    } 
 }	 
	  
 function updateRecallHistory($update, $maxItems) {
   global $cmdRecallHistory;
   
   if (!array_key_exists($update, $cmdRecallHistory)) {
	 // Making enough space in $cmdHistory for the update..
	 $shift = (count($cmdRecallHistory) + 1) - $maxItems;
	 if ($shift > 0) {
  	   $cmdRecallHistory = array_slice($cmdRecallHistory, $shift, $maxItems); 
	 }
	 
	 $cmdRecallHistory[$update] = $update . "\n";
   }
   		     
   // Writing out $cmdRecallHistory on disk..
   $filepath = HC_APP_PATH . HC_SLASH . ".HC_Recallhistory";
   file_put_contents($filepath, implode('', $cmdRecallHistory));	 
 }	 

 function updateHistoryWithErr(string $err) {
   global $prompt;
   global $command;
   	 
   $output = [];  
   $output[] = $prompt . " " . $command . "\n";
   $output[] = "$err\n";
   updateHistory($output, HC_HISTORY_MAX_ITEMS);  	 
 }	 	 
 
 function myExecCommand() {
   global $prompt;
   global $command;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   exec($command, $output);

   // Update history..
   foreach ($output as &$val) {
	 if (right($val,1)!="\n") {
	   $val = $val . "\n";
	 }	     
   }	 
   updateRecallHistory($command, HC_RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HC_HISTORY_MAX_ITEMS);
 }
 
 function myExecCopy() {
   global $prompt;
   global $command;
   global $param1;
   global $param2;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   copy($param1, $param2);

   // Update history..
   foreach ($output as &$val) {
	 if (right($val,1)!="\n") {
	   $val = $val . "\n";
	 }	     
   }	 
   updateRecallHistory($command, HC_RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HC_HISTORY_MAX_ITEMS);
 }

 function myExecCDFolderCommand() {
   global $prompt;
   global $command;
   global $param1;
   global $curPath;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   //exec($command, $output);

   $newPath = $curPath . HC_SLASH . $param1;
   chdir($newPath);

   $curPath = $newPath;
   $curDir = $param1;
 
   $prompt = str_replace("$1", $curDir, HC_APP_PROMPT);

   // Update history..
   foreach ($output as &$val) {
	 if (right($val,1)!="\n") {
	   $val = $val . "\n";
	 }	     
   }	 
   updateRecallHistory($command, HC_RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HC_HISTORY_MAX_ITEMS);
 }

 
 function myExecCDBackwCommand() {
   global $prompt;
   global $command;
   global $curPath;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   //exec($command, $output);

   $ipos = strripos($curPath, HC_SLASH);
   $newPath = substr($curPath, 0, $ipos);
   chdir($newPath);

   $curPath = getcwd();
   $ipos = strripos($curPath, HC_SLASH);
   $curDir = substr($curPath, $ipos);
 
   $prompt = str_replace("$1", $curDir, HC_APP_PROMPT);

   // Update history..
   foreach ($output as &$val) {
	 if (right($val,1)!="\n") {
	   $val = $val . "\n";
	 }	     
   }	 
   updateRecallHistory($command, HC_RECALL_HISTORY_MAX_ITEMS);
   updateHistory($output, HC_HISTORY_MAX_ITEMS);
 }
 
 function parseCommand() {
   global $command;
   global $cmd;
   global $opt;
   global $param1;
   global $param2;
   global $param3;
   
   $str = trim($command);
   
   $ipos = stripos($str, HC_SPACE);
   if ($ipos > 0) {
     $cmd = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $cmd = $str;
	 return;
   }	     
   
   if (left($str, 1) === "-") {
	 $ipos = stripos($str, HC_SPACE);
	 if ($ipos > 0) {
	   $opt = left($str, $ipos);
	   $str = substr($str, $ipos+1);
	 } else {
	   $opt = $str;
	   return;
	 }	     
   }
   
   $ipos = stripos($str, HC_SPACE);
   if ($ipos > 0) {
     $param1 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param1 = $str;
	 return;
   }	     
  
   $ipos = stripos($str, HC_SPACE);
   if ($ipos > 0) {
     $param2 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param2 = $str;
	 return;
   }
   
   $ipos = stripos($str, HC_SPACE);
   if ($ipos > 0) {
     $param3 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param3 = $str;
	 return;
   }	     
 	     
 }
 
 function is_word(string $string) {
   return preg_match("/^[\w\-]+?$/", $string);	 
 }	 
 
 function cdparamValidation() {
	global $curPath;
	global $param1;
    global $param2;
    	 
	//param1!="" and isword
	if (($param1===HC_STR) && !is_word($param1)) {
	  updateHistoryWithErr("invalid dir");	
      return false;
    }
    //param2==""
	if ($param2!==HC_STR) {
	  updateHistoryWithErr("invalid parameters");	
      return false;
    }	
	//param1 exist and is_dir
	$path = $curPath . HC_SLASH . $param1;
	if (!file_exists($path) || !is_dir($path)) {
	  updateHistoryWithErr("dir doesn't exist");	
	  return false;
	}  	
	return true;
 }	 
 
 function cpparamValidation() {
	global $curPath;
	global $opt;
	global $param1;
	global $param2; 
	global $param3;
	
	//opt!="" and opt!="-R" and opt!="-Rp"
    if (($opt!==HC_STR) && ($opt!=="-R") && ($opt!=="-Rp") && ($opt!=="-p"))	{
	  updateHistoryWithErr("invalid parameters");	
      return false;
    }	
	//param1!="" and isword
	if (($param1===HC_STR) && !is_word($param1)) {
	  updateHistoryWithErr("invalid source path");	
      return false;
    }	
	//param2!="" and isword
	if (($param2===HC_STR) && !is_word($param2)) {
      updateHistoryWithErr("invalid destination path");
      return false;
    }
    if ($param3!=HC_STR) {
      updateHistoryWithErr("invalid parameters");
      return false;
    }
	//param1 exist
	$path = $curPath . HC_SLASH . $param1;
	if (!file_exists($path)) {
	  updateHistoryWithErr("source must exists");	
	  return false;
	}  	
	//param2 doesn't exist 
	$path = $curPath . HC_SLASH . $param2;
	if (file_exists($path)) {
	  updateHistoryWithErr("destination already exists");	
	  return false;
	}  	
	return true;
 }

 function mvparamValidation() {
	global $curPath;
	global $opt;
	global $param1;
	global $param2;
	global $param3; 
	
	//opt!="" and opt!="-R"
    if ($opt!==HC_STR)	{
	  updateHistoryWithErr("invalid parameters");	
      return false;
    }	
	//param1!="" and isword
	if (($param1===HC_STR) && !is_word($param1)) {
	  updateHistoryWithErr("invalid source path");	
      return false;
    }	
	//param2!="" and isword
	if (($param2===HC_STR) && !is_word($param2)) {
      updateHistoryWithErr("invalid destination path");
      return false;
    }
    if ($param3!=HC_STR) {
      updateHistoryWithErr("invalid parameters");
      return false;
    }
	//param1 exist
	$path = $curPath . HC_SLASH . $param1;
	if (!file_exists($path)) {
	  updateHistoryWithErr("source must exists");	
	  return false;
	}  	
	//param2 doesn't exist 
	$path = $curPath . HC_SLASH . $param2;
	if (file_exists($path)) {
	  updateHistoryWithErr("destination already exists");	
	  return false;
	}  	
	return true;
 }
  
 $password = filter_input(INPUT_POST, "Password");
 $command = filter_input(INPUT_POST, "CommandLine");
 $pwd = filter_input(INPUT_POST, "pwd"); 
 $hideFB = filter_input(INPUT_POST, "hideFB");

 if ($password !== HC_STR) {	
	$hash = hash("sha256", $password . HC_APP_SALT, false);

	if ($hash !== HC_APP_HASH) {
	  $password=HC_STR;	
    }	 
 } 
 
 $curPath = HC_CMDLINE_CD_DEPTH;
 if ($pwd!==HC_STR) {
   if (left($pwd, strlen(HC_CMDLINE_CD_DEPTH)) === HC_CMDLINE_CD_DEPTH) {
     $curPath = $pwd;
     chdir($curPath);	   
   }	    
 }	 
 $ipos = strripos($curPath, HC_SLASH);
 $curDir = substr($curPath, $ipos);
 
 $prompt = str_replace("$1", $curDir, HC_APP_PROMPT);
 
 if ($password !== HC_STR) {
   
   loadRecallHistory();
   $cmdHistory = file(HC_APP_PATH . HC_SLASH . ".HC_history");
   
   parseCommand($command);
   //echo("cmd=" . $cmd . "<br>");
   //echo("opt=" . $opt . "<br>");
   //echo("param1=" . $param1 . "<br>");
   //echo("param2=" . $param2 . "<br>");
   
   if (mb_stripos(HC_CMDLINE_VALIDCMDS, "|" . $command . "|")) {
     
     if ($command === "cd ..") {
	   
	   $ipos = strripos($curPath, HC_SLASH);
	   $nextPath = substr($curPath, 0, $ipos);
	   
	   if (strlen(HC_CMDLINE_CD_DEPTH) > strlen($nextPath)) {
         updateHistoryWithErr("out of root boundary");
       } else {
		 myExecCDBackwCommand();
	   }	
	      
     } else {
	   myExecCommand(); 
    }
   
   } else if (mb_stripos(HC_CMDLINE_VALIDCMDS, "|" . $cmd . "|")) {
       
     if ($cmd === "cd") {
	   if (cdparamValidation()) {
	     myExecCDFolderCommand();
	   }	     
	 } else if ($cmd === "cp") {
	   if (cpparamValidation()) {
	     myExecCommand();
	   }	     
	 } else if ($cmd === "mv") {
	   if (mvparamValidation()) {
	     myExecCommand();
	   }	     
	 } 	   
       
   } else {
	 updateHistoryWithErr("invalid command");  
   }
   	  	
 } else {
   
   $cmdHistory = [];	 
 
 }
 
 ?>
 

<!DOCTYPE html>
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
<head>
	
  <meta charset="UTF-8"/>
  <meta name="style" content="day1"/>
  
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  
<!--
Copyright (c) 2016, 2024, 5 Mode
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
  * Redistributions of source code must retain the above copyright
    notice, this list of conditions and the following disclaimer.
  * Redistributions in binary form must reproduce the above copyright
    notice, this list of conditions and the following disclaimer in the
    documentation and/or other materials provided with the distribution.
  * Neither 5 Mode nor the names of its contributors 
    may be used to endorse or promote products derived from this software 
    without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS OR CONTRIBUTORS BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

https://opensource.org/licenses/BSD-3-Clause -->
  
    
  <title>Http Console: Ubiquity c'est la vie..</title>
	
  <link rel="shortcut icon" href="./HCres/favicon55.ico?v=<?php echo(time()); ?>" />
    
  <meta name="description" content="Welcome to <?php echo(HC_APP_NAME); ?>"/>
  <meta name="author" content="5 Mode"/> 
  <meta name="robots" content="noindex"/>
  
  <script src="./HCjs/jquery-3.1.0.min.js" type="text/javascript"></script>
  <script src="./HCjs/jquery-ui.1.12.1.min.js" type="text/javascript"></script>
  <script src="./HCjs/HC_common.js" type="text/javascript"></script>
  <script src="./HCjs/bootstrap.min.js" type="text/javascript"></script>
  <script src="./HCjs/sha.js" type="text/javascript"></script>
  
  <script src="./HCjs/HC.js" type="text/javascript" defer></script>
  
  <link href="./HCcss/bootstrap.min.css" type="text/css" rel="stylesheet">
  <link href="./HCcss/jquery-ui.1.12.1.css" type="text/css" rel="stylesheet">
  <link href="./HCcss/style.css?v=<?php echo(time()); ?>" type="text/css" rel="stylesheet">
     
  <script>
	
	 $(document).ready(function() {
	  
		 $("#CommandLine").on("keydown",function(e){
		   key = e.which;
		   //alert(key);
		   if (key===13) {
			 e.preventDefault();
			 frmHC.submit();
		   } else { 
			 //e.preventDefault();
		   }
		 });

     });
		  
     window.addEventListener("load", function() {
		 maxY = document.getElementById("Console").scrollHeight;
		 //alert(maxY);
         document.getElementById("Console").scrollTo(0, maxY);
	 }, true);

  </script>    
    
</head>
<body>

<form id="frmHC" method="POST" action="HC.php" target="_self">

<div class="header">
   <a href="/" style="color:white; text-decoration: none;"><img src="HCres/hclogo.png" style="width:48px;">&nbsp;Http Console</a>
</div>
	
<div style="clear:both; float:left; padding:8px; width:15%; height:100%;">
	&nbsp;Upload
    <br><br><br><br><br><br><br>
    &nbsp;Password<br>
    &nbsp;<input type="text" id="Password" name="Password" style="font-size:10px; color:black; width: 90%; border-radius:3px;" value="<?php echo($password);?>"><br>
    &nbsp;Salt<br>
    &nbsp;<input type="text" id="Salt" style="font-size:10px; color:black; width: 90%; border-radius:3px;" autocomplete="off"><br><br>
    &nbsp;<input type="button" id="Encode" value="Hash Me!" onclick="showEncodedPassword();" style="position:relative;left:-2px; width:92%; color:black; border-radius:2px;">
</div>

<div style="float:left; width:85%;height:100%; padding:8px; border-left: 1px solid #2c2f34;">
	
	<?php if ($hideFB !== HC_STR): ?>
	<div id="FirstBanner" style="border-radius:20px; position:relative; left:+3px; width:98%; background-color: #33aced; padding: 20px; margin-bottom:8px;">	
	
	   <button type="button" class="close" aria-label="Close" onclick="closeFirstBanner();" style="position:relative; left:-10px;">
          <span aria-hidden="true">&times;</span>
       </button>
	
	   Hello and welcome to Http Console!<br><br>
	   
	   Http Console is a light and simple web console to admin your website.<br><br>
	   
	   Http Console is supplied AS-IS and we do not take any responsibility for its misusage.<br><br>
	   
	   First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.<br><br>
	   
	   As you are going to make work Http Console in the PHP process environment, using a limited web server or phpfpm user, we reccomend you to follow some simple directives for an optimal first setup:<br>
	   <ol>
	   <li>We encourage you to setup a "stage" folder in your web app path; give to the stage folder the write permissions; and set the stage path in the config file as *cd depth*.</li>
	   <li>Inside the stage path create a "sample" folder and give to this folder the write permission. This folder will be the sample folder to copy from to create new folders with write permissions inside the stage path.</li>
	   <li>Likewise create an "upload" folder inside the stage path giving the right permissions.</li>
	   <li>Configure the max history items and max recall history items as required (default: 50).</li>	      
	   </ol>
	   
	   <br>	
	   
	   Http Console understands a limited set of commands with a far limited set of parameters:<br>
	   cd, cd.., cp, cp -R, ls, ls -lsa, mkdir, mv, pwd<br><br>	   
	   
	   Hope you can enjoy it and let us know about any feedback: <a href="mailto:info@httpconsole.com" style="color:#e6d236;">info@httpconsole.com</a>
	   
	</div>	
	<?php endif; ?>
	
	&nbsp;Console<br>
	<div id="Console" style="height:500px; overflow-y:auto; margin-top:10px;">
	<pre style="margin-left:5px;padding-left:0px;border:0px;background-color: #000000; color: #ffffff;">
<?php showHistory($cmdHistory); ?>		
<div style="position:relative;top:-15px;"><label id="Prompt" for="CommandLine"><?php echo($prompt); ?></label>&nbsp;<input id="CommandLine" name="CommandLine" list="CommandList" type="text" autocomplete="off" style="width:80%; height:22px; background-color: black; color:white; border:0px; border-bottom: 1px dashed #EEEEEE;"></div>	
	</pre>	
	</div>
	
	<datalist id="CommandList">
	<?php foreach($cmdRecallHistory as &$val): ?>
	<?php $val = left($val, strlen($val)-1); ?>
	<?php echo("<option value='$val'>\n"); ?>
	<?php endforeach; ?>	
	</datalist>	
	
</div>

<div class="footer">
<div id="footerCont">&nbsp;</div>
<div id="footer"><span style="background:#FFFFFF;opacity:1.0;margin-right:10px;">&nbsp;&nbsp;A <a href="http://5mode.com">5 Mode</a> project and <a href="http://wysiwyg.systems">WYSIWYG</a> system. Some rights reserved.</span></div>	
</div>

<input type="hidden" name="pwd" value="<?php echo($curPath); ?>" style="color:black">
<input type="hidden" name="hideFB" value="<?php echo($hideFB); ?>">

</form>

</body>	 
</html>	 
