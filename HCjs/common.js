/**
 * Copyright (c) 2016, 2024, the Open Gallery's contributors
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
 * commons.js
 * 
 * Common constants and functions
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2016, 2024, the Open Gallery's contributors     
 * @license https://opensource.org/licenses/BSD-3-Clause 
 */

var blinkTimeoutID = 0;

bFullImageVisible = false;

/**
 * Manage the Back/Forward/Refresh button press event is some specific
 * conditions such as:
 *  - inside fullscreen image views 
 * 
 * @param {Event} e The window beforeUnoad event object
 * @returns {String} the message to show to the user
 */
function beforeUnload(e) {
  if (bFullImageVisible) {
    var confMsg = "\o/";
    e.returnValue = confMsg;
    e.preventDefault();    
    return confMsg;
  }  
}  

/**
 * Blink the the given text
 * 
 * @param {string} tagID the tag ID containing the text to blink
 * @param {string} color the color of the text
 * @param {int} interval the interval of the "flashing"
 * @returns {void}
 */
function blinkMe(tagID, color, interval) {
  if ( $(tagID) ) {
    if ($(tagID).css("color") !== "transparent") {
      color = $(tagID).css("color");
      $(tagID).css("color", "transparent");
    } else {
      $(tagID).css("color",  color.toString());
    }
  }
  setTimeout("blinkMe()", interval, tagID, color, interval);
}  

/**
 * Blink the the given text
 * 
 * @param {string} tagID the tag ID containing the text to blink
 * @param {int} interval the interval of the "flashing"
 * @returns {void}
 */
function blinkMe2(tagID, interval) {
  
  if (blinkTimeoutID!==0) {
    clearTimeout(blinkTimeoutID);
  }  
  if ( $(tagID) ) {
    if ($(tagID).css("visibility") !== "hidden") {
      $(tagID).css("visibility", "hidden");
    } else {
      $(tagID).css("visibility", "visible");
    }
  }
  
  blinkTimeoutID = setTimeout(blinkMe2, interval, tagID, interval);
}  


/**
 * Copy the text of the given control to the clipboard
 * 
 * @param {string} selector the selector of the control
 * @returns {none} 
 */
function copyTextToClipboard(selector) {
  var control = $(selector);
  control.select();
  try {
    document.execCommand("copy");
  } catch (e) {
  }
}

/**
 * Count items into a selector collection  
 * 
 * @param {string} selector the selector for the collection
 * @returns {integer}
 */
function countItemsBySelector(selector) {
  i=0;
  $(selector).each(function() {
    i=i+1;
  });
  return i;
}

/**
 * Encrypt the given string
 * 
 * @param {string} string - The string to encrypt
 * @returns {string} the encrypted string
 */
function encryptSha2(string) {
  var jsSHAo = new jsSHA("SHA-256", "TEXT", 1);
  jsSHAo.update(string);
  return jsSHAo.getHash("HEX");
}

/**
 * Delay the runtime execution for the given interval of time
 * 
 * @param {int} interval -the interval of time of the delay
 * @returns {void}
 */
function delay(interval) { 
  var d = new Date();
  startTime = d.getTime();
  endTime = startTime;
  while((endTime - startTime) < interval) {
    d = new Date(); 
    endTime = d.getTime();
    //console.log(endTime - startTime);
  }  
}

/**
 * Filter the keys of the given search query input box
 * 
 * @param {object} this1 - The search query input box to filter
 * @returns {none}
 */
function filterKeysQ(this1) {
  var value = $(this1).val();
  //var re = /[^\w\-: ]/gui;
  var re = new RegExp(/[^\w\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\-: ]/, "gui");
  if (re.test(value)) {
    $(this1).val(value.replace(re, ""));
  }
}

/**
 * Filter the keys of the given email input box
 * 
 * @param {object} this1 - The email input box to filter
 * @returns {none}
 */
function filterKeysEmail(this1) {
  var value = $(this1).val();
  var re = new RegExp(/[^A-Za-z0-9-_@.]/, "g");
  //$(this1).val(value.replace(/[^A-Za-z0-9-_@.]/g, "")); 
  if (re.test(value)) {
    $(this1).val(value.replace(re, ""));
  }  
}

/**
 * Filter the keys of the given image name input box
 * 
 * @param {object} this1 - The name input box to filter
 * @returns {none}
 */
