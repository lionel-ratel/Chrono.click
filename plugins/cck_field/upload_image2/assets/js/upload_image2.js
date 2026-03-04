/* Spectrum-Colorpicker 1.8.0 */
!function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery"],t):"object"==typeof exports&&"object"==typeof module?module.exports=t(require("jquery")):t(jQuery)}(function(t,e){"use strict";var r={beforeShow:l,move:l,change:l,show:l,hide:l,color:!1,flat:!1,showInput:!1,allowEmpty:!1,showButtons:!0,clickoutFiresChange:!0,showInitial:!1,showPalette:!1,showPaletteOnly:!1,hideAfterPaletteSelect:!1,togglePaletteOnly:!1,showSelectionPalette:!0,localStorageKey:!1,appendTo:"body",maxSelectionSize:7,cancelText:"cancel",chooseText:"choose",togglePaletteMoreText:"more",togglePaletteLessText:"less",clearText:"Clear Color Selection",noColorSelectedText:"No Color Selected",preferredFormat:!1,className:"",containerClassName:"",replacerClassName:"",showAlpha:!1,theme:"sp-light",palette:[["#ffffff","#000000","#ff0000","#ff8000","#ffff00","#008000","#0000ff","#4b0082","#9400d3"]],selectionPalette:[],disabled:!1,offset:null},a=[],n=!!/msie/i.exec(window.navigator.userAgent),s=function(){function t(t,e){return!!~(""+t).indexOf(e)}var e=document.createElement("div").style;return e.cssText="background-color:rgba(0,0,0,.5)",t(e.backgroundColor,"rgba")||t(e.backgroundColor,"hsla")}(),i=function(){var t="";if(n)for(var e=1;e<=6;e++)t+="<div class='sp-"+e+"'></div>";return["<div class='sp-container sp-hidden'>","<div class='sp-palette-container'>","<div class='sp-palette sp-thumb sp-cf'></div>","<div class='sp-palette-button-container sp-cf'>","<button type='button' class='sp-palette-toggle'></button>","</div>","</div>","<div class='sp-picker-container'>","<div class='sp-top sp-cf'>","<div class='sp-fill'></div>","<div class='sp-top-inner'>","<div class='sp-color'>","<div class='sp-sat'>","<div class='sp-val'>","<div class='sp-dragger'></div>","</div>","</div>","</div>","<div class='sp-clear sp-clear-display'>","</div>","<div class='sp-hue'>","<div class='sp-slider'></div>",t,"</div>","</div>","<div class='sp-alpha'><div class='sp-alpha-inner'><div class='sp-alpha-handle'></div></div></div>","</div>","<div class='sp-input-container sp-cf'>","<input class='sp-input' type='text' spellcheck='false'  />","</div>","<div class='sp-initial sp-thumb sp-cf'></div>","<div class='sp-button-container sp-cf'>","<a class='sp-cancel' href='#'></a>","<button type='button' class='sp-choose'></button>","</div>","</div>","</div>"].join("")}();function o(e,r,a,n){for(var i=[],o=0;o<e.length;o++){var l=e[o];if(l){var c=tinycolor(l),f=c.toHsl().l<.5?"sp-thumb-el sp-thumb-dark":"sp-thumb-el sp-thumb-light";f+=tinycolor.equals(r,l)?" sp-thumb-active":"";var h=c.toString(n.preferredFormat||"rgb"),u=s?"background-color:"+c.toRgbString():"filter:"+c.toFilter();i.push('<span title="'+h+'" data-color="'+c.toRgbString()+'" class="'+f+'"><span class="sp-thumb-inner" style="'+u+';"></span></span>')}else i.push(t("<div />").append(t('<span data-color="" style="background-color:transparent;" class="sp-clear-display"></span>').attr("title",n.noColorSelectedText)).html())}return"<div class='sp-cf "+a+"'>"+i.join("")+"</div>"}function l(){}function c(t){t.stopPropagation()}function f(t,e){var r=Array.prototype.slice,a=r.call(arguments,2);return function(){return t.apply(e,a.concat(r.call(arguments)))}}function h(e,r,a,s){r=r||function(){},a=a||function(){},s=s||function(){};var i=document,o=!1,l={},c=0,f=0,h="ontouchstart"in window,u={};function d(t){t.stopPropagation&&t.stopPropagation(),t.preventDefault&&t.preventDefault(),t.returnValue=!1}function $(t){if(o){if(n&&i.documentMode<9&&!t.button)return p();var a=t.originalEvent&&t.originalEvent.touches&&t.originalEvent.touches[0],s=a&&a.pageX||t.pageX,u=a&&a.pageY||t.pageY,$=Math.max(0,Math.min(s-l.left,f)),g=Math.max(0,Math.min(u-l.top,c));h&&d(t),r.apply(e,[$,g,t])}}function p(){o&&(t(i).off(u),t(i.body).removeClass("sp-dragging"),setTimeout(function(){s.apply(e,arguments)},0)),o=!1}u.selectstart=d,u.dragstart=d,u["touchmove mousemove"]=$,u["touchend mouseup"]=p,t(e).on("touchstart mousedown",function r(n){(n.which?3==n.which:2==n.button)||o||!1===a.apply(e,arguments)||(o=!0,c=t(e).height(),f=t(e).width(),l=t(e).offset(),t(i).on(u),t(i.body).addClass("sp-dragging"),$(n),d(n))})}function u(){return t.fn.spectrum.inputTypeColorSupport()}var d="spectrum.id";t.fn.spectrum=function(l,$){if("string"==typeof l){var p=this,g=Array.prototype.slice.call(arguments,1);return this.each(function(){var e=a[t(this).data(d)];if(e){var r=e[l];if(!r)throw Error("Spectrum: no such method: '"+l+"'");"get"==l?p=e.get():"container"==l?p=e.container:"option"==l?p=e.option.apply(e,g):"destroy"==l?(e.destroy(),t(this).removeData(d)):r.apply(e,g)}}),p}return this.spectrum("destroy").each(function(){var $=function l(d,$){var p,g,b,v,m,_,y=10,w=(v=$,m=d,(_=t.extend({},r,v)).callbacks={move:f(_.move,m),change:f(_.change,m),show:f(_.show,m),hide:f(_.hide,m),beforeShow:f(_.beforeShow,m)},_),x=w.flat,k=w.showSelectionPalette,S=w.localStorageKey,C=w.theme,P=w.callbacks,A=(p=tM,function(){var t=this,e=arguments,r=function(){b=null,p.apply(t,e)};g&&clearTimeout(b),(g||!b)&&(b=setTimeout(r,10))}),H=!1,R=!1,O=0,T=0,F=0,q=0,D=0,N=0,j=0,M=0,I=0,z=0,E=0,B=1,L=[],K=[],V={},W=w.selectionPalette.slice(0),X=w.maxSelectionSize,Y="sp-dragging",G=null,J=d.ownerDocument,Q=(J.body,t(d)),U=!1,Z=t(i,J).addClass(C),tt=Z.find(".sp-picker-container"),te=Z.find(".sp-color"),tr=Z.find(".sp-dragger"),ta=Z.find(".sp-hue"),tn=Z.find(".sp-slider"),ts=Z.find(".sp-alpha-inner"),ti=Z.find(".sp-alpha"),to=Z.find(".sp-alpha-handle"),tl=Z.find(".sp-input"),tc=Z.find(".sp-palette"),tf=Z.find(".sp-initial"),th=Z.find(".sp-cancel"),tu=Z.find(".sp-clear"),td=Z.find(".sp-choose"),t$=Z.find(".sp-palette-toggle"),tp=Q.is("input"),tg=tp&&"color"===Q.attr("type")&&u(),tb=tp&&!x,tv=tb?t("<div class='sp-replacer'><div class='sp-preview'><div class='sp-preview-inner'></div></div><div class='sp-dd'>&#9660;</div></div>").addClass(C).addClass(w.className).addClass(w.replacerClassName):t([]),tm=tb?tv:Q,t_=tv.find(".sp-preview-inner"),ty=w.color||tp&&Q.val(),t3=!1,t0=w.preferredFormat,t8=!w.showButtons||w.clickoutFiresChange,tw=!ty,tx=w.allowEmpty&&!tg;function t4(){if(w.showPaletteOnly&&(w.showPalette=!0),t$.text(w.showPaletteOnly?w.togglePaletteMoreText:w.togglePaletteLessText),w.palette){L=w.palette.slice(0),K=t.isArray(L[0])?L:[L],V={};for(var e=0;e<K.length;e++)for(var r=0;r<K[e].length;r++)V[tinycolor(K[e][r]).toRgbString()]=!0}Z.toggleClass("sp-flat",x),Z.toggleClass("sp-input-disabled",!w.showInput),Z.toggleClass("sp-alpha-enabled",w.showAlpha),Z.toggleClass("sp-clear-enabled",tx),Z.toggleClass("sp-buttons-disabled",!w.showButtons),Z.toggleClass("sp-palette-buttons-disabled",!w.togglePaletteOnly),Z.toggleClass("sp-palette-disabled",!w.showPalette),Z.toggleClass("sp-palette-only",w.showPaletteOnly),Z.toggleClass("sp-initial-disabled",!w.showInitial),Z.addClass(w.className).addClass(w.containerClassName),tM()}function tk(){if(S&&window.localStorage){try{var e=window.localStorage[S].split(",#");e.length>1&&(delete window.localStorage[S],t.each(e,function(t,e){t6(e)}))}catch(r){}try{W=window.localStorage[S].split(";")}catch(a){}}}function t6(e){if(k){var r=tinycolor(e).toRgbString();if(!V[r]&&-1===t.inArray(r,W))for(W.push(r);W.length>X;)W.shift();if(S&&window.localStorage)try{window.localStorage[S]=W.join(";")}catch(a){}}}function t1(){var e=tF(),r=t.map(K,function(t,r){return o(t,e,"sp-palette-row sp-palette-row-"+r,w)});tk(),W&&r.push(o(function t(){var e=[];if(w.showPalette)for(var r=0;r<W.length;r++)V[tinycolor(W[r]).toRgbString()]||e.push(W[r]);return e.reverse().slice(0,w.maxSelectionSize)}(),e,"sp-palette-row sp-palette-row-selection",w)),tc.html(r.join(""))}function tS(){if(w.showInitial){var t=t3,e=tF();tf.html(o([t,e],e,"sp-palette-row-initial",w))}}function tC(){(T<=0||O<=0||q<=0)&&tM(),R=!0,Z.addClass(Y),G=null,Q.trigger("dragstart.spectrum",[tF()])}function t7(){R=!1,Z.removeClass(Y),Q.trigger("dragstop.spectrum",[tF()])}function tP(){var t=tl.val();if((null===t||""===t)&&tx)tT(null),tq(),tj();else{var e=tinycolor(t);e.isValid()?(tT(e),tq(),tj()):tl.addClass("sp-validation-error")}}function t2(){H?tR():tA()}function tA(){var e=t.Event("beforeShow.spectrum");if(H){tM();return}Q.trigger(e,[tF()]),!(!1===P.beforeShow(tF())||e.isDefaultPrevented())&&(function t(){for(var e=0;e<a.length;e++)a[e]&&a[e].hide()}(),H=!0,t(J).on("keydown.spectrum",tH),t(J).on("click.spectrum",t5),t(window).on("resize.spectrum",A),tv.addClass("sp-active"),Z.removeClass("sp-hidden"),tM(),tD(),t3=tF(),tS(),P.show(t3),Q.trigger("show.spectrum",[t3]))}function tH(t){27===t.keyCode&&tR()}function t5(t){2!=t.button&&!R&&(t8?tj(!0):tO(),tR())}function tR(){H&&!x&&(H=!1,t(J).off("keydown.spectrum",tH),t(J).off("click.spectrum",t5),t(window).off("resize.spectrum",A),tv.removeClass("sp-active"),Z.addClass("sp-hidden"),P.hide(tF()),Q.trigger("hide.spectrum",[tF()]))}function tO(){tT(t3,!0),tj(!0)}function tT(t,e){var r,a;if(tinycolor.equals(t,tF())){tD();return}!t&&tx?tw=!0:(tw=!1,I=(a=(r=tinycolor(t)).toHsv()).h%360/360,z=a.s,E=a.v,B=a.a),tD(),r&&r.isValid()&&!e&&(t0=w.preferredFormat||r.getFormat())}function tF(t){return(t=t||{},tx&&tw)?null:tinycolor.fromRatio({h:I,s:z,v:E,a:Math.round(1e3*B)/1e3},{format:t.format||t0})}function tq(){tD(),P.move(tF()),Q.trigger("move.spectrum",[tF()])}function tD(){tl.removeClass("sp-validation-error"),tN();var t=tinycolor.fromRatio({h:I,s:1,v:1});te.css("background-color",t.toHexString());var e=t0;B<1&&!(0===B&&"name"===e)&&("hex"===e||"hex3"===e||"hex6"===e||"name"===e)&&(e="rgb");var r=tF({format:e}),a="";if(t_.removeClass("sp-clear-display"),t_.css("background-color","transparent"),!r&&tx)t_.addClass("sp-clear-display");else{var i=r.toHexString(),o=r.toRgbString();if(s||1===r.alpha?t_.css("background-color",o):(t_.css("background-color","transparent"),t_.css("filter",r.toFilter())),w.showAlpha){var l=r.toRgb();l.a=0;var c=tinycolor(l).toRgbString(),f="linear-gradient(left, "+c+", "+i+")";n?ts.css("filter",tinycolor(c).toFilter({gradientType:1},i)):(ts.css("background","-webkit-"+f),ts.css("background","-moz-"+f),ts.css("background","-ms-"+f),ts.css("background","linear-gradient(to right, "+c+", "+i+")"))}a=r.toString(e)}w.showInput&&tl.val(a),w.showPalette&&t1(),tS()}function tN(){var t=z,e=E;if(tx&&tw)to.hide(),tn.hide(),tr.hide();else{to.show(),tn.show(),tr.show();var r=t*O,a=T-e*T;r=Math.max(-F,Math.min(O-F,r-F)),a=Math.max(-F,Math.min(T-F,a-F)),tr.css({top:a+"px",left:r+"px"});var n=B*N;to.css({left:n-j/2+"px"});var s=I*q;tn.css({top:s-M+"px"})}}function tj(t){var e=tF(),r="",a=!tinycolor.equals(e,t3);e&&(r=e.toString(t0),t6(e)),tp&&Q.val(r),t&&a&&(P.change(e),Q.trigger("change",[e]))}function tM(){if(H){var e,r,a,n,s,i,o,l,c,f,h,u;O=te.width(),T=te.height(),F=tr.height(),D=ta.width(),q=ta.height(),M=tn.height(),N=ti.width(),j=to.width(),x||(Z.css("position","absolute"),w.offset?Z.offset(w.offset):Z.offset((e=Z,r=tm,a=e.outerWidth(),n=e.outerHeight(),s=r.outerHeight(),i=e[0].ownerDocument,o=i.documentElement,l=o.clientWidth+t(i).scrollLeft(),c=o.clientHeight+t(i).scrollTop(),f=r.offset(),h=f.left,u=f.top,u+=s,h-=Math.min(h,h+a>l&&l>a?Math.abs(h+a-l):0),u-=Math.min(u,u+n>c&&c>n?Math.abs(n+s-0):0),{top:u,bottom:f.bottom,left:h,right:f.right,width:f.width,height:f.height}))),tN(),w.showPalette&&t1(),Q.trigger("reflow.spectrum")}}function tI(){tR(),U=!0,Q.attr("disabled",!0),tm.addClass("sp-disabled")}!function e(){if(n&&Z.find("*:not(input)").attr("unselectable","on"),t4(),tb&&Q.after(tv).hide(),tx||tu.hide(),x)Q.after(Z).hide();else{var r="parent"===w.appendTo?Q.parent():t(w.appendTo);1!==r.length&&(r=t("body")),r.append(Z)}function a(e){return e.data&&e.data.ignore?(tT(t(e.target).closest(".sp-thumb-el").data("color")),tq()):(tT(t(e.target).closest(".sp-thumb-el").data("color")),tq(),w.hideAfterPaletteSelect?(tj(!0),tR()):tj()),!1}tk(),tm.on("click.spectrum touchstart.spectrum",function(e){U||t2(),e.stopPropagation(),t(e.target).is("input")||e.preventDefault()}),(Q.is(":disabled")||!0===w.disabled)&&tI(),Z.click(c),tl.change(tP),tl.on("paste",function(){setTimeout(tP,1)}),tl.keydown(function(t){13==t.keyCode&&tP()}),th.text(w.cancelText),th.on("click.spectrum",function(t){t.stopPropagation(),t.preventDefault(),tO(),tR()}),tu.attr("title",w.clearText),tu.on("click.spectrum",function(t){t.stopPropagation(),t.preventDefault(),tw=!0,tq(),x&&tj(!0)}),td.text(w.chooseText),td.on("click.spectrum",function(t){t.stopPropagation(),t.preventDefault(),n&&tl.is(":focus")&&tl.trigger("change"),tl.hasClass("sp-validation-error")||(tj(!0),tR())}),t$.text(w.showPaletteOnly?w.togglePaletteMoreText:w.togglePaletteLessText),t$.on("click.spectrum",function(t){t.stopPropagation(),t.preventDefault(),w.showPaletteOnly=!w.showPaletteOnly,w.showPaletteOnly||x||Z.css("left","-="+(tt.outerWidth(!0)+5)),t4()}),h(ti,function(t,e,r){B=t/N,tw=!1,r.shiftKey&&(B=Math.round(10*B)/10),tq()},tC,t7),h(ta,function(t,e){I=parseFloat(e/q),tw=!1,w.showAlpha||(B=1),tq()},tC,t7),h(te,function(t,e,r){if(r.shiftKey){if(!G){var a,n;G=Math.abs(t-z*O)>Math.abs(e-(T-E*T))?"x":"y"}}else G=null;var s=!G||"x"===G,i=!G||"y"===G;s&&(z=parseFloat(t/O)),i&&(E=parseFloat((T-e)/T)),tw=!1,w.showAlpha||(B=1),tq()},tC,t7),ty?(tT(ty),tD(),t0=w.preferredFormat||tinycolor(ty).format,t6(ty)):tD(),x&&tA();var s=n?"mousedown.spectrum":"click.spectrum touchstart.spectrum";tc.on(s,".sp-thumb-el",a),tf.on(s,".sp-thumb-el:nth-child(1)",{ignore:!0},a)}();var tz={show:tA,hide:tR,toggle:t2,reflow:tM,option:function r(a,n){return a===e?t.extend({},w):n===e?w[a]:void(w[a]=n,"preferredFormat"===a&&(t0=w.preferredFormat),t4())},enable:function t(){U=!1,Q.attr("disabled",!1),tm.removeClass("sp-disabled")},disable:tI,offset:function t(e){w.offset=e,tM()},set:function(t){tT(t),tj()},get:tF,destroy:function t(){Q.show(),tm.off("click.spectrum touchstart.spectrum"),Z.remove(),tv.remove(),a[tz.id]=null},container:Z};return tz.id=a.push(tz)-1,tz}(this,t.extend({},t(this).data(),l));t(this).data(d,$.id)})},t.fn.spectrum.load=!0,t.fn.spectrum.loadOpts={},t.fn.spectrum.draggable=h,t.fn.spectrum.defaults=r,t.fn.spectrum.inputTypeColorSupport=function e(){if(void 0===e._cachedResult){var r=t("<input type='color'/>")[0];e._cachedResult="color"===r.type&&""!==r.value}return e._cachedResult},t.spectrum={},t.spectrum.localization={},t.spectrum.palettes={},t.fn.spectrum.processNativeColorInputs=function(){var e=t("input[type=color]");e.length&&!u()&&e.spectrum({preferredFormat:"hex6"})},function(){var t=/^[\s,#]+/,e=/\s+$/,r=0,a=Math,n=a.round,s=a.min,i=a.max,o=a.random,l=function(o,c){if(o=o||"",c=c||{},o instanceof l)return o;if(!(this instanceof l))return new l(o,c);var f,h,u,d,$,p,g,b,v,m,_,y,w,x,k,S,P,R,T,q,D=(f=o,h={r:0,g:0,b:0},u=1,d=!1,$=!1,"string"==typeof f&&(f=function r(a){a=a.replace(t,"").replace(e,"").toLowerCase();var n,s,i=!1;if(C[a])a=C[a],i=!0;else if("transparent"==a)return{r:0,g:0,b:0,a:0,format:"name"};return(s=j.rgb.exec(a))?{r:s[1],g:s[2],b:s[3]}:(s=j.rgba.exec(a))?{r:s[1],g:s[2],b:s[3],a:s[4]}:(s=j.hsl.exec(a))?{h:s[1],s:s[2],l:s[3]}:(s=j.hsla.exec(a))?{h:s[1],s:s[2],l:s[3],a:s[4]}:(s=j.hsv.exec(a))?{h:s[1],s:s[2],v:s[3]}:(s=j.hsva.exec(a))?{h:s[1],s:s[2],v:s[3],a:s[4]}:(s=j.hex8.exec(a))?{a:O(n=s[1])/255,r:O(s[2]),g:O(s[3]),b:O(s[4]),format:i?"name":"hex8"}:(s=j.hex6.exec(a))?{r:O(s[1]),g:O(s[2]),b:O(s[3]),format:i?"name":"hex"}:!!(s=j.hex3.exec(a))&&{r:O(s[1]+""+s[1]),g:O(s[2]+""+s[2]),b:O(s[3]+""+s[3]),format:i?"name":"hex"}}(f)),"object"==typeof f&&(f.hasOwnProperty("r")&&f.hasOwnProperty("g")&&f.hasOwnProperty("b")?(h=(p=f.r,g=f.g,b=f.b,{r:255*H(p,255),g:255*H(g,255),b:255*H(b,255)}),d=!0,$="%"===String(f.r).substr(-1)?"prgb":"rgb"):f.hasOwnProperty("h")&&f.hasOwnProperty("s")&&f.hasOwnProperty("v")?(f.s=F(f.s),f.v=F(f.v),h=(v=f.h,m=f.s,_=f.v,v=6*H(v,360),m=H(m,100),_=H(_,100),y=a.floor(v),w=v-y,x=_*(1-m),k=_*(1-w*m),S=_*(1-(1-w)*m),P=y%6,R=[_,k,x,x,S,_][P],T=[S,_,_,k,x,x][P],q=[x,x,S,_,_,k][P],{r:255*R,g:255*T,b:255*q}),d=!0,$="hsv"):f.hasOwnProperty("h")&&f.hasOwnProperty("s")&&f.hasOwnProperty("l")&&(f.s=F(f.s),f.l=F(f.l),h=function t(e,r,a){var n,s,i;function o(t,e,r){return(r<0&&(r+=1),r>1&&(r-=1),r<1/6)?t+(e-t)*6*r:r<.5?e:r<2/3?t+(e-t)*(2/3-r)*6:t}if(e=H(e,360),r=H(r,100),a=H(a,100),0===r)n=s=i=a;else{var l=a<.5?a*(1+r):a+r-a*r,c=2*a-l;n=o(c,l,e+1/3),s=o(c,l,e),i=o(c,l,e-1/3)}return{r:255*n,g:255*s,b:255*i}}(f.h,f.s,f.l),d=!0,$="hsl"),f.hasOwnProperty("a")&&(u=f.a)),u=A(u),{ok:d,format:f.format||$,r:s(255,i(h.r,0)),g:s(255,i(h.g,0)),b:s(255,i(h.b,0)),a:u});this._originalInput=o,this._r=D.r,this._g=D.g,this._b=D.b,this._a=D.a,this._roundA=n(1e3*this._a)/1e3,this._format=c.format||D.format,this._gradientType=c.gradientType,this._r<1&&(this._r=n(this._r)),this._g<1&&(this._g=n(this._g)),this._b<1&&(this._b=n(this._b)),this._ok=D.ok,this._tc_id=r++};function c(t,e,r){t=H(t,255),e=H(e,255),r=H(r,255);var a,n,o=i(t,e,r),l=s(t,e,r),c=(o+l)/2;if(o==l)a=n=0;else{var f=o-l;switch(n=c>.5?f/(2-o-l):f/(o+l),o){case t:a=(e-r)/f+(e<r?6:0);break;case e:a=(r-t)/f+2;break;case r:a=(t-e)/f+4}a/=6}return{h:a,s:n,l:c}}function f(t,e,r){t=H(t,255),e=H(e,255),r=H(r,255);var a,n,o=i(t,e,r),l=s(t,e,r),c=o-l;if(n=0===o?0:c/o,o==l)a=0;else{switch(o){case t:a=(e-r)/c+(e<r?6:0);break;case e:a=(r-t)/c+2;break;case r:a=(t-e)/c+4}a/=6}return{h:a,s:n,v:o}}function h(t,e,r,a){var s=[T(n(t).toString(16)),T(n(e).toString(16)),T(n(r).toString(16))];return a&&s[0].charAt(0)==s[0].charAt(1)&&s[1].charAt(0)==s[1].charAt(1)&&s[2].charAt(0)==s[2].charAt(1)?s[0].charAt(0)+s[1].charAt(0)+s[2].charAt(0):s.join("")}function u(t,e,r,a){var s;return[T((s=a,Math.round(255*parseFloat(s)).toString(16))),T(n(t).toString(16)),T(n(e).toString(16)),T(n(r).toString(16))].join("")}function d(t,e){e=0===e?0:e||10;var r=l(t).toHsl();return r.s-=e/100,r.s=R(r.s),l(r)}function $(t,e){e=0===e?0:e||10;var r=l(t).toHsl();return r.s+=e/100,r.s=R(r.s),l(r)}function p(t){return l(t).desaturate(100)}function g(t,e){e=0===e?0:e||10;var r=l(t).toHsl();return r.l+=e/100,r.l=R(r.l),l(r)}function b(t,e){e=0===e?0:e||10;var r=l(t).toRgb();return r.r=i(0,s(255,r.r-n(-(255*(e/100))))),r.g=i(0,s(255,r.g-n(-(255*(e/100))))),r.b=i(0,s(255,r.b-n(-(255*(e/100))))),l(r)}function v(t,e){e=0===e?0:e||10;var r=l(t).toHsl();return r.l-=e/100,r.l=R(r.l),l(r)}function m(t,e){var r=l(t).toHsl(),a=(n(r.h)+e)%360;return r.h=a<0?360+a:a,l(r)}function _(t){var e=l(t).toHsl();return e.h=(e.h+180)%360,l(e)}function y(t){var e=l(t).toHsl(),r=e.h;return[l(t),l({h:(r+120)%360,s:e.s,l:e.l}),l({h:(r+240)%360,s:e.s,l:e.l})]}function w(t){var e=l(t).toHsl(),r=e.h;return[l(t),l({h:(r+90)%360,s:e.s,l:e.l}),l({h:(r+180)%360,s:e.s,l:e.l}),l({h:(r+270)%360,s:e.s,l:e.l})]}function x(t){var e=l(t).toHsl(),r=e.h;return[l(t),l({h:(r+72)%360,s:e.s,l:e.l}),l({h:(r+216)%360,s:e.s,l:e.l})]}function k(t,e,r){e=e||6,r=r||30;var a=l(t).toHsl(),n=360/r,s=[l(t)];for(a.h=(a.h-(n*e>>1)+720)%360;--e;)a.h=(a.h+n)%360,s.push(l(a));return s}function S(t,e){e=e||6;for(var r=l(t).toHsv(),a=r.h,n=r.s,s=r.v,i=[],o=1/e;e--;)i.push(l({h:a,s:n,v:s})),s=(s+o)%1;return i}l.prototype={isDark:function(){return 128>this.getBrightness()},isLight:function(){return!this.isDark()},isValid:function(){return this._ok},getOriginalInput:function(){return this._originalInput},getFormat:function(){return this._format},getAlpha:function(){return this._a},getBrightness:function(){var t=this.toRgb();return(299*t.r+587*t.g+114*t.b)/1e3},setAlpha:function(t){return this._a=A(t),this._roundA=n(1e3*this._a)/1e3,this},toHsv:function(){var t=f(this._r,this._g,this._b);return{h:360*t.h,s:t.s,v:t.v,a:this._a}},toHsvString:function(){var t=f(this._r,this._g,this._b),e=n(360*t.h),r=n(100*t.s),a=n(100*t.v);return 1==this._a?"hsv("+e+", "+r+"%, "+a+"%)":"hsva("+e+", "+r+"%, "+a+"%, "+this._roundA+")"},toHsl:function(){var t=c(this._r,this._g,this._b);return{h:360*t.h,s:t.s,l:t.l,a:this._a}},toHslString:function(){var t=c(this._r,this._g,this._b),e=n(360*t.h),r=n(100*t.s),a=n(100*t.l);return 1==this._a?"hsl("+e+", "+r+"%, "+a+"%)":"hsla("+e+", "+r+"%, "+a+"%, "+this._roundA+")"},toHex:function(t){return h(this._r,this._g,this._b,t)},toHexString:function(t){return"#"+this.toHex(t)},toHex8:function(){return u(this._r,this._g,this._b,this._a)},toHex8String:function(){return"#"+this.toHex8()},toRgb:function(){return{r:n(this._r),g:n(this._g),b:n(this._b),a:this._a}},toRgbString:function(){return 1==this._a?"rgb("+n(this._r)+", "+n(this._g)+", "+n(this._b)+")":"rgba("+n(this._r)+", "+n(this._g)+", "+n(this._b)+", "+this._roundA+")"},toPercentageRgb:function(){return{r:n(100*H(this._r,255))+"%",g:n(100*H(this._g,255))+"%",b:n(100*H(this._b,255))+"%",a:this._a}},toPercentageRgbString:function(){return 1==this._a?"rgb("+n(100*H(this._r,255))+"%, "+n(100*H(this._g,255))+"%, "+n(100*H(this._b,255))+"%)":"rgba("+n(100*H(this._r,255))+"%, "+n(100*H(this._g,255))+"%, "+n(100*H(this._b,255))+"%, "+this._roundA+")"},toName:function(){return 0===this._a?"transparent":!(this._a<1)&&!!P[h(this._r,this._g,this._b,!0)]},toFilter:function(t){var e="#"+u(this._r,this._g,this._b,this._a),r=e,a=this._gradientType?"GradientType = 1, ":"";return t&&(r=l(t).toHex8String()),"progid:DXImageTransform.Microsoft.gradient("+a+"startColorstr="+e+",endColorstr="+r+")"},toString:function(t){var e=!!t;t=t||this._format;var r=!1,a=this._a<1&&this._a>=0;return!e&&a&&("hex"===t||"hex6"===t||"hex3"===t||"name"===t)?"name"===t&&0===this._a?this.toName():this.toRgbString():("rgb"===t&&(r=this.toRgbString()),"prgb"===t&&(r=this.toPercentageRgbString()),("hex"===t||"hex6"===t)&&(r=this.toHexString()),"hex3"===t&&(r=this.toHexString(!0)),"hex8"===t&&(r=this.toHex8String()),"name"===t&&(r=this.toName()),"hsl"===t&&(r=this.toHslString()),"hsv"===t&&(r=this.toHsvString()),r||this.toHexString())},_applyModification:function(t,e){var r=t.apply(null,[this].concat([].slice.call(e)));return this._r=r._r,this._g=r._g,this._b=r._b,this.setAlpha(r._a),this},lighten:function(){return this._applyModification(g,arguments)},brighten:function(){return this._applyModification(b,arguments)},darken:function(){return this._applyModification(v,arguments)},desaturate:function(){return this._applyModification(d,arguments)},saturate:function(){return this._applyModification($,arguments)},greyscale:function(){return this._applyModification(p,arguments)},spin:function(){return this._applyModification(m,arguments)},_applyCombination:function(t,e){return t.apply(null,[this].concat([].slice.call(e)))},analogous:function(){return this._applyCombination(k,arguments)},complement:function(){return this._applyCombination(_,arguments)},monochromatic:function(){return this._applyCombination(S,arguments)},splitcomplement:function(){return this._applyCombination(x,arguments)},triad:function(){return this._applyCombination(y,arguments)},tetrad:function(){return this._applyCombination(w,arguments)}},l.fromRatio=function(t,e){if("object"==typeof t){var r={};for(var a in t)t.hasOwnProperty(a)&&("a"===a?r[a]=t[a]:r[a]=F(t[a]));t=r}return l(t,e)},l.equals=function(t,e){return!!t&&!!e&&l(t).toRgbString()==l(e).toRgbString()},l.random=function(){return l.fromRatio({r:o(),g:o(),b:o()})},l.mix=function(t,e,r){r=0===r?0:r||50;var a,n=l(t).toRgb(),s=l(e).toRgb(),i=r/100,o=2*i-1,c=s.a-n.a,f=1-(a=((a=o*c==-1?o:(o+c)/(1+o*c))+1)/2);return l({r:s.r*a+n.r*f,g:s.g*a+n.g*f,b:s.b*a+n.b*f,a:s.a*i+n.a*(1-i)})},l.readability=function(t,e){var r,a=l(t),n=l(e),s=a.toRgb(),i=n.toRgb(),o=a.getBrightness();return{brightness:Math.abs(o-n.getBrightness()),color:Math.max(s.r,i.r)-Math.min(s.r,i.r)+Math.max(s.g,i.g)-Math.min(s.g,i.g)+Math.max(s.b,i.b)-Math.min(s.b,i.b)}},l.isReadable=function(t,e){var r=l.readability(t,e);return r.brightness>125&&r.color>500},l.mostReadable=function(t,e){for(var r=null,a=0,n=!1,s=0;s<e.length;s++){var i=l.readability(t,e[s]),o=i.brightness>125&&i.color>500,c=3*(i.brightness/125)+i.color/500;(o&&!n||o&&n&&c>a||!o&&!n&&c>a)&&(n=o,a=c,r=l(e[s]))}return r};var C=l.names={aliceblue:"f0f8ff",antiquewhite:"faebd7",aqua:"0ff",aquamarine:"7fffd4",azure:"f0ffff",beige:"f5f5dc",bisque:"ffe4c4",black:"000",blanchedalmond:"ffebcd",blue:"00f",blueviolet:"8a2be2",brown:"a52a2a",burlywood:"deb887",burntsienna:"ea7e5d",cadetblue:"5f9ea0",chartreuse:"7fff00",chocolate:"d2691e",coral:"ff7f50",cornflowerblue:"6495ed",cornsilk:"fff8dc",crimson:"dc143c",cyan:"0ff",darkblue:"00008b",darkcyan:"008b8b",darkgoldenrod:"b8860b",darkgray:"a9a9a9",darkgreen:"006400",darkgrey:"a9a9a9",darkkhaki:"bdb76b",darkmagenta:"8b008b",darkolivegreen:"556b2f",darkorange:"ff8c00",darkorchid:"9932cc",darkred:"8b0000",darksalmon:"e9967a",darkseagreen:"8fbc8f",darkslateblue:"483d8b",darkslategray:"2f4f4f",darkslategrey:"2f4f4f",darkturquoise:"00ced1",darkviolet:"9400d3",deeppink:"ff1493",deepskyblue:"00bfff",dimgray:"696969",dimgrey:"696969",dodgerblue:"1e90ff",firebrick:"b22222",floralwhite:"fffaf0",forestgreen:"228b22",fuchsia:"f0f",gainsboro:"dcdcdc",ghostwhite:"f8f8ff",gold:"ffd700",goldenrod:"daa520",gray:"808080",green:"008000",greenyellow:"adff2f",grey:"808080",honeydew:"f0fff0",hotpink:"ff69b4",indianred:"cd5c5c",indigo:"4b0082",ivory:"fffff0",khaki:"f0e68c",lavender:"e6e6fa",lavenderblush:"fff0f5",lawngreen:"7cfc00",lemonchiffon:"fffacd",lightblue:"add8e6",lightcoral:"f08080",lightcyan:"e0ffff",lightgoldenrodyellow:"fafad2",lightgray:"d3d3d3",lightgreen:"90ee90",lightgrey:"d3d3d3",lightpink:"ffb6c1",lightsalmon:"ffa07a",lightseagreen:"20b2aa",lightskyblue:"87cefa",lightslategray:"789",lightslategrey:"789",lightsteelblue:"b0c4de",lightyellow:"ffffe0",lime:"0f0",limegreen:"32cd32",linen:"faf0e6",magenta:"f0f",maroon:"800000",mediumaquamarine:"66cdaa",mediumblue:"0000cd",mediumorchid:"ba55d3",mediumpurple:"9370db",mediumseagreen:"3cb371",mediumslateblue:"7b68ee",mediumspringgreen:"00fa9a",mediumturquoise:"48d1cc",mediumvioletred:"c71585",midnightblue:"191970",mintcream:"f5fffa",mistyrose:"ffe4e1",moccasin:"ffe4b5",navajowhite:"ffdead",navy:"000080",oldlace:"fdf5e6",olive:"808000",olivedrab:"6b8e23",orange:"ffa500",orangered:"ff4500",orchid:"da70d6",palegoldenrod:"eee8aa",palegreen:"98fb98",paleturquoise:"afeeee",palevioletred:"db7093",papayawhip:"ffefd5",peachpuff:"ffdab9",peru:"cd853f",pink:"ffc0cb",plum:"dda0dd",powderblue:"b0e0e6",purple:"800080",rebeccapurple:"663399",red:"f00",rosybrown:"bc8f8f",royalblue:"4169e1",saddlebrown:"8b4513",salmon:"fa8072",sandybrown:"f4a460",seagreen:"2e8b57",seashell:"fff5ee",sienna:"a0522d",silver:"c0c0c0",skyblue:"87ceeb",slateblue:"6a5acd",slategray:"708090",slategrey:"708090",snow:"fffafa",springgreen:"00ff7f",steelblue:"4682b4",tan:"d2b48c",teal:"008080",thistle:"d8bfd8",tomato:"ff6347",turquoise:"40e0d0",violet:"ee82ee",wheat:"f5deb3",white:"fff",whitesmoke:"f5f5f5",yellow:"ff0",yellowgreen:"9acd32"},P=l.hexNames=function t(e){var r={};for(var a in e)e.hasOwnProperty(a)&&(r[e[a]]=a);return r}(C);function A(t){return(isNaN(t=parseFloat(t))||t<0||t>1)&&(t=1),t}function H(t,e){r=t,"string"==typeof r&&-1!=r.indexOf(".")&&1===parseFloat(r)&&(t="100%");var r,n,o=(n=t,"string"==typeof n&&-1!=n.indexOf("%"));return(t=s(e,i(0,parseFloat(t))),o&&(t=parseInt(t*e,10)/100),1e-6>a.abs(t-e))?1:t%e/parseFloat(e)}function R(t){return s(1,i(0,t))}function O(t){return parseInt(t,16)}function T(t){return 1==t.length?"0"+t:""+t}function F(t){return t<=1&&(t=100*t+"%"),t}var q,D,N,j=(D="[\\s|\\(]+("+(q="(?:[-\\+]?\\d*\\.\\d+%?)|(?:[-\\+]?\\d+%?)")+")[,|\\s]+("+q+")[,|\\s]+("+q+")\\s*\\)?",N="[\\s|\\(]+("+q+")[,|\\s]+("+q+")[,|\\s]+("+q+")[,|\\s]+("+q+")\\s*\\)?",{rgb:RegExp("rgb"+D),rgba:RegExp("rgba"+N),hsl:RegExp("hsl"+D),hsla:RegExp("hsla"+N),hsv:RegExp("hsv"+D),hsva:RegExp("hsva"+N),hex3:/^([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,hex6:/^([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,hex8:/^([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/});window.tinycolor=l}(),t(function(){t.fn.spectrum.load&&t.fn.spectrum.processNativeColorInputs()})});

