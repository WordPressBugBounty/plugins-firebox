(()=>{var e={184:(e,t)=>{var r;!function(){"use strict";var l={}.hasOwnProperty;function i(){for(var e=[],t=0;t<arguments.length;t++){var r=arguments[t];if(r){var s=typeof r;if("string"===s||"number"===s)e.push(r);else if(Array.isArray(r)){if(r.length){var o=i.apply(null,r);o&&e.push(o)}}else if("object"===s){if(r.toString!==Object.prototype.toString&&!r.toString.toString().includes("[native code]")){e.push(r.toString());continue}for(var a in r)l.call(r,a)&&r[a]&&e.push(a)}}}return e.join(" ")}e.exports?(i.default=i,e.exports=i):void 0===(r=function(){return i}.apply(t,[]))||(e.exports=r)}()},703:(e,t,r)=>{"use strict";var l=r(414);function i(){}function s(){}s.resetWarningCache=i,e.exports=function(){function e(e,t,r,i,s,o){if(o!==l){var a=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw a.name="Invariant Violation",a}}function t(){return e}e.isRequired=e;var r={array:e,bigint:e,bool:e,func:e,number:e,object:e,string:e,symbol:e,any:e,arrayOf:t,element:e,elementType:e,instanceOf:t,node:e,objectOf:t,oneOf:t,oneOfType:t,shape:t,exact:t,checkPropTypes:s,resetWarningCache:i};return r.PropTypes=r,r}},697:(e,t,r)=>{e.exports=r(703)()},414:e=>{"use strict";e.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"},281:(e,t,r)=>{"use strict";var l=r(889);function i(){}function s(){}s.resetWarningCache=i,e.exports=function(){function e(e,t,r,i,s,o){if(o!==l){var a=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw a.name="Invariant Violation",a}}function t(){return e}e.isRequired=e;var r={array:e,bigint:e,bool:e,func:e,number:e,object:e,string:e,symbol:e,any:e,arrayOf:t,element:e,elementType:e,instanceOf:t,node:e,objectOf:t,oneOf:t,oneOfType:t,shape:t,exact:t,checkPropTypes:s,resetWarningCache:i};return r.PropTypes=r,r}},277:(e,t,r)=>{e.exports=r(281)()},889:e=>{"use strict";e.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"}},t={};function r(l){var i=t[l];if(void 0!==i)return i.exports;var s=t[l]={exports:{}};return e[l](s,s.exports,r),s.exports}r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var l in t)r.o(t,l)&&!r.o(e,l)&&Object.defineProperty(e,l,{enumerable:!0,get:t[l]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";const e=window.wp.blocks,t=window.React,l=window.wp.element,i=window.wp.data;var s=r(697),o=r.n(s);const a=window.wp.blockEditor;var n=r(184),u=r.n(n);const p=e=>`field-${e}`;class d extends t.Component{constructor(){super(...arguments)}render(){this.props?.fieldProps?.className&&(this.props.fieldProps.className+=" fb-form-control-group"),this.props?.attributes?.cssClass&&(this.props.fieldProps.className+=" "+this.props.attributes.cssClass);let e=this?.props?.attributes?.fieldLabelRequiredFieldIndication||!1;return(0,t.createElement)("div",{...this.props.fieldProps},""!==this.props.attributes.fieldLabel&&!this.props.attributes.hideLabel&&(0,t.createElement)("label",{className:"fb-form-control-label",htmlFor:`fb-form-input-${this.props.fieldProps["data-field-id"]}`},this.props.attributes.fieldLabel,e&&this.props.attributes.required&&(0,t.createElement)("span",{className:"fb-form-control-required"},"*")),(0,t.createElement)("div",{className:"fb-form-control-input"},this.getInput()),""!==this.props.attributes.helpText&&(0,t.createElement)("div",{className:"fb-form-control-helptext"},this.props.attributes.helpText))}getInput(){}}const c=d,b=class extends c{getInput(){let e={value:this.props.attributes.defaultValue,placeholder:this.props.attributes.placeholder};return this.props.attributes.required&&(e.required="required"),this.props.attributes.disableBrowserAutocomplete&&(e.autocomplete="off"),(0,t.createElement)("input",{type:"email",name:`fb_form[${this.props.attributes.fieldName}]`,...e,id:`fb-form-input-${this.props.fieldProps["data-field-id"]}`,className:u()("fb-form-input",this.props.attributes.inputCssClass),onChange:()=>{}})}};var f=r(277),h=r.n(f);const m=window.wp.i18n,g=e=>!!e&&Object.values(e).some((e=>null!=e&&""!==e)),v={desktop:"",tablet:991,mobile:575},y=(e=[])=>{if(!e||!e.length)return[];const t=e.reduce(((e,t)=>{const r=(e=>{if(!e)return"";const t=e.match(/[^{\}]+(?=})/gi)[0];return{selector:e.replace(t,"").replace("{","").replace("}","").trim(),style:t.trim()}})(t),l=e.find((({selector:e})=>e===r.selector));return l?(l.styles.push(r.style),e):(e.push({selector:r.selector,styles:[r.style]}),e)}),[]);return t.map((e=>`${e.selector} { ${e.styles.join(" ")} }`))},x=e=>({...e,rules:y(e.rules)}),_=({id:e,value:t,rule:r,unit:l="",edgeCase:i,breakpoint:s="desktop",oneline:o=!1})=>{if(null==t||""===t)return null;let a=r.includes("[BLOCK_ID]")?r.replace("[BLOCK_ID]",e):`${r}`;if((e=>["box-shadow"].some((t=>e.includes(t))))(r))return["inset","outset"].includes(t.type)&&""!==t.color?(0,m.sprintf)(a,`${t.left}px ${t.top}px ${t.width}px ${t.spread}px ${"outset"===t.type?"":t.type} ${t.color}`):null;if((e=>["text-shadow"].some((t=>e.includes(t))))(r))return""!==t.hpos&&""!==t.vpos&&""!==t.blur&&""!==t.color?(0,m.sprintf)(a,`${t.hpos}px ${t.vpos}px ${t.blur}px ${t.color}`):null;if((e=>["padding","border-radius","margin","position-items"].some((t=>e.includes(t)))&&!["padding-","margin-"].some((t=>e.includes(t))))(r)){let e=r.match(/margin|border-radius|padding|position-items/gi)[0];if("position-items"===e){const e=["top","right","bottom","left"].map((e=>{if(null!=t[e]&&""!==t[e])return`${e}: ${"auto"===t[e]?"auto":`${t[e]}${l}`};`})).join(" ");return e.trim()?(n=` ${e} `,a.replace(/[^{\}]+(?=})/gi,n)):null}{const r=((e,t,r="",l=!1)=>{if(!e)return!1;if(g(e)||g(e[t])){const i=e[t]||e;if(["margin","padding"].includes(r)){if(4===Object.values(i).length&&1===new Set(Object.values(i)).size)return`${i.top}px`;if(Object.values(i).every((e=>""!==e))){if(i.top===i.bottom&&i.left===i.right)return`${i.top||0}px ${i.left||0}px`;Object.values(i).map((function(e){return`${e||0}px`})).join(" ")}let e=[],t=["top","right","bottom","left"];return l?(t.forEach((t=>{e.push(`${i[t]||0}px`)})),e.join(" ")):(t.forEach((t=>{""!==i[t]&&e.push(r+"-"+t+":"+i[t]+"px;")})),e)}if("border-radius"===r){if(4===Object.values(i).length&&1===new Set(Object.values(i)).size)return`${i.top_left}px`;if(Object.values(i).every((e=>""!==e))){if(i.top_left===i.bottom_right&&i.bottom_left===i.top_right)return`${i.top_left||0}px ${i.bottom_left||0}px`;Object.values(i).map((function(e){return`${e||0}px`})).join(" ")}let e=[],t=["top_left","top_right","bottom_right","bottom_left"];return l?(t.forEach((t=>{e.push(`${i[t]||0}px`)})),e.join(" ")):(t.forEach((t=>{""!==i[t]&&e.push("border-"+t.replace("_","-")+"-radius:"+i[t]+"px;")})),e)}}return!1})(t,null,e,o);if(!r)return null;if(r&&"string"==typeof r)return(0,m.sprintf)(a,r)||null;if(r&&"object"==typeof r)return a=a.replace("margin: ",""),a=a.replace("padding: ",""),a=a.replace("border-radius: ",""),a=a.replace("%s;","%s"),(0,m.sprintf)(a,r.join(""))||null}}var n;if((e=>!!e&&"object"==typeof e&&"url"in e&&"attachment"in e&&"repeat"in e)(t)){if(!t.url)return;const e=[`background-image: ${"none"!==t.url?`url(${t.url})`:"none"};`,`background-repeat: ${t.repeat};`,`background-size: ${t.size};`,`background-position: ${t.position};`,`background-attachment: ${t.attachment};`].join(" ");return(0,m.sprintf)(a,e)}if(i){const e=i.find((({edge:e})=>e===t));if(e)return""===e.value||e.skipInBreakpoints&&e.skipInBreakpoints.includes(s)?null:(l=e.unit||"",(0,m.sprintf)(a,`${e.value}${l}`))}return(0,m.sprintf)(a,`${t}${l}`)},C=e=>l.Children.toArray(e).map((e=>Array.isArray(e.props.children)?C(e.props.children):e)).reduce(((e,t)=>Array.isArray(t)?[...e,...t]:[...e,t]),[]).filter((e=>e.type&&"Rule"===e.type.displayName)),w={children:h().oneOfType([h().node,h().array]),id:h().string},k=({id:e="",children:r})=>{const l=(({id:e,children:t,getCSSRule:r,mapper:l=(e=>e)})=>{const i=[{name:"desktop",media:"max",width:v.desktop,rules:[]},{name:"tablet",media:"max",width:v.tablet,rules:[]},{name:"mobile",media:"max",width:v.mobile,rules:[]},{name:"desktop-only",media:"min",width:v.tablet+1,rules:[]},{name:"tablet-only",media:"min",width:v.mobile+1,rules:[]}];return C(t).reduce(((t,l)=>{const{value:i,rule:s,unit:o,edgeCase:a,breakpointLimit:n,oneline:u}=l.props;if(null==i)return t;if("object"!=typeof i||!("desktop"in i)){const l=t.find((({name:e})=>"desktop"===e)),n=r({id:e,value:i,rule:s,unit:o,edgeCase:a,breakpoint:"desktop",oneline:u});return null!=n&&l.rules.push(n),t}return Object.keys(i).forEach((l=>{const p=t.find((({name:e})=>n&&"mobile"!==l?`${l}-only`===e:e===l)),d=r({id:e,value:i[l],rule:s,unit:o,edgeCase:a,breakpoint:l,oneline:u});null!=d&&p&&p.rules.push(d)})),t}),i).map(l)})({id:e,children:r,getCSSRule:_,mapper:x});return(e=>e.every((e=>0===e.rules.length)))(l)?null:(0,t.createElement)("style",{dangerouslySetInnerHTML:{__html:(i=l,i.reduce(((e,t)=>{if(!t.rules.length)return e;const r=t.rules.map((e=>e.trim())).join("\n");return t.width?"tablet-only"===t.name?`${e}\n\n\t\t\t\t@media (min-width: ${v.mobile+1}px) and (max-width: ${v.tablet}px) {\n\t\t\t\t\t${r}\n\t\t\t\t}\n\t\t\t\t`:`${e}\n\n\t\t\t@media (${t.media}-width: ${t.width}px) {\n\t\t\t\t${r}\n\t\t\t}\n\t\t\t`:`${e}${r}\n`}),"").trim())}});var i};k.propTypes=w;const E=k,T={value:h().oneOfType([h().string,h().number,h().object]).isRequired,rule:h().string.isRequired,unit:h().oneOf(["","px","%","em","rem","vh","vw","pt","cm","mm"]),edgeCase:h().arrayOf(h().shape({edge:h().any.isRequired,value:h().oneOfType([h().string,h().number]).isRequired,skipInBreakpoints:h().array})),breakpointLimit:h().bool,oneline:h().bool},$=()=>null;$.displayName="Rule",$.propTypes=T;const I=$,q={attributes:o().object.isRequired,children:o().node},L=({attributes:e,children:r})=>{const{uniqueId:l,width:i,fieldLabelFontWeight:s}=e,o=p(l);return(0,t.createElement)(E,{id:o},(0,t.createElement)(I,{value:i,rule:".fb-form-control-group.[BLOCK_ID] { --field-width: %s; }"}),(0,t.createElement)(I,{value:s,rule:".fb-form-control-group.[BLOCK_ID] { --label-font-weight: %s; }"}),r)};L.propTypes=q;const O=L,P=window.wp.components,A=e=>{var r,l;const{fieldTypeName:i}=e,{fieldName:s,fieldLabel:o,helpText:a,required:n}=e.attributes,u=null===(r=e?.showFieldLabel)||void 0===r||r,p=null===(l=e?.showHelpText)||void 0===l||l;return(0,t.createElement)(P.PanelBody,{title:i+" "+__("Field","firebox"),initialOpen:!0},(0,t.createElement)(P.TextControl,{label:__("Field Name","firebox"),required:!0,value:s,onChange:t=>e.setAttributes({fieldName:t}),help:__("Set a unique field name which is used to reference the form data. Enter only alphanumerics and underscores.","firebox")}),u&&(0,t.createElement)(P.TextControl,{label:__("Field Label","firebox"),value:o,onChange:t=>e.setAttributes({fieldLabel:t}),help:__("Set a label for the field. Leave blank to hide the label.","firebox")}),p&&(0,t.createElement)(P.TextControl,{label:__("Help text","firebox"),value:a,onChange:t=>e.setAttributes({helpText:t}),help:__("Set a helpful description to show below the field.","firebox")}),(0,t.createElement)(P.ToggleControl,{label:__("Required","firebox"),checked:n,onChange:t=>e.setAttributes({required:t})}),e.children)},N=e=>{var r,l,i,s;const{width:o,defaultValue:a,placeholder:n,cssClass:u,inputCssClass:p,hideLabel:d,disableBrowserAutocomplete:c,fieldLabelFontWeight:b}=e.attributes,f=null===(r=e?.showWidth)||void 0===r||r,h=null===(l=e?.showPlaceholder)||void 0===l||l,m=null===(i=e?.showHideLabel)||void 0===i||i,g=null===(s=e?.showDisableBrowserAutocomplete)||void 0===s||s;return(0,t.createElement)(P.PanelBody,{title:__("Field Settings","firebox"),initialOpen:!1},f&&(0,t.createElement)(P.SelectControl,{label:__("Width","firebox"),options:[{label:"0%",value:"0%"},{label:"5%",value:"5%"},{label:"10%",value:"10%"},{label:"20%",value:"20%"},{label:"25%",value:"25%"},{label:"30%",value:"30%"},{label:"33%",value:"33%"},{label:"40%",value:"40%"},{label:"50%",value:"50%"},{label:"60%",value:"60%"},{label:"66%",value:"66%"},{label:"70%",value:"70%"},{label:"75%",value:"75%"},{label:"80%",value:"80%"},{label:"90%",value:"90%"},{label:"95%",value:"95%"},{label:"100%",value:"100%"}],value:o,onChange:t=>e.setAttributes({width:t})}),(0,t.createElement)(P.TextareaControl,{label:__("Default Value","firebox"),value:a,onChange:t=>e.setAttributes({defaultValue:t}),help:__("Set the default field value.","firebox")}),h&&(0,t.createElement)(P.TextControl,{label:__("Placeholder Text","firebox"),value:n,onChange:t=>e.setAttributes({placeholder:t}),help:__("The text you'd like displayed in the field, before a user enters any data.","firebox")}),(0,t.createElement)(P.SelectControl,{label:__("Label Font Weight","firebox"),options:[{value:"",label:"Select Font Weight",disabled:!0},{label:__("300","firebox"),value:"300"},{label:__("400","firebox"),value:"400"},{label:__("500","firebox"),value:"500"},{label:__("600","firebox"),value:"600"},{label:__("700","firebox"),value:"700"}],value:b,onChange:t=>e.setAttributes({fieldLabelFontWeight:t})}),(0,t.createElement)(P.TextControl,{label:__("CSS Class","firebox"),value:u,onChange:t=>e.setAttributes({cssClass:t}),help:__("Add CSS Classes to the input's container. This can mainly be used for layout purposes.","firebox")}),(0,t.createElement)(P.TextControl,{label:__("Input CSS Class","firebox"),value:p,onChange:t=>e.setAttributes({inputCssClass:t}),help:__("Add CSS Classes to the input element. Use this option to style the input itself.","firebox")}),m&&(0,t.createElement)(P.ToggleControl,{label:__("Hide Label","firebox"),default:!1,checked:d,onChange:t=>e.setAttributes({hideLabel:t}),help:__("Check this option to hide the form field label.","firebox")}),g&&(0,t.createElement)(P.ToggleControl,{label:__("Disable Browser Autocomplete","firebox"),default:!1,checked:c,onChange:t=>e.setAttributes({disableBrowserAutocomplete:t}),help:__("By default, browsers remember information that the user submits through input fields. To disable autocompletion in forms enable this option.","firebox")}),e.children)},j={attributes:o().object.isRequired,setAttributes:o().func.isRequired},S=({attributes:e,setAttributes:r})=>(0,t.createElement)(a.InspectorControls,null,(0,t.createElement)(A,{fieldTypeName:"Email",attributes:e,setAttributes:r}),(0,t.createElement)(N,{attributes:e,setAttributes:r}));S.propTypes=j;const B=S,R=window.lodash;const F={attributes:o().object.isRequired,setAttributes:o().func.isRequired,className:o().string,clientId:o().string.isRequired,context:o().object},D=({attributes:e,setAttributes:r,className:s,clientId:o,context:n})=>{const{uniqueId:d}=e,{addUniqueID:c}=(0,i.useDispatch)("firebox/data"),{isUniqueID:f,isUniqueBlock:h,parentData:m}=(0,i.useSelect)((e=>({isUniqueID:t=>e("firebox/data").isUniqueID(t),isUniqueBlock:(t,r)=>e("firebox/data").isUniqueBlock(t,r),parentData:{rootBlock:e("core/block-editor").getBlock(e("core/block-editor").getBlockHierarchyRootClientId(o)),postId:e("core/editor")?.getCurrentPostId()?e("core/editor")?.getCurrentPostId():"",reusableParent:e("core/block-editor").getBlockAttributes(e("core/block-editor").getBlockParentsByBlockName(o,"core/block").slice(-1)[0]),editedPostId:!!e("core/edit-site")&&e("core/edit-site").getEditedPostId()}})),[o]);useEffect((()=>{const t=function(e){const{postId:t,reusableParent:r,editedPostId:l}=e,i=function(e,t,r="block-unknown"){return(0,R.has)(t,"ref")?t.ref:e||r}(t,r,0);return 0===i&&l?function(e){for(var t=5381,r=e.length;r;)t=33*t^e.charCodeAt(--r);return t>>>0}(l)%1e6:i}(m);let l=function(e,t,r,l,i="",s=!1){const o=e&&2===e.split("_").length,a=(i?i+"_":"")+t.substr(2,9);return!e||o&&e.split("_")[0]!==i.toString()?r(a)?a:(0,R.uniqueId)(a):r(e)||l(e,t)?e:s?(0,R.uniqueId)(a):a}(d,o,f,h,t);l!==d?(e.uniqueId=l,r({uniqueId:l}),c(l,o)):(r({uniqueId:d}),c(d,o))}),[]);const g=p(d),v=(0,a.useBlockProps)({id:g,className:u()(s,g),"data-field-id":d});return(0,t.createElement)(l.Fragment,null,(0,t.createElement)(O,{attributes:e}),(0,t.createElement)(B,{attributes:e,setAttributes:r}),(0,t.createElement)(b,{fieldProps:v,attributes:e,context:n}))};D.propTypes=F;const W=D;class U extends t.Component{constructor(){super(...arguments)}render(){this.props?.fieldProps?.className&&(this.props.fieldProps.className+=" fb-form-control-group"),this.props?.attributes?.cssClass&&(this.props.fieldProps.className+=" "+this.props.attributes.cssClass);let e=this?.props?.attributes?.fieldLabelRequiredFieldIndication||!1;return(0,t.createElement)("div",{...this.props.fieldProps},""!==this.props.attributes.fieldLabel&&!this.props.attributes.hideLabel&&(0,t.createElement)("div",{className:"fb-form-control-label"},this.props.attributes.fieldLabel,e&&this.props.attributes.required&&(0,t.createElement)("span",{className:"fb-form-control-required"},"*")),(0,t.createElement)("div",{className:"fb-form-control-input"},this.getInput()),""!==this.props.attributes.helpText&&(0,t.createElement)("div",{className:"fb-form-control-helptext"},this.props.attributes.helpText))}getInput(){}}class H extends U{getInput(){let e={value:this.props.attributes.defaultValue,placeholder:this.props.attributes.placeholder};return this.props.attributes.required&&(e.required="required"),this.props.attributes.disableBrowserAutocomplete&&(e.autocomplete="off"),(0,t.createElement)("input",{type:"email",name:`fb_form[${this.props.attributes.fieldName}]`,...e,className:u()("fb-form-input",this.props.attributes.inputCssClass),onChange:()=>{}})}}const V=[{attributes:(({fieldName:e="",fieldLabel:t="",fieldLabelRequiredFieldIndication:r=!0,fieldRequired:l=!0})=>({uniqueId:{type:"string",default:""},fieldLabelRequiredFieldIndication:{type:"boolean",default:r},fieldName:{type:"string",default:e},fieldLabel:{type:"string",default:t},helpText:{type:"string",default:""},required:{type:"boolean",default:l},width:{type:"string",default:"100%"},defaultValue:{type:"string",default:""},placeholder:{type:"string",default:""},cssClass:{type:"string",default:""},inputCssClass:{type:"string",default:""},hideLabel:{type:"boolean",default:!1},disableBrowserAutocomplete:{type:"boolean",default:!1}}))({fieldName:"email",fieldLabel:"Email field"}),save:({attributes:e,className:r})=>{const{uniqueId:l}=e,i=p(l),s=a.useBlockProps.save({id:i,className:u()(r,i),"data-field-id":l});return(0,t.createElement)(t.Fragment,null,(0,t.createElement)(O,{attributes:e}),(0,t.createElement)(H,{fieldProps:s,attributes:e}))}}];"firebox"===window.typenow&&(0,e.registerBlockType)("firebox/email",{icon:()=>(0,t.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"24",viewBox:"0 0 48 48",className:"firebox-gutenberg-block-list-item"},(0,t.createElement)("path",{d:"M40 10H8C6.89543 10 6 10.8954 6 12V36C6 37.1046 6.89543 38 8 38H40C41.1046 38 42 37.1046 42 36V12C42 10.8954 41.1046 10 40 10Z",stroke:"#2438E9",strokeWidth:"3",strokeLinecap:"round",strokeLinejoin:"round",fill:"transparent"}),(0,t.createElement)("path",{d:"M6 14.5L22.829 26.6543C23.528 27.1591 24.472 27.1591 25.171 26.6543L42 14.5",stroke:"#2438E9",strokeWidth:"3",strokeLinecap:"round",strokeLinejoin:"round",fill:"transparent"})),edit:W,save:({attributes:e,className:r})=>{const{uniqueId:l}=e,i=p(l),s=a.useBlockProps.save({id:i,className:u()(r,i),"data-field-id":l});return(0,t.createElement)(t.Fragment,null,(0,t.createElement)(O,{attributes:e}),(0,t.createElement)(b,{fieldProps:s,attributes:e}))},deprecated:V})})()})();