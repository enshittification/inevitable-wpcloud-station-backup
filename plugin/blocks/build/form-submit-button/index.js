(()=>{var e,t={273:(e,t,r)=>{"use strict";const o=window.wp.blocks,n=window.React;var a=r(942),l=r.n(a);const s=window.wp.i18n,c=window.wp.blockEditor,i=window.wp.components,u=JSON.parse('{"UU":"wpcloud/form-submit-button"}');(0,o.registerBlockType)(u.UU,{edit:({attributes:e,setAttributes:t})=>{const{text:r,icon:o}=e,a=[["core/button",{text:r||(0,s.__)("Submit"),tagName:"button",type:"submit"}]],u=(0,n.createElement)(n.Fragment,null,(0,n.createElement)(c.InspectorControls,null,(0,n.createElement)(i.PanelBody,{title:(0,s.__)("Settings")},(0,n.createElement)(i.TextControl,{label:(0,s.__)("icon"),value:o,onChange:e=>t({icon:e}),help:(0,s.__)("Replace the button text with a Dashicon. See https://developer.wordpress.org/resource/dashicons/ for available icons.")})))),p=(0,c.useBlockProps)(),b=(0,c.useInnerBlocksProps)(p,{template:a});return o?(0,n.createElement)(n.Fragment,null,u,(0,n.createElement)("button",{type:"submit",onClick:e=>e.preventDefault(),className:l()("button","wpcloud-block-form-submit-icon-button",p.className),...p,"aria-label":r},(0,n.createElement)(i.Dashicon,{icon:o}))):(0,n.createElement)(n.Fragment,null,u,(0,n.createElement)("div",{className:l()("wpcloud-block-form-submit-wrapper",b.className),...b}))},save:()=>{const e=c.useBlockProps.save();return(0,n.createElement)("div",{className:l()("wpcloud-block-form-submit-wrapper",e.className),...e},(0,n.createElement)(c.InnerBlocks.Content,null))}})},942:(e,t)=>{var r;!function(){"use strict";var o={}.hasOwnProperty;function n(){for(var e="",t=0;t<arguments.length;t++){var r=arguments[t];r&&(e=l(e,a(r)))}return e}function a(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return n.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var t="";for(var r in e)o.call(e,r)&&e[r]&&(t=l(t,r));return t}function l(e,t){return t?e?e+" "+t:e+t:e}e.exports?(n.default=n,e.exports=n):void 0===(r=function(){return n}.apply(t,[]))||(e.exports=r)}()}},r={};function o(e){var n=r[e];if(void 0!==n)return n.exports;var a=r[e]={exports:{}};return t[e](a,a.exports,o),a.exports}o.m=t,e=[],o.O=(t,r,n,a)=>{if(!r){var l=1/0;for(u=0;u<e.length;u++){for(var[r,n,a]=e[u],s=!0,c=0;c<r.length;c++)(!1&a||l>=a)&&Object.keys(o.O).every((e=>o.O[e](r[c])))?r.splice(c--,1):(s=!1,a<l&&(l=a));if(s){e.splice(u--,1);var i=n();void 0!==i&&(t=i)}}return t}a=a||0;for(var u=e.length;u>0&&e[u-1][2]>a;u--)e[u]=e[u-1];e[u]=[r,n,a]},o.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return o.d(t,{a:t}),t},o.d=(e,t)=>{for(var r in t)o.o(t,r)&&!o.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},o.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={134:0,762:0};o.O.j=t=>0===e[t];var t=(t,r)=>{var n,a,[l,s,c]=r,i=0;if(l.some((t=>0!==e[t]))){for(n in s)o.o(s,n)&&(o.m[n]=s[n]);if(c)var u=c(o)}for(t&&t(r);i<l.length;i++)a=l[i],o.o(e,a)&&e[a]&&e[a][0](),e[a]=0;return o.O(u)},r=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})();var n=o.O(void 0,[762],(()=>o(273)));n=o.O(n)})();