function filterKeysImageName(this1) {
  var value = $(this1).val();
  //$(this1).val(value.replace(/[^A-Za-z0-9-_\s]/, "")); 
  //var re = new RegExp('/[^A-Za-z0-9\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\-\s]/gui');
  //re.compile();
  //if (re.test(value)) {
    $(this1).val(value.replace(/[^A-Za-z0-9\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\-\s]/gi, ""));
  //}  
}

/**
 * Filter the keys of the given image tags input box
 * 
 * @param {object} this1 - The tags input box to filter
 * @returns {none}
 */
function filterKeysImageTags(this1) {
  var value = $(this1).val();
  //$(this1).val(value.replace(/[^A-Za-z0-9-_\s]/, "")); 
  //var re = new RegExp(/[^\w\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\-\s]/, "gui");
  //if (re.test(value)) {
    $(this1).val(value.replace(/[^\w\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\-\s]/gi, ""));
  //}  
}

/**
 * Filter the keys of the given image url input box
 * 
 * @param {object} this1 - The url input box to filter
 * @returns {none}
 */
function filterKeysImageUrl(this1) {
  var value = $(this1).val();
  //$(this1).val(value.replace(/[^A-Za-z0-9-_]/, ""));
  //var re = new RegExp('/[^\w\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\-]/gui');
  //re.compile();
  //if (re.test(value)) {
    $(this1).val(value.replace(/[^\w\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\-]/gi, ""));
  //}  
}

/**
 * Filter the keys of the given email input box
 * 
 * @param {object} this1 - The email input box to filter
 * @returns {none}
 */
function filterKeysUsername(this1) {
  var value = $(this1).val();
  //$(this1).val(value.replace(/[^A-Za-z0-9-_]/, ""));
  var re = new RegExp(/[^\w\-]/, "gui");
  if (re.test(value)) {
    $(this1).val(value.replace(re, ""));
  }  
}

/**
 * Return the array key of a given value
 * 
 * @param {array} array - The array to search for the value
 * @param {string} value - The value to search
 * @returns {string} The key corrisponding to the value, if exists
 */
function getArrayKeyByValue(array, value) {
  for (var key in array) {
    if (key === 'length' || !array.hasOwnProperty(key)) continue;
    if (array[key]===value) {
      return key;
    }  
  }  
  return "";
}  

/**
 * Get the height of the whole document
 * 
 * @param {none} 
 * @returns {int} the document height
 */
function getDocHeight() {
  var D = document;
  return Math.max(
      D.body.scrollHeight, D.documentElement.scrollHeight,
      D.body.offsetHeight, D.documentElement.offsetHeight,
      D.body.clientHeight, D.documentElement.clientHeight
  );
}

function getDocHeight2() {
  var D = document;
  var scrollMaxY;
  if (window.scrollMaxY) {
    scrollMaxY = window.scrollMaxY;
  } else {
    scrollMaxY = D.documentElement.scrollHeight;
  }
  var height = Math.max(
      D.body.scrollHeight, scrollMaxY,    
      D.body.offsetHeight, D.documentElement.offsetHeight,
      D.body.clientHeight, D.documentElement.clientHeight
  );
  return height;
}


/**
 * Get the width of the whole document
 * 
 * @param {none} 
 * @returns {int} the document width
 */
function getDocWidth() {
  var D = document;
  return Math.max(
      D.body.scrollWidth, D.documentElement.scrollWidth,
      D.body.offsetWidth, D.documentElement.offsetWidth,
      D.body.clientWidth, D.documentElement.clientWidth
  );
}

function getDocWidth2() {
  var D = document;
  var scrollMaxX;
  if (window.scrollMaxX) {
    scrollMaxX = window.scrollMaxX;
  } else {
    scrollMaxX = D.documentElement.scrollWidth;
  }
  return Math.max(
      D.body.scrollWidth, scrollMaxX,
      D.body.offsetWidth, D.documentElement.offsetWidth,
      D.body.clientWidth, D.documentElement.clientWidth
  );
}

function getTimestampInSec() {
  var d = new Date();
  timestamp = parseInt(d.getTime() / 1000);
  return timestamp;
}

function getWindowScrollX() {
  var supportPageOffset = window.pageXOffset !== undefined;
  var isCSS1Compat = ((document.compatMode || "") === "CSS1Compat");
  return supportPageOffset ? window.pageXOffset : isCSS1Compat ? document.documentElement.scrollLeft : document.body.scrollLeft;
}  

