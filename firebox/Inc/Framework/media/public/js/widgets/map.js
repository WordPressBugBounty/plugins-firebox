var FPF_OSM_Map=function(){function t(t){this.container=t,this.options=this.container.dataset.options?JSON.parse(this.container.dataset.options):{},this.lat=parseFloat(this.options.latitude)||null,this.long=parseFloat(this.options.longitude)||null,this.scale=this.options.scale||null,this.scaleControl=null,this.zoomControl=this.options.zoomControl||null,this.zoom=this.options.zoom||null,this.markers=this.options.markers||[],this.map=null,this.container.instance=this}var e=t.prototype;return e.render=function(){this.map=L.map(this.container.querySelector(".inner"),{gestureHandling:!0,gestureHandlingOptions:{text:{touch:"Use two fingers to move the map",scroll:"Use ctrl + scroll to zoom the map",scrollMac:"Use ⌘ + scroll to zoom the map"}}}),L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',subdomains:["a","b","c"]}).addTo(this.map),this.map.setView([this.lat,this.long],this.zoom||15),this.setScale(this.scale)},e.setZoomControl=function(t){this.map.zoomControl&&(t?(this.map.addControl(this.map.zoomControl),this.map.touchZoom.enable(),this.map.doubleClickZoom.enable(),this.map.scrollWheelZoom.enable(),this.map.boxZoom.enable(),this.map.keyboard.enable()):(this.map.removeControl(this.map.zoomControl),this.map.touchZoom.disable(),this.map.doubleClickZoom.disable(),this.map.scrollWheelZoom.disable(),this.map.boxZoom.disable(),this.map.keyboard.disable()))},e.setScale=function(t){if(this.scaleControl&&(this.map.removeControl(this.scaleControl),this.scaleControl=null),t){var e={};"imperial"===t?(e.imperial=!0,e.metric=!1):(e.imperial=!1,e.metric=!0),this.scaleControl=L.control.scale(e).addTo(this.map)}},e.getMarkerContent=function(t){var e=document.createElement("div");e.classList.add("wp-block-map-marker-container");var o=document.createElement("h6");if(o.classList.add("wp-block-map-marker-label"),o.innerHTML=t.label,e.appendChild(o),""!==t.description){var n=document.createElement("div");n.classList.add("wp-block-map-marker-content");var i=document.createElement("p");i.innerHTML=t.description,n.appendChild(i),e.appendChild(n)}return e},e.createMarker=function(t){var e=window.L.marker([t.latitude,t.longitude]);return""!==t.label&&e.bindTooltip(t.label,{direction:"auto"}),""===t.label&&""===t.description||e.bindPopup(this.getMarkerContent(t)),e.on("click",function(){e.closeTooltip()}),e.on("mouseover",function(){""!==t.label&&e.openTooltip()}),e},e.renderAllMarkers=function(){var e=this;this.markers.map(function(t){return e.createMarker(t)}).forEach(function(t){e.map.addLayer(t)})},t}(),FPF_Map_Widget_Loader=function(){function t(){}return t.prototype.init=function(){if(window.IntersectionObserver){var e=new IntersectionObserver(function(t,o){t.forEach(function(t){if(t.isIntersecting){t.target.classList.add("done");var e=new FPF_OSM_Map(t.target);e.render(),e.renderAllMarkers(),o.unobserve(t.target)}})},{rootMargin:"0px 0px 0px 0px"});document.querySelectorAll(".fpf-map-widget:not(.done)").forEach(function(t){e.observe(t)})}},t}();"loading"==document.readyState?document.addEventListener("DOMContentLoaded",function(){(new FPF_Map_Widget_Loader).init()}):(new FPF_Map_Widget_Loader).init();
