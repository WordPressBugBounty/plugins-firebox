!function(){"use strict";var e=window.React,t=window.wp.plugins,a=window.wp.components;const{Fill:r,Slot:l}=(0,a.createSlotFill)("FireBoxEmailNotificationsSlotFill"),i=({children:t})=>(0,e.createElement)(r,null,t);i.Slot=({fillProps:t})=>(0,e.createElement)(l,{fillProps:t},(e=>e.length?e:null));var o=i,m=window.wp.blockEditor,n=window.wp.element,c=({attributes:t,setAttributes:a,max:r=!1})=>{const{emailNotifications:l}=t,[i,o]=(0,n.useState)(!1),c={label:"",from:"{fpf site.email}",to:"{fpf field.email}",subject:"New Submission #{fpf submission.id}: Contact Form",message:"{fpf all_fields}",fromName:"{fpf site.name}",replyToName:"",replyToEmail:"",cc:"",bcc:"",attachments:""},s=(e,t=null)=>{e.preventDefault(),e.stopPropagation(),a(null!==t?{emailNotifications:[...l.slice(0,t+1),c,...l.slice(t+1)]}:{emailNotifications:[...l,c]})},p=(e,t,r)=>{a({emailNotifications:l.map(((a,l)=>l===e?{...a,[t]:r}:a))})},f=!1===r||r>1;return(0,e.createElement)("div",{className:"firebox-repeater-block-control"},l.map(((t,r)=>(0,e.createElement)("div",{className:"firebox-repeater-block-control--item"+(i===r?" is-visible":""),key:r},(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--top",onClick:e=>((e,t)=>{!1!==i&&i===t&&e.target.closest(".firebox-repeater-block-control--item--top--label")||o(i!==t&&t)})(e,r)},(0,e.createElement)(m.RichText,{tagName:"div",placeholder:"Email Notification Label",className:"firebox-repeater-block-control--item--top--label",value:t.label||`Email Notification #${r+1}`,onChange:e=>p(r,"label",e)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--top--actions"},f&&(0,e.createElement)(e.Fragment,null,(0,e.createElement)("button",{onClick:e=>s(e,r),className:"firebox-repeater-block-control--item--top--actions--add"},(0,e.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"20px",height:"20px",viewBox:"0 -960 960 960",fill:"currentColor"},(0,e.createElement)("path",{d:"M450-450H220v-60h230v-230h60v230h230v60H510v230h-60v-230Z"}))),(0,e.createElement)("button",{onClick:e=>((e,t)=>{e.preventDefault(),e.stopPropagation(),a({emailNotifications:[...l.slice(0,t+1),l[t],...l.slice(t+1)]})})(e,r)},(0,e.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"20px",height:"20px",viewBox:"0 -960 960 960",fill:"currentColor"},(0,e.createElement)("path",{d:"M362.31-260Q332-260 311-281q-21-21-21-51.31v-455.38Q290-818 311-839q21-21 51.31-21h335.38Q728-860 749-839q21 21 21 51.31v455.38Q770-302 749-281q-21 21-51.31 21H362.31Zm0-60h335.38q4.62 0 8.46-3.85 3.85-3.84 3.85-8.46v-455.38q0-4.62-3.85-8.46-3.84-3.85-8.46-3.85H362.31q-4.62 0-8.46 3.85-3.85 3.84-3.85 8.46v455.38q0 4.62 3.85 8.46 3.84 3.85 8.46 3.85Zm-140 200Q192-120 171-141q-21-21-21-51.31v-515.38h60v515.38q0 4.62 3.85 8.46 3.84 3.85 8.46 3.85h395.38v60H222.31ZM350-320v-480 480Z"})))),(f||Object.keys(l).length>1)&&(0,e.createElement)("button",{onClick:e=>((e,t)=>{e.preventDefault(),e.stopPropagation(),a({emailNotifications:l.filter(((e,a)=>a!==t))})})(e,r)},(0,e.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"20px",height:"20px",viewBox:"0 -960 960 960",fill:"currentColor"},(0,e.createElement)("path",{d:"M292.31-140q-29.92 0-51.12-21.19Q220-182.39 220-212.31V-720h-40v-60h180v-35.38h240V-780h180v60h-40v507.69Q740-182 719-161q-21 21-51.31 21H292.31ZM680-720H280v507.69q0 5.39 3.46 8.85t8.85 3.46h375.38q4.62 0 8.46-3.85 3.85-3.84 3.85-8.46V-720ZM376.16-280h59.99v-360h-59.99v360Zm147.69 0h59.99v-360h-59.99v360ZM280-720v520-520Z"}))))),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form"},(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_subject_${r}`},"Subject"),(0,e.createElement)("input",{type:"text",id:`repeater_field_subject_${r}`,value:t.subject,onChange:e=>p(r,"subject",e.target.value)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"Enter a subject line for the email.")),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_to_${r}`},"To"),(0,e.createElement)("input",{type:"text",id:`repeater_field_to_${r}`,value:t.to,onChange:e=>p(r,"to",e.target.value)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"Who will this email be sent to? Multiple email addresses can be entered as a comma separated list, eg. test@example.com, example@example.com.")),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_fromName_${r}`},"From Name"),(0,e.createElement)("input",{type:"text",id:`repeater_field_fromName_${r}`,value:t.fromName,onChange:e=>p(r,"fromName",e.target.value)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"This is the name that is displayed as the sender when receiving the message.")),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_form_${r}`},"From"),(0,e.createElement)("input",{type:"text",id:`repeater_field_form_${r}`,value:t.from,onChange:e=>p(r,"from",e.target.value)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"This is the email address that is displayed as the sender when receiving the message. Many hosts will not allow anything other than your domain administrative email here.")),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_replyToName_${r}`},"Reply-To Name"),(0,e.createElement)("input",{type:"text",id:`repeater_field_replyToName_${r}`,value:t.replyToName,onChange:e=>p(r,"replyToName",e.target.value)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"Use this option if you want the recipient to reply to a different email address other than the one the message is sent from.")),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_replyToEmail_${r}`},"Reply-To Email"),(0,e.createElement)("input",{type:"text",id:`repeater_field_replyToEmail_${r}`,value:t.replyToEmail,onChange:e=>p(r,"replyToEmail",e.target.value)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"Specify the full name of the reply-to email address.")),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_cc_${r}`},"CC"),(0,e.createElement)("input",{type:"text",id:`repeater_field_cc_${r}`,value:t.cc,onChange:e=>p(r,"cc",e.target.value)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"Send this message to secondary recipients. Multiple email addresses can be entered as a comma separated list, eg. test@example.com, example@example.com.")),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_bcc_${r}`},"BCC"),(0,e.createElement)("input",{type:"text",id:`repeater_field_bcc_${r}`,value:t.bcc,onChange:e=>p(r,"bcc",e.target.value)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"Send this message to secondary recipients, but hide the users email address from anyone else receiving this message. Multiple email addresses can be entered as a comma separated list, eg. test@example.com, example@example.com.")),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_message_${r}`},"Body"),(0,e.createElement)("textarea",{id:`repeater_field_message_${r}`,onChange:e=>p(r,"message",e.target.value),rows:"10",defaultValue:t.message}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"Enter the body of the email. You can use HTML formatting or plain text.")),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item"},(0,e.createElement)("label",{htmlFor:`repeater_field_attachments_${r}`},"Attachments"),(0,e.createElement)("input",{type:"text",id:`repeater_field_attachments_${r}`,value:t.attachments,onChange:e=>p(r,"attachments",e.target.value)}),(0,e.createElement)("div",{className:"firebox-repeater-block-control--item--form--item--description"},"Location of a file to attach relative to the root of your webspace. Multiple files can be entered as a comma separated list.")))))),f&&(0,e.createElement)("button",{onClick:s,className:"firebox-repeater-block-control--add"},(0,e.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 -960 960 960",width:"24px",fill:"currentColor"},(0,e.createElement)("path",{d:"M450-450H220v-60h230v-230h60v230h230v60H510v230h-60v-230Z"})),"Add Email Notification"),(0,e.createElement)("div",{className:"firebox-repeater-block-control--footer"},"Personalize your email notifications with Smart Tags. ",(0,e.createElement)("a",{href:"https://www.fireplugins.com/docs/general/plugins/how-to-use-smart-tags/",target:"_blank",className:"blue-link"},"Read more")))},s=window.wp.i18n;const p=t=>{const a=t?.label||(0,s.__)("Unlock Pro Feature","firebox");return(0,e.createElement)("a",{href:"#","data-fpf-modal-item":t.feature,className:"fpf-button upgrade fpf-modal-opener fpframework-gutenberg-font-size-13","data-fpf-modal":"#fpfUpgradeToPro","data-fpf-plugin":t.plugin},(0,e.createElement)("svg",{className:"fpf-upgrade-icon lock-closed",xmlns:"http://www.w3.org/2000/svg",height:"18px",viewBox:"0 -960 960 960",width:"18px",fill:"#e8eaed"},(0,e.createElement)("path",{d:"M263.72-96Q234-96 213-117.15T192-168v-384q0-29.7 21.15-50.85Q234.3-624 264-624h24v-96q0-79.68 56.23-135.84 56.22-56.16 136-56.16Q560-912 616-855.84q56 56.16 56 135.84v96h24q29.7 0 50.85 21.15Q768-581.7 768-552v384q0 29.7-21.16 50.85Q725.68-96 695.96-96H263.72Zm.28-72h432v-384H264v384Zm216.21-120Q510-288 531-309.21t21-51Q552-390 530.79-411t-51-21Q450-432 429-410.79t-21 51Q408-330 429.21-309t51 21ZM360-624h240v-96q0-50-35-85t-85-35q-50 0-85 35t-35 85v96Zm-96 456v-384 384Z"})),(0,e.createElement)("svg",{className:"fpf-upgrade-icon lock-open",xmlns:"http://www.w3.org/2000/svg",height:"18px",viewBox:"0 -960 960 960",width:"18px",fill:"#e8eaed"},(0,e.createElement)("path",{d:"M264-624h336v-96q0-50-35-85t-85-35q-50 0-85 35t-35 85h-72q0-80 56.23-136 56.22-56 136-56Q560-912 616-855.84q56 56.16 56 135.84v96h24q29.7 0 50.85 21.15Q768-581.7 768-552v384q0 29.7-21.16 50.85Q725.68-96 695.96-96H263.72Q234-96 213-117.15T192-168v-384q0-29.7 21.15-50.85Q234.3-624 264-624Zm0 456h432v-384H264v384Zm216.21-120Q510-288 531-309.21t21-51Q552-390 530.79-411t-51-21Q450-432 429-410.79t-21 51Q408-330 429.21-309t51 21ZM264-168v-384 384Z"})),(0,e.createElement)("span",{className:"text"},a))};(0,t.registerPlugin)("custom-slot-fills",{render:()=>(0,e.createElement)(o,null,(({attributes:t,setAttributes:a})=>(0,e.createElement)(e.Fragment,null,(0,e.createElement)(c,{attributes:t,setAttributes:a,max:1}),(0,e.createElement)("div",{className:"fpframework-gutenberg-m-t-24"},(0,e.createElement)("h3",{className:"fpframework-gutenberg-m-b-8"},__("Unlimited Email Notifications","firebox")),(0,e.createElement)(p,{feature:__("Unlimited Email Notifications","firebox"),plugin:"FireBox"})))))})}();