function gp_init_inline_edit(b,d){"object"==typeof gp_add_plugins&&$.each(gp_add_plugins,function(a,b){CKEDITOR.plugins.addExternal(a,b)});var c=gp_editing.get_path(b),a=gp_editing.get_edit_area(b);!1!=a&&!1!=c&&(gp_editing.editor_tools(),a.prop("contenteditable",!0),a=a.get(0),a.innerHTML=d.content,CKEDITOR.disableAutoInline=!0,gp_editor=CKEDITOR.inline(a,gp_ckconfig),gp_editor.save_path=c,gp_editor.gp_saveData=function(){var a=gp_editor.getData();return"gpcontent="+encodeURIComponent(a)},$gp.loaded())}
;