function getWindowScrollY() {
  var supportPageOffset = window.pageYOffset !== undefined;
  var isCSS1Compat = ((document.compatMode || "") === "CSS1Compat");
  return supportPageOffset ? window.pageYOffset : isCSS1Compat ? document.documentElement.scrollTop : document.body.scrollTop;
}  
  
/**
 * Check if it is a valid username
 * 
 * @param {string} s - The string to check
 * @returns {bool} if it is a valid username, true/false
 */
function isUsername(s) {

  //var usernameRegEx = /^[a-zA-Z0-9_-]+?$/;
  //var usernameRegEx = /^[\w\-]+?$/;
  //var re = new RegExp(/^[\w\-]+$/, "i");
  var re = /^[\w\-]+$/i;
  
  return (re.test(s) && s.length>=3 && s.length<=20);
}

/**
 * Check if it is a valid email
 * 
 * @param {string} s - The string to check
 * @returns {bool} if it is a valid email, true/false
 */
function isEmail(s) {

  //var re = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  var re = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return (re.test(s) && s.length>=8 && s.length<=255);
}

/**
 * Check if it is a valid image name
 * 
 * @param {string} s - The string to check
 * @returns {bool} if it is a valid image name, true/false
 */
function isImageName(s) {

  //var imageNameRegEx = /^[a-zA-Z0-9_-\s]+?$/;
  //var imageNameRegEx = /^[\w\-\s]+?$/;
  var re = new RegExp('/^[A-Za-z0-9\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\-\s]+?$/ui');
  re.compile();
  return (re.test(s) && s.length>=3 && s.length<=50);
}

/**
 * Check if it is a valid image url
 * 
 * @param {string} s - The string to check
 * @returns {bool} if it is a valid image url, true/false
 */
function isImageUrl(s) {

  //var imageUrlRegEx = /^[a-zA-Z0-9_-]+?$/;
  //var imageUrlRegEx = /^[\w\-]+?$/;
  var re = new RegExp('/^[\w\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\-]+?$/ui');
  re.compile();
  return (re.test(s) && s.length>=3 && s.length<=255);
}

/**
 * Check if it is a latin lang string
 * 
 * @param {string} s - The string to check
 * @returns {bool} if it is a latin lang string, true/false
 */
function isLatinLang(s) {

  var re = new RegExp(/^[\u31C0-\u31EF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F]+?$/, "gui");
  return (!re.test(s));
}

/**
 * Load the footer content
 * 
 * @param {string} scriptName the script name
 * @returns {none}
 */
function loadPageFooter(scriptName) {
  $("div.footer").load("/footercontent?SCRIPT_NAME=" + scriptName + "&v=" + rnd(50000, 99999)); 
}

/**
 * Load the header content
 * 
 * @param {string} scriptName the script name
 * @param {string} q the query string
 * @returns {none}
 */
function loadPageHeader(scriptName, q, catMaskedPath, platform, errNo, errMsg, errScript, errLine, errStack, loginLnp) {
  $("div.header").load("/headercontent?SCRIPT_NAME=" + scriptName + "&q=" + q + "&catMaskedPath=" + catMaskedPath + "&platform=" + platform + "&errNo=" + errNo + "&errMsg=" + errMsg + "&errScript=" + errScript  + "&errLine=" + errLine + "&errStack=" + errStack + "&loginLnp=" + loginLnp); 
}

/**
 * Open a link from any event handler
 * 
 * @param {string} href the link to open
 * @param {string} target the frame target
 * @returns {none}
 */
function openLink(href, target) {
  
  window.open(href, target);
}

function rnd(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min +1)) + min;
}

/**
 * Retain the focus at the given control
 * 
 * @param {object} self - the given control 
 * @returns {none} none 
 */
function retainFocus(self) {
  setTimeout(function() { self.focus(); }, 10);
}

/**
 * Position a div box to the middle of the screen
 * 
 * @param {object} box div box to position
 * @returns {none}
 */
function setBoxToMiddle(box) {
  boxHeight = parseInt(box.height());
  box.css("top", "-" + (parseInt(screen.availHeight - boxHeight)) + "px");
}

/**
 * Position a div box to the top of the screen
 * 
 * @param {jQuery} box div box to position
 * @returns {none}
 */
