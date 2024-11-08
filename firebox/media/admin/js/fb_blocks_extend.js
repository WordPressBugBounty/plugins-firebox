function _extends(){return(_extends=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var o=arguments[t];for(var n in o)Object.prototype.hasOwnProperty.call(o,n)&&(e[n]=o[n])}return e}).apply(this,arguments)}var registerStore=wp.data.registerStore,__=wp.i18n.__,addFilter=wp.hooks.addFilter,_wp$element=wp.element,Fragment=_wp$element.Fragment,useEffect=_wp$element.useEffect,useState=_wp$element.useState,updateCategory=wp.blocks.updateCategory,InspectorControls=wp.blockEditor.InspectorControls,createHigherOrderComponent=wp.compose.createHigherOrderComponent,_wp$components=wp.components,TextControl=_wp$components.TextControl,SelectControl=_wp$components.SelectControl,ToggleControl=_wp$components.ToggleControl,PanelBody=_wp$components.PanelBody,SVG=_wp$components.SVG,fireplugins_firebox_allowedBlocks=["core/button","core/image","firebox/button","firebox/image"],FireBox_Event_Tracking_Toggler=function(){function e(){}var t=e.prototype;return t.getBlockWithTrackingEnabled=function(e){var o=this;if(e&&e.length){var n=null;return e.forEach(function(e){var t;e.attributes&&e.attributes.dataFBoxTrackingEnabled?n=e:(t=o.getBlockWithTrackingEnabled(e.innerBlocks))&&!n&&(n=t)}),n}},t.hasFireBoxFormBlock=function(e){var t=this;return e.some(function(e){return"firebox/form"===e.name||e.innerBlocks&&t.hasFireBoxFormBlock(e.innerBlocks)})},t.disableConversionTrackingInBlocks=function(e){var t=this;e&&e.length&&e.forEach(function(e){fireplugins_firebox_allowedBlocks.includes(e.name)?wp.data.dispatch("core/block-editor").updateBlockAttributes(e.clientId,{dataFBoxTrackingEnabled:!1,dataFBoxTrackingControlDisabled:!0}):t.disableConversionTrackingInBlocks(e.innerBlocks)})},t.disableOtherConversionTracking=function(t,e){var o=this;e&&e.length&&e.forEach(function(e){e.clientId!==t.clientId&&fireplugins_firebox_allowedBlocks.includes(e.name)?wp.data.dispatch("core/block-editor").updateBlockAttributes(e.clientId,{dataFBoxTrackingEnabled:!1,dataFBoxTrackingControlDisabled:!0}):o.disableOtherConversionTracking(t,e.innerBlocks)})},t.enableOtherConversionTracking=function(e){var t=this;e&&e.length&&e.forEach(function(e){fireplugins_firebox_allowedBlocks.includes(e.name)?wp.data.dispatch("core/block-editor").updateBlockAttributes(e.clientId,{dataFBoxTrackingControlDisabled:!1}):t.enableOtherConversionTracking(e.innerBlocks)})},t.init=function(){var e=wp.data.select("core/block-editor").getBlocks();if(e.length&&"firebox"===window.typenow){var t=this.getBlockWithTrackingEnabled(e);if(this.hasFireBoxFormBlock(e))return void this.disableConversionTrackingInBlocks(e);t?this.disableOtherConversionTracking(t,e):(this.maybeEnableSelectedBlock(),this.enableOtherConversionTracking(e))}},t.maybeEnableSelectedBlock=function(){var e=wp.data.select("core/block-editor").getSelectedBlock();e&&fireplugins_firebox_allowedBlocks.includes(e.name)&&null===e.attributes.dataFBoxTrackingEnabled&&wp.data.dispatch("core/block-editor").updateBlockAttributes(e.clientId,{dataFBoxTrackingEnabled:!0,dataFBoxTrackingControlDisabled:!1})},e}(),contentState=wp.data.select("core/editor").getEditedPostContent();wp.data.subscribe(lodash.debounce(function(){var e=wp.data.select("core/editor").getEditedPostContent();contentState!==e&&(new FireBox_Event_Tracking_Toggler).init(),contentState=e},200));var firebox_campaigns_selection=[];firebox_campaigns_selection.push({value:"none",label:__("Select a Campaign","firebox")}),"firebox"===window.typenow&&firebox_campaigns_selection.push({value:"current",label:__("Current Campaign","firebox")}),wp.apiFetch({path:"/fireplugins/firebox/boxes"}).then(function(e){firebox_parse_boxes(e).forEach(function(e){e.value!=wp.data.select("core/editor").getCurrentPostId()&&firebox_campaigns_selection.push({value:e.value,label:e.label})})});var FBox_Block_Add_Attributes=function(e){return fireplugins_firebox_allowedBlocks.includes(e.name)&&void 0!==e.attributes&&(e.attributes=Object.assign(e.attributes,{dataFBoxOnClick:{type:"string",default:"open_url"},dataFBoxOnClickURL:{type:"string",default:""},dataFBoxOnClickURLAddParameters:{type:"boolean",default:!1},dataFBoxOnClickCloseCampaign:{type:"string",default:"current"},dataFBoxOnClickOpenCampaign:{type:"string",default:""},dataFBoxOnClickCopyClipboard:{type:"string",default:""},dataFBoxOnClickDownloadFileURL:{type:"string",default:""},dataFBoxTrackingEnabled:{type:"boolean",default:null},dataFBoxTrackingControlDisabled:{type:"boolean",default:!1},dataFBox:{type:"string",default:"current"},dataFBoxCmd:{type:"string",default:"open"},dataFBoxEnabled:{type:"boolean",default:!1},dataFBoxClass:{type:"string",default:""}})),e},FBox_Block_Set_Advanced_Controls=createHigherOrderComponent(function(F){return function(e){function t(e){return 0<wp.data.select("core/block-editor").getBlockParentsByBlockName(e,"firebox/form").length}function o(e){void 0===e&&(e=""),""!==e&&void 0!==e?e!==d&&l({dataFBoxOnClickURL:e}):l({dataFBoxOnClickURL:"",dataFBoxOnClickURLAddParameters:!1})}var n=e.clientId,a=e.name,r=e.attributes,l=e.setAttributes,i=r.url,c=r.href,s=r.dataFBoxOnClick,d=r.dataFBoxOnClickURL,p=r.dataFBoxOnClickURLAddParameters,u=r.dataFBoxOnClickCloseCampaign,b=r.dataFBoxOnClickOpenCampaign,f=r.dataFBoxOnClickCopyClipboard,g=r.dataFBoxOnClickDownloadFileURL,x=r.dataFBoxTrackingEnabled,_=r.dataFBoxTrackingControlDisabled,m=r.dataFBox,C=r.dataFBoxCmd,k=r.dataFBoxEnabled,h=r.dataFBoxClass,B=[{label:__("Open URL","firebox"),value:"open_url"},{label:__("Close Campaign","firebox"),value:"close_campaign"},{label:__("Open Campaign","firebox"),value:"open_campaign"},{label:__("Copy to Clipboard","firebox"),value:"copy_clipboard"},{label:__("Download File","firebox"),value:"download_file"},{label:__("Go to Step (Coming Soon)","firebox"),disabled:!0,value:"go_to_step"}];return useEffect(function(){["core/image","firebox/image"].includes(a)&&o(c)},[c]),useEffect(function(){["core/button","firebox/button"].includes(a)&&o(i)},[i]),useEffect(function(){var e=!1;if(fireplugins_firebox_allowedBlocks.includes(a)){var t={};if(["core/button","firebox/button"].includes(a)?t.url=d:["core/image","firebox/image"].includes(a)&&(t.href=d),!Object.keys(t).length)return;e=setTimeout(function(){l(t)},250)}if(e)return function(){return clearTimeout(e)}},[d]),React.createElement(Fragment,null,fireplugins_firebox_allowedBlocks.includes(a)&&React.createElement(InspectorControls,null,React.createElement(PanelBody,{title:__("FireBox","firebox"),initialOpen:"firebox/button"!==a||!t(n)},React.createElement(SelectControl,{label:__("On Click","firebox"),value:s,options:B,onChange:function(e){return l({dataFBoxOnClick:e})}}),"open_url"==s&&React.createElement(React.Fragment,null,React.createElement(TextControl,{label:__("URL","firebox"),value:d,onChange:function(e){var t={dataFBoxOnClickURL:e};""===e&&["core/button","firebox/button"].includes(a)&&(t.url=""),"core/image"===a?t.href=e:"firebox/image"===a&&""===(t.href=e)&&(t.link="none"),e||(t.dataFBoxOnClickURLAddParameters=!1),l(t)},help:__("Enter a URL to redirect to when this "+a.split("/").pop()+" is clicked.","firebox")}),""!==d.trim()&&React.createElement(ToggleControl,{label:__("Add UTM Parameters","firebox"),checked:!!p,onChange:function(){return l({dataFBoxOnClickURLAddParameters:!p})},help:__("Enable to append UTM parameters to the URL. The UTM parameters are automatically added on the URL and consist of utm_source which is the post type, utm_medium which is the block name, utm_campaign which is the post title and the utm_content which is the block text or image URL.","firebox")})),"close_campaign"===s&&React.createElement(SelectControl,{label:__("Close Campaign","firebox"),value:u,options:firebox_campaigns_selection,onChange:function(e){return l({dataFBoxOnClickCloseCampaign:e})}}),"open_campaign"===s&&React.createElement(React.Fragment,null,2<=firebox_campaigns_selection.length?React.createElement(React.Fragment,null,React.createElement(SelectControl,{label:__("Open Campaign","firebox"),value:b,options:firebox_campaigns_selection,onChange:function(e){return l({dataFBoxOnClickOpenCampaign:e})}}),React.createElement("div",{className:"fpframework-gutenberg-help-text"},__("Note that the selected campaign must be present on the same page in order to be opened.","firebox"))):React.createElement("div",{className:"fpframework-gutenberg-help-text"},__("There are no published campaigns that can be opened. Please publish some campaigns first.","firebox"))),"copy_clipboard"===s&&React.createElement(TextControl,{label:__("Text to copy","firebox"),value:f,onChange:function(e){return l({dataFBoxOnClickCopyClipboard:e})},help:__("Enter a text to copy to the clipboard when this block is clicked.","firebox")}),"download_file"===s&&React.createElement(TextControl,{label:__("File URL","firebox"),value:g,onChange:function(e){return l({dataFBoxOnClickDownloadFileURL:e})},help:__("Enter a URL to a file to download when this block is clicked.","firebox")}),"firebox"===window.typenow&&!t(n)&&React.createElement(React.Fragment,null,React.createElement(ToggleControl,{label:__("Track conversion","firebox"),checked:!!x,onChange:function(){return l({dataFBoxTrackingEnabled:!x})},help:__("Enable to track conversions when the "+a.split("/").pop()+" is clicked.","firebox"),className:_?"fpframework-gutenberg-m-b-5":"",disabled:_}),_&&React.createElement("p",{className:"fpframework-color-red components-base-control"},__("One block per campaign is allowed to track conversions.","firebox"))))),React.createElement(F,e),fireplugins_firebox_allowedBlocks.includes(a)&&React.createElement(InspectorControls,null,React.createElement(PanelBody,{title:__("FireBox (Deprecated)","firebox"),initialOpen:!1},React.createElement(ToggleControl,{label:__("(Deprecated) Enable to trigger a FireBox campaign","firebox"),checked:!!k,onChange:function(){return l({dataFBoxEnabled:!k})},help:__('Deprecated. Please use the "On Click" setting to perform an action when this block is clicked. This section will be removed on January 1st, 2025.',"firebox")}),k&&React.createElement(Fragment,null,React.createElement(SelectControl,{label:__("Select a Campaign","firebox"),value:m,options:firebox_campaigns_selection,onChange:function(e){return l({dataFBox:e})}}),"none"!=m&&React.createElement(Fragment,null,React.createElement(SelectControl,{label:__("FireBox Action","firebox"),value:C,options:[{label:"Open",value:"open"},{label:"Close",value:"close"},{label:"Toggle",value:"toggle"}],onChange:function(e){return l({dataFBoxCmd:e})},help:__("Set the action of the popup. Open, Close or Toggle.","firebox")})),React.createElement(TextControl,{label:__("CSS Classes","firebox"),value:h,onChange:function(e){return l({dataFBoxClass:e})},help:__("Set custom CSS Classes to this block. Separate multiple classes with spaces.","firebox")})))))}},"FBox_Block_Set_Advanced_Controls"),FBox_Block_Apply_Advanced_Controls=function(e,t,o){if(e){if(!fireplugins_firebox_allowedBlocks.includes(t.name))return e;if(function t(e){return e.some(function(e){return"firebox/form"===e.name||e.innerBlocks&&t(e.innerBlocks)})}(wp.data.select("core/block-editor").getBlocks()))return e;var n=o.dataFBoxTrackingEnabled,a=o.dataFBoxOnClick,r=o.dataFBoxOnClickCloseCampaign,l=o.dataFBoxOnClickOpenCampaign,i=o.dataFBoxOnClickCopyClipboard,c=o.dataFBoxOnClickDownloadFileURL;if(n?e=React.cloneElement(e,{"data-fbox-tracking":"true"}):delete e.props["data-fbox-tracking"],"close_campaign"===a&&"none"!==r){var s={"data-fbox-cmd":"close"};return"current"!==r&&(s["data-fbox"]=r),React.cloneElement(e,s)}if("open_campaign"===a&&"none"!==l){var d={"data-fbox-cmd":"open"};return"current"!==l&&(d["data-fbox"]=l),React.cloneElement(e,d)}if("copy_clipboard"===a&&""!==i){var p={"data-fbox-copy-clipboard":i};return React.cloneElement(e,p)}if("download_file"!==a||""===c)return e;return React.cloneElement(function t(e,o){if(!e||"object"!=typeof e)return e;if("a"===e.type||"a"===e.props.tagName){var n=_extends({},e.props,{href:o,download:"true"});return _extends({},e,{props:n})}if(e.props&&e.props.children){var a=React.Children.map(e.props.children,function(e){return t(e,o)});return _extends({},e,{props:_extends({},e.props,{children:a})})}return e}(e,c),{})}};function firebox_parse_boxes(e){var t=[];return e.map(function(e){t.push({label:e.title,value:e.id})}),t}addFilter("blocks.registerBlockType","fbox/fbox-cpt-custom-attributes",FBox_Block_Add_Attributes),addFilter("editor.BlockEdit","fbox/fbox-cpt-custom-controls",FBox_Block_Set_Advanced_Controls),addFilter("blocks.getSaveElement","my-plugin/wrap-cover-block-in-container",FBox_Block_Apply_Advanced_Controls);var FireBoxCategoryIcon=function(){return React.createElement(SVG,{xmlns:"http://www.w3.org/2000/svg",width:"25",viewBox:"0 0 40 40",style:{marginLeft:"5px"}},React.createElement("path",{fillRule:"evenodd",clipRule:"evenodd",d:"M8.43049 0C3.77446 0 0 3.77446 0 8.43049V31.5695C0 36.2255 3.77446 40 8.43049 40H31.5695C36.2255 40 40 36.2255 40 31.5695V8.43049C40 3.77446 36.2255 0 31.5695 0H8.43049ZM11.1211 9.32735C10.1304 9.32735 9.32735 10.1304 9.32735 11.1211V28.8789C9.32735 29.8696 10.1304 30.6726 11.1211 30.6726H28.8789C29.8696 30.6726 30.6726 29.8696 30.6726 28.8789V11.1211C30.6726 10.1304 29.8696 9.32735 28.8789 9.32735H11.1211ZM16.8966 17.9728C16.8966 17.3785 17.3785 16.8966 17.9728 16.8966H22.0272C22.6215 16.8966 23.1034 17.3785 23.1034 17.9728V22.0272C23.1034 22.6215 22.6215 23.1034 22.0272 23.1034H17.9728C17.3785 23.1034 16.8966 22.6215 16.8966 22.0272V17.9728Z",fill:"#2438E9"}))};updateCategory("firebox",{icon:FireBoxCategoryIcon});var FirePluginsCategoryIcon=function(){return React.createElement(SVG,{xmlns:"http://www.w3.org/2000/svg",width:"25",viewBox:"0 0 40 40",style:{marginLeft:"5px"}},React.createElement("path",{fillRule:"evenodd",clipRule:"evenodd",d:"M16.7589 0L20.7524 2.63971C29.0855 8.14791 34.1404 15.9983 33.3678 24.8262C32.5645 34.0043 24.4333 40.737 15.2662 39.9352C6.09897 39.1335 -0.739139 31.0916 0.0641224 21.9136C0.180255 20.5867 0.37899 19.1879 1.12266 17.3053C1.80271 15.5836 2.89509 13.5649 4.59658 10.8145L8.53268 13.2477L8.67938 13.0126C8.7833 12.8461 8.93413 12.6044 9.12162 12.3038C9.49663 11.7027 10.0182 10.8662 10.6046 9.92495C11.7784 8.04091 13.2083 5.74258 14.2418 4.07104L16.7589 0ZM12.4706 15.7359C10.8676 18.3298 10.1297 19.7788 9.76001 20.7147C9.45209 21.4942 9.38344 21.9423 9.31514 22.7227C8.964 26.7348 11.9588 30.3275 16.0755 30.6875C20.1922 31.0476 23.7656 28.0293 24.1168 24.0172C24.413 20.6323 23.0665 16.8868 19.4014 13.3644C19.0872 13.8692 18.7792 14.3639 18.4873 14.8323C17.8998 15.7755 17.3772 16.6135 17.0014 17.2158C16.8136 17.517 16.6624 17.7593 16.5582 17.9263L16.3967 18.185C16.3967 18.185 16.3965 18.1852 12.4706 15.7359Z",fill:"#2438E9"}))};updateCategory("fireplugins",{icon:FirePluginsCategoryIcon});

