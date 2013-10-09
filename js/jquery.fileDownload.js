/*
* jQuery File Download Plugin v1.3.3
*
* http://www.johnculviner.com
*
* Copyright (c) 2012 - John Culviner
*
* Licensed under the MIT license:
*   http://www.opensource.org/licenses/mit-license.php
*/
(function(e){e.extend({fileDownload:function(t,n){function y(){if(document.cookie.indexOf(i.cookieName+"="+i.cookieValue)!=-1){c.onSuccess(t);var n=new Date(1e3);document.cookie=i.cookieName+"=; expires="+n.toUTCString()+"; path="+i.cookiePath;w(false);return}if(p||h){try{var r;if(p){r=p.document}else{r=b(h)}if(r&&r.body!=null&&r.body.innerHTML.length>0){var s=true;if(v&&v.length>0){var o=e(r.body).contents().first();if(o.length>0&&o[0]===v[0]){s=false}}if(s){c.onFail(r.body.innerHTML,t);w(true);return}}}catch(u){c.onFail("",t);w(true);return}}setTimeout(y,i.checkInterval)}function b(e){var t=e[0].contentWindow||e[0].contentDocument;if(t.document){t=t.document}return t}function w(e){setTimeout(function(){if(p){if(u){p.close()}if(o){if(e){p.focus();p.close()}else{p.focus()}}}},0)}function E(e){return e.replace(/[<>&\r\n"']/gm,function(e){return"&"+{"<":"lt;",">":"gt;","&":"amp;","\r":"#13;","\n":"#10;",'"':"quot;","'":"apos;"}[e]})}var r=function(e,t){alert("A file download error has occurred, please try again.")};var i=e.extend({preparingMessageHtml:null,failMessageHtml:null,androidPostUnsupportedMessageHtml:"Unfortunately your Android browser doesn't support this type of file download. Please try again with a different browser.",dialogOptions:{modal:true},successCallback:function(e){},failCallback:r,httpMethod:"GET",data:null,checkInterval:100,cookieName:"fileDownload",cookieValue:"true",cookiePath:"/",popupWindowTitle:"Initiating file download...",encodeHTMLEntities:true},n);var s=(navigator.userAgent||navigator.vendor||window.opera).toLowerCase();var o=false;var u=false;var a=false;if(/ip(ad|hone|od)/.test(s)){o=true}else if(s.indexOf("android")!=-1){u=true}else{a=/avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|playbook|silk|iemobile|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(s)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(s.substr(0,4))}var f=i.httpMethod.toUpperCase();if(u&&f!="GET"){if(e().dialog){e("<div>").html(i.androidPostUnsupportedMessageHtml).dialog(i.dialogOptions)}else{alert(i.androidPostUnsupportedMessageHtml)}return}var l=null;if(i.preparingMessageHtml){l=e("<div>").html(i.preparingMessageHtml).dialog(i.dialogOptions)}var c={onSuccess:function(e){if(l){l.dialog("close")}i.successCallback(e)},onFail:function(t,n){if(l){l.dialog("close")}if(i.failMessageHtml){e("<div>").html(i.failMessageHtml).dialog(i.dialogOptions);if(i.failCallback!=r){i.failCallback(t,n)}}else{i.failCallback(t,n)}}};if(i.data!==null&&typeof i.data!=="string"){i.data=e.param(i.data)}var h,p,d,v;if(f==="GET"){if(i.data!==null){var m=t.indexOf("?");if(m!=-1){if(t.substring(t.length-1)!=="&"){t=t+"&"}}else{t=t+"?"}t=t+i.data}if(o||u){p=window.open(t);p.document.title=i.popupWindowTitle;window.focus()}else if(a){window.location(t)}else{h=e("<iframe>").hide().attr("src",t).appendTo("body")}}else{var g="";if(i.data!==null){e.each(i.data.replace(/\+/g," ").split("&"),function(){var e=this.split("=");var t=i.encodeHTMLEntities?E(decodeURIComponent(e[0])):decodeURIComponent(e[0]);if(!t)return;var n=e[1]||"";n=i.encodeHTMLEntities?E(decodeURIComponent(e[1])):decodeURIComponent(e[1]);g+='<input type="hidden" name="'+t+'" value="'+n+'" />'})}if(a){v=e("<form>").appendTo("body");v.hide().attr("method",i.httpMethod).attr("action",t).html(g)}else{if(o){p=window.open("about:blank");p.document.title=i.popupWindowTitle;d=p.document;window.focus()}else{h=e("<iframe style='display: none' src='about:blank'></iframe>").appendTo("body");d=b(h)}d.write("<html><head></head><body><form method='"+i.httpMethod+"' action='"+t+"'>"+g+"</form>"+i.popupWindowTitle+"</body></html>");v=e(d).find("form")}v.submit()}setTimeout(y,i.checkInterval)}})})(jQuery)