!function(){function r(t){return r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(r){return typeof r}:function(r){return r&&"function"==typeof Symbol&&r.constructor===Symbol&&r!==Symbol.prototype?"symbol":typeof r},r(t)}$((function(){$(".clone").on({click:function(t){var o,e,n,a,i=t.target.dataset.id;$.ajax({url:Craft.getActionUrl("freeform/forms/duplicate"),type:"post",dataType:"json",data:(o={id:i},e=Craft.csrfTokenName,n=Craft.csrfTokenValue,a=function(t,o){if("object"!=r(t)||!t)return t;var e=t[Symbol.toPrimitive];if(void 0!==e){var n=e.call(t,"string");if("object"!=r(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(t)}(e),(e="symbol"==r(a)?a:String(a))in o?Object.defineProperty(o,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):o[e]=n,o),success:function(r){r.success&&window.location.reload(),r.errors&&r.errors.forEach((function(r){return Craft.cp.displayNotification("error",r)}))}})}}),$(".reset-spam-count").on({click:function(){var r=$(this).data("confirm-message");if(!confirm(r))return!1;var t=$(this).data("form-id"),o={formId:t};o[Craft.csrfTokenName]=Craft.csrfTokenValue,$.ajax({url:Craft.getActionUrl("freeform/forms/reset-spam-counter"),type:"post",data:o,dataType:"json",success:function(r){r.error?Craft.cp.displayNotification("error",r.error):r.success&&$("td.spam-count[data-form-id="+t+"] > span").html(0)}})}})}))}();