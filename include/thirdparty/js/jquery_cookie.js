(function(e,h,l){function m(b){return b}function n(b){return decodeURIComponent(b.replace(p," "))}var p=/\+/g,d=e.cookie=function(b,c,a){if(c!==l){a=e.extend({},d.defaults,a);null===c&&(a.expires=-1);if("number"===typeof a.expires){var f=a.expires,g=a.expires=new Date;g.setDate(g.getDate()+f)}c=d.json?JSON.stringify(c):String(c);return h.cookie=[encodeURIComponent(b),"=",d.raw?c:encodeURIComponent(c),a.expires?"; expires="+a.expires.toUTCString():"",a.path?"; path="+a.path:"",a.domain?"; domain="+
a.domain:"",a.secure?"; secure":""].join("")}c=d.raw?m:n;a=h.cookie.split("; ");f=0;for(g=a.length;f<g;f++){var k=a[f].split("=");if(c(k.shift())===b)return b=c(k.join("=")),d.json?JSON.parse(b):b}return null};d.defaults={};e.removeCookie=function(b,c){return null!==e.cookie(b)?(e.cookie(b,null,c),!0):!1}})(jQuery,document);