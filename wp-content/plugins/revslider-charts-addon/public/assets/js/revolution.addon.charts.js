/**
 * @preserve
 * @name Slider Revolution Charts AddOn
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
 * @version 1.0.1
 */

(function () {

	"use strict";

	// CHECK IF WE SCRIPT RUNNING IN EDITOR OR IN FRONTEND
	if (window._R_is_Editor) RVS._R = RVS._R===undefined ? {} : RVS._R; else window._R_is_Editor=false;
	jQuery.fn.revolution = jQuery.fn.revolution || {};
	var  _R = window._R_is_Editor ? RVS._R : jQuery.fn.revolution,		
		_TRANS_ = {rad:'radius',mt:'marginTop', mb:'marginBottom', ml:'marginLeft', mr:'marginRight', st:'st', dp:'dp', sbg:'sbg',n:'name',f:'font',c:'color',s:'size',v:'v',h:'h',xo:'xo',yo:'yo',fw:'fontWeight',a:'align',g:'gap',bg:'bg',su:'suf',pr:'pre',pl:'pl',ph:'paddingh',pv:'paddingv',dir:'direction',dz:'dez',ro:'ro',ev:'every',xu:'xuse',xc:'xcolor',xs:'xsize',xstc:'xstcolor',xsts:'xstsize',yu:'yuse',yc:'ycolor',ys:'ysize',yd:'ydivide',xbtc:'ybtcolor',ybts:'ybtsize',uv:'usevals',uxv:'usexval',tc:'textcolor',fi:'fill',dsh:'dash',dh:'dphidden',ds:'dpscale',ty:'type',wi:'width',he:'height',ix:'isx',ppl:'pl',ppr:'pr',sp:'speed',dl:'delay',fr:'fr'},
		_TRBNS_ = {anchorcolor : 'chartsAc', curves : 'chartsCvs',datapoint : 'chartsDp',index : 'chartsIndex',strokecolor : 'chartsSc',strokedash : 'chartsSd',strokewidth : 'chartsSw',valuebgcols : 'chartsVbg',valuefcolor : 'chartsVfc',valuecolor : 'chartsVc',fillcolor : 'chartsFc', data:'chartsData',altcolors:'chartsAlc', altcolorsuse:'chartsAlcu'};


///////////////////////////////////////////
// 	EXTENDED FUNCTIONS AVAILABLE GLOBAL  //
///////////////////////////////////////////
jQuery.extend(true,_R, {
	chartsInit : function(id) {
		_R[id].chartStaticAdded = false;
		// LISTEN TO INITIALISED LAYERS TO DOUBLE CHECK THEM
		_R[id].c.on('revolution.slideprepared',function(e,_) {					
			prepareLayers(id,_.key);
			if (_R[id].chartStaticAdded == false) {				
				prepareLayers(id,'static');
				_R[id].chartStaticAdded=true;
			}
		});	
		listenToLayerChanges(id);
	},	
	/*
	DRAW AND UPDATE CHARTS
	 */
	chartsBuild : {
		getBasics : function() {
			return  {							
						labels:{	
							font:'Arial',							
							x:{use:true,name:"",color:'#fff',size:12,v:"bottom",h:"center", xo:0,yo:10,fontWeight:'500'},
							y:{use:true,name:"",color:'#fff',size:12,v:"center",h:"left", xo:10,yo:0, fontWeight:'500'}					
						},

						legend:{ use:true, color:'#fff', size:12,v:"center",h:"left", xo:10,yo:0,align:"horizontal",gap:10,font:'Arial', fontWeight:'500',bg:'transparent',dp:true, st:true, sbg:true},

						values:{	
							font:'Arial',
							s:{pre:"",suf:"",dez:2,size:10,xo:0,yo:0,direction:"start", fontWeight:'500', paddingh:3, paddingv:5,fr:true, radius:4},							
							f:{pre:"",suf:"",dez:2,use:false,size:10,xo:0,yo:0,fontWeight:'500', fr:true},
							x:{pre:"",suf:"",dez:2,use:true,color:'#fff',size:10,v:"center",h:"left", xo:0,yo:5, ro:0, fontWeight:'500',every:3,fr:false},
							y:{pre:"",suf:"",dez:2,use:true,color:'#fff',size:13,v:"bottom",h:"center", xo:0, yo:6, fontWeight:'500',fr:true}
						},

						grid:{	xuse:true,xcolor:'rgba(255,255,255,1)',xsize:1,
								xstcolor:'rgba(255,255,255,1)',xstsize:1,
								yuse:true,ycolor:'rgba(255,255,255,0.75)',ysize:1, ydivide:6,
								ybtcolor:'rgba(255,255,255,1)',ybtsize:1
						},
						interaction:{					
							v:{use:true,usevals:true,usexval:true,color:'rgba(255,255,255,0.75)', size:1,dash:"0",fill:'#000',textcolor:'#fff',yo:15,xo:0,dphidden:false,dpscale:true}
						},
						
						settings: {
							type:"line",
							gap:5,																							
							width:800,
							height:500,
							isx:0,
							pl:0,
							pr:0,
							keepselected:true,
							keepstyle:true,		
							speed:2000,
							delay:0,			
							margin:{top:20, bottom:50, left:50, right:0}							
						},
						inuse:[],
						index:[],
						strokewidth:[],
						strokedash:[],
						curves:[],
						datapoint:[],
						strokecolor:[],
						anchorcolor:[],
						fillcolor:[],
						valuebgcols:[],
						valuecolor:[],
						valuefcolor:[],
						altcolors:[],
						altcolorsuse:[]					
					}
		},
		

		getTitle : function(_,l) {
			l = l===undefined ? 10 : l;
			 var r = _===undefined ? bricks.sets : _.length>l ? _.substring(0,l)+"..." :  _;
			 if (r[0]==" ") r=r.substring(1,r.length);
			 if (r[r.lenght-1]=='"') r.slice(0,-1);
			 return r;
		},
		getPos : function(p,o,g,fs) {					
				return  p ==="left" ? o : p ==="top" ? parseInt(o)+parseInt(fs) : p ==="right" || p==="bottom" ? parseInt(g) - parseInt(o) : g/2 + parseInt(o);				
		},
		updateColOder : function(_) {
			_.cOrder = [];
			var i=0,col;
			for (col=0;col<_.data[0].length;col++) {
				if (col==_.settings.isx || (window._R_is_Editor && _.inuse[col]!==true)) continue;
				_.cOrder.push([parseInt(_.index[col]),col]);
			}
			_.cOrder.sort(function (a, b) {if (a[0] === b[0]) return 0;else return (a[0] < b[0]) ? -1 : 1;});
		},			
		maxval : function(_) {				
			var max = -9999999,
				min = 0;
			for (var row in _.data) {					
				for (var col in _.data[row]) {
					if (col==_.settings.isx || (window._R_is_Editor && _.inuse[col]!==true)) continue;
					var a = parseFloat(_.data[row][col]);							
					if (""+a!=="NaN") {
						max = Math.max(a,max);
						min = Math.min(a,min);
					}						
				}
			}			
			min = Math.floor(min + (min*0.1));
			max = Math.ceil(max + (max*0.1)); 
			Math.round(max+min)

			return {min:min, max:max}
		},
		line : function(pointA, pointB) {
		  const lengthX = pointB[0] - pointA[0],
		  		lengthY = pointB[1] - pointA[1];
		  return {
		    length: Math.sqrt(Math.pow(lengthX, 2) + Math.pow(lengthY, 2)),
		    angle: Math.atan2(lengthY, lengthX)
		  }
		},

		controlPoint : function(current, previous, next, reverse,smooth) {
		  const p = previous || current,
		  		n = next || current,
		  		o = _R.chartsBuild.line(p, n),
		  		angle = o.angle + (reverse ? Math.PI : 0),
		  		length = o.length * smooth,
		  		x = current[0] + Math.cos(angle) * length,
		  		y = current[1] + Math.sin(angle) * length;
		  return [x, y]
		},

		bezier : function(point, i, a,smooth) {
		  const cps = _R.chartsBuild.controlPoint(a[i - 1], a[i - 2], point,undefined,smooth),
		  		cpe = _R.chartsBuild.controlPoint(point, a[i - 1], a[i + 1], true,smooth);		  
		  return 'C '+cps[0]+','+cps[1]+' '+cpe[0]+','+cpe[1]+' '+point[0]+','+point[1];
		},

		svgPath : function(points,smooth) {
		  return points.reduce((acc, point, i, a) => i === 0 ? 'M '+point[0]+','+point[1] : acc+' '+ _R.chartsBuild.bezier(point, i, a,smooth), '');			  
		},

		dataPoint : function(t,tx,ty,col,op) {
			var n = t=="1" || t=="2" ? "circle" : t=="3" ? "polygon" : "rect",
				v = n=="circle" ? {cx:tx, cy:ty, r:t=="1" ? 2 : 3} : n=="polygon" ? {points:(tx-3)+','+(ty+3)+','+(tx+3)+','+(ty+3)+','+(tx)+','+(ty-3)} : {width:5, height:5, x:(tx-2), y:(ty-2)};
			return _R.cNS({n:n, c:"chart_anchor", v:v, s:{transformOrigin: (n==="rect" || t=="1" ? "2px 2px" :  "3px 3px" ),fill:col, opacity:op}});
		},	

		updateLegend : function(wrap,a,_,w,h) {			
			//legend:{ use:true, color:'#fff', size:12,v:"center",h:"left", xo:10,yo:0,align:"horizontal",gap:10,font:'Arial', fontWeight:'500'},
			var fb = [a.lenght],					
				x = 0,
				y = 0,
				mw=0,mh=0,
				_fbh = 0,
				iBG = 1,
				iST = 2,
				iDT = 3;
			if(_.sbg!=true) {iST--;iDT--;}
			if (_.st!==true) {iST = -1;iDT--;}
			if (_.dp!==true) iDT=-1;
			// BUILD INNER CONTENT POSITIONS
			for (var i =0;i<a.length;i++) {
				fb[i] = a[i].childNodes[0].getBBox();
				tpGS.gsap.set(a[i].childNodes[0],{attr:{x:(40 + fb[i].width/2),y:fb[i].height/2}});
				if (iST!==-1) tpGS.gsap.set(a[i].childNodes[iST],{attr:{y1:fb[i].height/4,y2:fb[i].height/4}});
				if (iDT!==-1) tpGS.gsap.set(a[i].childNodes[iDT],{x:15,y:fb[i].height/4});
				tpGS.gsap.set(a[i],{x:x,y:y});
																					
				if (_.align==="horizontal") {						
					mw += (25 + fb[i].width + (i<a.length-1 ? parseInt(_.gap) : 0));
					mh = mh<=fb[i].height ? fb[i].height : mh;
					x += (25 + fb[i].width + parseInt(_.gap));
					_fbh = _fbh<(fb[i].height ) ? (fb[i].height ) : _fbh;
				}
				else {
					mh += (fb[i].height + (i<a.length-1 ? parseInt(_.gap) : 0));
					mw = mw<= (25 + fb[i].width) ? (40+fb[i].width) : mw;
					y += (fb[i].height + parseInt(_.gap));	
					_fbh = _fbh<(fb[i].height ) ? (fb[i].height ) : _fbh;
				}
			}
			mw = mw+30;
			mh = mh+30;				
			tpGS.gsap.set(wrap.childNodes[0],{width:mw, height:mh, y:-(15+_fbh/4)});
			// SET FULL CONTENT POSITION
			var p = wrap.getBBox();
			tpGS.gsap.set(wrap,{x: _.h == 'left' ? parseInt(_.xo) : _.h==='right' ? -parseInt(_.xo) + (w-p.width) : parseInt(_.xo) + (w/2 - p.width/2),
								y: _.v == 'top' ? parseInt(_.yo) : _.v==='bottom' ? -parseInt(_.yo) + (h-p.height) : parseInt(_.yo) + (h/2 - p.height/2)});
		},

		diagramm : function(id,_,LAYER) {
						
			// CACHE or RECACHE ELEMENTS				
			_R.chartsCache = _R.chartsCache===undefined ? {} : _R.chartsCache;

			//Fix some Data
			_.grid.ydivide = _.grid.ydivide===undefined ? 5 : _.grid.ydivide;
			_.settings.gap = _.settings.gap===undefined ? 5 : _.settings.gap;

			var i,x,y,val,row,col,F,T,A,v,n,nv,tx,ty,tp=Math.pow(10,_.values.s.dez),
				CC,YPa, Ba,Da,Dw,LL, ba,
				rdl = _.data.length - 1,
				w = parseInt(_.settings.width) || 800,
				h = parseInt(_.settings.height) || 500,						
				R = {
					box : {l:parseInt(_.settings.margin.left), r: w-parseInt(_.settings.margin.right), t:parseInt(_.settings.margin.top), b:h - parseInt(_.settings.margin.bottom), pl:parseInt(_.settings.pl), pr:parseInt(_.settings.pr)},
					xp : [],yp : [],vals : [],text : [],antexts : [],dots : [],paths : [],filledpaths : [],	charts : [],legends :[],bars:[], fv:[],
					valpadding:{h:(parseInt(_.values.s.paddingh) || 0), v:(parseInt(_.values.s.paddingv) || 0)},
					cOrder : _.cOrder,
					speed : parseInt(_.settings.speed)/1000 || 2,
					dpscale : _.interaction.v.dpscale,
					dphidden : _.interaction.v.dphidden,
				};		
			
			R.box.h = (R.box.b-R.box.t);
			R.box.w = (R.box.r-R.box.l) - ( R.box.pl+R.box.pr);
			
			_.minmax = _R.chartsBuild.maxval(_);
			
			
			var HS = R.box.h/_.grid.ydivide,
				VS = Math.round((_.minmax.max - _.minmax.min) / _.grid.ydivide),			
				XS = R.box.w / (rdl-(_.settings.type==="line" ? 1 : 0)); 
			
			R.box.pl = R.box.pl + (_.settings.type!=="line" ? XS/2 : 0);

			_R.chartsBuild.updateColOder(_);


						
			// BUILD SVG
			R.svg = _R.cNS({n:'svg',id:'chart_'+id, s:{width:"100%", height:"100%"}});				
			R.svg.setAttribute('viewBox', ('0 0 '+w+' '+h));					

			// DRAW HORIZONTAL LINES AND Y LABELS 
			if (_.grid.yuse || _.values.y.use) {
				R.hgroup = _R.cNS({n:'g',c:"chart_y_axis", v:(_.grid.yuse ? {stroke:_.grid.ycolor, strokeWidth:_.grid.ysize} : {})});
				if (_.grid.yuse) R.hlines = [];					
									
				for (i=0;i<=_.grid.ydivide;i++) {
					y = Math.max(Math.floor(R.box.b - (HS*i)),R.box.t);
					val = _.minmax.min + (VS*i);	

					if (_.grid.yuse) {
						R.hlines.push(T=_R.cNS({n:'line',v:{x1:(R.box.l-3), y1:y, x2:(R.box.r),y2:y},c:"charts_gird_h_lines"}));
						R.hgroup.appendChild(T);
					}
					if (_.values.y.use) {						
						R.text.push(T = _R.cNS({n:'text',v:{ x:((R.box.l-10)+parseInt(_.values.y.xo)), y:(y+parseInt(_.values.y.yo)), strokeWidth:0},t:_.values.y.pre+(_.values.y.fr ? Intl.NumberFormat().format(val) : val)+_.values.y.suf,c:"chart_text charts_y_vals",s:{fill:_.values.y.color,fontFamily:_.values.font,fontWeight:_.values.y.fontWeight,fontSize:parseInt(_.values.y.size)+"px",textAnchor:'end'}}));
						R.hgroup.appendChild(T);
					}
				}	
				R.svg.appendChild(R.hgroup);	// ADDING HORIZONTAL GROUPS									
			}

			// CREATE LABELS
			if (_.labels.x.use) {
				R.labelX = _R.cNS({n:'g',c:"chart_label_x_wrap", s:{transform:'translate('+_R.chartsBuild.getPos(_.labels.x.h, _.labels.x.xo, w)+'px, '+_R.chartsBuild.getPos(_.labels.x.v, _.labels.x.yo, h,parseInt(_.labels.x.size))+'px)'}});					
				R.text.push(T = _R.cNS({n:'text',t:_.labels.x.name, c:"chart_text chart_label_x",s:{fill:_.labels.x.color,fontFamily:_.labels.font, fontWeight:_.labels.x.fontWeight, fontSize:parseInt(_.labels.x.size)+"px",textAnchor:'middle'}}));
				R.labelX.appendChild(T);
				R.svg.appendChild(R.labelX);
			}
			if (_.labels.y.use) {
				R.labelY = _R.cNS({n:'g',c:"chart_label_y_wrap", s:{transform:'translate('+_R.chartsBuild.getPos(_.labels.y.h, _.labels.y.xo, w)+'px,'+_R.chartsBuild.getPos(_.labels.y.v, _.labels.y.yo, h,parseInt(_.labels.y.size))+'px) rotateZ(-90deg)'}});					
				R.text.push(T=_R.cNS({n:'text',t:_.labels.y.name,c:"chart_text chart_label_y",s:{fill:_.labels.y.color,fontFamily:_.labels.font, fontWeight:_.labels.y.fontWeight, fontSize:parseInt(_.labels.y.size)+"px",textAnchor:'middle'}}));
				R.labelY.appendChild(T);
				R.svg.appendChild(R.labelY);
			}

			
			R.legend = _R.cNS({n:'g', c:"chart_legend_wrap"});				
			R.legend.appendChild(_R.cNS({n:'rect', v:{x:0, y:0},s:{transform:'translate(-10px,-10px)',fill:_R.getSVGGradient(_.legend.bg)}}))			
			

			var XSG = -(XS/2 - (_.settings.gap/4)),
				XSW = Math.max(1,(XS - (_.settings.gap/2)) / (_.settings.type==="pbar" ? _.cOrder.length : 1)),
				XSSS,
				fv;
				
			// DRAW CHARTS
			for (var coli=0;coli<_.cOrder.length;coli++) {					
				col = _.cOrder[coli][1];
				R.charts.push(CC = _R.cNS({n:'g',id:'chart_'+id+'_column_'+col, c:"chart_column"}));
				Dw = _R.cNS({n:'g', c:"chart_data_points"});

				//ADD ANCHOR TEXT FIELDS
				if (_.interaction.v.usevals) { //
					R.antexts.push(T = _R.cNS({n:'g',id:'chart_value_'+col, c:"charts_values"}));	// CREATE GROUP
					T.appendChild(_R.cNS({n:'rect', v:{rx:(parseInt(_.values.s.radius) || 0), rx:(parseInt(_.values.s.radius) || 0),x:-parseInt(_.values.s.paddingh,0), y:-parseInt(_.values.s.paddingv,0)},s:{fill:_.valuebgcols[col]}})); // CREATE RECTANGLE BEHIND TEXT AND APPEND IT TO GROUP
					T.appendChild(_R.cNS({n:'text', v:{x:_.values.s.xo, y:_.values.s.yo},s:{fontWeight:_.values.s.fontWeight, fontFamily:_.values.font, fill:_.valuecolor[col], fontSize:parseInt(_.values.s.size)+"px", textAnchor:_.values.s.direction}}));						
					R.vals.push([]);
				}


				//ADD LEGEND FIELDS					
				if (_.legend.use) {
					R.legends.push(LL = _R.cNS({n:'g', c:"chart_legend"}));
					LL.appendChild(_R.cNS({n:'text', v:{x:0, y:0}, t:_R.chartsBuild.getTitle(_.data[0][col],40), s:{fontWeight:_.legend.fontWeight, fontFamily:_.legend.font, fill:_.legend.color, fontSize:parseInt(_.legend.size)+"px", textAnchor:"middle"}}));					
					if (_.legend.sbg===true) LL.appendChild(_R.cNS({n:'rect', v:{x:30,y:1,width:5,height:5, stroke:'transparent', fill:_R.getSVGGradient(_.fillcolor[col])}}));
					if (_.legend.st===true) LL.appendChild(_R.cNS({n:'line', v:{x1:5,x2:25,y1:-2,y2:-2, stroke:_.strokecolor[col], strokeDasharray:_.strokedash[col], strokeWidth:_.strokewidth[col]}}));
					if (_.legend.dp===true && _.datapoint[col]!==0) LL.appendChild(_R.chartsBuild.dataPoint(_.datapoint[col], 0,0,_.anchorcolor[col],1));
					
					R.text.push(LL);
					R.legend.appendChild(LL);
				}


				//COLLECT HELPERS
				R.yp.push(YPa = []);					
				R.dots.push(Da = []);
				if (_.settings.type!=="line") R.bars.push(Ba=[]);
									
				A = [];
				var FILL="",
					mapper = tpGS.gsap.utils.mapRange(_.minmax.min, _.minmax.max,0, R.box.h);

				for (row=0;row<rdl;row++) {
					nv = parseFloat(_.data[row+1][col]);
					if (""+nv==="NaN") continue;									
					nv = Math.round(nv*tp)/tp;
					if (_.interaction.v.usevals) R.vals[R.vals.length-1].push(_.values.s.pre+(_.values.s.fr ? Intl.NumberFormat().format(nv) : nv)+_.values.s.suf);						
					tx = R.box.pl + Math.round(R.box.l + (XS * row));
					ty = Math.round(R.box.b-mapper(nv));
					YPa.push(ty);
					if (_.settings.type!=="line") { // DRAW THE BARS	
						FILL = _R.getSVGGradient(((window._R_is_Editor && _.altcolorsuse[col]) ||  (!window._R_is_Editor && _.altcolors!==undefined && _.altcolors[col]!==undefined && _.altcolors[col][row]!==undefined)) ? _.altcolors[col][row] : _.fillcolor[col]);
						FILL = FILL==="transparent" ? '#245689' : FILL;
						
						Ba.push(ba = _R.cNS({n:'rect', v:{x:tx + XSG + (_.settings.type==="pbar" ? XSW * coli : 0), y:ty, width:XSW ,height:(R.box.b-ty), 
														//stroke:_.strokecolor[col], strokeDasharray:_.strokedash[col], strokeWidth:_.strokewidth[col]
														stroke:'transparent', strokeWidth:0
														}, 
														s:{fill: FILL}}));
						CC.appendChild(ba);						
					} else 
						if (_.datapoint[col]!=="0") Da.push(_R.chartsBuild.dataPoint(_.datapoint[col], tx,ty,_.anchorcolor[col],_.interaction.v.dphidden ? 0 : 1));						
					
					//ADD FIXED TEXT FIELDS
					if (_.values.f!==undefined && _.values.f.use) { //												
						R.fv.push(fv = _R.cNS({c:"charts_fixed_values", n:'text', t:(_.values.f.pre+(_.values.f.fr ? Intl.NumberFormat().format(nv) : nv)+_.values.f.suf), v:{
									x:_.settings.type!=="line" ? parseInt(_.values.f.xo) + tx + XSG + (_.settings.type==="pbar" ? XSW * coli : 0) + XSW/2 : tx, 
									y:_.settings.type!=="line" ? (parseInt(_.values.f.yo) + ty) : ty
								},
									s:{fontWeight:_.values.f.fontWeight, fontFamily:_.values.font, fill:_.valuefcolor!==undefined ? _.valuefcolor[col] : _.valuecolor[col], fontSize:parseInt(_.values.f.size)+"px", textAnchor:"middle"}}));												
						CC.appendChild(fv);
					}

					
					A.push([tx,ty]);										
				}
				if (_.settings.type==="line") { // OR DRAW THE PATHS
					F = _R.chartsBuild.svgPath(A,(_.curves[col])/10);
					R.paths.push(T = _R.cNS({n:'path',c:"charts_paths", id:'path_'+id+'_col',v:{d:F, stroke:_.strokecolor[col], strokeDasharray:_.strokedash[col], strokeWidth:_.strokewidth[col]}, s:{fill:'transparent'}}));
					CC.appendChild(T);							
					R.filledpaths.push(T = _R.cNS({n:'path',c:"charts_filledpaths", id:'filledpath_'+id+'_col',s:{fill:_R.getSVGGradient(_.fillcolor[col])},v:{d:'M '+R.box.l+' '+R.box.b+ F.replace('M','L') + ' L'+R.box.r+' '+R.box.b, stroke:"transparent", strokeWidth:_.strokewidth[col]}}));				
					CC.appendChild(T);
					for (i=0;i<Da.length;i++) Dw.appendChild(Da[i]);
					CC.appendChild(Dw);
				}
				R.svg.appendChild(CC);
			}
			

			// DRAW VERTICAL LINES AND X LABELS
			if (_.grid.xuse || _.values.x.use || (_.interaction.v.use && _.interaction.v.usexval)) {					
				R.vgroup = _R.cNS({n:'g',c:"chart_x_axis", v:(_.grid.xuse ? {stroke:_.grid.xcolor, strokeWidth:_.grid.xsize} : {})}); 
				if (_.grid.xuse) R.vlines = [];									
				if (_.interaction.v.use && _.interaction.v.usexval) R.vals.push([]);			
				for (i=0;i<=rdl;i++) {
					if (_.data[i+1]===undefined) continue;
					x =  R.box.pl + Math.round(R.box.l + XS*i);						
					val = _.data[i+1][_.settings.isx];
					if (_.interaction.v.use && _.interaction.v.usexval) R.vals[R.vals.length-1].push(_.values.x.pre+(_.values.x.fr ? Intl.NumberFormat().format(val) : val)+_.values.x.suf);
					R.xp.push(x);
					if (_.grid.xuse) { 
						if (i==0) {
							R.vlines.push(T = _R.cNS({n:'line',v:{stroke:_.grid.xstcolor, strokeWidth:_.grid.xstsize,x1:(x-R.box.pl), y1:(R.box.b + 3), x2:(x-R.box.pl), y2:R.box.t-3}, c:"charts_gird_v_lines"}));  
							R.vgroup.appendChild(T);
						}
						R.vlines.push(T = _R.cNS({n:'line',v:{x1:x, y1:(R.box.b + 3), x2:x, y2: R.box.b-3}, c:"charts_gird_v_lines"}));
						R.vgroup.appendChild(T);
					}
					if (_.values.x.use && (i%_.values.x.every==0)) {
						T = _R.cNS({n:'g',s:{transform:'translate('+(x+parseInt(_.values.x.xo))+'px, '+((R.box.b+10)+parseInt(_.values.x.yo))+'px)'}});
						R.text.push(F=_R.cNS({n:'text',t:(_.values.x.pre+(_.values.x.fr ? Intl.NumberFormat().format(val) : val)+_.values.x.suf),s:{transform:'rotate('+_.values.x.ro+'deg)', fontFamily:_.values.font, fontWeight:_.values.x.fontWeight, fontSize:parseInt(_.values.x.size)+"px", textAnchor:"middle"},v:{strokeWidth:0,fill:_R.getSVGGradient(_.values.x.color)}}));
						T.appendChild(F);
						R.vgroup.appendChild(T);
					}
				}
				R.svg.appendChild(R.vgroup);					
			}
			
			if (_.interaction.v.use && _.interaction.v.usexval) {					
				R.antexts.push(T = _R.cNS({n:'g',id:'chart_value_marker', c:"charts_values"}));	// CREATE GROUP				
				T.appendChild(_R.cNS({n:'rect', v:{rx:(parseInt(_.values.s.radius) || 0), rx:(parseInt(_.values.s.radius) || 0), x:-parseInt(_.values.s.paddingh), y:-parseInt(_.values.s.paddingv)}, s:{fill:_R.getSVGGradient(_.interaction.v.fill)}})); // CREATE RECTANGLE BEHIND TEXT AND APPEND IT TO GROUP
				T.appendChild(_R.cNS({n:'text', v:{x:_.interaction.v.xo, y:_.interaction.v.yo},s:{fontWeight:_.values.s.fontWeight, fontFamily:_.values.font, fill:_R.getSVGGradient(_.interaction.v.textcolor), fontSize:parseInt(_.values.s.size)+"px", textAnchor:'middle'}}));					
			}

			for (i=0; i<R.antexts.length;i++) R.svg.appendChild(R.antexts[i]); // ADDING ANCHORS

			// ADD TEXT BLOCKS
			if (_.interaction.v.use) {
				R.ml = _R.cNS({n:'line', c:"charts_markerline", s:{pointerEvents:"none"}, v:{stroke:_.interaction.v.color, strokeWidth:_.interaction.v.size, strokeDasharray:_.interaction.v.dash, x1:R.box.l, y1:R.box.t, x2:R.box.l, y2:(R.box.b)}});				
				R.svg.appendChild(R.ml);
			}
			
			R.svg.appendChild(T);	// ADD GROUP TO SVG
			R.svg.appendChild(R.legend);														
			LAYER.innerHTML="";			
			LAYER.appendChild(R.svg);
			

			_R.chartsBuild.updateLegend(R.legend,R.legends,_.legend,w,h);
			var firsttimeplay = window._R_is_Editor && (_R.chartsCache['chart_'+id]===undefined); 
			if (firsttimeplay) RVS.F.chartloadAllNeededFonts(id);		
			_R.chartsCache['chart_'+id] = R;				
			_R.chartsBuild.interaction(id);	
			if (firsttimeplay || !window._R_is_Editor) _R.chartsBuild.play(id);
			if (!window._R_is_Editor) return _R.chartsCache['chart_'+id];
			
									
		},
		interaction: function(id) {

			var chart = _R.chartsCache['chart_'+id];
			chart.pt = chart.svg.createSVGPoint();
			_R.isFireFoxC = _R.isFireFoxC || (jQuery.fn.revolution===undefined || jQuery.fn.revolution.isFirefox ===undefined ? true : jQuery.fn.revolution.isFirefox());
			chart.svg.addEventListener('mousemove',function(e) {			
				
				chart.pt.x = e.clientX;
				chart.pt.y = e.clientY;
				var res = chart.pt.matrixTransform(chart.svg.getScreenCTM().inverse()),
					over = _R.isFireFoxC ? !(res.x<chart.box.l || res.x>chart.box.r) : !(res.x<chart.box.l || res.x>chart.box.r || res.y<chart.box.t || res.y>chart.box.b);

				
				tpGS.gsap.to(chart.antexts,0.2, { opacity:(over ? 1 : 0)});
				
				res.x = Math.min(chart.box.r,Math.max(chart.box.l,res.x));
				
				if (chart.ml!==undefined ) tpGS.gsap.to(chart.ml,0.2,{opacity:(over ? 1 : 0),attr:{x1:res.x,x2:res.x}});
				var tx = tpGS.gsap.utils.snap(chart.xp,res.x),
					xi = chart.xp.indexOf(tx);
								

				for (var i=0;i<chart.antexts.length;i++) {						
					tpGS.gsap.to(chart.antexts[i],0.2,{x:chart.antexts[i].id==="chart_value_marker" ? res.x : chart.xp[xi], y:chart.antexts[i].id==="chart_value_marker" ? chart.box.b : chart.yp[i][xi]});
					chart.antexts[i].childNodes[1].textContent = chart.vals[i][xi];
					var bbox = chart.antexts[i].childNodes[1].getBBox();
					// IN OUT ANIMATION OF DOTS
					if (chart.antexts[i].id!=="chart_value_marker") {						
						if (chart.focusedDotsXi!=xi) tpGS.gsap.to(chart.dots[i][chart.focusedDotsXi],0.2,{scale:1,opacity:chart.dphidden ? 0 : 1,ease:'power2.inOut'});
						tpGS.gsap.to(chart.dots[i][xi],0.2,{scale:chart.dpscale ? 2 : 1,opacity:1,ease:'power2.inOut', overwrite:"auto"});
					}
					tpGS.gsap.set(chart.antexts[i].childNodes[0],{x:bbox.x, y:bbox.y, width:bbox.width+(2*chart.valpadding.h),height:bbox.height+(2*chart.valpadding.v)});						
				}	

				chart.focusedDotsXi = xi;									
				
			});
			chart.svg.addEventListener('mouseout',function(e) {
				tpGS.gsap.set(chart.antexts, { opacity:0});
				if (chart.focusedDotsXi!==undefined) for (var i=0;i<chart.antexts.length;i++) if (chart.antexts[i].id!=="chart_value_marker") tpGS.gsap.to(chart.dots[i][chart.focusedDotsXi],0.2,{scale:1,opacity:chart.dphidden ? 0 : 1,ease:'power2.inOut'});
			});
			chart.svg.addEventListener('mouseleave',function(e) {				
				tpGS.gsap.to(chart.antexts, 0.2,{ opacity:0});				
				if (chart.ml!==undefined ) tpGS.gsap.to(chart.ml,0.2,{opacity:0});
			});
		},
		play:function(id) {
			var _ = _R.chartsCache['chart_'+id];				
			_.anim = tpGS.gsap.timeline({paused:true});
			_.anim.add(tpGS.gsap.set(_.antexts, { opacity:0}),0);
			_.speed = _.speed===undefined ? 2 : _.speed;
			if (_.speed>100) _.speed = _.speed/1000;				
			_.anim.add(tpGS.gsap.from(_.paths, _.speed, { drawSVG:0, stagger:{amount:_.speed/4, from: "start" },ease:'power2.inOut'}),_.speed/4);
			if (_.bars!==undefined && _.bars.length>0) _.anim.add(tpGS.gsap.from(_.bars, _.speed, { scaleY:0, transformOrigin:"0% 100%", stagger:{amount:_.speed/4, from: "start" },ease:'power2.inOut'}),_.speed/4);
			if (_.filledpaths) _.anim.add(tpGS.gsap.from(_.filledpaths, _.speed, { opacity:0, stagger:{amount:_.speed/4, from: "start" },ease:'power2.inOut'}),_.speed/2);
			if (_.legend) _.anim.add(tpGS.gsap.from(_.legend, _.speed, { opacity:0, stagger:{amount:_.speed/4, from: "start" },ease:'power2.inOut'}),_.speed/2);
			if (_.vlines) _.anim.add(tpGS.gsap.from(_.vlines, _.speed/2, { drawSVG:0, ease:'power2.inOut', stagger:{amount:_.speed/4, from: "start" }}),_.speed/10);
			if (_.hlines) _.anim.add(tpGS.gsap.from(_.hlines, _.speed/2, { drawSVG:0,ease:'power2.inOut', stagger:{amount:_.speed/4, from: "start" }}),_.speed/5);				
			if (_.text) _.anim.add(tpGS.gsap.from(_.text, _.speed, { opacity:0,ease:'power2.inOut', stagger:{amount:_.speed/2, from: "start" }}),_.speed/5);				
			
			if (_.fv) _.anim.add(tpGS.gsap.from(_.fv, _.speed, { attr:{y:_.box.b},opacity:0,ease:'power2.inOut', stagger:{amount:_.speed/4, from: "start" },ease:'power2.inOut'}),_.speed/3.5);
			
			if (_.dots) _.anim.add(tpGS.gsap.from(_.dots, _.speed/5, { opacity:0,scale:0,ease:'back.out', stagger:{amount:_.speed/3.5, from: "random" }}),_.speed/3);
			if (_.ml) _.anim.add(tpGS.gsap.from(_.ml, _.speed/2, { drawSVG:0,ease:'power1.inOut'}),_.speed/2);			
			if (window._R_is_Editor) _.anim.play();
		}
	}
});

// GET OBJECT STRUCTURE
var getO = function(t,w,r) {
	var o={},u,s,i;
	for (u in t) {
		s = t[u].split(":");
		if (_TRANS_[s[0]]!==undefined) o[_TRANS_[s[0]]] = s[1];		
	}
	return o;
},

// SET OBJECT STRUCTURE
setO = function(_, f, sub, deep, movefont,use,totrue) {
	if (_[f]!==undefined) {
		jQuery.extend(true,(deep===undefined ? _.chart[sub] : _.chart[sub][deep]),getO(_[f].split(";")));
		if (movefont && deep!==undefined && _.chart[sub][deep].font!==undefined) _.chart[sub].font =  _.chart[sub][deep].font;
		delete _[f];
		if (totrue && deep!==undefined) _.chart[sub][deep].use = true;
	} else if (use) if (deep!==undefined) _.chart[sub][deep].use = false; else _.chart[sub].use=false;
},

/*
Prepare Layers for Listening
 */
prepareLayers = function(id,key) {
	
	for (var i in _R[id]._L) {

		if (!_R[id]._L.hasOwnProperty(i) || _R[id]._L[i].chartsData===undefined || _R[id]._L[i].chartsData.length==0) continue;
		
		
		if (key!==undefined && key!=="static" && _R[id]._L[i].slidekey!==key) continue;
		
		var _ = _R[id]._L[i];
		_.chart = _R.chartsBuild.getBasics();

		// READ STANDARDS
		for (var j in _TRBNS_) {
			if (!_TRBNS_.hasOwnProperty(j) || _[_TRBNS_[j]]===undefined) continue;			
			_.chart[j] = j=="data" || j=="altcolors" ?  
			_[_TRBNS_[j]] : 
			_[_TRBNS_[j]].split(';');
			delete _[_TRBNS_[j]];
		}
								
		// READ OBJECTS
		setO(_,'chartsLabelX','labels','x',true,true);
		setO(_,'chartsLabelY','labels','y',true,true);
		setO(_,'chartsLegend','legend',undefined,false,true);
		setO(_,'chartsGrid','grid',undefined,false,false);
		setO(_,'chartsInteraction','interaction','v',false,true);
		setO(_,'chartsBasics','settings',undefined,false,false);
		_.chart.settings.margin.top = _.chart.settings.marginTop!==undefined ? parseInt(_.chart.settings.marginTop) : _.chart.settings.margin.top;
		_.chart.settings.margin.bottom = _.chart.settings.marginBottom!==undefined ? parseInt(_.chart.settings.marginBottom) : _.chart.settings.margin.bottom;
		_.chart.settings.margin.left = _.chart.settings.marginLeft!==undefined ? parseInt(_.chart.settings.marginLeft) : _.chart.settings.margin.left;
		_.chart.settings.margin.right = _.chart.settings.marginRight!==undefined ? parseInt(_.chart.settings.marginRight) : _.chart.settings.margin.right;

		setO(_,'chartsValuesX','values','x',true,true);
		setO(_,'chartsValuesY','values','y',true,true);
		setO(_,'chartsValuesS','values','s',true,true);			
		setO(_,'chartsValuesF','values','f',true,true,true);			
		
		
		_.chart.cache = _R.chartsBuild.diagramm(_.c[0].id,_.chart,_.c[0],id);		
	}
},

listenToLayerChanges = function(id) {
	_R[id].c.on("revolution.layeraction",function(e,_){		
		if (_.layersettings.chart!==undefined) {						
			if (_.eventtype==="enterstage") {
				_.layersettings.chart.cache.anim.timeScale(1);
				if (_.layersettings.animationonscroll!="true" && _.layersettings.animationonscroll!=true) {
					_.layersettings.chart.startTimer = setTimeout(function() {					
					_.layersettings.chart.cache.anim.play(0);
					},parseInt(_.layersettings.chart.settings.delay));
				} else {
					_.layersettings.chart.cache.anim.paused(false);
					if (_.layersettings.timeline.labels!==undefined && _.layersettings.timeline.labels.chart===undefined) {						
						_.layersettings.timeline.addLabel('chart',_.layersettings.timeline.labels.frame_1 + parseInt(_.layersettings.chart.settings.delay)/1000);
						_.layersettings.timeline.add(_.layersettings.chart.cache.anim,'chart');
						_.layersettings.timeline.render(_.layersettings.timeline.time(),true,true);
						
					} 
				}				
			}
			if (_.eventtype==="leavestage") {
				_.layersettings.chart.cache.anim.timeScale(3);
				_.layersettings.chart.cache.anim.reverse();
			}
			if (_.eventtype=="leftstage") {
				_.layersettings.chart.cache.anim.pause(0);
			}
		}	
	});
}
//Support Defer and Async and Footer Loads
window.RS_MODULES = window.RS_MODULES || {};
window.RS_MODULES.charts = {loaded:true, version:'3.0.2'};
if (window.RS_MODULES.checkMinimal) window.RS_MODULES.checkMinimal();

})();