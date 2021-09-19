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
	 echo(str_replace("\n", "<br>", $val));
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
 
   // Creating the Download folder if doesn't exist..
   $downloadPath = $curPath . HC_SLASH . ".HCdownloads";
   if (!file_exists($downloadPath)) {
	 //copy(HC_APP_STAGE_PATH . HC_SLASH . ".HCsampledir", $downloadPath);  
     $mycmd = "cp -Rp " . HC_APP_STAGE_PATH . HC_SLASH . ".HCsampledir" . " " . $downloadPath;
     $myret = exec($mycmd);
   }
 
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

 function myExecLSCommand() {
   global $prompt;
   global $command;
   global $curPath;
 
   $downloadPath = $curPath . HC_SLASH . ".HCdownloads";
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   exec($command, $output);
   
   // Creating the Download path for the current folder..
   if (!file_exists($downloadPath)) {
     //copy(HC_APP_STAGE_PATH . HC_SLASH . ".HCsampledir", $downloadPath);
     $mycmd = "cp -Rp " . HC_APP_STAGE_PATH . HC_SLASH . ".HCsampledir" . " " . $downloadPath;
     $myret=exec($mycmd);
   }
   
   // Cleaning the Download folder..
   if (file_exists($downloadPath)) {
	   $files1 = scandir($downloadPath);
	   foreach($files1 as $file) {
		 if (!is_dir($downloadPath . HC_SLASH . $file) && $file !== "." && $file !== "..") {
		   unlink($downloadPath . HC_SLASH . $file);
		 }	     
	   }
   }
      
   // Update history..
   foreach ($output as &$val) {
	 if ($val === $prompt . " " . $command . "\n") {
     } else {	   
	   if (right($val,1)==="\n") {
	     $val = left($val, strlen($val)-1);
	   }  
	   
	   // Creating the tmp download for the file entry and generating the virtual path..
	   $virtualPath = HC_STR;
	   if (file_exists($downloadPath)) {
		 if (!is_dir($curPath . HC_SLASH . $val) && filesize($curPath . HC_SLASH . $val)<=651000) {
		   copy($curPath . HC_SLASH . $val, $downloadPath . HC_SLASH . $val . ".hcd");  
		   $virtualPath = getVirtualPath($downloadPath . HC_SLASH . $val . ".hcd");
		 }
	   } else {
		 $virtualPath=HC_STR;
	   }      
	   if ($virtualPath!==HC_STR) {
	     $val = "<a href='$virtualPath'>" . $val . "</a>\n";   	     
	   } else {
		 $val = $val . "\n";
	   }
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
 
 function cdparamValidation() {
	global $curPath;
	global $opt;
	global $param1;
    global $param2;
    global $param3;

    //opt==""
    if ($opt!=HC_STR) {
	  updateHistoryWithErr("invalid options");	
      return false;
    }	    	 
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
    //param3==""
	if ($param3!=HC_STR) {
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
 
 function is_subfolderdest(string $path): bool 
 {
	global $curPath;
	
	$ret=false;
	
	if ($path!=HC_STR) {
	  $folderName = left($path, strlen($path)-1);

      if (is_dir($curPath . HC_SLASH . $folderName) && (right($path,1)==="/")) {
	    $ret=true;	
	  }
    }
    return $ret;  
 }
 
 function cpparamValidation() {
	global $curPath;
	global $opt;
	global $param1;
	global $param2; 
	global $param3;
	
	//opt!="" and opt!="-R" and opt!="-Rp"
    if (($opt!==HC_STR) && ($opt!=="-R") && ($opt!=="-Rp") && ($opt!=="-p")) {
	  updateHistoryWithErr("invalid options");	
      return false;
    }	
	//param1!="" and isword  
	if (($param1===HC_STR) || !is_word($param1)) {
	  updateHistoryWithErr("invalid source path");	
      return false;
    }	
	//param2!="" and (isword or param2=="../" or is_subfolderdest)
	if (($param2===HC_STR) || (!is_word($param2) && ($param2!="../") && !is_subfolderdest($param2))) {
      updateHistoryWithErr("invalid destination path");
      return false;
    }
    //param3==""
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
	//isword(param2) && doesn't exist 
	if (is_word($param2)) {
	  $path = $curPath . HC_SLASH . $param2;
	  if (file_exists($path)) {
		updateHistoryWithErr("destination already exists");	
		return false;
	  }
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
    if ($opt!=HC_STR)	{
	  updateHistoryWithErr("invalid options");	
      return false;
    }	
	//param1!="" and isword
	if (($param1===HC_STR) || !is_word($param1)) {
	  updateHistoryWithErr("invalid source path");	
      return false;
    }	
	//param2!="" and (isword or param2=="../" or is_subfolderdest)
	if (($param2===HC_STR) || (!is_word($param2) && ($param2!="../") && !is_subfolderdest($param2))) {
      updateHistoryWithErr("invalid destination path");
      return false;
    }
    //param3!=""
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
	//isword(param2) && doesn't exist
	if (is_word($param2)) {
	  $path = $curPath . HC_SLASH . $param2;
	  if (file_exists($path)) {
		updateHistoryWithErr("destination already exists");	
		return false;
      }
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
 
 $curPath = HC_APP_STAGE_PATH;
 if ($pwd!==HC_STR) {
   if (left($pwd, strlen(HC_APP_STAGE_PATH)) === HC_APP_STAGE_PATH) {
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
	   
	   if (strlen(HC_APP_STAGE_PATH) > strlen($nextPath)) {
         updateHistoryWithErr("out of root boundary");
       } else {
		 myExecCDBackwCommand();
	   }	
	
	 } else if ($command === "ls") {
		 
	   myExecLSCommand();	 
	      
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
		 
		 <?php if($password===HC_STR):?>
		    $("#Password").addClass("emptyfield");
		 <?php endif; ?>
		 maxY = document.getElementById("Console").scrollHeight;
		 //alert(maxY);
         document.getElementById("Console").scrollTo(0, maxY);
	 }, true);

  </script>    
    
</head>
<body>

<form id="frmHC" method="POST" action="/hc" target="_self">

<div class="header">
   <a href="/" style="color:white; text-decoration: none;"><img src="HCres/hclogo.png" style="width:48px;">&nbsp;Http Console</a>
</div>
	
<div style="clear:both; float:left; padding:8px; width:15%; height:100%; text-align:center;">
	<div style="padding-left:12px;text-align: left;">
	  &nbsp;Upload
	</div>
    <br><br><br><br><br><br><br>
<!-- &nbsp;Password<br>
    &nbsp;<input type="text" id="Password" name="Password" style="font-size:10px; color:black; width: 90%; border-radius:3px;" value="<?php echo($password);?>"><br>
    &nbsp;Salt<br>
    &nbsp;<input type="text" id="Salt" style="font-size:10px; color:black; width: 90%; border-radius:3px;" autocomplete="off"><br><br>
    &nbsp;<input type="button" id="Encode" value="Hash Me!" onclick="showEncodedPassword();" style="position:relative;left:-2px; width:92%; color:black; border-radius:2px;"> -->
  
    &nbsp;<input type="text" id="Password" name="Password" placeholder="password" style="font-size:10px; background:#393939; color:#ffffff; width: 90%; border-radius:3px;" value="<?php echo($password);?>" autocomplete="off"><br>
    &nbsp;<input type="text" id="Salt" placeholder="salt" style="position:relative; top:+5px; font-size:10px; background:#393939; color:#ffffff; width: 90%; border-radius:3px;" autocomplete="off"><br>
    &nbsp;<a href="#" onclick="showEncodedPassword();" style="position:relative; left:-2px; top:+5px; color:#ffffff; font-size:12px;">Hash Me!</a>
     
</div>

<div style="float:left; width:85%;height:100%; padding:8px; border-left: 1px solid #2c2f34;">
	
	<?php if ($hideFB !== HC_STR): ?>
	<div id="FirstBanner" style="border-radius:20px; position:relative; left:+3px; width:98%; background-color: #33aced; padding: 20px; margin-bottom:8px;">	
	
	   <button type="button" class="close" aria-label="Close" onclick="closeFirstBanner();" style="position:relative; left:-10px;">
          <span aria-hidden="true">&times;</span>
       </button>
	
	   Hello and welcome to Http Console!<br><br>
	   
	   Http Console is a light and simple web console to manage your website.<br><br>
	   
	   Http Console is supplied AS-IS and we do not take any responsibility for its misusage.<br><br>
	   
	   First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.<br><br>
	   
	   As you are going to make work Http Console in the PHP process environment, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:<br>
	   <ol>
	   <li>Create a "stage" folder in your web app path; give to the stage folder the write permissions; and set the stage path in the config file.</li>
	   <li>In the stage path create a ".HCsampledir" folder and give to this folder the write permission. This folder will be the sample folder to copy from new folders inside the stage path.</li>
	   <li>Likewise, in the stage path create an empty ".HCsamplefile" and give to this file the write permission. This file will be the sample file to copy from new files inside the stage path.</li>
	   <li>Configure the max history items and max recall history items as required (default: 50).</li>	      
	   </ol>
	   
	   <br>	
	   
	   Http Console understands a limited set of commands with a far limited set of parameters:<br>
	   cd, cd.., cp, cp -R, ls, ls -lsa, mv, pwd<br><br>	   
	   
	   Hope you can enjoy it and let us know about any feedback: <a href="mailto:info@httpconsole.com" style="color:#e6d236;">info@httpconsole.com</a>
	   
	</div>	
	<?php endif; ?>
	
	&nbsp;Console<br>
	<div id="Console" style="height:493px; overflow-y:auto; margin-top:10px;">
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
