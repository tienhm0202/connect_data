 function EAL(){this.version="0.6.7";date=new Date();this.start_time=date.getTime();this.win="loading";this.error=false;this.baseURL="";this.template="";this.lang={};this.load_syntax={};this.syntax={};this.loadedFiles=[];this.waiting_loading={};this.scripts_to_load=[];this.sub_scripts_to_load=[];this.resize=[];this.hidden={};this.default_settings={debug: false ,smooth_selection: true ,font_size: "10" ,font_family: "monospace" ,start_highlight: false ,toolbar: "search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, |, help" ,begin_toolbar: "" ,end_toolbar: "" ,allow_resize: "both" ,min_width: 400 ,min_height: 100 ,replace_tab_by_spaces: false ,allow_toggle: true ,language: "en" ,syntax: "" ,display: "onload" ,max_undo: 20 ,browsers: "known" ,plugins: "" ,gecko_spellcheck: false ,fullscreen: false ,load_callback: "" ,save_callback: "" , save_as_callback: "", change_callback: "" ,submit_callback: "" ,EA_init_callback: "" ,EA_delete_callback: "" ,EA_load_callback: "" ,EA_unload_callback: "" ,EA_toggle_on_callback: "" ,EA_toggle_off_callback: "" };this.advanced_buttons=[ ['new_document', 'newdocument.gif', 'new_document'], ['search', 'search.gif', 'show_search'], ['go_to_line', 'go_to_line.gif', 'go_to_line'], ['undo', 'undo.gif', 'undo'], ['redo', 'redo.gif', 'redo'], ['change_smooth_selection', 'smooth_selection.gif', 'change_smooth_selection_mode'], ['reset_highlight', 'reset_highlight.gif', 'resync_highlight'], ['highlight', 'highlight.gif','change_highlight'], ['help', 'help.gif', 'show_help'], ['save', 'save.gif', 'save'], ['save_as', 'save_as.gif', 'save_as'], ['load', 'load.gif', 'load'], ['fullscreen', 'fullscreen.gif', 'toggle_full_screen'] ];ua=navigator.userAgent;this.nav={};this.nav['isIE']=(navigator.appName=="Microsoft Internet Explorer");if(this.nav['isIE']){this.nav['isIE']=ua.replace(/^.*?MSIE ([0-9\.]*).*$/, "$1");if(this.nav['isIE']<6) this.has_error();}if(this.nav['isNS']=ua.indexOf('Netscape/') !=-1){this.nav['isNS']=ua.substr(ua.indexOf('Netscape/')+9);if(this.nav['isNS']<8 || !this.nav['isIE']) this.has_error();}if(this.nav['isOpera']=(ua.indexOf('Opera') !=-1)){this.nav['isOpera']=ua.replace(/^.*?Opera.*?([0-9\.]+).*$/i, "$1");if(this.nav['isOpera']<9) this.has_error();this.nav['isIE']=false;}this.nav['isGecko']=(ua.indexOf('Gecko') !=-1);if(this.nav['isFirefox'] =(ua.indexOf('Firefox') !=-1)) this.nav['isFirefox']=ua.replace(/^.*?Firefox.*?([0-9\.]+).*$/i, "$1");if(this.nav['isIceweasel'] =(ua.indexOf('Iceweasel') !=-1)) this.nav['isFirefox']=this.nav['isIceweasel']=ua.replace(/^.*?Iceweasel.*?([0-9\.]+).*$/i, "$1");if(this.nav['isCamino'] =(ua.indexOf('Camino') !=-1)) this.nav['isCamino']=ua.replace(/^.*?Camino.*?([0-9\.]+).*$/i, "$1");this.nav['isSafari']=(ua.indexOf('Safari') !=-1);if(this.nav['isIE']>=6 || this.nav['isOpera']>=9 || this.nav['isFirefox'] || this.nav['isCamino']) this.nav['isValidBrowser']=true;
 else this.nav['isValidBrowser'] = false;
     this.set_base_url();
     for (var i = 0; i < this.scripts_to_load.length; i++) {
         setTimeout("eAL.load_script('" + this.baseURL + this.scripts_to_load[i] + ".js');", 1);
         this.waiting_loading[this.scripts_to_load[i] + ".js"] = false;
     }
     this.add_event(window, "load", EAL.prototype.window_loaded);
 }
 EAL.prototype.has_error = function () {
     this.error = true;
     for (var i in EAL.prototype) {
         EAL.prototype[i] = function () {
         };
     }
 };
 EAL.prototype.window_loaded = function () {
     eAL.win = "loaded";
     if (document.forms) {
         for (var i = 0; i < document.forms.length; i++) {
             var form = document.forms[i];
             form.edit_area_replaced_submit = null;
             try {
                 form.edit_area_replaced_submit = form.onsubmit;
                 form.onsubmit = "";
             } catch (e) {
             }
             eAL.add_event(form, "submit", EAL.prototype.submit);
             eAL.add_event(form, "reset", EAL.prototype.reset);
         }
     }
     eAL.add_event(window, "unload", function () {
         for (var i in eAs) {
             eAL.delete_instance(i);
         }
     });
 };
 EAL.prototype.init_ie_textarea = function (id) {
     textarea = document.getElementById(id);
     if (textarea && typeof(textarea.focused) == "undefined") {
         textarea.focus();
         textarea.focused = true;
         textarea.selectionStart = textarea.selectionEnd = 0;
         get_IE_selection(textarea);
         eAL.add_event(textarea, "focus", IE_textarea_focus);
         eAL.add_event(textarea, "blur", IE_textarea_blur);
     }
 };
 EAL.prototype.init = function (settings) {
     if (!settings["id"]) this.has_error();
     if (this.error) return;
     if (eAs[settings["id"]]) eAL.delete_instance(settings["id"]);
     for (var i in this.default_settings) {
         if (typeof(settings[i]) == "undefined") settings[i] = this.default_settings[i]
     }
     if (settings["browsers"] == "known" && this.nav['isValidBrowser'] == false) {
         return;
     }
     if (settings["begin_toolbar"].length > 0) settings["toolbar"] = settings["begin_toolbar"] + "," + settings["toolbar"];
     if (settings["end_toolbar"].length > 0) settings["toolbar"] = settings["toolbar"] + "," + settings["end_toolbar"];
     settings["tab_toolbar"] = settings["toolbar"].replace(/ /g, "").split(",");
     settings["plugins"] = settings["plugins"].replace(/ /g, "").split(",");
     for (var i = 0; i < settings["plugins"].length; i++) {
         if (settings["plugins"][i].length == 0) settings["plugins"].splice(i, 1);
     }
     this.get_template();
     this.load_script(this.baseURL + "langs/" + settings["language"] + ".js");
     if (settings["syntax"].length > 0) {
         settings["syntax"] = settings["syntax"].toLowerCase();
         this.load_script(this.baseURL + "reg_syntax/" + settings["syntax"] + ".js");
     }
     eAs[settings["id"]] = {"settings": settings};
     eAs[settings["id"]]["displayed"] = false;
     eAs[settings["id"]]["hidden"] = false;
     eAL.start(settings["id"]);
 };
 EAL.prototype.delete_instance = function (id) {
     eAL.execCommand(id, "EA_delete");
     if (window.frames["frame_" + id] && eAs[id]["displayed"]) window.frames["frame_" + id].editArea.execCommand("EA_unload");
     eAL.toggle(id, "off");
     var span = document.getElementById("EditAreaArroundInfos_" + id);
     if (span) {
         span.parentNode.removeChild(span);
     }
     var iframe = document.getElementById("frame_" + id);
     if (iframe) {
         iframe.parentNode.removeChild(iframe);
         try {
             delete window.frames["frame_" + id];
         } catch (e) {
         }
     }
     delete eAs[id];
 };
 EAL.prototype.start = function (id) {
     if (this.win != "loaded") {
         setTimeout("eAL.start('" + id + "');", 50);
         return;
     }
     for (var i in eAL.waiting_loading) {
         if (eAL.waiting_loading[i] != "loaded") {
             setTimeout("eAL.start('" + id + "');", 50);
             return;
         }
     }
     if (!eAL.lang[eAs[id]["settings"]["language"]] || (eAs[id]["settings"]["syntax"].length > 0 && !eAL.load_syntax[eAs[id]["settings"]["syntax"]])) {
         setTimeout("eAL.start('" + id + "');", 50);
         return;
     }
     if (eAs[id]["settings"]["syntax"].length > 0) eAL.init_syntax_regexp();
     if (!document.getElementById("EditAreaArroundInfos_" + id) && (eAs[id]["settings"]["debug"] || eAs[id]["settings"]["allow_toggle"])) {
         var span = document.createElement("span");
         span.id = "EditAreaArroundInfos_" + id;
         var html = "";
         if (eAs[id]["settings"]["allow_toggle"]) {
             checked = (eAs[id]["settings"]["display"] == "onload") ? "checked" : "";
             html += "<div id='edit_area_toggle_" + i + "'>";
             html += "<input id='edit_area_toggle_checkbox_" + id + "' class='toggle_" + id + "' type='checkbox' onclick='eAL.toggle(\"" + id + "\");' accesskey='e' " + checked + " />";
             html += "<label for='edit_area_toggle_checkbox_" + id + "'>{$toggle}</label></div>";
         }
         if (eAs[id]["settings"]["debug"]) html += "<textarea id='edit_area_debug_" + id + "' style='z-index: 20;width: 100%;height: 120px;overflow: auto;border: solid black 1px;'></textarea><br />";
         html = eAL.translate(html, eAs[id]["settings"]["language"]);
         span.innerHTML = html;
         var father = document.getElementById(id).parentNode;
         var next = document.getElementById(id).nextSibling;
         if (next == null) father.appendChild(span);
         else father.insertBefore(span, next);}if(!eAs[id]["initialized"]){this.execCommand(id, "EA_init");if(eAs[id]["settings"]["display"]=="later"){eAs[id]["initialized"]=true;return;}}if(this.nav['isIE']){eAL.init_ie_textarea(id);}var html_toolbar_content="";area=eAs[id];for(var i=0;i<area["settings"]["tab_toolbar"].length;i++){html_toolbar_content+=this.get_control_html(area["settings"]["tab_toolbar"][i], area["settings"]["language"]);}if(!this.iframe_script){this.iframe_script="";for(var i=0;i<this.sub_scripts_to_load.length;i++) this.iframe_script+='<script language="javascript" type="text/javascript" src="'+ this.baseURL + this.sub_scripts_to_load[i] +'.js"></script>';}for(var i=0;i<area["settings"]["plugins"].length;i++){if(!eAL.all_plugins_loaded) this.iframe_script+='<script language="javascript" type="text/javascript" src="'+ this.baseURL + 'plugins/' + area["settings"]["plugins"][i] + '/' + area["settings"]["plugins"][i] +'.js"></script>';this.iframe_script+='<script language="javascript" type="text/javascript" src="'+ this.baseURL + 'plugins/' + area["settings"]["plugins"][i] + '/langs/' + area["settings"]["language"] +'.js"></script>';}if(!this.iframe_css){this.iframe_css="<link href='"+ this.baseURL +"edit_area.css' rel='stylesheet' type='text/css' />";}var template=this.template.replace(/\[__BASEURL__\]/g, this.baseURL);template=template.replace("[__TOOLBAR__]",html_toolbar_content);template=this.translate(template, area["settings"]["language"], "template");template=template.replace("[__CSSRULES__]", this.iframe_css);template=template.replace("[__JSCODE__]", this.iframe_script);template=template.replace("[__EA_VERSION__]", this.version);area.textarea=document.getElementById(area["settings"]["id"]);eAs[area["settings"]["id"]]["textarea"]=area.textarea;var father=area.textarea.parentNode;var content=document.createElement("iframe");content.name="frame_"+area["settings"]["id"];content.id="frame_"+area["settings"]["id"];content.style.borderWidth="0px";setAttribute(content, "frameBorder", "0");content.style.overflow="hidden";content.style.display="none";var next=area.textarea.nextSibling;if(next==null) father.appendChild(content);
else father.insertBefore(content, next);var frame=window.frames["frame_"+area["settings"]["id"]];frame.document.open();frame.eAs=eAs;frame.area_id=area["settings"]["id"];frame.document.area_id=area["settings"]["id"];frame.document.write(template);frame.document.close();};EAL.prototype.toggle=function(id, toggle_to){if(!toggle_to) toggle_to=(eAs[id]["displayed"]==true)?"off":"on";if(eAs[id]["displayed"]==true  && toggle_to=="off"){this.toggle_off(id);}
else if(eAs[id]["displayed"]==false  && toggle_to=="on"){this.toggle_on(id);}return false;};EAL.prototype.toggle_off=function(id){if(window.frames["frame_"+id]){var frame=window.frames["frame_"+id];if(frame.editArea.fullscreen['isFull']) frame.editArea.toggle_full_screen(false);eAs[id]["displayed"]=false;eAs[id]["textarea"].wrap="off";setAttribute(eAs[id]["textarea"], "wrap", "off");var parNod=eAs[id]["textarea"].parentNode;var nxtSib=eAs[id]["textarea"].nextSibling;parNod.removeChild(eAs[id]["textarea"]);parNod.insertBefore(eAs[id]["textarea"], nxtSib);eAs[id]["textarea"].value=frame.editArea.textarea.value;var selStart=frame.editArea.last_selection["selectionStart"];var selEnd=frame.editArea.last_selection["selectionEnd"];var scrollTop=frame.document.getElementById("result").scrollTop;var scrollLeft=frame.document.getElementById("result").scrollLeft;document.getElementById("frame_"+id).style.display='none';eAs[id]["textarea"].style.display="inline";eAs[id]["textarea"].focus();if(this.nav['isIE']){eAs[id]["textarea"].selectionStart=selStart;eAs[id]["textarea"].selectionEnd=selEnd;eAs[id]["textarea"].focused=true;set_IE_selection(eAs[id]["textarea"]);}
 else {
     if (this.nav['isOpera']) {
         eAs[id]["textarea"].setSelectionRange(0, 0);
     }
     try {
         eAs[id]["textarea"].setSelectionRange(selStart, selEnd);
     } catch (e) {
     }
 }
     eAs[id]["textarea"].scrollTop = scrollTop;
     eAs[id]["textarea"].scrollLeft = scrollLeft;
     frame.editArea.execCommand("toggle_off");
 }
 };
 EAL.prototype.toggle_on = function (id) {
     if (window.frames["frame_" + id]) {
         var frame = window.frames["frame_" + id];
         area = window.frames["frame_" + id].editArea;
         area.textarea.value = eAs[id]["textarea"].value;
         var selStart = 0;
         var selEnd = 0;
         var scrollTop = 0;
         var scrollLeft = 0;
         if (eAs[id]["textarea"].use_last == true) {
             var selStart = eAs[id]["textarea"].last_selectionStart;
             var selEnd = eAs[id]["textarea"].last_selectionEnd;
             var scrollTop = eAs[id]["textarea"].last_scrollTop;
             var scrollLeft = eAs[id]["textarea"].last_scrollLeft;
             eAs[id]["textarea"].use_last = false;
         }
         else{try{var selStart=eAs[id]["textarea"].selectionStart;var selEnd=eAs[id]["textarea"].selectionEnd;var scrollTop=eAs[id]["textarea"].scrollTop;var scrollLeft=eAs[id]["textarea"].scrollLeft;}catch(ex){}}this.set_editarea_size_from_textarea(id, document.getElementById("frame_"+id));eAs[id]["textarea"].style.display="none";document.getElementById("frame_"+id).style.display="inline";area.execCommand("focus");eAs[id]["displayed"]=true;area.execCommand("update_size");window.frames["frame_"+id].document.getElementById("result").scrollTop=scrollTop;window.frames["frame_"+id].document.getElementById("result").scrollLeft=scrollLeft;area.area_select(selStart, selEnd-selStart);area.execCommand("toggle_on");}
else{var elem=document.getElementById(id);elem.last_selectionStart=elem.selectionStart;elem.last_selectionEnd=elem.selectionEnd;elem.last_scrollTop=elem.scrollTop;elem.last_scrollLeft=elem.scrollLeft;elem.use_last=true;eAL.start(id);}};EAL.prototype.set_editarea_size_from_textarea=function(id, frame){var elem=document.getElementById(id);var width=Math.max(eAs[id]["settings"]["min_width"], elem.offsetWidth)+"px";var height=Math.max(eAs[id]["settings"]["min_height"], elem.offsetHeight)+"px";if(elem.style.width.indexOf("%")!=-1) width=elem.style.width;if(elem.style.height.indexOf("%")!=-1) height=elem.style.height;frame.style.width=width;frame.style.height=height;};EAL.prototype.set_base_url=function(){if (!this.baseURL){var elements=document.getElementsByTagName('script');for (var i=0;i<elements.length;i++){if (elements[i].src && elements[i].src.match(/edit_area_[^\\\/]*$/i) ){var src=elements[i].src;src=src.substring(0, src.lastIndexOf('/'));this.baseURL=src;this.file_name=elements[i].src.substr(elements[i].src.lastIndexOf("/")+1);break;}}}var documentBasePath=document.location.href;if (documentBasePath.indexOf('?') !=-1) documentBasePath=documentBasePath.substring(0, documentBasePath.indexOf('?'));var documentURL=documentBasePath;documentBasePath=documentBasePath.substring(0, documentBasePath.lastIndexOf('/'));if (this.baseURL.indexOf('://')==-1 && this.baseURL.charAt(0) !='/'){this.baseURL=documentBasePath + "/" + this.baseURL;}this.baseURL+="/";};EAL.prototype.get_button_html=function(id, img, exec, baseURL){if(!baseURL) baseURL=this.baseURL;var cmd='editArea.execCommand(\'' + exec + '\')';html='<a href="javascript:' + cmd + '" onclick="' + cmd + ';return false;" onmousedown="return false;" target="_self">';html+='<img id="' + id + '" src="'+ baseURL +'images/' + img + '" title="{$' + id + '}" width="20" height="20" class="editAreaButtonNormal" onmouseover="editArea.switchClass(this,\'editAreaButtonOver\');" onmouseout="editArea.restoreClass(this);" onmousedown="editArea.restoreAndSwitchClass(this,\'editAreaButtonDown\');" /></a>';return html;};EAL.prototype.get_control_html=function(button_name, lang){for (var i=0;i<this.advanced_buttons.length;i++){var but=this.advanced_buttons[i];if (but[0]==button_name){return this.get_button_html(but[0], but[1], but[2]);}}switch (button_name){case "*": case "return": return "<br />";case "|": case "separator": return '<img src="'+ this.baseURL +'images/spacer.gif" width="1" height="15" class="editAreaSeparatorLine">';case "select_font": html="<select id='area_font_size' onchange='editArea.execCommand(\"change_font_size\")'>" +"			<option value='-1'>{$font_size}</option>" +"			<option value='8'>8 pt</option>" +"			<option value='9'>9 pt</option>" +"			<option value='10'>10 pt</option>" +"			<option value='11'>11 pt</option>" +"			<option value='12'>12 pt</option>" +"			<option value='14'>14 pt</option>" +"		</select>";return html;}return "<span id='tmp_tool_"+button_name+"'>["+button_name+"]</span>";};EAL.prototype.get_template=function(){if(this.template==""){var xhr_object=null;if(window.XMLHttpRequest) xhr_object=new XMLHttpRequest();
else if(window.ActiveXObject) xhr_object=new ActiveXObject("Microsoft.XMLHTTP");
else{alert("XMLHTTPRequest not supported. EditArea not loaded");return;}xhr_object.open("GET", this.baseURL+"template.html", false);xhr_object.send(null);if(xhr_object.readyState==4) this.template=xhr_object.responseText;
else this.has_error();}};EAL.prototype.translate=function(text, lang, mode){if(mode=="word") text=eAL.get_word_translation(text, lang);
else if(mode="template"){eAL.current_language=lang;text=text.replace(/\{\$([^\}]+)\}/gm, eAL.translate_template);}return text;};EAL.prototype.translate_template=function(){return eAL.get_word_translation(EAL.prototype.translate_template.arguments[1], eAL.current_language);};EAL.prototype.get_word_translation=function(val, lang){for(var i in eAL.lang[lang]){if(i==val) return eAL.lang[lang][i];}return "_"+val;};EAL.prototype.load_script=function(url){if (this.loadedFiles[url]) return;try{var script=document.createElement("script");script.type="text/javascript";script.src=url;var head=document.getElementsByTagName("head");head[0].appendChild(script);}catch(e){document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></sc'+'ript>');}this.loadedFiles[url]=true;};EAL.prototype.add_event=function(obj, name, handler){if (obj.attachEvent){obj.attachEvent("on" + name, handler);}
else{obj.addEventListener(name, handler, false);}};EAL.prototype.remove_event=function(obj, name, handler){if (obj.detachEvent) obj.detachEvent("on" + name, handler);
else obj.removeEventListener(name, handler, false);};EAL.prototype.reset=function(e){var formObj=eAL.nav['isIE'] ? window.event.srcElement : e.target;for(var i in eAs){var is_child=false;for (var x=0;x<formObj.elements.length;x++){if(formObj.elements[x].id==i) is_child=true;}if(window.frames["frame_"+i] && is_child && eAs[i]["displayed"]==true){var exec='window.frames["frame_'+ i +'"].editArea.textarea.value=document.getElementById("'+ i +'").value;';exec+='window.frames["frame_'+ i +'"].editArea.execCommand("focus");';exec+='window.frames["frame_'+ i +'"].editArea.check_line_selection();';exec+='window.frames["frame_'+ i +'"].editArea.execCommand("reset");';window.setTimeout(exec, 10);}}};EAL.prototype.submit=function(e){var formObj=eAL.nav['isIE'] ? window.event.srcElement : e.target;for(var i in eAs){var is_child=false;for (var x=0;x<formObj.elements.length;x++){if(formObj.elements[x].id==i) is_child=true;}if(is_child){if(window.frames["frame_"+i] && eAs[i]["displayed"]==true) document.getElementById(i).value=window.frames["frame_"+ i].editArea.textarea.value;eAL.execCommand(i,"EA_submit");}}if(typeof(formObj.edit_area_replaced_submit)=="function"){res=formObj.edit_area_replaced_submit();if(res==false){if(eAL.nav['isIE']) return false;
else e.preventDefault();}}};EAL.prototype.getValue=function(id){if(window.frames["frame_"+id] && eAs[id]["displayed"]==true){return window.frames["frame_"+ id].editArea.textarea.value;}
else if(elem=document.getElementById(id)){return elem.value;}return false;};EAL.prototype.setValue=function(id, new_val){if(window.frames["frame_"+id] && eAs[id]["displayed"]==true){window.frames["frame_"+ id].editArea.textarea.value=new_val;window.frames["frame_"+ id].editArea.execCommand("focus");window.frames["frame_"+ id].editArea.check_line_selection(false);window.frames["frame_"+ id].editArea.execCommand("onchange");}
else if(elem=document.getElementById(id)){elem.value=new_val;}};EAL.prototype.getSelectionRange=function(id){var sel={"start": 0, "end": 0};if(window.frames["frame_"+id] && eAs[id]["displayed"]==true){var editArea=window.frames["frame_"+ id].editArea;sel["start"]=editArea.textarea.selectionStart;sel["end"]=editArea.textarea.selectionEnd;}
else if(elem=document.getElementById(id)){sel=getSelectionRange(elem);}return sel;};EAL.prototype.setSelectionRange=function(id, new_start, new_end){if(window.frames["frame_"+id] && eAs[id]["displayed"]==true){window.frames["frame_"+ id].editArea.area_select(new_start, new_end-new_start);if(!this.nav['isIE']){window.frames["frame_"+ id].editArea.check_line_selection(false);window.frames["frame_"+ id].editArea.scroll_to_view();}}
else if(elem=document.getElementById(id)){setSelectionRange(elem, new_start, new_end);}};EAL.prototype.getSelectedText=function(id){var sel=this.getSelectionRange(id);return this.getValue(id).substring(sel["start"], sel["end"]);};EAL.prototype.setSelectedText=function(id, new_val){new_val=new_val.replace(/\r/g, "");var sel=this.getSelectionRange(id);var text=this.getValue(id);if(window.frames["frame_"+id] && eAs[id]["displayed"]==true){var scrollTop=window.frames["frame_"+ id].document.getElementById("result").scrollTop;var scrollLeft=window.frames["frame_"+ id].document.getElementById("result").scrollLeft;}
else{var scrollTop=document.getElementById(id).scrollTop;var scrollLeft=document.getElementById(id).scrollLeft;}text=text.substring(0, sel["start"])+ new_val +text.substring(sel["end"]);this.setValue(id, text);var new_sel_end=sel["start"]+ new_val.length;this.setSelectionRange(id, sel["start"], new_sel_end);if(new_val !=this.getSelectedText(id).replace(/\r/g, "")){this.setSelectionRange(id, sel["start"], new_sel_end+ new_val.split("\n").length -1);}if(window.frames["frame_"+id] && eAs[id]["displayed"]==true){window.frames["frame_"+ id].document.getElementById("result").scrollTop=scrollTop;window.frames["frame_"+ id].document.getElementById("result").scrollLeft=scrollLeft;window.frames["frame_"+ id].editArea.execCommand("onchange");}
else{document.getElementById(id).scrollTop=scrollTop;document.getElementById(id).scrollLeft=scrollLeft;}};EAL.prototype.insertTags=function(id, open_tag, close_tag){var old_sel=this.getSelectionRange(id);text=open_tag + this.getSelectedText(id) + close_tag;eAL.setSelectedText(id, text);var new_sel=this.getSelectionRange(id);if(old_sel["end"] > old_sel["start"]) this.setSelectionRange(id, new_sel["end"], new_sel["end"]);
else this.setSelectionRange(id, old_sel["start"]+open_tag.length, old_sel["start"]+open_tag.length);};EAL.prototype.hide=function(id){if(document.getElementById(id) && !this.hidden[id]){this.hidden[id]={};this.hidden[id]["selectionRange"]=this.getSelectionRange(id);if(document.getElementById(id).style.display!="none"){this.hidden[id]["scrollTop"]=document.getElementById(id).scrollTop;this.hidden[id]["scrollLeft"]=document.getElementById(id).scrollLeft;}if(window.frames["frame_"+id]){this.hidden[id]["toggle"]=eAs[id]["displayed"];if(window.frames["frame_"+id] && eAs[id]["displayed"]==true){var scrollTop=window.frames["frame_"+ id].document.getElementById("result").scrollTop;var scrollLeft=window.frames["frame_"+ id].document.getElementById("result").scrollLeft;}
else{var scrollTop=document.getElementById(id).scrollTop;var scrollLeft=document.getElementById(id).scrollLeft;}this.hidden[id]["scrollTop"]=scrollTop;this.hidden[id]["scrollLeft"]=scrollLeft;if(eAs[id]["displayed"]==true) eAL.toggle_off(id);}var span=document.getElementById("EditAreaArroundInfos_"+id);if(span){span.style.display='none';}document.getElementById(id).style.display="none";}};EAL.prototype.show=function(id){if((elem=document.getElementById(id)) && this.hidden[id]){elem.style.display="inline";elem.scrollTop=this.hidden[id]["scrollTop"];elem.scrollLeft=this.hidden[id]["scrollLeft"];var span=document.getElementById("EditAreaArroundInfos_"+id);if(span){span.style.display='inline';}if(window.frames["frame_"+id]){elem.style.display="inline";if(this.hidden[id]["toggle"]==true) eAL.toggle_on(id);scrollTop=this.hidden[id]["scrollTop"];scrollLeft=this.hidden[id]["scrollLeft"];if(window.frames["frame_"+id] && eAs[id]["displayed"]==true){window.frames["frame_"+ id].document.getElementById("result").scrollTop=scrollTop;window.frames["frame_"+ id].document.getElementById("result").scrollLeft=scrollLeft;}
 else {
     elem.scrollTop = scrollTop;
     elem.scrollLeft = scrollLeft;
 }
 }
     sel = this.hidden[id]["selectionRange"];
     this.setSelectionRange(id, sel["start"], sel["end"]);
     delete this.hidden[id];
 }
 };
 EAL.prototype.execCommand = function (id, cmd) {
     switch (cmd) {
         case "EA_init":
             if (eAs[id]['settings']["EA_init_callback"].length > 0) eval(eAs[id]['settings']["EA_init_callback"] + "('" + id + "');");
             break;
         case "EA_delete":
             if (eAs[id]['settings']["EA_delete_callback"].length > 0) eval(eAs[id]['settings']["EA_delete_callback"] + "('" + id + "');");
             break;
         case "EA_submit":
             if (eAs[id]['settings']["submit_callback"].length > 0) eval(eAs[id]['settings']["submit_callback"] + "('" + id + "');");
             break;
     }
     if (window.frames["frame_" + id]) {
         return eval('window.frames["frame_' + id + '"].editArea.' + cmd + ';');
     }
     return false;
 };
 var eAL = new EAL();
 var eAs = {};
 function getAttribute(elm, aname) {
     try {
         var avalue = elm.getAttribute(aname);
     } catch (exept) {
     }
     if (!avalue) {
         for (var i = 0; i < elm.attributes.length; i++) {
             var taName = elm.attributes [i].name.toLowerCase();
             if (taName == aname) {
                 avalue = elm.attributes [i].value;
                 return avalue;
             }
         }
     }
     return avalue;
 }
 function setAttribute(elm, attr, val) {
     if (attr == "class") {
         elm.setAttribute("className", val);
         elm.setAttribute("class", val);
     }
     else {
     elm.setAttribute(attr, val);
 }
 }
 function getChildren(elem, elem_type, elem_attribute, elem_attribute_match, option, depth) {
     if (!option) var option = "single";
     if (!depth) var depth = -1;
     if (elem) {
         var children = elem.childNodes;
         var result = null;
         var results = [];
         for (var x = 0; x < children.length; x++) {
             strTagName = String(children[x].tagName);
             children_class = "?";
             if (strTagName != "undefined") {
                 child_attribute = getAttribute(children[x], elem_attribute);
                 if ((strTagName.toLowerCase() == elem_type.toLowerCase() || elem_type == "") && (elem_attribute == "" || child_attribute == elem_attribute_match)) {
                     if (option == "all") {
                         results.push(children[x]);
                     }
                     else{return children[x];}}if(depth!=0){result=getChildren(children[x], elem_type, elem_attribute, elem_attribute_match, option, depth-1);if(option=="all"){if(result.length>0){results=results.concat(result);}}
 else if (result != null) {
     return result;
 }
 }
 }
 }
     if (option == "all") return results;
 }
     return null;
 }
 function isChildOf(elem, parent) {
     if (elem) {
         if (elem == parent) return true;
         while (elem.parentNode != 'undefined') {
             return isChildOf(elem.parentNode, parent);
         }
     }
     return false;
 }
 function getMouseX(e) {
     if (e != null && typeof(e.pageX) != "undefined") {
         return e.pageX;
     }
     else {
     return (e != null ? e.x : event.x) + document.documentElement.scrollLeft;
 }
 }
 function getMouseY(e) {
     if (e != null && typeof(e.pageY) != "undefined") {
         return e.pageY;
     }
     else {
     return (e != null ? e.y : event.y) + document.documentElement.scrollTop;
 }
 }
 function calculeOffsetLeft(r) {
     return calculeOffset(r, "offsetLeft")
 }
 function calculeOffsetTop(r) {
     return calculeOffset(r, "offsetTop")
 }
 function calculeOffset(element, attr) {
     var offset = 0;
     while (element) {
         offset += element[attr];
         element = element.offsetParent
     }
     return offset;
 }
 function get_css_property(elem, prop) {
     if (document.defaultView) {
         return document.defaultView.getComputedStyle(elem, null).getPropertyValue(prop);
     }
     else if(elem.currentStyle){var prop=prop.replace(/-\D/gi, function(sMatch){return sMatch.charAt(sMatch.length - 1).toUpperCase();});return elem.currentStyle[prop];}
 else return null;
 }
 var move_current_element;
 function start_move_element(e, id, frame) {
     var elem_id = (e.target || e.srcElement).id;
     if (id) elem_id = id;
     if (!frame) frame = window;
     if (frame.event) e = frame.event;
     move_current_element = frame.document.getElementById(elem_id);
     move_current_element.frame = frame;
     frame.document.onmousemove = move_element;
     frame.document.onmouseup = end_move_element;
     mouse_x = getMouseX(e);
     mouse_y = getMouseY(e);
     move_current_element.start_pos_x = mouse_x - (move_current_element.style.left.replace("px", "") || calculeOffsetLeft(move_current_element));
     move_current_element.start_pos_y = mouse_y - (move_current_element.style.top.replace("px", "") || calculeOffsetTop(move_current_element));
     return false;
 }
 function end_move_element(e) {
     move_current_element.frame.document.onmousemove = "";
     move_current_element.frame.document.onmouseup = "";
     move_current_element = null;
 }
 function move_element(e) {
     if (move_current_element.frame && move_current_element.frame.event) e = move_current_element.frame.event;
     var mouse_x = getMouseX(e);
     var mouse_y = getMouseY(e);
     var new_top = mouse_y - move_current_element.start_pos_y;
     var new_left = mouse_x - move_current_element.start_pos_x;
     var max_left = move_current_element.frame.document.body.offsetWidth - move_current_element.offsetWidth;
     max_top = move_current_element.frame.document.body.offsetHeight - move_current_element.offsetHeight;
     new_top = Math.min(Math.max(0, new_top), max_top);
     new_left = Math.min(Math.max(0, new_left), max_left);
     move_current_element.style.top = new_top + "px";
     move_current_element.style.left = new_left + "px";
     return false;
 }
 var nav = eAL.nav;
 function getSelectionRange(textarea) {
     return {"start": textarea.selectionStart, "end": textarea.selectionEnd};
 }
 function setSelectionRange(textarea, start, end) {
     textarea.focus();
     start = Math.max(0, Math.min(textarea.value.length, start));
     end = Math.max(start, Math.min(textarea.value.length, end));
     if (nav['isOpera']) {
         textarea.selectionEnd = 1;
         textarea.selectionStart = 0;
         textarea.selectionEnd = 1;
         textarea.selectionStart = 0;
     }
     textarea.selectionStart = start;
     textarea.selectionEnd = end;
     if (nav['isIE']) set_IE_selection(textarea);
 }
 function get_IE_selection(textarea) {
     if (textarea && textarea.focused) {
         if (!textarea.ea_line_height) {
             var div = document.createElement("div");
             div.style.fontFamily = get_css_property(textarea, "font-family");
             div.style.fontSize = get_css_property(textarea, "font-size");
             div.style.visibility = "hidden";
             div.innerHTML = "0";
             document.body.appendChild(div);
             textarea.ea_line_height = div.offsetHeight;
             document.body.removeChild(div);
         }
         var range = document.selection.createRange();
         var stored_range = range.duplicate();
         stored_range.moveToElementText(textarea);
         stored_range.setEndPoint('EndToEnd', range);
         if (stored_range.parentElement() == textarea) {
             var elem = textarea;
             var scrollTop = 0;
             while (elem.parentNode) {
                 scrollTop += elem.scrollTop;
                 elem = elem.parentNode;
             }
             var relative_top = range.offsetTop - calculeOffsetTop(textarea) + scrollTop;
             var line_start = Math.round((relative_top / textarea.ea_line_height) + 1);
             var line_nb = Math.round(range.boundingHeight / textarea.ea_line_height);
             var range_start = stored_range.text.length - range.text.length;
             var tab = textarea.value.substr(0, range_start).split("\n");
             range_start += (line_start - tab.length) * 2;
             textarea.selectionStart = range_start;
             var range_end = textarea.selectionStart + range.text.length;
             tab = textarea.value.substr(0, range_start + range.text.length).split("\n");
             range_end += (line_start + line_nb - 1 - tab.length) * 2;
             textarea.selectionEnd = range_end;
         }
     }
     setTimeout("get_IE_selection(document.getElementById('" + textarea.id + "'));", 50);
 }
 function IE_textarea_focus() {
     event.srcElement.focused = true;
 }
 function IE_textarea_blur() {
     event.srcElement.focused = false;
 }
 function set_IE_selection(textarea) {
     var nbLineStart = textarea.value.substr(0, textarea.selectionStart).split("\n").length - 1;
     var nbLineEnd = textarea.value.substr(0, textarea.selectionEnd).split("\n").length - 1;
     var range = document.selection.createRange();
     range.moveToElementText(textarea);
     range.setEndPoint('EndToStart', range);
     range.moveStart('character', textarea.selectionStart - nbLineStart);
     range.moveEnd('character', textarea.selectionEnd - nbLineEnd - (textarea.selectionStart - nbLineStart));
     range.select();
 }
 eAL.waiting_loading["elements_functions.js"] = "loaded";
 EAL.prototype.start_resize_area = function () {
     document.onmouseup = eAL.end_resize_area;
     document.onmousemove = eAL.resize_area;
     eAL.toggle(eAL.resize["id"]);
     var textarea = eAs[eAL.resize["id"]]["textarea"];
     var div = document.getElementById("edit_area_resize");
     if (!div) {
         div = document.createElement("div");
         div.id = "edit_area_resize";
         div.style.border = "dashed #888888 1px";
     }
     var width = textarea.offsetWidth - 2;
     var height = textarea.offsetHeight - 2;
     div.style.display = "block";
     div.style.width = width + "px";
     div.style.height = height + "px";
     var father = textarea.parentNode;
     father.insertBefore(div, textarea);
     textarea.style.display = "none";
     eAL.resize["start_top"] = calculeOffsetTop(div);
     eAL.resize["start_left"] = calculeOffsetLeft(div);
 };
 EAL.prototype.end_resize_area = function (e) {
     document.onmouseup = "";
     document.onmousemove = "";
     var div = document.getElementById("edit_area_resize");
     var textarea = eAs[eAL.resize["id"]]["textarea"];
     var width = Math.max(eAs[eAL.resize["id"]]["settings"]["min_width"], div.offsetWidth - 4);
     var height = Math.max(eAs[eAL.resize["id"]]["settings"]["min_height"], div.offsetHeight - 4);
     if (eAL.nav['isIE'] == 6) {
         width -= 2;
         height -= 2;
     }
     textarea.style.width = width + "px";
     textarea.style.height = height + "px";
     div.style.display = "none";
     textarea.style.display = "inline";
     textarea.selectionStart = eAL.resize["selectionStart"];
     textarea.selectionEnd = eAL.resize["selectionEnd"];
     eAL.toggle(eAL.resize["id"]);
     return false;
 };
 EAL.prototype.resize_area = function (e) {
     var allow = eAs[eAL.resize["id"]]["settings"]["allow_resize"];
     if (allow == "both" || allow == "y") {
         new_y = getMouseY(e);
         var new_height = Math.max(20, new_y - eAL.resize["start_top"]);
         document.getElementById("edit_area_resize").style.height = new_height + "px";
     }
     if (allow == "both" || allow == "x") {
         new_x = getMouseX(e);
         var new_width = Math.max(20, new_x - eAL.resize["start_left"]);
         document.getElementById("edit_area_resize").style.width = new_width + "px";
     }
     return false;
 };
 eAL.waiting_loading["resize_area.js"] = "loaded";
 EAL.prototype.get_regexp = function (text_array) {
     res = "(\\b)(";
     for (i = 0; i < text_array.length; i++) {
         if (i > 0) res += "|";
         res += this.get_escaped_regexp(text_array[i]);
     }
     res += ")(\\b)";
     reg = new RegExp(res);
     return res;
 };
 EAL.prototype.get_escaped_regexp = function (str) {
     return str.replace(/(\.|\?|\*|\+|\\|\(|\)|\[|\]|\}|\{|\$|\^|\|)/g, "\\$1");
 };
 EAL.prototype.init_syntax_regexp = function () {
     var lang_style = {};
     for (var lang in this.load_syntax) {
         if (!this.syntax[lang]) {
             this.syntax[lang] = {};
             this.syntax[lang]["keywords_reg_exp"] = {};
             this.keywords_reg_exp_nb = 0;
             if (this.load_syntax[lang]['KEYWORDS']) {
                 param = "g";
                 if (this.load_syntax[lang]['KEYWORD_CASE_SENSITIVE'] === false) param += "i";
                 for (var i in this.load_syntax[lang]['KEYWORDS']) {
                     if (typeof(this.load_syntax[lang]['KEYWORDS'][i]) == "function") continue;
                     this.syntax[lang]["keywords_reg_exp"][i] = new RegExp(this.get_regexp(this.load_syntax[lang]['KEYWORDS'][i]), param);
                     this.keywords_reg_exp_nb++;
                 }
             }
             if (this.load_syntax[lang]['OPERATORS']) {
                 var str = "";
                 var nb = 0;
                 for (var i in this.load_syntax[lang]['OPERATORS']) {
                     if (typeof(this.load_syntax[lang]['OPERATORS'][i]) == "function") continue;
                     if (nb > 0) str += "|";
                     str += this.get_escaped_regexp(this.load_syntax[lang]['OPERATORS'][i]);
                     nb++;
                 }
                 if (str.length > 0) this.syntax[lang]["operators_reg_exp"] = new RegExp("(" + str + ")", "g");
             }
             if (this.load_syntax[lang]['DELIMITERS']) {
                 var str = "";
                 var nb = 0;
                 for (var i in this.load_syntax[lang]['DELIMITERS']) {
                     if (typeof(this.load_syntax[lang]['DELIMITERS'][i]) == "function") continue;
                     if (nb > 0) str += "|";
                     str += this.get_escaped_regexp(this.load_syntax[lang]['DELIMITERS'][i]);
                     nb++;
                 }
                 if (str.length > 0) this.syntax[lang]["delimiters_reg_exp"] = new RegExp("(" + str + ")", "g");
             }
             var syntax_trace = [];
             this.syntax[lang]["quotes"] = {};
             var quote_tab = [];
             if (this.load_syntax[lang]['QUOTEMARKS']) {
                 for (var i in this.load_syntax[lang]['QUOTEMARKS']) {
                     if (typeof(this.load_syntax[lang]['QUOTEMARKS'][i]) == "function") continue;
                     var x = this.get_escaped_regexp(this.load_syntax[lang]['QUOTEMARKS'][i]);
                     this.syntax[lang]["quotes"][x] = x;
                     quote_tab[quote_tab.length] = "(" + x + "(?:[^" + x + "\\\\]*(\\\\\\\\)*(\\\\" + x + "?)?)*(" + x + "|$))";
                     syntax_trace.push(x);
                 }
             }
             this.syntax[lang]["comments"] = {};
             if (this.load_syntax[lang]['COMMENT_SINGLE']) {
                 for (var i in this.load_syntax[lang]['COMMENT_SINGLE']) {
                     if (typeof(this.load_syntax[lang]['COMMENT_SINGLE'][i]) == "function") continue;
                     var x = this.get_escaped_regexp(this.load_syntax[lang]['COMMENT_SINGLE'][i]);
                     quote_tab[quote_tab.length] = "(" + x + "(.|\\r|\\t)*(\\n|$))";
                     syntax_trace.push(x);
                     this.syntax[lang]["comments"][x] = "\n";
                 }
             }
             if (this.load_syntax[lang]['COMMENT_MULTI']) {
                 for (var i in this.load_syntax[lang]['COMMENT_MULTI']) {
                     if (typeof(this.load_syntax[lang]['COMMENT_MULTI'][i]) == "function") continue;
                     var start = this.get_escaped_regexp(i);
                     var end = this.get_escaped_regexp(this.load_syntax[lang]['COMMENT_MULTI'][i]);
                     quote_tab[quote_tab.length] = "(" + start + "(.|\\n|\\r)*?(" + end + "|$))";
                     syntax_trace.push(start);
                     syntax_trace.push(end);
                     this.syntax[lang]["comments"][i] = this.load_syntax[lang]['COMMENT_MULTI'][i];
                 }
             }
             if (quote_tab.length > 0) this.syntax[lang]["comment_or_quote_reg_exp"] = new RegExp("(" + quote_tab.join("|") + ")", "gi");
             if (syntax_trace.length > 0) this.syntax[lang]["syntax_trace_regexp"] = new RegExp("((.|\n)*?)(\\\\*(" + syntax_trace.join("|") + "|$))", "gmi");
             if (this.load_syntax[lang]['SCRIPT_DELIMITERS']) {
                 this.syntax[lang]["script_delimiters"] = {};
                 for (var i in this.load_syntax[lang]['SCRIPT_DELIMITERS']) {
                     if (typeof(this.load_syntax[lang]['SCRIPT_DELIMITERS'][i]) == "function") continue;
                     this.syntax[lang]["script_delimiters"][i] = this.load_syntax[lang]['SCRIPT_DELIMITERS'];
                 }
             }
             this.syntax[lang]["custom_regexp"] = {};
             if (this.load_syntax[lang]['REGEXPS']) {
                 for (var i in this.load_syntax[lang]['REGEXPS']) {
                     if (typeof(this.load_syntax[lang]['REGEXPS'][i]) == "function") continue;
                     var val = this.load_syntax[lang]['REGEXPS'][i];
                     if (!this.syntax[lang]["custom_regexp"][val['execute']]) this.syntax[lang]["custom_regexp"][val['execute']] = {};
                     this.syntax[lang]["custom_regexp"][val['execute']][i] = {
                         'regexp': new RegExp(val['search'], val['modifiers']),
                         'class': val['class']
                     };
                 }
             }
             if (this.load_syntax[lang]['STYLES']) {
                 lang_style[lang] = {};
                 for (var i in this.load_syntax[lang]['STYLES']) {
                     if (typeof(this.load_syntax[lang]['STYLES'][i]) == "function") continue;
                     if (typeof(this.load_syntax[lang]['STYLES'][i]) != "string") {
                         for (var j in this.load_syntax[lang]['STYLES'][i]) {
                             lang_style[lang][j] = this.load_syntax[lang]['STYLES'][i][j];
                         }
                     }
                     else{lang_style[lang][i]=this.load_syntax[lang]['STYLES'][i];}}}var style="";for(var i in lang_style[lang]){if(lang_style[lang][i].length>0){style+="."+ lang +" ."+ i.toLowerCase() +" span{"+lang_style[lang][i]+"}\n";style+="."+ lang +" ."+ i.toLowerCase() +"{"+lang_style[lang][i]+"}\n";}}this.syntax[lang]["styles"]=style;}}};eAL.waiting_loading["reg_syntax.js"]="loaded";
var editAreaLoader= eAL;var editAreas=eAs;EditAreaLoader=EAL;editAreaLoader.iframe_script= "<script language='Javascript' type='text/javascript'> function EA(){this.error=false;this.inlinePopup=new Array({popup_id: \"area_search_replace\", icon_id: \"search\"}, {popup_id: \"edit_area_help\", icon_id: \"help\"});this.plugins=new Object();this.line_number=0;this.nav=parent.eAL.nav;this.last_selection=new Object();this.last_text_to_highlight=\"\";this.last_hightlighted_text=\"\";this.textareaFocused=false;this.previous=new Array();this.next=new Array();this.last_undo=\"\";this.assocBracket=new Object();this.revertAssocBracket=new Object();this.assocBracket[\"(\"]=\")\";this.assocBracket[\"{\"]=\"}\";this.assocBracket[\"[\"]=\"]\";for(var index in this.assocBracket){this.revertAssocBracket[this.assocBracket[index]]=index;}this.lineHeight=16;this.tab_nb_char=8;if(this.nav['isOpera']) this.tab_nb_char=6;this.is_tabbing=false;this.fullscreen={'isFull': false};this.isResizing=false;this.id=area_id;this.settings=eAs[this.id][\"settings\"];if((\"\"+this.settings['replace_tab_by_spaces']).match(/^[0-9]+$/)){this.tab_nb_char=this.settings['replace_tab_by_spaces'];this.tabulation=\"\";for(var i=0;i<this.tab_nb_char;i++) this.tabulation+=\" \";}\nelse this.tabulation=\"\t\";};EA.prototype.update_size=function(){if(eAs[eA.id] && eAs[eA.id][\"displayed\"]==true){if(eA.fullscreen['isFull']){parent.document.getElementById(\"frame_\"+eA.id).style.width=parent.document.getElementsByTagName(\"html\")[0].clientWidth + \"px\";parent.document.getElementById(\"frame_\"+eA.id).style.height=parent.document.getElementsByTagName(\"html\")[0].clientHeight + \"px\";}var height=document.body.offsetHeight - eA.get_all_toolbar_height() - 4;eA.result.style.height=height +\"px\";var width=document.body.offsetWidth -2;eA.result.style.width=width+\"px\";for(var i=0;i<eA.inlinePopup.length;i++){var popup=document.getElementById(eA.inlinePopup[i][\"popup_id\"]);var max_left=document.body.offsetWidth- popup.offsetWidth;var max_top=document.body.offsetHeight- popup.offsetHeight;if(popup.offsetTop>max_top) popup.style.top=max_top+\"px\";if(popup.offsetLeft>max_left) popup.style.left=max_left+\"px\";}}};EA.prototype.init=function(){this.textarea=document.getElementById(\"textarea\");this.container=document.getElementById(\"container\");this.result=document.getElementById(\"result\");this.content_highlight=document.getElementById(\"content_highlight\");this.selection_field=document.getElementById(\"selection_field\");spans=parent.getChildren(document.getElementById(\"toolbar_1\"), \"span\", \"\", \"\", \"all\", -1);for(var i=0;i<spans.length;i++){id=spans[i].id.replace(/tmp_tool_(.*)/, \"$1\");if(id!=spans[i].id){for(var j in this.plugins){if(typeof(this.plugins[j].get_control_html)==\"function\" ){html=this.plugins[j].get_control_html(id);if(html!=false){html=parent.eAL.translate(html, this.settings[\"language\"], \"template\");var new_span=document.createElement(\"span\");new_span.innerHTML=html;var father=spans[i].parentNode;spans[i].parentNode.replaceChild(new_span, spans[i]);break;}}}}}this.textarea.value=eAs[this.id][\"textarea\"].value;if(this.settings[\"debug\"]) this.debug=parent.document.getElementById(\"edit_area_debug_\"+this.id);if(document.getElementById(\"redo\") !=null) this.switchClassSticky(document.getElementById(\"redo\"), 'editAreaButtonDisabled', true);if(typeof(parent.eAL.syntax[this.settings[\"syntax\"]])!=\"undefined\"){for(var i in parent.eAL.syntax){this.add_style(parent.eAL.syntax[i][\"styles\"]);}}if(this.nav['isOpera']) document.getElementById(\"editor\").onkeypress=keyDown;\nelse document.getElementById(\"editor\").onkeydown=keyDown;for(var i=0;i<this.inlinePopup.length;i++){if(this.nav['isIE'] || this.nav['isFirefox']) document.getElementById(this.inlinePopup[i][\"popup_id\"]).onkeydown=keyDown;\nelse document.getElementById(this.inlinePopup[i][\"popup_id\"]).onkeypress=keyDown;}if(this.settings[\"allow_resize\"]==\"both\" || this.settings[\"allow_resize\"]==\"x\" || this.settings[\"allow_resize\"]==\"y\") this.allow_resize(true);parent.eAL.toggle(this.id, \"on\");this.change_smooth_selection_mode(eA.smooth_selection);this.execCommand(\"change_highlight\", this.settings[\"start_highlight\"]);this.set_font(eA.settings[\"font_family\"], eA.settings[\"font_size\"]);children=parent.getChildren(document.body, \"\", \"selec\", \"none\", \"all\", -1);for(var i=0;i<children.length;i++){if(this.nav['isIE']) children[i].unselectable=true;\nelse children[i].onmousedown=function(){return false};}if(this.nav['isGecko']){this.textarea.spellcheck=this.settings[\"gecko_spellcheck\"];}if(this.nav['isOpera']){document.getElementById(\"editor\").style.position=\"absolute\";document.getElementById(\"selection_field\").style.marginTop=\"-1pt\";document.getElementById(\"selection_field\").style.paddingTop=\"1pt\";document.getElementById(\"cursor_pos\").style.marginTop=\"-1pt\";document.getElementById(\"end_bracket\").style.marginTop=\"-1pt\";document.getElementById(\"content_highlight\").style.marginTop=\"-1pt\";}parent.eAL.add_event(this.result, \"click\", function(e){if((e.target || e.srcElement)==eA.result){eA.area_select(eA.textarea.value.length, 0);}});setTimeout(\"eA.manage_size();eA.execCommand('EA_load');\", 10);this.check_undo();this.check_line_selection(true);this.scroll_to_view();for(var i in this.plugins){if(typeof(this.plugins[i].onload)==\"function\") this.plugins[i].onload();}if(this.settings['fullscreen']==true) this.toggle_full_screen(true);parent.eAL.add_event(window, \"resize\", eA.update_size);parent.eAL.add_event(parent.window, \"resize\", eA.update_size);parent.eAL.add_event(top.window, \"resize\", eA.update_size);parent.eAL.add_event(window, \"unload\", function(){if(eAs[eA.id] && eAs[eA.id][\"displayed\"]) eA.execCommand(\"EA_unload\");});};EA.prototype.manage_size=function(){if(!eAs[this.id]) return false;if(eAs[this.id][\"displayed\"]==true){var resized=false;area_width=this.textarea.scrollWidth;area_height=this.textarea.scrollHeight;if(this.nav['isOpera']){area_height=this.last_selection['nb_line']*this.lineHeight;area_width=10000;}if(this.nav['isIE']==7) area_width-=45;if(this.nav['isGecko'] && this.smooth_selection && this.last_selection[\"nb_line\"]) area_height=this.last_selection[\"nb_line\"]*this.lineHeight;if(this.last_selection[\"nb_line\"] >=this.line_number){var div_line_number=\"\";for(i=this.line_number+1;i<this.last_selection[\"nb_line\"]+100;i++){div_line_number+=i+\"<br />\";this.line_number++;}var span=document.createElement(\"span\");if(this.nav['isIE']) span.unselectable=true;span.innerHTML=div_line_number;document.getElementById(\"line_number\").appendChild(span);}if(this.textarea.previous_scrollWidth!=area_width){if(this.nav['isOpera']){}\nelse{if(this.textarea.style.width && (this.textarea.style.width.replace(\"px\",\"\") < area_width)){area_width+=50;}}if(this.nav['isGecko'] || this.nav['isOpera']) this.container.style.width=(area_width+45)+\"px\";\nelse this.container.style.width=area_width+\"px\";this.textarea.style.width=area_width+\"px\";this.content_highlight.style.width=area_width+\"px\";this.textarea.previous_scrollWidth=area_width;resized=true;}if(this.textarea.previous_scrollHeight!=area_height){this.container.style.height=(area_height+2)+\"px\";this.textarea.style.height=area_height+\"px\";this.content_highlight.style.height=area_height+\"px\";this.textarea.previous_scrollHeight=area_height;resized=true;}this.textarea.scrollTop=\"0px\";this.textarea.scrollLeft=\"0px\";if(resized==true){this.scroll_to_view();}}setTimeout(\"eA.manage_size();\", 100);};EA.prototype.add_event=function(obj, name, handler){if (this.nav['isIE']){obj.attachEvent(\"on\" + name, handler);}\nelse{obj.addEventListener(name, handler, false);}};EA.prototype.execCommand=function(cmd, param){for(var i in this.plugins){if(typeof(this.plugins[i].execCommand)==\"function\"){if(!this.plugins[i].execCommand(cmd, param)) return;}}switch(cmd){case \"save\": if(this.settings[\"save_callback\"].length>0) eval(\"parent.\"+this.settings[\"save_callback\"]+\"('\"+ this.id +\"', eA.textarea.value);\");break; case \"save_as\": if(this.settings[\"save_as_callback\"].length>0) eval(\"parent.\"+this.settings[\"save_as_callback\"]+\"('\"+ this.id +\"', eA.textarea.value);\");break; case \"load\": if(this.settings[\"load_callback\"].length>0) eval(\"parent.\"+this.settings[\"load_callback\"]+\"('\"+ this.id +\"');\");break;case \"onchange\": if(this.settings[\"change_callback\"].length>0) eval(\"parent.\"+this.settings[\"change_callback\"]+\"('\"+ this.id +\"');\");break;case \"EA_load\": if(this.settings[\"EA_load_callback\"].length>0) eval(\"parent.\"+this.settings[\"EA_load_callback\"]+\"('\"+ this.id +\"');\");break;case \"EA_unload\": if(this.settings[\"EA_unload_callback\"].length>0) eval(\"parent.\"+this.settings[\"EA_unload_callback\"]+\"('\"+ this.id +\"');\");break;case \"toggle_on\": if(this.settings[\"EA_toggle_on_callback\"].length>0) eval(\"parent.\"+this.settings[\"EA_toggle_on_callback\"]+\"('\"+ this.id +\"');\");break;case \"toggle_off\": if(this.settings[\"EA_toggle_off_callback\"].length>0) eval(\"parent.\"+this.settings[\"EA_toggle_off_callback\"]+\"('\"+ this.id +\"');\");break;case \"re_sync\": if(!this.do_highlight) break;default: if(typeof(eval(\"eA.\"+cmd))==\"function\") try{eval(\"eA.\"+ cmd +\"(param);\");}catch(e){};}};EA.prototype.get_translation=function(word, mode){if(mode==\"template\") return parent.eAL.translate(word, this.settings[\"language\"], mode);\nelse return parent.eAL.get_word_translation(word, this.settings[\"language\"]);};EA.prototype.add_plugin=function(plug_name, plug_obj){for(var i=0;i<this.settings[\"plugins\"].length;i++){if(this.settings[\"plugins\"][i]==plug_name){this.plugins[plug_name]=plug_obj;plug_obj.baseURL=parent.eAL.baseURL + \"plugins/\" + plug_name + \"/\";if( typeof(plug_obj.init)==\"function\" ) plug_obj.init();}}};EA.prototype.load_css=function(url){try{link=document.createElement(\"link\");link.type=\"text/css\";link.rel=\"stylesheet\";link.media=\"all\";link.href=url;head=document.getElementsByTagName(\"head\");head[0].appendChild(link);}catch(e){document.write(\"<link href='\"+ url +\"' rel='stylesheet' type='text/css' />\");}};EA.prototype.load_script=function(url){try{script=document.createElement(\"script\");script.type=\"text/javascript\";script.src =url;head=document.getElementsByTagName(\"head\");head[0].appendChild(script);}catch(e){document.write(\"<script type='text/javascript' src='\" + url + \"'><\"+\"/script>\");}};EA.prototype.add_lang=function(language, values){if(!parent.eAL.lang[language]) parent.eAL.lang[language]=new Object();for(var i in values) parent.eAL.lang[language][i]=values[i];};var eA=new EA();eA.add_event(window, \"load\", init);function init(){setTimeout(\"eA.init();\", 10);};	EA.prototype.focus=function(){this.textarea.focus();this.textareaFocused=true;};EA.prototype.check_line_selection=function(timer_checkup){if(!eAs[this.id]) return false;time=new Date;t1=t2=t3=time.getTime();if(!this.smooth_selection && !this.do_highlight){}\nelse if(this.textareaFocused && eAs[this.id][\"displayed\"]==true && this.isResizing==false){infos=this.get_selection_infos();time=new Date;t2=time.getTime();if(this.last_selection[\"line_start\"] !=infos[\"line_start\"] || this.last_selection[\"line_nb\"] !=infos[\"line_nb\"] || infos[\"full_text\"] !=this.last_selection[\"full_text\"] || this.reload_highlight){new_top=this.lineHeight * (infos[\"line_start\"]-1);new_height=Math.max(0, this.lineHeight * infos[\"line_nb\"]);new_width=Math.max(this.textarea.scrollWidth, this.container.clientWidth -50);this.selection_field.style.top=new_top+\"px\";this.selection_field.style.width=new_width+\"px\";this.selection_field.style.height=new_height+\"px\";document.getElementById(\"cursor_pos\").style.top=new_top+\"px\";if(this.do_highlight==true){var curr_text=infos[\"full_text\"].split(\"\\n\");var content=\"\";var start=Math.max(0,infos[\"line_start\"]-1);var end=Math.min(curr_text.length, infos[\"line_start\"]+infos[\"line_nb\"]-1);for(i=start;i< end;i++){content+=curr_text[i]+\"\\n\";}content=content.replace(/&/g,\"&amp;\");content=content.replace(/</g,\"&lt;\");content=content.replace(/>/g,\"&gt;\");if(this.nav['isIE'] || this.nav['isOpera']) this.selection_field.innerHTML=\"<pre>\" + content.replace(\"\\n\", \"<br/>\") + \"</pre>\";\nelse this.selection_field.innerHTML=content;if(this.reload_highlight || (infos[\"full_text\"] !=this.last_text_to_highlight && (this.last_selection[\"line_start\"]!=infos[\"line_start\"] || this.last_selection[\"line_nb\"]!=infos[\"line_nb\"] || this.last_selection[\"nb_line\"]!=infos[\"nb_line\"]) ) ) this.maj_highlight(infos);}}time=new Date;t3=time.getTime();if(infos[\"line_start\"] !=this.last_selection[\"line_start\"] || infos[\"curr_pos\"] !=this.last_selection[\"curr_pos\"] || infos[\"full_text\"].length!=this.last_selection[\"full_text\"].length || this.reload_highlight){var selec_char=infos[\"curr_line\"].charAt(infos[\"curr_pos\"]-1);var no_real_move=true;if(infos[\"line_nb\"]==1 && (this.assocBracket[selec_char] || this.revertAssocBracket[selec_char]) ){no_real_move=false;if(this.findEndBracket(infos, selec_char) ===true){document.getElementById(\"end_bracket\").style.visibility=\"visible\";document.getElementById(\"cursor_pos\").style.visibility=\"visible\";document.getElementById(\"cursor_pos\").innerHTML=selec_char;document.getElementById(\"end_bracket\").innerHTML=(this.assocBracket[selec_char] || this.revertAssocBracket[selec_char]);}\nelse{document.getElementById(\"end_bracket\").style.visibility=\"hidden\";document.getElementById(\"cursor_pos\").style.visibility=\"hidden\";}}\nelse{document.getElementById(\"cursor_pos\").style.visibility=\"hidden\";document.getElementById(\"end_bracket\").style.visibility=\"hidden\";}this.displayToCursorPosition(\"cursor_pos\", infos[\"line_start\"], infos[\"curr_pos\"]-1, infos[\"curr_line\"], no_real_move);if(infos[\"line_nb\"]==1 && infos[\"line_start\"]!=this.last_selection[\"line_start\"]) this.scroll_to_view();}this.last_selection=infos;}time=new Date;tend=time.getTime();if(timer_checkup){if(this.do_highlight==true) setTimeout(\"eA.check_line_selection(true)\", 50);\nelse setTimeout(\"eA.check_line_selection(true)\", 50);}};EA.prototype.get_selection_infos=function(){if(this.nav['isIE']) this.getIESelection();start=this.textarea.selectionStart;end=this.textarea.selectionEnd;if(this.last_selection[\"selectionStart\"]==start && this.last_selection[\"selectionEnd\"]==end && this.last_selection[\"full_text\"]==this.textarea.value) return this.last_selection;if(this.tabulation!=\"\t\" && this.textarea.value.indexOf(\"\t\")!=-1){var len=this.textarea.value.length;this.textarea.value=this.replace_tab(this.textarea.value);start=end=start+(this.textarea.value.length-len);this.area_select(start, 0);}var selections=new Object();selections[\"selectionStart\"]=start;selections[\"selectionEnd\"]=end;selections[\"full_text\"]=this.textarea.value;selections[\"line_start\"]=1;selections[\"line_nb\"]=1;selections[\"curr_pos\"]=0;selections[\"curr_line\"]=\"\";selections[\"indexOfCursor\"]=0;selections[\"selec_direction\"]=this.last_selection[\"selec_direction\"];var splitTab=selections[\"full_text\"].split(\"\\n\");var nbLine=Math.max(0, splitTab.length);var nbChar=Math.max(0, selections[\"full_text\"].length - (nbLine - 1));if(selections[\"full_text\"].indexOf(\"\\r\")!=-1) nbChar=nbChar - (nbLine -1);selections[\"nb_line\"]=nbLine;selections[\"nb_char\"]=nbChar;if(start>0){var str=selections[\"full_text\"].substr(0,start);selections[\"curr_pos\"]=start - str.lastIndexOf(\"\\n\");selections[\"line_start\"]=Math.max(1, str.split(\"\\n\").length);}\nelse{selections[\"curr_pos\"]=1;}if(end>start){selections[\"line_nb\"]=selections[\"full_text\"].substring(start,end).split(\"\\n\").length;}selections[\"indexOfCursor\"]=this.textarea.selectionStart;selections[\"curr_line\"]=splitTab[Math.max(0,selections[\"line_start\"]-1)];if(selections[\"selectionStart\"]==this.last_selection[\"selectionStart\"]){if(selections[\"selectionEnd\"]>this.last_selection[\"selectionEnd\"]) selections[\"selec_direction\"]=\"down\";\nelse if(selections[\"selectionEnd\"]==this.last_selection[\"selectionStart\"]) selections[\"selec_direction\"]=this.last_selection[\"selec_direction\"];}\nelse if(selections[\"selectionStart\"]==this.last_selection[\"selectionEnd\"] && selections[\"selectionEnd\"]>this.last_selection[\"selectionEnd\"]){selections[\"selec_direction\"]=\"down\";}\nelse{selections[\"selec_direction\"]=\"up\";}document.getElementById(\"nbLine\").innerHTML=nbLine;document.getElementById(\"nbChar\").innerHTML=nbChar;document.getElementById(\"linePos\").innerHTML=selections[\"line_start\"];document.getElementById(\"currPos\").innerHTML=selections[\"curr_pos\"];return selections;};EA.prototype.getIESelection=function(){var range=document.selection.createRange();var stored_range=range.duplicate();stored_range.moveToElementText( this.textarea );stored_range.setEndPoint( 'EndToEnd', range );if(stored_range.parentElement() !=this.textarea) return;var scrollTop=this.result.scrollTop + document.body.scrollTop;var relative_top=range.offsetTop - parent.calculeOffsetTop(this.textarea) + scrollTop;var line_start=Math.round((relative_top / this.lineHeight) +1);var line_nb=Math.round(range.boundingHeight / this.lineHeight);var range_start=stored_range.text.length - range.text.length;var tab=this.textarea.value.substr(0, range_start).split(\"\\n\");range_start+=(line_start - tab.length)*2;this.textarea.selectionStart=range_start;var range_end=this.textarea.selectionStart + range.text.length;tab=this.textarea.value.substr(0, range_start + range.text.length).split(\"\\n\");range_end+=(line_start + line_nb - 1 - tab.length)*2;this.textarea.selectionEnd=range_end;};EA.prototype.setIESelection=function(){var nbLineStart=this.textarea.value.substr(0, this.textarea.selectionStart).split(\"\\n\").length - 1;var nbLineEnd=this.textarea.value.substr(0, this.textarea.selectionEnd).split(\"\\n\").length - 1;var range=document.selection.createRange();range.moveToElementText( this.textarea );range.setEndPoint( 'EndToStart', range );range.moveStart('character', this.textarea.selectionStart - nbLineStart);range.moveEnd('character', this.textarea.selectionEnd - nbLineEnd - (this.textarea.selectionStart - nbLineStart)  );range.select();};EA.prototype.tab_selection=function(){if(this.is_tabbing) return;this.is_tabbing=true;if( this.nav['isIE'] ) this.getIESelection();var start=this.textarea.selectionStart;var end=this.textarea.selectionEnd;var insText=this.textarea.value.substring(start, end);var pos_start=start;var pos_end=end;if (insText.length==0){this.textarea.value=this.textarea.value.substr(0, start) + this.tabulation + this.textarea.value.substr(end);pos_start=start + this.tabulation.length;pos_end=pos_start;}\nelse{start=Math.max(0, this.textarea.value.substr(0, start).lastIndexOf(\"\\n\")+1);endText=this.textarea.value.substr(end);startText=this.textarea.value.substr(0, start);tmp=this.textarea.value.substring(start, end).split(\"\\n\");insText=this.tabulation+tmp.join(\"\\n\"+this.tabulation);this.textarea.value=startText + insText + endText;pos_start=start;pos_end=this.textarea.value.indexOf(\"\\n\", startText.length + insText.length);if(pos_end==-1) pos_end=this.textarea.value.length;}this.textarea.selectionStart=pos_start;this.textarea.selectionEnd=pos_end;if(this.nav['isIE']){this.setIESelection();setTimeout(\"eA.is_tabbing=false;\", 100);}\nelse this.is_tabbing=false;};EA.prototype.invert_tab_selection=function(){if(this.is_tabbing) return;this.is_tabbing=true;if(this.nav['isIE']) this.getIESelection();var start=this.textarea.selectionStart;var end=this.textarea.selectionEnd;var insText=this.textarea.value.substring(start, end);var pos_start=start;var pos_end=end;if (insText.length==0){if(this.textarea.value.substring(start-this.tabulation.length, start)==this.tabulation){this.textarea.value=this.textarea.value.substr(0, start-this.tabulation.length) + this.textarea.value.substr(end);pos_start=Math.max(0, start-this.tabulation.length);pos_end=pos_start;}}\nelse{start=this.textarea.value.substr(0, start).lastIndexOf(\"\\n\")+1;endText=this.textarea.value.substr(end);startText=this.textarea.value.substr(0, start);tmp=this.textarea.value.substring(start, end).split(\"\\n\");insText=\"\";for(i=0;i<tmp.length;i++){for(j=0;j<this.tab_nb_char;j++){if(tmp[i].charAt(0)==\"\t\"){tmp[i]=tmp[i].substr(1);j=this.tab_nb_char;}\nelse if(tmp[i].charAt(0)==\" \") tmp[i]=tmp[i].substr(1);}insText+=tmp[i];if(i<tmp.length-1) insText+=\"\\n\";}this.textarea.value=startText + insText + endText;pos_start=start;pos_end=this.textarea.value.indexOf(\"\\n\", startText.length + insText.length);if(pos_end==-1) pos_end=this.textarea.value.length;}this.textarea.selectionStart=pos_start;this.textarea.selectionEnd=pos_end;if(this.nav['isIE']){this.setIESelection();setTimeout(\"eA.is_tabbing=false;\", 100);}\nelse this.is_tabbing=false;};EA.prototype.press_enter=function(){if(!this.smooth_selection) return false;if(this.nav['isIE']) this.getIESelection();var scrollTop=this.result.scrollTop;var scrollLeft=this.result.scrollLeft;var start=this.textarea.selectionStart;var end=this.textarea.selectionEnd;var start_last_line=Math.max(0 , this.textarea.value.substring(0, start).lastIndexOf(\"\\n\") + 1 );var begin_line=this.textarea.value.substring(start_last_line, start).replace(/^([ \t]*).*/gm, \"$1\");if(begin_line==\"\\n\" || begin_line==\"\\r\" || begin_line.length==0) return false;if(this.nav['isIE'] || this.nav['isOpera']){begin_line=\"\\r\\n\"+ begin_line;}\nelse{begin_line=\"\\n\"+ begin_line;}this.textarea.value=this.textarea.value.substring(0, start) + begin_line + this.textarea.value.substring(end);this.area_select(start+ begin_line.length ,0);if(this.nav['isIE']){this.result.scrollTop=scrollTop;this.result.scrollLeft=scrollLeft;}return true;};EA.prototype.findEndBracket=function(infos, bracket){var start=infos[\"indexOfCursor\"];var normal_order=true;if(this.assocBracket[bracket]) endBracket=this.assocBracket[bracket];\nelse if(this.revertAssocBracket[bracket]){endBracket=this.revertAssocBracket[bracket];normal_order=false;}var end=-1;var nbBracketOpen=0;for(var i=start;i<infos[\"full_text\"].length && i>=0;){if(infos[\"full_text\"].charAt(i)==endBracket){nbBracketOpen--;if(nbBracketOpen<=0){end=i;break;}}\nelse if(infos[\"full_text\"].charAt(i)==bracket) nbBracketOpen++;if(normal_order) i++;\nelse i--;}if(end==-1) return false;var endLastLine=infos[\"full_text\"].substr(0, end).lastIndexOf(\"\\n\");if(endLastLine==-1) line=1;\nelse line=infos[\"full_text\"].substr(0, endLastLine).split(\"\\n\").length + 1;var curPos=end - endLastLine;this.displayToCursorPosition(\"end_bracket\", line, curPos, infos[\"full_text\"].substring(endLastLine +1, end));return true;};EA.prototype.displayToCursorPosition=function(id, start_line, cur_pos, lineContent, no_real_move){var elem=document.getElementById(\"test_font_size\");var dest=document.getElementById(id);var postLeft=0;elem.innerHTML=\"<pre><span id='test_font_size_inner'>\"+lineContent.substr(0, cur_pos).replace(/</g,\"&lt;\").replace(/&/g,\"&amp;\")+\"</span></pre>\";posLeft=45 + document.getElementById('test_font_size_inner').offsetWidth;var posTop=this.lineHeight * (start_line-1);if(no_real_move!=true){dest.style.top=posTop+\"px\";dest.style.left=posLeft+\"px\";}dest.cursor_top=posTop;dest.cursor_left=posLeft;};EA.prototype.area_select=function(start, length){this.textarea.focus();start=Math.max(0, Math.min(this.textarea.value.length, start));end=Math.max(start, Math.min(this.textarea.value.length, start+length));if(this.nav['isIE']){this.textarea.selectionStart=start;this.textarea.selectionEnd=end;this.setIESelection();}\nelse{if(this.nav['isOpera']){this.textarea.setSelectionRange(0, 0);}this.textarea.setSelectionRange(start, end);}this.check_line_selection();};EA.prototype.area_get_selection=function(){var text=\"\";if( document.selection ){var range=document.selection.createRange();text=range.text;}\nelse{text=this.textarea.value.substring(this.textarea.selectionStart, this.textarea.selectionEnd);}return text;}; EA.prototype.replace_tab=function(text){return text.replace(/((\\n?)([^\t\\n]*)\t)/gi, eA.smartTab);};EA.prototype.smartTab=function(){val=\"                   \";return EA.prototype.smartTab.arguments[2] + EA.prototype.smartTab.arguments[3] + val.substr(0, eA.tab_nb_char - (EA.prototype.smartTab.arguments[3].length)%eA.tab_nb_char);};EA.prototype.add_style=function(styles){if(styles.length>0){newcss=document.createElement(\"style\");newcss.type=\"text/css\";newcss.media=\"all\";document.getElementsByTagName(\"head\")[0].appendChild(newcss);cssrules=styles.split(\"}\");newcss=document.styleSheets[0];if(newcss.rules){for(i=cssrules.length-2;i>=0;i--){newrule=cssrules[i].split(\"{\");newcss.addRule(newrule[0],newrule[1])}}\nelse if(newcss.cssRules){for(i=cssrules.length-1;i>=0;i--){if(cssrules[i].indexOf(\"{\")!=-1){newcss.insertRule(cssrules[i]+\"}\",0);}}}}};EA.prototype.set_font=function(family, size){var elems=new Array(\"textarea\", \"content_highlight\", \"cursor_pos\", \"end_bracket\", \"selection_field\", \"line_number\");if(family && family!=\"\") this.settings[\"font_family\"]=family;if(size && size>0) this.settings[\"font_size\"]=size;if(this.nav['isOpera']) this.settings['font_family']=\"monospace\";var elem_font=document.getElementById(\"area_font_size\");if(elem_font){for(var i=0;i<elem_font.length;i++){if(elem_font.options[i].value && elem_font.options[i].value==this.settings[\"font_size\"]) elem_font.options[i].selected=true;}}document.getElementById(\"test_font_size\").style.fontFamily=\"\"+this.settings[\"font_family\"];document.getElementById(\"test_font_size\").style.fontSize=this.settings[\"font_size\"]+\"pt\";document.getElementById(\"test_font_size\").innerHTML=\"0\";this.lineHeight=document.getElementById(\"test_font_size\").offsetHeight;for(var i=0;i<elems.length;i++){var elem=document.getElementById(elems[i]);document.getElementById(elems[i]).style.fontFamily=this.settings[\"font_family\"];document.getElementById(elems[i]).style.fontSize=this.settings[\"font_size\"]+\"pt\";document.getElementById(elems[i]).style.lineHeight=this.lineHeight+\"px\";}if(this.nav['isOpera']){var start=this.textarea.selectionStart;var end=this.textarea.selectionEnd;var parNod=this.textarea.parentNode, nxtSib=this.textarea.nextSibling;parNod.removeChild(this.textarea);parNod.insertBefore(this.textarea, nxtSib);this.area_select(start, end-start);}this.add_style(\"pre{font-family:\"+this.settings[\"font_family\"]+\"}\");this.last_line_selected=-1;this.last_selection=new Array();this.resync_highlight();};EA.prototype.change_font_size=function(){var size=document.getElementById(\"area_font_size\").value;if(size>0) this.set_font(\"\", size);};EA.prototype.open_inline_popup=function(popup_id){this.close_all_inline_popup();var popup=document.getElementById(popup_id);var editor=document.getElementById(\"editor\");for(var i=0;i<this.inlinePopup.length;i++){if(this.inlinePopup[i][\"popup_id\"]==popup_id){var icon=document.getElementById(this.inlinePopup[i][\"icon_id\"]);if(icon){this.switchClassSticky(icon, 'editAreaButtonSelected', true);break;}}}popup.style.height=\"auto\";popup.style.overflow=\"visible\";if(document.body.offsetHeight< popup.offsetHeight){popup.style.height=(document.body.offsetHeight-10)+\"px\";popup.style.overflow=\"auto\";}if(!popup.positionned){var new_left=editor.offsetWidth /2 - popup.offsetWidth /2;var new_top=editor.offsetHeight /2 - popup.offsetHeight /2;popup.style.left=new_left+\"px\";popup.style.top=new_top+\"px\";popup.positionned=true;}popup.style.visibility=\"visible\";};EA.prototype.close_inline_popup=function(popup_id){var popup=document.getElementById(popup_id);for(var i=0;i<this.inlinePopup.length;i++){if(this.inlinePopup[i][\"popup_id\"]==popup_id){var icon=document.getElementById(this.inlinePopup[i][\"icon_id\"]);if(icon){this.switchClassSticky(icon, 'editAreaButtonNormal', false);break;}}}popup.style.visibility=\"hidden\";};EA.prototype.close_all_inline_popup=function(e){for(var i=0;i<this.inlinePopup.length;i++){this.close_inline_popup(this.inlinePopup[i][\"popup_id\"]);}this.textarea.focus();};EA.prototype.show_help=function(){this.open_inline_popup(\"edit_area_help\");};EA.prototype.new_document=function(){this.textarea.value=\"\";this.area_select(0,0);};EA.prototype.get_all_toolbar_height=function(){var area=document.getElementById(\"editor\");var results=parent.getChildren(area, \"div\", \"class\", \"area_toolbar\", \"all\", \"0\");var height=0;for(var i=0;i<results.length;i++){height+=results[i].offsetHeight;}return height;};EA.prototype.go_to_line=function(line){if(!line){var icon=document.getElementById(\"go_to_line\");if(icon !=null){this.restoreClass(icon);this.switchClassSticky(icon, 'editAreaButtonSelected', true);}line=prompt(this.get_translation(\"go_to_line_prompt\"));if(icon !=null) this.switchClassSticky(icon, 'editAreaButtonNormal', false);}if(line && line!=null && line.search(/^[0-9]+$/)!=-1){var start=0;var lines=this.textarea.value.split(\"\\n\");if(line > lines.length) start=this.textarea.value.length;\nelse{for(var i=0;i<Math.min(line-1, lines.length);i++) start+=lines[i].length + 1;}this.area_select(start, 0);}};EA.prototype.change_smooth_selection_mode=function(setTo){if(this.do_highlight) return;if(setTo !=null){if(setTo ===false) this.smooth_selection=true;\nelse this.smooth_selection=false;}var icon=document.getElementById(\"change_smooth_selection\");this.textarea.focus();if(this.smooth_selection===true){this.switchClassSticky(icon, 'editAreaButtonNormal', false);this.smooth_selection=false;document.getElementById(\"selection_field\").style.display=\"none\";document.getElementById(\"cursor_pos\").style.display=\"none\";document.getElementById(\"end_bracket\").style.display=\"none\";}\nelse{this.switchClassSticky(icon, 'editAreaButtonSelected', false);this.smooth_selection=true;document.getElementById(\"selection_field\").style.display=\"block\";document.getElementById(\"cursor_pos\").style.display=\"block\";document.getElementById(\"end_bracket\").style.display=\"block\";}};EA.prototype.scroll_to_view=function(show){if(!this.smooth_selection) return;var zone=document.getElementById(\"result\");var cursor_pos_top=document.getElementById(\"cursor_pos\").cursor_top;if(show==\"bottom\") cursor_pos_top+=(this.last_selection[\"line_nb\"]-1)* this.lineHeight;var max_height_visible=zone.clientHeight + zone.scrollTop;var miss_top=cursor_pos_top + this.lineHeight - max_height_visible;if(miss_top>0){zone.scrollTop=zone.scrollTop + miss_top;}\nelse if( zone.scrollTop > cursor_pos_top){zone.scrollTop=cursor_pos_top;}var cursor_pos_left=document.getElementById(\"cursor_pos\").cursor_left;var max_width_visible=zone.clientWidth + zone.scrollLeft;var miss_left=cursor_pos_left + 10 - max_width_visible;if(miss_left>0){zone.scrollLeft=zone.scrollLeft + miss_left+ 50;}\nelse if( zone.scrollLeft > cursor_pos_left){zone.scrollLeft=cursor_pos_left;}\nelse if( zone.scrollLeft==45){zone.scrollLeft=0;}};EA.prototype.check_undo=function(){if(!eAs[this.id]) return false;if(this.textareaFocused && eAs[this.id][\"displayed\"]==true){var text=this.textarea.value;if(this.previous.length<=1) this.switchClassSticky(document.getElementById(\"undo\"), 'editAreaButtonDisabled', true);if(!this.previous[this.previous.length-1] || this.previous[this.previous.length-1][\"text\"] !=text){this.previous.push({\"text\": text, \"selStart\": this.textarea.selectionStart, \"selEnd\": this.textarea.selectionEnd});if(this.previous.length > this.settings[\"max_undo\"]+1) this.previous.shift();}if(this.previous.length==2) this.switchClassSticky(document.getElementById(\"undo\"), 'editAreaButtonNormal', false);}setTimeout(\"eA.check_undo()\", 3000);};EA.prototype.undo=function(){if(this.previous.length > 0){if(this.nav['isIE']) this.getIESelection();this.next.push({\"text\": this.textarea.value, \"selStart\": this.textarea.selectionStart, \"selEnd\": this.textarea.selectionEnd});var prev=this.previous.pop();if(prev[\"text\"]==this.textarea.value && this.previous.length > 0) prev=this.previous.pop();this.textarea.value=prev[\"text\"];this.last_undo=prev[\"text\"];this.area_select(prev[\"selStart\"], prev[\"selEnd\"]-prev[\"selStart\"]);this.switchClassSticky(document.getElementById(\"redo\"), 'editAreaButtonNormal', false);this.resync_highlight(true);}};EA.prototype.redo=function(){if(this.next.length > 0){var next=this.next.pop();this.previous.push(next);this.textarea.value=next[\"text\"];this.last_undo=next[\"text\"];this.area_select(next[\"selStart\"], next[\"selEnd\"]-next[\"selStart\"]);this.switchClassSticky(document.getElementById(\"undo\"), 'editAreaButtonNormal', false);this.resync_highlight(true);}if(	this.next.length==0) this.switchClassSticky(document.getElementById(\"redo\"), 'editAreaButtonDisabled', true);};EA.prototype.check_redo=function(){if(eA.next.length > 0 && eA.textarea.value!=eA.last_undo){eA.next=new Array();eA.switchClassSticky(document.getElementById(\"redo\"), 'editAreaButtonDisabled', true);}};EA.prototype.switchClass=function(element, class_name, lock_state){var lockChanged=false;if (typeof(lock_state) !=\"undefined\" && element !=null){element.classLock=lock_state;lockChanged=true;}if (element !=null && (lockChanged || !element.classLock)){element.oldClassName=element.className;element.className=class_name;}};EA.prototype.restoreAndSwitchClass=function(element, class_name){if (element !=null && !element.classLock){this.restoreClass(element);this.switchClass(element, class_name);}};EA.prototype.restoreClass=function(element){if (element !=null && element.oldClassName && !element.classLock){element.className=element.oldClassName;element.oldClassName=null;}};EA.prototype.setClassLock=function(element, lock_state){if (element !=null) element.classLock=lock_state;};EA.prototype.switchClassSticky=function(element, class_name, lock_state){var lockChanged=false;if (typeof(lock_state) !=\"undefined\" && element !=null){element.classLock=lock_state;lockChanged=true;}if (element !=null && (lockChanged || !element.classLock)){element.className=class_name;element.oldClassName=class_name;if (this.nav['isOpera']){if (class_name==\"mceButtonDisabled\"){var suffix=\"\";if (!element.mceOldSrc) element.mceOldSrc=element.src;if (this.operaOpacityCounter > -1) suffix='?rnd=' + this.operaOpacityCounter++;element.src=this.baseURL + \"/images/opacity.png\";element.style.backgroundImage=\"url('\" + element.mceOldSrc + \"')\";}\nelse{if (element.mceOldSrc){element.src=element.mceOldSrc;element.parentNode.style.backgroundImage=\"\";element.mceOldSrc=null;}}}}};EA.prototype.scroll_page=function(params){var dir=params[\"dir\"];var shift_pressed=params[\"shift\"];screen_height=document.getElementById(\"result\").clientHeight;var lines=this.textarea.value.split(\"\\n\");var new_pos=0;var length=0;var char_left=0;var line_nb=0;if(dir==\"up\"){var scroll_line=Math.ceil((screen_height -30)/this.lineHeight);if(this.last_selection[\"selec_direction\"]==\"up\"){for(line_nb=0;line_nb< Math.min(this.last_selection[\"line_start\"]-scroll_line, lines.length);line_nb++){new_pos+=lines[line_nb].length + 1;}char_left=Math.min(lines[Math.min(lines.length-1, line_nb)].length, this.last_selection[\"curr_pos\"]-1);if(shift_pressed) length=this.last_selection[\"selectionEnd\"]-new_pos-char_left;this.area_select(new_pos+char_left, length);view=\"top\";}\nelse{view=\"bottom\";for(line_nb=0;line_nb< Math.min(this.last_selection[\"line_start\"]+this.last_selection[\"line_nb\"]-1-scroll_line, lines.length);line_nb++){new_pos+=lines[line_nb].length + 1;}char_left=Math.min(lines[Math.min(lines.length-1, line_nb)].length, this.last_selection[\"curr_pos\"]-1);if(shift_pressed){start=Math.min(this.last_selection[\"selectionStart\"], new_pos+char_left);length=Math.max(new_pos+char_left, this.last_selection[\"selectionStart\"] )- start;if(new_pos+char_left < this.last_selection[\"selectionStart\"]) view=\"top\";}\nelse start=new_pos+char_left;this.area_select(start, length);}}\nelse{var scroll_line=Math.floor((screen_height-30)/this.lineHeight);if(this.last_selection[\"selec_direction\"]==\"down\"){view=\"bottom\";for(line_nb=0;line_nb< Math.min(this.last_selection[\"line_start\"]+this.last_selection[\"line_nb\"]-2+scroll_line, lines.length);line_nb++){if(line_nb==this.last_selection[\"line_start\"]-1) char_left=this.last_selection[\"selectionStart\"] -new_pos;new_pos+=lines[line_nb].length + 1;}if(shift_pressed){length=Math.abs(this.last_selection[\"selectionStart\"]-new_pos);length+=Math.min(lines[Math.min(lines.length-1, line_nb)].length, this.last_selection[\"curr_pos\"]);this.area_select(Math.min(this.last_selection[\"selectionStart\"], new_pos), length);}\nelse{this.area_select(new_pos+char_left, 0);}}\nelse{view=\"top\";for(line_nb=0;line_nb< Math.min(this.last_selection[\"line_start\"]+scroll_line-1, lines.length, lines.length);line_nb++){if(line_nb==this.last_selection[\"line_start\"]-1) char_left=this.last_selection[\"selectionStart\"] -new_pos;new_pos+=lines[line_nb].length + 1;}if(shift_pressed){length=Math.abs(this.last_selection[\"selectionEnd\"]-new_pos-char_left);length+=Math.min(lines[Math.min(lines.length-1, line_nb)].length, this.last_selection[\"curr_pos\"])- char_left-1;this.area_select(Math.min(this.last_selection[\"selectionEnd\"], new_pos+char_left), length);if(new_pos+char_left > this.last_selection[\"selectionEnd\"]) view=\"bottom\";}\nelse{this.area_select(new_pos+char_left, 0);}}}this.check_line_selection();this.scroll_to_view(view);};EA.prototype.start_resize=function(e){parent.eAL.resize[\"id\"]=eA.id;parent.eAL.resize[\"start_x\"]=(e)? e.pageX : event.x + document.body.scrollLeft;parent.eAL.resize[\"start_y\"]=(e)? e.pageY : event.y + document.body.scrollTop;if(eA.nav['isIE']){eA.textarea.focus();eA.getIESelection();}parent.eAL.resize[\"selectionStart\"]=eA.textarea.selectionStart;parent.eAL.resize[\"selectionEnd\"]=eA.textarea.selectionEnd;parent.eAL.start_resize_area();};EA.prototype.toggle_full_screen=function(to){if(typeof(to)==\"undefined\") to=!this.fullscreen['isFull'];var old=this.fullscreen['isFull'];this.fullscreen['isFull']=to;var icon=document.getElementById(\"fullscreen\");if(to && to!=old){var selStart=this.textarea.selectionStart;var selEnd=this.textarea.selectionEnd;var html=parent.document.getElementsByTagName(\"html\")[0];var frame=parent.document.getElementById(\"frame_\"+this.id);this.fullscreen['old_overflow']=parent.get_css_property(html, \"overflow\");this.fullscreen['old_height']=parent.get_css_property(html, \"height\");this.fullscreen['old_width']=parent.get_css_property(html, \"width\");this.fullscreen['old_scrollTop']=html.scrollTop;this.fullscreen['old_scrollLeft']=html.scrollLeft;this.fullscreen['old_zIndex']=parent.get_css_property(frame, \"z-index\");if(this.nav['isOpera']){html.style.height=\"100%\";html.style.width=\"100%\";}html.style.overflow=\"hidden\";html.scrollTop=0;html.scrollLeft=0;frame.style.position=\"absolute\";frame.style.width=html.clientWidth+\"px\";frame.style.height=html.clientHeight+\"px\";frame.style.display=\"block\";frame.style.zIndex=\"999999\";frame.style.top=\"0px\";frame.style.left=\"0px\";frame.style.top=\"-\"+parent.calculeOffsetTop(frame)+\"px\";frame.style.left=\"-\"+parent.calculeOffsetLeft(frame)+\"px\";this.switchClassSticky(icon, 'editAreaButtonSelected', false);this.fullscreen['allow_resize']=this.resize_allowed;this.allow_resize(false);if(this.nav['isFirefox']){parent.eAL.execCommand(this.id, \"update_size();\");this.area_select(selStart, selEnd-selStart);this.scroll_to_view();this.focus();}\nelse{setTimeout(\"parent.eAL.execCommand('\"+ this.id +\"', 'update_size();');eA.focus();\", 10);}}\nelse if(to!=old){var selStart=this.textarea.selectionStart;var selEnd=this.textarea.selectionEnd;var frame=parent.document.getElementById(\"frame_\"+this.id);frame.style.position=\"static\";frame.style.zIndex=this.fullscreen['old_zIndex'];var html=parent.document.getElementsByTagName(\"html\")[0];if(this.nav['isOpera']){html.style.height=\"auto\";html.style.width=\"auto\";html.style.overflow=\"auto\";}\nelse if(this.nav['isIE'] && parent!=top){html.style.overflow=\"auto\";}\nelse html.style.overflow=this.fullscreen['old_overflow'];html.scrollTop=this.fullscreen['old_scrollTop'];html.scrollTop=this.fullscreen['old_scrollLeft'];parent.eAL.hide(this.id);parent.eAL.show(this.id);this.switchClassSticky(icon, 'editAreaButtonNormal', false);if(this.fullscreen['allow_resize']) this.allow_resize(this.fullscreen['allow_resize']);if(this.nav['isFirefox']){this.area_select(selStart, selEnd-selStart);setTimeout(\"eA.scroll_to_view();\", 10);}}};EA.prototype.allow_resize=function(allow){var resize=document.getElementById(\"resize_area\");if(allow){resize.style.visibility=\"visible\";parent.eAL.add_event(resize, \"mouseup\", eA.start_resize);}\nelse{resize.style.visibility=\"hidden\";parent.eAL.remove_event(resize, \"mouseup\", eA.start_resize);}this.resize_allowed=allow;};var clavier_cds=new Object(146);clavier_cds[8]=\"Retour arriere\";clavier_cds[9]=\"Tabulation\";clavier_cds[12]=\"Milieu (pave numerique)\";clavier_cds[13]=\"Entrer\";clavier_cds[16]=\"Shift\";clavier_cds[17]=\"Ctrl\";clavier_cds[18]=\"Alt\";clavier_cds[19]=\"Pause\";clavier_cds[20]=\"Verr Maj\";clavier_cds[27]=\"Esc\";clavier_cds[32]=\"Espace\";clavier_cds[33]=\"Page up\";clavier_cds[34]=\"Page down\";clavier_cds[35]=\"End\";clavier_cds[36]=\"Begin\";clavier_cds[37]=\"Fleche gauche\";clavier_cds[38]=\"Fleche haut\";clavier_cds[39]=\"Fleche droite\";clavier_cds[40]=\"Fleche bas\";clavier_cds[44]=\"Impr ecran\";clavier_cds[45]=\"Inser\";clavier_cds[46]=\"Suppr\";clavier_cds[91]=\"Menu Demarrer Windows / touche pomme Mac\";clavier_cds[92]=\"Menu Demarrer Windows\";clavier_cds[93]=\"Menu contextuel Windows\";clavier_cds[112]=\"F1\";clavier_cds[113]=\"F2\";clavier_cds[114]=\"F3\";clavier_cds[115]=\"F4\";clavier_cds[116]=\"F5\";clavier_cds[117]=\"F6\";clavier_cds[118]=\"F7\";clavier_cds[119]=\"F8\";clavier_cds[120]=\"F9\";clavier_cds[121]=\"F10\";clavier_cds[122]=\"F11\";clavier_cds[123]=\"F12\";clavier_cds[144]=\"Verr Num\";clavier_cds[145]=\"Arret defil\";function keyDown(e){if(!e){e=event;}for(var i in eA.plugins){if(typeof(eA.plugins[i].onkeydown)==\"function\"){if(eA.plugins[i].onkeydown(e)===false){if(eA.nav['isIE']) e.keyCode=0;return false;}}}var target_id=(e.target || e.srcElement).id;var use=false;if (clavier_cds[e.keyCode]) letter=clavier_cds[e.keyCode];\nelse letter=String.fromCharCode(e.keyCode);var low_letter=letter.toLowerCase();if(letter==\"Tabulation\" && target_id==\"textarea\" && !CtrlPressed(e) && !AltPressed(e)){if(ShiftPressed(e)) eA.execCommand(\"invert_tab_selection\");\nelse eA.execCommand(\"tab_selection\");use=true;if(eA.nav['isOpera']) setTimeout(\"eA.execCommand('focus');\", 1);}\nelse if(letter==\"Entrer\" && target_id==\"textarea\"){if(eA.press_enter()) use=true;}\nelse if(letter==\"Entrer\" && target_id==\"area_search\"){eA.execCommand(\"area_search\");use=true;}\nelse if(letter==\"Page up\" && !eA.nav['isOpera']){eA.execCommand(\"scroll_page\", {\"dir\": \"up\", \"shift\": ShiftPressed(e)});use=true;}\nelse if(letter==\"Page down\" && !eA.nav['isOpera']){eA.execCommand(\"scroll_page\", {\"dir\": \"down\", \"shift\": ShiftPressed(e)});use=true;}\nelse if(letter==\"Esc\"){eA.execCommand(\"close_all_inline_popup\", e);use=true;}\nelse if(CtrlPressed(e) && !AltPressed(e) && !ShiftPressed(e)){switch(low_letter){case \"f\": eA.execCommand(\"area_search\");use=true;break;case \"r\": eA.execCommand(\"area_replace\");use=true;break;case \"q\": eA.execCommand(\"close_all_inline_popup\", e);use=true;break;case \"h\": eA.execCommand(\"change_highlight\");use=true;break;case \"g\": setTimeout(\"eA.execCommand('go_to_line');\", 5);use=true;break;case \"e\": eA.execCommand(\"show_help\");use=true;break;case \"z\": use=true;eA.execCommand(\"undo\");break;case \"y\": use=true;eA.execCommand(\"redo\");break;default: break;}}if(eA.next.length > 0){setTimeout(\"eA.check_redo();\", 10);}if(use){if(eA.nav['isIE']) e.keyCode=0;return false;}return true;};function AltPressed(e){if (window.event){return (window.event.altKey);}\nelse{if(e.modifiers) return (e.altKey || (e.modifiers % 2));\nelse return e.altKey;}};function CtrlPressed(e){if (window.event){return (window.event.ctrlKey);}\nelse{return (e.ctrlKey || (e.modifiers==2) || (e.modifiers==3) || (e.modifiers>5));}};function ShiftPressed(e){if (window.event){return (window.event.shiftKey);}\nelse{return (e.shiftKey || (e.modifiers>3));}};	EA.prototype.show_search=function(){if(document.getElementById(\"area_search_replace\").style.visibility==\"visible\"){this.hidden_search();}\nelse{this.open_inline_popup(\"area_search_replace\");var text=this.area_get_selection();var search=text.split(\"\\n\")[0];document.getElementById(\"area_search\").value=search;document.getElementById(\"area_search\").focus();}};EA.prototype.hidden_search=function(){this.close_inline_popup(\"area_search_replace\");};EA.prototype.area_search=function(mode){if(!mode) mode=\"search\";document.getElementById(\"area_search_msg\").innerHTML=\"\";var search=document.getElementById(\"area_search\").value;this.textarea.focus();this.textarea.textareaFocused=true;var infos=this.get_selection_infos();var start=infos[\"selectionStart\"];var pos=-1;var pos_begin=-1;var length=search.length;if(document.getElementById(\"area_search_replace\").style.visibility!=\"visible\"){this.show_search();return;}if(search.length==0){document.getElementById(\"area_search_msg\").innerHTML=this.get_translation(\"search_field_empty\");return;}if(mode!=\"replace\" ){if(document.getElementById(\"area_search_reg_exp\").checked) start++;\nelse start+=search.length;}if(document.getElementById(\"area_search_reg_exp\").checked){var opt=\"m\";if(!document.getElementById(\"area_search_match_case\").checked) opt+=\"i\";var reg=new RegExp(search, opt);pos=infos[\"full_text\"].substr(start).search(reg);pos_begin=infos[\"full_text\"].search(reg);if(pos!=-1){pos+=start;length=infos[\"full_text\"].substr(start).match(reg)[0].length;}\nelse if(pos_begin!=-1){length=infos[\"full_text\"].match(reg)[0].length;}}\nelse{if(document.getElementById(\"area_search_match_case\").checked){pos=infos[\"full_text\"].indexOf(search, start);pos_begin=infos[\"full_text\"].indexOf(search);}\nelse{pos=infos[\"full_text\"].toLowerCase().indexOf(search.toLowerCase(), start);pos_begin=infos[\"full_text\"].toLowerCase().indexOf(search.toLowerCase());}}if(pos==-1 && pos_begin==-1){document.getElementById(\"area_search_msg\").innerHTML=\"<strong>\"+search+\"</strong> \"+this.get_translation(\"not_found\");return;}\nelse if(pos==-1 && pos_begin !=-1){begin=pos_begin;document.getElementById(\"area_search_msg\").innerHTML=this.get_translation(\"restart_search_at_begin\");}\nelse begin=pos;if(mode==\"replace\" && pos==infos[\"indexOfCursor\"]){var replace=document.getElementById(\"area_replace\").value;var new_text=\"\";if(document.getElementById(\"area_search_reg_exp\").checked){var opt=\"m\";if(!document.getElementById(\"area_search_match_case\").checked) opt+=\"i\";var reg=new RegExp(search, opt);new_text=infos[\"full_text\"].substr(0, begin) + infos[\"full_text\"].substr(start).replace(reg, replace);}\nelse{new_text=infos[\"full_text\"].substr(0, begin) + replace + infos[\"full_text\"].substr(begin + length);}this.textarea.value=new_text;this.area_select(begin, length);this.area_search();}\nelse this.area_select(begin, length);};EA.prototype.area_replace=function(){this.area_search(\"replace\");};EA.prototype.area_replace_all=function(){var base_text=this.textarea.value;var search=document.getElementById(\"area_search\").value;var replace=document.getElementById(\"area_replace\").value;if(search.length==0){document.getElementById(\"area_search_msg\").innerHTML=this.get_translation(\"search_field_empty\");return;}var new_text=\"\";var nb_change=0;if(document.getElementById(\"area_search_reg_exp\").checked){var opt=\"mg\";if(!document.getElementById(\"area_search_match_case\").checked) opt+=\"i\";var reg=new RegExp(search, opt);nb_change=infos[\"full_text\"].match(reg).length;new_text=infos[\"full_text\"].replace(reg, replace);}\nelse{if(document.getElementById(\"area_search_match_case\").checked){var tmp_tab=base_text.split(search);nb_change=tmp_tab.length -1;new_text=tmp_tab.join(replace);}\nelse{var lower_value=base_text.toLowerCase();var lower_search=search.toLowerCase();var start=0;var pos=lower_value.indexOf(lower_search);while(pos!=-1){nb_change++;new_text+=this.textarea.value.substring(start , pos)+replace;start=pos+ search.length;pos=lower_value.indexOf(lower_search, pos+1);}new_text+=this.textarea.value.substring(start);}}if(new_text==base_text){document.getElementById(\"area_search_msg\").innerHTML=\"<strong>\"+search+\"</strong> \"+this.get_translation(\"not_found\");}\nelse{this.textarea.value=new_text;document.getElementById(\"area_search_msg\").innerHTML=\"<strong>\"+nb_change+\"</strong> \"+this.get_translation(\"occurrence_replaced\");setTimeout(\"eA.textarea.focus();eA.textarea.textareaFocused=true;\", 100);}}; EA.prototype.change_highlight=function(change_to){if(this.settings[\"syntax\"].length==0){this.switchClassSticky(document.getElementById(\"highlight\"), 'editAreaButtonDisabled', true);this.switchClassSticky(document.getElementById(\"reset_highlight\"), 'editAreaButtonDisabled', true);return false;}if(this.nav['isIE']) this.getIESelection();var pos_start=this.textarea.selectionStart;var pos_end=this.textarea.selectionEnd;if(this.do_highlight===true || change_to==false) this.disable_highlight();\nelse this.enable_highlight();this.textarea.focus();this.textarea.selectionStart=pos_start;this.textarea.selectionEnd=pos_end;if(this.nav['isIE']) this.setIESelection();};EA.prototype.disable_highlight=function(displayOnly){document.getElementById(\"selection_field\").innerHTML=\"\";this.content_highlight.style.visibility=\"hidden\";var new_Obj=this.content_highlight.cloneNode(false);new_Obj.innerHTML=\"\";this.content_highlight.parentNode.insertBefore(new_Obj, this.content_highlight);this.content_highlight.parentNode.removeChild(this.content_highlight);this.content_highlight=new_Obj;var old_class=parent.getAttribute(this.textarea,\"class\");if(old_class){var new_class=old_class.replace(\"hidden\",\"\");parent.setAttribute(this.textarea, \"class\", new_class);}this.textarea.style.backgroundColor=\"transparent\";this.switchClassSticky(document.getElementById(\"highlight\"), 'editAreaButtonNormal', false);this.switchClassSticky(document.getElementById(\"reset_highlight\"), 'editAreaButtonDisabled', true);this.do_highlight=false;this.switchClassSticky(document.getElementById(\"change_smooth_selection\"), 'editAreaButtonSelected', true);if(typeof(this.smooth_selection_before_highlight)!=\"undefined\" && this.smooth_selection_before_highlight===false){this.change_smooth_selection_mode(false);}};EA.prototype.enable_highlight=function(){width=document.getElementById(\"editor\").offsetWidth;height=document.getElementById(\"editor\").offsetHeight;if(this.nav['isGecko'] || this.nav['isOpera'] || this.nav['isIE']>=7){width-=2;height-=2;}if(this.textarea.value.length>0){this.should_display_processing_screen=true;document.getElementById(\"processing\").style.display=\"block\";document.getElementById(\"processing\").style.width=width+\"px\";document.getElementById(\"processing\").style.height=height+\"px\";}this.content_highlight.style.visibility=\"visible\";var new_class=parent.getAttribute(this.textarea,\"class\")+\" hidden\";parent.setAttribute(this.textarea, \"class\", new_class);if(this.nav['isIE']) this.textarea.style.backgroundColor=\"#FFFFFF\";this.switchClassSticky(document.getElementById(\"highlight\"), 'editAreaButtonSelected', false);this.switchClassSticky(document.getElementById(\"reset_highlight\"), 'editAreaButtonNormal', false);this.smooth_selection_before_highlight=this.smooth_selection;if(!this.smooth_selection) this.change_smooth_selection_mode(true);this.switchClassSticky(document.getElementById(\"change_smooth_selection\"), 'editAreaButtonDisabled', true);this.do_highlight=true;this.resync_highlight();};EA.prototype.maj_highlight=function(infos){if(this.last_highlight_base_text==infos[\"full_text\"] && this.resync_highlight!==true) return;if(infos[\"full_text\"].indexOf(\"\\r\")!=-1) text_to_highlight=infos[\"full_text\"].replace(/\\r/g, \"\");\nelse text_to_highlight=infos[\"full_text\"];var start_line_pb=-1;var end_line_pb=-1;var stay_begin=\"\";var stay_end=\"\";var debug_opti=\"\";var date=new Date();var tps_start=date.getTime();var tps_middle_opti=date.getTime();if(this.reload_highlight===true){this.reload_highlight=false;}\nelse if(text_to_highlight.length==0){text_to_highlight=\"\\n \";}\nelse{var base_step=200;var cpt=0;var end=Math.min(text_to_highlight.length, this.last_text_to_highlight.length);var step=base_step;while(cpt<end && step>=1){if(this.last_text_to_highlight.substr(cpt, step)==text_to_highlight.substr(cpt, step)){cpt+=step;}\nelse{step=Math.floor(step/2);}}var pos_start_change=cpt;var line_start_change=text_to_highlight.substr(0, pos_start_change).split(\"\\n\").length -1;cpt_last=this.last_text_to_highlight.length;cpt=text_to_highlight.length;step=base_step;while(cpt>=0 && cpt_last>=0 && step>=1){if(this.last_text_to_highlight.substr(cpt_last-step, step)==text_to_highlight.substr(cpt-step, step)){cpt-=step;cpt_last-=step;}\nelse{step=Math.floor(step/2);}}var pos_new_end_change=cpt;var pos_last_end_change=cpt_last;if(pos_new_end_change<=pos_start_change){if(this.last_text_to_highlight.length < text_to_highlight.length){pos_new_end_change=pos_start_change + text_to_highlight.length - this.last_text_to_highlight.length;pos_last_end_change=pos_start_change;}\nelse{pos_last_end_change=pos_start_change + this.last_text_to_highlight.length - text_to_highlight.length;pos_new_end_change=pos_start_change;}}var change_new_text=text_to_highlight.substring(pos_start_change, pos_new_end_change);var change_last_text=this.last_text_to_highlight.substring(pos_start_change, pos_last_end_change);var line_new_end_change=text_to_highlight.substr(0, pos_new_end_change).split(\"\\n\").length -1;var line_last_end_change=this.last_text_to_highlight.substr(0, pos_last_end_change).split(\"\\n\").length -1;var change_new_text_line=text_to_highlight.split(\"\\n\").slice(line_start_change, line_new_end_change+1).join(\"\\n\");var change_last_text_line=this.last_text_to_highlight.split(\"\\n\").slice(line_start_change, line_last_end_change+1).join(\"\\n\");var trace_new=this.get_syntax_trace(change_new_text_line);var trace_last=this.get_syntax_trace(change_last_text_line);if(trace_new==trace_last){date=new Date();tps_middle_opti=date.getTime();stay_begin=this.last_hightlighted_text.split(\"\\n\").slice(0, line_start_change).join(\"\\n\");if(line_start_change>0) stay_begin+=\"\\n\";stay_end=this.last_hightlighted_text.split(\"\\n\").slice(line_last_end_change+1).join(\"\\n\");if(stay_end.length>0) stay_end=\"\\n\"+stay_end;if(stay_begin.length==0 && pos_last_end_change==-1) change_new_text_line+=\"\\n\";text_to_highlight=change_new_text_line;}if(this.settings[\"debug\"]){debug_opti=(trace_new==trace_last)?\"Optimisation\": \"No optimisation\";debug_opti+=\" start: \"+pos_start_change +\"(\"+line_start_change+\")\";debug_opti+=\" end_new: \"+ pos_new_end_change+\"(\"+line_new_end_change+\")\";debug_opti+=\" end_last: \"+ pos_last_end_change+\"(\"+line_last_end_change+\")\";debug_opti+=\"\\nchanged_text: \"+change_new_text+\" => trace: \"+trace_new;debug_opti+=\"\\nchanged_last_text: \"+change_last_text+\" => trace: \"+trace_last;debug_opti+=\"\\nchanged_line: \"+change_new_text_line;debug_opti+=\"\\nlast_changed_line: \"+change_last_text_line;debug_opti+=\"\\nstay_begin: \"+ stay_begin.slice(-200);debug_opti+=\"\\nstay_end: \"+ stay_end;debug_opti+=\"\\n\";}}date=new Date();tps_end_opti=date.getTime();var updated_highlight=this.colorize_text(text_to_highlight);date=new Date();tps2=date.getTime();var hightlighted_text=stay_begin + updated_highlight + stay_end;date=new Date();inner1=date.getTime();var new_Obj=this.content_highlight.cloneNode(false);if(this.nav['isIE'] || this.nav['isOpera']) new_Obj.innerHTML=\"<pre><span class='\"+ this.settings[\"syntax\"] +\"'>\" + hightlighted_text.replace(\"\\n\", \"<br/>\") + \"</span></pre>\";\nelse new_Obj.innerHTML=\"<span class='\"+ this.settings[\"syntax\"] +\"'>\"+ hightlighted_text +\"</span>\";this.content_highlight.parentNode.insertBefore(new_Obj, this.content_highlight);this.content_highlight.parentNode.removeChild(this.content_highlight);this.content_highlight=new_Obj;if(infos[\"full_text\"].indexOf(\"\\r\")!=-1) this.last_text_to_highlight=infos[\"full_text\"].replace(/\\r/g, \"\");\nelse this.last_text_to_highlight=infos[\"full_text\"];this.last_hightlighted_text=hightlighted_text;date=new Date();tps3=date.getTime();if(this.settings[\"debug\"]){tot1=tps_end_opti-tps_start;tot_middle=tps_end_opti- tps_middle_opti;tot2=tps2-tps_end_opti;tps_join=inner1-tps2;tps_td2=tps3-inner1;this.debug.value=\"Tps optimisation \"+tot1+\" (second part: \"+tot_middle+\") | tps reg exp: \"+tot2+\" | tps join: \"+tps_join;this.debug.value+=\" | tps update highlight content: \"+tps_td2+\"(\"+tps3+\")\\n\";this.debug.value+=debug_opti;}if(this.should_display_processing_screen){this.should_display_processing_screen=false;document.getElementById(\"processing\").style.display=\"none\";}};EA.prototype.resync_highlight=function(reload_now){this.reload_highlight=true;this.last_highlight_base_text=\"\";this.focus();if(reload_now) this.check_line_selection(false);}; EA.prototype.comment_or_quote=function(){var new_class=\"\";var close_tag=\"\";for(var i in parent.eAL.syntax[eA.current_code_lang][\"quotes\"]){if(EA.prototype.comment_or_quote.arguments[0].indexOf(i)==0){new_class=\"quotesmarks\";close_tag=parent.eAL.syntax[eA.current_code_lang][\"quotes\"][i];}}if(new_class.length==0){for(var i in parent.eAL.syntax[eA.current_code_lang][\"comments\"]){if(EA.prototype.comment_or_quote.arguments[0].indexOf(i)==0){new_class=\"comments\";close_tag=parent.eAL.syntax[eA.current_code_lang][\"comments\"][i];}}}if(close_tag==\"\\n\"){return \"µ__\"+ new_class +\"__µ\"+EA.prototype.comment_or_quote.arguments[0].replace(/(\\r?\\n)?$/m, \"µ_END_µ$1\");}\nelse{reg=new RegExp(parent.eAL.get_escaped_regexp(close_tag)+\"$\", \"m\");if(EA.prototype.comment_or_quote.arguments[0].search(reg)!=-1) return \"µ__\"+ new_class +\"__µ\"+EA.prototype.comment_or_quote.arguments[0]+\"µ_END_µ\";\nelse return \"µ__\"+ new_class +\"__µ\"+EA.prototype.comment_or_quote.arguments[0];}};EA.prototype.get_syntax_trace=function(text){if(this.settings[\"syntax\"].length>0 && parent.eAL.syntax[this.settings[\"syntax\"]][\"syntax_trace_regexp\"]) return text.replace(parent.eAL.syntax[this.settings[\"syntax\"]][\"syntax_trace_regexp\"], \"$3\");};EA.prototype.colorize_text=function(text){text=\" \"+text;if(this.settings[\"syntax\"].length>0) text=this.apply_syntax(text, this.settings[\"syntax\"]);text=text.substr(1);text=text.replace(/&/g,\"&amp;\");text=text.replace(/</g,\"&lt;\");text=text.replace(/>/g,\"&gt;\");text=text.replace(/µ_END_µ/g,\"</span>\");text=text.replace(/µ__([a-zA-Z0-9]+)__µ/g,\"<span class='$1'>\");return text;};EA.prototype.apply_syntax=function(text, lang){this.current_code_lang=lang;if(!parent.eAL.syntax[lang]) return text;if(parent.eAL.syntax[lang][\"custom_regexp\"]['before']){for( var i in parent.eAL.syntax[lang][\"custom_regexp\"]['before']){var convert=\"$1µ__\"+ parent.eAL.syntax[lang][\"custom_regexp\"]['before'][i]['class'] +\"__µ$2µ_END_µ$3\";text=text.replace(parent.eAL.syntax[lang][\"custom_regexp\"]['before'][i]['regexp'], convert);}}if(parent.eAL.syntax[lang][\"comment_or_quote_reg_exp\"]){text=text.replace(parent.eAL.syntax[lang][\"comment_or_quote_reg_exp\"], this.comment_or_quote);}if(parent.eAL.syntax[lang][\"keywords_reg_exp\"]){for(var i in parent.eAL.syntax[lang][\"keywords_reg_exp\"]){text=text.replace(parent.eAL.syntax[lang][\"keywords_reg_exp\"][i], 'µ__'+i+'__µ$2µ_END_µ');}}if(parent.eAL.syntax[lang][\"delimiters_reg_exp\"]){text=text.replace(parent.eAL.syntax[lang][\"delimiters_reg_exp\"], 'µ__delimiters__µ$1µ_END_µ');}if(parent.eAL.syntax[lang][\"operators_reg_exp\"]){text=text.replace(parent.eAL.syntax[lang][\"operators_reg_exp\"], 'µ__operators__µ$1µ_END_µ');}if(parent.eAL.syntax[lang][\"custom_regexp\"]['after']){for( var i in parent.eAL.syntax[lang][\"custom_regexp\"]['after']){var convert=\"$1µ__\"+ parent.eAL.syntax[lang][\"custom_regexp\"]['after'][i]['class'] +\"__µ$2µ_END_µ$3\";text=text.replace(parent.eAL.syntax[lang][\"custom_regexp\"]['after'][i]['regexp'], convert);}}return text;};var editArea= eA;EditArea=EA;</script>";
editAreaLoader.template= "<?xml version=\"1.0\" encoding=\"UTF-8\"?> <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\"> <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" > <head> <title>EditArea</title> <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /> [__CSSRULES__] [__JSCODE__] </head> <body> <div class='editor' id='editor'> <div class='area_toolbar' id='toolbar_1'>[__TOOLBAR__]</div> <div id='result'> <div id='container'> <div id='cursor_pos' class='edit_area_cursor'>&nbsp;</div> <div id='end_bracket' class='edit_area_cursor'>&nbsp;</div> <div id='selection_field'></div> <div id='line_number' selec='none'></div> <div id='content_highlight'></div> <div id='test_font_size'></div> <textarea id='textarea' wrap='off' onchange='editArea.execCommand(\"onchange\");' onfocus='javascript:editArea.textareaFocused=true;' onblur='javascript:editArea.textareaFocused=false;'> </textarea> </div> </div> <div class='area_toolbar' id='toolbar_2'> <table class='statusbar' cellspacing='0' cellpadding='0'> <tr> <td class='total' selec='none'>{$position}:</td> <td class='infos' selec='none'> {$line_abbr} <span  id='linePos'>0</span>, {$char_abbr} <span id='currPos'>0</span> </td> <td class='total' selec='none'>{$total}:</td> <td class='infos' selec='none'> {$line_abbr} <span id='nbLine'>0</span>, {$char_abbr} <span id='nbChar'>0</span> </td> <td class='resize'> <span id='resize_area'><img src='[__BASEURL__]images/statusbar_resize.gif' alt='resize' selec='none'></span> </td> </tr> </table> </div> </div> <div id='processing'> <div id='processing_text'> {$processing} </div> </div> <div id='area_search_replace' class='editarea_popup'> <table cellspacing='2' cellpadding='0' style='width: 100%'> <tr> <td selec='none'>{$search}</td> <td><input type='text' id='area_search' /></td> <td id='close_area_search_replace'> <a onclick='Javascript:editArea.execCommand(\"hidden_search\")'><img selec='none' src='[__BASEURL__]images/close.gif' alt='{$close_popup}' title='{$close_popup}' /></a><br /> </tr><tr> <td selec='none'>{$replace}</td> <td><input type='text' id='area_replace' /></td> <td><img id='move_area_search_replace' onmousedown='return parent.start_move_element(event,\"area_search_replace\", parent.frames[\"frame_\"+editArea.id]);'  src='[__BASEURL__]images/move.gif' alt='{$move_popup}' title='{$move_popup}' /></td> </tr> </table> <div class='button'> <input type='checkbox' id='area_search_match_case' /><label for='area_search_match_case' selec='none'>{$match_case}</label> <input type='checkbox' id='area_search_reg_exp' /><label for='area_search_reg_exp' selec='none'>{$reg_exp}</label> <br /> <a onclick='Javascript:editArea.execCommand(\"area_search\")' selec='none'>{$find_next}</a> <a onclick='Javascript:editArea.execCommand(\"area_replace\")' selec='none'>{$replace}</a> <a onclick='Javascript:editArea.execCommand(\"area_replace_all\")' selec='none'>{$replace_all}</a><br /> </div> <div id='area_search_msg' selec='none'></div> </div> <div id='edit_area_help' class='editarea_popup'> <div class='close_popup'> <a onclick='Javascript:editArea.execCommand(\"close_all_inline_popup\")'><img src='[__BASEURL__]images/close.gif' alt='{$close_popup}' title='{$close_popup}' /></a> </div> <div><h2>Editarea [__EA_VERSION__]</h2><br /> <h3>{$shortcuts}:</h3> {$tab}: {$add_tab}<br /> {$shift}+{$tab}: {$remove_tab}<br /> {$ctrl}+f: {$search_command}<br /> {$ctrl}+r: {$replace_command}<br /> {$ctrl}+h: {$highlight}<br /> {$ctrl}+g: {$go_to_line}<br /> {$ctrl}+z: {$undo}<br /> {$ctrl}+y: {$redo}<br /> {$ctrl}+e: {$help}<br /> {$ctrl}+q, {$esc}: {$close_popup}<br /> {$accesskey} E: {$toggle}<br /> <br /> <em>{$about_notice}</em> <br /><div class='copyright'>&copy; Christophe Dolivet - 2007</div> </div> </div> </div> </body> </html> ";
editAreaLoader.iframe_css= "<style>body, html{margin: 0;padding: 0;height: 100%;border: none;overflow: hidden;background-color: #FFFFFF;}body, html, table, form, textarea{font: 12px monospace, sans-serif;}#editor{border: solid #888888 1px;overflow: hidden;}#result{z-index: 4;overflow: scroll;border-top: solid #888888 1px;border-bottom: solid #888888 1px;position: relative;}#container{overflow: hidden;border: solid blue 0px;position: relative;padding: 0 5px 0px 0;z-index: 10;}#textarea{position: relative;top: 0px;left: 0px;padding: 0px 0px 0px 45px;width: 100%;height: 100%;overflow: hidden;z-index: 7;border: solid green 0px;}#content_highlight{white-space: pre;padding: 1px 0 0 45px;position : absolute;z-index: 4;overflow: visible;border: solid yellow 0px;}#selection_field{padding: 0px 0px 0 45px;background-color: #FFFF99;height: 1px;position: absolute;z-index: 5;top: -100px;margin: 1px 0 0 0px;white-space: pre;overflow: hidden;}#line_number{position: absolute;overflow: hidden;border-right: solid black 1px;z-index:8;width: 38px;padding-right: 5px;text-align: right;color: #AAAAAA;}#test_font_size{padding: 0;margin: 0;visibility: hidden;position: absolute;white-space: pre;}pre{margin: 0;padding: 0;}.hidden{opacity: 0.2;-moz-opacity: 0.2;filter:alpha(opacity=20);}#result .edit_area_cursor{position: absolute;z-index:6;background-color: #FF6633;top: -100px;margin: 1px 0 0 0px;}#result .edit_area_selection_field .overline{background-color: #996600;}.editarea_popup{border: solid 1px #888888;background-color: #ECE9D8;width: 250px;padding: 4px;position: absolute;visibility: hidden;z-index: 15;top: -500px;}.editarea_popup, .editarea_popup table{font-family: sans-serif;font-size: 10pt;}.editarea_popup img{border: 0;}.editarea_popup .close_popup{float: right;line-height: 16px;border: 0px;padding: 0px;}.editarea_popup h1,.editarea_popup h2,.editarea_popup h3,.editarea_popup h4,.editarea_popup h5,.editarea_popup h6{margin: 0px;padding: 0px;}.editarea_popup .copyright{text-align: right;}div#area_search_replace{}div#area_search_replace img{border: 0px;}div#area_search_replace div.button{text-align: center;line-height: 1.7em;}div#area_search_replace .button a{cursor: pointer;border: solid 1px #888888;background-color: #DEDEDE;text-decoration: none;padding: 0 2px;color: #000000;white-space: nowrap;}div#area_search_replace a:hover{background-color: #EDEDED;}div#area_search_replace  #move_area_search_replace{cursor: move;border: solid 1px #888888;}div#area_search_replace  #close_area_search_replace{text-align: right;vertical-align: top;white-space: nowrap;}div#area_search_replace  #area_search_msg{height: 18px;overflow: hidden;border-top: solid 1px #888888;margin-top: 3px;}#edit_area_help{width: 350px;}#edit_area_help div.close_popup{float: right;}.area_toolbar{width: 100%;margin: 0px;padding: 0px;background-color: #ECE9D8;text-align: center;}.area_toolbar, .area_toolbar table{font: 11px sans-serif;}.area_toolbar img{border: 0px;vertical-align: middle;}.area_toolbar input{margin: 0px;padding: 0px;}.area_toolbar select{font-family: 'MS Sans Serif',sans-serif,Verdana,Arial;font-size: 7pt;font-weight: normal;margin: 2px 0 0 0 ;padding: 0;vertical-align: top;background-color: #F0F0EE;}table.statusbar{width: 100%;}.area_toolbar td.infos{text-align: center;width: 130px;border-right: solid 1px #888888;border-width: 0 1px 0 0;padding: 0;}.area_toolbar td.total{text-align: right;width: 50px;padding: 0;}.area_toolbar td.resize{text-align: right;}.area_toolbar span#resize_area{cursor: nw-resize;visibility: hidden;}.editAreaButtonNormal, .editAreaButtonOver, .editAreaButtonDown, .editAreaSeparator, .editAreaSeparatorLine, .editAreaButtonDisabled, .editAreaButtonSelected {border: 0px; margin: 0px; padding: 0px; background: transparent;margin-top: 0px;margin-left: 1px;padding: 0px;}.editAreaButtonNormal {border: 1px solid #ECE9D8 !important;cursor: pointer;}.editAreaButtonOver {border: 1px solid #0A246A !important;cursor: pointer;background-color: #B6BDD2;}.editAreaButtonDown {cursor: pointer;border: 1px solid #0A246A !important;background-color: #8592B5;}.editAreaButtonSelected {border: 1px solid #C0C0BB !important;cursor: pointer;background-color: #F4F2E8;}.editAreaButtonDisabled {filter:progid:DXImageTransform.Microsoft.Alpha(opacity=30);-moz-opacity:0.3;opacity: 0.3;border: 1px solid #F0F0EE !important;cursor: pointer;}.editAreaSeparatorLine {margin: 1px 2px;background-color: #C0C0BB;width: 2px;height: 18px;}#processing{display: none;background-color:#ECE9D8;border: solid #888888 1px;position: absolute;top: 0;left: 0;width: 100%;height: 100%;z-index: 100;text-align: center;}#processing_text{position:absolute;left: 50%;top: 50%;width: 200px;height: 20px;margin-left: -100px;margin-top: -10px;text-align: center;}</style>";