/* ImgAreaSelect V0.9.10*/
(function($){var abs=Math.abs,max=Math.max,min=Math.min,round=Math.round;function div(){return $('<div/>')}$.imgAreaSelect=function(img,options){var $img=$(img),imgLoaded,$box=div(),$area=div(),$border=div().add(div()).add(div()).add(div()),$outer=div().add(div()).add(div()).add(div()),$handles=$([]),$areaOpera,left,top,imgOfs={left:0,top:0},imgWidth,imgHeight,$parent,parOfs={left:0,top:0},zIndex=0,position='absolute',startX,startY,scaleX,scaleY,resize,minWidth,minHeight,maxWidth,maxHeight,aspectRatio,shown,x1,y1,x2,y2,selection={x1:0,y1:0,x2:0,y2:0,width:0,height:0},docElem=document.documentElement,ua=navigator.userAgent,$p,d,i,o,w,h,adjusted;function viewX(x){return x+imgOfs.left-parOfs.left}function viewY(y){return y+imgOfs.top-parOfs.top}function selX(x){return x-imgOfs.left+parOfs.left}function selY(y){return y-imgOfs.top+parOfs.top}function evX(event){return event.pageX-parOfs.left}function evY(event){return event.pageY-parOfs.top}function getSelection(noScale){var sx=noScale||scaleX,sy=noScale||scaleY;return{x1:round(selection.x1*sx),y1:round(selection.y1*sy),x2:round(selection.x2*sx),y2:round(selection.y2*sy),width:round(selection.x2*sx)-round(selection.x1*sx),height:round(selection.y2*sy)-round(selection.y1*sy)}}function setSelection(x1,y1,x2,y2,noScale){var sx=noScale||scaleX,sy=noScale||scaleY;selection={x1:round(x1/sx||0),y1:round(y1/sy||0),x2:round(x2/sx||0),y2:round(y2/sy||0)};selection.width=selection.x2-selection.x1;selection.height=selection.y2-selection.y1}function adjust(){if(!imgLoaded||!$img.width())return;imgOfs={left:round($img.offset().left),top:round($img.offset().top)};imgWidth=$img.innerWidth();imgHeight=$img.innerHeight();imgOfs.top+=($img.outerHeight()-imgHeight)>>1;imgOfs.left+=($img.outerWidth()-imgWidth)>>1;minWidth=round(options.minWidth/scaleX)||0;minHeight=round(options.minHeight/scaleY)||0;maxWidth=round(min(options.maxWidth/scaleX||1<<24,imgWidth));maxHeight=round(min(options.maxHeight/scaleY||1<<24,imgHeight));if($().jquery=='1.3.2'&&position=='fixed'&&!docElem['getBoundingClientRect']){imgOfs.top+=max(document.body.scrollTop,docElem.scrollTop);imgOfs.left+=max(document.body.scrollLeft,docElem.scrollLeft)}parOfs=/absolute|relative/.test($parent.css('position'))?{left:round($parent.offset().left)-$parent.scrollLeft(),top:round($parent.offset().top)-$parent.scrollTop()}:position=='fixed'?{left:$(document).scrollLeft(),top:$(document).scrollTop()}:{left:0,top:0};left=viewX(0);top=viewY(0);if(selection.x2>imgWidth||selection.y2>imgHeight)doResize()}function update(resetKeyPress){if(!shown)return;$box.css({left:viewX(selection.x1),top:viewY(selection.y1)}).add($area).width(w=selection.width).height(h=selection.height);$area.add($border).add($handles).css({left:0,top:0});$border.width(max(w-$border.outerWidth()+$border.innerWidth(),0)).height(max(h-$border.outerHeight()+$border.innerHeight(),0));$($outer[0]).css({left:left,top:top,width:selection.x1,height:imgHeight});$($outer[1]).css({left:left+selection.x1,top:top,width:w,height:selection.y1});$($outer[2]).css({left:left+selection.x2,top:top,width:imgWidth-selection.x2,height:imgHeight});$($outer[3]).css({left:left+selection.x1,top:top+selection.y2,width:w,height:imgHeight-selection.y2});w-=$handles.outerWidth();h-=$handles.outerHeight();switch($handles.length){case 8:$($handles[4]).css({left:w>>1});$($handles[5]).css({left:w,top:h>>1});$($handles[6]).css({left:w>>1,top:h});$($handles[7]).css({top:h>>1});case 4:$handles.slice(1,3).css({left:w});$handles.slice(2,4).css({top:h})}if(resetKeyPress!==false){if($.imgAreaSelect.onKeyPress!=docKeyPress)$(document).unbind($.imgAreaSelect.keyPress,$.imgAreaSelect.onKeyPress);if(options.keys)$(document)[$.imgAreaSelect.keyPress]($.imgAreaSelect.onKeyPress=docKeyPress)}if(msie&&$border.outerWidth()-$border.innerWidth()==2){$border.css('margin',0);setTimeout(function(){$border.css('margin','auto')},0)}}function doUpdate(resetKeyPress){adjust();update(resetKeyPress);x1=viewX(selection.x1);y1=viewY(selection.y1);x2=viewX(selection.x2);y2=viewY(selection.y2)}function hide($elem,fn){options.fadeSpeed?$elem.fadeOut(options.fadeSpeed,fn):$elem.hide()}function areaMouseMove(event){var x=selX(evX(event))-selection.x1,y=selY(evY(event))-selection.y1;if(!adjusted){adjust();adjusted=true;$box.one('mouseout',function(){adjusted=false})}resize='';if(options.resizable){if(y<=options.resizeMargin)resize='n';else if(y>=selection.height-options.resizeMargin)resize='s';if(x<=options.resizeMargin)resize+='w';else if(x>=selection.width-options.resizeMargin)resize+='e'}$box.css('cursor',resize?resize+'-resize':options.movable?'move':'');if($areaOpera)$areaOpera.toggle()}function docMouseUp(event){$('body').css('cursor','');if(options.autoHide||selection.width*selection.height==0)hide($box.add($outer),function(){$(this).hide()});$(document).unbind('mousemove',selectingMouseMove);$box.mousemove(areaMouseMove);options.onSelectEnd(img,getSelection())}function areaMouseDown(event){if(event.which!=1)return false;adjust();if(resize){$('body').css('cursor',resize+'-resize');x1=viewX(selection[/w/.test(resize)?'x2':'x1']);y1=viewY(selection[/n/.test(resize)?'y2':'y1']);$(document).mousemove(selectingMouseMove).one('mouseup',docMouseUp);$box.unbind('mousemove',areaMouseMove)}else if(options.movable){startX=left+selection.x1-evX(event);startY=top+selection.y1-evY(event);$box.unbind('mousemove',areaMouseMove);$(document).mousemove(movingMouseMove).one('mouseup',function(){options.onSelectEnd(img,getSelection());$(document).unbind('mousemove',movingMouseMove);$box.mousemove(areaMouseMove)})}else $img.mousedown(event);return false}function fixAspectRatio(xFirst){if(aspectRatio)if(xFirst){x2=max(left,min(left+imgWidth,x1+abs(y2-y1)*aspectRatio*(x2>x1||-1)));y2=round(max(top,min(top+imgHeight,y1+abs(x2-x1)/aspectRatio*(y2>y1||-1))));x2=round(x2)}else{y2=max(top,min(top+imgHeight,y1+abs(x2-x1)/aspectRatio*(y2>y1||-1)));x2=round(max(left,min(left+imgWidth,x1+abs(y2-y1)*aspectRatio*(x2>x1||-1))));y2=round(y2)}}function doResize(){x1=min(x1,left+imgWidth);y1=min(y1,top+imgHeight);if(abs(x2-x1)<minWidth){x2=x1-minWidth*(x2<x1||-1);if(x2<left)x1=left+minWidth;else if(x2>left+imgWidth)x1=left+imgWidth-minWidth}if(abs(y2-y1)<minHeight){y2=y1-minHeight*(y2<y1||-1);if(y2<top)y1=top+minHeight;else if(y2>top+imgHeight)y1=top+imgHeight-minHeight}x2=max(left,min(x2,left+imgWidth));y2=max(top,min(y2,top+imgHeight));fixAspectRatio(abs(x2-x1)<abs(y2-y1)*aspectRatio);if(abs(x2-x1)>maxWidth){x2=x1-maxWidth*(x2<x1||-1);fixAspectRatio()}if(abs(y2-y1)>maxHeight){y2=y1-maxHeight*(y2<y1||-1);fixAspectRatio(true)}selection={x1:selX(min(x1,x2)),x2:selX(max(x1,x2)),y1:selY(min(y1,y2)),y2:selY(max(y1,y2)),width:abs(x2-x1),height:abs(y2-y1)};update();options.onSelectChange(img,getSelection())}function selectingMouseMove(event){x2=/w|e|^$/.test(resize)||aspectRatio?evX(event):viewX(selection.x2);y2=/n|s|^$/.test(resize)||aspectRatio?evY(event):viewY(selection.y2);doResize();return false}function doMove(newX1,newY1){x2=(x1=newX1)+selection.width;y2=(y1=newY1)+selection.height;$.extend(selection,{x1:selX(x1),y1:selY(y1),x2:selX(x2),y2:selY(y2)});update();options.onSelectChange(img,getSelection())}function movingMouseMove(event){x1=max(left,min(startX+evX(event),left+imgWidth-selection.width));y1=max(top,min(startY+evY(event),top+imgHeight-selection.height));doMove(x1,y1);event.preventDefault();return false}function startSelection(){$(document).unbind('mousemove',startSelection);adjust();x2=x1;y2=y1;doResize();resize='';if(!$outer.is(':visible'))$box.add($outer).hide().fadeIn(options.fadeSpeed||0);shown=true;$(document).unbind('mouseup',cancelSelection).mousemove(selectingMouseMove).one('mouseup',docMouseUp);$box.unbind('mousemove',areaMouseMove);options.onSelectStart(img,getSelection())}function cancelSelection(){$(document).unbind('mousemove',startSelection).unbind('mouseup',cancelSelection);hide($box.add($outer));setSelection(selX(x1),selY(y1),selX(x1),selY(y1));if(!(this instanceof $.imgAreaSelect)){options.onSelectChange(img,getSelection());options.onSelectEnd(img,getSelection())}}function imgMouseDown(event){if(event.which!=1||$outer.is(':animated'))return false;adjust();startX=x1=evX(event);startY=y1=evY(event);$(document).mousemove(startSelection).mouseup(cancelSelection);return false}function windowResize(){doUpdate(false)}function imgLoad(){imgLoaded=true;setOptions(options=$.extend({classPrefix:'imgareaselect',movable:true,parent:'body',resizable:true,resizeMargin:10,onInit:function(){},onSelectStart:function(){},onSelectChange:function(){},onSelectEnd:function(){}},options));$box.add($outer).css({visibility:''});if(options.show){shown=true;adjust();update();$box.add($outer).hide().fadeIn(options.fadeSpeed||0)}setTimeout(function(){options.onInit(img,getSelection())},0)}var docKeyPress=function(event){var k=options.keys,d,t,key=event.keyCode;d=!isNaN(k.alt)&&(event.altKey||event.originalEvent.altKey)?k.alt:!isNaN(k.ctrl)&&event.ctrlKey?k.ctrl:!isNaN(k.shift)&&event.shiftKey?k.shift:!isNaN(k.arrows)?k.arrows:10;if(k.arrows=='resize'||(k.shift=='resize'&&event.shiftKey)||(k.ctrl=='resize'&&event.ctrlKey)||(k.alt=='resize'&&(event.altKey||event.originalEvent.altKey))){switch(key){case 37:d=-d;case 39:t=max(x1,x2);x1=min(x1,x2);x2=max(t+d,x1);fixAspectRatio();break;case 38:d=-d;case 40:t=max(y1,y2);y1=min(y1,y2);y2=max(t+d,y1);fixAspectRatio(true);break;default:return}doResize()}else{x1=min(x1,x2);y1=min(y1,y2);switch(key){case 37:doMove(max(x1-d,left),y1);break;case 38:doMove(x1,max(y1-d,top));break;case 39:doMove(x1+min(d,imgWidth-selX(x2)),y1);break;case 40:doMove(x1,y1+min(d,imgHeight-selY(y2)));break;default:return}}return false};function styleOptions($elem,props){for(var option in props)if(options[option]!==undefined)$elem.css(props[option],options[option])}function setOptions(newOptions){if(newOptions.parent)($parent=$(newOptions.parent)).append($box.add($outer));$.extend(options,newOptions);adjust();if(newOptions.handles!=null){$handles.remove();$handles=$([]);i=newOptions.handles?newOptions.handles=='corners'?4:8:0;while(i--)$handles=$handles.add(div());$handles.addClass(options.classPrefix+'-handle').css({position:'absolute',fontSize:0,zIndex:zIndex+1||1});if(!parseInt($handles.css('width'))>=0)$handles.width(5).height(5);if(o=options.borderWidth)$handles.css({borderWidth:o,borderStyle:'solid'});styleOptions($handles,{borderColor1:'border-color',borderColor2:'background-color',borderOpacity:'opacity'})}scaleX=options.imageWidth/imgWidth||1;scaleY=options.imageHeight/imgHeight||1;if(newOptions.x1!=null){setSelection(newOptions.x1,newOptions.y1,newOptions.x2,newOptions.y2);newOptions.show=!newOptions.hide}if(newOptions.keys)options.keys=$.extend({shift:1,ctrl:'resize'},newOptions.keys);$outer.addClass(options.classPrefix+'-outer');$area.addClass(options.classPrefix+'-selection');for(i=0;i++<4;)$($border[i-1]).addClass(options.classPrefix+'-border'+i);styleOptions($area,{selectionColor:'background-color',selectionOpacity:'opacity'});styleOptions($border,{borderOpacity:'opacity',borderWidth:'border-width'});styleOptions($outer,{outerColor:'background-color',outerOpacity:'opacity'});if(o=options.borderColor1)$($border[0]).css({borderStyle:'solid',borderColor:o});if(o=options.borderColor2)$($border[1]).css({borderStyle:'dashed',borderColor:o});$box.append($area.add($border).add($areaOpera)).append($handles);if(msie){if(o=($outer.css('filter')||'').match(/opacity=(\d+)/))$outer.css('opacity',o[1]/100);if(o=($border.css('filter')||'').match(/opacity=(\d+)/))$border.css('opacity',o[1]/100)}if(newOptions.hide)hide($box.add($outer));else if(newOptions.show&&imgLoaded){shown=true;$box.add($outer).fadeIn(options.fadeSpeed||0);doUpdate()}aspectRatio=(d=(options.aspectRatio||'').split(/:/))[0]/d[1];$img.add($outer).unbind('mousedown',imgMouseDown);if(options.disable||options.enable===false){$box.unbind('mousemove',areaMouseMove).unbind('mousedown',areaMouseDown);$(window).unbind('resize',windowResize)}else{if(options.enable||options.disable===false){if(options.resizable||options.movable)$box.mousemove(areaMouseMove).mousedown(areaMouseDown);$(window).resize(windowResize)}if(!options.persistent)$img.add($outer).mousedown(imgMouseDown)}options.enable=options.disable=undefined}this.remove=function(){setOptions({disable:true});$box.add($outer).remove()};this.getOptions=function(){return options};this.setOptions=setOptions;this.getSelection=getSelection;this.setSelection=setSelection;this.cancelSelection=cancelSelection;this.update=doUpdate;var msie=(/msie ([\w.]+)/i.exec(ua)||[])[1],opera=/opera/i.test(ua),safari=/webkit/i.test(ua)&&!/chrome/i.test(ua);$p=$img;while($p.length){zIndex=max(zIndex,!isNaN($p.css('z-index'))?$p.css('z-index'):zIndex);if($p.css('position')=='fixed')position='fixed';$p=$p.parent(':not(body)')}zIndex=options.zIndex||zIndex;if(msie)$img.attr('unselectable','on');$.imgAreaSelect.keyPress=msie||safari?'keydown':'keypress';if(opera)$areaOpera=div().css({width:'100%',height:'100%',position:'absolute',zIndex:zIndex+2||2});$box.add($outer).css({visibility:'hidden',position:position,overflow:'hidden',zIndex:zIndex||'0'});$box.css({zIndex:zIndex+2||2});$area.add($border).css({position:'absolute',fontSize:0});img.complete||img.readyState=='complete'||!$img.is('img')?imgLoad():$img.one('load',imgLoad);if(!imgLoaded&&msie&&msie>=7)img.src=img.src};$.fn.imgAreaSelect=function(options){options=options||{};this.each(function(){if($(this).data('imgAreaSelect')){if(options.remove){$(this).data('imgAreaSelect').remove();$(this).removeData('imgAreaSelect')}else $(this).data('imgAreaSelect').setOptions(options)}else if(!options.remove){if(options.enable===undefined&&options.disable===undefined)options.enable=true;$(this).data('imgAreaSelect',new $.imgAreaSelect(this,options))}});if(options.instance)return $(this).data('imgAreaSelect');return this}})(jQuery);

