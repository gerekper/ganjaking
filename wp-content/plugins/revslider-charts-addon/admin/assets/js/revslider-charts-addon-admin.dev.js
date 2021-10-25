/**
 * @preserve
 * @name Slider Revolution Charts AddOn
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
 * @version 1.0.0
 */

(function($) {
		
		var addon = {},
			slug = 'revslider-charts-addon',
			bricks = revslider_charts_addon.bricks,
			_COLORS_ = ['#8B1E3F', '#3C153B', '#89BD9E','#F0C987','#DB4C40','#3772FF','#70E4EF','#A1C181','#233D4D','#FE7F2D','#619B8A','#fbc01a','#e67010','#41bcde','#8ab8dc','#ef1921'],
			_BGS_ = ['rgba(139, 30, 63, 0.5)', 'rgba(60, 21, 59, 0.5)', 'rgba(137, 189, 158, 0.5)','rgba(137, 189, 158, 0.5)','rgba(219, 76, 64, 0.5)','rgba(55, 114, 255, 0.5)','rgba(112, 228, 239, 0.5)','rgba(161, 193, 129, 0.5)','rgba(35, 61, 77, 0.5)','rgba(254, 127, 45, 0.5)','rgba(97, 155, 138, 0.5)'],
			presets = {			
				charts_g_1 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Montserrat","x":{"name":"SELL","color":"#2d2d2d","yo":"30","fontWeight":"600","font":"Lato","e":true},"y":{"name":"ROOM N°","color":"#2d2d2d","xo":"0","fontWeight":"600"}},"legend":{"color":"#2d2d2d","size":"12px","v":"top","h":"center","xo":"0","yo":"40","gap":"15","font":"Montserrat","bg":"rgba(255, 255, 255, 0)","sbg":false,"e":true},"values":{"font":"Montserrat","s":{"suf":" ROOM","xo":"15","yo":"3","fontWeight":"400","round":true},"x":{"dez":"0","color":"#555555","size":"11px","every":"4","round":true},"y":{"dez":"0","color":"#555555","round":true}},"grid":{"xuse":false,"xcolor":"#cccccc","xsize":"1px","xstcolor":"#cccccc","xstsize":"1px","ycolor":"#e5e5e5","ysize":"1px","ybtcolor":"#cccccc","ybtsize":"1px"},"interaction":{"v":{"usexval":false,"color":"rgba(170, 170, 170, 0.75)","dash":"2","useval":true},"h":{"use":true,"color":"rgba(255,255,255,0.75)","size":1,"dash":"0","fill":"#000","textcolor":"#fff"},"e":true},"settings":{"gap":"1","speed":"1200ms","delay":"400ms","margin":{"top":"80","bottom":"80","left":"60","right":"60"},"usetitle":true},"inuse":[true,true,true,true,false],"index":[1,"1","2",4,5],"strokewidth":[2,"1","1","1","1"],"strokedash":[0,"0","0","0","0"],"curves":[0,"0","0","0","0"],"datapoint":[1,"2","2","2","2"],"strokecolor":["#8B1E3F","#2ceabe","#ffcc00","#004fef","#e271a8"],"anchorcolor":["#8B1E3F","#2ceabe","#ffcc00","#004fef","#e2006d"],"fillcolor":["rgba(139, 30, 63, 0.5)","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:105,&g&:234,&b&:203,&a&:0.8,&position&:0,&align&:&top&},{&r&:105,&g&:234,&b&:203,&a&:0.8,&position&:0,&align&:&bottom&},{&r&:105,&g&:234,&b&:203,&a&:0,&position&:80,&align&:&top&},{&r&:105,&g&:234,&b&:203,&a&:0,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:205,&b&:79,&a&:0.8,&position&:0,&align&:&top&},{&r&:255,&g&:205,&b&:79,&a&:0.8,&position&:0,&align&:&bottom&},{&r&:255,&g&:205,&b&:17,&a&:0,&position&:80,&align&:&top&},{&r&:255,&g&:205,&b&:2,&a&:0,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:0,&g&:79,&b&:239,&a&:1,&position&:0,&align&:&top&},{&r&:0,&g&:79,&b&:239,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:255,&b&:255,&a&:0,&position&:100,&align&:&bottom&},{&r&:255,&g&:255,&b&:255,&a&:0,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:226,&g&:0,&b&:109,&a&:0.8,&position&:0,&align&:&bottom&},{&r&:226,&g&:0,&b&:109,&a&:0.8,&position&:0,&align&:&top&},{&r&:226,&g&:0,&b&:109,&a&:0.05,&position&:80,&align&:&top&},{&r&:226,&g&:0,&b&:109,&a&:0.05,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}"],"valuebgcols":["#8B1E3F","rgba(0, 165, 132, 0.9)","rgba(255, 204, 0, 0.9)","rgba(0, 79, 239, 0.9)","rgba(226, 0, 109, 0.9)"],"valuecolor":["#FFF","#FFF","#0c0c0c","#FFF","#FFF"],"valuefcolor":["#FFF","#FFF","#FFF","#FFF","#FFF"],"altcolors":[[],[],[],[],[]],"altcolorsuse":[false,false,false,false,false,false,false,false,false],"csv":"Sell,Rooms,Beds,Baths,Acres\n142,10,5,3,0.28\n175,8,4,1,0.43\n129,6,3,1,0.33\n138,7,3,1,0.46\n232,8,4,3,2.05\n135,7,4,3,0.57\n150,8,4,3,4.00\n207,8,4,2,2.22\n271,10,5,2,0.53\n89,5,3,1,0.30\n153,8,3,3,0.38\n87,7,3,1,0.65\n234,8,4,2,1.61\n106,8,4,1,0.22\n175,8,4,2,2.06\n165,8,4,2,0.46\n166,9,4,2,0.27\n136,7,3,1,0.63\n148,7,3,2,0.36\n151,8,4,2,0.34\n180,9,4,2,1.55\n293,8,4,3,0.46\n167,9,4,2,0.46\n110,8,4,1,0.29\n135,7,4,1,0.43\n567,11,4,4,0.85\n\n"}},"type":"shape","subtype":"charts","alias":"Light-lines-gradient","size":{"width":{"d":{"v":"1240px","e":true},"n":{"v":"1024px"},"t":{"v":"778px"},"m":{"v":"480px"}},"height":{"d":{"v":"670px","e":true},"n":{"v":"553px"},"t":{"v":"420px"},"m":{"v":"259px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":5},"timeline":{"frames":{"frame_0":{"transform":{"y":{"d":{"v":"200%"},"n":{"v":"200%"},"t":{"v":"200%"},"m":{"v":"200%"}},"scaleX":2,"scaleY":2,"opacity":1,"rotationX":"-20deg","rotationY":"-20deg"},"timeline":{"endWithSlide":false,"alias":"Anim From"}},"frame_1":{"timeline":{"ease":"power3.out","speed":1000,"start":590,"startRelative":590,"endWithSlide":false,"frameLength":1000,"alias":"Anim To"}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":910,"start":9000,"startRelative":7410,"endWithSlide":true,"frameLength":910}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":590},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":true},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#ffffff","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_2 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Lato","x":{"use":false},"y":{"use":false}},"legend":{"use":false},"values":{"font":"Arial, Helvetica, sans-serif","s":{"suf":"VALUE","xo":"15","yo":"3","fontWeight":"400","round":true},"x":{"dez":"0","color":"#555555","size":"11px","fontWeight":"400","every":"4","round":true},"y":{"dez":"0","color":"#555555","fontWeight":"400","round":true}},"grid":{"xuse":false,"xcolor":"#cccccc","xsize":"1px","xstcolor":"#cccccc","xstsize":"1px","ycolor":"#5b5b5b","ysize":"1px","ybtcolor":"#cccccc","ybtsize":"1px"},"interaction":{"v":{"dash":"2","dphidden":true,"useval":true},"h":{"use":true,"color":"rgba(255,255,255,0.75)","size":1,"dash":"0","fill":"#000","textcolor":"#fff"},"e":true},"settings":{"gap":"1","speed":"1200ms","delay":"400ms","margin":{"top":"50","bottom":"50","left":"50","right":"50"},"usetitle":true},"inuse":[true,true,true,false,true],"index":[1,"1","0",4,5],"strokewidth":[2,"2","2","2","2"],"strokedash":[0,"0","0","0","0"],"curves":[0,"2","2","2","2"],"datapoint":[1,"2","2","2","2"],"strokecolor":["#8B1E3F","#2ceabe","#ffcc00","#004fef","#e271a8"],"anchorcolor":["#8B1E3F","#2ceabe","#ffcc00","#004fef","#e2006d"],"fillcolor":["rgba(139, 30, 63, 0.5)","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:105,&g&:234,&b&:203,&a&:0.8,&position&:0,&align&:&top&},{&r&:105,&g&:234,&b&:203,&a&:0.8,&position&:0,&align&:&bottom&},{&r&:105,&g&:234,&b&:203,&a&:0,&position&:80,&align&:&top&},{&r&:105,&g&:234,&b&:203,&a&:0,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:205,&b&:79,&a&:0.8,&position&:0,&align&:&top&},{&r&:255,&g&:205,&b&:79,&a&:0.8,&position&:0,&align&:&bottom&},{&r&:255,&g&:205,&b&:17,&a&:0,&position&:80,&align&:&top&},{&r&:255,&g&:205,&b&:2,&a&:0,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:0,&g&:79,&b&:239,&a&:1,&position&:0,&align&:&top&},{&r&:0,&g&:79,&b&:239,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:255,&b&:255,&a&:0,&position&:100,&align&:&bottom&},{&r&:255,&g&:255,&b&:255,&a&:0,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:226,&g&:0,&b&:109,&a&:0.8,&position&:0,&align&:&bottom&},{&r&:226,&g&:0,&b&:109,&a&:0.8,&position&:0,&align&:&top&},{&r&:226,&g&:0,&b&:109,&a&:0.05,&position&:80,&align&:&top&},{&r&:226,&g&:0,&b&:109,&a&:0.05,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}"],"valuebgcols":["#8B1E3F","rgba(0, 165, 132, 0.9)","rgba(255, 204, 0, 0.9)","rgba(0, 0, 0, 0.9)","rgba(226, 0, 109, 0.9)"],"valuecolor":["#FFF","#FFF","#0c0c0c","#FFF","#FFF"],"valuefcolor":["#FFF","#FFF","#FFF","#FFF","#FFF"],"altcolors":[[],[],[],[],[]],"altcolorsuse":[false,false,false,false,false,false,false,false,false],"csv":"Sell,Rooms,Beds,Baths,Acres\n142,10,5,3,0.28\n175,8,4,1,0.43\n129,6,3,1,0.33\n138,7,3,1,0.46\n232,8,4,3,2.05\n135,7,4,3,0.57\n150,8,4,3,4.00\n207,8,4,2,2.22\n271,10,5,2,0.53\n89,5,3,1,0.30\n153,8,3,3,0.38\n87,7,3,1,0.65\n234,8,4,2,1.61\n106,8,4,1,0.22\n175,8,4,2,2.06\n165,8,4,2,0.46\n166,9,4,2,0.27\n136,7,3,1,0.63\n148,7,3,2,0.36\n151,8,4,2,0.34\n180,9,4,2,1.55\n293,8,4,3,0.46\n167,9,4,2,0.46\n110,8,4,1,0.29\n135,7,4,1,0.43\n567,11,4,4,0.85\n\n"}},"type":"shape","subtype":"charts","alias":"Dark-gradient","size":{"width":{"d":{"v":"1240px","e":true},"n":{"v":"1024px"},"t":{"v":"778px"},"m":{"v":"480px"}},"height":{"d":{"v":"670px","e":true},"n":{"v":"553px"},"t":{"v":"420px"},"m":{"v":"259px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":5},"timeline":{"frames":{"frame_0":{"transform":{"y":{"d":{"v":"200%"},"n":{"v":"200%"},"t":{"v":"200%"},"m":{"v":"200%"}},"scaleX":2,"scaleY":2,"opacity":1,"rotationX":"-20deg","rotationY":"-20deg"},"timeline":{"endWithSlide":false,"alias":"Anim From"}},"frame_1":{"timeline":{"ease":"power3.out","speed":1000,"start":590,"startRelative":590,"endWithSlide":false,"frameLength":1000,"alias":"Anim To"}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":910,"start":9000,"startRelative":7410,"endWithSlide":true,"frameLength":910}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":590},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":true},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#080023","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_3 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Poppins","x":{"name":"Temperature Table","color":"#000000","size":"20px","h":"left","xo":"200","yo":"25","font":"Nunito"},"y":{"use":false}},"legend":{"color":"#000000","size":"15px","v":"bottom","h":"right","xo":"35 ","yo":"-15","gap":"20","font":"Poppins","fontWeight":"400","dp":false,"sbg":false,"e":true},"values":{"font":"Poppins","s":{"suf":"C°","size":"15px","xo":"20","yo":"-20","direction":"middle","paddingh":"15px","paddingv":"3px"},"x":{"color":"#333333","size":"15px","yo":"20","fontWeight":"400","every":"1"},"y":{"suf":"C°","dez":"0","color":"#333333","size":"14px","xo":"-20","fontWeight":"600"}},"grid":{"xcolor":"#cccccc","xsize":"0px","xstcolor":"transparent","xstsize":"0px","ycolor":"#cccccc","ysize":"1px","ybtcolor":"#dddddd","ybtsize":"0px"},"interaction":{"v":{"use":false,"usexval":false,"color":"rgba(12, 12, 12, 0.75)"},"e":true},"settings":{"gap":"20","width":"1200","height":"650","delay":"0ms","margin":{"top":"50","bottom":"120","left":"100","right":"50"}},"inuse":[true,true,true,true,true],"index":[0,1,2,3,4],"strokewidth":[1,"4","4","4","4"],"strokedash":[1,"0","0","0","0"],"curves":[0,"0","0","0",0],"datapoint":["3","2","2","2","2"],"strokecolor":["transparent","#ff2414","#ffa500","#34aadc","#004faf"],"anchorcolor":["#007aff","#ff2414","#ffa500","#34aadc","#004faf"],"fillcolor":["#ff3a2d","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:36,&b&:20,&a&:0.1,&position&:0,&align&:&top&},{&r&:255,&g&:36,&b&:20,&a&:0.1,&position&:0,&align&:&bottom&},{&r&:3,&g&:0,&b&:201,&a&:0.1,&position&:100,&align&:&bottom&},{&r&:3,&g&:0,&b&:201,&a&:0.1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","transparent","transparent","transparent"],"valuebgcols":["#ffffff","#ff3a2d","#ffcc00","#5ac8fa","#007aff"],"valuecolor":["#000000","#000000","#000000","#FFF","#FFF"],"valuefcolor":["#FFF","#FFF","#FFF","#FFF","#FFF"],"altcolors":[["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B"],[],[],[],[]],"altcolorsuse":[true,false,false,false,false,false,false],"csv":"Date,Hawaii Highs, Hawaii Lows,South Pole Highs, South Pole Lows\nJan,24,17,-26,-30,2,-7\nFeb,24,18,-38,-43,4,-5\nMar,25,19,-50,-57,9,-2\nApr,27,21,-53,-61,14,3\nMay,28,23,-54,-62,18,8\nJun,30,25,-54,-63,21,12\nJul,32,26,-54,-63,24,14\nAug,31,26,-55,-63,25,14\nSep,30,25,-54,-62,19,10\nOct,29,24,-48,-54,15,5\nNov,26,21,-36,-40,9,0\nDec,25,19,-26,-29,3,-4"}},"type":"shape","subtype":"charts","alias":"Light-temperature","size":{"width":{"d":{"v":"1243px","e":true},"n":{"v":"1026px"},"t":{"v":"779px"},"m":{"v":"480px"}},"height":{"d":{"v":"760px","e":true},"n":{"v":"627px"},"t":{"v":"476px"},"m":{"v":"293px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"v":"-1px","e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"endWithSlide":false},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_1":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"speed":1010,"start":640,"startRelative":640,"endWithSlide":false,"frameLength":1010},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#ffffff","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_4 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Poppins","x":{"name":"Temperature Table","color":"#dddddd","size":"24px","v":"top","yo":"-10","font":"Nunito","e":true},"y":{"use":false}},"legend":{"color":"#bcbcbc","size":"15px","v":"bottom","h":"center","xo":"0","yo":"-30","gap":"20","font":"Poppins","fontWeight":"400","dp":false,"sbg":false,"e":true},"values":{"font":"Poppins","s":{"suf":"C°","size":"15px","xo":"20","yo":"-20","direction":"middle","paddingh":"15px","paddingv":"3px"},"x":{"color":"#bcbcbc","size":"15px","yo":"20","fontWeight":"400","every":"1"},"y":{"suf":"C°","dez":"0","color":"#bcbcbc","size":"14px","xo":"-20","fontWeight":"600"}},"grid":{"xcolor":"#353535","xsize":"1px","xstcolor":"transparent","xstsize":"0px","ycolor":"#353535","ybtcolor":"#dddddd","ybtsize":"1px"},"interaction":{"v":{"use":false,"usexval":false,"color":"rgba(12, 12, 12, 0.75)"},"e":true},"settings":{"gap":"20","width":"1200","height":"650","delay":"0ms","margin":{"top":"50","bottom":"120","left":"100","right":"50"}},"inuse":[true,true,true,true,true],"index":[0,1,2,3,4],"strokewidth":[1,"6","6","6","6"],"strokedash":[1,"0","0","0","0"],"curves":[0,"0","0","0","0"],"datapoint":["3","2","2","2","2"],"strokecolor":["transparent","#ff2414","#ffa500","#34aadc","#0300c9"],"anchorcolor":["#007aff","#ff2414","#ffa500","#34aadc","#0300c9"],"fillcolor":["#ff3a2d","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:36,&b&:20,&a&:0.1,&position&:0,&align&:&top&},{&r&:255,&g&:36,&b&:20,&a&:0.1,&position&:0,&align&:&bottom&},{&r&:3,&g&:0,&b&:201,&a&:0.1,&position&:100,&align&:&bottom&},{&r&:3,&g&:0,&b&:201,&a&:0.1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","transparent","transparent","transparent"],"valuebgcols":["#ffffff","#ff3a2d","#ffcc00","#5ac8fa","#007aff"],"valuecolor":["#000000","#000000","#000000","#FFF","#FFF"],"valuefcolor":["#FFF","#FFF","#FFF","#FFF","#FFF"],"altcolors":[["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B"],[],[],[],[]],"altcolorsuse":[true,false,false,false,false,false,false],"csv":"Date,Hawaii Highs, Hawaii Lows,South Pole Highs, South Pole Lows\nJan,24,17,-26,-30,2,-7\nFeb,24,18,-38,-43,4,-5\nMar,25,19,-50,-57,9,-2\nApr,27,21,-53,-61,14,3\nMay,28,23,-54,-62,18,8\nJun,30,25,-54,-63,21,12\nJul,32,26,-54,-63,24,14\nAug,31,26,-55,-63,25,14\nSep,30,25,-54,-62,19,10\nOct,29,24,-48,-54,15,5\nNov,26,21,-36,-40,9,0\nDec,25,19,-26,-29,3,-4"}},"type":"shape","subtype":"charts","alias":"Dark-temperature","size":{"width":{"d":{"v":"1243px","e":true},"n":{"v":"1026px"},"t":{"v":"779px"},"m":{"v":"480px"}},"height":{"d":{"v":"785px","e":true},"n":{"v":"648px"},"t":{"v":"492px"},"m":{"v":"303px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"v":"-1px","e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"endWithSlide":false},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_1":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"speed":1010,"start":640,"startRelative":640,"endWithSlide":false,"frameLength":1010},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#1a001e","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_5 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Poppins","x":{"name":"Comparison","color":"rgba(12, 12, 12, 0.8)","size":"22px","yo":"20","fontWeight":"700","font":"Nunito"},"y":{"color":"#ffffff","size":"20px","xo":"35","fontWeight":"700"}},"legend":{"color":"rgba(12, 12, 12, 0.8)","size":"16px","v":"top","h":"center","xo":"0","yo":"20","font":"Poppins","fontWeight":"400","dp":false,"st":false,"e":true},"values":{"font":"Poppins","s":{"size":"15px","xo":"20","yo":"-20","direction":"middle","fontWeight":"400","paddingh":"15px","paddingv":"3px","fr":false,"radius":"0px"},"x":{"color":"rgba(12, 12, 12, 0.8)","size":"15px","yo":"10","fontWeight":"400","every":"1"},"y":{"dez":"0","color":"rgba(12, 12, 12, 0.8)","size":"15px","xo":"-10","fontWeight":"400","fr":false}},"grid":{"xuse":false,"xcolor":"#b7b7b7","xsize":"1px","xstcolor":"transparent","xstsize":"0px","ycolor":"#cccccc","ysize":"1px","ybtcolor":"#fff4f4","ybtsize":"0px"},"interaction":{"v":{"use":false,"usexval":false},"e":true},"settings":{"type":"pbar","gap":"100","width":"1200","height":"700","delay":"0ms","margin":{"top":"100","bottom":"100","left":"100","right":"50"}},"inuse":[true,true,true],"index":[0,"1",2],"strokewidth":[1,"2","2"],"strokedash":[1,"0","0"],"curves":[0,0,0],"datapoint":["3","2","2"],"strokecolor":["transparent","#ff3a2d","#ffcc00"],"anchorcolor":["#007aff","#000000","#000000"],"fillcolor":["#ff3a2d","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:149,&b&:0,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#dbdbdb"],"valuebgcols":["#ffffff","#ff9500","#cccccc"],"valuecolor":["#000000","#000000","#0c0c0c"],"valuefcolor":["#FFF","#FFF","#FFF"],"altcolors":[["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B"],["#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40","#3772FF","#70E4EF","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F"],[]],"altcolorsuse":[true,false,false,false,false],"csv":"Year, Your Company, Others\n2010,64,58\n2011,75,69\n2012,77,41\n2013,68,53\n2014,50,45\n2015,71,52\n2016,61,56\n2017,50,35\n2018,59,34\n2019,46,41\n2020,75,59"}},"type":"shape","subtype":"charts","alias":"Comparison","size":{"width":{"d":{"v":"1215px"},"n":{"v":"1003px"},"t":{"v":"762px"},"m":{"v":"470px"}},"height":{"d":{"v":"780px","e":true},"n":{"v":"644px"},"t":{"v":"489px"},"m":{"v":"301px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"rotationX":"-70deg","originZ":"-50"},"timeline":{"endWithSlide":false,"alias":"Anim From"}},"frame_1":{"transform":{"originZ":"-50"},"timeline":{"ease":"power4.inOut","speed":1750,"start":640,"endWithSlide":false,"frameLength":1750,"alias":"Anim To"}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#ffffff","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_6 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Poppins","x":{"name":"Comparison","color":"rgba(255, 255, 255, 0.8)","size":"22px","yo":"20","fontWeight":"700","font":"Nunito"},"y":{"color":"#ffffff","size":"20px","xo":"35","fontWeight":"700"}},"legend":{"color":"rgba(255, 255, 255, 0.8)","size":"14px","v":"top","h":"center","xo":"0","yo":"20","font":"Poppins","fontWeight":"400","dp":false,"st":false,"e":true},"values":{"font":"Poppins","s":{"size":"15px","xo":"20","yo":"-20","direction":"middle","fontWeight":"400","paddingh":"15px","paddingv":"3px","fr":false,"radius":"0px"},"x":{"color":"rgba(255, 255, 255, 0.5)","size":"15px","yo":"10","fontWeight":"400","every":"1"},"y":{"dez":"0","color":"rgba(255, 255, 255, 0.5)","size":"15px","xo":"-10","fontWeight":"400","fr":false}},"grid":{"xuse":false,"xcolor":"#b7b7b7","xsize":"1px","xstcolor":"transparent","xstsize":"0px","yuse":false,"ycolor":"rgba(255, 244, 244, 0.1)","ysize":"1px","ybtcolor":"#fff4f4","ybtsize":"0px"},"interaction":{"v":{"use":false,"usexval":false},"e":true},"settings":{"type":"pbar","gap":"60","width":"1200","height":"700","delay":"0ms","margin":{"top":"100","bottom":"100","left":"100","right":"50"}},"inuse":[true,true,true],"index":[0,"1",2],"strokewidth":[1,"2","2"],"strokedash":[1,"0","0"],"curves":[0,0,0],"datapoint":["3","2","2"],"strokecolor":["transparent","#ff3a2d","#ffcc00"],"anchorcolor":["#007aff","#000000","#000000"],"fillcolor":["#ff3a2d","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:68,&g&:209,&b&:60,&a&:1,&position&:0,&align&:&top&},{&r&:68,&g&:209,&b&:60,&a&:1,&position&:0,&align&:&bottom&},{&r&:0,&g&:186,&b&:21,&a&:1,&position&:100,&align&:&bottom&},{&r&:0,&g&:186,&b&:21,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","rgba(255, 255, 255, 0.1)"],"valuebgcols":["#ffffff","#44d13c","rgba(255, 255, 255, 0.1)"],"valuecolor":["#000000","#000000","rgba(255, 255, 255, 0.7)"],"valuefcolor":["#FFF","#FFF","#FFF"],"altcolors":[["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B"],["#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40","#3772FF","#70E4EF","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F"],[]],"altcolorsuse":[true,false,false,false,false],"csv":"Year, Your Company, Others\n2010,64,58\n2011,75,69\n2012,77,41\n2013,68,53\n2014,50,45\n2015,71,52\n2016,61,56\n2017,50,35\n2018,59,34\n2019,46,41\n2020,75,59"}},"type":"shape","subtype":"charts","alias":"Comparison","size":{"width":{"d":{"v":"1215px"},"n":{"v":"1003px"},"t":{"v":"762px"},"m":{"v":"470px"}},"height":{"d":{"v":"780px","e":true},"n":{"v":"644px"},"t":{"v":"489px"},"m":{"v":"301px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"rotationX":"-70deg","originZ":"-50"},"timeline":{"endWithSlide":false,"alias":"Anim From"}},"frame_1":{"transform":{"originZ":"-50"},"timeline":{"ease":"power4.inOut","speed":1750,"start":640,"endWithSlide":false,"frameLength":1750,"alias":"Anim To"}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"{&type&:&radial&,&angle&:&0&,&colors&:[{&r&:49,&g&:29,&b&:61,&a&:1,&position&:0,&align&:&top&},{&r&:49,&g&:29,&b&:61,&a&:1,&position&:0,&align&:&bottom&},{&r&:6,&g&:5,&b&:15,&a&:1,&position&:100,&align&:&bottom&},{&r&:6,&g&:5,&b&:15,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_7 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Arvo","x":{"use":false},"y":{"use":false}},"legend":{"color":"#474747","v":"top","h":"center","xo":"0","yo":"22","gap":"50","font":"Roboto","fontWeight":"400","sbg":false,"e":true},"values":{"font":"Arial, Helvetica, sans-serif","s":{"suf":"GB ","xo":"15","yo":"3","fontWeight":"400","round":true},"x":{"suf":"s","dez":"0","color":"#606060","size":"10px","xo":"5","fontWeight":"400","every":"20","fr":true,"round":true},"y":{"suf":"GB","dez":"0","color":"#606060","size":"11px","fontWeight":"400","round":true}},"grid":{"xcolor":"transparent","xsize":"0px","xstcolor":"rgba(135, 98, 110, 0.5)","xstsize":"0.5px","ycolor":"rgba(135, 98, 110, 0.5)","ysize":"0.5px","ybtcolor":"transparent","ybtsize":"1px"},"interaction":{"v":{"usexval":false,"color":"rgba(79, 79, 79, 0.85)","dash":"1","dphidden":true,"useval":true},"h":{"use":true,"color":"rgba(255,255,255,0.75)","size":1,"dash":"0","fill":"#000","textcolor":"#fff"},"e":true},"settings":{"gap":"1","speed":"1200ms","delay":"400ms","margin":{"top":"50","bottom":"70","left":"50","right":"0"},"usetitle":true},"inuse":[true,true,true,true],"index":[1,"1","30","20"],"strokewidth":[2,"0.5","0.5","0.5"],"strokedash":[0,"0","0","0"],"curves":[0,"0","0",0],"datapoint":[1,"1","1","1"],"strokecolor":["#8B1E3F","#00748e","#ff0000","#e25600"],"anchorcolor":["#8B1E3F","#0095af","#ff0000","#e25600"],"fillcolor":["rgba(139, 30, 63, 0.5)","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:0,&g&:149,&b&:175,&a&:0.25,&position&:0,&align&:&top&},{&r&:0,&g&:149,&b&:175,&a&:0.25,&position&:0,&align&:&bottom&},{&r&:0,&g&:149,&b&:175,&a&:0.03,&position&:100,&align&:&bottom&},{&r&:0,&g&:149,&b&:175,&a&:0.03,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:0,&b&:0,&a&:0.2,&position&:0,&align&:&top&},{&r&:255,&g&:0,&b&:0,&a&:0.2,&position&:0,&align&:&bottom&},{&r&:255,&g&:0,&b&:0,&a&:0.03,&position&:100,&align&:&bottom&},{&r&:255,&g&:0,&b&:0,&a&:0.03,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:226,&g&:86,&b&:0,&a&:0.2,&position&:0,&align&:&top&},{&r&:226,&g&:86,&b&:0,&a&:0.2,&position&:0,&align&:&bottom&},{&r&:226,&g&:86,&b&:0,&a&:0.03,&position&:100,&align&:&bottom&},{&r&:226,&g&:86,&b&:0,&a&:0.03,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}"],"valuebgcols":["#8B1E3F","rgba(12, 12, 12, 0.7)","rgba(12, 12, 12, 0.7)","rgba(12, 12, 12, 0.7)"],"valuecolor":["#FFF","#ffffff","#ffffff","#ffffff"],"valuefcolor":["#FFF","#FFF","#FFF","#FFF"],"altcolors":[[],[],[],[]],"altcolorsuse":[false,false,false,false,false,false,false,false,false],"csv":"sec,Cache,Buffer,Used\n10,10.12,2.09,4.96\n20,6.91,2.93,5.62\n30,10.62,2.26,4.39\n40,7.41,2.04,5.33\n50,7.57,2.34,4.63\n60,10.15,2.31,4.6\n70,10.97,2.97,4.62\n80,11.72,2.07,5.86\n90,9.13,2.69,5.69\n100,9.18,2.61,4.75\n110,11.14,2.29,5.54\n120,9.6,2.66,4.02\n130,10.35,2.44,5.2\n140,9.88,2.68,5.49\n150,10.38,2.6,5.66\n160,7.84,2.08,5.6\n170,11.12,2.57,4.04\n180,6.37,2.48,5.91\n190,11.31,2.05,5.08\n200,8.57,2.26,5.34\n210,8.02,2.52,4.52\n220,11.35,2.64,5.79\n230,8.73,2.56,4.48\n240,9.09,2.31,5.04\n250,10.77,2.51,5.95\n260,10.31,2.3,4.75\n270,6.31,2.08,5.27\n280,7.21,2.46,5.6\n290,8.51,2.64,4.98\n300,6.7,2.2,5.92\n310,9.69,2.08,4.99\n320,9.89,2.56,4.41\n330,9.06,2.81,5.66\n340,9.03,2.12,4.79\n350,9.06,2.65,5.62\n360,7.19,2.04,5.08\n370,6.17,2.75,4.06\n380,6.45,2.73,4.78\n390,11.69,2.46,4.17\n400,6.4,2.39,5.46\n410,11.47,2.82,5.32\n420,8.83,2.79,5.31\n430,11.78,2.69,5.92\n440,9.62,2.69,4.13\n450,7.52,2.06,4.91\n460,9.9,2.73,4.43\n470,6.48,2.44,4.75\n480,6.45,2.23,4.77\n490,8.68,2.99,6\n500,8.35,2.95,4.01\n510,6.48,2.94,4.6\n520,6.57,2.59,4.86\n530,9.64,2.28,5.63\n540,8.57,2.78,5.23\n550,9.74,2.03,5.06\n560,7.59,2.96,4.73\n570,8.63,2.26,4.62\n580,9.93,2.7,5.82\n590,6.1,2.33,5.39\n600,11.58,2.67,5.28\n610,8.41,2.83,5.63\n620,6.72,2.81,4.58\n630,8.58,2.22,4.51\n640,7.58,2.56,5.98\n650,8.85,2.09,4.29\n660,7.38,2.77,4.59\n670,11.16,2.58,4.04\n680,7.22,2.4,5.79\n690,8.26,2.58,5.17\n700,9.85,2.76,5.8\n710,7.06,2.61,5.2\n720,8.46,2.76,5.28\n730,11.97,2.22,4.08\n740,10.66,2.89,5.04\n750,6.1,2.26,5.86\n760,6.21,2.71,5.68\n770,6.98,2.34,4.26\n780,10.25,2.93,5.66\n790,10.91,2.51,4.07\n800,8.25,2.65,4.6\n810,6.15,2.21,5.1\n820,6.52,2.29,4.38\n830,10.5,2.17,4.99\n840,9.18,2.46,5.62\n850,11.64,2.31,5.92\n860,7.47,2.7,5.04\n870,7.78,2.52,5.68\n880,8.52,2.77,5.48\n890,10.56,2.65,5.78\n900,7.86,2.53,4.95\n910,9.32,2.57,5.74\n920,7.3,2.54,5.85\n930,10.54,2.7,4.84\n940,6.04,2.41,4.41\n950,9.96,2.72,4.31\n960,6.28,2.46,5.45\n970,7.53,2.96,4.48\n980,7.2,2.52,5.55\n990,10.64,2.38,4.78\n1000,6.59,2.63,5.19\n1010,11.52,2.88,4.92\n1020,7.82,2.61,4.73\n1030,6.87,2.53,5.93\n1040,8.19,2.03,5.91\n1050,10.44,2.04,5.68\n1060,8.61,2.73,5.17\n1070,6.04,2.58,4.22\n1080,7.34,2.3,5.94\n1090,9.07,2.03,5.6\n1100,10.7,2.54,5.86\n1110,9.44,2.54,5.19\n1120,7.41,2.41,4.77\n1130,6.22,2.86,5.33\n1140,10.6,2.72,4.1\n1150,10.41,2.12,4.66\n1160,8.7,2.25,4.85\n1170,7.92,2.26,5.87\n1180,6.68,2.06,4.94\n1190,11.21,2.66,5.89\n1200,10.55,2.03,4.92\n1210,9.88,2.37,5.55\n1220,8.35,2.11,5.9\n1230,11.42,2.25,5.67\n1240,9.61,2.15,5.54\n1250,11.8,2.46,4.75\n1260,9.08,2.06,5.65\n1270,8.71,3,5.46\n1280,10.55,2.97,4.72\n1290,10.15,2.71,5.83\n1300,9.26,2.86,5.66\n1310,10.34,2.84,4.29\n1320,11.89,2.39,5.61\n1330,6.27,2.39,5.07\n1340,8.34,2.01,5.07\n1350,9.51,2.94,4.8\n1360,10.05,2.4,5.94\n1370,7.28,2.62,4.97\n1380,9.52,2.41,5.91\n1390,7.68,2.27,4.35\n1400,11.67,2.65,4.8\n1410,7.04,2.88,4.47\n1420,8.47,2.95,5.5\n1430,7.94,2.52,5.3\n1440,6.12,2.24,5.43\n1450,8.12,2.72,5.62\n1460,7.22,2.86,4.98\n1470,11.72,2.45,5.48\n1480,11.25,2.27,5.79\n1490,8.63,2.09,4.9\n1500,8.6,2.33,5.65\n1510,9.96,2.45,5.86\n1520,8.98,2.03,4.56\n1530,7.05,2.85,5.1\n1540,6.39,2.44,5.74\n1550,11.29,2.7,5.91\n1560,11.47,2.72,4.49\n1570,11.66,2.38,5.61\n1580,9.69,2.98,4.07\n1590,9.33,2.4,4\n1600,11.7,2.2,4.97\n1610,6.03,2.54,5.88\n1620,10.56,2.02,5.85\n1630,9.18,2.97,5.24\n1640,7.31,2.52,5.35\n1650,11.65,2.16,5.5\n1660,6.12,2.97,4.1\n1670,6.92,2.81,5.2\n1680,9.01,2.72,5.93\n1690,6.6,2.14,4.29\n1700,10.28,2.1,4.17\n1710,10.77,2.24,5.94\n1720,8.39,2.53,5.92\n1730,11.04,2.53,5.49\n1740,7.07,2.7,5.33\n1750,11.08,2.57,5.74\n1760,7,2.34,5.18\n1770,9.88,2.19,4.63\n1780,10.13,2.81,5.41\n1790,8.57,2.54,5.6\n1800,7.64,2.72,5.47\n1810,10.04,2.16,5.89\n1820,8.14,2.08,5.59\n1830,11.56,2.07,4.25\n1840,9.25,2.01,4.6\n1850,7.06,2.26,4.63\n1860,6.17,2.97,5.41\n1870,7.97,2.84,5.25\n1880,10.37,2.06,4.41\n1890,8.29,2.88,4.85\n1900,10.65,2.72,4.84\n1910,10.66,2.11,4.29\n1920,11.23,2.2,4.21\n1930,10.16,2.15,5.08\n1940,7.2,2.94,5.92\n1950,6.12,2.75,4.47\n1960,8.91,2.79,5.95\n1970,8.91,2.35,4.69\n1980,10.72,2.91,5.73\n1990,7.31,2.33,4.17"}},"type":"shape","subtype":"charts","alias":"Light-memory","size":{"width":{"d":{"v":"1200px","e":true},"n":{"v":"990px"},"t":{"v":"752px"},"m":{"v":"463px"}},"height":{"d":{"v":"700px","e":true},"n":{"v":"578px"},"t":{"v":"439px"},"m":{"v":"270px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":5},"timeline":{"frames":{"frame_0":{"transform":{"y":{"d":{"v":"200%"},"n":{"v":"200%"},"t":{"v":"200%"},"m":{"v":"200%"}},"scaleX":2,"scaleY":2,"opacity":1,"rotationX":"-20deg","rotationY":"-20deg"},"timeline":{"endWithSlide":false,"alias":"Anim From"}},"frame_1":{"timeline":{"ease":"power3.out","speed":1000,"start":590,"startRelative":590,"endWithSlide":false,"frameLength":1000,"alias":"Anim To"}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":910,"start":9000,"startRelative":7410,"endWithSlide":true,"frameLength":910}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":590},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#ffffff","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}},"bgimagelib":""}},
				charts_g_8 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Arvo","x":{"use":false},"y":{"use":false}},"legend":{"color":"#8999af","v":"top","h":"center","xo":"0","yo":"22","gap":"50","font":"Roboto","fontWeight":"400","sbg":false,"e":true},"values":{"font":"Arial, Helvetica, sans-serif","s":{"suf":"GB ","xo":"15","yo":"3","fontWeight":"400","round":true},"x":{"suf":"s","dez":"0","color":"#8999af","size":"10px","xo":"5","fontWeight":"400","every":"20","fr":true,"round":true},"y":{"suf":"GB","dez":"0","color":"#8999af","size":"11px","fontWeight":"400","round":true}},"grid":{"xcolor":"transparent","xsize":"0px","xstcolor":"rgba(43, 63, 94, 0.5)","xstsize":"1px","ycolor":"rgba(43, 63, 94, 0.5)","ysize":"1px","ybtcolor":"transparent","ybtsize":"1px"},"interaction":{"v":{"color":"rgba(79, 79, 79, 0.75)","dash":"2","dphidden":true,"useval":true},"h":{"use":true,"color":"rgba(255,255,255,0.75)","size":1,"dash":"0","fill":"#000","textcolor":"#fff"},"e":true},"settings":{"gap":"1","speed":"1200ms","delay":"400ms","margin":{"top":"50","bottom":"70","left":"50","right":"0"},"usetitle":true},"inuse":[true,true,true,true],"index":[1,"1","30","20"],"strokewidth":[2,"0.5","0.5","0.5"],"strokedash":[0,"0","0","0"],"curves":[0,"0","0",0],"datapoint":[1,"1","1","1"],"strokecolor":["#8B1E3F","#008baa","#b50000","#cc9c00"],"anchorcolor":["#8B1E3F","#008baa","#b50000","#cc9c00"],"fillcolor":["rgba(139, 30, 63, 0.5)","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:0,&g&:149,&b&:175,&a&:0.3,&position&:0,&align&:&top&},{&r&:0,&g&:149,&b&:175,&a&:0.3,&position&:0,&align&:&bottom&},{&r&:0,&g&:149,&b&:175,&a&:0,&position&:100,&align&:&bottom&},{&r&:0,&g&:149,&b&:175,&a&:0,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:181,&g&:0,&b&:0,&a&:0.3,&position&:0,&align&:&top&},{&r&:181,&g&:0,&b&:0,&a&:0.3,&position&:0,&align&:&bottom&},{&r&:181,&g&:0,&b&:0,&a&:0,&position&:100,&align&:&bottom&},{&r&:181,&g&:0,&b&:0,&a&:0,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:204,&g&:156,&b&:0,&a&:0.3,&position&:0,&align&:&top&},{&r&:204,&g&:156,&b&:0,&a&:0.3,&position&:0,&align&:&bottom&},{&r&:204,&g&:156,&b&:0,&a&:0,&position&:100,&align&:&bottom&},{&r&:204,&g&:156,&b&:0,&a&:0,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}"],"valuebgcols":["#8B1E3F","rgba(0, 139, 170, 0.7)","rgba(181, 0, 0, 0.7)","rgba(204, 156, 0, 0.7)"],"valuecolor":["#FFF","#ffffff","#ffffff","#ffffff"],"valuefcolor":["#FFF","#FFF","#FFF","#FFF"],"altcolors":[[],[],[],[]],"altcolorsuse":[false,false,false,false,false,false,false,false,false],"csv":"sec,Cache,Buffer,Used\n10,10.12,2.09,4.96\n20,6.91,2.93,5.62\n30,10.62,2.26,4.39\n40,7.41,2.04,5.33\n50,7.57,2.34,4.63\n60,10.15,2.31,4.6\n70,10.97,2.97,4.62\n80,11.72,2.07,5.86\n90,9.13,2.69,5.69\n100,9.18,2.61,4.75\n110,11.14,2.29,5.54\n120,9.6,2.66,4.02\n130,10.35,2.44,5.2\n140,9.88,2.68,5.49\n150,10.38,2.6,5.66\n160,7.84,2.08,5.6\n170,11.12,2.57,4.04\n180,6.37,2.48,5.91\n190,11.31,2.05,5.08\n200,8.57,2.26,5.34\n210,8.02,2.52,4.52\n220,11.35,2.64,5.79\n230,8.73,2.56,4.48\n240,9.09,2.31,5.04\n250,10.77,2.51,5.95\n260,10.31,2.3,4.75\n270,6.31,2.08,5.27\n280,7.21,2.46,5.6\n290,8.51,2.64,4.98\n300,6.7,2.2,5.92\n310,9.69,2.08,4.99\n320,9.89,2.56,4.41\n330,9.06,2.81,5.66\n340,9.03,2.12,4.79\n350,9.06,2.65,5.62\n360,7.19,2.04,5.08\n370,6.17,2.75,4.06\n380,6.45,2.73,4.78\n390,11.69,2.46,4.17\n400,6.4,2.39,5.46\n410,11.47,2.82,5.32\n420,8.83,2.79,5.31\n430,11.78,2.69,5.92\n440,9.62,2.69,4.13\n450,7.52,2.06,4.91\n460,9.9,2.73,4.43\n470,6.48,2.44,4.75\n480,6.45,2.23,4.77\n490,8.68,2.99,6\n500,8.35,2.95,4.01\n510,6.48,2.94,4.6\n520,6.57,2.59,4.86\n530,9.64,2.28,5.63\n540,8.57,2.78,5.23\n550,9.74,2.03,5.06\n560,7.59,2.96,4.73\n570,8.63,2.26,4.62\n580,9.93,2.7,5.82\n590,6.1,2.33,5.39\n600,11.58,2.67,5.28\n610,8.41,2.83,5.63\n620,6.72,2.81,4.58\n630,8.58,2.22,4.51\n640,7.58,2.56,5.98\n650,8.85,2.09,4.29\n660,7.38,2.77,4.59\n670,11.16,2.58,4.04\n680,7.22,2.4,5.79\n690,8.26,2.58,5.17\n700,9.85,2.76,5.8\n710,7.06,2.61,5.2\n720,8.46,2.76,5.28\n730,11.97,2.22,4.08\n740,10.66,2.89,5.04\n750,6.1,2.26,5.86\n760,6.21,2.71,5.68\n770,6.98,2.34,4.26\n780,10.25,2.93,5.66\n790,10.91,2.51,4.07\n800,8.25,2.65,4.6\n810,6.15,2.21,5.1\n820,6.52,2.29,4.38\n830,10.5,2.17,4.99\n840,9.18,2.46,5.62\n850,11.64,2.31,5.92\n860,7.47,2.7,5.04\n870,7.78,2.52,5.68\n880,8.52,2.77,5.48\n890,10.56,2.65,5.78\n900,7.86,2.53,4.95\n910,9.32,2.57,5.74\n920,7.3,2.54,5.85\n930,10.54,2.7,4.84\n940,6.04,2.41,4.41\n950,9.96,2.72,4.31\n960,6.28,2.46,5.45\n970,7.53,2.96,4.48\n980,7.2,2.52,5.55\n990,10.64,2.38,4.78\n1000,6.59,2.63,5.19\n1010,11.52,2.88,4.92\n1020,7.82,2.61,4.73\n1030,6.87,2.53,5.93\n1040,8.19,2.03,5.91\n1050,10.44,2.04,5.68\n1060,8.61,2.73,5.17\n1070,6.04,2.58,4.22\n1080,7.34,2.3,5.94\n1090,9.07,2.03,5.6\n1100,10.7,2.54,5.86\n1110,9.44,2.54,5.19\n1120,7.41,2.41,4.77\n1130,6.22,2.86,5.33\n1140,10.6,2.72,4.1\n1150,10.41,2.12,4.66\n1160,8.7,2.25,4.85\n1170,7.92,2.26,5.87\n1180,6.68,2.06,4.94\n1190,11.21,2.66,5.89\n1200,10.55,2.03,4.92\n1210,9.88,2.37,5.55\n1220,8.35,2.11,5.9\n1230,11.42,2.25,5.67\n1240,9.61,2.15,5.54\n1250,11.8,2.46,4.75\n1260,9.08,2.06,5.65\n1270,8.71,3,5.46\n1280,10.55,2.97,4.72\n1290,10.15,2.71,5.83\n1300,9.26,2.86,5.66\n1310,10.34,2.84,4.29\n1320,11.89,2.39,5.61\n1330,6.27,2.39,5.07\n1340,8.34,2.01,5.07\n1350,9.51,2.94,4.8\n1360,10.05,2.4,5.94\n1370,7.28,2.62,4.97\n1380,9.52,2.41,5.91\n1390,7.68,2.27,4.35\n1400,11.67,2.65,4.8\n1410,7.04,2.88,4.47\n1420,8.47,2.95,5.5\n1430,7.94,2.52,5.3\n1440,6.12,2.24,5.43\n1450,8.12,2.72,5.62\n1460,7.22,2.86,4.98\n1470,11.72,2.45,5.48\n1480,11.25,2.27,5.79\n1490,8.63,2.09,4.9\n1500,8.6,2.33,5.65\n1510,9.96,2.45,5.86\n1520,8.98,2.03,4.56\n1530,7.05,2.85,5.1\n1540,6.39,2.44,5.74\n1550,11.29,2.7,5.91\n1560,11.47,2.72,4.49\n1570,11.66,2.38,5.61\n1580,9.69,2.98,4.07\n1590,9.33,2.4,4\n1600,11.7,2.2,4.97\n1610,6.03,2.54,5.88\n1620,10.56,2.02,5.85\n1630,9.18,2.97,5.24\n1640,7.31,2.52,5.35\n1650,11.65,2.16,5.5\n1660,6.12,2.97,4.1\n1670,6.92,2.81,5.2\n1680,9.01,2.72,5.93\n1690,6.6,2.14,4.29\n1700,10.28,2.1,4.17\n1710,10.77,2.24,5.94\n1720,8.39,2.53,5.92\n1730,11.04,2.53,5.49\n1740,7.07,2.7,5.33\n1750,11.08,2.57,5.74\n1760,7,2.34,5.18\n1770,9.88,2.19,4.63\n1780,10.13,2.81,5.41\n1790,8.57,2.54,5.6\n1800,7.64,2.72,5.47\n1810,10.04,2.16,5.89\n1820,8.14,2.08,5.59\n1830,11.56,2.07,4.25\n1840,9.25,2.01,4.6\n1850,7.06,2.26,4.63\n1860,6.17,2.97,5.41\n1870,7.97,2.84,5.25\n1880,10.37,2.06,4.41\n1890,8.29,2.88,4.85\n1900,10.65,2.72,4.84\n1910,10.66,2.11,4.29\n1920,11.23,2.2,4.21\n1930,10.16,2.15,5.08\n1940,7.2,2.94,5.92\n1950,6.12,2.75,4.47\n1960,8.91,2.79,5.95\n1970,8.91,2.35,4.69\n1980,10.72,2.91,5.73\n1990,7.31,2.33,4.17"}},"type":"shape","subtype":"charts","alias":"Light-memory","size":{"width":{"d":{"v":"1200px","e":true},"n":{"v":"990px"},"t":{"v":"752px"},"m":{"v":"463px"}},"height":{"d":{"v":"700px","e":true},"n":{"v":"578px"},"t":{"v":"439px"},"m":{"v":"270px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":5},"timeline":{"frames":{"frame_0":{"transform":{"y":{"d":{"v":"200%"},"n":{"v":"200%"},"t":{"v":"200%"},"m":{"v":"200%"}},"scaleX":2,"scaleY":2,"opacity":1,"rotationX":"-20deg","rotationY":"-20deg"},"timeline":{"endWithSlide":false,"alias":"Anim From"}},"frame_1":{"timeline":{"ease":"power3.out","speed":1000,"start":590,"startRelative":590,"endWithSlide":false,"frameLength":1000,"alias":"Anim To"}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":910,"start":9000,"startRelative":7410,"endWithSlide":true,"frameLength":910}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":590},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#001021","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}},"bgimagelib":""}},
				charts_g_9 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"PT Sans Narrow","x":{"name":"MMbbl = one million barrels","color":"#86a1aa","size":"18px","yo":"25","fontWeight":"400","font":"Nunito"},"y":{"name":"Reserves (MMBBL)","color":"#86a1aa","size":"18px","xo":"35","fontWeight":"400"}},"legend":{"use":false},"values":{"font":"PT Sans Narrow","s":{"suf":"MMBL","size":"14px","yo":"-20","direction":"middle","fontWeight":"400","paddingh":"10px","paddingv":"10px"},"f":{"fontWeight":"400"},"x":{"color":"#86a1aa","size":"16px","yo":"10","fontWeight":"400","every":"1"},"y":{"suf":"L","dez":"0","color":"#86a1aa","size":"16px","xo":"-10","fontWeight":"400"}},"grid":{"xuse":false,"xcolor":"#cecece","xsize":"1px","xstcolor":"transparent","xstsize":"0px","ycolor":"rgba(255, 255, 255, 0.1)","ysize":"1px","ybtcolor":"transparent","ybtsize":"0px"},"interaction":{"v":{"use":false,"usexval":false},"e":true},"settings":{"type":"bar","gap":"100","width":"1200","height":"700","isx":"1","delay":"0ms","margin":{"top":"100","bottom":"100","left":"150","right":"50"}},"inuse":[true,true],"index":[0,1],"strokewidth":[1,1],"strokedash":[1,1],"curves":[0,0],"datapoint":["3","1"],"strokecolor":["transparent","#3C153B"],"anchorcolor":["#007aff","#3C153B"],"fillcolor":["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:239,&g&:125,&b&:55,&a&:1,&position&:0,&align&:&top&},{&r&:239,&g&:125,&b&:55,&a&:1,&position&:0,&align&:&bottom&},{&r&:206,&g&:30,&b&:30,&a&:1,&position&:100,&align&:&bottom&},{&r&:206,&g&:30,&b&:30,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","rgba(60, 21, 59, 0.5)"],"valuebgcols":["rgba(12, 12, 12, 0.85)","#3C153B"],"valuecolor":["#ffffff","#FFF"],"valuefcolor":["#FFF","#FFF"],"altcolors":[["#ff7715","#ff852d","#ffaa6d","#ff7715","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D"],[]],"altcolorsuse":[false,false],"csv":"Reserves(MMbbl),Country\n304300,Venezuela\n248100, Saudi Arabia\n170200, Canada\n156300, Iran\n145200, Iraq\n107300, Russia\n97500, Kuwait\n88000, Arab Emirates"}},"type":"shape","subtype":"charts","alias":"Bar graph dark","size":{"width":{"d":{"v":"1215px"},"n":{"v":"1003px"},"t":{"v":"762px"},"m":{"v":"470px"}},"height":{"d":{"v":"721px"},"n":{"v":"595px"},"t":{"v":"452px"},"m":{"v":"278px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"endWithSlide":false},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_1":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"speed":1010,"start":640,"startRelative":640,"endWithSlide":false,"frameLength":1010},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"{&type&:&radial&,&angle&:&0&,&colors&:[{&r&:0,&g&:42,&b&:61,&a&:1,&position&:0,&align&:&top&},{&r&:0,&g&:42,&b&:61,&a&:1,&position&:0,&align&:&bottom&},{&r&:3,&g&:23,&b&:35,&a&:1,&position&:100,&align&:&bottom&},{&r&:3,&g&:23,&b&:35,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_10 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"PT Sans Narrow","x":{"name":"MMbbl = one million barrels","color":"#000000","size":"16px","yo":"25","fontWeight":"700","font":"Nunito"},"y":{"name":"Reserves (MMBBL)","color":"#000000","size":"18px","xo":"25","fontWeight":"700"}},"legend":{"use":false},"values":{"font":"PT Sans Narrow","s":{"suf":"MMBL","size":"12px","yo":"-20","direction":"middle","fontWeight":"700","paddingh":"10px","paddingv":"10px"},"f":{"fontWeight":"400"},"x":{"color":"#000000","size":"15px","yo":"10","fontWeight":"400","every":"1"},"y":{"suf":"L","dez":"0","color":"#000000","size":"15px","xo":"-10","fontWeight":"700"}},"grid":{"xcolor":"#cecece","xsize":"1px","xstcolor":"transparent","xstsize":"1px","ycolor":"#cecece","ydivide":"8","ybtcolor":"#000000"},"interaction":{"v":{"use":false,"usexval":false},"e":true},"settings":{"type":"bar","gap":"120","width":"1200","height":"700","isx":"1","delay":"0ms","margin":{"top":"100","bottom":"100","left":"150","right":"50"}},"inuse":[true,true],"index":[0,1],"strokewidth":[1,1],"strokedash":[1,1],"curves":[0,0],"datapoint":["3","1"],"strokecolor":["transparent","#3C153B"],"anchorcolor":["#007aff","#3C153B"],"fillcolor":["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","rgba(60, 21, 59, 0.5)"],"valuebgcols":["#ffffff","#3C153B"],"valuecolor":["#000000","#FFF"],"valuefcolor":["#FFF","#FFF"],"altcolors":[["#ff7715","#ff852d","#ffaa6d","#ff7715","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D"],[]],"altcolorsuse":[false,false],"csv":"Reserves(MMbbl),Country\n304300,Venezuela\n248100, Saudi Arabia\n170200, Canada\n156300, Iran\n145200, Iraq\n107300, Russia\n97500, Kuwait\n88000, Arab Emirates"}},"type":"shape","subtype":"charts","alias":"Bar graph light","size":{"width":{"d":{"v":"1215px"},"n":{"v":"1003px"},"t":{"v":"762px"},"m":{"v":"470px"}},"height":{"d":{"v":"721px"},"n":{"v":"595px"},"t":{"v":"452px"},"m":{"v":"278px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"v":"-1px","e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"endWithSlide":false},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_1":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"speed":1010,"start":640,"startRelative":640,"endWithSlide":false,"frameLength":1010},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#ffffff","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},

				charts_g_11 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Open Sans","x":{"name":"Simple Line Graph","color":"rgba(255, 255, 255, 0.7)","size":"18px","yo":"35","fontWeight":"600","font":"Nunito"},"y":{"use":false}},"legend":{"use":false},"values":{"font":"Roboto","s":{"size":"15px","yo":"-20","direction":"middle","paddingh":"15px","paddingv":"3px"},"x":{"color":"rgba(255, 255, 255, 0.5)","size":"15px","yo":"10","fontWeight":"400","every":"1"},"y":{"dez":"0","color":"rgba(255, 255, 255, 0.5)","size":"15px","fontWeight":"700"}},"grid":{"xcolor":"rgba(173, 173, 173, 0.5)","xsize":"1px","xstcolor":"#878181","xstsize":"1px","ycolor":"rgba(135, 129, 129, 0.5)","ysize":"1px","ybtcolor":"transparent","ybtsize":"1px"},"interaction":{"v":{"usexval":false,"color":"rgba(255, 255, 255, 0.3)"},"e":true},"settings":{"gap":"30","width":"1200","height":"700","delay":"0ms","margin":{"top":"50","bottom":"110","left":"100","right":"50"}},"inuse":[true,true,true],"index":[0,1,2],"strokewidth":[1,"3","3"],"strokedash":[1,"0","0"],"curves":[0,"0","0"],"datapoint":["3","2","2"],"strokecolor":["transparent","rgba(255, 255, 255, 0.5)","rgba(255, 255, 255, 0.5)"],"anchorcolor":["#007aff","#ffffff","#ffffff"],"fillcolor":["#ff3a2d","rgba(255, 255, 255, 0)","rgba(137, 189, 158, 0)"],"valuebgcols":["#ffffff","#ffffff","#ffffff"],"valuecolor":["#000000","#000000","#000000"],"valuefcolor":["#FFF","#FFF","#FFF"],"altcolors":[["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B"],["#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40","#3772FF","#70E4EF","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40"],[]],"altcolorsuse":[true,true,false,false,false],"csv":"x,y,z\n10,58,30\n20,32,20\n30,75,50\n40,92,44\n50,10,33\n60,60,21\n70,77,35\n80,33,77\n90,29,90\n100,80,45\n"}},"type":"shape","subtype":"charts","alias":"Simple-line-mono-dark","size":{"width":{"d":{"v":"1200px","e":true},"n":{"v":"990px"},"t":{"v":"752px"},"m":{"v":"463px"}},"height":{"d":{"v":"720px","e":true},"n":{"v":"594px"},"t":{"v":"451px"},"m":{"v":"278px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"v":"-1px","e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"endWithSlide":false},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_1":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"speed":1010,"start":640,"startRelative":640,"endWithSlide":false,"frameLength":1010},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#00112d","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_12 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Open Sans","x":{"name":"Simple Line Graph","color":"#00112d","size":"18px","yo":"35","fontWeight":"600","font":"Nunito"},"y":{"use":false}},"legend":{"use":false},"values":{"font":"Roboto","s":{"size":"15px","yo":"-20","direction":"middle","paddingh":"15px","paddingv":"3px"},"x":{"color":"rgba(0, 17, 45, 0.5)","size":"15px","yo":"10","fontWeight":"400","every":"1"},"y":{"dez":"0","color":"rgba(0, 17, 45, 0.5)","size":"15px","xo":"-10","fontWeight":"700"}},"grid":{"xcolor":"rgba(173, 173, 173, 0.5)","xsize":"1px","xstcolor":"#878181","xstsize":"1px","ycolor":"rgba(135, 129, 129, 0.5)","ysize":"1px","ybtcolor":"transparent","ybtsize":"1px"},"interaction":{"v":{"use":false,"usexval":false},"e":true},"settings":{"gap":"30","width":"1200","height":"700","delay":"0ms","margin":{"top":"50","bottom":"110","left":"100","right":"50"}},"inuse":[true,true,true],"index":[0,1,2],"strokewidth":[1,"3","3"],"strokedash":[1,"0","0"],"curves":[0,"0","0"],"datapoint":["3","2","2"],"strokecolor":["transparent","rgba(0, 17, 45, 0.5)","rgba(0, 17, 45, 0.5)"],"anchorcolor":["#007aff","#00112d","#00112d"],"fillcolor":["#ff3a2d","rgba(255, 255, 255, 0)","rgba(137, 189, 158, 0)"],"valuebgcols":["#ffffff","#00112d","#00112d"],"valuecolor":["#000000","#ffffff","#ffffff"],"valuefcolor":["#FFF","#FFF","#FFF"],"altcolors":[["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B"],["#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40","#3772FF","#70E4EF","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40"],[]],"altcolorsuse":[true,true,false,false,false],"csv":"x,y,z\n10,38,30\n20,16,35\n30,75,55\n40,82,24\n50,10,63\n60,60,21\n70,67,45\n80,33,77\n90,22,70\n100,80,45\n"}},"type":"shape","subtype":"charts","alias":"Simple-line-mono-light","size":{"width":{"d":{"v":"1200px","e":true},"n":{"v":"990px"},"t":{"v":"752px"},"m":{"v":"463px"}},"height":{"d":{"v":"720px","e":true},"n":{"v":"594px"},"t":{"v":"451px"},"m":{"v":"278px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"v":"-1px","e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"endWithSlide":false},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_1":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"speed":1010,"start":640,"startRelative":640,"endWithSlide":false,"frameLength":1010},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#ffffff","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_13 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Open Sans","x":{"name":"Simple Line Graph","color":"#00112d","size":"18px","yo":"35","fontWeight":"600","font":"Nunito"},"y":{"use":false}},"legend":{"use":false},"values":{"font":"Roboto","s":{"size":"15px","yo":"-20","direction":"middle","paddingh":"15px","paddingv":"3px"},"x":{"color":"rgba(0, 17, 45, 0.5)","size":"15px","yo":"10","fontWeight":"400","every":"1"},"y":{"dez":"0","color":"rgba(0, 17, 45, 0.5)","size":"15px","xo":"-10","fontWeight":"700"}},"grid":{"xcolor":"rgba(173, 173, 173, 0.5)","xsize":"1px","xstcolor":"#878181","xstsize":"1px","ycolor":"rgba(135, 129, 129, 0.5)","ysize":"1px","ybtcolor":"transparent","ybtsize":"1px"},"interaction":{"v":{"usexval":false,"color":"rgba(12, 12, 12, 0.3)"},"e":true},"settings":{"gap":"30","width":"1200","height":"700","delay":"0ms","margin":{"top":"50","bottom":"110","left":"100","right":"50"}},"inuse":[true,false,true],"index":[0,1,2],"strokewidth":[1,"3","4"],"strokedash":[1,"0","0"],"curves":[0,"0","2"],"datapoint":["3","2","4"],"strokecolor":["transparent","rgba(0, 17, 45, 0.5)","#47c9cc"],"anchorcolor":["#007aff","#00112d","#00112d"],"fillcolor":["#ff3a2d","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:0,&g&:17,&b&:45,&a&:0.1,&position&:0,&align&:&bottom&},{&r&:0,&g&:17,&b&:45,&a&:0.1,&position&:0,&align&:&top&},{&r&:0,&g&:17,&b&:45,&a&:0.05,&position&:100,&align&:&top&},{&r&:0,&g&:17,&b&:45,&a&:0.05,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:71,&g&:201,&b&:204,&a&:0.15,&position&:0,&align&:&bottom&},{&r&:71,&g&:201,&b&:204,&a&:0.15,&position&:0,&align&:&top&},{&r&:71,&g&:201,&b&:204,&a&:0.05,&position&:100,&align&:&top&},{&r&:71,&g&:201,&b&:204,&a&:0.05,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}"],"valuebgcols":["#ffffff","#00112d","#00112d"],"valuecolor":["#000000","#ffffff","#ffffff"],"valuefcolor":["#FFF","#FFF","#FFF"],"altcolors":[["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B"],["#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40","#3772FF","#70E4EF","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40"],[]],"altcolorsuse":[true,true,false,false,false],"csv":"x,y,z\n10,38,30\n20,16,35\n30,75,55\n40,82,24\n50,10,63\n60,60,21\n70,67,45\n80,33,77\n90,22,70\n100,80,45\n"}},"type":"shape","subtype":"charts","alias":"Single-line-mono-curved","size":{"width":{"d":{"v":"1200px","e":true},"n":{"v":"990px"},"t":{"v":"752px"},"m":{"v":"463px"}},"height":{"d":{"v":"720px","e":true},"n":{"v":"594px"},"t":{"v":"451px"},"m":{"v":"278px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"v":"-1px","e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"endWithSlide":false},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_1":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"speed":1010,"start":640,"startRelative":640,"endWithSlide":false,"frameLength":1010},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#ffffff","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},
				charts_g_14 : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Open Sans","x":{"use":false},"y":{"use":false}},"legend":{"use":false},"values":{"font":"Arial, Helvetica, sans-serif","s":{"xo":"15","yo":"3","fontWeight":"400","round":true},"x":{"dez":"0","color":"rgba(255, 255, 255, 0.45)","size":"10px","yo":"10","fontWeight":"400","every":"1","fr":true,"round":true},"y":{"suf":"0","dez":"0","color":"rgba(255, 255, 255, 0.45)","size":"11px","fontWeight":"400","round":true}},"grid":{"xcolor":"transparent","xsize":"0px","xstcolor":"rgba(255, 255, 255, 0.15)","xstsize":"1px","ycolor":"rgba(255, 255, 255, 0.15)","ysize":"1px","ybtcolor":"transparent","ybtsize":"1px"},"interaction":{"v":{"usexval":false,"color":"rgba(255, 255, 255, 0.3)","dash":"2","dphidden":true,"useval":true},"h":{"use":true,"color":"rgba(255,255,255,0.75)","size":1,"dash":"0","fill":"#000","textcolor":"#fff"},"e":true},"settings":{"gap":"1","speed":"1200ms","delay":"400ms","margin":{"top":"50","bottom":"70","left":"50","right":"50"},"usetitle":true},"inuse":[true,true,true,true],"index":[1,"1","30",3],"strokewidth":[2,"1","1",1],"strokedash":[0,"2","2","2"],"curves":[0,"2","2","2"],"datapoint":[1,"1","1","1"],"strokecolor":["#8B1E3F","#c464af","#ffd738","#5ac8fa"],"anchorcolor":["#8B1E3F","#c464af","#ffd738","#5ac8fa"],"fillcolor":["rgba(139, 30, 63, 0.5)","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:196,&g&:0,&b&:153,&a&:0.7,&position&:0,&align&:&top&},{&r&:196,&g&:0,&b&:153,&a&:0.7,&position&:0,&align&:&bottom&},{&r&:61,&g&:0,&b&:142,&a&:0,&position&:100,&align&:&bottom&},{&r&:61,&g&:0,&b&:142,&a&:0,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:243,&g&:255,&b&:76,&a&:0.7,&position&:0,&align&:&top&},{&r&:243,&g&:255,&b&:76,&a&:0.7,&position&:0,&align&:&bottom&},{&r&:106,&g&:163,&b&:1,&a&:0,&position&:100,&align&:&top&},{&r&:106,&g&:163,&b&:1,&a&:0,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:76,&g&:195,&b&:255,&a&:0.7,&position&:0,&align&:&top&},{&r&:76,&g&:195,&b&:255,&a&:0.7,&position&:0,&align&:&bottom&},{&r&:1,&g&:109,&b&:155,&a&:0,&position&:100,&align&:&top&},{&r&:1,&g&:109,&b&:155,&a&:0,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}"],"valuebgcols":["#8B1E3F","rgba(0, 0, 0, 0.5)","rgba(12, 12, 12, 0.5)","rgba(12, 7, 7, 0.5)"],"valuecolor":["#FFF","#ffffff","#ffffff","#FFF"],"valuefcolor":["#FFF","#FFF","#FFF","#FFF"],"altcolors":[[],[],[],[]],"altcolorsuse":[false,false,false,false,false,false,false,false,false],"csv":"x,y,z,p\n10,55,30,40\n20,66,25,50\n30,75,35,52\n40,77,34,42\n50,69,23,39\n60,60,21,53\n70,67,28,33\n80,53,17,35\n90,82,30,65\n100,69,24,49\n"}},"type":"shape","subtype":"charts","alias":"Dark-curved-dotted","size":{"width":{"d":{"v":"100%","e":true},"n":{"v":"100%"},"t":{"v":"100%"},"m":{"v":"100%"}},"height":{"d":{"v":"100%","e":true},"n":{"v":"100%"},"t":{"v":"100%"},"m":{"v":"100%"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":true},"position":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":5},"behavior":{"baseAlign":"slide"},"timeline":{"frames":{"frame_0":{"transform":{"y":{"d":{"v":"200%"},"n":{"v":"200%"},"t":{"v":"200%"},"m":{"v":"200%"}},"scaleX":2,"scaleY":2,"opacity":1,"rotationX":"-20deg","rotationY":"-20deg"},"timeline":{"endWithSlide":false,"alias":"Anim From"}},"frame_1":{"timeline":{"ease":"power3.out","speed":1000,"start":590,"startRelative":590,"endWithSlide":false,"frameLength":1000,"alias":"Anim To"}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":910,"start":9000,"startRelative":7410,"endWithSlide":true,"frameLength":910}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":590},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"#0d001e","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}},"bgimagelib":""}},

				charts_b : {"addOns":{"revslider-charts-addon":{"labels":{"font":"PT Sans Narrow","x":{"name":"MMbbl = one million barrels","color":"#000000","size":"16px","yo":"25","fontWeight":"700","font":"Nunito"},"y":{"name":"Reserves (MMBBL)","color":"#000000","size":"18px","xo":"25","fontWeight":"700"}},"legend":{"use":false},"values":{"font":"PT Sans Narrow","s":{"suf":"MMBL","size":"12px","yo":"-20","direction":"middle","fontWeight":"700","paddingh":"10px","paddingv":"10px"},"x":{"color":"#000000","size":"15px","yo":"10","fontWeight":"400","every":"1"},"y":{"suf":"L","dez":"0","color":"#000000","size":"15px","xo":"-10","fontWeight":"700"}},"grid":{"xcolor":"#000000","xsize":"1px","xstcolor":"transparent","xstsize":"1px","ycolor":"#000000","ydivide":"5","ybtcolor":"#000000"},"interaction":{"v":{"use":false,"usexval":false},"e":true},"settings":{"type":"bar","gap":"20","width":"1200","height":"700","isx":"1","delay":"0ms","margin":{"top":"100","bottom":"100","left":"150","right":"50"}},"inuse":[true,true],"index":[0,1],"strokewidth":[1,1],"strokedash":[1,1],"curves":[0,0],"datapoint":["3","1"],"strokecolor":["transparent","#3C153B"],"anchorcolor":["#007aff","#3C153B"],"fillcolor":["#ff3a2d","rgba(60, 21, 59, 0.5)"],"valuebgcols":["#ffffff","#3C153B"],"valuecolor":["#000000","#FFF"],"csv":"Reserves(MMbbl),Country\n304300,Venezuela\n248100, Saudi Arabia\n170200, Canada\n156300, Iran\n145200, Iraq\n107300, Russia\n97500, Kuwait\n88000, Arab Emirates","altcolors":[["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D"],[]],"altcolorsuse":[true,false]}},"type":"shape","subtype":"charts","alias":"Shape-1","size":{"width":{"d":{"v":"1215px","e":true},"n":{"v":"1003px"},"t":{"v":"762px"},"m":{"v":"470px"}},"height":{"d":{"v":"721px","e":true},"n":{"v":"595px"},"t":{"v":"452px"},"m":{"v":"278px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"horizontal":{"d":{"v":"center","e":true},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle","e":true},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"endWithSlide":false},"chars":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_1":{"transform":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"speed":1010,"start":640,"startRelative":640,"endWithSlide":false,"frameLength":1010},"chars":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px","e":true},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_999":{"transform":{"x":{"d":{"e":true}},"y":{"d":{"e":true}},"opacity":0},"mask":{"x":{"d":{"e":true}},"y":{"d":{"e":true}}},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170},"chars":{"x":{"d":{"e":true}},"y":{"d":{"e":true}}},"words":{"x":{"d":{"e":true}},"y":{"d":{"e":true}}},"lines":{"x":{"d":{"e":true}},"y":{"d":{"e":true}}}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":true},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"rgba(255, 255, 255, 0.75)","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},				
				charts_d : {"addOns":{"revslider-charts-addon":{"labels":{"font":"Open Sans","x":{"name":"SIMPLE BAR GRAPH","color":"#000000","size":"20px","yo":"25","fontWeight":"700","font":"Nunito"},"y":{"color":"#000000","size":"20px","xo":"35","fontWeight":"700"}},"legend":{"use":false},"values":{"font":"Roboto","s":{"size":"15px","yo":"-20","direction":"middle","paddingh":"15px","paddingv":"3px"},"x":{"color":"#000000","size":"15px","yo":"10","fontWeight":"400","every":"1"},"y":{"dez":"0","color":"#000000","size":"15px","xo":"-10","fontWeight":"700"}},"grid":{"xcolor":"#000000","xsize":"1px","xstcolor":"transparent","xstsize":"0px","ycolor":"#343536","ybtcolor":"#343536","ybtsize":"1px"},"interaction":{"v":{"use":false,"usexval":false},"e":true},"settings":{"type":"bar","gap":"30","width":"1200","height":"700","delay":"0ms","margin":{"top":"100","bottom":"100","left":"100","right":"50"}},"inuse":[true,true],"index":[0,1],"strokewidth":[1,"2"],"strokedash":[1,"0"],"curves":[0,0],"datapoint":["3","2"],"strokecolor":["transparent","#ff3a2d"],"anchorcolor":["#007aff","#000000"],"fillcolor":["#ff3a2d","#34aadc"],"valuebgcols":["#ffffff","#ffffff"],"valuecolor":["#000000","#000000"],"altcolors":[["{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:1,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:1,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:1,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:1,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:1,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:1,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:1,&position&:100,&align&:&bottom&}],&easing&:&none&,&strength&:100}","{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:1,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B"],["#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40","#3772FF","#70E4EF","#A1C181","#233D4D","#FE7F2D","#619B8A","#8B1E3F","#3C153B","#89BD9E","#F0C987","#DB4C40"]],"altcolorsuse":[true,true,false,false,false],"csv":"x,y\n10,58\n20,32\n30,75\n40,92\n50,10\n60,60\n70,47\n80,33\n90,29\n100,80\n110,75\n120,25\n130,48\n140,75\n150,33"}},"type":"shape","subtype":"charts","alias":"Shape-1","size":{"width":{"d":{"v":"1215px"},"n":{"v":"1003px"},"t":{"v":"762px"},"m":{"v":"470px"}},"height":{"d":{"v":"721px"},"n":{"v":"595px"},"t":{"v":"452px"},"m":{"v":"278px"}},"maxWidth":{"d":{"e":true}},"maxHeight":{"d":{"e":true}},"minWidth":{"d":{"e":true}},"minHeight":{"d":{"e":true}},"originalWidth":"300px","originalHeight":"180px","aspectRatio":{"d":{"v":1.6666666666666667},"n":{"v":1.6666666666666667},"t":{"v":1.6666666666666667},"m":{"v":1.6666666666666667}},"scaleProportional":false},"position":{"x":{"d":{"v":"14px","e":true},"n":{"v":"11px"},"t":{"v":"8px"},"m":{"v":"4px"}},"y":{"d":{"v":"-61px","e":true},"n":{"v":"-50px"},"t":{"v":"-37px"},"m":{"v":"-22px"}},"horizontal":{"d":{"v":"center"},"n":{"v":"center"},"t":{"v":"center"},"m":{"v":"center"}},"vertical":{"d":{"v":"middle"},"n":{"v":"middle"},"t":{"v":"middle"},"m":{"v":"middle"}},"zIndex":8},"timeline":{"frames":{"frame_0":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"endWithSlide":false},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_1":{"transform":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"mask":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"timeline":{"speed":1010,"start":640,"startRelative":640,"endWithSlide":false,"frameLength":1010},"chars":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"words":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}},"lines":{"x":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}},"y":{"d":{"v":"0px"},"n":{"v":"0px"},"t":{"v":"0px"},"m":{"v":"0px"}}}},"frame_999":{"transform":{"opacity":0},"timeline":{"speed":1170,"start":9000,"startRelative":7350,"endWithSlide":true,"frameLength":1170}}},"hoverFilterUsed":false,"frameOrder":[{"id":"frame_0","start":-1},{"id":"frame_1","start":640},{"id":"frame_999","start":9000}],"split":false,"sessionFilterUsed":false},"idle":{"margin":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"padding":{"d":{"v":[0,0,0,0],"e":false},"n":{"v":[0,0,0,0],"e":false},"t":{"v":[0,0,0,0],"e":false},"m":{"v":[0,0,0,0],"e":false}},"fontSize":{"n":{"v":"16"},"t":{"v":"12"},"m":{"v":"7"}},"lineHeight":{"n":{"v":"20"},"t":{"v":"15"},"m":{"v":"9"}},"backgroundColor":"{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:247,&g&:247,&b&:247,&a&:1,&position&:0,&align&:&top&},{&r&:247,&g&:247,&b&:247,&a&:1,&position&:0,&align&:&bottom&},{&r&:215,&g&:215,&b&:215,&a&:1,&position&:100,&align&:&bottom&},{&r&:215,&g&:215,&b&:215,&a&:1,&position&:100,&align&:&top&}],&easing&:&none&,&strength&:100}","borderRadius":{"v":[0,0,0,0]},"borderWidth":[0,0,0,0],"whiteSpace":{"d":{"v":"full"},"n":{"v":"full"},"t":{"v":"full"},"m":{"v":"full"}}}},				
				
			}
				
		// INITIALISE THE ADDON
		RVS.DOC.on(slug+'_init',function() {			
			
			if(!addon.initialised) {
				
				// CREATE CONTAINERS				
				RVS.F.addOnContainer.create({slug: slug, icon:'leaderboard', title:bricks.charts, alias:bricks.charts, layer:true});				
				
				// PICK THE CONTAINERS WE NEED	
				addon.forms = {
					
					layergeneral: $('#form_layerinner_' + slug),
					layericon: $('#gst_layer_' + slug),
					layer: $('#form_layer_' + slug),
					
				};
				
				// ADD ADDON HTML
				createLayerSettingsFields();
				
				// INIT
				addEvents();
				initInputs();	
				initHelp();	
				extendLayerTypes();
				addon.initialised = true;
				RVS.S.chartsAddonFirstInitialisaion = false;
				buildAllDataStructure();
				
			} else {				
				if(!addon.initialised) {				
					// DISABLE THINGS		
					punchgs.TweenLite.set(addon.forms.layericon,{display: 'none'});			
					$(addon.forms.layericon).removeClass('selected');	
					addon.forms.layer.addClass('collapsed');
					$('body').addClass('charts-disabled');
					// hide help definitions
					if(typeof HelpGuide !== 'undefined') HelpGuide.deactivate('charts_addon');
				}				
			}	
			
		});

		// Build Structures and Draf All Charts
		function buildAllDataStructure() {

			if (RVS.L===undefined) {
				setTimeout(function() {
					requestAnimationFrame(function() { buildAllDataStructure();});
				},100);
				return;
			}
			for (var id in RVS.L) {				
				if (!RVS.L.hasOwnProperty(id)) continue;				
				if (RVS.L[id]!==undefined && RVS.L[id].addOns!==undefined && RVS.L[id].subtype=="charts") buildDataStructure(id);
			}
			requestAnimationFrame(function() {
				RVS.S.chartsAddonFirstInitialisation = true;
			});
		}

		
		//EXTEND LAYER TYPES
		function extendLayerTypes() {
			RVS.S.layerIcons = jQuery.extend(true, RVS.S.layerIcons, {
				charts: "leaderboard"
			});
			
			RVS.F.extendLayerTypes({ 
			
				icon: 'leaderboard',
				type: 'shape', 
				subtype: 'charts',
				alias: 'Charts',
				subdrop:true,

				subList:{
					charts_g_1 : { name:"Line Chart - Light Gradient", icon:"show_chart"},
					charts_g_2 : { name:"Line Chart - Dark Gradient", icon:"multiline_chart"},
					charts_g_3 : { name:"Line Chart - Light Temperature", icon:"thermostat"},
					charts_g_4 : { name:"Line Chart - Dark Temperature", icon:"thermostat"},
					charts_g_7 : { name:"Line Chart - Light Memory Usage", icon:"stacked_line_chart"},
					charts_g_8 : { name:"Line Chart - Dark Memory Usage", icon:"stacked_line_chart"},
					charts_g_11 : { name:"Line Chart - Mono Dark", icon:"show_chart"},
					charts_g_12 : { name:"Line Chart - Mono Light", icon:"show_chart"},
					charts_g_13 : { name:"Line Chart - Mono Curved Light", icon:"show_chart"},
					charts_g_14 : { name:"Line Chart - Curved Dotted Dark", icon:"show_chart"},
										
					charts_g_5 : { name:"Bars - Light Comparison", icon:"insert_chart"},
					charts_g_6 : { name:"Bars - Dark Comparison", icon:"insert_chart"},
					charts_g_9 : { name:"Bar - Dark Graph", icon:"leaderboard"},
					charts_g_10 : { name:"Bar - Light Graph", icon:"leaderboard"},
					charts_b : { name:"Bar Chart - Gradient", icon:"leaderboard"},					
					charts_d : { name:"Bar Chart - Simple", icon:"insert_chart"},
					
					
				},
				extension: { 
					addOns: { 'revslider-charts-addon' : RVS._R.chartsBuild.getBasics()},
					runtime: {internalClass: 'tp-shape tp-shapewrapper tp-charts'}							
				}						
			});			
		}

		var _svg = {
			lto : function(x,y,xt,yt) {
				xt = xt===undefined ? RVS._R.chartsBuild.xcache || 0 : xt;
				yt = yt===undefined ? RVS._R.chartsBuild.ycache || 0 : yt;
				RVS._R.chartsBuild.xcache = x;
				RVS._R.chartsBuild.ycache = y;
				return '<line x1="'+x+'" y1="'+y+'" x2="'+xt+'", y2="'+yt+'"></line>';
			}
		};
				

		function buildFontColorSize(l,icona,iconb,labela,labelb,nodot) {			
			labela = labela===undefined ? '<label_icon class="'+(icona===undefined ? 'ui_bg' : icona)+'"></label_icon>' : '<label_a>'+labela+'</label_a>';
			labelb = labelb===undefined ? '<label_icon class="'+(iconb===undefined ? 'ui_fontsize' : iconb)+'"></label_icon>' : '<label_a>'+labelb+'</label_a>';
			l = nodot ? l : l+'.';
			return 	'<row class="directrow"><onelong>'+labela+'<input class="layerinput my-color-field easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-editing="Charts Label Color" name="charts_label_color" data-mode="single" data-r="addOns.'+slug+'.'+l+'color" type="text"></onelong><oneshort>'+labelb+'<input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.'+l+'size" data-max="500" type="text"></oneshort></row>';
					
		}
		function buildName(l) {
			return '<label_a>'+bricks.name+'</label_a><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.'+l+'.name"  type="text">';
		}
		function buildXYOffset(a,l) {
			return '<row class="directrow"><onelong><label_icon class="ui_x"></label_icon><input id="'+a+'_pos_x" class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.'+l+'.xo"  type="text"></onelong><oneshort><label_icon class="ui_y"></label_icon><input id="'+a+'_pos_y" class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.'+l+'.yo"  type="text"></oneshort></row>';
		}
		function buildAlignOffset(a,l,name) {
			
			return	(name!==false ? buildName(l) : '') + 
					buildFontColorSize(l) + 
					'<div class="div15"></div>' +
					'<select style="display:none !important" id="'+a+'_pos_halign" data-unselect=".'+a+'_hor_selector" data-select="#'+a+'_hor_*val*" class="layerinput easyinit" data-r="addOns.'+slug+'.'+l+'.h" data-triggerinp="#'+a+'_pos_x" data-triggerinpval="0"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select><select style="display:none !important" id="'+a+'_pos_valign" data-unselect=".'+a+'_ver_selector" data-select="#'+a+'_ver_*val*" class="layerinput easyinit" data-r="addOns.'+slug+'.'+l+'.v" data-triggerinp="#'+a+'_pos_y" data-triggerinpval="0"><option value="top">Top</option><option value="middle">Center</option><option value="bottom">Bottom</option></select>'+
					'<row><onelabel><label_a>Alignment</label_a></onelabel><oneshort><label_icon class="triggerselect ui_leftalign   '+a+'_hor_selector selected" data-select="#'+a+'_pos_halign" data-val="left"  id="'+a+'_hor_left"></label_icon><label_icon class="triggerselect ui_centeralign '+a+'_hor_selector" 		  data-select="#'+a+'_pos_halign" data-val="center" id="'+a+'_hor_center"></label_icon><label_icon class="triggerselect ui_rightalign  '+a+'_hor_selector" 		  data-select="#'+a+'_pos_halign" data-val="right"  id="'+a+'_hor_right"></label_icon></oneshort>'+
					'<oneshort class="lp10"><label_icon class="triggerselect ui_topalign    '+a+'_ver_selector selected" data-select="#'+a+'_pos_valign" data-val="top" 	id="'+a+'_ver_top"></label_icon><label_icon class="triggerselect ui_middlealign '+a+'_ver_selector" 		  data-select="#'+a+'_pos_valign" data-val="middle" 	id="'+a+'_ver_middle"></label_icon><label_icon class="triggerselect ui_bottomalign '+a+'_ver_selector" 		  data-select="#'+a+'_pos_valign" data-val="bottom" 	id="'+a+'_ver_bottom"></label_icon></oneshort>'+
					'</row>' +
					buildXYOffset(a,l);
		}

		function buildPadding(a) {
			return '<row class="directrow">'+
					'<onelong><label_icon class="ui_padding_top"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent"  data-evt="chartsupdate" data-allowed="px"  data-numeric="true" data-r="addOns.'+slug+'.'+a+'.paddingv" data-min="0" data-max="50" type="text"></onelong>'+
					'<oneshort><label_icon class="ui_padding_right"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed="px" data-numeric="true"  data-r="addOns.'+slug+'.'+a+'.paddingh" data-min="0" data-max="50" type="text"></oneshort>'+
					'</row>';
		}

		function buildDez(a) {
			return '<label_a>'+bricks.dez+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed="" data-numeric="true"  data-r="addOns.'+slug+'.'+a+'.dez" data-min="0" data-max="50" type="text"><span class="linebreak"></span>'+
				   '<label_a>'+bricks.format+'</label_a><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.'+a+'.fr"><span class="linebreak"></span>';

					
		}
		function buildPreffSuff(a) {
			return '<row class="directrow">'+
					'<onelong><label_a>'+bricks.pre+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent"  data-evt="chartsupdate" data-r="addOns.'+slug+'.'+a+'.pre" type="text"></onelong>'+
					'<oneshort><label_a>'+bricks.suf+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.'+a+'.suf" type="text"></oneshort>'+
					'</row>';
		}

		function buildFontFamilies(a,l) {
			return 	'<label_a>'+bricks.fontfamily+'</label_a><select id="charts_'+a+'_fontfamily" class="layerinput easyinit searchbox tos2" data-evt="updateFontFamilyCharts" data-evtparam="'+l+'" data-theme="fontfamily" data-r="addOns.'+slug+'.'+l+'.font">'+RVS.S.fontfamilyopts +'</select>';
						
			 			
		}
		function buildFontWeights(a,l) {
			return 	'<label_a>'+bricks.fontweight+'</label_a><select id="charts_fontweight_'+l.replace('.','_')+'" data-evt="updateFontFamilyCharts" class="layerinput tos2 nosearchbox easyinit"  data-r="addOns.'+slug+'.'+l+'.fontWeight"><option value="100">100 Thin</option><option value="200">200 Extra-Light</option><option value="300">300 Light</option><option selected="selected" value="400">400 Regular</option><option value="500">500 Medium</option><option value="600">600 Semi-Bold </option><option value="700">700 Bold</option><option value="800">800 Extra-Bold</option><option value="900">900 Black</option></select>';			 		
		}

		function buildMarker(a,l) {
			return 	'<div class="form_inner">'+
						'<div class="form_inner_header"><i class="material-icons">more_vert</i>'+bricks[a+'marker']+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-showhide="#charts_iline_'+a+'" data-showhidedep="true" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.interaction.'+a+'.use"></div>'+
			 			'<div id="charts_iline_'+a+'" style="padding: 20px">'+
			 				'<row class="direktrow">'+
			 					'<onelong><label_icon class="ui_strokewidth"></label_icon><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.interaction.'+a+'.size" type="text"></onelong>'+
			 					'<oneshort><label_icon class="ui_strokedasharray"></label_icon><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate"  data-r="addOns.'+slug+'.interaction.'+a+'.dash" type="text"></oneshort>'+
			 				'</row>'+			 				
			 				'<label_a>'+bricks.dashcolor+'</label_a><input type="text" data-editing="'+l+' Marker Dash Color" data-mode="single" name="charts_'+a+'marker_color" id="charts_'+a+'_color" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.interaction.'+a+'.color" value="transparent">'+			 				
			 			'</div>'+
			 		'</div>'+	
			 		'<div class="form_inner" style="margin-bottom:1px">'+
			 			'<div class="form_inner_header"><i class="material-icons">view_week</i>'+bricks.columnvalues+'</div>'+
			 			'<div style="padding:20px">'+
			 				'<longoption><label_a>'+bricks.showsingleval+'</label_a><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.interaction.'+a+'.usevals"></longoption>'+
			 				'<div class="chart_hide_on_bars">'+
			 				'<longoption><label_a>'+bricks.dpscale+'</label_a><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.interaction.'+a+'.dpscale"></longoption>'+
			 				'<longoption><label_a>'+bricks.dphidden+'</label_a><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.interaction.'+a+'.dphidden"></longoption>'+
			 				'</div>'+
			 			'</div>'+
			 		'</div>'+
			 		'<div class="form_inner">'+
						'<div class="form_inner_header"><i class="material-icons">label</i>'+bricks.showxaxisval+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-showhide="#charts_ilineval_'+a+'" data-showhidedep="true" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.interaction.'+a+'.usexval"></div>'+			 				
		 				'<div id="charts_ilineval_'+a+'" style="padding: 20px">'+
		 					buildXYOffset('interaction_'+a+'_text','interaction.'+a)+
		 					'<label_a>'+bricks.valuebgcolor+'</label_a><input type="text" data-editing="Value Color" name="charts_valuecolorr_'+a+'" id="charts_valuecolorr_'+a+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.interaction.'+a+'.fill" value="transparent">'+
		 					'<div class="div5"></div>'+	
							'<label_a>'+bricks.valuecolor+'</label_a><input type="text" data-editing="Value BG Color" name="charts_valbgcolorr_'+a+'" id="charts_vbgcolorr_'+a+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.interaction.'+a+'.textcolor" value="transparent">'+
						'</div>'+			 			
			 		'</div>';
		}


		// Build The Data Structure
		function buildDataStructure(id,redraw,resetcsv) {	
		
			if (RVS.L[id]===undefined || RVS.L[id].subtype!=="charts") return;			
			var _ = RVS.L[id].addOns[slug],i;			
			if (_.settings.type==="line") {
				addon.forms.layer[0].classList.remove('chart_bars');
				addon.forms.layer[0].classList.add('chart_lines');
			} else {
				addon.forms.layer[0].classList.add('chart_bars');
				addon.forms.layer[0].classList.remove('chart_lines');
			}
			
			if (_.csv===undefined || _.csv.length===0) {
				addon.forms.structure[0].innerHTML = '<row class="direktrow"><labelhalf><i class="material-icons vmi">sms_failed</i></labelhalf><contenthalf><div class="function_info">'+bricks.nodata+'</div></contenthalf></row>';
				addon.forms.horref[0].innerHTML ="";
			} else {		

				//CONVERT DATA				
				if (_.data===undefined || _.data.length===0 || resetcsv) _.data = convertCSVToJSON(_.csv).filter(function(el) { return el!==null && el!==" " && el!=="" && el[0]!=="" && el[0]!==" ";});				
				var _s = '<label_a>'+bricks.useasx+'</label_a><select class="layerinput easyinit nosearchbox tos2 callEvent" data-evt="chartsupdate"  data-r="addOns.'+slug+'.settings.isx">';
				for (i=0;i<_.data[0].length;i++)  _s +='<option value="'+i+'">'+bricks.col+' '+(i+1)+' ('+RVS._R.chartsBuild.getTitle(_.data[0][i],10)+')</option>';
				_s += '</select>';
				_s += '<label_a>'+bricks.ysplit+'</label_a><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.grid.ydivide"  type="text">';	

				addon.forms.horref[0].innerHTML = _s;
				_s = "";	
				_.altcolors =_.altcolors===undefined ? [] : _.altcolors;
				_.valuefcolor = _.valuefcolor===undefined ? [] : _.valuefcolor;
				_.altcolorsuse =_.altcolorsuse===undefined ? [] : _.altcolorsuse;

				for (i=0;i<_.data[0].length;i++) {	
					_.inuse[i]=_.inuse[i]===undefined ? true : _.inuse[i];
					_.index[i]=_.index[i]===undefined ? i : _.index[i];
					_.strokewidth[i]=_.strokewidth[i]===undefined ? 1 : _.strokewidth[i];
					_.strokedash[i]=_.strokedash[i]===undefined ? 1 : _.strokedash[i];
					_.curves[i]=_.curves[i]===undefined ? 0 : _.curves[i];
					_.datapoint[i]=_.datapoint[i]===undefined ? "1" : _.datapoint[i];
					_.strokecolor[i]=_.strokecolor[i]===undefined ? _COLORS_[tpGS.gsap.utils.wrap([0,1,2,3,4,5,6,7,8,9,10],i)]: _.strokecolor[i];
					_.anchorcolor[i]=_.anchorcolor[i]===undefined ? _COLORS_[tpGS.gsap.utils.wrap([0,1,2,3,4,5,6,7,8,9,10],i)]: _.anchorcolor[i];
					_.fillcolor[i]=_.fillcolor[i]===undefined ? _BGS_[tpGS.gsap.utils.wrap([0,1,2,3,4,5,6,7,8,9,10],i)]: _.fillcolor[i];
					_.valuebgcols[i]=_.valuebgcols[i]===undefined ? _COLORS_[tpGS.gsap.utils.wrap([0,1,2,3,4,5,6,7,8,9,10],i)]: _.valuebgcols[i];
					_.valuecolor[i]=_.valuecolor[i]===undefined ? '#FFF' : _.valuecolor[i];
					_.valuefcolor[i]=_.valuefcolor[i]===undefined ? '#FFF' : _.valuefcolor[i];
					_.altcolors[i]=_.altcolors[i]===undefined ? [] : _.altcolors[i];
					_.altcolorsuse[i]=_.altcolorsuse[i]===undefined ? false : _.altcolorsuse[i]; 

					if (""+i!==""+_.settings.isx) {							
						_s +='<div class="form_inner"><div class="form_inner_header" style="margin-bottom:1px"><i class="material-icons">perm_data_setting</i>'+RVS._R.chartsBuild.getTitle(_.data[0][i],40)+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-showhide="#charts_style_'+i+'" data-showhidedep="true" data-evt="chartsupdatelive" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.inuse.'+i+'"></div>';									
						_s +='<div id="charts_style_'+i+'">';
						_s += 	'<div style="padding:20px">';								
						if (_.settings.type=="line") {
							_s += 		'<row class="directrow">';
							_s +=			'<onelong class="charts_data_column_'+i+'"><label_a>'+bricks.zindex+'</label_a><input class="layerinput smallinput easyinit callEvent charts_zindex" data-evt="chartsupdate" data-r="addOns.'+slug+'.index.'+i+'"  type="text"></onelong>';
							_s +=			'<oneshort class="chart_hide_on_bars"><i class="label_icon inshort material-icons">gesture</i><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-numeric="true" data-min="0" data-max="5" data-allowed="" data-r="addOns.'+slug+'.curves.'+i+'" type="text"></oneshort>';
							_s +=		'</row>';						
							_s +=		'<div class="div10"></div>';
							_s += 		'<row class="direktrow ">';
							_s += 			'<onelong><label_a>'+bricks.strokewidth+'</label_a><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-numeric="true" data-allowed="" data-r="addOns.'+slug+'.strokewidth.'+i+'" type="text"></onelong>';
							_s += 			'<oneshort><label_a class="chrt_lngtxt">'+bricks.strokestyle+'</label_a><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate"  data-r="addOns.'+slug+'.strokedash.'+i+'" type="text"></oneshort>';
							_s += 		'</row>'
							_s += 		'<row class="directrow twocolorfields" style="margin-bottom:5px">';
							_s += 			'<onelong><label_a>'+bricks.strokecolor+'</label_a><input type="text" data-editing="Column Dash Color" data-mode="single" name="charts_dashcolor_'+i+'" id="charts_dashcolor_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.strokecolor.'+i+'" value="transparent"></onelong>';
							_s += 			'<oneshort><label_a>'+bricks.fill+'</label_a><input type="text" data-editing="Column Fill Color" name="charts_fillcolor_'+i+'" id="charts_fillcolor_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.fillcolor.'+i+'" value="transparent"></oneshort>';
							_s += 		'</row>';
							_s +=		'<div class="div10"></div>';
							_s += 		'<row class="directrow twocolorfields">';
							_s += 			'<onelong><label_a>'+bricks.datapoint+'</label_a><select class="layerinput easyinit nosearchbox tos2 callEvent" data-evt="chartsupdate"  data-r="addOns.'+slug+'.datapoint.'+i+'" data-theme="min120"><option value="0">'+bricks.none+'</option><option value="1">'+bricks.dot+'</option><option value="2">'+bricks.bdot+'</option><option value="3">'+bricks.tri+'</option><option value="4">'+bricks.rec+'</option></select></onelong>';
							_s += 			'<oneshort><label_a>'+bricks.fill+'</label_a><input type="text" data-editing="Anchor Color" name="charts_anchorcolor_'+i+'" id="charts_anchorcolor_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.anchorcolor.'+i+'" value="transparent"></onehsort>';
							_s += 		'</row>';
							_s += 		'<row class="directrow twocolorfields">';
							_s +=			'<onelong><label_a>'+bricks.hlabel+'</label_a><input type="text" data-editing="Value Color" name="charts_valuecolora_'+i+'" id="charts_valuecolora_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.valuecolor.'+i+'" value="transparent"></onelong>';
							_s += 			'<oneshort><label_a>'+bricks.hbgshort+'</label_a><input type="text" data-editing="Value BG Color" name="charts_valbgcolor_'+i+'" id="charts_vbgcolorr_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.valuebgcols.'+i+'" value="transparent"></oneshort>';						
							_s += 		'</row>';
							_s +=		'<div class="div5"></div>';
							_s +=		'<label_a>'+bricks.fixlabel+'</label_a><input type="text" data-editing="Fixed Value Color" name="charts_valuecolorb_'+i+'" id="charts_valuefcolorb_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.valuefcolor.'+i+'" value="transparent">';
						} else {
							_s +=		'<label_a>'+bricks.zindex+'</label_a><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.index.'+i+'"  type="text">';
							_s += 		'<div id="chart_normal_fillcolor"><label_a>'+bricks.cfill+'</label_a><input type="text" data-editing="Column Fill Color" name="charts_fillcolor_'+i+'" id="charts_fillcolor_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.fillcolor.'+i+'" value="transparent"><span class="linebreak"></span><div class="div5"></div></div>';							
							_s +=		'<label_a>'+bricks.hlabel+'</label_a><input type="text" data-editing="Value Color" name="charts_valuecolor_'+i+'" id="charts_valuecolor_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.valuecolor.'+i+'" value="transparent"><span class="linebreak"></span>';							
							_s +=		'<div class="div5"></div>';
							_s += 		'<label_a>'+bricks.hbg+'</label_a><input type="text" data-editing="Value BG Color" name="charts_valbgcolor_'+i+'" id="charts_vbgcolorr_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.valuebgcols.'+i+'" value="transparent"><span class="linebreak"></span>';
							_s +=		'<div class="div5"></div>';
							_s +=		'<label_a>'+bricks.fixlabel+'</label_a><input type="text" data-editing="Fixed Value Color" name="charts_valuecolor_'+i+'" id="charts_valuefcolor_'+i+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.valuefcolor.'+i+'" value="transparent"><span class="linebreak"></span>';
							if (_.data.length<=20) {
								_s +=		'<div class="div10"></div>';
								_s += 		'<label_a>'+bricks.altcolors+'</label_a><input type="checkbox" data-showhide="#charts_altcolors" data-hideshow="#chart_normal_fillcolor" data-showhidedep="true" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.altcolorsuse.'+i+'">';
								_s +=		'<div id="charts_altcolors">';
								if (_.altcolorsuse[i]===true) {
									for (var q=0;q<_.data.length;q++) {										
										_.altcolors[i][q]=_.altcolors[i][q]===undefined ? _COLORS_[tpGS.gsap.utils.wrap([0,1,2,3,4,5,6,7,8,9,10],q)]: _.altcolors[i][q];
										_s += 	'<label_a>'+bricks.fill+'</label_a><input type="text" data-editing="Column Fill Color" name="charts_fillcolor_'+i+'_'+q+'" id="charts_fillcolor_'+i+'_'+q+'" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.altcolors.'+i+'.'+q+'" value="transparent"><div class="div5"></div>';
									}
								}
								_s +=		'</div>';
							}
						}						
						
						_s +=	'</div>';
						_s +='</div>';
						if (_.inuse[i]===false) for (var row=1;row<_.data.length;row++) _.data[row][i] = "";
					}

				}
				//
				// SHORT UP ARRAYS
				_.inuse=_.inuse.slice(0,_.data[0].length);
				_.index=_.index.slice(0,_.data[0].length);
				_.strokewidth=_.strokewidth.slice(0,_.data[0].length);
				_.strokedash=_.strokedash.slice(0,_.data[0].length);
				_.curves=_.curves.slice(0,_.data[0].length);
				_.datapoint=_.datapoint.slice(0,_.data[0].length);
				_.strokecolor=_.strokecolor.slice(0,_.data[0].length);
				_.anchorcolor=_.anchorcolor.slice(0,_.data[0].length);
				_.fillcolor=_.fillcolor.slice(0,_.data[0].length);
				_.valuebgcols=_.valuebgcols.slice(0,_.data[0].length);
				_.valuecolor=_.valuecolor.slice(0,_.data[0].length);
				_.valuefcolor=_.valuefcolor.slice(0,_.data[0].length);
				_.altcolors=_.altcolors.slice(0,_.data[0].length);



				addon.forms.structure[0].innerHTML = _s;
				// INIT INPUTS
				RVS.F.initTpColorBoxes(addon.forms.structure.find('.my-color-field'));
				RVS.F.updateEasyInputs({container:addon.forms.horref, path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
				RVS.F.updateEasyInputs({container:addon.forms.structure, path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});				
				RVS.F.initOnOff(addon.forms.structure);
				if (redraw!==false) RVS._R.chartsBuild.diagramm(id,_,RVS.H[id].c[0]);
			}
		}

					
		// CREATE INPUT FIELDS
		function createLayerSettingsFields() {
			
			var _h="";
			if (RVS.S.fontfamilyopts==undefined) {
				RVS.S.fontfamilyopts = "";
				for (var fontindex in RVS.LIB.FONTS) if(RVS.LIB.FONTS.hasOwnProperty(fontindex) && RVS.LIB.FONTS[fontindex].label!=="Dont Show Me") RVS.S.fontfamilyopts +='<option value="'+RVS.LIB.FONTS[fontindex].label+'">'+RVS.LIB.FONTS[fontindex].label+'</option>';
			}
			

			_h += '<div class="form_inner_header"><i class="material-icons">leaderboard</i>'+bricks.settings+'</div>';
			
			// SUBMENU 
			_h +='<div  id="charts_form_wrap" style="display:block !important">';
			_h += 	'<div style="padding:20px">';	
			_h += 	'<div id="charts-tab-4" class="settingsmenu_wrapbtn"><div data-inside="#charts_form_wrap" data-showssm="#charts_datas" class="ssmbtn selected">'+bricks.datas+'</div></div>';						
			_h += 	'<div id="charts-tab-1" class="settingsmenu_wrapbtn"><div data-inside="#charts_form_wrap" data-showssm="#charts_columns" class="ssmbtn">'+bricks.chart+'</div></div>';						
			_h += 	'<div id="charts-tab-3" class="settingsmenu_wrapbtn"><div data-inside="#charts_form_wrap" data-showssm="#charts_grid" class="ssmbtn">'+bricks.grid+'</div></div>';
			_h += 	'<div id="charts-tab-2" class="settingsmenu_wrapbtn"><div data-inside="#charts_form_wrap" data-showssm="#charts_basics" class="ssmbtn">'+bricks.labels+'</div></div>';
			_h += 	'<div id="charts-tab-8" class="settingsmenu_wrapbtn"><div data-inside="#charts_form_wrap" data-showssm="#charts_legend" class="ssmbtn">'+bricks.legend+'</div></div>';
			_h += 	'<div id="charts-tab-7" class="settingsmenu_wrapbtn"><div data-inside="#charts_form_wrap" data-showssm="#charts_vals" class="ssmbtn ">'+bricks.values+'</div></div>';			
			_h += 	'<div id="charts-tab-5" class="settingsmenu_wrapbtn"><div data-inside="#charts_form_wrap" data-showssm="#charts_inter" class="ssmbtn ">'+bricks.interaction+'</div></div>';			
			_h += 	'<div id="charts-tab-9" class="settingsmenu_wrapbtn"><div data-inside="#charts_form_wrap" data-showssm="#charts_anim" class="ssmbtn ">'+bricks.animation+'</div></div>';			
			_h += 	'<div class="div25"></div>';
			_h += '</div>';
			
			// GRID
			_h += '<div id="charts_grid" class="ssm_content">';	
			_h += 	'<div class="form_inner_header"><i class="material-icons">zoom_out_map</i>'+bricks.positionspace+'</div>';		
			_h += 	'<div style="padding: 20px">';	
			_h += 		'<row class="directrow">'
			_h += 			'<onelong><label_icon class="ui_width"></label_icon><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.settings.width"  type="text"></onelong>';
			_h += 			'<oneshort><label_icon class="ui_height"></label_icon><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.settings.height"  type="text"></oneshort>';
			_h += 		'</row>';			
			_h +=		'<div class="div10"></div>';
			_h += 		'<row class="directrow">';
			_h += 			'<onelong><label_icon class="ui_margin_top"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed=""  data-numeric="true" data-r="addOns.'+slug+'.settings.margin.top" data-min="0" data-max="1000" type="text"></onelong>';
			_h += 			'<oneshort><label_icon class="ui_margin_right"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed=""  data-numeric="true" data-r="addOns.'+slug+'.settings.margin.right" data-min="0" data-max="1000" type="text"></oneshort>';
			_h += 		'</row>';
			_h +=		'<row>';
			_h +=			'<onelong><label_icon class="ui_margin_bottom"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed=""  data-numeric="true" data-r="addOns.'+slug+'.settings.margin.bottom" data-min="0" data-max="1000" type="text"></onelong>';
			_h +=			'<oneshort><label_icon class="ui_margin_left"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed=""  data-numeric="true" data-r="addOns.'+slug+'.settings.margin.left" data-min="0" data-max="1000" type="text"></oneshort>';
			_h +=		'</row>';
			_h +=		'<row class="directrow">'+
							'<onelong><label_icon class="ui_padding_left"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent"  data-evt="chartsupdate" data-allowed=""  data-numeric="true" data-r="addOns.'+slug+'.settings.pl" data-min="0" data-max="50" type="text"></onelong>'+
							'<oneshort><label_icon class="ui_padding_right"></label_icon><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed="" data-numeric="true"  data-r="addOns.'+slug+'.settings.pr" data-min="0" data-max="50" type="text"></oneshort>'+
						'</row>';
			_h += 	'</div>';		
			_h += 	'<div class="form_inner">';
			_h += 		'<div class="form_inner_header"><i class="material-icons">border_vertical</i>'+bricks.gridx+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-showhide="#charts_grid_x" data-showhidedep="true" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.grid.xuse"></div>';			
			_h +=		'<div id="charts_grid_x" style="padding: 20px">';
			_h += 			buildFontColorSize('grid.xst',undefined,'ui_strokewidth',bricks.lebo,undefined,true);
			_h += 			buildFontColorSize('grid.x',undefined,'ui_strokewidth',bricks.markers,undefined,true);
			_h += 		'</div>';
			_h +=	'</div>';

			_h += 	'<div class="form_inner">';
			_h += 		'<div class="form_inner_header"><i class="material-icons">border_horizontal</i>'+bricks.gridy+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-showhide="#charts_grid_y" data-showhidedep="true" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.grid.yuse"></div>';			
			_h +=		'<div id="charts_grid_y" style="padding: 20px">';
			_h += 			buildFontColorSize('grid.ybt',undefined,'ui_strokewidth',bricks.bobo,undefined,true);			
			_h += 			buildFontColorSize('grid.y',undefined,'ui_strokewidth',bricks.markers,undefined,true);			
			_h += 		'</div>';
			_h +=	'</div>';
			_h += '</div>';

			// VALUES
			_h += '<div id="charts_vals" class="ssm_content">';			
			_h += 	'<div style="padding:0px 20px 20px">' + buildFontFamilies('values','values') + '</div>';	
			// VALUSE X
			_h += 	'<div class="form_inner">';
			_h += 		'<div class="form_inner_header"><i class="material-icons">more_horiz</i>'+bricks.valuesx+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-showhide="#charts_values_x" data-showhidedep="true" data-r="addOns.'+slug+'.values.x.use"></div>';			
			_h += 		'<div id="charts_values_x" style="padding: 20px">';			
			_h +=			buildXYOffset('valuesx','values.x');
			_h +=			'<div class="div10"></div>';
			_h += 			buildFontWeights('valuesx','values.x');
			_h += 			buildFontColorSize('values.x');
			_h += 			'<row class="directrow"><onelong><label_a>'+bricks.everyn+'</label_a><input class="layerinput smallinput easyinit callEvent" data-numeric="true" data-allowed="" data-min="1" data-max="1000" data-evt="chartsupdate" data-r="addOns.'+slug+'.values.x.every"  type="text"></onelong><oneshort><label_icon class="ui_rotatez"></label_icon><input class="layerinput smallinput easyinit callEvent" data-numeric="true" data-allowed="" data-evt="chartsupdate" data-r="addOns.'+slug+'.values.x.ro"  type="text"></oneshort></row>';
			_h += 			buildPreffSuff('values.x');
			_h +=			buildDez('values.x');
			_h += 		'</div>'
			_h += 	'</div>';
			// VALUSE Y
			_h += 	'<div class="form_inner">';
			_h += 		'<div class="form_inner_header"><i class="material-icons">more_vert</i>'+bricks.valuesy+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-showhide="#charts_values_y" data-showhidedep="true" data-r="addOns.'+slug+'.values.y.use"></div>';
			_h += 		'<div id="charts_values_y" style="padding: 20px">';			
			_h +=			buildXYOffset('valuesy','values.y');
			_h +=			'<div class="div10"></div>';
			_h += 			buildFontWeights('valuesy','values.y');
			_h += 			buildFontColorSize('values.y');
			_h += 			buildPreffSuff('values.y');
			_h +=			buildDez('values.y');
			_h += 		'</div>';
			_h += 	'</div>';

			// SINGLE VALUES
			_h +=	'<div class="form_inner">';			
			_h += 		'<div class="form_inner_header"><i class="material-icons">more</i>'+bricks.singlevalues+'</div>';
			_h += 		'<div style="padding: 20px">';
			_h += 			buildFontWeights('interaction_text','values.s');
			_h += 			'<label_a>'+bricks.textalign+'</label_a><select class="layerinput easyinit nosearchbox tos2 callEvent"  data-evt="chartsupdate"  data-r="addOns.'+slug+'.values.s.direction"><option value="start">'+bricks.left+'</option><option value="end">'+bricks.right+'</option><option value="middle">'+bricks.middle+'</option></select>';
			_h += 			'<label_a>'+bricks.fontsize+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.values.s.size" data-max="500" type="text">';			
			_h += 			'<div class="div15"></div>';
			_h += 			buildXYOffset('interactiontext','values.s');
			_h +=			buildPadding('values.s');
			_h += 			'<label_a>'+bricks.rad+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.values.s.radius" data-max="100" type="text">';
			_h += 			'<div class="div15"></div>';
			_h += 			buildPreffSuff('values.s');
			_h +=			buildDez('values.s');		
			_h +=   	'</div>';
			_h +=   '</div>';			

			// FIXED VALUES
			_h +=	'<div class="form_inner">';			
			_h += 		'<div class="form_inner_header"><i class="material-icons">more_vert</i>'+bricks.valuesfixed+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-showhide="#charts_values_fixed" data-showhidedep="true" data-r="addOns.'+slug+'.values.f.use"></div>';
			_h += 		'<div id="charts_values_fixed" style="padding: 20px">';
			_h += 			buildFontWeights('interaction_text','values.f');			
			_h += 			'<label_a>'+bricks.fontsize+'</label_a><input class="layerinput valueduekeyboard smallinput easyinit callEvent" data-evt="chartsupdate" data-allowed="px" data-numeric="true" data-r="addOns.'+slug+'.values.f.size" data-max="500" type="text">';
			_h += 			'<div class="div15"></div>';
			_h += 			buildXYOffset('interactiontext','values.f');						
			_h += 			'<div class="div15"></div>';
			_h += 			buildPreffSuff('values.f');
			_h +=			buildDez('values.f');		
			_h +=   	'</div>';
			_h +=   '</div>';
			_h +='</div>';
			
			
			// LEGENDS
			_h += '<div id="charts_legend" class="ssm_content">'; 				
			_h += 	'<div class="form_inner">';
			_h += 		'<div class="form_inner_header"><i class="material-icons">description</i>'+bricks.legend+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-showhide="#charts_legend_wrap" data-showhidedep="true" data-r="addOns.'+slug+'.legend.use"></div>';		
			_h += 		'<div id="charts_legend_wrap" style="padding: 20px">';
			_h += 			buildFontFamilies('legend','legend');
			_h += 			buildFontWeights('legend','legend');
			_h +=		'<div class="div10"></div>';
			_h +=			'<label_a>'+bricks.bgcolor+'</label_a><input type="text" data-editing="Value BG Color" name="charts_legenbgcolor" id="charts_legend_bgcolor" class="my-color-field layerinput easyinit callEvent" data-evt="chartsupdate" data-visible="true" data-r="addOns.'+slug+'.legend.bg" value="transparent">';
			_h +=		'<div class="div5"></div>';
			_h += 		 	buildAlignOffset('charts_legend','legend',false);					
			_h += 			'<label_a>'+bricks.align+'</label_a><select class="layerinput easyinit nosearchbox tos2 callEvent"  data-evt="chartsupdate"  data-r="addOns.'+slug+'.legend.align"><option value="horizontal">'+bricks.horizontal+'</option><option value="vertical">'+bricks.vertical+'</option></select>';
			_h += 			'<label_a>'+bricks.gap+'</label_a><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.legend.gap"  type="text">';
			_h +=		'<div class="div10"></div>';
			_h +=			'<label_a>'+bricks.showbg+'</label_a><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.legend.sbg"><span class="linebreak"></span>';
			_h +=			'<label_a>'+bricks.showdp+'</label_a><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.legend.dp"><span class="linebreak"></span>';
			_h +=			'<label_a>'+bricks.showst+'</label_a><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-r="addOns.'+slug+'.legend.st">';
			_h += 		'</div>';
			_h += 	'</div>';
			_h += '</div>';	

			// LABELS
			_h += '<div id="charts_basics" class="ssm_content">'; 	
			_h += 	'<div style="padding:0px 20px 20px">' + buildFontFamilies('labels','labels') + '</div>';	
			_h += 	'<div class="form_inner">';
			_h += 		'<div class="form_inner_header"><i class="material-icons">label</i>'+bricks.labelsx+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-showhide="#charts_label_x" data-showhidedep="true" data-r="addOns.'+slug+'.labels.x.use"></div>';			
			_h += 		'<div id="charts_label_x" style="padding: 20px ">';
			_h += 			buildName('labels.x');
			_h += 			buildFontWeights('labels','labels.x');						
			_h += 			buildAlignOffset('charts_labelx','labels.x',false);			
			_h += 		'</div>';
			_h += 	'</div>';			
			_h += 	'<div class="form_inner">';
			_h += 		'<div class="form_inner_header"><i class="material-icons">label</i>'+bricks.labelsy+'</div><div class="on_off_navig_wrap"><input type="checkbox" data-evt="chartsupdate" class="layerinput easyinit callEvent" data-showhide="#charts_label_y" data-showhidedep="true" data-r="addOns.'+slug+'.labels.y.use"></div>';			
			_h += 		'<div id="charts_label_y" style="padding: 20px">';	
			_h += 			buildName('labels.y');			
			_h += 			buildFontWeights('labelsy','labels.y');			
			_h += 			buildAlignOffset('charts_labely','labels.y',false);
			_h += 		'</div>';
			_h += 	'</div>';			
			_h += '</div>';
			

			//DATAS
			_h += '<div id="charts_datas" class="ssm_content selected">';
			_h += 	'<div class="form_inner_header"><i class="material-icons">settings</i>'+bricks.source+'</div>';
			_h += 	'<div style="padding:20px">';			
			_h += 		'<input id="chartsfile" type="file" hidden/>';
			_h += 		'<label_a>'+bricks.pickcsv+'</label_a><div id="charts_upload_button" class="basic_action_button layerinput"><i class="material-icons">cloud_upload</i>'+bricks.upload+'</div>';				
			_h += 		'<label_a>'+bricks.importedcsv+'</label_a><div class="linebreak"></div>';
			_h +=		'<textarea style="white-space: pre; width:100%; height:300px" class="layerinput easyinit callEvent" data-evt="chartsupdatelive" data-cursortoclick="true" data-r="addOns.'+slug+'.csv"></textarea>';			
			_h += 		'<div class="div15"></div>'
			_h += 	'</div>';
			_h +='</div>';

			//COLUMNS  			
			_h += '<div id="charts_columns" class="ssm_content">';			
			_h += 	'<div class="form_inner_header"><i class="material-icons">settings</i>'+bricks.basics+'</div>';
			_h += 	'<div style="padding:20px">';			
			_h += 		'<label_a>'+bricks.charttype+'</label_a><select class="layerinput easyinit nosearchbox tos2 callEvent"  data-evt="chartsupdatelive" data-show=".chart_graph_gap_*val*" data-hide=".chart_graph_gap" data-showprio="show" data-r="addOns.'+slug+'.settings.type"><option value="line">'+bricks.linegraph+'</option><option value="bar">'+bricks.bargraph+'</option><option value="pbar">'+bricks.pbargraph+'</option></select>';
			_h += 		'<div class="chart_graph_gap_bar chart_graph_gap_pbar chart_graph_gap"><label_a>'+bricks.gap+'</label_a><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-r="addOns.'+slug+'.settings.gap"  type="text"></div>';			
			_h += 		'<div id="charts_horref"></div>'; // USE AS X			
			_h += 	'</div>';			
			_h += 	'<div id="charts_data_structure"></div>';			
			_h +='</div>';

			//ANIMATION
			_h += '<div id="charts_anim" class="ssm_content">';
			_h += 	'<div class="form_inner_header"><i class="material-icons">theaters</i>'+bricks.animation+'</div>';
			_h += 	'<div style="padding: 20px">';
			_h += 		'<label_a>'+bricks.speed+'</label_a><input class="layerinput smallinput easyinit callEvent" data-evt="chartsupdate" data-numeric="true" data-allowed="ms" data-min="0" data-max="10000" data-r="addOns.'+slug+'.settings.speed"  type="text">';
			_h += 		'<label_a>'+bricks.delay+'</label_a><input class="layerinput smallinput easyinit" data-numeric="true" data-allowed="ms" data-min="0" data-max="10000" data-r="addOns.'+slug+'.settings.delay"  type="text">';
			_h += 		'<div id="charts_play_anim" class="basic_action_button layerinput fullbutton"><i class="material-icons">play_circle</i>'+bricks.playanimation+'</div>';	
			_h +=	'</div>';			
			_h +='</div>';

			//INTERACTION
			_h += '<div id="charts_inter" class="ssm_content">';			
			_h +=	buildMarker('v','Vertical'); 
			_h +='</div>';

		
			
			
			// append settings markup
			addon.forms.layergeneral.append(_h);			
			addon.forms.file = document.getElementById('chartsfile');
			addon.forms.structure = jQuery('#charts_data_structure');			
			addon.forms.horref = jQuery('#charts_horref');	

			//FONTWEIGHTS
			addon.forms.lx = document.getElementById('charts_fontweight_labels_x');
			addon.forms.ly = document.getElementById('charts_fontweight_labels_y');
			addon.forms.vx = document.getElementById('charts_fontweight_values_x');
			addon.forms.vy = document.getElementById('charts_fontweight_values_y');
			addon.forms.vs = document.getElementById('charts_fontweight_values_s');
			addon.forms.le = document.getElementById('charts_fontweight_legend');

			window.I = addon.forms;

		}


		
		function initInputs() {			
			
			// init ddTP
			addon.forms.layergeneral.find('.tos2.nosearchbox').ddTP({placeholder:bricks.placeholder_select});
			
			// colorPicker init
			RVS.F.initTpColorBoxes(addon.forms.layergeneral.find('.my-color-field'));	
			
			// on/off init
			RVS.F.initOnOff(addon.forms.layergeneral);
			delete addon.forms.layergeneral;
			
		}
		
		var events = {

			layerSelected: function() {				
				var allAddOn,
					len = RVS.selLayers.length;
					
				if(len) {					
					allAddOn = true;
					for(var i = 0; i < len; i++) if(RVS.L[RVS.selLayers[i]].subtype !== 'charts') {	allAddOn = false;break;}					
				}
				
				if(allAddOn) {					
					addon.forms.layericon.css('display', 'inline-block');
					addon.forms.layer.css('visibility', 'visible');
					if (RVS.selLayers.length!==0 && RVS.L[RVS.selLayers[0]]!==undefined) {
						var id = RVS.selLayers[0];
						if (RVS.L[id].addOns[slug].values.f===undefined) RVS.L[id].addOns[slug].values.f = {pre:"",suf:"",dez:2,use:false,size:10,xo:0,yo:0,fontWeight:'500', fr:true};
						RVS.L[id].addOns[slug].labels.x.fontWeight = checkFontWeights(addon.forms.lx,RVS.L[id].addOns[slug].labels.font,RVS.L[id].addOns[slug].labels.x.fontWeight,id);
						RVS.L[id].addOns[slug].labels.y.fontWeight = checkFontWeights(addon.forms.ly,RVS.L[id].addOns[slug].labels.font,RVS.L[id].addOns[slug].labels.y.fontWeight,id);												
						RVS.L[id].addOns[slug].values.x.fontWeight = checkFontWeights(addon.forms.vx,RVS.L[id].addOns[slug].values.font,RVS.L[id].addOns[slug].values.x.fontWeight,id);
						RVS.L[id].addOns[slug].values.y.fontWeight = checkFontWeights(addon.forms.vy,RVS.L[id].addOns[slug].values.font,RVS.L[id].addOns[slug].values.y.fontWeight,id);	
						RVS.L[id].addOns[slug].values.s.fontWeight = checkFontWeights(addon.forms.vs,RVS.L[id].addOns[slug].values.font,RVS.L[id].addOns[slug].values.s.fontWeight,id);
						RVS.L[id].addOns[slug].values.f.fontWeight = checkFontWeights(addon.forms.vx,RVS.L[id].addOns[slug].values.font,RVS.L[id].addOns[slug].values.f.fontWeight,id);																								
						RVS.L[id].addOns[slug].legend.fontWeight = checkFontWeights(addon.forms.le,RVS.L[id].addOns[slug].legend.font,RVS.L[id].addOns[slug].legend.fontWeight,id);
						RVS.F.updateEasyInputs({container:addon.forms.layer, path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});
						buildDataStructure(id,false);
					}
					
				} else {
					
					addon.forms.layericon.css('display', 'none');
					addon.forms.layer.css('visibility', 'hidden');
					
				}
				
			}
			
		};
		
		function convertCSVToJSON(str) {				
		    const rows = str.split('\n');
		    return rows.map(row => {
		    	var cols = row.indexOf('"')===-1 && row.indexOf(';')===-1 ? row.split(',') : row.split('",');
		    	for (var i in cols) cols[i] = cols[i].replace('"','');
		    	if (cols.length===1) return row.split(";"); else return cols;		        
		    });
		};

		
		function readCSVFile(file) {
			if (file.type && file.type.indexOf('csv') === -1) {
				    console.log('File is not a CSV', file.type, file);
				    return;
			}
		    const reader = new FileReader();
		    reader.addEventListener('load',(event) => {
		    	delete RVS.L[RVS.selLayers[0]].addOns[slug].data;
		    	RVS.L[RVS.selLayers[0]].addOns[slug].csv =  atob(event.target.result.replace('data:text/csv;base64,',''));
		    	/*if (RVS.L[RVS.selLayers[0]].addOns[slug].settings.keepselected!==true) {
		    		RVS.L[RVS.selLayers[0]].addOns[slug].inuse = [true,true,true,true,true,true,true,true,true,true,true];
		    		RVS.L[RVS.selLayers[0]].addOns[slug].settings.isx=0;
		    	}*/
		    	buildDataStructure(RVS.selLayers[0]);
		    });
		    reader.readAsDataURL(file);
		}

		/* FONT LOADING MANAGEMENT */			
		function checkFontWeights(_SELECT,ffam,FW,layerid) {
			
			var change=false,
				first ="";
								
			for (var fontindex in RVS.LIB.FONTS) {
				if(!RVS.LIB.FONTS.hasOwnProperty(fontindex)) continue;
				var font = RVS.LIB.FONTS[fontindex];
				if (font.label === ffam) {
					for (var o=0;o<_SELECT.options.length;o++) {						
						var v = _SELECT.options[o].value;						
						_SELECT.options[o].disabled = (jQuery.inArray(v,font.variants)>=0 || font.type==="websafe") ? false : true;
						first = first ==="" && !_SELECT.options[o].disabled ? v : first;  						
						if ((""+FW) == (""+v) && _SELECT.options[o].disabled) change = true;						
					}
				}									
			}	

			if (change==true) {
				_SELECT.value = first;
				FW = first;
			}	
			return FW;
		};

		function pushToLoad(_) {
			if (_===undefined || _==="") return undefined;			
			return  RVS.F.loadSingleFont({family: _.replace(/\ /g,'_'),  font:_});			
		}

		RVS.F.chartloadAllNeededFonts = function(id) {
			var a = [],b								
			b = pushToLoad(RVS.L[id].addOns[slug].values.font);if (b!==undefined) a.push(b);			
			b = pushToLoad(RVS.L[id].addOns[slug].labels.font);if (b!==undefined) a.push(b);			
			b = pushToLoad(RVS.L[id].addOns[slug].legend.font);if (b!==undefined) a.push(b);			
			RVS.F.do_google_font_load(a,{silent:true});
		}
		
		function addEvents() {
			
			
			RVS.DOC.on('selectLayersDone.charts', events.layerSelected);
			//
			RVS.DOC.on('chartsupdate', function(a,b) {
				if (b===undefined || b.val===undefined) return;
				var layerid  = RVS.selLayers[0];
				requestAnimationFrame(function() {
					buildDataStructure(layerid);
				});
			});

			RVS.DOC.on('chartsupdatelive', function(a,b) {
				if (b===undefined || b.val===undefined) return;
				var layerid  = RVS.selLayers[0];
				requestAnimationFrame(function() {
					buildDataStructure(layerid,true,true);
				});
			});
			RVS.DOC.on('click','#charts_upload_button',function() {
				addon.forms.file.click();
			});
			RVS.DOC.on('click','#charts_play_anim',function() {
				RVS._R.chartsBuild.play(RVS.selLayers[0]);
			});
			addon.forms.file.addEventListener('change', (event) => {
			    const fileList = event.target.files;
			    readCSVFile(fileList[0]);			    
			});

			RVS.DOC.on('updateFontFamilyCharts',function(e,b) {											
				if (b!==undefined) {
					var id = RVS.selLayers[0];
					RVS.F.chartloadAllNeededFonts(id);									
					if (b==="labels") {						
						RVS.L[id].addOns[slug].labels.x.fontWeight = checkFontWeights(addon.forms.lx,RVS.L[id].addOns[slug].labels.font,RVS.L[id].addOns[slug].labels.x.fontWeight,id);
						RVS.L[id].addOns[slug].labels.y.fontWeight = checkFontWeights(addon.forms.ly,RVS.L[id].addOns[slug].labels.font,RVS.L[id].addOns[slug].labels.y.fontWeight,id);						
					} else 
					if (b==="values") {
						RVS.L[id].addOns[slug].values.x.fontWeight = checkFontWeights(addon.forms.vx,RVS.L[id].addOns[slug].values.font,RVS.L[id].addOns[slug].values.x.fontWeight,id);
						RVS.L[id].addOns[slug].values.y.fontWeight = checkFontWeights(addon.forms.vy,RVS.L[id].addOns[slug].values.font,RVS.L[id].addOns[slug].values.y.fontWeight,id);	
						RVS.L[id].addOns[slug].values.s.fontWeight = checkFontWeights(addon.forms.vs,RVS.L[id].addOns[slug].values.font,RVS.L[id].addOns[slug].values.s.fontWeight,id);																		
					} else 
					if (b==="legend") {
						RVS.L[id].addOns[slug].legend.fontWeight = checkFontWeights(addon.forms.le,RVS.L[id].addOns[slug].legend.font,RVS.L[id].addOns[slug].legend.fontWeight,id);
					}
					RVS.F.updateEasyInputs({container:addon.forms.layer, path:RVS.S.slideId+".layers.", trigger:"init", multiselection:true});

					buildDataStructure(id);
				}				
			});

			RVS.DOC.on('slideFocusFunctionEnd',function() {				
				if (RVS.S.chartsAddonFirstInitialisation==true) requestAnimationFrame(buildAllDataStructure);
			});

			RVS.DOC.on('layerAdded',function(e,_) {
				
				if (_!==undefined && _.params!==undefined && _.params.subsubtype!==undefined && presets[_.params.subsubtype]!==undefined) {											
					RVS.L[_.layerid] = jQuery.extend(true,RVS.L[_.layerid],presets[_.params.subsubtype]);					
					buildDataStructure(_.layerid);
				}				
			});

			RVS.DOC.on('SceneUpdatedAfterRestore',function(a,b) {
				if (b!==undefined && b.todo!==undefined) {
					if (b.todo.LayerSettings && b.todo.layer!==undefined && RVS.L[b.todo.layer]!==undefined && RVS.L[b.todo.layer].subtype==="charts") {
						buildDataStructure(b.todo.layer);
					} else 
					if (b.todo.LayerSettings && b.todo.layers!==undefined) {
						for  (var i in b.todo.layers) {
							if (RVS.L[b.todo.layers[i]] !==undefined && RVS.L[b.todo.layers[i]].subtype=="charts")
								buildDataStructure(b.todo.layers[i]);
						}
					}
				}
			})
		}

		RVS.F.chartsGetObjAsTemplates = function() {
			if (RVS.selLayers[0]===undefined) {
				console.log("SELECT LAYER !!");
				return;
			}
			var empty = RVS._R.chartsBuild.getBasics(),
				copy = jQuery.extend(true,{},RVS.L[RVS.selLayers[0]].addOns["revslider-charts-addon"]),
				emptyL = RVS.F.addLayerObj(_.type,undefined,true),
    			copyL = jQuery.extend(true,{},RVS.L[RVS.selLayers[0]]);
    			

			delete copy.data;
			delete copy.cOrder;
			delete copy.colOrder;
			delete copy.minmax;
			delete copy.enable;
			if (copy.legend.use==false) copy.legend = {use:false}; 
			if (copy.labels.x.use==false) copy.labels.x = {use:false}; 
			if (copy.labels.y.use==false) copy.labels.y = {use:false}; 
			
			var nL = jQuery.extend(true,{}, RVS.F.simplifyObject(emptyL,copyL));

			nL.addOns = {};
			nL.addOns["revslider-charts-addon"] = jQuery.extend(true,{}, RVS.F.simplifyObject(empty,copy))

			delete nL.uid;
			delete nL.toggle;
			delete nL.text;
			delete nL.runtime;
			delete nL.hover;
			delete nL.group;
			delete nL.actions;

			var nL = JSON.stringify(nL);
			console.log(nL);
			console.log(nL.length)


		}
		
		function initHelp() {			
			// only add on-demand if the AddOn plugin is activated from inside the editor
			// otherwise if the AddOn plugin is already activated, the help definitions will get added when the help guide is officially used (via php filter)
			if(revslider_charts_addon.hasOwnProperty('help') && typeof HelpGuide !== 'undefined') {
			
				var obj = {slug: 'charts_addon'};
				$.extend(true, obj, revslider_charts_addon.help);
				HelpGuide.add(obj);			
			}		
		}


})(jQuery);