var FPF_Countdown_Widget_Timer=function(){function t(t,e){this.expected=null,this.timeout=null,this.instance=t,this.countdown=t.elem,this.data=t.data,this.helper=t.helper,this.interval=e||this.helper.MILLIS_IN_SECOND}var e=t.prototype;return e.start=function(){if(this.expected=Date.now()+this.interval,this.countdown.innerHTML=this.data.format,this.countdown.classList.remove("is-preview"),this.instance.tick(),this.helper.end())return!1;this.timeout=setTimeout(this.step.bind(this),this.interval)},e.stop=function(){clearTimeout(this.timeout),this.interval=null},e.step=function(){if(this.helper.end())return!1;var t=Date.now()-this.expected;this.instance.tick(),this.expected+=this.interval,this.timeout=setTimeout(this.step.bind(this),Math.max(0,this.interval-t))},t}(),FPF_Countdown_Widget_Helper=function(){function t(t){this.instance=t,this.countdown=t.elem,this.data=t.data,this.MILLIS_IN_DAY=864e5,this.MILLIS_IN_HOUR=36e5,this.MILLIS_IN_MINUTE=6e4,this.MILLIS_IN_SECOND=1e3,this.SECONDS_IN_HOUR=3600,this.SECONDS_IN_MINUTE=60,this.MINUTES_IN_HOUR=60,this.HOURS_IN_DAY=24,this.MONTHS_IN_YEAR=12}var e=t.prototype;return e.end=function(){var t=new Date(this.data.value),e=new Date;if(!this.nowPassedTarget(e,t))return!1;this.countdown.ticker.stop();var n=new CustomEvent("onFPFCountdownFinish",{detail:this.countdown,cancelable:!0});switch(this.countdown.dispatchEvent(n),this.data.countdownAction){case"keep":case"hide":"hide"!=this.data.countdownAction||n.defaultPrevented||this.countdown.classList.add("fpf-hidden");break;case"message":this.instance.showMessage(),n.defaultPrevented||this.instance.markDone();break;case"redirect":"redirect"!=this.data.countdownAction||n.defaultPrevented||(window.location.href=this.data.redirectUrl)}return"dynamic"==this.data.countdownType&&"restart"==this.data.countdownAction&&(this.instance.deleteDynamicLocalStorageItem(),this.instance.reset()),!0},e.getTimerData=function(){var t=this.findAllTags();if(!t)return!1;var e=new Date(this.data.value),n=new Date,s=e-n,a=this.convertDateToObject(e),i=this.convertDateToObject(n);if(this.nowPassedTarget(n,e)){var o="true"===this.data.doubleZeroesFormat?"00":"0";return{years:o,months:o,days:o,hours:o,minutes:o,seconds:o}}var r=a.year-i.year,c=a.month-i.month,u=a.day-i.day,d=a.hours-i.hours,h=a.minutes-i.minutes,l=a.seconds-i.seconds;if(l<0&&(h--,l+=this.SECONDS_IN_MINUTE),h<0&&(d--,h+=this.MINUTES_IN_HOUR),d<0&&(u--,d+=this.HOURS_IN_DAY),u<0){c--;var m=this.calculateDaysInThisMonth(i.year,i.month),w=this.calculateDaysInPreviousMonth(a.year,a.month);u+=w<m?m:w}c<0&&(r--,c+=this.MONTHS_IN_YEAR);var g=(d*this.SECONDS_IN_HOUR+h*this.SECONDS_IN_MINUTE+l)*this.MILLIS_IN_SECOND,f=new Date(Date.UTC(i.year+r,i.month-1,2==i.month&&29==i.day&&1!=new Date(i.year+r,1,29).getMonth()?28:i.day,i.hours,i.minutes,i.seconds)),p=f.valueOf()-n.valueOf(),I=new Date(e.valueOf()-u*this.MILLIS_IN_DAY-g).valueOf()-f.valueOf(),_={},y=s;if(1!=t.years&&1!=t.months||(y-=p),1==t.years?_.years=r:1==t.months&&(c+=r*this.MONTHS_IN_YEAR),1==t.months?(y-=I,_.months=c):u=Math.floor(y/this.MILLIS_IN_DAY),1==t.days&&(_.days=Math.floor(y/this.MILLIS_IN_DAY),y-=u*this.MILLIS_IN_DAY),1==t.hours&&(_.hours=Math.floor(y/this.MILLIS_IN_HOUR),y-=_.hours*this.MILLIS_IN_HOUR),1==t.minutes&&(_.minutes=Math.floor(y/this.MILLIS_IN_MINUTE),y-=_.minutes*this.MILLIS_IN_MINUTE),_.seconds=Math.floor(y/this.MILLIS_IN_SECOND),"true"===this.data.doubleZeroesFormat){Object.keys(_).map(function(t,e){var n,s;_[t]=_[t].toString().length<2?(n=_[t],s=2,String(n).padStart(s,"0")):_[t]})}return _},e.findAllTags=function(){if(this.data.tags)return this.data.tags;var e={years:0,months:0,days:0,hours:0,minutes:0,seconds:0},t=this.data.format.match(/countdown-digit (days|hours|minutes|seconds)/g);if(t)return t.forEach(function(t){t=t.replace("countdown-digit ",""),e[t]=1}),this.data.tags=e;if(!(t=this.data.format.match(/[^{]+(?=\})/g)))return!1;var n=this.countdown.innerHTML;return t.forEach(function(t){n=n.replace("{"+t+"}",'<span class="countdown-digit '+t+'"></span>'),e[t]=1}),this.countdown.innerHTML=n,this.data.tags=e},e.nowPassedTarget=function(t,e){var n=new Date(t).getTime();return new Date(e).getTime()<n},e.convertDateToObject=function(t){return{year:t.getUTCFullYear(),month:t.getUTCMonth()+1,day:t.getUTCDate(),hours:t.getUTCHours(),minutes:t.getUTCMinutes(),seconds:t.getUTCSeconds()}},e.calculateDaysInPreviousMonth=function(t,e){return(e-=2)<0&&t--,new Date(Date.UTC(t,e+1,0)).getUTCDate()},e.calculateDaysInThisMonth=function(t,e){return 12==e&&(t++,e=0),new Date(Date.UTC(t,e,0)).getUTCDate()},t}(),FPF_Countdown_Widget=function(){function t(t){this.countdown=t,this.init()}var e=t.prototype;return e.init=function(){return this.elem=this.countdown,this.data=this.getCountdownInstanceData(),this.helper=new FPF_Countdown_Widget_Helper(this),this.countdown.instance=this},e.start=function(){return this.countdown.ticker=new FPF_Countdown_Widget_Timer(this),this.countdown.ticker.start(),this},e.reset=function(){this.init(),this.stop(),this.countdown.classList.remove("fpf-hidden"),this.countdown.classList.remove("showing-message"),this.countdown.classList.remove("done"),this.start()},e.stop=function(){this.countdown.ticker&&this.countdown.ticker.stop()},e.tick=function(){for(var t=this.helper.getTimerData(),e=0,n=Object.keys(t);e<n.length;e++){var s=n[e],a=this.countdown.querySelector(".countdown-digit."+s);if(a)for(var i=t[s].toString(),o=0;o<i.length;o++){var r=1===i.length?2:o+1,c=a.querySelector(".digit-"+r);c?c.innerHTML!=i[o]&&(c.classList.remove("empty"),c.innerHTML=i[o]):a.innerHTML+='<span class="digit-number digit-'+r+'">'+i[o]+"</span>"}}},e.getCountdownInstanceData=function(){var t=Object.assign({},this.countdown.dataset);if("dynamic"===t.countdownType){var e=new Date,n=this.getDynamicCountdownDate(t),s=this.getDynamicLocalStorageKey();localStorage.getItem(s)||localStorage.setItem(s,e),t.value=n}else"static"===t.countdownType&&"client"===t.timezone&&(t.value=new Date(t.value));return t.elem=this.countdown,t.days="true"===t.days,t.hours="true"===t.hours,t.minutes="true"===t.minutes,t.seconds="true"===t.seconds,t.format=t.format,t.format=this.getThemeFormat(t),t},e.getThemeFormat=function(o){if("custom"===o.theme)return o.format;var r=[];o.days&&r.push("days"),o.hours&&r.push("hours"),o.minutes&&r.push("minutes"),o.seconds&&r.push("seconds");var c="oneline"===o.theme?" ":"",u="",d=1;return r.forEach(function(t){var e=o[t+"Label"],n=""!=e?c+'<span class="countdown-digit-label">'+e+"</span>":"",s="",a="";"default"===o.theme&&(s=""===e?" no-label":"");var i='<span class="countdown-item'+s+'"><span class="countdown-digit '+t+'"><span class="digit-number digit-1 empty"></span><span class="digit-number digit-2 empty"></span></span>'+n+"</span>";"true"===o.separator&&"default"===o.theme&&(a=d!==r.length?'<span class="countdown-item separator"><span class="countdown-digit">:</span><span class="countdown-digit-label">&nbsp;</span></span>':""),"oneline"===o.theme&&(a=""!==(a=d<r.length-1?", ":d!==r.length?", "+fpframework_countdown_widget.AND+" ":"")?'<span class="countdown-item separator"><span class="countdown-digit">'+a+"</span></span>":""),u+=i+a,d++}),u},e.getDynamicLocalStorageKey=function(){return"fpf_dynamic_countdown_"+btoa(encodeURIComponent(JSON.stringify(Object.assign({},this.countdown.dataset))))},e.deleteDynamicLocalStorageItem=function(){"dynamic"==this.data.countdownType&&localStorage.removeItem(this.getDynamicLocalStorageKey())},e.getDynamicCountdownDate=function(t){var e=new Date,n=localStorage.getItem(this.getDynamicLocalStorageKey());return n&&(e=new Date(n)),this.applyDynamicDataToDate(e,t)},e.applyDynamicDataToDate=function(t,e){var n=parseInt(e.dynamicDays)||0,s=parseInt(e.dynamicHours)||0,a=parseInt(e.dynamicMinutes)||0,i=parseInt(e.dynamicSeconds)||0;return 0!=n&&t.setDate(t.getDate()+n),0!=s&&t.setHours(t.getHours()+s),0!=a&&t.setMinutes(t.getMinutes()+a),0!=i&&t.setSeconds(t.getSeconds()+i),t},e.showMessage=function(){this.countdown.classList.add("showing-message"),this.countdown.innerHTML=this.data.finishText},e.markDone=function(){var e=this;Object.keys(this.countdown.dataset).forEach(function(t){delete e.countdown.dataset[t]}),this.countdown.classList.add("done")},t}(),FPF_Countdown_Widget_Loader=function(){function t(){}return t.prototype.init=function(){if(window.IntersectionObserver){var e=new IntersectionObserver(function(t,e){t.forEach(function(t){t.isIntersecting&&(new FPF_Countdown_Widget(t.target).start(),e.unobserve(t.target))})},{rootMargin:"0px 0px 0px 0px"});document.querySelectorAll(".fpf-countdown:not(.done)").forEach(function(t){e.observe(t)})}},t}();"loading"==document.readyState?document.addEventListener("DOMContentLoaded",function(){(new FPF_Countdown_Widget_Loader).init()}):(new FPF_Countdown_Widget_Loader).init();

