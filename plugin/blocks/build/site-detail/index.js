(()=>{var e,t={601:(e,t,l)=>{"use strict";const n=window.wp.blocks,a=window.React;var r=l(942),i=l.n(r);const o=window.wp.i18n,c=window.wp.blockEditor,s=window.wp.components,d=(e="")=>{const t=e.replace(/–|-|_/g," ");return t.includes(" ")?t.replace(/\b\w/g,(e=>e.toUpperCase())).replace(/\b(.{2})\b/i,(e=>e.toUpperCase())).replace(/\bapi\b/i,"API").replace(/\bphp\b/i,"PHP"):e};function u({attributes:e,setAttributes:t,onChange:l}){const n=window.wpcloud?.siteDetailKeys||[],r=["-"].concat(n),{name:i}=e;return(0,a.createElement)(s.SelectControl,{label:(0,o.__)("Select a site detail"),value:i,options:r.map((e=>({value:e,label:d(e)}))),onChange:e=>{t({name:e,label:d(e)}),l&&l(e)}})}const p=JSON.parse('{"UU":"wpcloud/site-detail"}');(0,n.registerBlockType)(p.UU,{edit:function({attributes:e,setAttributes:t,className:l}){const{label:n,adminOnly:r,inline:d,hideLabel:p}=e,b=(0,c.useBlockProps)(),m=(0,a.createElement)(a.Fragment,null,(0,a.createElement)(c.InspectorControls,null,(0,a.createElement)(s.PanelBody,{label:(0,o.__)("Settings")},(0,a.createElement)(u,{attributes:e,setAttributes:t}),(0,a.createElement)(s.ToggleControl,{label:(0,o.__)("Display Inline"),checked:d,onChange:e=>{t({inline:e})}}),(0,a.createElement)(s.ToggleControl,{label:(0,o.__)("Show Value only"),checked:p,onChange:e=>{t({hideLabel:e})},help:(0,o.__)("Only show the value of the site detail. The label will be hidden.")}),(0,a.createElement)(s.ToggleControl,{label:(0,o.__)("Limit to Admins"),checked:r,onChange:e=>{t({adminOnly:e})},help:(0,o.__)("Only admins will see this field. Inputs marked as admin only will appear with a dashed border in the editor")}))));return(0,a.createElement)("span",{...b},(0,a.createElement)("div",{className:i()(l,"wpcloud-block-site-detail",{"is-inline":d,"is-admin-only":r})},m,p?null:(0,a.createElement)("div",{className:i()(l,"wpcloud-block-site-detail__title")},(0,a.createElement)(c.RichText,{tagName:"h4",className:"wpcloud-block-site-detail__title-content",value:n,onChange:e=>{t({label:e})},placeholder:(0,o.__)("label")})),(0,a.createElement)("div",{className:"wpcloud-block-site-detail__value"},`{ ${n} }`)))},save:function({attributes:e,className:t}){const{label:l,inline:n,adminOnly:r,hideLabel:o}=e,s=c.useBlockProps.save();return(0,a.createElement)("span",{...s},(0,a.createElement)("div",{className:i()(t,"wpcloud-block-site-detail",{"is-inline":n,"is-admin-only":r})},o?null:(0,a.createElement)("div",{className:i()(t,"wpcloud-block-site-detail__title")},(0,a.createElement)(c.RichText.Content,{tagName:"h4",className:"wpcloud-block-site-detail__title-content",value:l})),(0,a.createElement)("div",{className:"wpcloud-block-site-detail__value"},`{ ${l} }`)))}})},942:(e,t)=>{var l;!function(){"use strict";var n={}.hasOwnProperty;function a(){for(var e="",t=0;t<arguments.length;t++){var l=arguments[t];l&&(e=i(e,r(l)))}return e}function r(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return a.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var t="";for(var l in e)n.call(e,l)&&e[l]&&(t=i(t,l));return t}function i(e,t){return t?e?e+" "+t:e+t:e}e.exports?(a.default=a,e.exports=a):void 0===(l=function(){return a}.apply(t,[]))||(e.exports=l)}()}},l={};function n(e){var a=l[e];if(void 0!==a)return a.exports;var r=l[e]={exports:{}};return t[e](r,r.exports,n),r.exports}n.m=t,e=[],n.O=(t,l,a,r)=>{if(!l){var i=1/0;for(d=0;d<e.length;d++){for(var[l,a,r]=e[d],o=!0,c=0;c<l.length;c++)(!1&r||i>=r)&&Object.keys(n.O).every((e=>n.O[e](l[c])))?l.splice(c--,1):(o=!1,r<i&&(i=r));if(o){e.splice(d--,1);var s=a();void 0!==s&&(t=s)}}return t}r=r||0;for(var d=e.length;d>0&&e[d-1][2]>r;d--)e[d]=e[d-1];e[d]=[l,a,r]},n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var l in t)n.o(t,l)&&!n.o(e,l)&&Object.defineProperty(e,l,{enumerable:!0,get:t[l]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={217:0,117:0};n.O.j=t=>0===e[t];var t=(t,l)=>{var a,r,[i,o,c]=l,s=0;if(i.some((t=>0!==e[t]))){for(a in o)n.o(o,a)&&(n.m[a]=o[a]);if(c)var d=c(n)}for(t&&t(l);s<i.length;s++)r=i[s],n.o(e,r)&&e[r]&&e[r][0](),e[r]=0;return n.O(d)},l=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];l.forEach(t.bind(null,0)),l.push=t.bind(null,l.push.bind(l))})();var a=n.O(void 0,[117],(()=>n(601)));a=n.O(a)})();