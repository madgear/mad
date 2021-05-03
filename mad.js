function msgbox(e,o,s){var t,a;1==e&&(t="bg-danger m-2 ontop "),2==e&&(t="bg-success m-2"),3==e&&(t="bg-info m-2"),a=o,$(document).Toasts("create",{title:a,fixed:!0,class:t,autohide:!0,delay:3e3,body:s})}
function number_only(e,x){if(e.keyCode>47 && e.keyCode<58 || e.keyCode==46){if(e.keyCode==46){var txt=x.value;var n=txt.indexOf(".");if(n!=-1){e.preventDefault(true);e.stopPropagation(true);}}}else{e.preventDefault(true);e.stopPropagation(true);}}
function s_html(s_id,response){document.getElementById(s_id).innerHTML=response;}
function g_html(g_id){return document.getElementById(g_id).innerHTML;}
function s_value(s_id,svalue){document.getElementById(s_id).value=svalue;}
function g_value(g_id){return document.getElementById(g_id).value;}
function center_div(e){var t=document.body,n=document.documentElement,o=Math.max(t.scrollHeight,t.offsetHeight,n.clientHeight,n.scrollHeight,n.offsetHeight),c=document.getElementById(e).offsetHeight;document.getElementById(e).style.marginTop=o/2-+c/2+"px"}