function setBoxToTop(box) {
//undo last change (and remove all the following)
////  boxHeight = parseInt(box.height());
////  box.css("top", "-" + (parseInt((getDocHeight2()-boxHeight)/2)) + "px");
//  box.css("position", "relative");
//  box.css("top", "20px");
// end undo
  boxWidth = parseInt(box.width());
  docWidth = parseInt(box.parent().width()); //getDocWidth2();
  box.css("position", "absolute");
  box.css("top", (window.scrollY + 20) + "px");
  box.css("left", parseInt(((docWidth - boxWidth) / 2)) + "px");
}

/**
 * Resize a div container to the doc height
 * 
 * @param {object} container div container to resize
 * @returns {none}
 */
function setContToDocHeight(container) {
  container.css("height", getDocHeight() + "px");
}

/**
 * Set the footer position
 * 
 * @returns {void}
 */
function setFooterPosition() {

  if ($(".footer")) {
    //$(".footer").attr("top", "-3000px");
    //var docHeight = parseInt(getDocHeight2());
    bodyRect = document.body.getBoundingClientRect();
    var docHeight = bodyRect.height;
    //$(".body-area").css("height", "500px");
    //$("#linkContainer").css("height", "500px");
    $("#linkContainer").css("height", parseInt(docHeight) + "px");
    //var screenHeight = parseInt(screen.availHeight);
    //var screenWidth = parseInt(screen.availWidth);
    //var footerRect = document.getElementsByClassName("footer")[0].getBoundingClientRect();
    //var footerTollerance = 25;
    var footerHeight = 80;
    var newTop = docHeight - footerHeight;

    if (newTop < 550) { 
      newTop = 550;
    }
    
    /*
    // if in home page..
    if (document.getElementById("linkContainer")) {
      
      var headerHeight = 300;
      var linkContainerRect = document.getElementById("linkContainer").getBoundingClientRect();
      //var bodyAreaRect = document.getElementsByClassName("body-area")[0].getBoundingClientRect();
      newTop = linkContainerRect.height + headerHeight;
       
      if (newTop < (screenHeight -footerHeight)) {
        newTop = screenHeight - (footerHeight*1) - footerTollerance;
      }  
      
      //document.getElementsByClassName("body-area")[0].style.height = parseInt(((screenHeight - (footerHeight*1) - footerTollerance) * 100) / screenHeight) + "%";
    }  
    */
    //
    //if (newTop < window.innerHeight) {
    //  //newTop = window.innerHeight - footerRect.height;
    //} 
 
    newTop = newTop - 25;
   
    $(".footer").css("top", newTop.toString() + "px");
  
    // if in search page..
    if (document.getElementById("qrcodeCont") && document.getElementById("frmSearch")) {
      //var frmSearchRect = document.getElementById("frmSearch").getBoundingClientRect();  
      var qrcodeContRect = document.getElementById("qrcodeCont").getBoundingClientRect();  
      //newTop = docHeight - footerRect.height - qrcodeContRect.height;
            
      newTop = parseInt($(".footer").css("top")) - qrcodeContRect.height - 50;
      
//$("#qrcodeTitle").text(newTop);
      //if (newTop < 320) { 
      //  newTop = 320;
      //}
      $("#qrcodeCont").css("top", newTop.toString() + "px");
    }  

    setFeedbackIconPos();

    //setFeedbackPos();
    
    newTop = parseInt($(".footer").css("top")) + 25;
    
    $(".footer").css("top", newTop.toString() + "px");
  }
}

function setFooterPos() {
  if (document.getElementById("footerCont")) {
    tollerance = 25;
    $("#footerCont").css("top", parseInt( window.innerHeight - $("#footerCont").height() - tollerance ) + "px");
    $("#footer").css("top", parseInt( window.innerHeight - $("#footer").height() - tollerance ) + "px");
  }
}

function setCookieBannerPos() {
  if (document.getElementById("bannerCookies")) {
    tollerance = 30;
    bodyRect = document.body.getBoundingClientRect();
    docHeight = parseInt(getDocHeight2());
    $("#bannerCookies").css("top", parseInt(window.scrollY + window.innerHeight - ($("#bannerCookies").height() + tollerance)));
    $("#bannerCookies").css("left", parseInt(bodyRect.width - ($("#bannerCookies").width() + tollerance)));
  }
}

