(()=>{var e={4184:(e,t)=>{var r;!function(){"use strict";var l={}.hasOwnProperty;function o(){for(var e=[],t=0;t<arguments.length;t++){var r=arguments[t];if(r){var i=typeof r;if("string"===i||"number"===i)e.push(r);else if(Array.isArray(r)){if(r.length){var n=o.apply(null,r);n&&e.push(n)}}else if("object"===i){if(r.toString!==Object.prototype.toString&&!r.toString.toString().includes("[native code]")){e.push(r.toString());continue}for(var a in r)l.call(r,a)&&r[a]&&e.push(a)}}}return e.join(" ")}e.exports?(o.default=o,e.exports=o):void 0===(r=function(){return o}.apply(t,[]))||(e.exports=r)}()},2703:(e,t,r)=>{"use strict";var l=r(414);function o(){}function i(){}i.resetWarningCache=o,e.exports=function(){function e(e,t,r,o,i,n){if(n!==l){var a=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw a.name="Invariant Violation",a}}function t(){return e}e.isRequired=e;var r={array:e,bigint:e,bool:e,func:e,number:e,object:e,string:e,symbol:e,any:e,arrayOf:t,element:e,elementType:e,instanceOf:t,node:e,objectOf:t,oneOf:t,oneOfType:t,shape:t,exact:t,checkPropTypes:i,resetWarningCache:o};return r.PropTypes=r,r}},5697:(e,t,r)=>{e.exports=r(2703)()},414:e=>{"use strict";e.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"},9281:(e,t,r)=>{"use strict";var l=r(7889);function o(){}function i(){}i.resetWarningCache=o,e.exports=function(){function e(e,t,r,o,i,n){if(n!==l){var a=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw a.name="Invariant Violation",a}}function t(){return e}e.isRequired=e;var r={array:e,bigint:e,bool:e,func:e,number:e,object:e,string:e,symbol:e,any:e,arrayOf:t,element:e,elementType:e,instanceOf:t,node:e,objectOf:t,oneOf:t,oneOfType:t,shape:t,exact:t,checkPropTypes:i,resetWarningCache:o};return r.PropTypes=r,r}},1277:(e,t,r)=>{e.exports=r(9281)()},7889:e=>{"use strict";e.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"}},t={};function r(l){var o=t[l];if(void 0!==o)return o.exports;var i=t[l]={exports:{}};return e[l](i,i.exports,r),i.exports}r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var l in t)r.o(t,l)&&!r.o(e,l)&&Object.defineProperty(e,l,{enumerable:!0,get:t[l]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";const e=window.wp.blocks,t=window.React,l=window.wp.element,o=window.wp.data;var i=r(5697),n=r.n(i);const a=window.wp.blockEditor;var s=r(4184),u=r.n(s);const p=e=>`field-${e}`,c=window.wp.components;class d extends t.Component{constructor(){super(...arguments)}render(){this.props?.fieldProps?.className&&(this.props.fieldProps.className+=" fb-form-control-group"),this.props?.attributes?.cssClass&&(this.props.fieldProps.className+=" "+this.props.attributes.cssClass);let e=this?.props?.attributes?.fieldLabelRequiredFieldIndication||!1;return(0,t.createElement)("div",{...this.props.fieldProps},""!==this.props.attributes.fieldLabel&&!this.props.attributes.hideLabel&&(0,t.createElement)("label",{className:"fb-form-control-label",htmlFor:`fb-form-input-${this.props.fieldProps["data-field-id"]}`},this.props.attributes.fieldLabel,e&&this.props.attributes.required&&(0,t.createElement)("span",{className:"fb-form-control-required"},"*")),(0,t.createElement)("div",{className:"fb-form-control-input"},this.getInput()),""!==this.props.attributes.helpText&&(0,t.createElement)("div",{className:"fb-form-control-helptext"},this.props.attributes.helpText))}getInput(){}}const b=d,f=class extends b{getInput(){let e=this.props.attributes?.theme||"auto",r=this.props.attributes?.size||"normal",l=fbox_block_editor_object.media_url+"admin/images/turnstile/"+("dark"===e?"dark":"light")+"-"+r+".png";return(0,t.createElement)(t.Fragment,null,(""===fbox_block_editor_object.turnstile_site_key||""===fbox_block_editor_object.turnstile_site_key)&&(0,t.createElement)(t.Fragment,null,(0,t.createElement)(c.Notice,{status:"warning",isDismissible:!1},"Please set your Cloudflare Site Key and Secret Key in the ",(0,t.createElement)("a",{href:fbox_block_editor_object.settings_url+"#captcha",target:"_blank"},"FireBox settings"),"."),(0,t.createElement)("br",null)),(0,t.createElement)("img",{src:l,alt:"cloudflare turnstile preview"}))}};var h=r(1277),m=r.n(h);const g=window.wp.i18n,_=e=>!!e&&Object.values(e).some((e=>null!=e&&""!==e)),v={desktop:"",tablet:991,mobile:575},x=(e=[])=>{if(!e||!e.length)return[];const t=e.reduce(((e,t)=>{const r=(e=>{if(!e)return"";const t=e.match(/[^{\}]+(?=})/gi)[0];return{selector:e.replace(t,"").replace("{","").replace("}","").trim(),style:t.trim()}})(t),l=e.find((({selector:e})=>e===r.selector));return l?(l.styles.push(r.style),e):(e.push({selector:r.selector,styles:[r.style]}),e)}),[]);return t.map((e=>`${e.selector} { ${e.styles.join(" ")} }`))},y=e=>({...e,rules:x(e.rules)}),w=({id:e,value:t,rule:r,unit:l="",edgeCase:o,breakpoint:i="desktop",oneline:n=!1})=>{if(null==t||""===t)return null;let a=r.includes("[BLOCK_ID]")?r.replace("[BLOCK_ID]",e):`${r}`;if((e=>["box-shadow"].some((t=>e.includes(t))))(r))return["inset","outset"].includes(t.type)&&""!==t.color?(0,g.sprintf)(a,`${t.left}px ${t.top}px ${t.width}px ${t.spread}px ${"outset"===t.type?"":t.type} ${t.color}`):null;if((e=>["text-shadow"].some((t=>e.includes(t))))(r))return""!==t.hpos&&""!==t.vpos&&""!==t.blur&&""!==t.color?(0,g.sprintf)(a,`${t.hpos}px ${t.vpos}px ${t.blur}px ${t.color}`):null;if((e=>["padding","border-radius","margin","position-items"].some((t=>e.includes(t)))&&!["padding-","margin-"].some((t=>e.includes(t))))(r)){let e=r.match(/margin|border-radius|padding|position-items/gi)[0];if("position-items"===e){const e=["top","right","bottom","left"].map((e=>{if(null!=t[e]&&""!==t[e])return`${e}: ${"auto"===t[e]?"auto":`${t[e]}${l}`};`})).join(" ");return e.trim()?(s=` ${e} `,a.replace(/[^{\}]+(?=})/gi,s)):null}{const r=((e,t,r="",l=!1)=>{if(!e)return!1;if(_(e)||_(e[t])){const o=e[t]||e;if(["margin","padding"].includes(r)){if(4===Object.values(o).length&&1===new Set(Object.values(o)).size)return`${o.top}px`;if(Object.values(o).every((e=>""!==e))){if(o.top===o.bottom&&o.left===o.right)return`${o.top||0}px ${o.left||0}px`;Object.values(o).map((function(e){return`${e||0}px`})).join(" ")}let e=[],t=["top","right","bottom","left"];return l?(t.forEach((t=>{e.push(`${o[t]||0}px`)})),e.join(" ")):(t.forEach((t=>{""!==o[t]&&e.push(r+"-"+t+":"+o[t]+"px;")})),e)}if("border-radius"===r){if(4===Object.values(o).length&&1===new Set(Object.values(o)).size)return`${o.top_left}px`;if(Object.values(o).every((e=>""!==e))){if(o.top_left===o.bottom_right&&o.bottom_left===o.top_right)return`${o.top_left||0}px ${o.bottom_left||0}px`;Object.values(o).map((function(e){return`${e||0}px`})).join(" ")}let e=[],t=["top_left","top_right","bottom_right","bottom_left"];return l?(t.forEach((t=>{e.push(`${o[t]||0}px`)})),e.join(" ")):(t.forEach((t=>{""!==o[t]&&e.push("border-"+t.replace("_","-")+"-radius:"+o[t]+"px;")})),e)}}return!1})(t,null,e,n);if(!r)return null;if(r&&"string"==typeof r)return(0,g.sprintf)(a,r)||null;if(r&&"object"==typeof r)return a=a.replace("margin: ",""),a=a.replace("padding: ",""),a=a.replace("border-radius: ",""),a=a.replace("%s;","%s"),(0,g.sprintf)(a,r.join(""))||null}}var s;if((e=>!!e&&"object"==typeof e&&"url"in e&&"attachment"in e&&"repeat"in e)(t)){if(!t.url)return;const e=[`background-image: ${"none"!==t.url?`url(${t.url})`:"none"};`,`background-repeat: ${t.repeat};`,`background-size: ${t.size};`,`background-position: ${t.position};`,`background-attachment: ${t.attachment};`].join(" ");return(0,g.sprintf)(a,e)}if(o){const e=o.find((({edge:e})=>e===t));if(e)return""===e.value||e.skipInBreakpoints&&e.skipInBreakpoints.includes(i)?null:(l=e.unit||"",(0,g.sprintf)(a,`${e.value}${l}`))}return(0,g.sprintf)(a,`${t}${l}`)},C=e=>l.Children.toArray(e).map((e=>Array.isArray(e.props.children)?C(e.props.children):e)).reduce(((e,t)=>Array.isArray(t)?[...e,...t]:[...e,t]),[]).filter((e=>e.type&&"Rule"===e.type.displayName)),k={children:m().oneOfType([m().node,m().array]),id:m().string},E=({id:e="",children:r})=>{const l=(({id:e,children:t,getCSSRule:r,mapper:l=(e=>e)})=>{const o=[{name:"desktop",media:"max",width:v.desktop,rules:[]},{name:"tablet",media:"max",width:v.tablet,rules:[]},{name:"mobile",media:"max",width:v.mobile,rules:[]},{name:"desktop-only",media:"min",width:v.tablet+1,rules:[]},{name:"tablet-only",media:"min",width:v.mobile+1,rules:[]}];return C(t).reduce(((t,l)=>{const{value:o,rule:i,unit:n,edgeCase:a,breakpointLimit:s,oneline:u}=l.props;if(null==o)return t;if("object"!=typeof o||!("desktop"in o)){const l=t.find((({name:e})=>"desktop"===e)),s=r({id:e,value:o,rule:i,unit:n,edgeCase:a,breakpoint:"desktop",oneline:u});return null!=s&&l.rules.push(s),t}return Object.keys(o).forEach((l=>{const p=t.find((({name:e})=>s&&"mobile"!==l?`${l}-only`===e:e===l)),c=r({id:e,value:o[l],rule:i,unit:n,edgeCase:a,breakpoint:l,oneline:u});null!=c&&p&&p.rules.push(c)})),t}),o).map(l)})({id:e,children:r,getCSSRule:w,mapper:y});return(e=>e.every((e=>0===e.rules.length)))(l)?null:(0,t.createElement)("style",{dangerouslySetInnerHTML:{__html:(o=l,o.reduce(((e,t)=>{if(!t.rules.length)return e;const r=t.rules.map((e=>e.trim())).join("\n");return t.width?"tablet-only"===t.name?`${e}\n\n\t\t\t\t@media (min-width: ${v.mobile+1}px) and (max-width: ${v.tablet}px) {\n\t\t\t\t\t${r}\n\t\t\t\t}\n\t\t\t\t`:`${e}\n\n\t\t\t@media (${t.media}-width: ${t.width}px) {\n\t\t\t\t${r}\n\t\t\t}\n\t\t\t`:`${e}${r}\n`}),"").trim())}});var o};E.propTypes=k;const T=E,$={value:m().oneOfType([m().string,m().number,m().object]).isRequired,rule:m().string.isRequired,unit:m().oneOf(["","px","%","em","rem","vh","vw","pt","cm","mm"]),edgeCase:m().arrayOf(m().shape({edge:m().any.isRequired,value:m().oneOfType([m().string,m().number]).isRequired,skipInBreakpoints:m().array})),breakpointLimit:m().bool,oneline:m().bool},I=()=>null;I.displayName="Rule",I.propTypes=$;const O=I,S={attributes:n().object.isRequired,children:n().node},j=({attributes:e,children:r})=>{const{uniqueId:l,fieldLabelFontWeight:o}=e,i=p(l);return(0,t.createElement)(T,{id:i},(0,t.createElement)(O,{value:o,rule:".fb-form-control-group.[BLOCK_ID] { --label-font-weight: %s; }"}),r)};j.propTypes=S;const A=j,L=e=>{var r,l,o,i;const{fieldTypeName:n}=e,{fieldName:a,fieldLabel:s,helpText:u,required:p}=e.attributes,d=null===(r=e?.showFieldName)||void 0===r||r,b=null===(l=e?.showFieldLabel)||void 0===l||l,f=null===(o=e?.showHelpText)||void 0===o||o,h=null===(i=e?.showRequired)||void 0===i||i;return(0,t.createElement)(c.PanelBody,{title:n+" "+__("Field","firebox"),initialOpen:!0},d&&(0,t.createElement)(c.TextControl,{label:__("Field Name","firebox"),required:!0,value:a,onChange:t=>e.setAttributes({fieldName:t}),help:__("Set a unique field name which is used to reference the form data. Enter only alphanumerics and underscores.","firebox")}),b&&(0,t.createElement)(c.TextControl,{label:__("Field Label","firebox"),value:s,onChange:t=>e.setAttributes({fieldLabel:t}),help:__("Set a label for the field. Leave blank to hide the label.","firebox")}),f&&(0,t.createElement)(c.TextControl,{label:__("Help text","firebox"),value:u,onChange:t=>e.setAttributes({helpText:t}),help:__("Set a helpful description to show below the field.","firebox")}),h&&(0,t.createElement)(c.ToggleControl,{label:__("Required","firebox"),checked:p,onChange:t=>e.setAttributes({required:t})}),e.children)},q=e=>{var r,l,o,i,n,a;const{width:s,defaultValue:u,placeholder:p,cssClass:d,inputCssClass:b,hideLabel:f,disableBrowserAutocomplete:h,fieldLabelFontWeight:m}=e.attributes,g=null===(r=e?.showWidth)||void 0===r||r,_=null===(l=e?.showDefaultValue)||void 0===l||l,v=null===(o=e?.showPlaceholder)||void 0===o||o,x=null===(i=e?.showHideLabel)||void 0===i||i,y=null===(n=e?.showDisableBrowserAutocomplete)||void 0===n||n,w=null===(a=e?.showInputCssClass)||void 0===a||a;return(0,t.createElement)(c.PanelBody,{title:__("Field Settings","firebox"),initialOpen:!1},g&&(0,t.createElement)(c.SelectControl,{label:__("Width","firebox"),options:[{label:"0%",value:"0%"},{label:"5%",value:"5%"},{label:"10%",value:"10%"},{label:"20%",value:"20%"},{label:"25%",value:"25%"},{label:"30%",value:"30%"},{label:"33%",value:"33%"},{label:"40%",value:"40%"},{label:"50%",value:"50%"},{label:"60%",value:"60%"},{label:"66%",value:"66%"},{label:"70%",value:"70%"},{label:"75%",value:"75%"},{label:"80%",value:"80%"},{label:"90%",value:"90%"},{label:"95%",value:"95%"},{label:"100%",value:"100%"}],value:s,onChange:t=>e.setAttributes({width:t})}),_&&(0,t.createElement)(c.TextareaControl,{label:__("Default Value","firebox"),value:u,onChange:t=>e.setAttributes({defaultValue:t}),help:__("Set the default field value.","firebox")}),v&&(0,t.createElement)(c.TextControl,{label:__("Placeholder Text","firebox"),value:p,onChange:t=>e.setAttributes({placeholder:t}),help:__("The text you'd like displayed in the field, before a user enters any data.","firebox")}),(0,t.createElement)(c.SelectControl,{label:__("Label Font Weight","firebox"),options:[{value:"",label:"Select Font Weight",disabled:!0},{label:__("300","firebox"),value:"300"},{label:__("400","firebox"),value:"400"},{label:__("500","firebox"),value:"500"},{label:__("600","firebox"),value:"600"},{label:__("700","firebox"),value:"700"}],value:m,onChange:t=>e.setAttributes({fieldLabelFontWeight:t})}),(0,t.createElement)(c.TextControl,{label:__("CSS Class","firebox"),value:d,onChange:t=>e.setAttributes({cssClass:t}),help:__("Add CSS Classes to the input's container. This can mainly be used for layout purposes.","firebox")}),w&&(0,t.createElement)(c.TextControl,{label:__("Input CSS Class","firebox"),value:b,onChange:t=>e.setAttributes({inputCssClass:t}),help:__("Add CSS Classes to the input element. Use this option to style the input itself.","firebox")}),x&&(0,t.createElement)(c.ToggleControl,{label:__("Hide Label","firebox"),default:!1,checked:f,onChange:t=>e.setAttributes({hideLabel:t}),help:__("Check this option to hide the form field label.","firebox")}),y&&(0,t.createElement)(c.ToggleControl,{label:__("Disable Browser Autocomplete","firebox"),default:!1,checked:h,onChange:t=>e.setAttributes({disableBrowserAutocomplete:t}),help:__("By default, browsers remember information that the user submits through input fields. To disable autocompletion in forms enable this option.","firebox")}),e.children)},P={attributes:n().object.isRequired,setAttributes:n().func.isRequired},B=({attributes:e,setAttributes:r})=>(0,t.createElement)(a.InspectorControls,null,(0,t.createElement)(L,{fieldTypeName:"Turnstile",attributes:e,setAttributes:r,showFieldName:!1,showRequired:!1},(0,t.createElement)(SelectControl,{label:(0,g.__)("Theme","firebox"),value:e.theme,options:[{label:(0,g.__)("Auto","firebox"),value:"auto"},{label:(0,g.__)("Light","firebox"),value:"light"},{label:(0,g.__)("Dark","firebox"),value:"dark"}],onChange:e=>r({theme:e})}),(0,t.createElement)(SelectControl,{label:(0,g.__)("Size","firebox"),value:e.size,options:[{label:(0,g.__)("Normal","firebox"),value:"normal"},{label:(0,g.__)("Compact","firebox"),value:"compact"}],onChange:e=>r({size:e})})),(0,t.createElement)(q,{attributes:e,setAttributes:r,showDefaultValue:!1,showPlaceholder:!1,showInputCssClass:!1,showDisableBrowserAutocomplete:!1}));B.propTypes=P;const R=B,D=window.lodash;const N={attributes:n().object.isRequired,setAttributes:n().func.isRequired,className:n().string,clientId:n().string.isRequired,context:n().object},F=({attributes:e,setAttributes:r,className:i,clientId:n,context:s})=>{const{uniqueId:c}=e,{addUniqueID:d}=(0,o.useDispatch)("firebox/data"),{isUniqueID:b,isUniqueBlock:h,parentData:m}=(0,o.useSelect)((e=>({isUniqueID:t=>e("firebox/data").isUniqueID(t),isUniqueBlock:(t,r)=>e("firebox/data").isUniqueBlock(t,r),parentData:{rootBlock:e("core/block-editor").getBlock(e("core/block-editor").getBlockHierarchyRootClientId(n)),postId:e("core/editor")?.getCurrentPostId()?e("core/editor")?.getCurrentPostId():"",reusableParent:e("core/block-editor").getBlockAttributes(e("core/block-editor").getBlockParentsByBlockName(n,"core/block").slice(-1)[0]),editedPostId:!!e("core/edit-site")&&e("core/edit-site").getEditedPostId()}})),[n]);useEffect((()=>{const t=function(e){const{postId:t,reusableParent:r,editedPostId:l}=e,o=function(e,t,r="block-unknown"){return(0,D.has)(t,"ref")?t.ref:e||r}(t,r,0);return 0===o&&l?function(e){for(var t=5381,r=e.length;r;)t=33*t^e.charCodeAt(--r);return t>>>0}(l)%1e6:o}(m);let l=function(e,t,r,l,o="",i=!1){const n=e&&2===e.split("_").length,a=(o?o+"_":"")+t.substr(2,9);return!e||n&&e.split("_")[0]!==o.toString()?r(a)?a:(0,D.uniqueId)(a):r(e)||l(e,t)?e:i?(0,D.uniqueId)(a):a}(c,n,b,h,t);l!==c?(e.uniqueId=l,r({uniqueId:l}),d(l,n)):(r({uniqueId:c}),d(c,n))}),[]);const g=p(c),_=(0,a.useBlockProps)({id:g,className:u()(i,g),"data-field-id":c});return(0,t.createElement)(l.Fragment,null,(0,t.createElement)(A,{attributes:e}),(0,t.createElement)(R,{attributes:e,setAttributes:r}),(0,t.createElement)(f,{fieldProps:_,attributes:e,context:s}))};F.propTypes=N;const U=F;"firebox"===window.typenow&&(0,e.registerBlockType)("firebox/turnstile",{icon:()=>(0,t.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"24",viewBox:"0 0 48 48",className:"firebox-gutenberg-block-list-item"},(0,t.createElement)("mask",{id:"mask0_2459_1669",style:{maskType:"alpha"},maskUnits:"userSpaceOnUse",x:"0",y:"0",width:"48",height:"48"},(0,t.createElement)("rect",{width:"48",height:"48",fill:"#D9D9D9"})),(0,t.createElement)("g",{mask:"url(#mask0_2459_1669)"},(0,t.createElement)("path",{d:"M21.8999 30.4078L32.5076 19.8001L30.3692 17.6617L21.8999 26.1309L17.6615 21.8925L15.5231 24.0309L21.8999 30.4078ZM23.9999 42.9616C19.6743 41.7821 16.0929 39.236 13.2557 35.3232C10.4186 31.4104 9 27.036 9 22.2001V10.6925L23.9999 5.07715L38.9999 10.6925V22.2001C38.9999 27.036 37.5813 31.4104 34.7441 35.3232C31.907 39.236 28.3256 41.7821 23.9999 42.9616ZM23.9999 39.8002C27.4666 38.7002 30.3333 36.5001 32.5999 33.2001C34.8666 29.9001 36 26.2335 36 22.2001V12.7501L23.9999 8.2694L11.9999 12.7501V22.2001C11.9999 26.2335 13.1333 29.9001 15.3999 33.2001C17.6666 36.5001 20.5333 38.7002 23.9999 39.8002Z",fill:"#2438E9"}))),edit:U,save:()=>null})})()})();