var gp_editing={id:function(a){return $(a).attr("id").substr(13)},new_edit_area:function(a){a=gp_editing.id(a);return gp_editing.get_edit_area(a)},get_path:function(a){a=$("a#ExtraEditLink"+a);return 0==a.length?!1:a.attr("href")},get_edit_area:function(a){a=$("#ExtraEditArea"+a);if(0==a.length)return!1;$("#edit_area_overlay_top").hide();$(".ExtraEditLink").remove();$(".editable_area").unbind(".gp");var c=a.find(".twysiwygr:first");c.length&&(a=c);a.addClass("gp_editing");return a},close_editor:function(a){a.preventDefault();
$gp.Reload()},save_changes:function(a,c){a.preventDefault();if(gp_editor){$gp.loading();var b=gp_editor.save_path,b=strip_from(b,"#"),d="";0<b.indexOf("?")&&(d=strip_to(b,"?")+"&");d=d+"cmd=save&"+gp_editor.gp_saveData();gpresponse.ck_saved=function(){gp_editor&&(gp_editor.updateElement(),gp_editor.resetDirty(),"ck_close"==c&&gp_editing.close_editor(a))};$gp.postC(b,d)}},editor_tools:function(){$("#ckeditor_top").html("");$("#ckeditor_bottom").html("");SimpleDrag("#ckeditor_area .toolbar","#ckeditor_area",
"fixed",function(a){gpui.ckx=a.left;gpui.cky=a.top;gpui.ckd&&(gpui.ckd=!1,gp_editing.setdock(!0));$gp.SaveGPUI()});gp_editing.setdock(!1)},setdock:function(a){var c=$("#ckeditor_wrap").show(),b=$("#ckeditor_area").show(),d=$("body");a&&$gp.SaveGPUI();gpui.ckd?(b.addClass("docked").removeClass("keep_viewable"),d.css({"margin-top":"+=30px"}),c.css({height:30}),b.css({top:"auto",left:0,bottom:0}).bind("mouseenter.gpdock",function(){c.stop(!0,!0,!0).animate({height:b.height()},100)}).bind("mouseleave.gpdock",
function(){c.stop(!0,!0,!0).delay(500).animate({height:30})})):(b.removeClass("docked").addClass("keep_viewable"),a&&$("body").css({"margin-top":"-=30px"}),c.css({height:0}),b.css({top:gpui.cky,left:gpui.ckx,bottom:"auto"}).unbind(".gpdock"),$gp.$win.resize())},strip_special:function(a){return a}};$gp.links.ck_close=gp_editing.close_editor;$gp.links.ck_save=gp_editing.save_changes;gplinks.ck_docklink=function(a,c){gpui.ckd=!gpui.ckd;gp_editing.setdock(!0)};