/* Panzoom 4.5.1 Copyright Timmy Willison */
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):(t="undefined"!=typeof globalThis?globalThis:t||self).Panzoom=e()}(this,function(){"use strict";var Y=function(){return(Y=Object.assign||function(t){for(var e,n=1,o=arguments.length;n<o;n++)for(var r in e=arguments[n])Object.prototype.hasOwnProperty.call(e,r)&&(t[r]=e[r]);return t}).apply(this,arguments)};function C(t,e){for(var n=t.length;n--;)if(t[n].pointerId===e.pointerId)return n;return-1}function T(t,e){if(e.touches)for(var n=0,o=0,r=e.touches;o<r.length;o++){var a=r[o];a.pointerId=n++,T(t,a)}else-1<(n=C(t,e))&&t.splice(n,1),t.push(e)}function N(t){for(var e,n=(t=t.slice(0)).pop();e=t.pop();)n={clientX:(e.clientX-n.clientX)/2+n.clientX,clientY:(e.clientY-n.clientY)/2+n.clientY};return n}function L(t){var e;return t.length<2?0:(e=t[0],t=t[1],Math.sqrt(Math.pow(Math.abs(t.clientX-e.clientX),2)+Math.pow(Math.abs(t.clientY-e.clientY),2)))}"undefined"!=typeof window&&(window.NodeList&&!NodeList.prototype.forEach&&(NodeList.prototype.forEach=Array.prototype.forEach),"function"!=typeof window.CustomEvent&&(window.CustomEvent=function(t,e){e=e||{bubbles:!1,cancelable:!1,detail:null};var n=document.createEvent("CustomEvent");return n.initCustomEvent(t,e.bubbles,e.cancelable,e.detail),n}));var V={down:"mousedown",move:"mousemove",up:"mouseup mouseleave"};function D(t,e,n,o){V[t].split(" ").forEach(function(t){e.addEventListener(t,n,o)})}function G(t,e,n){V[t].split(" ").forEach(function(t){e.removeEventListener(t,n)})}"undefined"!=typeof window&&("function"==typeof window.PointerEvent?V={down:"pointerdown",move:"pointermove",up:"pointerup pointerleave pointercancel"}:"function"==typeof window.TouchEvent&&(V={down:"touchstart",move:"touchmove",up:"touchend touchcancel"}));var a,i="undefined"!=typeof document&&!!document.documentMode;var c=["webkit","moz","ms"],l={};function I(t){if(l[t])return l[t];var e=a=a||document.createElement("div").style;if(t in e)return l[t]=t;for(var n=t[0].toUpperCase()+t.slice(1),o=c.length;o--;){var r="".concat(c[o]).concat(n);if(r in e)return l[t]=r}}function o(t,e){return parseFloat(e[I(t)])||0}function s(t,e,n){void 0===n&&(n=window.getComputedStyle(t));t="border"===e?"Width":"";return{left:o("".concat(e,"Left").concat(t),n),right:o("".concat(e,"Right").concat(t),n),top:o("".concat(e,"Top").concat(t),n),bottom:o("".concat(e,"Bottom").concat(t),n)}}function W(t,e,n){t.style[I(e)]=n}function Z(t){var e=t.parentNode,n=window.getComputedStyle(t),o=window.getComputedStyle(e),r=t.getBoundingClientRect(),a=e.getBoundingClientRect();return{elem:{style:n,width:r.width,height:r.height,top:r.top,bottom:r.bottom,left:r.left,right:r.right,margin:s(t,"margin",n),border:s(t,"border",n)},parent:{style:o,width:a.width,height:a.height,top:a.top,bottom:a.bottom,left:a.left,right:a.right,padding:s(e,"padding",o),border:s(e,"border",o)}}}var q=/^http:[\w\.\/]+svg$/;var B={animate:!1,canvas:!1,cursor:"move",disablePan:!1,disableZoom:!1,disableXAxis:!1,disableYAxis:!1,duration:200,easing:"ease-in-out",exclude:[],excludeClass:"panzoom-exclude",handleStartEvent:function(t){t.preventDefault(),t.stopPropagation()},maxScale:4,minScale:.125,overflow:"hidden",panOnlyWhenZoomed:!1,pinchAndPan:!1,relative:!1,setTransform:function(t,e,n){var o=e.x,r=e.y,a=e.scale,e=e.isSVG;W(t,"transform","scale(".concat(a,") translate(").concat(o,"px, ").concat(r,"px)")),e&&i&&(a=window.getComputedStyle(t).getPropertyValue("transform"),t.setAttribute("transform",a))},startX:0,startY:0,startScale:1,step:.3,touchAction:"none"};function t(u,f){if(!u)throw new Error("Panzoom requires an element as an argument");if(1!==u.nodeType)throw new Error("Panzoom requires an element with a nodeType of 1");if(e=(t=u).ownerDocument,t=t.parentNode,!(e&&t&&9===e.nodeType&&1===t.nodeType&&e.documentElement.contains(t)))throw new Error("Panzoom should be called on elements that have been attached to the DOM");f=Y(Y({},B),f),e=u;var t,e,l=q.test(e.namespaceURI)&&"svg"!==e.nodeName.toLowerCase(),n=u.parentNode;n.style.overflow=f.overflow,n.style.userSelect="none",n.style.touchAction=f.touchAction,(f.canvas?n:u).style.cursor=f.cursor,u.style.userSelect="none",u.style.touchAction=f.touchAction,W(u,"transformOrigin","string"==typeof f.origin?f.origin:l?"0 0":"50% 50%");var r,a,i,c,s,d,m=0,h=0,v=1,p=!1;function g(t,e,n){n.silent||(n=new CustomEvent(t,{detail:e}),u.dispatchEvent(n))}function y(o,r,t){var a={x:m,y:h,scale:v,isSVG:l,originalEvent:t};return requestAnimationFrame(function(){var t,e,n;"boolean"==typeof r.animate&&(r.animate?(t=u,e=r,n=I("transform"),W(t,"transition","".concat(n," ").concat(e.duration,"ms ").concat(e.easing))):W(u,"transition","none")),r.setTransform(u,a,r),g(o,a,r),g("panzoomchange",a,r)}),a}function w(t,e,n,o){var r,a,i,c,l,s,d,o=Y(Y({},f),o),p={x:m,y:h,opts:o};return!o.force&&(o.disablePan||o.panOnlyWhenZoomed&&v===o.startScale)||(t=parseFloat(t),e=parseFloat(e),o.disableXAxis||(p.x=(o.relative?m:0)+t),o.disableYAxis||(p.y=(o.relative?h:0)+e),o.contain&&(e=((r=(e=(t=Z(u)).elem.width/v)*n)-e)/2,i=((a=(i=t.elem.height/v)*n)-i)/2,"inside"===o.contain?(c=(-t.elem.margin.left-t.parent.padding.left+e)/n,l=(t.parent.width-r-t.parent.padding.left-t.elem.margin.left-t.parent.border.left-t.parent.border.right+e)/n,p.x=Math.max(Math.min(p.x,l),c),s=(-t.elem.margin.top-t.parent.padding.top+i)/n,d=(t.parent.height-a-t.parent.padding.top-t.elem.margin.top-t.parent.border.top-t.parent.border.bottom+i)/n,p.y=Math.max(Math.min(p.y,d),s)):"outside"===o.contain&&(c=(-(r-t.parent.width)-t.parent.padding.left-t.parent.border.left-t.parent.border.right+e)/n,l=(e-t.parent.padding.left)/n,p.x=Math.max(Math.min(p.x,l),c),s=(-(a-t.parent.height)-t.parent.padding.top-t.parent.border.top-t.parent.border.bottom+i)/n,d=(i-t.parent.padding.top)/n,p.y=Math.max(Math.min(p.y,d),s))),o.roundPixels&&(p.x=Math.round(p.x),p.y=Math.round(p.y))),p}function b(t,e){var n,o,r,a,e=Y(Y({},f),e),i={scale:v,opts:e};return!e.force&&e.disableZoom||(n=f.minScale,o=f.maxScale,e.contain&&(a=(e=Z(u)).elem.width/v,r=e.elem.height/v,1<a&&1<r&&(a=(e.parent.width-e.parent.border.left-e.parent.border.right)/a,e=(e.parent.height-e.parent.border.top-e.parent.border.bottom)/r,"inside"===f.contain?o=Math.min(o,a,e):"outside"===f.contain&&(n=Math.max(n,a,e)))),i.scale=Math.min(Math.max(t,n),o)),i}function x(t,e,n,o){t=w(t,e,v,n);return m!==t.x||h!==t.y?(m=t.x,h=t.y,y("panzoompan",t.opts,o)):{x:m,y:h,scale:v,isSVG:l,originalEvent:o}}function E(t,e,n){var o,r,e=b(t,e),a=e.opts;if(a.force||!a.disableZoom)return t=e.scale,e=m,o=h,a.focal&&(e=((r=a.focal).x/t-r.x/v+m*t)/t,o=(r.y/t-r.y/v+h*t)/t),r=w(e,o,t,{relative:!1,force:!0}),m=r.x,h=r.y,v=t,y("panzoomzoom",a,n)}function o(t,e){e=Y(Y(Y({},f),{animate:!0}),e);return E(v*Math.exp((t?1:-1)*e.step),e)}function S(t,e,n,o){var r=Z(u),a=r.parent.width-r.parent.padding.left-r.parent.padding.right-r.parent.border.left-r.parent.border.right,i=r.parent.height-r.parent.padding.top-r.parent.padding.bottom-r.parent.border.top-r.parent.border.bottom,c=e.clientX-r.parent.left-r.parent.padding.left-r.parent.border.left-r.elem.margin.left,e=e.clientY-r.parent.top-r.parent.padding.top-r.parent.border.top-r.elem.margin.top,r=(l||(c-=r.elem.width/v/2,e-=r.elem.height/v/2),{x:c/a*(a*t),y:e/i*(i*t)});return E(t,Y(Y({},n),{animate:!1,focal:r}),o)}E(f.startScale,{animate:!1,force:!0}),setTimeout(function(){x(f.startX,f.startY,{animate:!1,force:!0})});var M=[];function A(t){!function(t,e){for(var n,o,r=t;null!=r;r=r.parentNode)if(n=r,o=e.excludeClass,1===n.nodeType&&-1<" ".concat((n.getAttribute("class")||"").trim()," ").indexOf(" ".concat(o," "))||-1<e.exclude.indexOf(r))return 1}(t.target,f)&&(T(M,t),p=!0,f.handleStartEvent(t),g("panzoomstart",{x:r=m,y:a=h,scale:v,isSVG:l,originalEvent:t},f),t=N(M),i=t.clientX,c=t.clientY,s=v,d=L(M))}function P(t){var e,n,o;p&&void 0!==r&&void 0!==a&&void 0!==i&&void 0!==c&&(T(M,t),e=N(M),n=1<M.length,o=v,n&&(0===d&&(d=L(M)),S(o=b((L(M)-d)*f.step/80+s).scale,e,{animate:!1},t)),n&&!f.pinchAndPan||x(r+(e.clientX-i)/o,a+(e.clientY-c)/o,{animate:!1},t))}function O(t){1===M.length&&g("panzoomend",{x:m,y:h,scale:v,isSVG:l,originalEvent:t},f);var e=M;if(t.touches)for(;e.length;)e.pop();else{t=C(e,t);-1<t&&e.splice(t,1)}p&&(p=!1,r=a=i=c=void 0)}var z=!1;function X(){z||(z=!0,D("down",f.canvas?n:u,A),D("move",document,P,{passive:!0}),D("up",document,O,{passive:!0}))}return f.noBind||X(),{bind:X,destroy:function(){z=!1,G("down",f.canvas?n:u,A),G("move",document,P),G("up",document,O)},eventNames:V,getPan:function(){return{x:m,y:h}},getScale:function(){return v},getOptions:function(){var t,e=f,n={};for(t in e)e.hasOwnProperty(t)&&(n[t]=e[t]);return n},handleDown:A,handleMove:P,handleUp:O,pan:x,reset:function(t){var t=Y(Y(Y({},f),{animate:!0,force:!0}),t),e=(v=b(t.startScale,t).scale,w(t.startX,t.startY,v,t));return m=e.x,h=e.y,y("panzoomreset",t)},resetStyle:function(){n.style.overflow="",n.style.userSelect="",n.style.touchAction="",n.style.cursor="",u.style.cursor="",u.style.userSelect="",u.style.touchAction="",W(u,"transformOrigin","")},setOptions:function(t){for(var e in t=void 0===t?{}:t)t.hasOwnProperty(e)&&(f[e]=t[e]);(t.hasOwnProperty("cursor")||t.hasOwnProperty("canvas"))&&(n.style.cursor=u.style.cursor="",(f.canvas?n:u).style.cursor=f.cursor),t.hasOwnProperty("overflow")&&(n.style.overflow=t.overflow),t.hasOwnProperty("touchAction")&&(n.style.touchAction=t.touchAction,u.style.touchAction=t.touchAction)},setStyle:function(t,e){return W(u,t,e)},zoom:E,zoomIn:function(t){return o(!0,t)},zoomOut:function(t){return o(!1,t)},zoomToPoint:S,zoomWithWheel:function(t,e){t.preventDefault();var e=Y(Y(Y({},f),e),{animate:!1}),n=0===t.deltaY&&t.deltaX?t.deltaX:t.deltaY;return S(b(v*Math.exp((n<0?1:-1)*e.step/3),e).scale,t,e,t)}}}return t.defaultOptions=B,t});

