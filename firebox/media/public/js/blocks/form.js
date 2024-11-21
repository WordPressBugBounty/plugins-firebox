var FireBox_Form=function(){function e(e){this.form=e,this.submitButtonClass=".firebox-block-button-element",this.formControlGroupClass=".fb-form-control-group",this.formInputClass=".fb-form-input",this.formMessageClass=".fb-form-response",this.skipEmptyValidation=!0,this.requiredFieldMessage=this.form.querySelector(".fb_form_required_field_message").value,this.invalidEmailMessage=this.form.querySelector(".fb_form_invalid_email_message").value,this.honeypotFieldTriggered=this.form.querySelector(".fb_form_honeypot_field_triggered")?this.form.querySelector(".fb_form_honeypot_field_triggered").value:"",this.initEvents()}var t=e.prototype;return t.initEvents=function(){var t=this;this.prepareFormButtons(),this.form.querySelector(this.submitButtonClass)&&this.form.querySelector(this.submitButtonClass).addEventListener("click",this.submitForm.bind(this)),this.form.addEventListener("focusout",function(e){t.updateFieldStateWhileTyping(e)}),this.form.addEventListener("keydown",function(e){t.onFormKeyDown(e)})},t.prepareFormButtons=function(){this.form.querySelectorAll(".firebox-block-button-element").forEach(function(e){e.setAttribute("tabindex",0)})},t.updateFieldStateWhileTyping=function(e){var t,r=e.target.closest(this.formInputClass);r&&((t=this.validateField(r)).error?this.setFieldHasError(r,t.error):this.setFieldHasNoError(r))},t.isTextareaField=function(e){return e.closest("textarea.fb-form-input")},t.submitForm=function(s){var i=this;s.preventDefault(),this.skipEmptyValidation=!1;var n=this.getFields();if(n){var a=!0,l={form_id:this.form.querySelector('input[name="fb_form[form_id]"]').value,fields:{}},f=null;if(Object.keys(n).forEach(function(e){var t=n[e];"fb_form[hnpt]"===t.name&&""!==t.value&&(a=!1,i.showFormMessage(i.honeypotFieldTriggered,!0));var r=i.validateField(t);if(r.error)return a=!1,i.setFieldHasError(t.element,r.error),void(f||(f=!0,t.element.focus()));i.setFieldHasNoError(t.element);var o=(o=e.replace("fb_form[","")).replace("]","");"phonenumber"===t.type&&(o=o.replace(/\[\w+\]/g,"")),l.fields[o]=r.value}),a){this.setLoading(!0);var m=this,e=this.emitEvent("beforeSubmit");if(e.defaultPrevented)return e.detail.hasOwnProperty("error")&&this.showFormMessage(e.detail.error,!0),void m.setLoading(!1);var t=this.form.closest(".fb-inst")?this.form.closest(".fb-inst").dataset.id:null,r=!!this.form.closest(".fb-inst")&&parseInt(this.form.closest(".fb-inst").firebox.box_log_id);this.addCaptchaFields(l);var o=new FormData;o.append("nonce",fbox_js_object.nonce),o.append("action","fb_form_submit"),o.append("form_data",JSON.stringify(l)),o.append("box_log_id",r),o.append("box_id",t),fetch(fbox_js_object.ajax_url,{method:"post",body:o}).then(function(e){return e.json()}).then(function(e){var o,t;e.validation&&e.validation.length?(o=!1,e.validation.forEach(function(e){var t="fb_form["+e.name+"]";"radio"===e.type?t+="[]":"phonenumber"===e.type&&(t+="[value]");var r=m.form.querySelector(m.formInputClass+'[name="'+t+'"]');o=!0,m.setFieldHasError(r,e.validation_message)}),o&&m.showFormMessage(e.message,!0),m.emitEvent("validation_error",{response:e})):e.error?(m.showFormMessage(e.message,e.error),m.emitEvent("error",{response:e})):("message"===e.action?m.showFormMessage(e.message,e.error):(t=m.emitEvent("beforeRedirectUser",{url:e.redirectURL}),window.location.href=t.detail.url),e.resetForm&&m.resetForm(n),e.hideForm&&m.form.classList.add("fb-hide-form"),m.form.closest(".fb-inst")&&m.form.closest(".fb-inst").classList.add("fb-form-success"),m.emitEvent("success",{response:e,submitButton:s.srcElement})),m.emitEvent("afterSubmit",{response:e});var r=m.getFormMessage();r&&!m.isInViewport(r)&&r.scrollIntoView(),e.hideForm||m.setLoading(!1)})}}},t.addCaptchaFields=function(e){var t=this.form.querySelector('input[name="cf-turnstile-response"]');t&&(e.fields["cf-turnstile-response"]=t.value)},t.isInViewport=function(e){var t=e.getBoundingClientRect();return 0<=t.top&&0<=t.left&&t.bottom<=(window.innerHeight||document.documentElement.clientHeight)&&t.right<=(window.innerWidth||document.documentElement.clientWidth)},t.emitEvent=function(e,t,r){var o;if(e)return(t=t||{}).instance=this,(r=r||!0)&&(o="FireBoxForm"+this.capitalize(e),this.dispatchEvent(o,document,t)),this.dispatchEvent(e,this.form,t)},t.capitalize=function(e){return"string"!=typeof e?"":e.charAt(0).toUpperCase()+e.slice(1)},t.dispatchEvent=function(e,t,r){var o=new CustomEvent(e,{detail:r,cancelable:!0});return t.dispatchEvent(o),o},t.resetForm=function(t){Object.keys(t).forEach(function(e){t[e].element.value="","radio"===t[e].type&&(t[e].element.checked=!1)})},t.setLoading=function(e){void 0===e&&(e=!0);var t=this.form.querySelector(this.submitButtonClass);t&&(e?(t.dataset.initialLabel||t.setAttribute("data-initial-label",t.innerHTML),t.classList.add("is-working"),t.innerHTML='<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="currentColor" stroke-width="10" r="40" stroke-dasharray="164.93361431346415 56.97787143782138"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>'):(t.innerHTML=t.dataset.initialLabel,t.classList.remove("is-working")))},t.showFormMessage=function(e,t){void 0===t&&(t=!1);var r=this.getFormMessage();r||(this.form.appendChild(this.getFormMessageTemplate()),r=this.getFormMessage()),r.classList.add("fb-msg-is-visible"),r.innerHTML=e,t?r.classList.add("is-error"):r.classList.remove("is-error")},t.getFormMessage=function(){return this.form.querySelector(this.formMessageClass)},t.getFormMessageTemplate=function(){var e=document.createElement("div");return e.className=this.formMessageClass.substring(1),e},t.hideFormMessage=function(){var e=this.getFormMessage();e&&(e.classList.remove("fb-msg-is-visible","is-error"),e.innerHTML="")},t.setFieldHasError=function(e,t){var r,o;!e||(r=e.closest(this.formControlGroupClass))&&(r.classList.contains("has-errors")?r.querySelector(".fb-form-field-error").innerHTML=t:(r.classList.add("has-errors"),(o=this.getErrorMessageTemplate()).innerHTML=t,r.appendChild(o)))},t.getErrorMessageTemplate=function(){var e=document.createElement("div");return e.className="fb-form-field-error",e},t.setFieldHasNoError=function(e){var t=e.closest(this.formControlGroupClass);t&&t.classList.contains("has-errors")&&(t.classList.remove("has-errors"),t.querySelector(".fb-form-field-error").remove())},t.validateField=function(e){var t=e.name,r=e.type,o=e.value,s=e.classList,i=e.required||!1,n=!1;if(i&&"hidden"!==r&&("checkbox"===r&&o instanceof Array?0===o.length&&(n=!0):"phonenumber"===r?""===o.value.trim()&&(n=!0):""===o.trim()&&(n=!0),s.contains("fb-date-time-input")&&""==o&&document.querySelector(".flatpickr-calendar.open")&&(n=!1)),i&&"hidden"===r&&s.contains("fb-date-time-input")&&""===o&&(n=!0),"email"===r)if(""===o)n=!0;else if(!this.validateEmail(o))return{error:this.invalidEmailMessage};if(n&&!this.skipEmptyValidation)return{error:this.requiredFieldMessage};var a=e instanceof HTMLElement?e:e.element,l=null;return a.closest(".fb-form-control-group")&&(l=a.closest(".fb-form-control-group").dataset.fieldId),{name:t,type:r,value:o,id:l}},t.getFields=function(){var d=this,h=[];return this.form.querySelectorAll(this.formInputClass).forEach(function(e){var t=e.name.replace("[]","");if(t&&!h[t]){var r="input",o=e.name,s=e.type,i=e.value;switch(s){case"select-one":r="select";break;case"textarea":r=s}var n,a,l,f,m,c=d.form.querySelector(r+'.fb-form-input[name="'+o+'"]');"radio"==s?(i="",(n=d.form.querySelector(r+'.fb-form-input[name="'+o+'"]:checked'))&&(i=(c=n)?c.value:"")):"checkbox"===s?(i="",(a=d.form.querySelectorAll(r+'.fb-form-input[name="'+o+'"]:checked')).length&&(i=[],a.forEach(function(e){i.push(e.value)}))):e.closest(".fpf-phone-control")&&(f=(l=e.closest(".fpf-phone-control")).querySelector(".fpf-phone-control--flag--selector"),m=l.querySelector(".fpf-phone-control--number"),i={code:f.value,value:m.value},c=l,s="phonenumber");var u={name:o,type:s,value:i,required:e.required,classList:e.classList,element:c};h[t]=u}}),h},t.validateEmail=function(e){return String(e).toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)},t.onFormKeyDown=function(e){if("Enter"===e.key||13===e.keyCode){var t=e.target.closest(".fpf-phone-control");if(t&&t.querySelector(".fpf-phone-control--flag.is-focused"))return void e.preventDefault();this.isTextareaField(e.target)||(e.preventDefault(),this.submitForm(e))}},e}();document.addEventListener("DOMContentLoaded",function(e){document.querySelectorAll("form.wp-block-firebox-form").forEach(function(e){new FireBox_Form(e)})});

