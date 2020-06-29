<?php

/**
 * script-login.php is created for adding JS code in login page footer.
 * @since 2.3.0
 * @version 1.2.3
 */

$loginpress_theme_tem          = get_option( 'customize_presets_settings', 'default1' );
$loginpress_ShowPassword       = version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ? '<span class="dashicons dashicons-visibility" aria-hidden="true"></span>' : __( "Show Password", "loginpress" );
$loginpress_ShowPassword_label = version_compare( $GLOBALS['wp_version'], '5.3', '>=' ) ? '.user-pass-wrap' : '[for="user_pass"]';


/**
 * Load 18th template script.
 *
 * @since 2.3.0
 * * * * * * * * * * * * * * * */

if ( ( $loginpress_theme_tem == 'default18' ) ) : ?>
  <script id="LoginPressTweenMax" src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.20.3/TweenMax.min.js"></script>
  <script id="LoginPressMorphSVGPlugin">
  /*!
  * VERSION: 0.8.6
  * DATE: 2016-07-08
  * UPDATES AND DOCS AT: http://greensock.com
  *
  * @license Copyright (c) 2008-2016, GreenSock. All rights reserved.
  * MorphSVGPlugin is a Club GreenSock membership benefit; You must have a valid membership to use
  * this code without violating the terms of use. Visit http://greensock.com/club/ to sign up or get more details.
  * This work is subject to the software agreement that was issued with your membership.
  *
  * @author: Jack Doyle, jack@greensock.com
  */
  var _gsScope="undefined"!=typeof module&&module.exports&&"undefined"!=typeof global?global:this||window;(_gsScope._gsQueue||(_gsScope._gsQueue=[])).push(function(){"use strict";var a=Math.PI/180,b=180/Math.PI,c=/[achlmqstvz]|(-?\d*\.?\d*(?:e[\-+]?\d+)?)[0-9]/gi,d=/(?:(-|-=|\+=)?\d*\.?\d*(?:e[\-+]?\d+)?)[0-9]/gi,e=/[achlmqstvz]/gi,f=/[\+\-]?\d*\.?\d+e[\+\-]?\d+/gi,g=_gsScope._gsDefine.globals.TweenLite,h=function(a){window.console&&console.log(a)},i=function(b,c){var d,e,f,g,h,i,j=Math.ceil(Math.abs(c)/90),k=0,l=[];for(b*=a,c*=a,d=c/j,e=4/3*Math.sin(d/2)/(1+Math.cos(d/2)),i=0;j>i;i++)f=b+i*d,g=Math.cos(f),h=Math.sin(f),l[k++]=g-e*h,l[k++]=h+e*g,f+=d,g=Math.cos(f),h=Math.sin(f),l[k++]=g+e*h,l[k++]=h-e*g,l[k++]=g,l[k++]=h;return l},j=function(c,d,e,f,g,h,j,k,l){if(c!==k||d!==l){e=Math.abs(e),f=Math.abs(f);var m=g%360*a,n=Math.cos(m),o=Math.sin(m),p=(c-k)/2,q=(d-l)/2,r=n*p+o*q,s=-o*p+n*q,t=e*e,u=f*f,v=r*r,w=s*s,x=v/t+w/u;x>1&&(e=Math.sqrt(x)*e,f=Math.sqrt(x)*f,t=e*e,u=f*f);var y=h===j?-1:1,z=(t*u-t*w-u*v)/(t*w+u*v);0>z&&(z=0);var A=y*Math.sqrt(z),B=A*(e*s/f),C=A*-(f*r/e),D=(c+k)/2,E=(d+l)/2,F=D+(n*B-o*C),G=E+(o*B+n*C),H=(r-B)/e,I=(s-C)/f,J=(-r-B)/e,K=(-s-C)/f,L=Math.sqrt(H*H+I*I),M=H;y=0>I?-1:1;var N=y*Math.acos(M/L)*b;L=Math.sqrt((H*H+I*I)*(J*J+K*K)),M=H*J+I*K,y=0>H*K-I*J?-1:1;var O=y*Math.acos(M/L)*b;!j&&O>0?O-=360:j&&0>O&&(O+=360),O%=360,N%=360;var P,Q,R,S=i(N,O),T=n*e,U=o*e,V=o*-f,W=n*f,X=S.length-2;for(P=0;X>P;P+=2)Q=S[P],R=S[P+1],S[P]=Q*T+R*V+F,S[P+1]=Q*U+R*W+G;return S[S.length-2]=k,S[S.length-1]=l,S}},k=function(a){var b,d,e,g,i,k,l,m,n,o,p,q,r,s=(a+"").replace(f,function(a){var b=+a;return 1e-4>b&&b>-1e-4?0:b}).match(c)||[],t=[],u=0,v=0,w=s.length,x=2,y=0;if(!a||!isNaN(s[0])||isNaN(s[1]))return h("ERROR: malformed path data: "+a),t;for(b=0;w>b;b++)if(r=i,isNaN(s[b])?(i=s[b].toUpperCase(),k=i!==s[b]):b--,e=+s[b+1],g=+s[b+2],k&&(e+=u,g+=v),0===b&&(m=e,n=g),"M"===i)l&&l.length<8&&(t.length-=1,x=0),u=m=e,v=n=g,l=[e,g],y+=x,x=2,t.push(l),b+=2,i="L";else if("C"===i)l||(l=[0,0]),l[x++]=e,l[x++]=g,k||(u=v=0),l[x++]=u+1*s[b+3],l[x++]=v+1*s[b+4],l[x++]=u+=1*s[b+5],l[x++]=v+=1*s[b+6],b+=6;else if("S"===i)"C"===r||"S"===r?(o=u-l[x-4],p=v-l[x-3],l[x++]=u+o,l[x++]=v+p):(l[x++]=u,l[x++]=v),l[x++]=e,l[x++]=g,k||(u=v=0),l[x++]=u+=1*s[b+3],l[x++]=v+=1*s[b+4],b+=4;else if("Q"===i)o=e-u,p=g-v,l[x++]=u+2*o/3,l[x++]=v+2*p/3,k||(u=v=0),u+=1*s[b+3],v+=1*s[b+4],o=e-u,p=g-v,l[x++]=u+2*o/3,l[x++]=v+2*p/3,l[x++]=u,l[x++]=v,b+=4;else if("T"===i)o=u-l[x-4],p=v-l[x-3],l[x++]=u+o,l[x++]=v+p,o=u+1.5*o-e,p=v+1.5*p-g,l[x++]=e+2*o/3,l[x++]=g+2*p/3,l[x++]=u=e,l[x++]=v=g,b+=2;else if("H"===i)g=v,l[x++]=u+(e-u)/3,l[x++]=v+(g-v)/3,l[x++]=u+2*(e-u)/3,l[x++]=v+2*(g-v)/3,l[x++]=u=e,l[x++]=g,b+=1;else if("V"===i)g=e,e=u,k&&(g+=v-u),l[x++]=e,l[x++]=v+(g-v)/3,l[x++]=e,l[x++]=v+2*(g-v)/3,l[x++]=e,l[x++]=v=g,b+=1;else if("L"===i||"Z"===i)"Z"===i&&(e=m,g=n,l.closed=!0),("L"===i||Math.abs(u-e)>.5||Math.abs(v-g)>.5)&&(l[x++]=u+(e-u)/3,l[x++]=v+(g-v)/3,l[x++]=u+2*(e-u)/3,l[x++]=v+2*(g-v)/3,l[x++]=e,l[x++]=g,"L"===i&&(b+=2)),u=e,v=g;else if("A"===i){for(q=j(u,v,1*s[b+1],1*s[b+2],1*s[b+3],1*s[b+4],1*s[b+5],(k?u:0)+1*s[b+6],(k?v:0)+1*s[b+7]),d=0;d<q.length;d++)l[x++]=q[d];u=l[x-2],v=l[x-1],b+=7}else h("Error: malformed path data: "+a);return t.totalPoints=y+x,t},l=function(a,b){var c,d,e,f,g,h,i,j,k,l,m,n,o,p,q=0,r=.999999,s=a.length,t=b/((s-2)/6);for(o=2;s>o;o+=6)for(q+=t;q>r;)c=a[o-2],d=a[o-1],e=a[o],f=a[o+1],g=a[o+2],h=a[o+3],i=a[o+4],j=a[o+5],p=1/(Math.floor(q)+1),k=c+(e-c)*p,m=e+(g-e)*p,k+=(m-k)*p,m+=(g+(i-g)*p-m)*p,l=d+(f-d)*p,n=f+(h-f)*p,l+=(n-l)*p,n+=(h+(j-h)*p-n)*p,a.splice(o,4,c+(e-c)*p,d+(f-d)*p,k,l,k+(m-k)*p,l+(n-l)*p,m,n,g+(i-g)*p,h+(j-h)*p),o+=6,s+=6,q--;return a},m=function(a){var b,c,d,e,f="",g=a.length,h=100;for(c=0;g>c;c++){for(e=a[c],f+="M"+e[0]+","+e[1]+" C",b=e.length,d=2;b>d;d++)f+=(e[d++]*h|0)/h+","+(e[d++]*h|0)/h+" "+(e[d++]*h|0)/h+","+(e[d++]*h|0)/h+" "+(e[d++]*h|0)/h+","+(e[d]*h|0)/h+" ";e.closed&&(f+="z")}return f},n=function(a){for(var b=[],c=a.length-1,d=0;--c>-1;)b[d++]=a[c],b[d++]=a[c+1],c--;for(c=0;d>c;c++)a[c]=b[c];a.reversed=a.reversed?!1:!0},o=function(a){var b,c=a.length,d=0,e=0;for(b=0;c>b;b++)d+=a[b++],e+=a[b];return[d/(c/2),e/(c/2)]},p=function(a){var b,c,d,e=a.length,f=a[0],g=f,h=a[1],i=h;for(d=6;e>d;d+=6)b=a[d],c=a[d+1],b>f?f=b:g>b&&(g=b),c>h?h=c:i>c&&(i=c);return a.centerX=(f+g)/2,a.centerY=(h+i)/2,a.size=(f-g)*(h-i)},q=function(a){for(var b,c,d,e,f,g=a.length,h=a[0][0],i=h,j=a[0][1],k=j;--g>-1;)for(f=a[g],b=f.length,e=6;b>e;e+=6)c=f[e],d=f[e+1],c>h?h=c:i>c&&(i=c),d>j?j=d:k>d&&(k=d);return a.centerX=(h+i)/2,a.centerY=(j+k)/2,a.size=(h-i)*(j-k)},r=function(a,b){return b.length-a.length},s=function(a,b){var c=a.size||p(a),d=b.size||p(b);return Math.abs(d-c)<(c+d)/20?b.centerX-a.centerX||b.centerY-a.centerY:d-c},t=function(a,b){var c,d,e=a.slice(0),f=a.length,g=f-2;for(b=0|b,c=0;f>c;c++)d=(c+b)%g,a[c++]=e[d],a[c]=e[d+1]},u=function(a,b,c,d,e){var f,g,h,i,j=a.length,k=0,l=j-2;for(c*=6,g=0;j>g;g+=6)f=(g+c)%l,i=a[f]-(b[g]-d),h=a[f+1]-(b[g+1]-e),k+=Math.sqrt(h*h+i*i);return k},v=function(a,b,c){var d,e,f,g=a.length,h=o(a),i=o(b),j=i[0]-h[0],k=i[1]-h[1],l=u(a,b,0,j,k),m=0;for(f=6;g>f;f+=6)e=u(a,b,f/6,j,k),l>e&&(l=e,m=f);if(c)for(d=a.slice(0),n(d),f=6;g>f;f+=6)e=u(d,b,f/6,j,k),l>e&&(l=e,m=-f);return m/6},w=function(a,b,c){for(var d,e,f,g,h,i,j=a.length,k=99999999999,l=0,m=0;--j>-1;)for(d=a[j],i=d.length,h=0;i>h;h+=6)e=d[h]-b,f=d[h+1]-c,g=Math.sqrt(e*e+f*f),k>g&&(k=g,l=d[h],m=d[h+1]);return[l,m]},x=function(a,b,c,d,e,f){var g,h,i,j,k,l=b.length,m=0,n=Math.min(a.size||p(a),b[c].size||p(b[c]))*d,o=999999999999,q=a.centerX+e,r=a.centerY+f;for(h=c;l>h&&(g=b[h].size||p(b[h]),!(n>g));h++)i=b[h].centerX-q,j=b[h].centerY-r,k=Math.sqrt(i*i+j*j),o>k&&(m=h,o=k);return k=b[m],b.splice(m,1),k},y=function(a,b,c,d){var e,f,g,i,j,k,m,o=b.length-a.length,u=o>0?b:a,y=o>0?a:b,z=0,A="complexity"===d?r:s,B="position"===d?0:"number"==typeof d?d:.8,C=y.length,D="object"==typeof c&&c.push?c.slice(0):[c],E="reverse"===D[0]||D[0]<0,F="log"===c;if(y[0]){if(u.length>1&&(a.sort(A),b.sort(A),k=u.size||q(u),k=y.size||q(y),k=u.centerX-y.centerX,m=u.centerY-y.centerY,A===s))for(C=0;C<y.length;C++)u.splice(C,0,x(y[C],u,C,B,k,m));if(o)for(0>o&&(o=-o),u[0].length>y[0].length&&l(y[0],(u[0].length-y[0].length)/6|0),C=y.length;o>z;)i=u[C].size||p(u[C]),g=w(y,u[C].centerX,u[C].centerY),i=g[0],j=g[1],y[C++]=[i,j,i,j,i,j,i,j],y.totalPoints+=8,z++;for(C=0;C<a.length;C++)e=b[C],f=a[C],o=e.length-f.length,0>o?l(e,-o/6|0):o>0&&l(f,o/6|0),E&&!f.reversed&&n(f),c=D[C]||0===D[C]?D[C]:"auto",c&&(f.closed||Math.abs(f[0]-f[f.length-2])<.5&&Math.abs(f[1]-f[f.length-1])<.5?"auto"===c||"log"===c?(D[C]=c=v(f,e,0===C),0>c&&(E=!0,n(f),c=-c),t(f,6*c)):"reverse"!==c&&(C&&0>c&&n(f),t(f,6*(0>c?-c:c))):!E&&("auto"===c&&Math.abs(e[0]-f[0])+Math.abs(e[1]-f[1])+Math.abs(e[e.length-2]-f[f.length-2])+Math.abs(e[e.length-1]-f[f.length-1])>Math.abs(e[0]-f[f.length-2])+Math.abs(e[1]-f[f.length-1])+Math.abs(e[e.length-2]-f[0])+Math.abs(e[e.length-1]-f[1])||c%2)?(n(f),D[C]=-1,E=!0):"auto"===c?D[C]=0:"reverse"===c&&(D[C]=-1),f.closed!==e.closed&&(f.closed=e.closed=!1));return F&&h("shapeIndex:["+D.join(",")+"]"),D}},z=function(a,b,c,d){var e=k(a[0]),f=k(a[1]);y(e,f,b||0===b?b:"auto",c)&&(a[0]=m(e),a[1]=m(f),("log"===d||d===!0)&&h('precompile:["'+a[0]+'","'+a[1]+'"]'))},A=function(a,b,c){return b||c||a||0===a?function(d){z(d,a,b,c)}:z},B=function(a,b){if(!b)return a;var c,e,f,g=a.match(d)||[],h=g.length,i="";for("reverse"===b?(e=h-1,c=-2):(e=(2*(parseInt(b,10)||0)+1+100*h)%h,c=2),f=0;h>f;f+=2)i+=g[e-1]+","+g[e]+" ",e=(e+c)%h;return i},C=function(a,b){var c,d,e,f,g,h,i,j=0,k=parseFloat(a[0]),l=parseFloat(a[1]),m=k+","+l+" ",n=.999999;for(e=a.length,c=.5*b/(.5*e-1),d=0;e-2>d;d+=2){if(j+=c,h=parseFloat(a[d+2]),i=parseFloat(a[d+3]),j>n)for(g=1/(Math.floor(j)+1),f=1;j>n;)m+=(k+(h-k)*g*f).toFixed(2)+","+(l+(i-l)*g*f).toFixed(2)+" ",j--,f++;m+=h+","+i+" ",k=h,l=i}return m},D=function(a){var b=a[0].match(d)||[],c=a[1].match(d)||[],e=c.length-b.length;e>0?a[0]=C(b,e):a[1]=C(c,-e)},E=function(a){return isNaN(a)?D:function(b){D(b),b[1]=B(b[1],parseInt(a,10))}},F=function(a,b){var c=document.createElementNS("http://www.w3.org/2000/svg","path"),d=Array.prototype.slice.call(a.attributes),e=d.length;for(b=","+b+",";--e>-1;)-1===b.indexOf(","+d[e].nodeName+",")&&c.setAttributeNS(null,d[e].nodeName,d[e].nodeValue);return c},G=function(a,b){var c,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y=a.tagName.toLowerCase(),z=.552284749831;return"path"!==y&&a.getBBox?(i=F(a,"x,y,width,height,cx,cy,rx,ry,r,x1,x2,y1,y2,points"),"rect"===y?(g=+a.getAttribute("rx")||0,h=+a.getAttribute("ry")||0,e=+a.getAttribute("x")||0,f=+a.getAttribute("y")||0,m=(+a.getAttribute("width")||0)-2*g,n=(+a.getAttribute("height")||0)-2*h,g||h?(o=e+g*(1-z),p=e+g,q=p+m,r=q+g*z,s=q+g,t=f+h*(1-z),u=f+h,v=u+n,w=v+h*z,x=v+h,c="M"+s+","+u+" V"+v+" C"+[s,w,r,x,q,x,q-(q-p)/3,x,p+(q-p)/3,x,p,x,o,x,e,w,e,v,e,v-(v-u)/3,e,u+(v-u)/3,e,u,e,t,o,f,p,f,p+(q-p)/3,f,q-(q-p)/3,f,q,f,r,f,s,t,s,u].join(",")+"z"):c="M"+(e+m)+","+f+" v"+n+" h"+-m+" v"+-n+" h"+m+"z"):"circle"===y||"ellipse"===y?("circle"===y?(g=h=+a.getAttribute("r")||0,k=g*z):(g=+a.getAttribute("rx")||0,h=+a.getAttribute("ry")||0,k=h*z),e=+a.getAttribute("cx")||0,f=+a.getAttribute("cy")||0,j=g*z,c="M"+(e+g)+","+f+" C"+[e+g,f+k,e+j,f+h,e,f+h,e-j,f+h,e-g,f+k,e-g,f,e-g,f-k,e-j,f-h,e,f-h,e+j,f-h,e+g,f-k,e+g,f].join(",")+"z"):"line"===y?c="M"+a.getAttribute("x1")+","+a.getAttribute("y1")+" L"+a.getAttribute("x2")+","+a.getAttribute("y2"):("polyline"===y||"polygon"===y)&&(l=(a.getAttribute("points")+"").match(d)||[],e=l.shift(),f=l.shift(),c="M"+e+","+f+" L"+l.join(","),"polygon"===y&&(c+=","+e+","+f+"z")),i.setAttribute("d",c),b&&a.parentNode&&(a.parentNode.insertBefore(i,a),a.parentNode.removeChild(a)),i):a},H=function(a,b,c){var e,f,i="string"==typeof a;return(!i||(a.match(d)||[]).length<3)&&(e=i?g.selector(a):a&&a[0]?a:[a],e&&e[0]?(e=e[0],f=e.nodeName.toUpperCase(),b&&"PATH"!==f&&(e=G(e,!1),f="PATH"),a=e.getAttribute("PATH"===f?"d":"points")||"",e===c&&(a=e.getAttributeNS(null,"data-original")||a)):(h("WARNING: invalid morph to: "+a),a=!1)),a},I="Use MorphSVGPlugin.convertToPath(elementOrSelectorText) to convert to a path before morphing.",J=_gsScope._gsDefine.plugin({propName:"morphSVG",API:2,global:!0,version:"0.8.6",init:function(a,b,c,d){var f,g,i,j,k;return"function"!=typeof a.setAttribute?!1:("function"==typeof b&&(b=b(d,a)),f=a.nodeName.toUpperCase(),k="POLYLINE"===f||"POLYGON"===f,"PATH"===f||k?(g="PATH"===f?"d":"points",("string"==typeof b||b.getBBox||b[0])&&(b={shape:b}),j=H(b.shape||b.d||b.points||"","d"===g,a),k&&e.test(j)?(h("WARNING: a <"+f+"> cannot accept path data. "+I),!1):(j&&(this._target=a,a.getAttributeNS(null,"data-original")||a.setAttributeNS(null,"data-original",a.getAttribute(g)),i=this._addTween(a,"setAttribute",a.getAttribute(g)+"",j+"","morphSVG",!1,g,"object"==typeof b.precompile?function(a){a[0]=b.precompile[0],a[1]=b.precompile[1]}:"d"===g?A(b.shapeIndex,b.map||J.defaultMap,b.precompile):E(b.shapeIndex)),i&&(this._overwriteProps.push("morphSVG"),i.end=j,i.endProp=g)),!0)):(h("WARNING: cannot morph a <"+f+"> SVG element. "+I),!1))},set:function(a){var b;if(this._super.setRatio.call(this,a),1===a)for(b=this._firstPT;b;)b.end&&this._target.setAttribute(b.endProp,b.end),b=b._next}});J.pathFilter=z,J.pointsFilter=D,J.subdivideRawBezier=l,J.defaultMap="size",J.pathDataToRawBezier=function(a){return k(H(a,!0))},J.equalizeSegmentQuantity=y,J.convertToPath=function(a,b){"string"==typeof a&&(a=g.selector(a));for(var c=a&&0!==a.length?a.length&&a[0]&&a[0].nodeType?Array.prototype.slice.call(a,0):[a]:[],d=c.length;--d>-1;)c[d]=G(c[d],b!==!1);return c},J.pathDataToBezier=function(a,b){var c,d,e,f,h,i,j,l,m=k(H(a,!0))[0]||[],n=0;if(b=b||{},l=b.align||b.relative,f=b.matrix||[1,0,0,1,0,0],h=b.offsetX||0,i=b.offsetY||0,"relative"===l||l===!0?(h-=m[0]*f[0]+m[1]*f[2],i-=m[0]*f[1]+m[1]*f[3],n="+="):(h+=f[4],i+=f[5],l&&(l="string"==typeof l?g.selector(l):l&&l[0]?l:[l],l&&l[0]&&(j=l[0].getBBox()||{x:0,y:0},h-=j.x,i-=j.y))),c=[],e=m.length,f)for(d=0;e>d;d+=2)c.push({x:n+(m[d]*f[0]+m[d+1]*f[2]+h),y:n+(m[d]*f[1]+m[d+1]*f[3]+i)});else for(d=0;e>d;d+=2)c.push({x:n+(m[d]+h),y:n+(m[d+1]+i)});return c}}),_gsScope._gsDefine&&_gsScope._gsQueue.pop()(),function(a){"use strict";var b=function(){return(_gsScope.GreenSockGlobals||_gsScope)[a]};"function"==typeof define&&define.amd?define(["TweenLite"],b):"undefined"!=typeof module&&module.exports&&(require("../TweenLite.js"),module.exports=b())}("MorphSVGPlugin");
  </script>
  <script id="LoginPressSVGContainer">
  document.addEventListener( 'DOMContentLoaded', function() {
  	document.querySelector('#login h1 a').innerHTML = '<div class="loginpress_svgContainer"> <div> <svg class="loginpress_mySVG" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 200 200"> <defs> <circle id="loginpress_armMaskPath" cx="100" cy="100" r="100"/> </defs> <clipPath id="loginpress_armMask"> <use xlink:href="#loginpress_armMaskPath" overflow="visible"/> </clipPath> <circle cx="100" cy="100" r="100" fill="#a9ddf3"/> <g class="loginpress_body"> <path class="loginpress_bodyBGchanged" style="display: none;" fill="#FFFFFF" d="M200,122h-35h-14.9V72c0-27.6-22.4-50-50-50s-50,22.4-50,50v50H35.8H0l0,91h200L200,122z"/> <path class="loginpress_bodyBGnormal" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoinn="round" fill="#FFFFFF" d="M200,158.5c0-20.2-14.8-36.5-35-36.5h-14.9V72.8c0-27.4-21.7-50.4-49.1-50.8c-28-0.5-50.9,22.1-50.9,50v50 H35.8C16,122,0,138,0,157.8L0,213h200L200,158.5z"/> <path fill="#DDF1FA" d="M100,156.4c-22.9,0-43,11.1-54.1,27.7c15.6,10,34.2,15.9,54.1,15.9s38.5-5.8,54.1-15.9 C143,167.5,122.9,156.4,100,156.4z"/> </g> <g class="loginpress_earL"> <g class="loginpress_outerEar" fill="#ddf1fa" stroke="#3a5e77" stroke-width="2.5"> <circle cx="47" cy="83" r="11.5"/> <path d="M46.3 78.9c-2.3 0-4.1 1.9-4.1 4.1 0 2.3 1.9 4.1 4.1 4.1" stroke-linecap="round" stroke-linejoin="round"/> </g> <g class="loginpress_earHair"> <rect x="51" y="64" fill="#FFFFFF" width="15" height="35"/> <path d="M53.4 62.8C48.5 67.4 45 72.2 42.8 77c3.4-.1 6.8-.1 10.1.1-4 3.7-6.8 7.6-8.2 11.6 2.1 0 4.2 0 6.3.2-2.6 4.1-3.8 8.3-3.7 12.5 1.2-.7 3.4-1.4 5.2-1.9" fill="#fff" stroke="#3a5e77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/> </g> </g> <g class="loginpress_earR"> <g class="loginpress_outerEar"> <circle fill="#DDF1FA" stroke="#3A5E77" stroke-width="2.5" cx="153" cy="83" r="11.5"/> <path fill="#DDF1FA" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M153.7,78.9 c2.3,0,4.1,1.9,4.1,4.1c0,2.3-1.9,4.1-4.1,4.1"/> </g> <g class="loginpress_earHair"> <rect x="134" y="64" fill="#FFFFFF" width="15" height="35"/> <path fill="#FFFFFF" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M146.6,62.8 c4.9,4.6,8.4,9.4,10.6,14.2c-3.4-0.1-6.8-0.1-10.1,0.1c4,3.7,6.8,7.6,8.2,11.6c-2.1,0-4.2,0-6.3,0.2c2.6,4.1,3.8,8.3,3.7,12.5 c-1.2-0.7-3.4-1.4-5.2-1.9"/> </g> </g> <path class="loginpress_chin" d="M84.1 121.6c2.7 2.9 6.1 5.4 9.8 7.5l.9-4.5c2.9 2.5 6.3 4.8 10.2 6.5 0-1.9-.1-3.9-.2-5.8 3 1.2 6.2 2 9.7 2.5-.3-2.1-.7-4.1-1.2-6.1" fill="none" stroke="#3a5e77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/> <path class="loginpress_face" fill="#DDF1FA" d="M134.5,46v35.5c0,21.815-15.446,39.5-34.5,39.5s-34.5-17.685-34.5-39.5V46"/> <path class="loginpress_hair" fill="#FFFFFF" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M81.457,27.929 c1.755-4.084,5.51-8.262,11.253-11.77c0.979,2.565,1.883,5.14,2.712,7.723c3.162-4.265,8.626-8.27,16.272-11.235 c-0.737,3.293-1.588,6.573-2.554,9.837c4.857-2.116,11.049-3.64,18.428-4.156c-2.403,3.23-5.021,6.391-7.852,9.474"/> <g class="loginpress_eyebrow"> <path fill="#FFFFFF" d="M138.142,55.064c-4.93,1.259-9.874,2.118-14.787,2.599c-0.336,3.341-0.776,6.689-1.322,10.037 c-4.569-1.465-8.909-3.222-12.996-5.226c-0.98,3.075-2.07,6.137-3.267,9.179c-5.514-3.067-10.559-6.545-15.097-10.329 c-1.806,2.889-3.745,5.73-5.816,8.515c-7.916-4.124-15.053-9.114-21.296-14.738l1.107-11.768h73.475V55.064z"/> <path fill="#FFFFFF" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M63.56,55.102 c6.243,5.624,13.38,10.614,21.296,14.738c2.071-2.785,4.01-5.626,5.816-8.515c4.537,3.785,9.583,7.263,15.097,10.329 c1.197-3.043,2.287-6.104,3.267-9.179c4.087,2.004,8.427,3.761,12.996,5.226c0.545-3.348,0.986-6.696,1.322-10.037 c4.913-0.481,9.857-1.34,14.787-2.599"/> </g> <g class="loginpress_eyeL"> <circle cx="85.5" cy="78.5" r="3.5" fill="#3a5e77"/> <circle cx="84" cy="76" r="1" fill="#fff"/> </g> <g class="loginpress_eyeR"> <circle cx="114.5" cy="78.5" r="3.5" fill="#3a5e77"/> <circle cx="113" cy="76" r="1" fill="#fff"/> </g> <g class="loginpress_mouth"> <path class="loginpress_mouthBG" fill="#617E92" d="M100.2,101c-0.4,0-1.4,0-1.8,0c-2.7-0.3-5.3-1.1-8-2.5c-0.7-0.3-0.9-1.2-0.6-1.8 c0.2-0.5,0.7-0.7,1.2-0.7c0.2,0,0.5,0.1,0.6,0.2c3,1.5,5.8,2.3,8.6,2.3s5.7-0.7,8.6-2.3c0.2-0.1,0.4-0.2,0.6-0.2 c0.5,0,1,0.3,1.2,0.7c0.4,0.7,0.1,1.5-0.6,1.9c-2.6,1.4-5.3,2.2-7.9,2.5C101.7,101,100.5,101,100.2,101z"/> <path style="display: none;" class="loginpress_mouthSmallBG" fill="#617E92" d="M100.2,101c-0.4,0-1.4,0-1.8,0c-2.7-0.3-5.3-1.1-8-2.5c-0.7-0.3-0.9-1.2-0.6-1.8 c0.2-0.5,0.7-0.7,1.2-0.7c0.2,0,0.5,0.1,0.6,0.2c3,1.5,5.8,2.3,8.6,2.3s5.7-0.7,8.6-2.3c0.2-0.1,0.4-0.2,0.6-0.2 c0.5,0,1,0.3,1.2,0.7c0.4,0.7,0.1,1.5-0.6,1.9c-2.6,1.4-5.3,2.2-7.9,2.5C101.7,101,100.5,101,100.2,101z"/> <path style="display: none;" class="loginpress_mouthMediumBG" d="M95,104.2c-4.5,0-8.2-3.7-8.2-8.2v-2c0-1.2,1-2.2,2.2-2.2h22c1.2,0,2.2,1,2.2,2.2v2 c0,4.5-3.7,8.2-8.2,8.2H95z"/> <path style="display: none;" class="loginpress_mouthLargeBG" d="M100 110.2c-9 0-16.2-7.3-16.2-16.2 0-2.3 1.9-4.2 4.2-4.2h24c2.3 0 4.2 1.9 4.2 4.2 0 9-7.2 16.2-16.2 16.2z" fill="#617e92" stroke="#3a5e77" stroke-linejoin="round" stroke-width="2.5"/> <defs> <path id="loginpress_mouthMaskPath" d="M100.2,101c-0.4,0-1.4,0-1.8,0c-2.7-0.3-5.3-1.1-8-2.5c-0.7-0.3-0.9-1.2-0.6-1.8 c0.2-0.5,0.7-0.7,1.2-0.7c0.2,0,0.5,0.1,0.6,0.2c3,1.5,5.8,2.3,8.6,2.3s5.7-0.7,8.6-2.3c0.2-0.1,0.4-0.2,0.6-0.2 c0.5,0,1,0.3,1.2,0.7c0.4,0.7,0.1,1.5-0.6,1.9c-2.6,1.4-5.3,2.2-7.9,2.5C101.7,101,100.5,101,100.2,101z"/> </defs> <clipPath id="loginpress_mouthMask"> <use xlink:href="#loginpress_mouthMaskPath" overflow="visible"/> </clipPath> <g clip-path="url(#loginpress_mouthMask)"> <g class="loginpress_tongue"> <circle cx="100" cy="107" r="8" fill="#cc4a6c"/> <ellipse class="loginpress_tongueHighlight" cx="100" cy="100.5" rx="3" ry="1.5" opacity=".1" fill="#fff"/> </g> </g> <path clip-path="url(#loginpress_mouthMask)" class="loginpress_tooth" style="fill:#FFFFFF;" d="M106,97h-4c-1.1,0-2-0.9-2-2v-2h8v2C108,96.1,107.1,97,106,97z"/> <path class="loginpress_mouthOutline" fill="none" stroke="#3A5E77" stroke-width="2.5" stroke-linejoin="round" d="M100.2,101c-0.4,0-1.4,0-1.8,0c-2.7-0.3-5.3-1.1-8-2.5c-0.7-0.3-0.9-1.2-0.6-1.8 c0.2-0.5,0.7-0.7,1.2-0.7c0.2,0,0.5,0.1,0.6,0.2c3,1.5,5.8,2.3,8.6,2.3s5.7-0.7,8.6-2.3c0.2-0.1,0.4-0.2,0.6-0.2 c0.5,0,1,0.3,1.2,0.7c0.4,0.7,0.1,1.5-0.6,1.9c-2.6,1.4-5.3,2.2-7.9,2.5C101.7,101,100.5,101,100.2,101z"/> </g> <path class="loginpress_nose" d="M97.7 79.9h4.7c1.9 0 3 2.2 1.9 3.7l-2.3 3.3c-.9 1.3-2.9 1.3-3.8 0l-2.3-3.3c-1.3-1.6-.2-3.7 1.8-3.7z" fill="#3a5e77"/> <g class="loginpress_arms" clip-path="url(#loginpress_armMask)"> <g class="loginpress_armL" style="visibility: hidden;"> <polygon fill="#DDF1FA" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" points="121.3,98.4 111,59.7 149.8,49.3 169.8,85.4"/> <path fill="#DDF1FA" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="M134.4,53.5l19.3-5.2c2.7-0.7,5.4,0.9,6.1,3.5v0c0.7,2.7-0.9,5.4-3.5,6.1l-10.3,2.8"/> <path fill="#DDF1FA" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="M150.9,59.4l26-7c2.7-0.7,5.4,0.9,6.1,3.5v0c0.7,2.7-0.9,5.4-3.5,6.1l-21.3,5.7"/> <g class="loginpress_twoFingers"> <path fill="#DDF1FA" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="M158.3,67.8l23.1-6.2c2.7-0.7,5.4,0.9,6.1,3.5v0c0.7,2.7-0.9,5.4-3.5,6.1l-23.1,6.2"/> <path fill="#A9DDF3" d="M180.1,65l2.2-0.6c1.1-0.3,2.2,0.3,2.4,1.4v0c0.3,1.1-0.3,2.2-1.4,2.4l-2.2,0.6L180.1,65z"/> <path fill="#DDF1FA" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="M160.8,77.5l19.4-5.2c2.7-0.7,5.4,0.9,6.1,3.5v0c0.7,2.7-0.9,5.4-3.5,6.1l-18.3,4.9"/> <path fill="#A9DDF3" d="M178.8,75.7l2.2-0.6c1.1-0.3,2.2,0.3,2.4,1.4v0c0.3,1.1-0.3,2.2-1.4,2.4l-2.2,0.6L178.8,75.7z"/> </g> <path fill="#A9DDF3" d="M175.5,55.9l2.2-0.6c1.1-0.3,2.2,0.3,2.4,1.4v0c0.3,1.1-0.3,2.2-1.4,2.4l-2.2,0.6L175.5,55.9z"/> <path fill="#A9DDF3" d="M152.1,50.4l2.2-0.6c1.1-0.3,2.2,0.3,2.4,1.4v0c0.3,1.1-0.3,2.2-1.4,2.4l-2.2,0.6L152.1,50.4z"/> <path fill="#FFFFFF" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M123.5,97.8 c-41.4,14.9-84.1,30.7-108.2,35.5L1.2,81c33.5-9.9,71.9-16.5,111.9-21.8"/> <path fill="#FFFFFF" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M108.5,60.4 c7.7-5.3,14.3-8.4,22.8-13.2c-2.4,5.3-4.7,10.3-6.7,15.1c4.3,0.3,8.4,0.7,12.3,1.3c-4.2,5-8.1,9.6-11.5,13.9 c3.1,1.1,6,2.4,8.7,3.8c-1.4,2.9-2.7,5.8-3.9,8.5c2.5,3.5,4.6,7.2,6.3,11c-4.9-0.8-9-0.7-16.2-2.7"/> <path fill="#FFFFFF" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M94.5,103.8 c-0.6,4-3.8,8.9-9.4,14.7c-2.6-1.8-5-3.7-7.2-5.7c-2.5,4.1-6.6,8.8-12.2,14c-1.9-2.2-3.4-4.5-4.5-6.9c-4.4,3.3-9.5,6.9-15.4,10.8 c-0.2-3.4,0.1-7.1,1.1-10.9"/> <path fill="#FFFFFF" stroke="#3A5E77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M97.5,63.9 c-1.7-2.4-5.9-4.1-12.4-5.2c-0.9,2.2-1.8,4.3-2.5,6.5c-3.8-1.8-9.4-3.1-17-3.8c0.5,2.3,1.2,4.5,1.9,6.8c-5-0.6-11.2-0.9-18.4-1 c2,2.9,0.9,3.5,3.9,6.2"/> </g> <g class="loginpress_armR" style="visibility: hidden;"> <path fill="#ddf1fa" stroke="#3a5e77" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2.5" d="M265.4 97.3l10.4-38.6-38.9-10.5-20 36.1z"/> <path fill="#ddf1fa" stroke="#3a5e77" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2.5" d="M252.4 52.4L233 47.2c-2.7-.7-5.4.9-6.1 3.5-.7 2.7.9 5.4 3.5 6.1l10.3 2.8M226 76.4l-19.4-5.2c-2.7-.7-5.4.9-6.1 3.5-.7 2.7.9 5.4 3.5 6.1l18.3 4.9M228.4 66.7l-23.1-6.2c-2.7-.7-5.4.9-6.1 3.5-.7 2.7.9 5.4 3.5 6.1l23.1 6.2M235.8 58.3l-26-7c-2.7-.7-5.4.9-6.1 3.5-.7 2.7.9 5.4 3.5 6.1l21.3 5.7"/> <path fill="#a9ddf3" d="M207.9 74.7l-2.2-.6c-1.1-.3-2.2.3-2.4 1.4-.3 1.1.3 2.2 1.4 2.4l2.2.6 1-3.8zM206.7 64l-2.2-.6c-1.1-.3-2.2.3-2.4 1.4-.3 1.1.3 2.2 1.4 2.4l2.2.6 1-3.8zM211.2 54.8l-2.2-.6c-1.1-.3-2.2.3-2.4 1.4-.3 1.1.3 2.2 1.4 2.4l2.2.6 1-3.8zM234.6 49.4l-2.2-.6c-1.1-.3-2.2.3-2.4 1.4-.3 1.1.3 2.2 1.4 2.4l2.2.6 1-3.8z"/> <path fill="#fff" stroke="#3a5e77" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M263.3 96.7c41.4 14.9 84.1 30.7 108.2 35.5l14-52.3C352 70 313.6 63.5 273.6 58.1"/> <path fill="#fff" stroke="#3a5e77" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M278.2 59.3l-18.6-10 2.5 11.9-10.7 6.5 9.9 8.7-13.9 6.4 9.1 5.9-13.2 9.2 23.1-.9M284.5 100.1c-.4 4 1.8 8.9 6.7 14.8 3.5-1.8 6.7-3.6 9.7-5.5 1.8 4.2 5.1 8.9 10.1 14.1 2.7-2.1 5.1-4.4 7.1-6.8 4.1 3.4 9 7 14.7 11 1.2-3.4 1.8-7 1.7-10.9M314 66.7s5.4-5.7 12.6-7.4c1.7 2.9 3.3 5.7 4.9 8.6 3.8-2.5 9.8-4.4 18.2-5.7.1 3.1.1 6.1 0 9.2 5.5-1 12.5-1.6 20.8-1.9-1.4 3.9-2.5 8.4-2.5 8.4"/> </g> </g> </svg> </div> </div>';
  	var emailLabel = document.querySelector('[for="user_login"]'),
      email = document.querySelector('#user_login'),
      passwordLabel = document.querySelector('[for="user_pass"]'),
      passwordwrapper = document.querySelector('<?php echo $loginpress_ShowPassword_label; ?>'),
  	check_box = document.createElement("input"),
  	check_wrapper = document.createElement("div"),
  	check_box_label = document.createElement("label");
  	check_box_label.setAttribute('for','loginpress_showPasswordCheck');
  	check_box_label.setAttribute('id','loginpress_showPasswordToggle');
  	check_wrapper.setAttribute('id','loginpress_showPasswordWrapper');
  	check_box_label.innerHTML = '<?php echo $loginpress_ShowPassword; ?>';
  	check_box.setAttribute('type','checkbox');
  	check_box.setAttribute('id','loginpress_showPasswordCheck');
  	check_box.value = "<?php esc_html_e( 'Show Password', 'loginpress' ); ?>";
	  if(passwordwrapper){
  	passwordwrapper.appendChild(check_wrapper);
  	document.querySelector('#loginpress_showPasswordWrapper').appendChild(check_box);
  	document.querySelector('#loginpress_showPasswordWrapper').appendChild(check_box_label);
	  }
	  reg_password = document.querySelector('#loginpress-reg-pass'),
	  repeat_password = document.querySelector('#lloginpress-reg-pass-2'),
	  user_email = document.querySelector('[name="user_email"]'),
      password = document.querySelector('#user_pass'), showPasswordCheck = document.querySelector('#loginpress_showPasswordCheck'), showPasswordToggle = document.querySelector('#loginpress_showPasswordToggle'), mySVG = document.querySelector('.loginpress_svgContainer'), twoFingers = document.querySelector('.loginpress_twoFingers'), armL = document.querySelector('.loginpress_armL'), armR = document.querySelector('.loginpress_armR'), eyeL = document.querySelector('.loginpress_eyeL'), eyeR = document.querySelector('.loginpress_eyeR'), nose = document.querySelector('.loginpress_nose'), mouth = document.querySelector('.loginpress_mouth'), mouthBG = document.querySelector('.loginpress_mouthBG'), mouthSmallBG = document.querySelector('.loginpress_mouthSmallBG'), mouthMediumBG = document.querySelector('.loginpress_mouthMediumBG'), mouthLargeBG = document.querySelector('.loginpress_mouthLargeBG'), mouthMaskPath = document.querySelector('#loginpress_mouthMaskPath'), mouthOutline = document.querySelector('.loginpress_mouthOutline'), tooth = document.querySelector('.loginpress_tooth'), tongue = document.querySelector('.loginpress_tongue'), chin = document.querySelector('.loginpress_chin'), face = document.querySelector('.loginpress_face'), eyebrow = document.querySelector('.loginpress_eyebrow'), outerEarL = document.querySelector('.loginpress_earL .loginpress_outerEar'), outerEarR = document.querySelector('.loginpress_earR .loginpress_outerEar'), earHairL = document.querySelector('.loginpress_earL .loginpress_earHair'), earHairR = document.querySelector('.loginpress_earR .loginpress_earHair'), hair = document.querySelector('.loginpress_hair'), bodyBG = document.querySelector('.loginpress_bodyBGnormal'), bodyBGchanged = document.querySelector('.loginpress_bodyBGchanged');
  var activeElement, curEmailIndex, screenCenter, svgCoords, emailCoords, emailScrollMax, chinMin = .5, dFromC, mouthStatus = "small", blinking, eyeScale = 1, eyesCovered = false, showPasswordClicked = false;
  var eyeLCoords, eyeRCoords, noseCoords, mouthCoords, eyeLAngle, eyeLX, eyeLY, eyeRAngle, eyeRX, eyeRY, noseAngle, noseX, noseY, mouthAngle, mouthX, mouthY, mouthR, chinX, chinY, chinS, faceX, faceY, faceSkew, eyebrowSkew, outerEarX, outerEarY, hairX, hairS;
  function calculateFaceMoveLoginPress(e) {
  	var
  		carPos = email.selectionEnd,
  		div = document.createElement('div'),
  		span = document.createElement('span'),
  		copyStyle = getComputedStyle(email),
  		caretCoords = {}
  	;
  	if(carPos == null || carPos == 0) {
  		// if browser doesn't support 'selectionEnd' property on input[type="email"], use 'value.length' property instead
  		carPos = email.value.length;
  	}
  	[].forEach.call(copyStyle, function(prop){
  		div.style[prop] = copyStyle[prop];
  	});
  	div.style.position = 'absolute';
  	document.body.appendChild(div);
  	div.textContent = email.value.substr(0, carPos);
  	span.textContent = email.value.substr(carPos) || '.';
  	div.appendChild(span);

  	if(email.scrollWidth <= emailScrollMax) {
  		caretCoords = getPositionLoginPress(span);
  		dFromC = screenCenter - (caretCoords.x + emailCoords.x);
  		eyeLAngle = getAngleLoginPress(eyeLCoords.x, eyeLCoords.y, emailCoords.x + caretCoords.x, emailCoords.y + 25);
  		eyeRAngle = getAngleLoginPress(eyeRCoords.x, eyeRCoords.y, emailCoords.x + caretCoords.x, emailCoords.y + 25);
  		noseAngle = getAngleLoginPress(noseCoords.x, noseCoords.y, emailCoords.x + caretCoords.x, emailCoords.y + 25);
  		mouthAngle = getAngleLoginPress(mouthCoords.x, mouthCoords.y, emailCoords.x + caretCoords.x, emailCoords.y + 25);
  	} else {
  		eyeLAngle = getAngleLoginPress(eyeLCoords.x, eyeLCoords.y, emailCoords.x + emailScrollMax, emailCoords.y + 25);
  		eyeRAngle = getAngleLoginPress(eyeRCoords.x, eyeRCoords.y, emailCoords.x + emailScrollMax, emailCoords.y + 25);
  		noseAngle = getAngleLoginPress(noseCoords.x, noseCoords.y, emailCoords.x + emailScrollMax, emailCoords.y + 25);
  		mouthAngle = getAngleLoginPress(mouthCoords.x, mouthCoords.y, emailCoords.x + emailScrollMax, emailCoords.y + 25);
  	}

  	eyeLX = Math.cos(eyeLAngle) * 20;
  	eyeLY = Math.sin(eyeLAngle) * 10;
  	eyeRX = Math.cos(eyeRAngle) * 20;
  	eyeRY = Math.sin(eyeRAngle) * 10;
  	noseX = Math.cos(noseAngle) * 23;
  	noseY = Math.sin(noseAngle) * 10;
  	mouthX = Math.cos(mouthAngle) * 23;
  	mouthY = Math.sin(mouthAngle) * 10;
  	mouthR = Math.cos(mouthAngle) * 6;
  	chinX = mouthX * .8;
  	chinY = mouthY * .5;
  	chinS = 1 - ((dFromC * .15) / 100);
  	if(chinS > 1) {
  		chinS = 1 - (chinS - 1);
  		if(chinS < chinMin) {
  			chinS = chinMin;
  		}
  	}
  	faceX = mouthX * .3;
  	faceY = mouthY * .4;
  	faceSkew = Math.cos(mouthAngle) * 5;
  	eyebrowSkew = Math.cos(mouthAngle) * 25;
  	outerEarX = Math.cos(mouthAngle) * 4;
  	outerEarY = Math.cos(mouthAngle) * 5;
  	hairX = Math.cos(mouthAngle) * 6;
  	hairS = 1.2;

  	TweenMax.to(eyeL, 1, {x: -eyeLX , y: -eyeLY, ease: Expo.easeOut});
  	TweenMax.to(eyeR, 1, {x: -eyeRX , y: -eyeRY, ease: Expo.easeOut});
  	TweenMax.to(nose, 1, {x: -noseX, y: -noseY, rotation: mouthR, transformOrigin: "center center", ease: Expo.easeOut});
  	TweenMax.to(mouth, 1, {x: -mouthX , y: -mouthY, rotation: mouthR, transformOrigin: "center center", ease: Expo.easeOut});
  	TweenMax.to(chin, 1, {x: -chinX, y: -chinY, scaleY: chinS, ease: Expo.easeOut});
  	TweenMax.to(face, 1, {x: -faceX, y: -faceY, skewX: -faceSkew, transformOrigin: "center top", ease: Expo.easeOut});
  	TweenMax.to(eyebrow, 1, {x: -faceX, y: -faceY, skewX: -eyebrowSkew, transformOrigin: "center top", ease: Expo.easeOut});
  	TweenMax.to(outerEarL, 1, {x: outerEarX, y: -outerEarY, ease: Expo.easeOut});
  	TweenMax.to(outerEarR, 1, {x: outerEarX, y: outerEarY, ease: Expo.easeOut});
  	TweenMax.to(earHairL, 1, {x: -outerEarX, y: -outerEarY, ease: Expo.easeOut});
  	TweenMax.to(earHairR, 1, {x: -outerEarX, y: outerEarY, ease: Expo.easeOut});
  	TweenMax.to(hair, 1, {x: hairX, scaleY: hairS, transformOrigin: "center bottom", ease: Expo.easeOut});

  	document.body.removeChild(div);
  };

  function onLoginPressEmailInput(e) {
  	calculateFaceMoveLoginPress(e);
  	var value = email.value;
  	curEmailIndex = value.length;

  	// very crude email validation to trigger effects
  	if(curEmailIndex > 0) {
  		if(mouthStatus == "small") {
  			mouthStatus = "medium";
  			TweenMax.to([mouthBG, mouthOutline, mouthMaskPath], 1, {morphSVG: mouthMediumBG, shapeIndex: 8, ease: Expo.easeOut});
  			TweenMax.to(tooth, 1, {x: 0, y: 0, ease: Expo.easeOut});
  			TweenMax.to(tongue, 1, {x: 0, y: 1, ease: Expo.easeOut});
  			TweenMax.to([eyeL, eyeR], 1, {scaleX: .85, scaleY: .85, ease: Expo.easeOut});
  			eyeScale = .85;
  		}
  		if(value.includes("@")) {
  			mouthStatus = "large";
  			TweenMax.to([mouthBG, mouthOutline, mouthMaskPath], 1, {morphSVG: mouthLargeBG, ease: Expo.easeOut});
  			TweenMax.to(tooth, 1, {x: 3, y: -2, ease: Expo.easeOut});
  			TweenMax.to(tongue, 1, {y: 2, ease: Expo.easeOut});
  			TweenMax.to([eyeL, eyeR], 1, {scaleX: .65, scaleY: .65, ease: Expo.easeOut, transformOrigin: "center center"});
  			eyeScale = .65;
  		} else {
  			mouthStatus = "medium";
  			TweenMax.to([mouthBG, mouthOutline, mouthMaskPath], 1, {morphSVG: mouthMediumBG, ease: Expo.easeOut});
  			TweenMax.to(tooth, 1, {x: 0, y: 0, ease: Expo.easeOut});
  			TweenMax.to(tongue, 1, {x: 0, y: 1, ease: Expo.easeOut});
  			TweenMax.to([eyeL, eyeR], 1, {scaleX: .85, scaleY: .85, ease: Expo.easeOut});
  			eyeScale = .85;
  		}
  	} else {
  		mouthStatus = "small";
  		TweenMax.to([mouthBG, mouthOutline, mouthMaskPath], 1, {morphSVG: mouthSmallBG, shapeIndex: 9, ease: Expo.easeOut});
  		TweenMax.to(tooth, 1, {x: 0, y: 0, ease: Expo.easeOut});
  		TweenMax.to(tongue, 1, {y: 0, ease: Expo.easeOut});
  		TweenMax.to([eyeL, eyeR], 1, {scaleX: 1, scaleY: 1, ease: Expo.easeOut});
  		eyeScale = 1;
  	}
  }

  function onLoginPressEmailFocus(e) {
  	activeElement = "email";
  	e.target.parentElement.classList.add("focusWithText");
  	//stopBlinkingLoginPress();
  	//calculateFaceMoveLoginPress();
  	onLoginPressEmailInput();
  }

  function onLoginPressEmailBlur(e) {
  	activeElement = null;
  	setTimeout(function() {
  		if(activeElement == "email") {
  		} else {
  			if(e.target.value == "") {
  				e.target.parentElement.classList.remove("focusWithText");
  			}
  			//startBlinkingLoginPress();
  			resetFaceLoginPress();
  		}
  	}, 100);
  }

  function onLoginPressEmailLabelClick(e) {
  	activeElement = "email";
  }

  function onLoginPressPasswordFocus(e) {
  	activeElement = "password";
  	if(!eyesCovered) {
  		coverEyesLoginPress();
  	}
  }

  function onLoginPressPasswordBlur(e) {
  	activeElement = null;
  	setTimeout(function() {
  		if(activeElement == "toggle" || activeElement == "password") {
  		} else {
  			uncoverEyesLoginPress();
  		}
  	}, 100);
  }

  function onLoginPressPasswordToggleFocus(e) {
  	activeElement = "toggle";
  	if(!eyesCovered) {
  		coverEyesLoginPress();
  	}
  }

  function onLoginPressPasswordToggleBlur(e) {
  	activeElement = null;
  	if(!showPasswordClicked) {
  		setTimeout(function() {
  			if(activeElement == "password" || activeElement == "toggle") {
  			} else {
  				uncoverEyesLoginPress();
  			}
  		}, 100);
  	}
  }

  function onLoginPressPasswordToggleMouseDown(e) {
  	showPasswordClicked = true;
  }

  function onLoginPressPasswordToggleMouseUp(e) {
  	showPasswordClicked = false;
  }

  function onLoginPressPasswordToggleChange(e) {
  	setTimeout(function() {
  		// if checkbox is checked, show password
  		if(e.target.checked) {
  			password.type = "text";
  			spreadFingersLoginPress();

  		// if checkbox is off, hide password
  		} else {
  			password.type = "password";
  			closeFingersLoginPress();
  		}
  	}, 100);
  }

  function onLoginPressPasswordToggleClick(e) {
  	//console.log("click: " + e.target.id);
  	e.target.focus();
  }

  function spreadFingersLoginPress() {
  	TweenMax.to(twoFingers, .35, {transformOrigin: "bottom left", rotation: 30, x: -9, y: -2, ease: Power2.easeInOut});
  }

  function closeFingersLoginPress() {
  	TweenMax.to(twoFingers, .35, {transformOrigin: "bottom left", rotation: 0, x: 0, y: 0, ease: Power2.easeInOut});
  }

  function coverEyesLoginPress() {
  	TweenMax.killTweensOf([armL, armR]);
  	TweenMax.set([armL, armR], {visibility: "visible"});
  	TweenMax.to(armL, .45, {x: -93, y: 10, rotation: 0, ease: Quad.easeOut});
  	TweenMax.to(armR, .45, {x: -93, y: 10, rotation: 0, ease: Quad.easeOut, delay: .1});
  	TweenMax.to(bodyBG, .45, {morphSVG: bodyBGchanged, ease: Quad.easeOut});
  	eyesCovered = true;
  }

  function uncoverEyesLoginPress() {
  	TweenMax.killTweensOf([armL, armR]);
  	TweenMax.to(armL, 1.35, {y: 220, ease: Quad.easeOut});
  	TweenMax.to(armL, 1.35, {rotation: 105, ease: Quad.easeOut, delay: .1});
  	TweenMax.to(armR, 1.35, {y: 220, ease: Quad.easeOut});
  	TweenMax.to(armR, 1.35, {rotation: -105, ease: Quad.easeOut, delay: .1, onComplete: function() {
  		TweenMax.set([armL, armR], {visibility: "hidden"});
  	}});
  	TweenMax.to(bodyBG, .45, {morphSVG: bodyBG, ease: Quad.easeOut});
  	eyesCovered = false;
  }

  function resetFaceLoginPress() {
  	TweenMax.to([eyeL, eyeR], 1, {x: 0, y: 0, ease: Expo.easeOut});
  	TweenMax.to(nose, 1, {x: 0, y: 0, scaleX: 1, scaleY: 1, ease: Expo.easeOut});
  	TweenMax.to(mouth, 1, {x: 0, y: 0, rotation: 0, ease: Expo.easeOut});
  	TweenMax.to(chin, 1, {x: 0, y: 0, scaleY: 1, ease: Expo.easeOut});
  	TweenMax.to([face, eyebrow], 1, {x: 0, y: 0, skewX: 0, ease: Expo.easeOut});
  	TweenMax.to([outerEarL, outerEarR, earHairL, earHairR, hair], 1, {x: 0, y: 0, scaleY: 1, ease: Expo.easeOut});
  }

  function startBlinkingLoginPress(delay) {
  	if(delay) {
  		delay = getRandomIntLoginPress(delay);
  	} else {
  		delay = 1;
  	}
  	blinking = TweenMax.to([eyeL, eyeR], .1, {delay: delay, scaleY: 0, yoyo: true, repeat: 1, transformOrigin: "center center", onComplete: function() {
  		startBlinkingLoginPress(12);
  	}});
  }

  function stopBlinkingLoginPress() {
  	blinking.kill();
  	blinking = null;
  	TweenMax.set([eyeL, eyeR], {scaleY: eyeScale});
  }

  function getRandomIntLoginPress(max) {
  	return Math.floor(Math.random() * Math.floor(max));
  }

  function getAngleLoginPress(x1, y1, x2, y2) {
  	var angle = Math.atan2(y1 - y2, x1 - x2);
  	return angle;
  }

  function getPositionLoginPress(el) {
  	var xPos = 0;
  	var yPos = 0;

  	while (el) {
  		if (el.tagName == "BODY") {
  			// deal with browser quirks with body/window/document and page scroll
  			var xScroll = el.scrollLeft || document.documentElement.scrollLeft;
  			var yScroll = el.scrollTop || document.documentElement.scrollTop;

  			xPos += (el.offsetLeft - xScroll + el.clientLeft);
  			yPos += (el.offsetTop - yScroll + el.clientTop);
  		} else {
  			// for all other non-BODY elements
  			xPos += (el.offsetLeft - el.scrollLeft + el.clientLeft);
  			yPos += (el.offsetTop - el.scrollTop + el.clientTop);
  		}

  		el = el.offsetParent;
  	}
  	//console.log("xPos: " + xPos + ", yPos: " + yPos);
  	return {
  		x: xPos,
  		y: yPos
  	};
  }

  function isMobileDeviceLoginPress() {
  	var check = false;
  	(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
  	return check;
  };

  function initLoginPressForm() {
  	// some measurements for the svg's elements
  	svgCoords = getPositionLoginPress(mySVG);
  	emailCoords = getPositionLoginPress(email);
  	screenCenter = svgCoords.x + (mySVG.offsetWidth / 2);
  	eyeLCoords = {x: svgCoords.x + 84, y: svgCoords.y + 76};
  	eyeRCoords = {x: svgCoords.x + 113, y: svgCoords.y + 76};
  	noseCoords = {x: svgCoords.x + 97, y: svgCoords.y + 81};
  	mouthCoords = {x: svgCoords.x + 100, y: svgCoords.y + 100};

  	// handle events for email input
  	email.addEventListener( 'focus', onLoginPressEmailFocus );
  	email.addEventListener( 'blur', onLoginPressEmailBlur );
  	email.addEventListener( 'input', onLoginPressEmailInput );
  	emailLabel.addEventListener( 'click', onLoginPressEmailLabelClick );
	//   reg_password = document.querySelector('#loginpress-reg-pass'),
	//   repeat_password = document.querySelector('#lloginpress-reg-pass-2'),
	//   user_email = document.querySelector('[name="user_email"]'),
	  if(user_email){
		user_email.addEventListener( 'focus', onLoginPressEmailFocus );
		user_email.addEventListener( 'blur', onLoginPressEmailBlur );
		user_email.addEventListener( 'input', onLoginPressEmailInput );
	  }
  	// handle events for password input
	  if(password){

		password.addEventListener( 'blur', onLoginPressPasswordBlur );
  	password.addEventListener( 'focus', onLoginPressPasswordFocus )
	  };
	  if(repeat_password){

repeat_password.addEventListener( 'blur', onLoginPressPasswordBlur );
repeat_password.addEventListener( 'focus', onLoginPressPasswordFocus )
};
	  if(reg_password){

reg_password.addEventListener( 'blur', onLoginPressPasswordBlur );
reg_password.addEventListener( 'focus', onLoginPressPasswordFocus )
};
  	//passwordLabel.addEventListener( 'click', onLoginPressPasswordLabelClick );

  	// handle events for password checkbox
	  if(showPasswordCheck){
  	showPasswordCheck.addEventListener( 'change', onLoginPressPasswordToggleChange );
  	showPasswordCheck.addEventListener( 'focus', onLoginPressPasswordToggleFocus );
  	showPasswordCheck.addEventListener( 'blur', onLoginPressPasswordToggleBlur );
  	showPasswordCheck.addEventListener( 'click', onLoginPressPasswordToggleClick );
  	showPasswordToggle.addEventListener( 'mouseup', onLoginPressPasswordToggleMouseUp );
  	showPasswordToggle.addEventListener( 'mousedown', onLoginPressPasswordToggleMouseDown );
	  }

  	// move arms to initial positions
  	TweenMax.set( armL, {x: -93, y: 220, rotation: 105, transformOrigin: "top left"} );
  	TweenMax.set( armR, {x: -93, y: 220, rotation: -105, transformOrigin: "top right"} );

  	// set initial mouth property (fixes positioning bug)
  	TweenMax.set( mouth, {transformOrigin: "center center"} );

  	// activate blinking
  	startBlinkingLoginPress(5);

  	// determine how far email input can go before scrolling occurs
  	// will be used as the furthest point avatar will look to the right
  	emailScrollMax = email.scrollWidth;

  	// check if we're on mobile/tablet, if so then show password initially
  	if(isMobileDeviceLoginPress()) {
  		password.type = "text";
  		showPasswordCheck.checked = true;
  		TweenMax.set(twoFingers, {transformOrigin: "bottom left", rotation: 30, x: -9, y: -2, ease: Power2.easeInOut});
  	}

  	// clear the console
  	console.clear();
  }

  initLoginPressForm();
  }, false );
  </script>
<?php endif; ?>
