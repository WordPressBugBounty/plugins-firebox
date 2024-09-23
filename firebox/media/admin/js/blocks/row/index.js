!function(){var e={894:function(){const e="SET_BREAKPOINT",t={breakpoint:"desktop"};wp.data.select("fpframework-breakpoints")||wp.data.registerStore("fpframework-breakpoints",{reducer(r=t,o){return o.type===e?{...r,breakpoint:o.breakpoint}:r},actions:{onBreakpointSet:t=>({type:e,breakpoint:t})},selectors:{getBreakpoint:e=>e.breakpoint}})},184:function(e,t){var r;!function(){"use strict";var o={}.hasOwnProperty;function n(){for(var e=[],t=0;t<arguments.length;t++){var r=arguments[t];if(r){var l=typeof r;if("string"===l||"number"===l)e.push(r);else if(Array.isArray(r)){if(r.length){var a=n.apply(null,r);a&&e.push(a)}}else if("object"===l){if(r.toString!==Object.prototype.toString&&!r.toString.toString().includes("[native code]")){e.push(r.toString());continue}for(var i in r)o.call(r,i)&&r[i]&&e.push(i)}}}return e.join(" ")}e.exports?(n.default=n,e.exports=n):void 0===(r=function(){return n}.apply(t,[]))||(e.exports=r)}()},703:function(e,t,r){"use strict";var o=r(414);function n(){}function l(){}l.resetWarningCache=n,e.exports=function(){function e(e,t,r,n,l,a){if(a!==o){var i=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw i.name="Invariant Violation",i}}function t(){return e}e.isRequired=e;var r={array:e,bigint:e,bool:e,func:e,number:e,object:e,string:e,symbol:e,any:e,arrayOf:t,element:e,elementType:e,instanceOf:t,node:e,objectOf:t,oneOf:t,oneOfType:t,shape:t,exact:t,checkPropTypes:l,resetWarningCache:n};return r.PropTypes=r,r}},697:function(e,t,r){e.exports=r(703)()},414:function(e){"use strict";e.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"},192:function(e,t){var r;!function(){"use strict";var o={}.hasOwnProperty;function n(){for(var e=[],t=0;t<arguments.length;t++){var r=arguments[t];if(r){var l=typeof r;if("string"===l||"number"===l)e.push(r);else if(Array.isArray(r)){if(r.length){var a=n.apply(null,r);a&&e.push(a)}}else if("object"===l){if(r.toString!==Object.prototype.toString&&!r.toString.toString().includes("[native code]")){e.push(r.toString());continue}for(var i in r)o.call(r,i)&&r[i]&&e.push(i)}}}return e.join(" ")}e.exports?(n.default=n,e.exports=n):void 0===(r=function(){return n}.apply(t,[]))||(e.exports=r)}()},281:function(e,t,r){"use strict";var o=r(889);function n(){}function l(){}l.resetWarningCache=n,e.exports=function(){function e(e,t,r,n,l,a){if(a!==o){var i=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw i.name="Invariant Violation",i}}function t(){return e}e.isRequired=e;var r={array:e,bigint:e,bool:e,func:e,number:e,object:e,string:e,symbol:e,any:e,arrayOf:t,element:e,elementType:e,instanceOf:t,node:e,objectOf:t,oneOf:t,oneOfType:t,shape:t,exact:t,checkPropTypes:l,resetWarningCache:n};return r.PropTypes=r,r}},277:function(e,t,r){e.exports=r(281)()},889:function(e){"use strict";e.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"}},t={};function r(o){var n=t[o];if(void 0!==n)return n.exports;var l=t[o]={exports:{}};return e[o](l,l.exports,r),l.exports}r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,{a:t}),t},r.d=function(e,t){for(var o in t)r.o(t,o)&&!r.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){"use strict";var e=window.wp.i18n,t=window.wp.blocks,o=window.React,n=r(697),l=r.n(n),a=window.wp.blockEditor,i=r(184),s=r.n(i),c=window.wp.data,p=e=>`block-${e}`,u=window.wp.element,d=window.wp.components,m=r(192),f=r.n(m);r(894);var b=({children:e,childrenClass:t="",wrapperClass:r=""})=>{const n=(0,c.useSelect)((e=>e("fpframework-breakpoints").getBreakpoint())),{onBreakpointSet:l}=(0,c.useDispatch)("fpframework-breakpoints");return(0,o.createElement)("div",{className:`components-base-control wp-block-fpframework-responsive-control-wrap ${r}`},(0,o.createElement)("div",{className:"wp-block-fpframework-responsive-control-wrap-buttons"},[{value:"desktop",icon:"desktop"},{value:"tablet",icon:"tablet"},{value:"mobile",icon:"smartphone"}].map(((e,t)=>(0,o.createElement)("button",{key:t,onClick:()=>l(e.value),className:f()({"wp-block-fpframework-responsive-control-wrap-button":!0,active:e.value===n})},(0,o.createElement)("span",{className:`dashicons dashicons-${e.icon}`}))))),(0,o.createElement)("div",{className:t},e(n)))},w=({setAttributes:t,attributeKey:r,attributes:n,label:l,breakpoint:a,positionsLabels:i=[],positions:s=["top","right","bottom","left"]})=>{const c=n[r],p=s[0],u=s[1],m=s[2],f=s[3],b=n[r]?.linked;return(0,o.createElement)("div",{className:"wp-block-fpframework-control-spacing components-base-control"},(0,o.createElement)("p",{className:"wp-block-fpframework-control-spacing-label"},l),(0,o.createElement)("div",{className:"wp-block-fpframework-control-spacing-controls-wrap"},(0,o.createElement)("div",{className:"wp-block-fpframework-control-spacing-controls"},s.map(((e,n)=>{var l;return(0,o.createElement)("div",{key:n,className:"wp-block-fpframework-spacing-controls-item"},(0,o.createElement)(d.TextControl,{value:a?c[a][e]:c[e],type:"number",min:-200,max:200,step:1,onChange:o=>((e,o)=>{if(a)return b?void t({[r]:{...c,[a]:{...c[a],[p]:e,[u]:e,[m]:e,[f]:e}}}):void t({[r]:{...c,[a]:{...c[a],[o]:e}}});t(b?{[r]:{...c,[p]:e,[u]:e,[m]:e,[f]:e}}:{[r]:{...c,[o]:e}})})(o,e)}),(0,o.createElement)("p",{className:"label",dangerouslySetInnerHTML:{__html:null!==(l=i[e])&&void 0!==l?l:e}}))}))),(0,o.createElement)(d.Button,{isSecondary:!b,isPrimary:b,className:"components-toolbar__control",label:(0,e.__)("Link Values","fpf-framework"),icon:"admin-links",onClick:()=>{t({[r]:{...c,linked:!b}})}})))},h=({label:t,disableAlpha:r=!1,wrapperClass:n="",value:l,help:a="",defaultValue:i="",onChange:s})=>{const[p,m]=(0,u.useState)(!1),[f,b]=(0,u.useState)(!1),w=(0,c.useSelect)((e=>e("core/block-editor").getSettings().colors));return(0,o.createElement)("div",{className:"components-base-control"},(0,o.createElement)("div",{className:`wp-block-fpframework-color-selector-settings-container ${n}`},(0,o.createElement)("div",{className:"inner"},t&&(0,o.createElement)("span",{className:"wp-block-fpframework-color-selector-label"},t),(0,o.createElement)("div",{className:"actions"},l&&l!==i&&(0,o.createElement)(d.Tooltip,{text:(0,e.__)("Reset","fpf-framework")},(0,o.createElement)(d.Button,{className:"components-color-palette__clear",type:"button",onClick:()=>{s(i)},isSmall:!0},(0,o.createElement)(d.Dashicon,{icon:"redo"}))),(0,o.createElement)("div",{className:"wp-block-fpframework-color-selector-color-click"},p&&(0,o.createElement)(u.Fragment,null,(0,o.createElement)(d.Popover,{position:"top left",className:"wp-block-fpframework-popover-color",onClose:()=>m(!1)},(0,o.createElement)(d.ColorPicker,{key:f,color:l,onChangeComplete:e=>{if(e.rgb&&1===e.rgb.a||r)s(e.hex);else{const{r:t,g:r,b:o,a:n}=e.rgb;s(`rgba(${t}, ${r}, ${o}, ${n})`)}},disableAlpha:!1}),w.length>0&&(0,o.createElement)("div",{className:"components-color-palette"},w.map((t=>(0,o.createElement)("div",{key:t.slug,className:"components-color-palette__item-wrapper"},(0,o.createElement)(d.Button,{type:"button",className:"components-color-palette__item "+(l===t.color?"is-active":""),onClick:()=>{s(t.color),b((e=>!e))},style:{backgroundColor:t.color,color:t.color},"aria-label":t.name?
// translators: %s: The name of the color i.e.: "blue".
// translators: %s: The name of the color i.e.: "blue".
(0,e.sprintf)((0,e.__)("Color: %s"),name):
// translators: %s: color hex code i.e.: "#ffffff".
// translators: %s: color hex code i.e.: "#ffffff".
(0,e.sprintf)((0,e.__)("Color hex code: %s"),t),"aria-pressed":t.color===l}),t.color===l&&(0,o.createElement)(d.Dashicon,{icon:"saved"}))))),(0,o.createElement)("div",{className:"wp-block-fpframework-color-selector-popover-footer"},(0,o.createElement)(d.Button,{isSecondary:!0,isSmall:!0,disabled:!l,onClick:()=>s(i)},(0,e.__)("Clear","fpf-framework"))))),(0,o.createElement)(d.Tooltip,{text:(0,e.__)("Select Color","fpf-framework")},(0,o.createElement)(d.Button,{className:"wp-block-fpframework-color-selector-icon-indicator",onMouseDown:e=>{e.preventDefault(),e.stopPropagation()},onClick:()=>{m((e=>!e))}},(0,o.createElement)(d.ColorIndicator,{className:"wp-block-fpframework-color-selector-indicator"+(""===l?" is-empty":""),colorValue:l})))))),a&&(0,o.createElement)("div",{className:"wp-block-fpframework-color-selector-help"},a)))},k=r(277),v=r.n(k);const g={attributes:v().object.isRequired,setAttributes:v().func.isRequired,attributePrefix:v().string,maxBorderWidth:v().number,showBorderRadius:v().bool,defaultValues:v().shape({color:v().string,radius:v().object,width:v().string})},_=({attributes:t,setAttributes:r,attributePrefix:n="block",title:l="",defaultValues:a={},maxBorderWidth:i=100,showBorderRadius:s=!0})=>{const c=n?t[`${n}BorderColor`]:t.borderColor,p=n?t[`${n}BorderWidth`]:t.borderWidth,m=n?t[`${n}BorderStyle`]:t.borderStyle,f=n?`${n}BorderRadius`:"borderRadius";return(0,o.createElement)(u.Fragment,null,(0,o.createElement)(d.SelectControl,{label:l,value:m,options:[{value:"none",label:(0,e.__)("None","fpf-framework")},{value:"solid",label:(0,e.__)("Solid","fpf-framework")},{value:"dotted",label:(0,e.__)("Dotted","fpf-framework")},{value:"dashed",label:(0,e.__)("Dashed","fpf-framework")},{value:"double",label:(0,e.__)("Double","fpf-framework")},{value:"groove",label:(0,e.__)("Groove","fpf-framework")},{value:"ridge",label:(0,e.__)("Ridge","fpf-framework")}],onChange:e=>{r(n?{[`${n}BorderStyle`]:e}:{borderStyle:e})}}),"none"!==m&&!!m&&(0,o.createElement)(u.Fragment,null,(0,o.createElement)(h,{value:c||"",defaultValue:a.color||"",label:(0,e.__)("Border color","fpf-framework"),disableAlpha:!1,onChange:e=>{r(n?{[`${n}BorderColor`]:e}:{borderColor:e})}}),(0,o.createElement)(d.RangeControl,{label:(0,e.__)("Border Width (px)","fpf-framework"),min:0,max:i,value:p,onChange:e=>{r(n?{[`${n}BorderWidth`]:e}:{borderWidth:e})},step:1,initialPosition:a.width||0,allowReset:!0})),s&&(0,o.createElement)(b,null,(n=>(0,o.createElement)(w,{label:(0,e.__)("Border Radius (px)","fpf-framework"),attributeKey:f,attributes:t,setAttributes:r,breakpoint:n,positions:["top_left","top_right","bottom_right","bottom_left"],positionsLabels:{top_left:`<div>${(0,e.__)("TOP","fpf-framework")}</div><div>${(0,e.__)("LEFT","fpf-framework")}</div>`,top_right:`<div>${(0,e.__)("TOP","fpf-framework")}</div><div>${(0,e.__)("RIGHT","fpf-framework")}</div>`,bottom_right:`<div>${(0,e.__)("BOTTOM","fpf-framework")}</div><div>${(0,e.__)("RIGHT","fpf-framework")}</div>`,bottom_left:`<div>${(0,e.__)("BOTTOM","fpf-framework")}</div><div>${(0,e.__)("LEFT","fpf-framework")}</div>`}}))))};_.propTypes=g;var x=_;const E={value:v().object.isRequired,onUpdate:v().func.isRequired},y=({value:t,onUpdate:r})=>{const{type:n,top:l,left:a,spread:i,color:s,width:c}=null!=t?t:{},p=e=>{r({...t,...e})};return(0,o.createElement)(u.Fragment,null,(0,o.createElement)(d.SelectControl,{label:(0,e.__)("Type","fpf-framework"),value:n,options:[{value:"none",label:(0,e.__)("None","fpf-framework")},{value:"outset",label:(0,e.__)("Outset","fpf-framework")},{value:"inset",label:(0,e.__)("Inset","fpf-framework")}],onChange:e=>{p({type:e})}}),"none"!==n&&!!n&&(0,o.createElement)(u.Fragment,null,(0,o.createElement)(h,{wrapperClass:"fpframework-gutenberg-p-t-0",value:s,defaultValue:"",label:(0,e.__)("Color","fpf-framework"),disableAlpha:!1,onChange:e=>{p({color:e})}}),(0,o.createElement)(d.RangeControl,{label:(0,e.__)("Blur (px)","fpf-framework"),min:0,max:100,value:c,onChange:e=>{p({width:e||0})},step:1,initialPosition:7,allowReset:!0}),(0,o.createElement)(d.RangeControl,{label:(0,e.__)("Spread (px)","fpf-framework"),min:0,max:100,value:i,onChange:e=>{p({spread:e||0})},step:1,initialPosition:0,allowReset:!0}),(0,o.createElement)(d.RangeControl,{label:(0,e.__)("Left (px)","fpf-framework"),min:-100,max:100,value:a,onChange:e=>{p({left:e||0})},step:1,initialPosition:0,allowReset:!0}),(0,o.createElement)(d.RangeControl,{label:(0,e.__)("Top (px)","fpf-framework"),min:-100,max:100,value:l,onChange:e=>{p({top:e||0})},step:1,initialPosition:0,allowReset:!0})))};y.propTypes=E;var C=y,B=window.wp.primitives,$=(0,u.createElement)(B.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,u.createElement)(B.Path,{d:"M20.5 16h-.7V8c0-1.1-.9-2-2-2H6.2c-1.1 0-2 .9-2 2v8h-.7c-.8 0-1.5.7-1.5 1.5h20c0-.8-.7-1.5-1.5-1.5zM5.7 8c0-.3.2-.5.5-.5h11.6c.3 0 .5.2.5.5v7.6H5.7V8z"})),O=(0,u.createElement)(B.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,u.createElement)(B.Path,{d:"M17 4H7c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H7c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h10c.3 0 .5.2.5.5v12zm-7.5-.5h4V16h-4v1.5z"})),S=(0,u.createElement)(B.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,u.createElement)(B.Path,{d:"M15 4H9c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm.5 14c0 .3-.2.5-.5.5H9c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h6c.3 0 .5.2.5.5v12zm-4.5-.5h2V16h-2v1.5z"}));const I=e=>!!e&&Object.values(e).some((e=>null!=e&&""!==e)),N="desktop",R="tablet",T="mobile",P={[N]:"",[R]:991,[T]:575},j=(e=[])=>{if(!e||!e.length)return[];const t=e.reduce(((e,t)=>{const r=(e=>{if(!e)return"";const t=e.match(/[^{\}]+(?=})/gi)[0];return{selector:e.replace(t,"").replace("{","").replace("}","").trim(),style:t.trim()}})(t),o=e.find((({selector:e})=>e===r.selector));return o?(o.styles.push(r.style),e):(e.push({selector:r.selector,styles:[r.style]}),e)}),[]);return t.map((e=>`${e.selector} { ${e.styles.join(" ")} }`))},A=e=>({...e,rules:j(e.rules)}),q=({id:t,value:r,rule:o,unit:n="",edgeCase:l,breakpoint:a="desktop",oneline:i=!1})=>{if(null==r||""===r)return null;let s=o.includes("[BLOCK_ID]")?o.replace("[BLOCK_ID]",t):`${o}`;if((e=>["box-shadow"].some((t=>e.includes(t))))(o))return["inset","outset"].includes(r.type)&&""!==r.color?(0,e.sprintf)(s,`${r.left}px ${r.top}px ${r.width}px ${r.spread}px ${"outset"===r.type?"":r.type} ${r.color}`):null;if((e=>["text-shadow"].some((t=>e.includes(t))))(o))return""!==r.hpos&&""!==r.vpos&&""!==r.blur&&""!==r.color?(0,e.sprintf)(s,`${r.hpos}px ${r.vpos}px ${r.blur}px ${r.color}`):null;if((e=>["padding","border-radius","margin","position-items"].some((t=>e.includes(t)))&&!["padding-","margin-"].some((t=>e.includes(t))))(o)){let t=o.match(/margin|border-radius|padding|position-items/gi)[0];if("position-items"===t){const e=["top","right","bottom","left"].map((e=>{if(null!=r[e]&&""!==r[e])return`${e}: ${"auto"===r[e]?"auto":`${r[e]}${n}`};`})).join(" ");return e.trim()?(c=` ${e} `,s.replace(/[^{\}]+(?=})/gi,c)):null}{const o=((e,t,r="",o=!1)=>{if(!e)return!1;if(I(e)||I(e[t])){const n=e[t]||e;if(["margin","padding"].includes(r)){if(4===Object.values(n).length&&1===new Set(Object.values(n)).size)return`${n.top}px`;if(Object.values(n).every((e=>""!==e))){if(n.top===n.bottom&&n.left===n.right)return`${n.top||0}px ${n.left||0}px`;Object.values(n).map((function(e){return`${e||0}px`})).join(" ")}let e=[],t=["top","right","bottom","left"];return o?(t.forEach((t=>{e.push(`${n[t]||0}px`)})),e.join(" ")):(t.forEach((t=>{""!==n[t]&&e.push(r+"-"+t+":"+n[t]+"px;")})),e)}if("border-radius"===r){if(4===Object.values(n).length&&1===new Set(Object.values(n)).size)return`${n.top_left}px`;if(Object.values(n).every((e=>""!==e))){if(n.top_left===n.bottom_right&&n.bottom_left===n.top_right)return`${n.top_left||0}px ${n.bottom_left||0}px`;Object.values(n).map((function(e){return`${e||0}px`})).join(" ")}let e=[],t=["top_left","top_right","bottom_right","bottom_left"];return o?(t.forEach((t=>{e.push(`${n[t]||0}px`)})),e.join(" ")):(t.forEach((t=>{""!==n[t]&&e.push("border-"+t.replace("_","-")+"-radius:"+n[t]+"px;")})),e)}}return!1})(r,null,t,i);if(!o)return null;if(o&&"string"==typeof o)return(0,e.sprintf)(s,o)||null;if(o&&"object"==typeof o)return s=s.replace("margin: ",""),s=s.replace("padding: ",""),s=s.replace("border-radius: ",""),s=s.replace("%s;","%s"),(0,e.sprintf)(s,o.join(""))||null}}var c;if((e=>!!e&&"object"==typeof e&&"url"in e&&"attachment"in e&&"repeat"in e)(r)){if(!r.url)return;const t=[`background-image: ${"none"!==r.url?`url(${r.url})`:"none"};`,`background-repeat: ${r.repeat};`,`background-size: ${r.size};`,`background-position: ${r.position};`,`background-attachment: ${r.attachment};`].join(" ");return(0,e.sprintf)(s,t)}if(l){const t=l.find((({edge:e})=>e===r));if(t)return""===t.value||t.skipInBreakpoints&&t.skipInBreakpoints.includes(a)?null:(n=t.unit||"",(0,e.sprintf)(s,`${t.value}${n}`))}return(0,e.sprintf)(s,`${r}${n}`)},D=e=>u.Children.toArray(e).map((e=>Array.isArray(e.props.children)?D(e.props.children):e)).reduce(((e,t)=>Array.isArray(t)?[...e,...t]:[...e,t]),[]).filter((e=>e.type&&"Rule"===e.type.displayName)),V=({desktop:e="",tablet:t="",mobile:r=""}={})=>({desktop:e,tablet:t,mobile:r}),L=[{icon:$,title:(0,e.__)("Hide on desktop","fpf-framework"),breakpoint:N,value:"fpframework-hidden-desktop"},{icon:O,title:(0,e.__)("Hide on tablet","fpf-framework"),breakpoint:R,value:"fpframework-hidden-tablet"},{icon:S,title:(0,e.__)("Hide on mobile","fpf-framework"),breakpoint:T,value:"fpframework-hidden-mobile"}],z={values:v().shape({desktop:v().bool,tablet:v().bool,mobile:v().bool}),onChange:v().func.isRequired,label:v().string,isCollapsed:v().bool},H=({values:e=V(),onChange:t,label:r=""})=>(0,o.createElement)(d.BaseControl,null,""!==r&&(0,o.createElement)("div",{className:"fpframework-gutenberg-m-b-10"},r),L.map((r=>{const n=!!e[r.breakpoint];return(0,o.createElement)(d.ToggleControl,{key:r.breakpoint,label:r.title,checked:n,onChange:()=>t({...e,[r.breakpoint]:!n})})})));H.propTypes=z;var W=H;const K={value:v().string.isRequired,label:v().string,className:v().string,onUpdate:v().func.isRequired},M=({value:t,className:r="",label:n=(0,e.__)("Orientation","fpf-framework"),onUpdate:l})=>{const a=e=>{l(e.target.closest(".wp-fpframework-flex-control-item").dataset.orientation)};return(0,o.createElement)("div",{className:"wp-fpframework-flex-control-wrapper"+(r?" "+r:"")},(0,o.createElement)("div",{className:"control-title fpframework-gutenberg-m-b-5"},n),(0,o.createElement)("div",{className:"wp-fpframework-flex-control"},(0,o.createElement)(d.Button,{className:"wp-fpframework-flex-control-item"+("row"===t?" active":""),"data-orientation":"row",onClick:a},(0,o.createElement)("svg",{width:"24",height:"24",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24","aria-hidden":"true",focusable:"false"},(0,o.createElement)("path",{fill:"currentColor",d:"M14.3 6.7l-1.1 1.1 4 4H4v1.5h13.3l-4.1 4.4 1.1 1.1 5.8-6.3z"}))),(0,o.createElement)(d.Button,{className:"wp-fpframework-flex-control-item"+("column"===t?" active":""),"data-orientation":"column",onClick:a},(0,o.createElement)("svg",{width:"24",height:"24",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24","aria-hidden":"true",focusable:"false"},(0,o.createElement)("path",{fill:"currentColor",d:"M16.2 13.2l-4 4V4h-1.5v13.3l-4.5-4.1-1 1.1 6.2 5.8 5.8-5.8-1-1.1z"})))))};M.propTypes=K;var U=M;const G={value:v().string.isRequired,label:v().string,attributesKey:v().string,breakpoint:v().string,attributes:v().object,onUpdate:v().func.isRequired},F=({value:t,label:r=(0,e.__)("Justification","fpf-framework"),attributesKey:n="orientation",breakpoint:l="",attributes:a,onUpdate:i})=>{let s=!1;l&&a[n]&&a[n][l]&&(s=a[n][l]);const c=e=>{i(e.target.closest(".wp-fpframework-flex-control-item").dataset.justification)};return(0,o.createElement)("div",{className:"wp-fpframework-flex-control-wrapper"},(0,o.createElement)("div",{className:"control-title fpframework-gutenberg-m-b-5"},r),(0,o.createElement)("div",{className:"wp-fpframework-flex-control"},(0,o.createElement)(d.Button,{className:"wp-fpframework-flex-control-item"+("flex-start"===t?" active":""),"data-justification":"flex-start",onClick:c},(0,o.createElement)("svg",{width:"24",height:"24",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24","aria-hidden":"true",focusable:"false"},(0,o.createElement)("path",{fill:"currentColor",d:"M9 9v6h11V9H9zM4 20h1.5V4H4v16z"}))),(0,o.createElement)(d.Button,{className:"wp-fpframework-flex-control-item"+("center"===t?" active":""),"data-justification":"center",onClick:c},(0,o.createElement)("svg",{width:"24",height:"24",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24","aria-hidden":"true",focusable:"false"},(0,o.createElement)("path",{fill:"currentColor",d:"M20 9h-7.2V4h-1.6v5H4v6h7.2v5h1.6v-5H20z"}))),(0,o.createElement)(d.Button,{className:"wp-fpframework-flex-control-item"+("flex-end"===t?" active":""),"data-justification":"flex-end",onClick:c},(0,o.createElement)("svg",{width:"24",height:"24",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24","aria-hidden":"true",focusable:"false"},(0,o.createElement)("path",{fill:"currentColor",d:"M4 15h11V9H4v6zM18.5 4v16H20V4h-1.5z"}))),"row"===s&&(0,o.createElement)(d.Button,{className:"wp-fpframework-flex-control-item"+("space-between"===t?" active":""),"data-justification":"space-between",onClick:c},(0,o.createElement)("svg",{width:"24",height:"24",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24","aria-hidden":"true",focusable:"false"},(0,o.createElement)("path",{fill:"currentColor",d:"M9 15h6V9H9v6zm-5 5h1.5V4H4v16zM18.5 4v16H20V4h-1.5z"})))))};F.propTypes=G;var J=F;const Y={attributes:l().object.isRequired,setAttributes:l().func.isRequired},Q=({attributes:t,setAttributes:r})=>{const{orientation:n,boxShadow:l,backgroundColor:i,gap:s,flexWrap:p,flexGrow:m,horizontalContentAlignment:f,visibility:k}=t,v=(0,c.useSelect)((e=>e("fpframework-breakpoints").getBreakpoint()));return(0,o.createElement)(u.Fragment,null,(0,o.createElement)(a.InspectorControls,null,(0,o.createElement)(d.PanelBody,{title:(0,e.__)("Row","firebox"),initialOpen:!0},(0,o.createElement)(b,null,(n=>(0,o.createElement)(J,{label:(0,e.__)("Justification","firebox"),value:f[n],breakpoint:n,attributes:t,onUpdate:e=>r({horizontalContentAlignment:{...f,[n]:f[n]===e?"":e}})}))),(0,o.createElement)(b,null,(t=>(0,o.createElement)(U,{label:(0,e.__)("Orientation","firebox"),value:n[t],onUpdate:e=>r({orientation:{...n,[t]:e}})}))),(0,o.createElement)(b,null,(t=>(0,o.createElement)(d.RangeControl,{label:(0,e.__)("Content Gap (px)","firebox"),min:0,max:500,value:s[t],allowReset:!0,onChange:e=>{r({gap:{...s,[t]:null!=e?e:""}})}}))),("row"===n[v]||""===n[v])&&(0,o.createElement)(u.Fragment,null,(0,o.createElement)(b,{childrenClass:"fpframework-gutenberg-p-t-25"},(t=>(0,o.createElement)(d.ToggleControl,{label:(0,e.__)("Allow to wrap to multiple lines","firebox"),checked:"wrap"===p[t],onChange:e=>r({flexWrap:{...p,[t]:e?"wrap":""}})}))),(0,o.createElement)(d.ToggleControl,{label:(0,e.__)("Grow all child elements equally","firebox"),checked:m,onChange:e=>r({flexGrow:e})})),(0,o.createElement)(b,null,(n=>(0,o.createElement)(w,{label:(0,e.__)("Margin (px)","firebox"),attributeKey:"margin",attributes:t,setAttributes:r,breakpoint:n}))),(0,o.createElement)(b,null,(n=>(0,o.createElement)(w,{label:(0,e.__)("Padding (px)","firebox"),attributeKey:"padding",attributes:t,setAttributes:r,breakpoint:n})))),(0,o.createElement)(d.PanelBody,{title:(0,e.__)("Colors","firebox"),initialOpen:!1},(0,o.createElement)(h,{label:(0,e.__)("Background Color","firebox"),value:i||"",onChange:e=>r({backgroundColor:e})})),(0,o.createElement)(d.PanelBody,{title:(0,e.__)("Border","firebox"),initialOpen:!1},(0,o.createElement)(x,{attributes:t,setAttributes:r})),(0,o.createElement)(d.PanelBody,{title:(0,e.__)("Box Shadow","firebox"),initialOpen:!1},(0,o.createElement)(b,null,(e=>(0,o.createElement)(C,{value:l[e],onUpdate:t=>r({boxShadow:{...l,[e]:t}})})))),(0,o.createElement)(d.PanelBody,{title:(0,e.__)("Visibility Settings","firebox"),initialOpen:!1},(0,o.createElement)(W,{values:k,onChange:e=>r({visibility:e})}))))};Q.propTypes=Y;var X=Q;const Z={children:v().oneOfType([v().node,v().array]),id:v().string},ee=({id:e="",children:t})=>{const r=(({id:e,children:t,getCSSRule:r,mapper:o=(e=>e)})=>{const n=[{name:"desktop",media:"max",width:P.desktop,rules:[]},{name:"tablet",media:"max",width:P.tablet,rules:[]},{name:"mobile",media:"max",width:P.mobile,rules:[]},{name:"desktop-only",media:"min",width:P.tablet+1,rules:[]},{name:"tablet-only",media:"min",width:P.mobile+1,rules:[]}];return D(t).reduce(((t,o)=>{const{value:n,rule:l,unit:a,edgeCase:i,breakpointLimit:s,oneline:c}=o.props;if(null==n)return t;if("object"!=typeof n||!("desktop"in n)){const o=t.find((({name:e})=>"desktop"===e)),s=r({id:e,value:n,rule:l,unit:a,edgeCase:i,breakpoint:"desktop",oneline:c});return null!=s&&o.rules.push(s),t}return Object.keys(n).forEach((o=>{const p=t.find((({name:e})=>s&&"mobile"!==o?`${o}-only`===e:e===o)),u=r({id:e,value:n[o],rule:l,unit:a,edgeCase:i,breakpoint:o,oneline:c});null!=u&&p&&p.rules.push(u)})),t}),n).map(o)})({id:e,children:t,getCSSRule:q,mapper:A});return(e=>e.every((e=>0===e.rules.length)))(r)?null:(0,o.createElement)("style",{dangerouslySetInnerHTML:{__html:(n=r,n.reduce(((e,t)=>{if(!t.rules.length)return e;const r=t.rules.map((e=>e.trim())).join("\n");return t.width?"tablet-only"===t.name?`${e}\n\n\t\t\t\t@media (min-width: ${P.mobile+1}px) and (max-width: ${P.tablet}px) {\n\t\t\t\t\t${r}\n\t\t\t\t}\n\t\t\t\t`:`${e}\n\n\t\t\t@media (${t.media}-width: ${t.width}px) {\n\t\t\t\t${r}\n\t\t\t}\n\t\t\t`:`${e}${r}\n`}),"").trim())}});var n};ee.propTypes=Z;var te=ee;const re={value:v().oneOfType([v().string,v().number,v().object]).isRequired,rule:v().string.isRequired,unit:v().oneOf(["","px","%","em","rem","vh","vw","pt","cm","mm"]),edgeCase:v().arrayOf(v().shape({edge:v().any.isRequired,value:v().oneOfType([v().string,v().number]).isRequired,skipInBreakpoints:v().array})),breakpointLimit:v().bool,oneline:v().bool},oe=()=>null;oe.displayName="Rule",oe.propTypes=re;var ne=oe;const le=({attributes:e,prefix:t="block"})=>{const r=(({attributes:e,prefix:t="block"})=>{const r=t?`${t}BorderColor`:"borderColor",o=e&&r in e?e[r]:null,n=t?`${t}BorderWidth`:"borderWidth",l=e&&n in e?e[n]:null,a=t?`${t}BorderStyle`:"borderStyle",i=e&&a in e?e[a]:null;return i&&"none"!==i?{borderColor:o||void 0,borderStyle:"none"!==i&&i?i:void 0,borderWidth:null!=l?`${l}px`:void 0}:{borderColor:void 0,borderStyle:void 0,borderWidth:void 0}})({attributes:e,prefix:t});return r.borderWidth&&r.borderStyle&&r.borderColor?`${r.borderWidth} ${r.borderStyle} ${r.borderColor}`:""};var ae=({attributes:e,children:t})=>{const{uniqueId:r,padding:n,margin:l,horizontalContentAlignment:a,gap:i,orientation:s,backgroundColor:c,flexWrap:u,blockBorderRadius:d}=e,m=p(r);let f={desktop:"row"===s.desktop?a.desktop:"",tablet:"row"===s.tablet?a.tablet:"",mobile:"row"===s.mobile?a.mobile:""},b={desktop:"row"===s.desktop?"center":a.desktop,tablet:"row"===s.tablet?"center":a.tablet,mobile:"row"===s.mobile?"center":a.mobile};return(0,o.createElement)(te,{id:m},(0,o.createElement)(ne,{value:i,rule:".wp-block-firebox-row.[BLOCK_ID] { gap: %s; }",unit:"px"}),(0,o.createElement)(ne,{value:u,rule:".wp-block-firebox-row.[BLOCK_ID] { flex-wrap: %s; }",unit:""}),(0,o.createElement)(ne,{value:s,rule:".wp-block-firebox-row.[BLOCK_ID] { flex-direction: %s; }",unit:"",edgeCase:[{edge:"",value:""}]}),(0,o.createElement)(ne,{value:le({attributes:e}),rule:".wp-block-firebox-row.[BLOCK_ID] { border: %s; }",unit:""}),(0,o.createElement)(ne,{value:d,rule:".wp-block-firebox-row.[BLOCK_ID] { border-radius: %s; }",unit:""}),(0,o.createElement)(ne,{value:c||"",rule:".wp-block-firebox-row.[BLOCK_ID] { background-color: %s; }"}),(0,o.createElement)(ne,{value:l,rule:".wp-block-firebox-row.[BLOCK_ID] { margin: %s; }",unit:"px"}),(0,o.createElement)(ne,{value:n,rule:".wp-block-firebox-row.[BLOCK_ID] { padding: %s; }",unit:"px"}),(0,o.createElement)(ne,{value:f,rule:".wp-block-firebox-row.[BLOCK_ID] { justify-content: %s; }"}),(0,o.createElement)(ne,{value:b,rule:".wp-block-firebox-row.[BLOCK_ID] { align-items: %s; }"}),(({attributes:e,selector:t=""})=>(0,o.createElement)(ne,{value:e.boxShadow,rule:t+" { box-shadow: %s !important; }",unit:""}))({attributes:e,selector:".wp-block-firebox-row.[BLOCK_ID]"}),t)},ie=window.lodash;const se={className:l().string,attributes:l().object.isRequired,setAttributes:l().func.isRequired},ce=({className:e,attributes:t,setAttributes:r,clientId:n})=>{const{uniqueId:l,flexGrow:i}=t,{addUniqueID:u}=(0,c.useDispatch)("firebox/data"),{isUniqueID:d,isUniqueBlock:m,parentData:f}=(0,c.useSelect)((e=>({isUniqueID:t=>e("firebox/data").isUniqueID(t),isUniqueBlock:(t,r)=>e("firebox/data").isUniqueBlock(t,r),parentData:{rootBlock:e("core/block-editor").getBlock(e("core/block-editor").getBlockHierarchyRootClientId(n)),postId:e("core/editor")?.getCurrentPostId()?e("core/editor")?.getCurrentPostId():"",reusableParent:e("core/block-editor").getBlockAttributes(e("core/block-editor").getBlockParentsByBlockName(n,"core/block").slice(-1)[0]),editedPostId:!!e("core/edit-site")&&e("core/edit-site").getEditedPostId()}})),[n]);useEffect((()=>{const e=function(e){const{postId:t,reusableParent:r,editedPostId:o}=e,n=function(e,t,r="block-unknown"){return(0,ie.has)(t,"ref")?t.ref:e||r}(t,r,0);return 0===n&&o?function(e){for(var t=5381,r=e.length;r;)t=33*t^e.charCodeAt(--r);return t>>>0}(o)%1e6:n}(f);let o=function(e,t,r,o,n="",l=!1){const a=e&&2===e.split("_").length,i=(n?n+"_":"")+t.substr(2,9);return!e||a&&e.split("_")[0]!==n.toString()?r(i)?i:(0,ie.uniqueId)(i):r(e)||o(e,t)?e:l?(0,ie.uniqueId)(i):i}(l,n,d,m,e);o!==l?(t.uniqueId=o,r({uniqueId:o}),u(o,n)):(r({uniqueId:l}),u(l,n))}),[]);const{hasInnerBlocks:b}=(0,c.useSelect)((e=>{const{getBlock:t}=e(a.store),r=t(n);return{hasInnerBlocks:!(!r||!r.innerBlocks.length)}}),[n]),w=p(l),h=(0,a.useBlockProps)({id:w,className:s()(b?"":"fb-has-no-childs",e,w,"wp-block-group is-layout-flex","wp-block-firebox",i?"fb-flex-grow-childs":"")}),k=(0,a.useInnerBlocksProps)(h,{orientation:"horizontal",templateLock:!1,renderAppender:b?void 0:a.InnerBlocks.ButtonBlockAppender});return(0,o.createElement)("div",{className:"wp-block"},(0,o.createElement)(ae,{attributes:t}),(0,o.createElement)(X,{attributes:t,setAttributes:r}),(0,o.createElement)("div",{...k}))};ce.propTypes=se;var pe=ce;const ue=e=>{if(e)return Object.keys(e).reduce(((t,r)=>e[r]?(t.push(`fpframework-hidden-${r}`),t):t),[]).join(" ")};(0,t.registerBlockType)("firebox/row",{icon:()=>(0,o.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 48 48",className:"firebox-gutenberg-block-list-item"},(0,o.createElement)("g",{clipPath:"url(#clip0_1118_1860)"},(0,o.createElement)("mask",{id:"path-1-inside-1_1118_1860",fill:"white"},(0,o.createElement)("rect",{x:"-3",y:"14",width:"25",height:"20",rx:"1"})),(0,o.createElement)("rect",{x:"-3",y:"14",width:"25",height:"20",rx:"1",stroke:"#2438E9",fill:"transparent",strokeWidth:"6",mask:"url(#path-1-inside-1_1118_1860)"}),(0,o.createElement)("mask",{id:"path-2-inside-2_1118_1860",fill:"white"},(0,o.createElement)("rect",{x:"26",y:"14",width:"25",height:"20",rx:"1"})),(0,o.createElement)("rect",{x:"26",y:"14",width:"25",height:"20",rx:"1",stroke:"#2438E9",fill:"transparent",strokeWidth:"6",mask:"url(#path-2-inside-2_1118_1860)"})),(0,o.createElement)("defs",null,(0,o.createElement)("clipPath",{id:"clip0_1118_1860"},(0,o.createElement)("rect",{width:"48",height:"48",fill:"white"})))),edit:pe,save:({attributes:e,className:t})=>{const{uniqueId:r,visibility:n,flexGrow:l}=e,i=p(r);return(0,o.createElement)("div",{...a.useBlockProps.save({id:i,className:s()(t,ue(n),i,"wp-block-firebox",l?"fb-flex-grow-childs":"")})},(0,o.createElement)(ae,{attributes:e}),(0,o.createElement)(a.InnerBlocks.Content,null))}})}()}();