function setFeedbackPos() {
  if (document.getElementById("popupFeedback")) {
    $("#popupFeedback").attr("top", "-3000px");
    $("#popupFeedback").attr("left", "-3000px");
    bodyRect = document.body.getBoundingClientRect();
    var docHeight = parseInt(getDocHeight2()); //window.scrollY + bodyRect.height;
    var docWidth = bodyRect.width;
    var feedbackRect = document.getElementById("popupFeedback").getBoundingClientRect();
    newTop = docHeight - (feedbackRect.height + 10);
    newLeft = docWidth - (feedbackRect.width + 10);
    $("#popupFeedback").css("top", newTop.toString() + "px");
    $("#popupFeedback").css("left", newLeft.toString() + "px");
  }
}

function setFeedbackIconPos() {
  if (document.getElementById("butFeedback")) {
    bodyRect = document.body.getBoundingClientRect();
    var docHeight = parseInt(getDocHeight2());
    var docWidth = bodyRect.width; //window.scrollX + bodyRect.width;
    var feedbackIconRect = document.getElementById("butFeedback").getBoundingClientRect();
    newTop = -65; //docHeight - (feedbackIconRect.height + 10);
    newLeft = docWidth - (55 + 10);
    $("#butFeedback").css("top", newTop.toString() + "px");
    $("#butFeedback").css("left", newLeft.toString() + "px");
    $("#butFeedback").css("display", "inline");
  }
}

function setParentButPos() {
  bodyRect = document.body.getBoundingClientRect();
  var docWidth = bodyRect.width;
  if (docWidth < 850) {
    $("div#butParent").css("left","+5px");
  } else {
    $("div#butParent").css("left","+40px");
  }
  $("div#butParent").css("display","inline");
}

// Global window load event handler..
//window.addEventListener("scroll", function() {

  // Reposition footer..
  //$(".footer").attr("top", "-3000px");
  //$("#popupFeedback").attr("top", "-3000px");
  //$("#popupFeedback").attr("left", "-3000px");
  //setTimeout("setFooterPosition2()", 1000);

  //$("#bannerCookies").attr("top", "-3000px");
  //setTimeout("setCookieBannerPos()", 4500);
  
//}, true);

function resetDispElmnts() {
  $(".footer").attr("top", "-3000px");
  //$(".body-area").css("height", "500px");
  //$("#linkContainer").css("height", "500px");
}

// Global window resize event handler..
window.addEventListener("resize", function() {
  
  // Reposition footer..
  //resetDispElmnts();
  //$(".footer").attr("top", "-3000px");
  //$("#popupFeedback").attr("top", "-3000px");
  //$("#popupFeedback").attr("left", "-3000px");  
  setTimeout("setFooterPos()", 3000);
  bodyRect = document.body.getBoundingClientRect();
  bodyRect.width = window.innerWidth;  
  $(".body-area").css("width", parseInt(window.innerWidth)+"px");
  $(".footer").css("width", parseInt(window.innerWidth)+"px");
  $("#footerCont").css("width", parseInt(window.innerWidth)+"px");
  
  //$("#bannerCookies").attr("top", "-3000px");
  //setTimeout("setCookieBannerPos()", 4500);
  
  // Resizing the burger menu..
  if (bBurgerMenuVisible) {
    $("div#contBurgerMenuBox").css("height", getDocHeight2() + "px"); 
  }

  setTimeout("setParentButPos()", 1300);

}, true);


// Global window load event handler..
window.addEventListener("load", function() {

  // Reposition footer..
  //resetDispElmnts();
  //$(".footer").attr("top", "-3000px");
  //$("#popupFeedback").attr("top", "-3000px");
  //$("#popupFeedback").attr("left", "-3000px");
  setTimeout("setFooterPos()", 3000);
  bodyRect = document.body.getBoundingClientRect();
  bodyRect.width = window.innerWidth;  
  $(".body-area").css("width", parseInt(window.innerWidth)+"px");
  $(".footer").css("width", parseInt(window.innerWidth)+"px");
  $("#footerCont").css("width", parseInt(window.innerWidth)+"px");
  
  //$("#bannerCookies").attr("top", "-3000px");
  //setTimeout("setCookieBannerPos()", 4500);
  
  setTimeout("setParentButPos()", 1300);
  
}, true);


$(document).ready(function() {
  $(".content-area-results").on("click", function() {
    // Hide the Feedback for if visible..
    if (bFeedbackVisible) {
      $("#popupFeedback").css("display", "none");
      bFeedbackVisible = false;
    }  
  });
});  
