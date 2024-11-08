(()=>{"use strict";const e=window.React,t=window.wp.compose,i=window.wp.element,n=window.wp.blockEditor,a=window.wp.components,l=window.wp.i18n,o=e=>`<a href="https://www.fireplugins.com/docs/firebox/how-to-connect-firebox-with-${e}/?utm_source=product&utm_campaign=firebox=${"lite"===fbox_admin_editor_js_object.license_type?"free":"pro"}&utm_medium=misc&utm_content=${e}#find_my_api_key" target="_blank">${(0,l.__)("Where can I find my API Key?")}</a>`;wp.hooks.addFilter("blocks.registerBlockType","firebox/integrations-extend",(function(e,t){return"firebox/form"!==t||(e.attributes.integrations.default.push({label:(0,l.__)("AcyMailing","firebox"),value:"AcyMailing"}),e.attributes.acymailingListID={type:"string",default:""},e.attributes.acymailingDoubleOptin={type:"boolean",default:!1}),e}));const s=(0,t.createHigherOrderComponent)((t=>s=>{const{attributes:r,setAttributes:c}=s,{actions:m,acymailingListID:g,acymailingDoubleOptin:p}=r,[d,y]=(0,i.useState)(!1);return(0,e.createElement)(i.Fragment,null,(0,e.createElement)(t,{...s}),(0,e.createElement)(n.InspectorControls,null,m?.AcyMailing&&(0,e.createElement)(a.PanelBody,{title:(0,l.__)("AcyMailing Integration","firebox"),initialOpen:!1,onToggle:e=>((e,t)=>{let i=t.toLowerCase();y((e=>({...e,[i]:{...e[i],panelOpen:!0}}))),e&&!d?.[i]?.lists&&((e="skip",t,i)=>{let n=t.toLowerCase();if(i((e=>({...e,[n]:{...e[n],lists:!1,message:{type:"",message:""}}}))),""===e?.trim()&&"skip"!==e)return void i((e=>({...e,[n]:{...e[n],lists:!1,message:{type:"info",message:(0,l.__)("Please enter an API Key.","firebox")+"<div>"+o(n)+"</div>"}}})));let a=new FormData;a.append("nonce",fpf_js_object.nonce),null!==e&&a.append("api_key",e),a.append("integration",t),a.append("action","fb_get_integration_lists"),fetch(fpf_js_object.ajax_url,{method:"POST",body:a}).then((function(e){return e.json()})).then((function(e){let t=!1,a="",l=!1;if(e.error){let t=e.message;e.help&&(t+=" <div>"+e.help+"</div>"),a="error",l=t}else t=e.message.lists;i((e=>({...e,[n]:{...e[n],lists:t,message:{type:a,message:l}}})))}))})("skip",t,y)})(e,"AcyMailing")},""===d?.acymailing?.message?.type&&!d?.acymailing?.lists&&(0,e.createElement)("div",null,(0,l.__)("Loading AcyMailing lists...","firebox")),"info"===d?.acymailing?.message?.type&&(0,e.createElement)("div",{dangerouslySetInnerHTML:{__html:d?.acymailing?.message?.message}}),"error"===d?.acymailing?.message?.type&&(0,e.createElement)("div",{className:"fpframework-color-red",dangerouslySetInnerHTML:{__html:(0,l.__)("AcyMailing error:","firebox")+" "+d?.acymailing?.message?.message}}),""===d?.acymailing?.message?.type&&d?.acymailing?.lists&&(0,e.createElement)(i.Fragment,null,(0,e.createElement)(SelectControl,{label:(0,l.__)("List","firebox"),value:g,options:d?.acymailing?.lists,onChange:e=>c({acymailingListID:e})}),(0,e.createElement)(ToggleControl,{label:(0,l.__)("Double Optin","firebox"),default:!1,checked:p,onChange:e=>c({acymailingDoubleOptin:e})})))))}),"acymailingPanel");wp.hooks.addFilter("editor.BlockEdit","firebox/integrations/acymailing/panel",s)})();