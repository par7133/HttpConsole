# Http Console

Hello and welcome to Http Console!   
   
Http Console is a light and simple web console to manage your website.     
   
Http Console is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.    
   
First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.   
   
As you are going to run Http Console in the PHP process context, using a limited web server or phpfpm user, 
you must follow some simple directives for an optimal first setup:   
 
1. Create a "stage" folder in your web app path; give to the stage folder the write permissions; and set the stage path in the config file.
2. In the stage path create a ".HCsampledir" folder and give to this folder the write permission. This folder will be the sample folder to copy from new folders inside the stage path.   
3. Likewise, in the stage path create an empty ".HCsamplefile" and give to this file the write permission. This file will be the sample file to copy from new files inside the stage path.     
4. Configure the max history items and max recall history items as required (default: 50).        
  
Http Console understands a limited set of commands with a far limited set of parameters:  
cd, cd.., cp, cp -p, cp -R, help, ls, ls -lsa, mv, pwd  	   
  
Hope you can enjoy it and let us know about any feedback: posta@elettronica.lol   
	   
### Screenshot:

 ![Http Console in action](/HCres/screenshot1.png)
