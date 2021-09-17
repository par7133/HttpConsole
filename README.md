# HttpConsole

Hello and welcome to Http Console!   
   
Http Console is a light and simple web console to admin your website.     
   
Http Console is supplied AS-IS and we do not take any responsibility for its misusage.    
   
First step, use the left side panel password and salt fields to create the hash to insert in the config file.   
Remember to manually set there also the salt value.   
   
As you are going to make work Http Console in the PHP process environment, using a limited web server or phpfpm user, 
we reccomend you to follow some simple directives for an optimal first setup:   
 
1. We encourage you to setup a "stage" folder in your web app path; give to the stage folder the write permissions; and set the stage path in the config file as *cd depth*.
2. Inside the stage path create a "sample" folder and give to this folder the write permission.  
This folder will be the sample folder to copy from to create new folders with write permissions inside the stage path.  
3. Likewise create an "upload" folder inside the stage path giving the right permissions.  
4. Configure the max history items and max recall history items as required (default: 50).        
  
Http Console understands a limited set of commands with a far limited set of parameters:  
cd, cd.., cp, cp -R, ls, ls -lsa, mkdir, mv, pwd  	   
  
Hope you can enjoy it and let us know about any feedback: info@httpconsole.com   
	   
