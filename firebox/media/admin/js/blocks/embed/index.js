(()=>{var e={184:(e,t)=>{var r;!function(){"use strict";var o={}.hasOwnProperty;function n(){for(var e=[],t=0;t<arguments.length;t++){var r=arguments[t];if(r){var i=typeof r;if("string"===i||"number"===i)e.push(r);else if(Array.isArray(r)){if(r.length){var a=n.apply(null,r);a&&e.push(a)}}else if("object"===i){if(r.toString!==Object.prototype.toString&&!r.toString.toString().includes("[native code]")){e.push(r.toString());continue}for(var s in r)o.call(r,s)&&r[s]&&e.push(s)}}}return e.join(" ")}e.exports?(n.default=n,e.exports=n):void 0===(r=function(){return n}.apply(t,[]))||(e.exports=r)}()},703:(e,t,r)=>{"use strict";var o=r(414);function n(){}function i(){}i.resetWarningCache=n,e.exports=function(){function e(e,t,r,n,i,a){if(a!==o){var s=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw s.name="Invariant Violation",s}}function t(){return e}e.isRequired=e;var r={array:e,bigint:e,bool:e,func:e,number:e,object:e,string:e,symbol:e,any:e,arrayOf:t,element:e,elementType:e,instanceOf:t,node:e,objectOf:t,oneOf:t,oneOfType:t,shape:t,exact:t,checkPropTypes:i,resetWarningCache:n};return r.PropTypes=r,r}},697:(e,t,r)=>{e.exports=r(703)()},414:e=>{"use strict";e.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"}},t={};function r(o){var n=t[o];if(void 0!==n)return n.exports;var i=t[o]={exports:{}};return e[o](i,i.exports,r),i.exports}r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var o in t)r.o(t,o)&&!r.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";const e=window.wp.blocks,t=window.React,o=window.wp.i18n,n=window.wp.blockEditor,i=window.wp.data,a=window.wp.components,s=window.wp.element,c=function(e){let{icon:t,size:r=24,...o}=e;return(0,s.cloneElement)(t,{width:r,height:r,...o})},l=window.wp.primitives,d=(0,s.createElement)(l.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,s.createElement)(l.Path,{d:"M16 11.2h-3.2V8h-1.6v3.2H8v1.6h3.2V16h1.6v-3.2H16z"}));var u=r(184),p=r.n(u);const b=window.wp.serverSideRender;var f=r.n(b),m=r(697),g=r.n(m);const h={attributes:g().object.isRequired,setAttributes:g().func.isRequired},w=({attributes:e,setAttributes:r})=>{const{campaign:i}=e,[c,l]=(0,s.useState)(!0),[d,u]=(0,s.useState)([]);return useEffect((()=>{wp.apiFetch({path:"/firebox/embeds"}).then((e=>{let t=[];firebox_parse_boxes(e).forEach((e=>{e.value!=wp.data.select("core/editor").getCurrentPostId()&&t.push({value:e.value,label:e.label})})),u(t),l(!1)}))}),[]),(0,t.createElement)(n.InspectorControls,null,(0,t.createElement)(a.PanelBody,{title:(0,o.__)("Embed Campaign","firebox"),initialOpen:!0},c?(0,o.__)("Loading FireBox campaigns...","firebox"):d.length>0?(0,t.createElement)(a.ComboboxControl,{label:(0,o.__)("FireBox Campaign","fireplugins"),options:d,value:i,onChange:e=>r({campaign:e}),help:(0,o.__)("Select a FireBox campaign to embed.","firebox")}):(0,o.__)("No embed FireBox campaigns found. Please create a FireBox campaign with embed mode enabled in order to embed it.","firebox")))};w.propTypes=h;const x=w,v=window.lodash;const _=()=>(0,t.createElement)(a.Placeholder,{icon:(0,t.createElement)(c,{icon:d}),label:(0,o.__)("Embed FireBox Campaign (Experimental)","firebox")},(0,o.__)("This campaign cannot be embedded. Please ensure it exists and that it's enabled.","firebox"));"firebox"!==window.typenow&&(0,e.registerBlockType)("firebox/embed",{icon:()=>(0,t.createElement)("svg",{width:"24",height:"24",viewBox:"0 0 48 48",fill:"none",xmlns:"http://www.w3.org/2000/svg"},(0,t.createElement)("circle",{cx:"24",cy:"24",r:"20",stroke:"#2438E9",strokeWidth:"3",fill:"white"}),(0,t.createElement)("path",{d:"M31 22.2679C32.3333 23.0377 32.3333 24.9623 31 25.7321L22 30.9282C20.6667 31.698 19 30.7358 19 29.1962L19 18.8038C19 17.2642 20.6667 16.302 22 17.0718L31 22.2679Z",stroke:"#2438E9",strokeWidth:"3"})),edit:({className:e,attributes:r,setAttributes:s,clientId:c})=>{const{uniqueId:l,campaign:d}=r,{addUniqueID:u}=(0,i.useDispatch)("firebox/data"),{isUniqueID:b,isUniqueBlock:m,parentData:g}=(0,i.useSelect)((e=>({isUniqueID:t=>e("firebox/data").isUniqueID(t),isUniqueBlock:(t,r)=>e("firebox/data").isUniqueBlock(t,r),parentData:{rootBlock:e("core/block-editor").getBlock(e("core/block-editor").getBlockHierarchyRootClientId(c)),postId:e("core/editor")?.getCurrentPostId()?e("core/editor")?.getCurrentPostId():"",reusableParent:e("core/block-editor").getBlockAttributes(e("core/block-editor").getBlockParentsByBlockName(c,"core/block").slice(-1)[0]),editedPostId:!!e("core/edit-site")&&e("core/edit-site").getEditedPostId()}})),[c]);useEffect((()=>{const e=function(e){const{postId:t,reusableParent:r,editedPostId:o}=e,n=function(e,t,r="block-unknown"){return(0,v.has)(t,"ref")?t.ref:e||r}(t,r,0);return 0===n&&o?function(e){for(var t=5381,r=e.length;r;)t=33*t^e.charCodeAt(--r);return t>>>0}(o)%1e6:n}(g);let t=function(e,t,r,o,n="",i=!1){const a=e&&2===e.split("_").length,s=(n?n+"_":"")+t.substr(2,9);return!e||a&&e.split("_")[0]!==n.toString()?r(s)?s:(0,v.uniqueId)(s):r(e)||o(e,t)?e:i?(0,v.uniqueId)(s):s}(l,c,b,m,e);t!==l?(r.uniqueId=t,s({uniqueId:t}),u(t,c)):(s({uniqueId:l}),u(l,c))}),[]);const h=(e=>`block-${e}`)(l),w=(0,n.useBlockProps)({id:c,className:p()(e,h,{"wp-block":!0,"wp-block-firebox":!0})});return(0,t.createElement)("div",{...w},d?(0,t.createElement)(a.Disabled,null,(0,t.createElement)(f(),{skipBlockSupportAttributes:!0,block:"firebox/embed",attributes:{...r},urlQueryArgs:{editor:!0},EmptyResponsePlaceholder:_})):(0,t.createElement)("div",{style:{display:"flex",justifyContent:"center",alignItems:"center",gap:"16px",background:"#f5f5f5",borderRadius:"5px",flexDirection:"column",padding:"60px 24px"}},(0,t.createElement)("img",{src:`${fbox_admin_editor_js_object.media_url}admin/images/logo_full.svg`,width:"120",alt:"logo"}),(0,o.__)("Please select a campaign to embed.","firebox")),(0,t.createElement)(x,{attributes:r,setAttributes:s}))},save:()=>null})})()})();