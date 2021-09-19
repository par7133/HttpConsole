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
 * HC.php
 * 
 * Http Console home page.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, 5 Mode      
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

 function updateHistoryWithErr(string $err, bool $withCommand = true) 
 {
   global $prompt;
   global $command;
   	 
   $output = [];  
   if ($withCommand) {
     $output[] = $prompt . " " . $command . "\n";
   }
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
		   $fileext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
		   if ($fileext === "php") {
		     copy($curPath . HC_SLASH . $val, $downloadPath . HC_SLASH . $val . ".hcd");  
		     $virtualPath = getVirtualPath($downloadPath . HC_SLASH . $val . ".hcd");			   	 
	       } else {
		     copy($curPath . HC_SLASH . $val, $downloadPath . HC_SLASH . $val);  
		     $virtualPath = getVirtualPath($downloadPath . HC_SLASH . $val);			   
		   }	 
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

 function myExecPWDCommand() {
   global $prompt;
   global $command;
   global $curPath;
 
    // Exec command..
   $output = [];
   $output[] = $prompt . " " . $command . "\n";   
   exec($command, $output);

   // Update history..
   foreach ($output as &$val) {
	 if (mb_stripos("~".$val,HC_APP_STAGE_PATH)) {  
	   $val = str_replace(dirname(HC_APP_STAGE_PATH), "~ ", $val) . "\n";
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
    // param2=="../" && is_root 
    // param2=="../" && dest exists	
    if ($param2==="../") {
	  if ($curPath === HC_APP_STAGE_PATH) {	
	    updateHistoryWithErr("out of root boundary");
	    return false;
	  }  
	  $path = dirname($curPath) . HC_SLASH . $param1;
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
    // param2=="../" && is_root 
    // param2=="../" && dest exists	
    if ($param2==="../") {
	  if ($curPath === HC_APP_STAGE_PATH) {	
	    updateHistoryWithErr("out of root boundary");
	    return false;
	  }  
	  $path = dirname($curPath) . HC_SLASH . $param1;
      if (file_exists($path)) {
		updateHistoryWithErr("destination already exists");	
		return false;
      }	  
	}	
	return true;
 }
  
 function upload() {

   global $curPath;
   global $prompt;

   //if (!empty($_FILES['files'])) {
   if (!empty($_FILES['files']['tmp_name'][0])) {
	   
     // Updating history..
     $output = [];
     $output[] = $prompt . " " . "File upload" . "\n";   
     updateHistory($output, HC_HISTORY_MAX_ITEMS);
	   	 
     $uploads = (array)fixMultipleFileUpload($_FILES['files']);
     
     //no file uploaded
     if ($uploads[0]['error'] === HC_UPLOAD_ERR_NO_FILE) {
       updateHistoryWithErr("No file uploaded.", false);
       return;
     } 
 
     foreach($uploads as &$upload) {
		
	   switch ($upload['error']) {
		 case HC_UPLOAD_ERR_OK:
		   break;
		 case HC_UPLOAD_ERR_NO_FILE:
		   updateHistoryWithErr("One or more uploaded files are missing.", false);
		   return;
		 case HC_UPLOAD_ERR_INI_SIZE:
		   updateHistoryWithErr("File exceeded INI size limit.", false);
		   return;
		 case HC_UPLOAD_ERR_FORM_SIZE:
		   updateHistoryWithErr("File exceeded form size limit.", false);
		   return;
		 case HC_UPLOAD_ERR_PARTIAL:
		   updateHistoryWithErr("File only partially uploaded.", false);
		   return;
		 case HC_UPLOAD_ERR_NO_TMP_DIR:
		   updateHistoryWithErr("TMP dir doesn't exist.", false);
		   return;
		 case HC_UPLOAD_ERR_CANT_WRITE:
		   updateHistoryWithErr("Failed to write to the disk.", false);
		   return;
		 case HC_UPLOAD_ERR_EXTENSION:
		   updateHistoryWithErr("A PHP extension stopped the file upload.", false);
		   return;
		 default:
		   updateHistoryWithErr("Unexpected error happened.", false);
		   return;
	   }
		
	   if (!is_uploaded_file($upload['tmp_name'])) {
		 updateHistoryWithErr("One or more file have not been uploaded.", false);
		 return;
	   }
		
	   // name	 
	   $name = (string)substr((string)filter_var($upload['name']), 0, 255);
	   if ($name == HC_STR) {
         updateHistoryWithErr("Invalid file name: " . $name, false);
         return;
       } 
	   $upload['name'] = $name;
	   
	   // fileType
	   $fileType = substr((string)filter_var($upload['type']), 0, 30);
	   $upload['type'] = $fileType;	 
	   
	   // tmp_name
	   $tmp_name = substr((string)filter_var($upload['tmp_name']), 0, 300);
	   if ($tmp_name == HC_STR || !file_exists($tmp_name)) {
         updateHistoryWithErr("Invalid file temp path: " . $tmp_name, false);
         return;
       } 
	   $upload['tmp_name'] = $tmp_name;
	   
 	   //size
 	   $size = substr((string)filter_var($upload['size'], FILTER_SANITIZE_NUMBER_INT), 0, 12);
	   if ($size == "") {
		 updateHistoryWithErr("Invalid file size.", false);
		 return;
	   } 
	   $upload["size"] = $size;

	   $tmpFullPath = $upload["tmp_name"];
	   
	   $originalFilename = pathinfo($name, PATHINFO_FILENAME);
	   $originalFileExt = pathinfo($name, PATHINFO_EXTENSION);
	   $FileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));
	   
	   if ($originalFileExt!==HC_STR) {
	     $destFileName = $originalFilename . "." . $originalFileExt;
	   } else {
		 $destFileName = $originalFilename;  
       }	   
       $destFullPath = $curPath . PHP_SLASH . $destFileName;
	   
	   if (file_exists($destFullPath)) {
		 updateHistoryWithErr("destination already exists", false);
		 return;
	   }	   
	    
	   copy($tmpFullPath, $destFullPath);

       // Updating history..
       $output = [];
       $output[] = $destFileName . " " . "uploaded" . "\n";   
       updateHistory($output, HC_HISTORY_MAX_ITEMS);
  
	   // Cleaning up..
	  
	   // Delete the tmp file..
	   unlink($tmpFullPath); 
	    
	 }	 
 
   }
 }	  
  
  
 $password = filter_input(INPUT_POST, "Password");
 $command = filter_input(INPUT_POST, "CommandLine");
 $pwd = filter_input(INPUT_POST, "pwd"); 
 $hideSplash = filter_input(INPUT_POST, "hideSplash");

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
   
   upload();
   
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
	 
     } else if ($command === "pwd") { 
	   
	   myExecPWDCommand();
	      
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
	   
	 if (empty($_FILES['files']['tmp_name'][0])) {  
	   updateHistoryWithErr("invalid command");
	 }    
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
    Copyright 2021, 2024 5 Mode

    This file is part of Http Console.

    Http Console is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Http Console is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Http Console. If not, see <https://www.gnu.org/licenses/>.
 -->
  
    
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

<form id="frmHC" method="POST" action="/hc" target="_self" enctype="multipart/form-data">

<div class="header">
   <a href="http://httpconsole.com" target="_blank" style="color:white; text-decoration: none;"><img src="HCres/hclogo.png" style="width:48px;">&nbsp;Http Console</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://github.com/par7133/HttpConsole" style="color:#ffffff"><span style="color:#119fe2">on</span> github</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:info@httpconsole.com" style="color:#ffffff"><span style="color:#119fe2">for</span> feedback</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="tel:+39-331-4029415" style="font-size:13px;background-color:#15c60b;border:2px solid #15c60b;color:white;height:27px;text-decoration:none;">&nbsp;&nbsp;get support&nbsp;&nbsp;</a>
</div>
	
<div style="clear:both; float:left; padding:8px; width:15%; height:100%; text-align:center;">
	<div style="padding-left:12px;text-align: left;">
	  <!--&nbsp;Upload-->
	  &nbsp;<a href="#" id="upload" style="<?php echo(($password===HC_STR?'text-decoration:none;color:gray;':'color:#ffffff;')); ?>" onclick="<?php echo(($password!==HC_STR?'upload()':'')); ?>">Upload</a>
	  <input id="files" name="files[]" type="file" accept=".css, .doc,.docx,.gif,.htm,.html,.ico,.inc,.jpg,.js,.php,.pdf,.png,.txt,.xls,.xlsx" style="visibility: hidden;">
	</div>
    <br><br><br><br><br><br><br>
  
    &nbsp;<input type="text" id="Password" name="Password" placeholder="password" style="font-size:10px; background:#393939; color:#ffffff; width: 90%; border-radius:3px;" value="<?php echo($password);?>" autocomplete="off"><br>
    &nbsp;<input type="text" id="Salt" placeholder="salt" style="position:relative; top:+5px; font-size:10px; background:#393939; color:#ffffff; width: 90%; border-radius:3px;" autocomplete="off"><br>
    &nbsp;<a href="#" onclick="showEncodedPassword();" style="position:relative; left:-2px; top:+5px; color:#ffffff; font-size:12px;">Hash Me!</a>
     
</div>

<div style="float:left; width:85%;height:100%; padding:8px; border-left: 1px solid #2c2f34;">
	
	<?php if (HC_APP_SPLASH): ?>
	<?php if ($hideSplash !== HC_STR): ?>
	<div id="splash" style="border-radius:20px; position:relative; left:+3px; width:98%; background-color: #33aced; padding: 20px; margin-bottom:8px;">	
	
	   <button type="button" class="close" aria-label="Close" onclick="closeSplash();" style="position:relative; left:-10px;">
          <span aria-hidden="true">&times;</span>
       </button>
	
	   Hello and welcome to Http Console!<br><br>
	   
	   Http Console is a light and simple web console to manage your website.<br><br>
	   
	   Http Console is released under GPLv3 license, is supplied AS-IS and we do not take any responsibility for its misusage.<br><br>
	   
	   First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.<br><br>
	   
	   As you are going to run Http Console in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:<br>
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
<input type="hidden" name="hideSplash" value="<?php echo($hideSplash); ?>">

</form>

</body>	 
</html>	 
