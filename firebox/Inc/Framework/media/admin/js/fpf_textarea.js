var FPF_Textarea=function(){function e(e){this.parent=e||document,this.textareaClass=".fpf-field-control.textarea",this.init()}var t=e.prototype;return t.init=function(){this.textareasExist()&&this.initTextareas()},t.initTextareas=function(){var r=this;if(window.IntersectionObserver){var t=new IntersectionObserver(function(e,t){e.forEach(function(e){e.isIntersecting&&(r.initTextarea(e.target),t.unobserve(e.target))})},{rootMargin:"0px 0px 0px 0px"});this.parent.querySelectorAll(this.textareaClass).forEach(function(e){t.observe(e)})}},t.initTextarea=function(e){var i=e.querySelector(".fpf-control-input-item.textarea"),t=i.getAttribute("data-mode");if(t){i.nextElementSibling&&i.nextElementSibling.classList.contains("CodeMirror")&&i.nextElementSibling.remove();var r=wp.codeEditor.defaultSettings?_.clone(wp.codeEditor.defaultSettings):{};r.codemirror=_.extend({},r.codemirror,{indentUnit:2,tabSize:2,mode:t});var n=wp.codeEditor.initialize(i,r),a=e.querySelector(".CodeMirror-sizer");if(a){var o="",s="";if("javascript"===t?(o='&lt;script type="text/javascript"&gt;',s="&lt;/script&gt;"):"css"===t?(o='&lt;style type="text/css"&gt;',s="&lt;/style&gt;"):"text/x-php"===t&&(o="&lt;?php",s="?&gt;"),o&&s){var c=document.createElement("div");c.classList.add("fpf-field-textarea-codemirror-code"),c.innerHTML=o,a.insertBefore(c,a.firstChild);var l=document.createElement("div");l.classList.add("fpf-field-textarea-codemirror-code"),l.innerHTML=s,a.appendChild(l)}}setTimeout(function(){n.codemirror.refresh()},10),n.codemirror.on("keyup",function(e,t){var r=e.getValue();i.value=r,i.innerHTML=r})}},t.textareasExist=function(){return!!this.parent.querySelectorAll(this.textareaClass).length},e}();FPF_Helper.onReady(function(){new FPF_Textarea});

