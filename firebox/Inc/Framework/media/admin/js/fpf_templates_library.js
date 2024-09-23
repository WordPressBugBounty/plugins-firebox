var FPF_Templates_Library_Filters=function(){function e(e){this.instance=e,this.filtersPillsClass=".fpf-library-selected-filters-pills",this.filterItemClass=".fpf-library-filter-item",this.initEvents()}var t=e.prototype;return t.initEvents=function(){document.addEventListener("click",function(e){this.initFilterItemToggle(e),this.onClearAllFilters(e),this.onRemoveFilterPillItem(e)}.bind(this)),document.addEventListener("change",function(e){this.onFilterItemSelect(e)}.bind(this))},t.initFilterItemToggle=function(e){var t=e.target.closest(".fpf-library-filter-item-label");t&&(e.preventDefault(),t.closest(this.filterItemClass).classList.toggle("open"))},t.onFilterItemSelect=function(e){e.target.closest(".fpf-library-filter-choice-item-checkbox")&&this.update()},t.update=function(){var e=this.instance.filters_sidebar.querySelectorAll(".fpf-library-filter-item"),l=[];e.forEach(function(t){var e=t.querySelectorAll('input[type="checkbox"]:checked');l[t.dataset.type]=[],e.forEach(function(e){l[t.dataset.type].push(e.value)})});var o=Object.values(l).every(function(e){return 0===e.length});o?(this.setNoFilters(),l=[]):this.setHasFilters(),l&&this.instance.items.getCurrentItems().forEach(function(e){var t=e.dataset.filterCategory||"",r=e.dataset.filterSolution||"",i=e.dataset.filterEvent||"",s=e.dataset.filterCompatibility||"",a=e.dataset.filterTags||"",n=!!o;for(key in l)if(0!==l[key].length){if("category"===key&&(""===t&&(n=!1),l[key].forEach(function(e){t.includes(e)&&(n=!0)}),!n))break;if("events"===key&&0<l[key].length&&(""===i&&(n=!1),l[key].forEach(function(e){i.includes(e)&&(n=!0)}),!n))break;if("solution"===key&&(""===r&&(n=!1),l[key].forEach(function(e){r.includes(e)&&(n=!0)}),!n))break;if("compatibility"===key&&(""===s&&(n=!1),!(n=l[key].includes(s))))break;if("tags"===key){for(tag in""===a&&(n=!1),l[key]){if(-1!==a.indexOf(l[key][tag])){n=!0;break}n=!1}if(!n)break}}n?e.classList.remove("is-hidden"):e.classList.add("is-hidden")}),this.instance.search.search(),this.instance.sorting.sort()},t.onUpdateSearchElements=function(){var r=this;this.emptyFilterPills();var i=this.getFilterPillsWrapper();this.instance.filters_sidebar.querySelectorAll('input[type="checkbox"]:checked').forEach(function(e){var t=r.instance.library_wrapper.querySelector(".fpf-library-filter-template").cloneNode(!0);t.querySelector(".filter").dataset.filter=e.value,t.querySelector(".filter .filter-label").innerHTML=e.value,i.appendChild(t.children[0])})},t.emptyFilterPills=function(){this.getFilterPillsWrapper().innerHTML=""},t.getFilterPillsWrapper=function(){return this.instance.library_wrapper.querySelector(this.filtersPillsClass)},t.onClearAllFilters=function(e){e.target.closest(".fpf-library-filters-clear-all")&&(e.preventDefault(),this.instance.library_toolbar.querySelector(".fpf-library-search input").value="",this.instance.filters_sidebar.querySelectorAll('input[type="checkbox"]:checked').forEach(function(e){e.checked=!1}),this.update())},t.onRemoveFilterPillItem=function(e){var t=e.target.closest(".fpf-library-filter-pill-item-remove");if(t){e.preventDefault();var r=t.closest(".filter").dataset.filter;this.instance.filters_sidebar.querySelectorAll('input[type="checkbox"][value="'+r+'"]:checked').forEach(function(e){e.checked=!1}),this.update()}},t.setHasFilters=function(){this.instance.library_wrapper.classList.add("has-filters")},t.setNoFilters=function(){this.instance.library_wrapper.classList.remove("has-filters")},e}(),FPF_Templates_Info_Modal=function(){function e(e){this.instance=e,this.modal=document.querySelector("#fpf-library-item-info-popup"),this.initEvents()}var t=e.prototype;return t.initEvents=function(){this.modal.addEventListener("fpf_modal_open",function(e){this.onBeforeOpen(e)}.bind(this))},t.onBeforeOpen=function(e){var r=this,t=e.detail.initiator,i=this.instance.getTemplateItem(t),s=i.querySelector(".info-popup-actions"),a=this.modal.querySelector(".dependency-items");a.innerHTML="";var n=JSON.parse(i.dataset.capabilities);this.modal.querySelector(".modal-title").innerText=i.querySelector(".template-label").innerHTML,this.modal.querySelector(".item-description").innerHTML=i.dataset.note,this.modal.querySelector(".template-details").querySelector(".cell.category .content").innerText=n.category.value,n.event.value&&""!==n.event.value.trim()?(this.modal.querySelector(".template-details").querySelector(".cell.event").classList.remove("is-hidden"),this.modal.querySelector(".template-details").querySelector(".cell.event .content").innerText=n.event.value):this.modal.querySelector(".template-details").querySelector(".cell.event").classList.add("is-hidden"),this.modal.querySelector(".template-details").querySelector(".cell.solution .content").innerText=n.solution.value;var l=this.modal.querySelector(".dependency-item.template").cloneNode(!0);l.classList.remove("template"),l.querySelector(".requirement").innerHTML=fpf_templates_library_js_object.plugin_name+" "+("pro"===n.pro.requirement?fpf_templates_library_js_object.pro:fpf_templates_library_js_object.lite),l.querySelector(".detected").innerHTML=fpf_templates_library_js_object.plugin_name+" "+fpf_templates_library_js_object[n.pro.detected],"pro"===n.pro.requirement&&"pro"!==n.pro.detected?l.querySelector(".value").appendChild(s.querySelector("a.pro").cloneNode(!0)):l.classList.add("pass"),a.appendChild(l),(l=this.modal.querySelector(".dependency-item.template").cloneNode(!0)).classList.remove("template"),l.querySelector(".requirement").innerHTML=fpf_templates_library_js_object.wordpress+" "+n.wordpress.value+"+",l.querySelector(".detected").innerHTML=fpf_templates_library_js_object.wordpress+" "+n.wordpress.detected,""!==n.wordpress.icon?l.querySelector(".value").appendChild(s.querySelector("a.wordpress").cloneNode(!0)):l.classList.add("pass"),a.appendChild(l),(l=this.modal.querySelector(".dependency-item.template").cloneNode(!0)).classList.remove("template"),l.querySelector(".requirement").innerHTML=fpf_templates_library_js_object.plugin_name+" "+n.plugin.value+"+",l.querySelector(".detected").innerHTML=fpf_templates_library_js_object.plugin_name+" "+n.plugin.detected,""!==n.plugin.icon?(l.querySelector(".value").appendChild(s.querySelector("a.plugin").cloneNode(!0)),l.querySelector(".value a .label").innerHTML=fpf_templates_library_js_object.update_plugin):l.classList.add("pass"),a.appendChild(l),n.third_party_dependencies.value&&n.third_party_dependencies.value.forEach(function(e,t){(l=r.modal.querySelector(".dependency-item.template").cloneNode(!0)).classList.remove("template"),l.querySelector(".requirement").innerHTML=e.name+" "+e.version+"+",l.querySelector(".detected").innerHTML="none"===e.detected?"-":e.name+" "+e.detected,e.valid?l.classList.add("pass"):(l.querySelector(".value").appendChild(s.querySelector("a.third_party_dependencies_"+t).cloneNode(!0)),l.querySelector(".value a .label").innerHTML="update"===e.icon?fpf_templates_library_js_object.update_plugin:"activate"===e.icon?fpf_templates_library_js_object.activate_plugin:fpf_templates_library_js_object.install_plugin),a.appendChild(l)}),""!==n.license_error.value&&((l=this.modal.querySelector(".dependency-item.template").cloneNode(!0)).classList.remove("template"),l.querySelector(".requirement").innerHTML=fpf_templates_library_js_object.license_key,l.querySelector(".detected").innerHTML="missing"===n.license_error.value?"-":fpf_templates_library_js_object.license,l.querySelector(".value").appendChild(s.querySelector("a.license").cloneNode(!0)),a.appendChild(l))},e}(),FPF_Templates_Library_Items=function(){function e(e){this.instance=e,this.initEvents()}var t=e.prototype;return t.initEvents=function(){document.addEventListener("click",function(e){this.onFavorite(e),this.onInsert(e)}.bind(this))},t.onFavorite=function(e){var i=e.target.closest(".fpf-library-favorite-item");if(i){e.preventDefault(),i.classList.add("working");var s=e.target.closest(".fpf-library-item").dataset.id,a=this,t=new FormData;t.append("nonce",fpf_js_object.nonce),t.append("action","fpf_library_favorites_toggle"),t.append("template_id",s);var n=new XMLHttpRequest;n.open("POST",fpf_js_object.ajax_url),n.onload=function(){if(200<=n.status&&n.status<300){var e=JSON.parse(n.response),t=Object.keys(e).includes(s),r=document.querySelector(".fpf-library-item[data-id='"+s+"'] .fpf-library-favorite-item");i.classList.remove("working"),t?r.classList.add("active"):(r.classList.remove("active"),a.instance.filters.update())}},n.send(t)}},t.onInsert=function(e){var t=e.target.closest(".fpf-library-item-insert-btn");if(t){var r=t.dataset.templateId;if(r){e.preventDefault();var i=t.closest(".fpf-library-item");document.body.classList.add("fpf-templates-library-inserting"),i&&i.classList.add("inserting-template"),this.instance.hideMessageAlert(),t.classList.add("working");var s=this.instance.previewModal.classList.contains("is-visible"),a=this,n=new FormData;n.append("nonce",fpf_js_object.nonce),n.append("template",r),n.append("action","fpf_library_insert_template"),fetch(fpf_js_object.ajax_url,{method:"post",body:n}).then(function(e){return e.json()}).then(function(e){e.error?s?(a.instance.showMessageAlert(e.message),a.instance.modal.querySelector(".fpf-library-body").scrollTo({top:0,behavior:"smooth"}),FPF_Modal.closeModal(a.instance.previewModal)):a.instance.showTemplateMessage(i,e.message):window.location.href=e.redirect,document.body.classList.remove("fpf-templates-library-inserting"),i&&i.classList.remove("inserting-template"),t.classList.remove("working")})}}},t.getCurrentItems=function(e){void 0===e&&(e=!1);var t=".fpf-library-list > .fpf-library-item:not(.blank_popup):not(.ignore)";return e&&(t+=":not(.is-hidden)"),this.instance.library_wrapper.querySelectorAll(t)},e}(),FPF_Templates_Library=function(){function e(){this.modal=document.querySelector(".fpf-templates-library"),this.library_wrapper=document.querySelector(".fpf-library-page"),this.library_noresults=document.querySelector(".fpf-library-no-results"),this.library_list=document.querySelector(".fpf-library-list"),this.library_toolbar=this.library_wrapper.querySelector(".fpf-library-toolbar"),this.library_messages=this.library_wrapper.querySelector(".fpf-library-messages"),this.sidebar_element=this.library_wrapper.querySelector(".fpf-library-sidebar"),this.filters_sidebar=this.sidebar_element.querySelector(".fpf-library-sidebar-filters"),this.previewModal=document.querySelector(".fpf-templates-library-popup-preview"),this.items=new FPF_Templates_Library_Items(this),this.toolbar=new FPF_Templates_Library_Toolbar(this),this.filters=new FPF_Templates_Library_Filters(this),this.search=new FPF_Templates_Library_Search(this),this.sidebar=new FPF_Templates_Library_Sidebar(this),this.info_modal=new FPF_Templates_Info_Modal(this),this.sorting=new FPF_Templates_Library_Sorting(this),this.preview_modal=new FPF_Templates_Preview_Modal(this),this.initEvents()}var t=e.prototype;return t.initEvents=function(){document.addEventListener("click",function(e){this.handleRefreshTemplates(e),this.onLibraryOpen(e),this.onMessageHide(e),this.toggleFullscreenMode(e)}.bind(this))},t.onMessageHide=function(e){var t=e.target.closest(".fpf-library-messages-hide-btn");t&&(t.closest(".fpf-alert").classList.add("is-hidden"),t.closest(".fpf-alert").querySelector("span.text").innerHTML="")},t.toggleFullscreenMode=function(e){var t=e.target.closest(".fpf-templates-library-toggle-fullscreen");t&&(e.preventDefault(),this.modal.classList.toggle("fullscreen"),t.classList.toggle("fullscreen"))},t.onLibraryOpen=function(e){var t=this.getLibraryOpenerElement(e);if(t){var r=document.querySelector(".fpf-templates-library > .modal");FPF_Modal.open(r,t),this.loadTemplates(),e.preventDefault()}},t.getLibraryOpenerElement=function(e){return e.target.closest(".page-title-action")?e.target.closest(".page-title-action"):!!e.target.closest(".fpf-open-library-modal")&&e.target.closest(".fpf-open-library-modal")},t.loadTemplates=function(){if(this.library_wrapper.classList.contains("loaded"))return!1;var t=this.modal.querySelector(".fpf-templates-refresh-btn");t.classList.add("working"),this.resetTemplatesLayout();var r=this,e=new FormData;e.append("nonce",fpf_js_object.nonce),e.append("action","fpf_library_get_templates"),fetch(fpf_js_object.ajax_url,{method:"post",body:e}).then(function(e){return e.json()}).then(function(e){r.updateLibraryLayout(e),t.classList.remove("working")})},t.handleRefreshTemplates=function(e){var t=e.target.closest(".fpf-templates-refresh-btn");if(t){var r=this;t.classList.add("working"),this.resetTemplatesLayout();var i=new FormData;i.append("nonce",fpf_js_object.nonce),i.append("action","fpf_library_refresh_templates"),fetch(fpf_js_object.ajax_url,{method:"post",body:i}).then(function(e){return e.json()}).then(function(e){r.updateLibraryLayout(e),t.classList.remove("working"),e.code||e.message||(t.classList.add("checkmark"),setTimeout(function(){t.classList.remove("checkmark")},2e3))}),e.preventDefault()}},t.updateLibraryLayout=function(e){var t="";if(e){if(e.code&&e.message)t=e.message;else if(e.errors)for(var r in e.errors)if(e.errors[r])for(var i in e.errors[r])t+="<p>"+e.errors[r][i]+"</p>"}else t="Cannot load templates";if(t)return this.showMessageAlert(t),void this.modal.querySelector(".fpf-library-body").scrollTo({top:0,behavior:"smooth"});this.library_list.querySelectorAll(".fpf-library-item:not(.blank_popup)").forEach(function(e){e.remove()}),this.library_wrapper.classList.add("loaded"),this.library_list.innerHTML+=e.templates,e.filters&&(this.filters_sidebar.innerHTML=e.filters),this.filters.update()},t.resetTemplatesLayout=function(){this.hideMessageAlert(),document.querySelector(".fpf-library-list").classList.remove("is-hidden"),document.querySelector(".fpf-library-no-results").classList.remove("is-visible")},t.showMessageAlert=function(e){var t=document.querySelector(".fpf-open-library-modal");t&&t.click(),e&&(this.library_messages.querySelector(".fpf-library-messages-text").innerHTML=e),this.library_messages.classList.remove("is-hidden")},t.hideMessageAlert=function(){this.library_messages.querySelector(".fpf-library-messages-text").innerHTML="",this.library_messages.classList.add("is-hidden")},t.showTemplateMessage=function(e,t){var r=e.querySelector(".fpf-template-item-message");t&&(r.querySelector(".fpf-template-item-message-text").innerHTML=t),r.classList.remove("is-hidden")},t.getTemplateItem=function(e){var t=e.dataset.templateId;if(t){var r=this.library_list.querySelector('.fpf-library-item[data-id="'+t+'"]');if(r)return r}return e.closest(".fpf-library-item")},e}();document.addEventListener("DOMContentLoaded",function(){new FPF_Templates_Library});var FPF_Templates_Preview_Modal=function(){function e(e){this.instance=e,this.previewing=!1,this.modal=document.querySelector("#fpf-library-preview-popup"),this.initEvents()}var t=e.prototype;return t.initEvents=function(){window.addEventListener("hashchange",function(e){"#templates-library-previewer"===window.location.hash&&(this.previewing=!0),this.previewing&&""===window.location.hash&&FPF_Modal.closeModal(this.modal.parentElement)}.bind(this)),this.modal.addEventListener("fpf_modal_open",function(e){this.onBeforeOpen(e)}.bind(this)),this.modal.addEventListener("fpf_modal_close",function(e){this.onBeforeClose()}.bind(this)),document.addEventListener("click",function(e){this.toggleResponsiveViewport(e),this.refreshDemo(e)}.bind(this))},t.onBeforeOpen=function(e){var t=e.detail.initiator,r=this.instance.getTemplateItem(t);this.modal.querySelector(".modal-title").innerText=r.querySelector(".template-label").innerHTML;var i=!!r.querySelector(".fpf-library-template-item-info")&&r.querySelector(".fpf-library-template-item-info").cloneNode(!0);this.modal.querySelector(".modal-header .fpf-library-template-item-info")&&this.modal.querySelector(".modal-header .fpf-library-template-item-info").remove(),i&&(r.classList.contains("has-errors")&&i.classList.add("has-errors"),this.modal.querySelector(".modal-header .modal-title-wrapper").appendChild(i));var s=this.instance.library_wrapper.dataset.previewUrl;if(s=s.replace("TEMPLATE_ID",r.dataset.id),this.modal.querySelector("iframe")){var a=this.modal.querySelector("iframe").cloneNode();a.src=s,this.modal.querySelector("iframe").parentNode.replaceChild(a,this.modal.querySelector("iframe"))}else{var n=document.createElement("iframe");n.className="fpf-library-preview-iframe",n.src=s,this.modal.querySelector(".fpf-library-preview-inner").appendChild(n)}this.modal.querySelector("iframe").addEventListener("load",this.onIframeLoaded.bind(this),!0),this.setViewport("desktop");var l=r.querySelector(".fpf-library-item-actions a").cloneNode(!0);this.modal.querySelector(".modal-header .fpf-library-preview-action")&&this.modal.querySelector(".modal-header .fpf-library-preview-action").remove(),l.classList.add("fpf-library-preview-action","fpf-button","outline"),l.classList.contains("red")||(r.classList.contains("has-errors")?l.classList.add("orange"):l.classList.add("blue")),this.modal.querySelector(".modal-header .actions-wrapper").insertBefore(l,this.modal.querySelector(".modal-header .actions-wrapper").firstChild)},t.onIframeLoaded=function(){this.modal.classList.remove("refreshing")},t.onBeforeClose=function(){this.previewing=!1,window.location.hash="";var e=this.getIFrame();e&&(e.removeEventListener("load",this.onIframeLoaded),e.src="")},t.refreshDemo=function(e){if(e.target.closest(".fpf-templates-library-refresh-demo")){e.preventDefault(),this.modal.querySelector("iframe").removeEventListener("load",this.onIframeLoaded),this.modal.classList.add("refreshing");var t=this.getIFrame(),r=t.cloneNode();r.addEventListener("load",this.onIframeLoaded.bind(this),!0),r.src=t.src,t.parentNode.replaceChild(r,t)}},t.getIFrame=function(){return this.modal.querySelector("iframe")},t.toggleResponsiveViewport=function(e){var t=e.target.closest(".fpf-templates-library-preview-responsive-device");t&&this.setViewport(t.dataset.device)},t.setViewport=function(e){var t=this.getIFrame();this.modal.querySelector(".fpf-templates-library-preview-responsive-device.active").classList.remove("active"),this.modal.querySelector('.fpf-templates-library-preview-responsive-device[data-device="'+e+'"]').classList.add("active"),t.classList.remove("desktop","tablet","mobile"),t.classList.add(e)},e}(),FPF_Templates_Library_Search=function(){function e(e){this.instance=e}var t=e.prototype;return t.search=function(){var i=this.getSearchTerm(),s=this.instance.library_toolbar.querySelector(".fpf-library-view-favorites.active");this.instance.items.getCurrentItems(!0).forEach(function(e){var t=!0;if(i){if(t=!1,e.dataset.title.toLowerCase().includes(i)&&(t=!0),e.dataset.note.toLowerCase().includes(i)&&(t=!0),e.dataset.filterCategory.toLowerCase().includes(i)&&(t=!0),e.dataset.filterTags.toLowerCase().includes(i)&&(t=!0),e.dataset.filterEvent)e.dataset.filterEvent.toLowerCase().includes(i)&&(t=!0);e.dataset.filterSolution.toLowerCase().includes(i)&&(t=!0)}if(s){var r=e.querySelector(".fpf-library-favorite-item");r&&(!r||e.querySelector(".fpf-library-favorite-item").classList.contains("active"))||(t=!1)}t?e.classList.remove("is-hidden"):e.classList.add("is-hidden")}),this.updateSearchElements()},t.updateSearchElements=function(){var e=this.instance.items.getCurrentItems(!0).length;0===e?(this.instance.library_noresults.classList.add("is-visible"),this.instance.library_list.classList.add("is-hidden")):(this.instance.library_noresults.classList.remove("is-visible"),this.instance.library_list.classList.remove("is-hidden")),this.instance.library_wrapper.querySelector(".fpf-showing-results-counter").innerHTML=e,this.instance.filters.onUpdateSearchElements();var t=this.getSearchTerm();if(t){this.instance.filters.setHasFilters();var r=this.instance.library_wrapper.querySelector(".fpf-library-filter-template").cloneNode(!0);r.querySelector(".filter").classList.add("search-filter-pill"),r.querySelector(".filter svg").remove(),r.querySelector(".filter .filter-label").textContent='"'+t+'"',this.instance.filters.getFilterPillsWrapper().appendChild(r.children[0])}},t.getSearchTerm=function(){return this.instance.library_toolbar.querySelector("#fpf_search_template").value.trim().toLowerCase()},e}(),FPF_Templates_Library_Sidebar=function(){function e(e){this.instance=e,this.items=this.instance.items,this.toolbar=this.instance.toolbar,this.initEvents()}var t=e.prototype;return t.initEvents=function(){this.onPageLoadToggle(),document.addEventListener("click",function(e){this.initSidebarToggle(e)}.bind(this))},t.initSidebarToggle=function(e){if(e.target.closest(".fpf-library-sidebar-toggle")){e.preventDefault(),this.instance.modal.classList.toggle("sidebar-open");var t=this.getLibraryID();this.instance.modal.classList.contains("sidebar-open")?window.localStorage.setItem(t,"open"):(window.localStorage.removeItem(t),this.instance.sidebar_element.scrollTo({top:0,behavior:"smooth"}))}},t.getLibraryID=function(){return"templates_library_"+this.instance.library_wrapper.closest(".fpf-templates-library").querySelector(":scope > .modal").id},t.onPageLoadToggle=function(){window.localStorage.getItem(this.getLibraryID())?this.instance.modal.classList.add("sidebar-open"):this.instance.modal.classList.remove("sidebar-open")},e}();function _readOnlyError(e){throw new TypeError('"'+e+'" is read-only')}var FPF_Templates_Library_Sorting=function(){function e(e){this.instance=e,this.sortWrapper=this.instance.library_toolbar.querySelector(".sorting-selector-item"),this.initEvents()}var t=e.prototype;return t.initEvents=function(){document.addEventListener("click",function(e){this.onSortUpdate(e),this.onSortHide(e)}.bind(this)),this.sortWrapper.addEventListener("mouseover",function(e){this.onSortingShow(e)}.bind(this)),this.sortWrapper.addEventListener("mouseout",function(e){this.onSortingMouseOutHide(e)}.bind(this))},t.onSortingMouseOutHide=function(e){(e.target.closest(".sorting-selected-label")||e.target.closest(".sorting-selector-items"))&&this.sortWrapper.classList.remove("visible")},t.onSortUpdate=function(e){if(e.target.closest(".sorting-selector-items")){var t=e.target.closest("li");t&&(t.classList.contains("selected")||(e.preventDefault(),this.sortWrapper.classList.remove("visible"),this.sortWrapper.querySelector(".sorting-selector-items li.selected").classList.remove("selected"),t.classList.add("selected"),this.sortWrapper.querySelector(".sorting-selected-label .selected-label").innerHTML=t.innerHTML,this.sort()))}},t.sort=function(){var i=this,s=this.sortWrapper.querySelector(".sorting-selector-items li.selected").dataset.value,e=this.instance.items.getCurrentItems(!0);e.forEach(function(e){e.style.order=null});var t=Array.from(e).sort(function(e,t){var r="sort"+i.capitalizeFirstLetter(s);return"date"===s?e.dataset[r].localeCompare(t.dataset[r]):parseInt(e.dataset[r],10)<=parseInt(t.dataset[r],10)?-1:1});t=[].concat(t).reverse();var r=1;t.forEach(function(e){e.style.order=r,r++})},t.onSortHide=function(e){e.target.closest(".sorting-selector-item")||this.sortWrapper.classList.remove("visible")},t.onSortingShow=function(e){e.target.closest(".sorting-selector-item")&&this.sortWrapper.classList.add("visible")},t.capitalizeFirstLetter=function(e){return e.charAt(0).toUpperCase()+e.slice(1)},e}(),FPF_Templates_Library_Toolbar=function(){function e(e){this.instance=e,this.initEvents()}var t=e.prototype;return t.initEvents=function(){document.addEventListener("input",function(e){this.onInputSearch(e)}.bind(this)),document.addEventListener("click",function(e){this.onFavoritesView(e)}.bind(this))},t.onInputSearch=function(e){e.target.closest("#fpf_search_template")&&(e.target.value.trim(),this.instance.filters.update())},t.onFavoritesView=function(e){var t=e.target.closest(".fpf-library-view-favorites");t&&(t.classList.toggle("active"),this.instance.library_wrapper.classList.toggle("favorites-view"),this.instance.filters.update(),e.preventDefault())},e}();