/* Bootstrap Notify V3.1.5*/
!function(t){"function"==typeof define&&define.amd?define(["jquery"],t):t("object"==typeof exports?require("jquery"):jQuery)}(function(t){function s(s){var e=!1;return t('[data-notify="container"]').each(function(i,n){var a=t(n),o=a.find('[data-notify="title"]').text().trim(),r=a.find('[data-notify="message"]').html().trim(),l=o===t("<div>"+s.settings.content.title+"</div>").html().trim(),d=r===t("<div>"+s.settings.content.message+"</div>").html().trim(),g=a.hasClass("alert-"+s.settings.type);return l&&d&&g&&(e=!0),!e}),e}function e(e,n,a){var o={content:{message:"object"==typeof n?n.message:n,title:n.title?n.title:"",icon:n.icon?n.icon:"",url:n.url?n.url:"#",target:n.target?n.target:"-"}};a=t.extend(!0,{},o,a),this.settings=t.extend(!0,{},i,a),this._defaults=i,"-"===this.settings.content.target&&(this.settings.content.target=this.settings.url_target),this.animations={start:"webkitAnimationStart oanimationstart MSAnimationStart animationstart",end:"webkitAnimationEnd oanimationend MSAnimationEnd animationend"},"number"==typeof this.settings.offset&&(this.settings.offset={x:this.settings.offset,y:this.settings.offset}),(this.settings.allow_duplicates||!this.settings.allow_duplicates&&!s(this))&&this.init()}var i={element:"body",position:null,type:"info",allow_dismiss:!0,allow_duplicates:!0,newest_on_top:!1,showProgressbar:!1,placement:{from:"top",align:"right"},offset:20,spacing:10,z_index:1031,delay:5e3,timer:1e3,url_target:"_blank",mouse_over:null,animate:{enter:"animated fadeInDown",exit:"animated fadeOutUp"},onShow:null,onShown:null,onClose:null,onClosed:null,icon_type:"class",template:'<div data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>'};String.format=function(){for(var t=arguments[0],s=1;s<arguments.length;s++)t=t.replace(RegExp("\\{"+(s-1)+"\\}","gm"),arguments[s]);return t},t.extend(e.prototype,{init:function(){var t=this;this.buildNotify(),this.settings.content.icon&&this.setIcon(),"#"!=this.settings.content.url&&this.styleURL(),this.styleDismiss(),this.placement(),this.bind(),this.notify={$ele:this.$ele,update:function(s,e){var i={};"string"==typeof s?i[s]=e:i=s;for(var n in i)switch(n){case"type":this.$ele.removeClass("alert-"+t.settings.type),this.$ele.find('[data-notify="progressbar"] > .progress-bar').removeClass("progress-bar-"+t.settings.type),t.settings.type=i[n],this.$ele.addClass("alert-"+i[n]).find('[data-notify="progressbar"] > .progress-bar').addClass("progress-bar-"+i[n]);break;case"icon":var a=this.$ele.find('[data-notify="icon"]');"class"===t.settings.icon_type.toLowerCase()?a.removeClass(t.settings.content.icon).addClass(i[n]):(a.is("img")||a.find("img"),a.attr("src",i[n]));break;case"progress":var o=t.settings.delay-t.settings.delay*(i[n]/100);this.$ele.data("notify-delay",o),this.$ele.find('[data-notify="progressbar"] > div').attr("aria-valuenow",i[n]).css("width",i[n]+"%");break;case"url":this.$ele.find('[data-notify="url"]').attr("href",i[n]);break;case"target":this.$ele.find('[data-notify="url"]').attr("target",i[n]);break;default:this.$ele.find('[data-notify="'+n+'"]').html(i[n])}var r=this.$ele.outerHeight()+parseInt(t.settings.spacing)+parseInt(t.settings.offset.y);t.reposition(r)},close:function(){t.close()}}},buildNotify:function(){var s=this.settings.content;this.$ele=t(String.format(this.settings.template,this.settings.type,s.title,s.message,s.url,s.target)),this.$ele.attr("data-notify-position",this.settings.placement.from+"-"+this.settings.placement.align),this.settings.allow_dismiss||this.$ele.find('[data-notify="dismiss"]').css("display","none"),(this.settings.delay<=0&&!this.settings.showProgressbar||!this.settings.showProgressbar)&&this.$ele.find('[data-notify="progressbar"]').remove()},setIcon:function(){"class"===this.settings.icon_type.toLowerCase()?this.$ele.find('[data-notify="icon"]').addClass(this.settings.content.icon):this.$ele.find('[data-notify="icon"]').is("img")?this.$ele.find('[data-notify="icon"]').attr("src",this.settings.content.icon):this.$ele.find('[data-notify="icon"]').append('<img src="'+this.settings.content.icon+'" alt="Notify Icon" />')},styleDismiss:function(){this.$ele.find('[data-notify="dismiss"]').css({position:"absolute",right:"10px",top:"5px",zIndex:this.settings.z_index+2})},styleURL:function(){this.$ele.find('[data-notify="url"]').css({backgroundImage:"url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7)",height:"100%",left:0,position:"absolute",top:0,width:"100%",zIndex:this.settings.z_index+1})},placement:function(){var s=this,e=this.settings.offset.y,i={display:"inline-block",margin:"0px auto",position:this.settings.position?this.settings.position:"body"===this.settings.element?"fixed":"absolute",transition:"all .5s ease-in-out",zIndex:this.settings.z_index},n=!1,a=this.settings;switch(t('[data-notify-position="'+this.settings.placement.from+"-"+this.settings.placement.align+'"]:not([data-closing="true"])').each(function(){e=Math.max(e,parseInt(t(this).css(a.placement.from))+parseInt(t(this).outerHeight())+parseInt(a.spacing))}),this.settings.newest_on_top===!0&&(e=this.settings.offset.y),i[this.settings.placement.from]=e+"px",this.settings.placement.align){case"left":case"right":i[this.settings.placement.align]=this.settings.offset.x+"px";break;case"center":i.left=0,i.right=0}this.$ele.css(i).addClass(this.settings.animate.enter),t.each(Array("webkit-","moz-","o-","ms-",""),function(t,e){s.$ele[0].style[e+"AnimationIterationCount"]=1}),t(this.settings.element).append(this.$ele),this.settings.newest_on_top===!0&&(e=parseInt(e)+parseInt(this.settings.spacing)+this.$ele.outerHeight(),this.reposition(e)),t.isFunction(s.settings.onShow)&&s.settings.onShow.call(this.$ele),this.$ele.one(this.animations.start,function(){n=!0}).one(this.animations.end,function(){s.$ele.removeClass(s.settings.animate.enter),t.isFunction(s.settings.onShown)&&s.settings.onShown.call(this)}),setTimeout(function(){n||t.isFunction(s.settings.onShown)&&s.settings.onShown.call(this)},600)},bind:function(){var s=this;if(this.$ele.find('[data-notify="dismiss"]').on("click",function(){s.close()}),this.$ele.mouseover(function(){t(this).data("data-hover","true")}).mouseout(function(){t(this).data("data-hover","false")}),this.$ele.data("data-hover","false"),this.settings.delay>0){s.$ele.data("notify-delay",s.settings.delay);var e=setInterval(function(){var t=parseInt(s.$ele.data("notify-delay"))-s.settings.timer;if("false"===s.$ele.data("data-hover")&&"pause"===s.settings.mouse_over||"pause"!=s.settings.mouse_over){var i=(s.settings.delay-t)/s.settings.delay*100;s.$ele.data("notify-delay",t),s.$ele.find('[data-notify="progressbar"] > div').attr("aria-valuenow",i).css("width",i+"%")}t<=-s.settings.timer&&(clearInterval(e),s.close())},s.settings.timer)}},close:function(){var s=this,e=parseInt(this.$ele.css(this.settings.placement.from)),i=!1;this.$ele.attr("data-closing","true").addClass(this.settings.animate.exit),s.reposition(e),t.isFunction(s.settings.onClose)&&s.settings.onClose.call(this.$ele),this.$ele.one(this.animations.start,function(){i=!0}).one(this.animations.end,function(){t(this).remove(),t.isFunction(s.settings.onClosed)&&s.settings.onClosed.call(this)}),setTimeout(function(){i||(s.$ele.remove(),s.settings.onClosed&&s.settings.onClosed(s.$ele))},600)},reposition:function(s){var e=this,i='[data-notify-position="'+this.settings.placement.from+"-"+this.settings.placement.align+'"]:not([data-closing="true"])',n=this.$ele.nextAll(i);this.settings.newest_on_top===!0&&(n=this.$ele.prevAll(i)),n.each(function(){t(this).css(e.settings.placement.from,s),s=parseInt(s)+parseInt(e.settings.spacing)+t(this).outerHeight()})}}),t.notify=function(t,s){var i=new e(this,t,s);return i.notify},t.notifyDefaults=function(s){return i=t.extend(!0,{},i,s)},t.notifyClose=function(s){"warning"===s&&(s="danger"),"undefined"==typeof s||"all"===s?t("[data-notify]").find('[data-notify="dismiss"]').trigger("click"):"success"===s||"info"===s||"warning"===s||"danger"===s?t(".alert-"+s+"[data-notify]").find('[data-notify="dismiss"]').trigger("click"):s?t(s+"[data-notify]").find('[data-notify="dismiss"]').trigger("click"):t('[data-notify-position="'+s+'"]').find('[data-notify="dismiss"]').trigger("click")},t.notifyCloseExcept=function(s){"warning"===s&&(s="danger"),"success"===s||"info"===s||"warning"===s||"danger"===s?t("[data-notify]").not(".alert-"+s).find('[data-notify="dismiss"]').trigger("click"):t("[data-notify]").not(s).find('[data-notify="dismiss"]').trigger("click")}});

