CKEDITOR.on("instanceCreated",function(b){var a=b.editor;a.on("uiSpace",function(){if(a.elementMode!==CKEDITOR.ELEMENT_MODE_INLINE)for(i in a.config.toolbar){var c=a.config.toolbar[i].indexOf("Sourcedialog");-1<c&&a.config.toolbar[i].splice(c,1)}var c="About Bold Italic Underline Scayt Strike Subscript Superscript BidiLtr BidiRtl Blockquote Cut Copy Paste TextColor BGColor Templates CreateDiv - NumberedList BulletedList Indent Outdent Find Replace Flash Font FontSize Form Checkbox Radio TextField Textarea Select Button ImageButton HiddenField Format HorizontalRule Iframe Image Smiley JustifyLeft JustifyCenter JustifyRight JustifyBlock Link Unlink Anchor Maximize NewPage PageBreak PasteText PasteFromWord RemoveFormat Save SelectAll ShowBlocks Source Sourcedialog SpecialChar Styles Table Undo Redo".split(" "),
b=[];for(i in a.ui.items)-1===jQuery.inArray(i,c)&&b.push(i);0!=b.length&&a.config.toolbar.push(b)})});
CKEDITOR.on("dialogDefinition",function(b){if("undefined"!=typeof gptitles){var a=b.data.definition;if("link"==b.data.name){var c=!1;b=a.getContents("info").get("protocol");b["default"]="";b.items.unshift(["",""]);a.onOk=CKEDITOR.tools.override(a.onOk,function(a){return function(b){return c?c=!1:a.call(this,b)}});a.onLoad=CKEDITOR.tools.override(a.onLoad,function(a){return function(){a.call(this);var b=this.getContentElement("info","url").getInputElement().$,e=this.getContentElement("info","protocol").getInputElement().$;
$(b).css({position:"relative",zIndex:12E3}).autocomplete({source:gptitles,delay:100,minLength:0,select:function(a,d){if(d.item)return b.value=encodeURI(d.item[1]),e.value="",13==a.which&&(c=!0),a.stopPropagation(),!1}}).data("ui-autocomplete")._renderItem=function(a,b){return $("<li></li>").data("ui-autocomplete-item",b[1]).append("<a>"+$gp.htmlchars(b[0])+"<span>"+$gp.htmlchars(b[1])+"</span></a>").appendTo(a)}}})}}});