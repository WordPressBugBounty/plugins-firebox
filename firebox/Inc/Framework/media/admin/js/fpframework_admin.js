var FPFrameworkAdmin=function(){this.ui=new FPF_UI,this.cpt_helper=new FPF_CPT_Helper,this.conditionize=new FPF_Conditionize,this.tabs_state=new FPF_Tabs_State};document.addEventListener("DOMContentLoaded",function(e){new FPFrameworkAdmin});var FPF_Animation=function(){function e(){}return e.slideUp=function(e,t){void 0===t&&(t=500),e.style.transitionProperty="height, margin, padding",e.style.transitionDuration=t+"ms",e.style.boxSizing="border-box",e.style.height=e.offsetHeight+"px",e.offsetHeight,e.style.overflow="hidden",e.style.height=0,e.style.paddingTop=0,e.style.paddingBottom=0,e.style.marginTop=0,e.style.marginBottom=0,window.setTimeout(function(){e.style.display="none",e.style.removeProperty("height"),e.style.removeProperty("padding-top"),e.style.removeProperty("padding-bottom"),e.style.removeProperty("margin-top"),e.style.removeProperty("margin-bottom"),e.style.removeProperty("overflow"),e.style.removeProperty("transition-duration"),e.style.removeProperty("transition-property")},t)},e.slideDown=function(e,t){void 0===t&&(t=500),e.style.removeProperty("display");var i=window.getComputedStyle(e).display;"none"===i&&(i="block"),e.style.display=i;var n=e.offsetHeight;e.style.overflow="hidden",e.style.height=0,e.style.paddingTop=0,e.style.paddingBottom=0,e.style.marginTop=0,e.style.marginBottom=0,e.offsetHeight,e.style.boxSizing="border-box",e.style.transitionProperty="height, margin, padding",e.style.transitionDuration=t+"ms",e.style.height=n+"px",e.style.removeProperty("padding-top"),e.style.removeProperty("padding-bottom"),e.style.removeProperty("margin-top"),e.style.removeProperty("margin-bottom"),window.setTimeout(function(){e.style.removeProperty("height"),e.style.removeProperty("overflow"),e.style.removeProperty("transition-duration"),e.style.removeProperty("transition-property")},t)},e.slideToggle=function(e,t){return void 0===t&&(t=500),"none"===window.getComputedStyle(e).display?this.slideDown(e,t):this.slideUp(e,t)},e}(),FPF_Conditionize=function(){function e(e){var s=this,a=this;this.container=e||document,this.fields={},this.showonFields=[].slice.call(this.container.querySelectorAll("[data-showon]")),this.showonFields.length&&(this.showonFields.forEach(function(i){if(!i.hasAttribute("data-showon-initialised")){i.setAttribute("data-showon-initialised","");var n,e=i.getAttribute("data-showon")||"",o=JSON.parse(e);o.length&&(n=[].slice.call(a.container.querySelectorAll('[name="'+o[0].field+'"], [name="'+o[0].field+'[]"]')),s.fields[o[0].field]||(s.fields[o[0].field]={origin:[],targets:[]}),n.forEach(function(e){-1===s.fields[o[0].field].origin.indexOf(e)&&s.fields[o[0].field].origin.push(e)}),s.fields[o[0].field].targets.push(i),1<o.length&&o.forEach(function(e,t){0!==t&&(n=[].slice.call(a.container.querySelectorAll('[name="'+e.field+'"], [name="'+e.field+'[]"]')),s.fields[o[0].field]||(s.fields[o[0].field]={origin:[],targets:[]}),n.forEach(function(e){-1===s.fields[o[0].field].origin.indexOf(e)&&s.fields[o[0].field].origin.push(e)}),-1===s.fields[o[0].field].targets.indexOf(i)&&s.fields[o[0].field].targets.push(i))}))}}),this.linkedOptions=this.linkedOptions.bind(this),Object.keys(this.fields).forEach(function(t){s.fields[t].origin.length&&s.fields[t].origin.forEach(function(e){a.linkedOptions(t),e.addEventListener("change",function(){a.linkedOptions(t)}),e.addEventListener("keyup",function(){a.linkedOptions(t)}),e.addEventListener("click",function(){a.linkedOptions(t)})})}))}return e.prototype.linkedOptions=function(a){var r=this;this.fields[a].targets.forEach(function(e){var n,o=JSON.parse(e.getAttribute("data-showon"))||[],s=!0;o.forEach(function(t,e){var i=t||{};i.valid=0,r.fields[a].origin.forEach(function(e){e.name.replace("[]","")===t.field&&(e.getAttribute("type")&&["checkbox","radio"].includes(e.getAttribute("type").toLowerCase())?n=e.checked?e.value:"":"SELECT"===e.nodeName&&e.hasAttribute("multiple")?n=Array.from(e.querySelectorAll("option:checked")).map(function(e){return e.value}):e.parentElement&&e.parentElement.classList.contains("fptoggle")?n=e.checked?e.value:0:null===(n=e.value)&&"select"===e.tagName.toLowerCase()&&(n=[]),"object"!=typeof n&&(n=JSON.parse('["'+n+'"]')),n.forEach(function(e){"="===i.sign&&-1!==i.values.indexOf(e)&&(i.valid=1),"!="===i.sign&&-1===i.values.indexOf(e)&&(i.valid=1)}))}),""===i.op?0===i.valid&&(s=!1):("AND"===i.op&&i.valid+o[e-1].valid<2&&(s=!1,i.valid=0),"OR"===i.op&&0<i.valid+o[e-1].valid&&(s=!0,i.valid=1))}),"option"!==e.tagName?s?(e.classList.remove("hidden"),e.dispatchEvent(new CustomEvent("fpf:showon-show",{bubbles:!0}))):(e.classList.add("hidden"),e.dispatchEvent(new CustomEvent("fpf:showon-hide",{bubbles:!0}))):e.disabled=!s})},e}(),FPF_Cookie=function(){function e(){}return e.set=function(e,t,i){var n="";if(i){var o=new Date;o.setTime(o.getTime()+24*i*60*60*1e3),n="; expires="+o.toUTCString()}document.cookie=e+"="+(t||"")+n+"; path=/"},e.get=function(e){for(var t=e+"=",i=document.cookie.split(";"),n=0;n<i.length;n++){for(var o=i[n];" "==o.charAt(0);)o=o.substring(1,o.length);if(0==o.indexOf(t))return o.substring(t.length,o.length)}return null},e.remove=function(e){document.cookie=e+"=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;"},e}(),FPF_CPT_Helper=function(){function e(){this.initEvents()}var t=e.prototype;return t.initEvents=function(){document.addEventListener("click",function(e){this.handlePostTypeStatusChange(e)}.bind(this))},t.handlePostTypeStatusChange=function(e){var t=e.target.closest(".fpf-toggle-post-status");if(t){t.classList.add("disabled");var i=t.getAttribute("data-post-id"),n=t.checked?"publish":"draft";fetch(fpf_js_object.base_url+"/?rest_route=/fpframework/v1/cpt/"+i+"/"+n,{method:"POST",headers:{"X-WP-Nonce":fpf_js_object.wp_rest_nonce}}).then(function(e){return e.json()}).then(function(e){t.classList.remove("disabled")})}},e}(),FPF_FieldTooltipPosition=function(){function e(){this.labelClass=".fpf-tooltip-item",this.init()}return e.prototype.init=function(){document.addEventListener("mouseover",function(e){var t=e.target.closest(this.labelClass);if(t){var i=(t=t.querySelector(".fpf-tooltip")).getBoundingClientRect(),n=t.parentElement.getBoundingClientRect(),o=window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight;n.top<=120||i.height/2>n.top?t.classList.add("pos-bottom"):n.top+120>o||n.top+120+i.height/2>o?t.classList.add("pos-top"):(t.classList.remove("pos-top"),t.classList.remove("pos-bottom"))}}.bind(this))},e}(),FPF_Helper=function(){function e(){}return e.popupWindow=function(e,t,i,n){var o=null!=window.screenLeft?window.screenLeft:window.screenX,s=null!=window.screenTop?window.screenTop:window.screenY,a=window.innerWidth?window.innerWidth:document.documentElement.clientWidth?document.documentElement.clientWidth:screen.width,r=window.innerHeight?window.innerHeight:document.documentElement.clientHeight?document.documentElement.clientHeight:screen.height,l=a/window.screen.availWidth,d=(a-i)/2/l+o,c=(r-n)/2/l+s,f=window.open(e,t,"scrollbars=yes, width="+i/l+", height="+n/l+", top="+c+", left="+d);window.focus&&f.focus()},e.getQueryString=function(e){var t=window.location.search;return new URLSearchParams(t).get(e)},e.number_format=function(e,t,i,n){if(null==e||!isFinite(e))throw new TypeError("Number is not valid");if(!t){var o=e.toString().split(".").length;t=1<o?o:0}i=i||".",n=n||",";var s=(e=(e=parseFloat(e).toFixed(t)).replace(".",i)).split(i);return s[0]=s[0].replace(/\B(?=(\d{3})+(?!\d))/g,n),e=s.join(i)},e.loadScript=function(e,t,i){var n;if(void 0===i&&(i=!1),0<(n=document.querySelectorAll('script[src^="'+e+'"]')).length){if(!i)return void("function"==typeof t&&t());n.forEach(function(e){e.remove()})}(n=document.createElement("script")).src=e,document.head.appendChild(n),"function"==typeof t&&(n.onload=function(){t()})},e.loadStyleSheet=function(e,t){if(0<document.querySelectorAll('link[href^="'+e+'"]').length)"function"==typeof t&&t();else{var i=document.createElement("link");i.href=e,i.type="text/css",i.rel="stylesheet",document.getElementsByTagName("head")[0].prepend(i)}},e.onReady=function(e){"loading"==document.readyState?document.addEventListener("DOMContentLoaded",e):e()},e}(),FPF_Tabs=function(){function e(){}var t=e.prototype;return t.init=function(){this.initEvents(),this.initMobileToggleLabel()},t.initEvents=function(){document.addEventListener("click",function(e){this.initNavigation(e),this.initMobileToggle(e),this.initMobileCloseOnDocumentClick(e)}.bind(this))},t.initNavigation=function(e){var t=e.target.closest(".fpf-tabs-nav-item");if(t){var i=t.closest(".fpf-tabs-wrapper"),n=t.getAttribute("id");tab_name=n.replace("fpf-tab-",""),this.selectTab(i,tab_name),e.preventDefault()}},t.selectTab=function(e,t){if(this.getTabNavItem(e,t)&&(this.clearTabsSelection(e),this.changeSelection(e,t),"function"==typeof FPF_Textarea)){var i=e.querySelector('.fpf-tab-item[id$="'+t+'"]');new FPF_Textarea(i).init()}},t.initMobileToggleLabel=function(){var e=document.querySelectorAll(".fpf-tabs-wrapper");e&&e.forEach(function(e){var t=e.querySelector(".fpf-tabs-nav-mobile-toggle span.text-outer");if(t){var i=e.querySelector(".fpf-tabs-nav .fpf-tabs"),n=i.querySelector("li.is-active");t.innerHTML=n?n.querySelector(".text").innerHTML:i.children[0].querySelector(".text").innerHTML}})},t.initMobileToggle=function(e){var t=e.target.closest(".fpf-tabs-nav-mobile-toggle");if(t){var i=t.parentNode;i.classList.contains("mobile-mode")?i.classList.remove("mobile-mode"):i.classList.add("mobile-mode"),e.preventDefault()}},t.initMobileCloseOnDocumentClick=function(e){if(!e.target.closest(".fpf-tabs-nav")&&!e.target.closest(".fpf-tabs-nav-mobile-toggle")){var t=document.querySelector(".fpf-tabs-nav-wrapper.mobile-mode");t&&t.classList.remove("mobile-mode")}},t.clearTabsSelection=function(e){var t=e.querySelector(".fpf-tabs-nav-item.is-active");t&&t.classList.remove("is-active");var i=e.querySelector(".fpf-tab-item.is-active");i&&i.classList.remove("is-active")},t.changeSelection=function(e,t){var i=this.getTabNavItem(e,t);i.classList.add("is-active"),e.querySelector('.fpf-tab-item[id$="'+t+'"]').classList.add("is-active"),e.querySelector(".fpf-tabs-nav-mobile-toggle")&&(e.querySelector(".fpf-tabs-nav-mobile-toggle .text-outer").innerHTML=i.innerHTML),e.querySelector(".fpf-tabs-nav-wrapper.mobile-mode")&&e.querySelector(".fpf-tabs-nav-wrapper.mobile-mode").classList.remove("mobile-mode")},t.getTabNavItem=function(e,t){return e.querySelector('.fpf-tabs-nav-item[id$="'+t+'"]')},e}(),FPF_Tabs_State=function(){function e(){this.tabsClass=".fpf-tabs-wrapper",this.tabNavItemClass=".fpf-tabs-nav-item",this.tabs_elements=document.querySelectorAll(this.tabsClass),this.tabs=new FPF_Tabs,this.init()}var t=e.prototype;return t.init=function(){this.tabs_elements.length&&(this.detectTabStateAndUpdateTabs(),this.initEvents())},t.initEvents=function(){document.addEventListener("click",function(e){this.setNewTabState(e)}.bind(this)),window.addEventListener("hashchange",function(){this.detectTabStateAndUpdateTabs()}.bind(this))},t.setNewTabState=function(e){var t=e.target.closest(this.tabNavItemClass);if(t){var i=t.getAttribute("id");i=i.replace("fpf-tab-","");var n=t.closest(".fpf-tabs-wrapper").getAttribute("id");null!=n&&FPF_Cookie.set(n,i)}},t.detectTabStateAndUpdateTabs=function(){var t=this,i=window.location.hash;i=i.replace("#",""),this.tabs_elements.forEach(function(e){t.tabs.selectTab(e,i)})},e}(),FPF_UI=function(){function e(){this.tabs=new FPF_Tabs,this.tabs.init(),this.fieldTooltipPosition=new FPF_FieldTooltipPosition,this.maybeSetThemeBodyClass(),this.maybeSetAdminSidebarBodyClass(),this.initEvents()}var t=e.prototype;return t.maybeSetThemeBodyClass=function(){if(document.querySelector(".fpframework-toggle-theme")){"dark"===(FPF_Cookie.get("fireplugins_theme")||"light")?document.body.classList.add("dark"):document.body.classList.remove("dark")}},t.maybeSetAdminSidebarBodyClass=function(){if(document.querySelector(".fpf-admin-sidebar-toggle")){"shrink"===(FPF_Cookie.get("fireplugins_sidebar_state")||"expand")?document.body.classList.add("fpf-admin-sidebar-shrink"):document.body.classList.remove("fpf-admin-sidebar-shrink")}},t.initEvents=function(){document.addEventListener("click",function(e){this.onNoticeClose(e),this.toggleTheme(e),this.toggleSidebar(e)}.bind(this))},t.onNoticeClose=function(e){var t=e.target.closest(".fpf-notice-close-btn");t&&(e.preventDefault(),t.parentElement.remove())},t.toggleTheme=function(e){if(e.target.closest(".fpframework-toggle-theme")){e.preventDefault();var t="fireplugins_theme",i="light"===(FPF_Cookie.get(t)||"light")?"dark":"light";FPF_Cookie.set(t,i,99999),document.body.classList.remove("light"),document.body.classList.remove("dark"),document.body.classList.add(i);var n=new Event("fireplugins/theme/change");window.dispatchEvent(n,{detail:i})}},t.toggleSidebar=function(e){if(e.target.closest(".fpf-admin-sidebar-toggle")){e.preventDefault();var t="fireplugins_sidebar_state",i="expand"===(FPF_Cookie.get(t)||"expand")?"shrink":"expand";FPF_Cookie.set(t,i,99999),document.body.classList.remove("fpf-admin-sidebar-expand"),document.body.classList.remove("fpf-admin-sidebar-shrink"),document.body.classList.add("fpf-admin-sidebar-"+i)}},e}();

