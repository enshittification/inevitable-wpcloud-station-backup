(()=>{var e,t={279:(e,t,a)=>{"use strict";const l=window.wp.blocks,r=window.React;var n=a(942),o=a.n(n);const i=window.wp.i18n,s=window.wp.blockEditor,c=window.wp.components,u=window.wp.element;function p({attributes:e,className:t,styleProps:a={},onPlaceholderChange:l}){const{placeholder:n,required:s,type:c}=e,{borderProps:u,colorProps:p}=a,d=(0,r.createElement)(r.Fragment,null),m="textarea"===c?"textarea":"input";return(0,r.createElement)(r.Fragment,null,d,(0,r.createElement)(m,{type:"textarea"===c?void 0:c,className:o()(t,"wpcloud-block-form-input__input",p.className,u.className),"aria-label":(0,i.__)("Optional placeholder text"),placeholder:n?void 0:(0,i.__)("Optional placeholder…"),value:n,onChange:e=>l(e.target.value),"aria-required":s,style:{...u.style,...p.style}}))}function d({attributes:e,className:t,styleProps:a={},onValueChange:l}){const{value:n,options:s}=e,{borderProps:c,colorProps:u}=a,p=(0,r.createElement)(r.Fragment,null);return(0,r.createElement)(r.Fragment,null,p,(0,r.createElement)("select",{className:o()(t,"wpcloud-dashboard-form-input__select",u.className,c.className),"aria-label":(0,i.__)("Select"),value:n,onChange:e=>l(e.target.value),style:{...c.style,...u.style}},s.map((e=>(0,r.createElement)("option",{key:e.value,value:e.value},e.label)))))}(0,r.createElement)(r.Fragment,null);var m=a(731),b=a.n(m);const h=window.wp.dom,E=e=>b()((0,h.__unstableStripHTML)(e)).replace(/[^\p{L}\p{N}]+/gu,"_").toLowerCase().replace(/(^-+)|(-+$)/g,"");const _=JSON.parse('{"UU":"wpcloud/form-input"}');(0,l.registerBlockType)(_.UU,{edit:function({attributes:e,setAttributes:t,className:a}){const{type:l,inlineLabel:n,label:m,adminOnly:b,required:h,name:E}=e,_=(0,s.useBlockProps)(),v=(0,u.useRef)(),f=(0,s.__experimentalUseBorderProps)(e),y=(0,s.__experimentalUseColorProps)(e);v.current&&v.current.focus();const O=(0,u.useCallback)((e=>t({placeholder:e})),[t]),g=(0,u.useCallback)((e=>t({value:e})),[t]),w="hidden"===l,x={text:p,email:p,password:p,hidden:p,textarea:p,select:d},C=x[l]?x[l]:p,k=(0,r.createElement)(r.Fragment,null,(0,r.createElement)(s.InspectorControls,null,(0,r.createElement)(c.PanelBody,{title:(0,i.__)("Settings")},(0,r.createElement)(c.TextControl,{label:(0,i.__)("Field Name"),value:E,onChange:e=>t({name:e}),help:(0,i.__)("The name attribute of the input field")}),"checkbox"!==l&&(0,r.createElement)(c.CheckboxControl,{label:(0,i.__)("Inline label"),checked:n,onChange:e=>{t({inlineLabel:e})}}),(0,r.createElement)(c.CheckboxControl,{label:(0,i.__)("Limit to Admins"),checked:b,onChange:e=>{t({adminOnly:e})},help:(0,i.__)("Only admins will see this field. Inputs marked as admin only will appear with a dashed border in the editor")}),(0,r.createElement)(c.CheckboxControl,{label:(0,i.__)("Required"),checked:h,onChange:e=>{t({required:e})}}))));return(0,r.createElement)("div",{..._},k,(0,r.createElement)("span",{className:o()("wpcloud-block-form-input__label",{"is-label-inline":n||"checkbox"===l,"is-admin-only":b})},!w&&(0,r.createElement)(s.RichText,{tagName:"span",className:"wpcloud-block-form-input__label-content",value:m,onChange:e=>t({label:e}),"aria-label":m?(0,i.__)("Label"):(0,i.__)("Empty label"),"data-empty":!m,placeholder:(0,i.__)("Type the label for this input")}),(0,r.createElement)(C,{attributes:e,onPlaceholderChange:O,onValueChange:g,className:a,styleProps:{colorProps:y,borderProps:f}})))},save:function({attributes:e}){const{type:t,label:a,name:l,value:n,inlineLabel:i}=e,c=s.useBlockProps.save();return"hidden"===t?(0,r.createElement)("input",{type:t,name:l,value:n}):(0,r.createElement)("div",{...c},(0,r.createElement)("label",{className:o()("wpcloud-block-form-input__label",{"is-label-inline":i})},(0,r.createElement)("span",{className:"wpcloud-block-form-input__label-content"},(0,r.createElement)(s.RichText.Content,{value:a})),function(e){const{type:t}=e,a=(0,s.__experimentalGetBorderClassesAndStyles)(e),l=(0,s.__experimentalGetColorClassesAndStyles)(e),n={...a.style,...l.style},i=o()("wpcloud-block-form-input__input",l.className,a.className);return"select"===t?function({options:e,label:t,name:a,required:l},n,i){return(0,r.createElement)("select",{className:o()("wpcloud-dashboard-form-input__select",n),style:i,name:a||E(t),required:l,"aria-required":l},e.map((e=>(0,r.createElement)("option",{key:e.value,value:e.value},e.label))))}(e,i,n):function({type:e,name:t,label:a,required:l,placeholder:n},o,i){const s="textarea"===e?"textarea":"input";return(0,r.createElement)(s,{className:o,type:"textarea"===e?void 0:e,name:t||E(a),required:l,"aria-required":l,placeholder:n||void 0,style:i})}(e,i,n)}(e)))}})},731:e=>{var t={À:"A",Á:"A",Â:"A",Ã:"A",Ä:"A",Å:"A",Ấ:"A",Ắ:"A",Ẳ:"A",Ẵ:"A",Ặ:"A",Æ:"AE",Ầ:"A",Ằ:"A",Ȃ:"A",Ả:"A",Ạ:"A",Ẩ:"A",Ẫ:"A",Ậ:"A",Ç:"C",Ḉ:"C",È:"E",É:"E",Ê:"E",Ë:"E",Ế:"E",Ḗ:"E",Ề:"E",Ḕ:"E",Ḝ:"E",Ȇ:"E",Ẻ:"E",Ẽ:"E",Ẹ:"E",Ể:"E",Ễ:"E",Ệ:"E",Ì:"I",Í:"I",Î:"I",Ï:"I",Ḯ:"I",Ȋ:"I",Ỉ:"I",Ị:"I",Ð:"D",Ñ:"N",Ò:"O",Ó:"O",Ô:"O",Õ:"O",Ö:"O",Ø:"O",Ố:"O",Ṍ:"O",Ṓ:"O",Ȏ:"O",Ỏ:"O",Ọ:"O",Ổ:"O",Ỗ:"O",Ộ:"O",Ờ:"O",Ở:"O",Ỡ:"O",Ớ:"O",Ợ:"O",Ù:"U",Ú:"U",Û:"U",Ü:"U",Ủ:"U",Ụ:"U",Ử:"U",Ữ:"U",Ự:"U",Ý:"Y",à:"a",á:"a",â:"a",ã:"a",ä:"a",å:"a",ấ:"a",ắ:"a",ẳ:"a",ẵ:"a",ặ:"a",æ:"ae",ầ:"a",ằ:"a",ȃ:"a",ả:"a",ạ:"a",ẩ:"a",ẫ:"a",ậ:"a",ç:"c",ḉ:"c",è:"e",é:"e",ê:"e",ë:"e",ế:"e",ḗ:"e",ề:"e",ḕ:"e",ḝ:"e",ȇ:"e",ẻ:"e",ẽ:"e",ẹ:"e",ể:"e",ễ:"e",ệ:"e",ì:"i",í:"i",î:"i",ï:"i",ḯ:"i",ȋ:"i",ỉ:"i",ị:"i",ð:"d",ñ:"n",ò:"o",ó:"o",ô:"o",õ:"o",ö:"o",ø:"o",ố:"o",ṍ:"o",ṓ:"o",ȏ:"o",ỏ:"o",ọ:"o",ổ:"o",ỗ:"o",ộ:"o",ờ:"o",ở:"o",ỡ:"o",ớ:"o",ợ:"o",ù:"u",ú:"u",û:"u",ü:"u",ủ:"u",ụ:"u",ử:"u",ữ:"u",ự:"u",ý:"y",ÿ:"y",Ā:"A",ā:"a",Ă:"A",ă:"a",Ą:"A",ą:"a",Ć:"C",ć:"c",Ĉ:"C",ĉ:"c",Ċ:"C",ċ:"c",Č:"C",č:"c",C̆:"C",c̆:"c",Ď:"D",ď:"d",Đ:"D",đ:"d",Ē:"E",ē:"e",Ĕ:"E",ĕ:"e",Ė:"E",ė:"e",Ę:"E",ę:"e",Ě:"E",ě:"e",Ĝ:"G",Ǵ:"G",ĝ:"g",ǵ:"g",Ğ:"G",ğ:"g",Ġ:"G",ġ:"g",Ģ:"G",ģ:"g",Ĥ:"H",ĥ:"h",Ħ:"H",ħ:"h",Ḫ:"H",ḫ:"h",Ĩ:"I",ĩ:"i",Ī:"I",ī:"i",Ĭ:"I",ĭ:"i",Į:"I",į:"i",İ:"I",ı:"i",Ĳ:"IJ",ĳ:"ij",Ĵ:"J",ĵ:"j",Ķ:"K",ķ:"k",Ḱ:"K",ḱ:"k",K̆:"K",k̆:"k",Ĺ:"L",ĺ:"l",Ļ:"L",ļ:"l",Ľ:"L",ľ:"l",Ŀ:"L",ŀ:"l",Ł:"l",ł:"l",Ḿ:"M",ḿ:"m",M̆:"M",m̆:"m",Ń:"N",ń:"n",Ņ:"N",ņ:"n",Ň:"N",ň:"n",ŉ:"n",N̆:"N",n̆:"n",Ō:"O",ō:"o",Ŏ:"O",ŏ:"o",Ő:"O",ő:"o",Œ:"OE",œ:"oe",P̆:"P",p̆:"p",Ŕ:"R",ŕ:"r",Ŗ:"R",ŗ:"r",Ř:"R",ř:"r",R̆:"R",r̆:"r",Ȓ:"R",ȓ:"r",Ś:"S",ś:"s",Ŝ:"S",ŝ:"s",Ş:"S",Ș:"S",ș:"s",ş:"s",Š:"S",š:"s",Ţ:"T",ţ:"t",ț:"t",Ț:"T",Ť:"T",ť:"t",Ŧ:"T",ŧ:"t",T̆:"T",t̆:"t",Ũ:"U",ũ:"u",Ū:"U",ū:"u",Ŭ:"U",ŭ:"u",Ů:"U",ů:"u",Ű:"U",ű:"u",Ų:"U",ų:"u",Ȗ:"U",ȗ:"u",V̆:"V",v̆:"v",Ŵ:"W",ŵ:"w",Ẃ:"W",ẃ:"w",X̆:"X",x̆:"x",Ŷ:"Y",ŷ:"y",Ÿ:"Y",Y̆:"Y",y̆:"y",Ź:"Z",ź:"z",Ż:"Z",ż:"z",Ž:"Z",ž:"z",ſ:"s",ƒ:"f",Ơ:"O",ơ:"o",Ư:"U",ư:"u",Ǎ:"A",ǎ:"a",Ǐ:"I",ǐ:"i",Ǒ:"O",ǒ:"o",Ǔ:"U",ǔ:"u",Ǖ:"U",ǖ:"u",Ǘ:"U",ǘ:"u",Ǚ:"U",ǚ:"u",Ǜ:"U",ǜ:"u",Ứ:"U",ứ:"u",Ṹ:"U",ṹ:"u",Ǻ:"A",ǻ:"a",Ǽ:"AE",ǽ:"ae",Ǿ:"O",ǿ:"o",Þ:"TH",þ:"th",Ṕ:"P",ṕ:"p",Ṥ:"S",ṥ:"s",X́:"X",x́:"x",Ѓ:"Г",ѓ:"г",Ќ:"К",ќ:"к",A̋:"A",a̋:"a",E̋:"E",e̋:"e",I̋:"I",i̋:"i",Ǹ:"N",ǹ:"n",Ồ:"O",ồ:"o",Ṑ:"O",ṑ:"o",Ừ:"U",ừ:"u",Ẁ:"W",ẁ:"w",Ỳ:"Y",ỳ:"y",Ȁ:"A",ȁ:"a",Ȅ:"E",ȅ:"e",Ȉ:"I",ȉ:"i",Ȍ:"O",ȍ:"o",Ȑ:"R",ȑ:"r",Ȕ:"U",ȕ:"u",B̌:"B",b̌:"b",Č̣:"C",č̣:"c",Ê̌:"E",ê̌:"e",F̌:"F",f̌:"f",Ǧ:"G",ǧ:"g",Ȟ:"H",ȟ:"h",J̌:"J",ǰ:"j",Ǩ:"K",ǩ:"k",M̌:"M",m̌:"m",P̌:"P",p̌:"p",Q̌:"Q",q̌:"q",Ř̩:"R",ř̩:"r",Ṧ:"S",ṧ:"s",V̌:"V",v̌:"v",W̌:"W",w̌:"w",X̌:"X",x̌:"x",Y̌:"Y",y̌:"y",A̧:"A",a̧:"a",B̧:"B",b̧:"b",Ḑ:"D",ḑ:"d",Ȩ:"E",ȩ:"e",Ɛ̧:"E",ɛ̧:"e",Ḩ:"H",ḩ:"h",I̧:"I",i̧:"i",Ɨ̧:"I",ɨ̧:"i",M̧:"M",m̧:"m",O̧:"O",o̧:"o",Q̧:"Q",q̧:"q",U̧:"U",u̧:"u",X̧:"X",x̧:"x",Z̧:"Z",z̧:"z",й:"и",Й:"И",ё:"е",Ё:"Е"},a=Object.keys(t).join("|"),l=new RegExp(a,"g"),r=new RegExp(a,"");function n(e){return t[e]}var o=function(e){return e.replace(l,n)};e.exports=o,e.exports.has=function(e){return!!e.match(r)},e.exports.remove=o},942:(e,t)=>{var a;!function(){"use strict";var l={}.hasOwnProperty;function r(){for(var e="",t=0;t<arguments.length;t++){var a=arguments[t];a&&(e=o(e,n(a)))}return e}function n(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return r.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var t="";for(var a in e)l.call(e,a)&&e[a]&&(t=o(t,a));return t}function o(e,t){return t?e?e+" "+t:e+t:e}e.exports?(r.default=r,e.exports=r):void 0===(a=function(){return r}.apply(t,[]))||(e.exports=a)}()}},a={};function l(e){var r=a[e];if(void 0!==r)return r.exports;var n=a[e]={exports:{}};return t[e](n,n.exports,l),n.exports}l.m=t,e=[],l.O=(t,a,r,n)=>{if(!a){var o=1/0;for(u=0;u<e.length;u++){for(var[a,r,n]=e[u],i=!0,s=0;s<a.length;s++)(!1&n||o>=n)&&Object.keys(l.O).every((e=>l.O[e](a[s])))?a.splice(s--,1):(i=!1,n<o&&(o=n));if(i){e.splice(u--,1);var c=r();void 0!==c&&(t=c)}}return t}n=n||0;for(var u=e.length;u>0&&e[u-1][2]>n;u--)e[u]=e[u-1];e[u]=[a,r,n]},l.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return l.d(t,{a:t}),t},l.d=(e,t)=>{for(var a in t)l.o(t,a)&&!l.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},l.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={855:0,367:0};l.O.j=t=>0===e[t];var t=(t,a)=>{var r,n,[o,i,s]=a,c=0;if(o.some((t=>0!==e[t]))){for(r in i)l.o(i,r)&&(l.m[r]=i[r]);if(s)var u=s(l)}for(t&&t(a);c<o.length;c++)n=o[c],l.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return l.O(u)},a=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];a.forEach(t.bind(null,0)),a.push=t.bind(null,a.push.bind(a))})();var r=l.O(void 0,[367],(()=>l(279)));r=l.O(r)})();