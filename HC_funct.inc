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
 * HC_funct.inc
 * 
 * Http Console function file.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, 5 Mode     
 */

if (!function_exists("fixMultipleFileUpload")) { 
/**
 * Fix multiple file uploaded array ($_FILE)
 * 
 * @param array $f the $_FILE array
 * @return array the array fixed
 */
function fixMultipleFileUpload(&$f): array
{
    $files = array();
    $count = count($f['name']);
    $keys = array_keys($f);
   
    for($i=0;$i<$count;$i++)
    {
        foreach($keys as $key)
        {
            $files[$i][$key] = $f[$key][$i];
        }
    }
    return $files;
}
}
 
if (!function_exists("getVirtualPath")) {
 
function getVirtualPath(string $path): string 
{
  $ret = HC_STR; 
  if (left($path, strlen(HC_WEBSITE_WEBROOT)) === HC_WEBSITE_WEBROOT) {
    $ret = substr($path, strlen(HC_WEBSITE_WEBROOT));	
  }	
  return $ret;
} 
}
 
if (!function_exists("HTMLencode")) { 
/**
 * Encode any HTML of a given string
 * 
 * @param string $s the string to encode
 * @param bool $withBR keep the BR tag, true/false
 * @return string the string encoded
 */
function HTMLencode(?string $s, bool $withBR = false): string 
{
  if (!isset($s)) {
    return HC_STR;
  }
    
  $s = str_ireplace("&#39;", "'", $s); 
  $s = str_ireplace("&#34;", "\"", $s);
  $s = str_ireplace("\\n", chr(10), $s);
  $s = htmlspecialchars($s, ENT_QUOTES |ENT_IGNORE | ENT_HTML5, "UTF-8");
    
  if ($withBR) {
    $s = str_ireplace(chr(10), HC_BR, $s);
  }  
    
  return $s;
} 
}

if (!function_exists("is_word")) {
/**
 * Determine if the given string is a word 
 * 
 * @param string $string the string being checked
 * @return bool true/false if it a word o not    
 */
 function is_word(string $string) {
   return preg_match("/^[\w\-\.]+?$/", $string);	 
 }	 

}

if (!function_exists("left")) { 
/**
 * Left cut the given substring for the specified length 
 * 
 * @param string $string the string being cut on the left
 * @param int $length the length of the substring to return
 * @return string the resulting substring    
 */
function left(?string $string, int $length): string 
{
  if (!isset($string) || $string === HC_STR) {
    return HC_STR;
  }
  return mb_substr($string, 0, $length);
}
}

if (!function_exists("right")) {
/**
 * Right cut the given string for the specified length 
 * 
 * @param string $string the string being cut on the right
 * @param int $length the length of the substring to return
 * @return string the resulting substring    
 */
function right(?string $string, int $length): string 
{
  if (!isset($string) || $string === HC_STR) {
    return HC_STR;
  }  
  return mb_substr($string, mb_strlen($string) - $length);
}
}