//
if("undefined"===typeof JCck){JCck={}};
if("undefined"===typeof JCck.More){JCck.More={}};
JCck.More.CropX={};

(function ($){
	//	Variables
	JCck.More.CropX.ias = null;
	JCck.More.CropX.color = null;
	JCck.More.CropX.modal = null;
	JCck.More.CropX.i8n = [];
	JCck.More.CropX.link = '';

	// Panzoom
	JCck.More.CropX.panzoom = null;
	JCck.More.CropX.btnZoomOut = null;
	JCck.More.CropX.btnZoomIn = null;
	JCck.More.CropX.btnZoomRange = null;
	JCck.More.CropX.previousValue = 1;

	//	Main
	JCck.More.CropX.getArea = function(that) {	
		if( $(that).hasClass("active") ){
			return false;
		}

		$('a.getcrop').addClass("active disabled");
		$('a.rotate').addClass("disabled");
		
		$.ajax({
			cache: false,
			url: JCck.More.CropX.link+'&t=getArea',
			data: ({ data: JCck.More.CropX.getData(that) }),
			type: "GET",
			dataType: "json",
			success: function(response){
				JCck.More.CropX.i8n = response.i8n;
				JCck.More.CropX.modal.loadHtml(response.area);
			},
			error:function(){}
		});
	};

	JCck.More.CropX.getThumb = function(that) {	
		var data = JCck.More.CropX.getData($(that).closest('#toolbar-crop'));
			data['thumb'] = $('#toolbar-crop .dropdown-toggle').attr('data-value');
			$(that).parent().attr('data-thumb',data['thumb']);
			data = JCck.More.CropX.getSize(data);

		$.ajax({
			cache: false,
			url: JCck.More.CropX.link+'&t=getThumb',
			data: ({ data: data	}),
			type: "GET",
			dataType: "json",
			beforeSend:function(){
				JCck.More.CropX.addProgress();						
			},
			success: function(response){
				$('#toolbar-crop').attr({
					'data-thumb':response.thumb,
					'data-pl':response.pl,
					'data-wpl':response.wpl,
					'data-hpl':response.hpl,
					'data-cropped':response.cropped
				});

				//	Add Placeholder
				$('div#resize-parent').html(response.placeholder);

				//	Color Picker
				JCck.More.CropX.displayColor(
									response.ext, 
									response.color,
									response.picker,
									response.palette
								);

				//	PanZoom
				var btnzoom = $('.zoom-buttons').closest('.toolbar-btn');

				if (response.zoom) {
					$('.set-expand,.set-contract').show();
					btnzoom.show();

					const element = document.getElementById('panzoom');

					JCck.More.CropX.btnZoomOut = document.getElementById('zoom-out');
					JCck.More.CropX.btnZoomIn = document.getElementById('zoom-in');
					JCck.More.CropX.btnZoomRange = document.getElementById('zoom-range') ?? { value: 1 };
					JCck.More.CropX.panzoom = Panzoom(element, {
													maxScale: 1,
													minScale: 0.2,
													step: 0.005,
													contain: 'inside',
													disablePan: true,
													zoomWithWheel: false
											    });

					JCck.More.CropX.panzoom.zoom(response.zoom);
					JCck.More.CropX.btnZoomRange.value = response.zoom;

					JCck.More.CropX.btnZoomRange.addEventListener('input', (event) => {
						JCck.More.CropX.panzoom.zoom(event.target.valueAsNumber);
					});

					JCck.More.CropX.btnZoomIn.addEventListener('click', () => {
						JCck.More.CropX.panzoom.zoomIn();
						JCck.More.CropX.btnZoomRange.value = JCck.More.CropX.panzoom.getScale();
					});

					JCck.More.CropX.btnZoomOut.addEventListener('click', () => {
						JCck.More.CropX.panzoom.zoomOut();
						JCck.More.CropX.btnZoomRange.value = JCck.More.CropX.panzoom.getScale();
					});
				} else {
					if (response.pl == 2) {
						$('.set-expand,.set-contract').hide();
					}
					btnzoom.hide();
				}

				//	ImageAreaSelect
				JCck.More.CropX.ias = $('#target').imgAreaSelect({
				    instance: true,
				    parent: $("div#resize-parent"),
				    handles: (response.pl == 2) ? false : true,
		    		persistent: (response.pl == 2) ? true : false,
					resizable: (response.pl == 2) ? false : true,
					hide: true,
					onInit: function(i,s) {
						var maxW = parseInt(response.wtrue);
						var maxH = parseInt(response.htrue);

						JCck.More.CropX.ias.setOptions({
							imageWidth: maxW,
							imageHeight: maxH,
							minWidth: response.wmin,
							minHeight: response.hmin,
							aspectRatio: response.aspectRatio
						});

						response.x = Math.round(response.x);
						response.y = Math.round(response.y);
						response.w = Math.round(response.w);
						response.h = Math.round(response.h);

						if ((response.x + response.w) >= maxW - 1) {
							response.x = maxW - response.w - 2; 
						}
						if ((response.y + response.h) >= maxH - 1) {
							response.y = maxH - response.h - 2;
						}

						response.x = Math.max(0, response.x);
						response.y = Math.max(0, response.y);

						$('#resize-parent').css({ width: response.wpl, height: response.htpl }).children('div').css("position", "absolute");
						$('#target img#panzoom').css({
							'top': '0',
							'left': '0',
							'position': 'absolute',
							'width': response.wpl + 'px',
							'height': response.hpl + 'px',
							'display': 'block',
							'max-width': 'none'
						});
						$('#target').css({
							'width': response.wpl + 'px',
							'height': response.hpl + 'px',
							'overflow': 'hidden',
							'display': 'block',
							'position': 'relative'
						});

						JCck.More.CropX.ias.setSelection(response.x, response.y, response.x + response.w, response.y + response.h);
						JCck.More.CropX.ias.setOptions({ fadeSpeed: 800, show: true });
						JCck.More.CropX.ias.update();

						JCck.More.CropX.updateColor(response.color);
						JCck.More.CropX.removeProgress();
						JCck.More.CropX.notify(JCck.More.CropX.i8n.loaded, 'success');
						
						if (!response.cropped) {
							if (response.pl == 2) {
								$(".set-center").trigger('click');
							} else {
								var sl = (response.method) ? '.set-contract' : '.set-expand';
								$(sl).trigger('click');
							}
							$('.set-crop').html(JCck.More.CropX.i8n.crop);
						} else {
							$('.set-crop').html(JCck.More.CropX.i8n.again);
						}
				    }
				});
			},
			error:function(){}
		});
	};

	JCck.More.CropX.crop = function(that) {
		var data = JCck.More.CropX.getData($(that).closest('#toolbar-crop'));
			data['selection'] = JCck.More.CropX.ias.getSelection();
			$('#crop-color').spectrum('hide');

		var color = $('#crop-color').spectrum('get');

		if ( color.getAlpha() < 1 ){
			data['color'] = '';
		} else {
			data['color'] = color.toHexString();
		}

		data['matrix'] = 1;

		if ( $('.zoom-range').is(':visible') ) {
	    	data['matrix'] = JCck.More.CropX.panzoom.getScale();
		}

		$.ajax({
			cache: false,
			url: JCck.More.CropX.link+'&t=cropThumbs',
			data: ({ data: data	}),
			type: "GET",
			dataType: "json",
			beforeSend:function(){
				JCck.More.CropX.addProgress();
			},
			success: function(response){
				$('input[name="'+response.name+'_version"]').val(response.version);
				$('.rotate[data-pk="'+response.pk+'"]').addClass('cropped');
				$('#toolbar-crop .dropdown-toggle span').removeClass('to-crop').addClass('cropped');
				var n = $('#toolbar-crop .dropdown-toggle').attr('data-value');
				$('#toolbar-crop .dropdown-menu').find('a[data-value="'+n+'"]').children('span').removeClass('to-crop').addClass('cropped');
				$('.set-crop').html(JCck.More.CropX.i8n.again);							
				JCck.More.CropX.removeProgress();
				JCck.More.CropX.notify(JCck.More.CropX.i8n.cropped,'success');
			},
			error:function(){
				JCck.More.CropX.removeProgress();
				JCck.More.CropX.notify(JCck.More.CropX.i8n.error,'danger');
			}
		});
	};

	JCck.More.CropX.expand = function(that) {
		var data = JCck.More.CropX.getData($(that).closest('#toolbar-crop'));
			data['selection'] = JCck.More.CropX.ias.getSelection();

		$.ajax({
			cache: false,
			url: JCck.More.CropX.link+'&t=setContract',
			data: ({ data: data	}),
			type: "GET",
			dataType: "json",
			beforeSend:function(){
				JCck.More.CropX.addProgress();			
			},
			success: function(response){
		    	JCck.More.CropX.ias.setSelection(response.x,response.y,response.x+response.width,response.y+response.height);
		    	JCck.More.CropX.ias.setOptions({ fadeSpeed: 1000, show:true});
				JCck.More.CropX.ias.update();
		
				const target = document.getElementById('target');
				const selectarea = target.nextElementSibling;
				const area = selectarea.getBoundingClientRect();

				const element = document.getElementById('toolbar-crop');
				const wpl = element.getAttribute('data-wpl');
				const scale = area.width / wpl;

				JCck.More.CropX.btnZoomRange.value = scale;
				JCck.More.CropX.panzoom.zoom(scale);

				JCck.More.CropX.center(that);
				JCck.More.CropX.removeProgress();
				JCck.More.CropX.notify(JCck.More.CropX.i8n.expanded,'success');
			},
			error:function(){}
		});
	};

	JCck.More.CropX.contract = function(that) {
		var data = JCck.More.CropX.getData($(that).closest('#toolbar-crop'));
			data['selection'] = JCck.More.CropX.ias.getSelection();

		$.ajax({
			cache: false,
			url: JCck.More.CropX.link+'&t=setContract',
			data: ({ data: data	}),
			type: "GET",
			dataType: "json",
			beforeSend:function(){
				JCck.More.CropX.addProgress();			
			},
			success: function(response){
		    	JCck.More.CropX.ias.setSelection(response.x,response.y,response.x+response.width,response.y+response.height);
		    	JCck.More.CropX.ias.setOptions({ fadeSpeed: 1000, show:true});
				JCck.More.CropX.ias.update();
				
				JCck.More.CropX.btnZoomRange.value = 1;
				JCck.More.CropX.panzoom.zoom(1);

				JCck.More.CropX.removeProgress();
				JCck.More.CropX.notify(JCck.More.CropX.i8n.contracted,'success');
			},
			error:function(){}
		});
	};

	JCck.More.CropX.center = function(that) {
		var data = JCck.More.CropX.getData($(that).closest('#toolbar-crop')),
			se = JCck.More.CropX.ias.getSelection(),
			op = JCck.More.CropX.ias.getOptions(),
			x = (op.imageWidth - se.width)/2,
			y = (op.imageHeight - se.height)/2;

	    	JCck.More.CropX.ias.setSelection(parseInt(x), parseInt(y), parseInt(x)+parseInt(se.width), parseInt(y)+parseInt(se.height));
	    	JCck.More.CropX.ias.update();
	    	JCck.More.CropX.notify(JCck.More.CropX.i8n.centered,'success');
	};

	JCck.More.CropX.rotate = function(that) {
		if( $(that).hasClass("disabled") ){
			return false;
		}
		if( $(that).hasClass("cropped") ){
			if ( ! confirm(JCck.More.CropX.i8n.alert) ) {
				return false;
			}
		}
		var data = JCck.More.CropX.getData(that);
		$.ajax({
			cache: false,
			url: JCck.More.CropX.link+'&t=rotate',
			data: ({ data: data	}),
			type: "GET",
			dataType: "json",
			beforeSend:function(){
				JCck.More.CropX.addProgress('body');
			},
			success: function(response){
				if (response.preview != '' ) {
					//
				}

				$(that).removeClass('cropped');
				JCck.More.CropX.removeProgress();
				JCck.More.CropX.notify(response.rotated,'success');
			},
			error:function(){}
		});
	};

	//	Modal 
	JCck.More.CropX.modal	=	JCck.Core.getModal({
		"header": false,
		"title": false,
		"close": false,
		"class": "cropx-modal modal-large o-modal-full",
		"callbacks": {
			'shown' : function(e) {
				var n = $('#toolbar-crop .dropdown-toggle').data('value');
				$('#toolbar-crop .dropdown-menu').find('a[data-value="'+n+'"]').trigger('click');
			},
			'hide' : function(e) {
				JCck.More.CropX.close();
			},
			'hidden' : function(e) {
				if ('function' === $("#seblod_form").validationEngine ) {
					$("#seblod_form").validationEngine("hide");
				}
			}
		}
	});

	JCck.More.CropX.close = function() {
		var data = JCck.More.CropX.getData($('#toolbar-crop'));
		$.ajax({
			url: JCck.More.CropX.link+'&t=cleanFile',
			data: ({ data: data }),
			type: "POST"
		});

		var img = $('#'+data.name).parent('.upload_image2').find('img');

		if (img.length) {
			if ($(img).attr('src').indexOf('data:image') == -1) {
				$(img).attr('src', $(img).attr('src')+'1');
			} else {
				var preview = ( data.preview == '0' ) ? '' : '_thumb'+data.preview+'/';
				$(img).attr('src', JCck.Core.sourceURI+'/tmp/'+data.uuid+'/'+preview+data.value+'?1' );
			}
		}

		JCck.More.CropX.destroyColor();	
		$('#resize-parent2').imgAreaSelect({ hide: true, remove: true });
		$("a.getcrop,a.rotate").removeClass("active disabled");
	};

	//	Helpers
	JCck.More.CropX.getData = function(that) {
		return { 
				cropped: $(that).attr('data-cropped'),
				fid: $(that).attr('data-fid'),
				force: parseInt($(that).attr('data-force')) || 0,
				hpl: $(that).attr('data-hpl'),
				name: $(that).attr('data-name'),
				pk: $(that).attr('data-pk'),
				pl: $(that).attr('data-pl'),
				preview: $(that).attr('data-preview'),
				thumb: $(that).attr('data-thumb'),
				thumbs: $(that).attr('data-thumbs'),
				uuid: $(that).attr('data-uuid'),
				value: $(that).attr('data-value'),
				version: $(that).attr('data-version'),
				wpl: $(that).attr('data-wpl')
			  };
	};

	JCck.More.CropX.getSize = function(data) {
		data['ww'] = $("#modal-cck").width();
		data['wh'] = $("#modal-cck").height() - 44 - 44;
		return data;
	};

	//	Manage Color
	JCck.More.CropX.displayColor = function(ext,color,picker,palette) {
		palette = (undefined != palette) ? JSON.parse(palette) : [];
		JCck.More.CropX.color = $('#crop-color').spectrum({
			showInput: true,
			className: "btn-group",
			containerClassName: "dropdown-menu",
			replacerClassName:"btn",
			allowEmpty: (ext=='png' || ext=='gif') ? true : false,
			preferredFormat: "hex",
			chooseText: JCck.More.CropX.i8n.choose,
    		cancelText: JCck.More.CropX.i8n.cancel,
    		showPalette: picker,
    		showPaletteOnly: picker,
    		palette: palette,
    		appendTo: '#color-wrapper',
			move: function(tinycolor) { 
				JCck.More.CropX.updateColor(tinycolor);
			},
			change: function(tinycolor) { 
				JCck.More.CropX.updateColor(tinycolor);
			}
		});
		if (!color) {
			color = 'rgba(255,255,255,0);'
		}
		JCck.More.CropX.color.spectrum('set',color);
	};

	JCck.More.CropX.updateColor = function(tinycolor) {
		var color;
		if ( typeof tinycolor !== 'object' ) {
			color = ( tinycolor == null ) ? '' : tinycolor;
		} else {
			color = tinycolor.toRgbString();
		}
		$('#target').css('background-color',color);
	};

	JCck.More.CropX.destroyColor = function(tinycolor) {
		$('#crop-color').spectrum('destroy');
	};

	//	Screen Notifications
	JCck.More.CropX.notify = function(msg,type) {
		var container = '#modal-cck';
		$.notify(msg, {
			element: container,
			type: type,
			offset: 60,
			z_index: 1060,
			delay: 3000
		});
	};

	JCck.More.CropX.showTooltip = function(that) {
		var msg = that.data('desc');
		$('#tooltip-desc').html('').append('<p>'+JCck.More.CropX.i8n[msg]+'</p>');
	};

	JCck.More.CropX.hideTooltip = function(that) {
		$('#tooltip-desc').html('');
		if ($(that).hasClass('crop-help')) {
			$('#resize-parent').find('.imgareaselect-handle').removeClass('handle-zoom').each(function(i,elem){
				if (i==1||i==2||i==5){
					$(elem).css("left","+=13");
				}
				if (i==2||i==3||i==6){
					$(elem).css("top","+=13");
				}
			});
		}
	};

	JCck.More.CropX.addProgress = function(ct) {
		var container = (ct == 'body') ? ct : '.'+ct+'-content';
		$(container).append(   '<div class="loading-wrapper">' +
			'<div class="ic-Spin-cycle--classic">' +
			'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0" y="0" viewBox="156 -189 512 512" enable-background="new 156 -189 512 512" xml:space="preserve">' +
			'<path d="M636 99h-64c-17.7 0-32-14.3-32-32s14.3-32 32-32h64c17.7 0 32 14.3 32 32S653.7 99 636 99z"/>' +
			'<path d="M547.8-23.5C535.2-11 515-11 502.5-23.5s-12.5-32.8 0-45.2l45.2-45.2c12.5-12.5 32.8-12.5 45.2 0s12.5 32.8 0 45.2L547.8-23.5z"/>' +
			'<path d="M412-61c-17.7 0-32-14.3-32-32v-64c0-17.7 14.3-32 32-32s32 14.3 32 32v64C444-75.3 429.7-61 412-61z"/>' +
			'<path d="M276.2-23.5L231-68.8c-12.5-12.5-12.5-32.8 0-45.2s32.8-12.5 45.2 0l45.2 45.2c12.5 12.5 12.5 32.8 0 45.2S288.8-11 276.2-23.5z"/>' +
			'<path d="M284 67c0 17.7-14.3 32-32 32h-64c-17.7 0-32-14.3-32-32s14.3-32 32-32h64C269.7 35 284 49.3 284 67z"/>' +
			'<path d="M276.2 248c-12.5 12.5-32.8 12.5-45.2 0 -12.5-12.5-12.5-32.8 0-45.2l45.2-45.2c12.5-12.5 32.8-12.5 45.2 0s12.5 32.8 0 45.2L276.2 248z"/>' +
			'<path d="M412 323c-17.7 0-32-14.3-32-32v-64c0-17.7 14.3-32 32-32s32 14.3 32 32v64C444 308.7 429.7 323 412 323z"/>' +
			'<path d="M547.8 157.5l45.2 45.2c12.5 12.5 12.5 32.8 0 45.2 -12.5 12.5-32.8 12.5-45.2 0l-45.2-45.2c-12.5-12.5-12.5-32.8 0-45.2S535.2 145 547.8 157.5z"/>' +
			'</svg></div></div>' );
	};

	//	Select
	JCck.More.CropX.removeProgress = function() {
		$('.loading-wrapper').remove();
	};

	JCck.More.CropX.openSelect = function() {
		$('#toolbar-crop .dropdown-menu').toggle();
	};

	JCck.More.CropX.changeSelect = function(that) {
	    $('#toolbar-crop .dropdown-toggle').attr('data-value',$(that).data('value')).html($(that).html()).prepend('<i class="tri"></i>');
	 	$('#toolbar-crop .dropdown-menu').hide();
		JCck.More.CropX.getThumb($(that).closest('.dropdown'));
	};

	$(document).ready(function() {
			$(document).on('click', function(e) {
		    if (! $(e.target).parents().hasClass('dropdown')) {
		      $('#toolbar-crop .dropdown-menu').hide();
		    }
		});

		$('body')
		.on('mouseover', '.cropTooltip', function(e){
			e.stopPropagation();
			e.preventDefault();
			JCck.More.CropX.showTooltip($(this));
		})
		.on('mouseout', '.cropTooltip', function(e){
			e.stopPropagation();
			e.preventDefault();
			JCck.More.CropX.hideTooltip($(this));
		})
	});
})(jQuery);
