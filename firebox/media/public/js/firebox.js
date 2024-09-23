function _extends(){return(_extends=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(e[o]=n[o])}return e}).apply(this,arguments)}function _inheritsLoose(e,t){e.prototype=Object.create(t.prototype),_setPrototypeOf(e.prototype.constructor=e,t)}function _setPrototypeOf(e,t){return(_setPrototypeOf=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(e,t){return e.__proto__=t,e})(e,t)}function _createForOfIteratorHelperLoose(e,t){var n="undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(n)return(n=n.call(e)).next.bind(n);if(Array.isArray(e)||(n=_unsupportedIterableToArray(e))||t&&e&&"number"==typeof e.length){n&&(e=n);var o=0;return function(){return o>=e.length?{done:!0}:{done:!1,value:e[o++]}}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function _unsupportedIterableToArray(e,t){if(e){if("string"==typeof e)return _arrayLikeToArray(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);return"Object"===n&&e.constructor&&(n=e.constructor.name),"Map"===n||"Set"===n?Array.from(e):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?_arrayLikeToArray(e,t):void 0}}function _arrayLikeToArray(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,o=new Array(t);n<t;n++)o[n]=e[n];return o}!function(c,a){"use strict";var e={onIdle:function(e){var t,n=["mousedown","mousemove","keypress","scroll","touchstart","click"];function o(){clearTimeout(t),t=setTimeout(i,e.time)}function i(){e.callback(),n.forEach(function(e){a.removeEventListener(e,o)})}this.start=function(){o(),c.addEventListener("load",o),n.forEach(function(e){a.addEventListener(e,o)})},this.bind=function(){this.start()}},onAdBlockDetect:function(e){function t(){n||!function(){var e=a.createElement("div");e.className="adsbygoogle pub_300x250 pub_300x250m pub_728x90 text-ad textAd text_ad text_ads text-ads text-ad-links ad-text adSense adBlock adContent adBanner ebAdDetector",e.setAttribute("style","width: 1px !important; height: 1px !important; position: absolute !important; left: -10000px !important; top: -1000px !important;"),a.body.appendChild(e);var t="none"===getComputedStyle(e,null).display;return setTimeout(function(){e.remove()},5e3),t}()||(n=!0,e.callback())}var n=!1;return this.bind=function(){t(),c.onload=t,c.onload=setTimeout(t,1e3)},this},onHover:function(e){function t(){o=setTimeout(function(){e.callback()},e.delay)}function n(){clearTimeout(o)}var o,i;try{if(!(i=a.querySelectorAll(e.selector)).length)return void(this.error="Selector "+e.selector+" does not match any elements on the page.")}catch(e){return void(this.error=e)}return this.bind=function(){i.forEach(function(e){e.addEventListener("mouseover",t),e.addEventListener("mouseout",n)})},this.unbind=function(){i.forEach(function(e){e.removeEventListener("mouseover",t),e.removeEventListener("mouseout",n)})},this},onExit:function(t){var e=this;t=FireBox.extend({sensitivity:20,aggressive:!1,timer:2e3,delay:0,callback:null},t);function n(e){e.relatedTarget||e.toElement||e.clientY>t.sensitivity||(i=setTimeout(r,t.delay))}function o(){i&&(clearTimeout(i),i=null)}var i=null,r=function(){t.callback(),t.aggressive||e.unbind()};return this.bind=function(){setTimeout(function(){a.addEventListener("mouseout",n),a.addEventListener("mouseenter",o)},t.timer)},this.unbind=function(){a.removeEventListener("mouseout",n),a.removeEventListener("mouseenter",o)},this},onScrollDepth:function(r){var e,t=this;r=FireBox.extend({scroll_depth:"percentage",scroll_depth_value:80,scroll_dir:"down",callback:null,once:!0},r);function n(){var e,t,n,o,i="percentage"==r.scroll_depth?(e=a.documentElement,t=a.body,o="scrollHeight",(e[n="scrollTop"]||t[n])/((e[o]||t[o])-e.clientHeight)*100):c.scrollY;return"up"==s()?i<=r.scroll_depth_value:i>=r.scroll_depth_value}function o(){var e=s()==r.scroll_dir;n()&&e&&"function"==typeof r.callback&&(r.callback(),r.once&&t.unbind()),i=a.body.getBoundingClientRect().top}var s=function(){var e=a.body.getBoundingClientRect().top;return i<e?"up":"down"},i=0;return this.bind=function(){e=FireBox.throttle(o,10),o(),c.addEventListener("scroll",e)},this.unbind=function(){c.removeEventListener("scroll",e)},this},onClick:function(t){if((t=FireBox.extend({selector:null,callback:null},t)).selector){var e=function(e){e.target.closest(t.selector)&&(t.prevent_default&&e.preventDefault(),t.callback(e))};return this.bind=function(){return a.addEventListener("click",e)},this.unbind=function(){return a.removeEventListener("click",e)},this}},onExternalLink:function(o){o=FireBox.extend({selector:null,callback:null},o);function e(e){var t=e.target;if(t.classList.contains("wp-block-button")&&t.children&&0<t.children.length&&"a"==t.children[0].nodeName.toLocaleLowerCase()&&(t=t.children[0]),"a"!=t.nodeName.toLocaleLowerCase()){var n=t.closest("a");if(!n)return;t=n}t.classList.contains("fb-btn-continue")||t.parentNode.classList.contains("fb-btn-continue")||o.selector&&!t.matches(o.selector)&&!t.parentNode.matches(o.selector)||t.hostname.includes(location.hostname)||!t.hostname.length||(e.preventDefault(),o.callback(t))}return this.bind=function(){a.addEventListener("click",e)},this.unbind=function(){a.removeEventListener("click",e)},this},onPageReady:function(e){return this.bind=function(){e.callback()},this},onPageLoad:function(e){return this.bind=function(){c.addEventListener("load",function(){e.callback()})},this},onElementVisibility:function(t){var n=this;if((t=FireBox.extend({selector:"",threshold:.5,on:"",off:"",once:!0},t)).selector&&c.IntersectionObserver){var e=a.querySelector(t.selector),o=!1,i=new IntersectionObserver(function(e){e.forEach(function(e){e.isIntersecting?(t.on(),o=!0,"function"!=typeof t.off&&t.once&&n.unbind()):"function"==typeof t.off&&(t.off(),o&&t.once&&n.unbind())})},{threshold:t.threshold});return this.bind=function(){i.observe(e)},this.unbind=function(){i.unobserve(e)},this}}};c.FireBoxTriggers=e}(window,document),function(r,s,c){"use strict";var n=[],e=function(i){function u(t,e){var n=i.call(this)||this;n.name="FireBox",n.el=t,n.el.box=t.querySelector(".fb-dialog"),n.id=parseInt(t.dataset.id),n.activeTriggers=[],n.opened=!1;var o;try{o=JSON.parse(t.dataset.options)}catch(e){console.warn("FireBox: Cannot parse box's data-options property. Using default settings.",t)}return n.options=n.constructor.extend({name:"FireBox",trigger:"onPageLoad",trigger_selector:"",prevent_default:!0,delay:0,scroll_depth:"percentage",scroll_depth_value:80,scroll_dir:"down",firing_frequency:1,reverse_scroll_close:!1,threshold:.5,close_out_viewport:!1,exit_timer:2e3,idle_time:1e4,animation_open:"transition.fadeIn",animation_close:"transition.fadeOut",animation_duration:400,backdrop:!0,backdrop_color:"rgba(0,0,0,.8)",backdrop_click:!0,disable_page_scroll:!1,close_on_esc:!1,css_class_prefix:"fb-",auto_focus:!1,box_log_id:0,test_mode:!1,debug:!1},e||(o||[])),n.init(),n}_inheritsLoose(u,i);var e=u.prototype;return e.open=function(n){var o=this,i=this.constructor.extend(_extends({},this.options),n);setTimeout(function(){if(o.opened)o.log("Open Fail: Already opened.");else if(o.isTransitioning())o.log("Open Fail: Is triggering..");else if(!1!==o.emitEvent("beforeOpen")){o.el.classList.add("triggering");var e=function(){o.el.classList.remove("fb-hide"),o.emitEvent("open",n)},t=function(){o.opened=!0,o.el.classList.remove("triggering"),o.emitEvent("afterOpen",n)};if(!c)return e(),void o.constructor.animateCSS(o.el.box,"fb-fadeIn",function(){t()});c(o.el.box,i.animation_open,{duration:i.animation_duration,begin:function(){e(),-1<i.animation_open.indexOf("callout.")&&(o.el.box.style.display="block",o.el.box.style.opacity="1")},complete:function(){t()}})}else o.log("Open aborted")},i.delay)},e.close=function(e){var t=this,n=this.constructor.extend(_extends({},this.options),e);if(this.opened)if(this.isTransitioning())this.log("Close Fail: Is triggering.");else if(!1!==this.emitEvent("beforeClose")){this.el.classList.add("triggering");var o=function(){t.emitEvent("close",e)},i=function(){t.opened=!1,t.el.classList.remove("triggering"),t.emitEvent("afterClose",e),t.el.classList.add("fb-hide")};if(!c)return o(),void this.constructor.animateCSS(this.el.box,"fb-fadeOut",function(){i()});c(this.el.box,n.animation_close,{duration:n.animation_duration,begin:function(){o()},complete:function(){i()}})}else this.log("Close aborted");else this.log("Close Fail: Already closed")},e.getNonceToken=function(){return this.constructor.getWPOptions("nonce")},e.getAJAXURL=function(){return this.constructor.getWPOptions("ajax_url")},e.request=function(t,n){var e={nonce:this.getNonceToken()};t=Object.assign(e,t),n=this.constructor.extend({data:null,onSuccess:null},n);var o=Object.keys(t).map(function(e){return e+"="+t[e]}).join("&"),i=new XMLHttpRequest;i.open("POST",this.getAJAXURL()+"?"+o,!0),i.setRequestHeader("X-Ajax-Engine",this.name),i.onload=function(){var e=i.response;try{e=JSON.parse(e)}catch(e){}200===i.status&&n.onSuccess&&n.onSuccess.call(r,e,i)},i.send()},e.isTransitioning=function(){return this.el.classList.contains("triggering")},e.toggle=function(e){this.opened?this.close(e):this.open(e)},e.unbindTriggers=function(){this.activeTriggers.forEach(function(e){e.hasOwnProperty("unbind")&&e.unbind()}),this.log("Triggers unbound")},e.trigger_onScrollDepth=function(e){var t=this,n=this.constructor.extend(_extends({},this.options),e);return new FireBoxTriggers.onScrollDepth({scroll_depth:n.scroll_depth,scroll_depth_value:n.scroll_depth_value,scroll_dir:n.scroll_dir,once:1==n.firing_frequency,callback:function(){t.open(),n.reverse_scroll_close&&new FireBoxTriggers.onScrollDepth({scroll_depth:n.scroll_depth,scroll_depth_value:n.scroll_depth_value,scroll_dir:"up"==n.scroll_dir?"down":"up",callback:function(){t.close({temporary:!0})}}).bind()}})},e.trigger_onElementVisibility=function(e){var t=this,n=this.constructor.extend(_extends({},this.options),e);return new FireBoxTriggers.onElementVisibility({selector:n.trigger_selector,threshold:n.threshold,once:1==n.firing_frequency,on:function(){t.open()},off:function(){n.close_out_viewport&&t.close({temporary:!0})}})},e.trigger_onHover=function(e){var t=this,n=this.constructor.extend(_extends({},this.options),e);return new FireBoxTriggers.onHover({selector:n.trigger_selector,delay:n.delay,callback:function(){t.open({delay:0})}})},e.trigger_onClick=function(e){var t=this,n=this.constructor.extend(_extends({},this.options),e);return new FireBoxTriggers.onClick({selector:n.trigger_selector,prevent_default:n.prevent_default,callback:function(e){t.triggerElement=e.target,t.open()}})},e.trigger_onExternalLink=function(e){var n=this,t=this.constructor.extend(_extends({},this.options),e);return new FireBoxTriggers.onExternalLink({selector:t.trigger_selector,callback:function(e){n.triggerElement=e,n.open();var t=n.el.querySelector(".fb-btn-continue");t&&("a"!=t.nodeName.toLocaleLowerCase()&&t.children&&Object.values(t.children).forEach(function(e){"a"!=e.nodeName.toLocaleLowerCase()||(t=e)}),t.removeAttribute("href"),t.removeAttribute("target"),t.setAttribute("href",e.getAttribute("href")),null!==e.getAttribute("target")&&t.setAttribute("target",e.getAttribute("target")))}})},e.trigger_onExit=function(e){var t=this,n=this.constructor.extend(_extends({},this.options),e);return this.constructor.isTouchDevice()?new FireBoxTriggers.onScrollDepth({scroll_depth_value:40,scroll_dir:"up",once:!0,callback:function(){t.open()}}):new FireBoxTriggers.onExit({timer:n.exit_timer,aggressive:2==n.firing_frequency,callback:function(){t.open({delay:0})}})},e.trigger_onPageLoad=function(){var e=this;return new FireBoxTriggers.onPageLoad({callback:function(){e.open()}})},e.trigger_onPageReady=function(){var e=this;return new FireBoxTriggers.onPageReady({callback:function(){e.open()}})},e.trigger_onAdBlockDetect=function(){var e=this;return new FireBoxTriggers.onAdBlockDetect({callback:function(){e.open()}})},e.trigger_onIdle=function(e){var t=this,n=this.constructor.extend(_extends({},this.options),e),o=new FireBoxTriggers.onIdle({time:n.idle_time,callback:function(){t.open({delay:0})}});return 2==n.firing_frequency&&this.on("afterClose",function(){o.start()}),o},e.bindTrigger=function(e,t){var n="trigger_"+(e=e||this.options.trigger);if("function"==typeof this[n]){var o=this[n](t);if(!o.hasOwnProperty("bind")){var i="Cannot bind trigger "+e+". ";return o.error&&(i+=o.error),void this.error(i)}o.bind(),this.activeTriggers.push(o),this.log("Bind Trigger: "+e)}else this.log("Trigger not found: "+e)},e.emitEvent=function(e,t){(t=t||{}).instance=this;var n="FireBox"+(e.charAt(0).toUpperCase()+e.slice(1)),o=new CustomEvent(n,{detail:t,cancelable:!0});return s.dispatchEvent(o),this.emit(e,this,t)},e.HTMLAttributes=function(){0<n.length||s.addEventListener("click",function(e){var t=e.target.closest("*[data-fbox]"),n=e.target.closest("*[data-fbox-cmd]");if(t||n){var o=t||n,i=o.dataset&&o.dataset.fbox?o.dataset.fbox:o.closest(".fb-inst")&&o.closest(".fb-inst").dataset.id?o.closest(".fb-inst").dataset.id:null;if(i){var r=u.getInstance(i);if(r){for(var s=o.getAttribute("data-fbox-cmd")||"open",c={},a=0;a<o.attributes.length;a++){var l=o.attributes[a];/^data-fbox-/.test(l.nodeName)&&(c[l.nodeName.replace(/^data-fbox-/,"")]=l.nodeValue)}switch(this.triggerElement=o,s){case"open":r.open(c);break;case"close":r.close(c);break;case"closeKeep":case"hide":console.warn('The "'+s+'" command is deprecated. Please, use "close" command instead.'),r.close(c);break;default:r.toggle(c)}"0"!=o.getAttribute("data-fbox-prevent")&&e.preventDefault()}else console.warn('FireBox with ID "#'+i+'" not found on the page. Make sure it is published.')}}})},e.bindActions=function(){var o=this;this.on("beforeOpen",function(){if(!o.options.test_mode&&o.constructor.getCookie(o.name.toLowerCase()+"_"+o.id))return!1});var e,t=s.querySelector("html"),n=this.options.css_class_prefix;this.options.backdrop&&this.on("open",function(){(e=s.createElement("div")).classList.add(n+"backdrop"),e.style.backgroundColor=o.options.backdrop_color,o.el.appendChild(e),o.constructor.animateCSS(e,n+"fadeIn"),o.options.backdrop_click&&e.addEventListener("click",function(){o.close()})}).on("close",function(){o.constructor.animateCSS(e,n+"fadeOut",function(){e.parentNode.removeChild(e)})}),this.options.disable_page_scroll&&this.on("open",function(){t.classList.add(n+"page_no_scroll")}).on("afterClose",function(){t.classList.remove(n+"page_no_scroll")}),this.HTMLAttributes(),this.on("open",function(){t.classList.add(n+this.id+"-opening")}).on("afterOpen",function(){this.el.classList.add(n+"visible"),t.classList.add(n+this.id+"-open"),t.classList.remove(n+this.id+"-opening"),t.classList.add(n+this.el.dataset.type)}).on("close",function(){t.classList.add(n+this.id+"-closing")}).on("afterClose",function(){this.el.classList.remove(n+"visible"),t.classList.remove(n+this.id+"-open"),t.classList.remove(n+this.id+"-closing"),t.classList.remove(n+this.el.dataset.type)}),this.on("open",function(){o.box_log_id=0;var e={event:"open",action:"firebox_trackevent",box:o.id,referrer:encodeURIComponent(o.constructor.getWPOptions("referrer")),page:encodeURIComponent(r.location.href)};o.request(e,{onSuccess:function(e){e.box_log_id&&(o.box_log_id=e.box_log_id)}})}).on("afterClose",function(e,t){var n={event:"close",action:"firebox_trackevent",box_log_id:o.box_log_id,box:o.id};t&&(delete t.instance,Object.keys(t).forEach(function(e){n["options["+e+"]"]=t[e]})),o.request(n,{onSuccess:function(e){"stop"==e.action&&o.destroy()}})})},e.destroy=function(){return this.off(),this.unbindTriggers(),this.log("Destroyed"),this.el.parentNode.removeChild(this.el),this},e.update=function(){return this.off(),this.unbindTriggers(),this.bindActions(),this.bindTrigger(),this},e.error=function(e){e=this.name+" #"+this.id+": "+e,console.error(e)},e.log=function(e){this.options.debug&&(e=this.name+" #"+this.id+": "+e,console.log(e))},e.getContent=function(){return this.el.querySelector(".fb-content")},u.getCookie=function(e){for(var t=e+"=",n=s.cookie.split(";"),o=0;o<n.length;o++){for(var i=n[o];" "==i.charAt(0);)i=i.substring(1);if(0==i.indexOf(t))return i.substring(t.length,i.length)}return""},u.setCookie=function(e,t,n){var o=new Date;o.setTime(o.getTime()+24*n*60*60*1e3);var i="expires="+o.toUTCString();s.cookie=e+"="+t+";"+i+"; path=/; secure"},u.getInstance=function(t){return n.find(function(e){return e.id==t})},u.closeAll=function(){n.forEach(function(e){e.close()})},u.getInstances=function(t){return void 0===t?n:n.filter(function(e){return e.el.dataset.type==t})},u.getTotalOpened=function(){var t=0;return n.forEach(function(e){e.opened&&t++}),t},u.onReady=function(e){"loading"==s.readyState?s.addEventListener("DOMContentLoaded",e):e()},u.getWPOptions=function(e,t){return void 0===fbox_js_object[e]?t:fbox_js_object[e]},u.throttle=function(o,i){var r=this,s=!1;return function(){if(!s){for(var e=arguments.length,t=new Array(e),n=0;n<e;n++)t[n]=arguments[n];o.apply(r,t),s=!0,setTimeout(function(){s=!1},i)}}},u.isTouchDevice=function(){var e=" -webkit- -moz- -o- -ms- ".split(" ");if("ontouchstart"in r||r.DocumentTouch&&s instanceof DocumentTouch)return!0;var t,n=["(",e.join("touch-enabled),("),"heartz",")"].join("");return t=n,r.matchMedia(t).matches},u.extend=function(e,t){for(var n in t)t.hasOwnProperty(n)&&(e[n]=t[n]);return e},u.animateCSS=function(t,n,o){t.classList.add("fb-animate",n),t.addEventListener("animationend",function e(){t.classList.remove("fb-animate",n),t.removeEventListener("animationend",e),"function"==typeof o&&o()})},e.init=function(){var t=this;this.el.classList.contains(this.options.css_class_prefix+"init")||n.some(function(e){return e.id==t.id})||(this.bindActions(),this.bindTrigger(),this.el.classList.add(this.options.css_class_prefix+"init"),n.push(this),this.el.firebox=this)},u}(function(){function e(){}var t=e.prototype;return t.on=function(e,t){return this._callbacks=this._callbacks||{},this._callbacks[e]||(this._callbacks[e]=[]),this._callbacks[e].push(t),this},t.emit=function(e){this._callbacks=this._callbacks||{};var t=this._callbacks[e];if(t){for(var n=arguments.length,o=new Array(1<n?n-1:0),i=1;i<n;i++)o[i-1]=arguments[i];for(var r,s=_createForOfIteratorHelperLoose(t);!(r=s()).done;){if(0==r.value.apply(this,o))return!1}}return this},t.off=function(e,t){if(!this._callbacks||0===arguments.length)return this._callbacks={},this;var n=this._callbacks[e];if(!n)return this;if(1===arguments.length)return delete this._callbacks[e],this;for(var o=0;o<n.length;o++){if(n[o]===t){n.splice(o,1);break}}return this},e}());r.FireBox=e}(window,document,window.jQuery?window.jQuery.Velocity:window.Velocity),FireBox.onReady(function(){document.querySelectorAll(".fb-inst").forEach(function(e){new FireBox(e)})}),function(){"use strict";document.addEventListener("FireBoxBeforeClose",function(e){var t=e.detail.instance,n=t.getContent();n&&t.on("afterClose",function(){var e=n.querySelectorAll("video, audio");e.length&&e.forEach(function(e){return e.pause()});var t=n.querySelectorAll('iframe[src*="youtube.com"]');t.length&&t.forEach(function(e){return e.contentWindow.postMessage('{"event":"command","func":"stopVideo","args":""}',"*")})})})}(),function(n){"use strict";new(function(){function e(){this.initEvents()}var t=e.prototype;return t.initEvents=function(){n.addEventListener("click",function(e){this.onClick(e)}.bind(this))},t.onClick=function(e){var t,n,o,i,r,s,c,a,l=e.target.closest('[data-fbox-tracking="true"]');l&&((t=!!l.closest(".wp-block-firebox-form")&&l.closest(".wp-block-firebox-form").querySelector(".firebox-block-button-element"))&&t.closest(".wp-block-firebox-button").id===l.closest(".wp-block-firebox-button").id||(o=(n=l.closest(".fb-inst")).dataset.id,i=l.nodeName.toLowerCase(),s=r="",s=l.querySelector("img")?(r="image",l.querySelector("img").src):"img"===i?(r="image",l.src):"a"===i?l.querySelector("img")?(r="image",l.querySelector("img").src):(r=i,l.innerText):(r=l.classList.contains("wp-block-button")||l.classList.contains("wp-block-firebox-button")?"button":i,l.innerText),c="firebox_conversions_tracker","string"==typeof(a=FireBox.getCookie(c)||{})&&(a=JSON.parse(a)||{}),a[o]={box_log_id:n.firebox.box_log_id,source:r,label:s},FireBox.setCookie(c,JSON.stringify(a),999),this.maybeTrackViaAjax(l,a[o])))},t.maybeTrackViaAjax=function(e,t){var n;t.box_log_id&&(e.closest("a")&&e.closest("a").href&&!e.closest("a").href.startsWith("#")||e.querySelector("a")&&e.querySelector("a").href&&!e.querySelector("a").href.startsWith("#")||"close"===e.dataset.fboxCmd||e.querySelector('*[data-fbox-cmd="close"]')||((n=new FormData).append("nonce",FireBox.getWPOptions("nonce")),n.append("action","firebox_trackconversion"),fetch(FireBox.getWPOptions("ajax_url"),{method:"POST",body:n})))},e}())}(document);var FireBox_Accessibility=function(){function e(){window.FireBox&&(this.instances=this.getInstancesSortedByZIndexDesc(),0!==this.instances.length&&(this.lastClickedElement=null,this.initEvents()))}var t=e.prototype;return t.initEvents=function(){var t=this;document.addEventListener("click",function(e){t.lastClickedElement=e.target,t.setFocus(e)}),document.addEventListener("keydown",function(e){t.closePopupOnESC(e)}),document.addEventListener("FireBoxOpen",function(e){e.detail.instance.options.lastFocusableEl=document.activeElement,setTimeout(function(){t.focusOnFirstFocusableElement(e.detail.instance)},20)}),document.addEventListener("FireBoxAfterClose",function(e){t.focusOnLastFocusableElement(e.detail.instance)})},t.setFocus=function(e){var t=e.target.closest(".fb-inst");t&&this.trapFocusInModal(t.firebox)},t.closePopupOnESC=function(e){var t,n;"Escape"!==e.key&&"Esc"!==e.key&&27!==e.keyCode||(n=null,(t=document.activeElement.closest(".fb-inst"))?n=t.firebox:this.lastClickedElement&&this.lastClickedElement.closest(".fb-inst")&&(n=this.lastClickedElement.closest(".fb-inst").firebox),n&&n.options.close_on_esc&&n.close())},t.focusOnFirstFocusableElement=function(e){var t;e.options.auto_focus&&(parseInt(e.el.style.zIndex||0)<this.getHighestZIndex()||((t=this.getFocusableElements(e.el)).length&&this.getFirstFocusableElement(t).focus(),this.trapFocusInModal(e)))},t.trapFocusInModal=function(e){var t,n,o=this.getFocusableElements(e.el);o.length&&(t=o[0],n=o[o.length-1],e.el.addEventListener("keydown",function(e){"Tab"!==e.key&&9!==e.keyCode||(e.shiftKey?document.activeElement===t&&(n.focus(),e.preventDefault()):document.activeElement===n&&(t.focus(),e.preventDefault()))}))},t.focusOnLastFocusableElement=function(e){e.options.lastFocusableEl&&e.options.lastFocusableEl.focus()},t.getFirstFocusableElement=function(e){if(e.length){var t=e[0];return t.closest("button.fb-close")&&e[1]&&(t=e[1]),t}},t.getFocusableElements=function(e){for(var t=e.querySelector(".fb-content"),n=Array.from(t.querySelectorAll('button:not([disabled]), a.firebox-block-button-element, input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')),o=[],i=0;i<n.length;i++)null!==n[i].offsetParent&&o.push(n[i]);return e.querySelector(".fb-close")&&o.unshift(e.querySelector(".fb-close")),o},t.getInstancesSortedByZIndexDesc=function(){var e=FireBox.getInstances();return 0===e.length?[]:e.sort(function(e,t){return(parseInt(t.el.style.zIndex)||99999)-(parseInt(e.el.style.zIndex)||99999)})},t.getHighestZIndex=function(){return parseInt(this.instances[0].el.style.zIndex||0)},e}();FireBox.onReady(function(){new FireBox_Accessibility});
