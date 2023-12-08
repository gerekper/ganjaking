/**Scroll Sequence*/
(function ($) {
    "use strict";
    var WidgetScrollSequenceHandler = function($scope, $) { 
        if(elementorFrontend.isEditMode() || !elementorFrontend.isEditMode()){
            let scrollSequence = $scope[0].querySelector('.tp-scroll-sequence');
            if(scrollSequence){
                let scrollSattr = scrollSequence.getAttribute('data-attr');
                    scrollSattr = JSON.parse(scrollSattr);
                let widget_id = (scrollSattr.widget_id) ? scrollSattr.widget_id : '',
                    applyto = (scrollSattr && scrollSattr.applyto) ? scrollSattr.applyto : 'default',
                    getSecId = (scrollSattr && scrollSattr.seclist) ? scrollSattr.seclist : '',
                    containerSS = '',
                    get_section = '',
                    contid = '';

                var secoffTop = [],
                    getSec = []
                    if( getSecId ){
                        for(var i=0; i < getSecId.length; i++){

                            if(getSecId[i].sectionId){

                                var getHtml = document.querySelector(`#${getSecId[i].sectionId}`),
                                    topHeight = getSecId[i].offsetop ? getSecId[i].offsetop.size : ''

                                getSec.push(getHtml)
                                getHtml ? getHtml.classList.add('tp-animation-hide') : ''
                                secoffTop.push(topHeight)

                            }

                        }   
                    }
                    
                    if( applyto == 'default' ){
                        get_section = scrollSequence.closest('.elementor-section,.e-con');
                        contid = get_section.getAttribute('data-id');
                        containerSS = ".elementor-element-"+contid;    
                    }else if( applyto == 'body' ){
                        get_section = document.querySelector('body');
                        containerSS = "body";
                    }else if( applyto == 'innerContainer' ){
                        get_section = scrollSequence.closest('.elementor-column');
                        if(get_section){
                            contid = get_section.getAttribute('data-id');
                            containerSS = ".elementor-element-"+contid;
                        }
                    }
                    
                    if(get_section && containerSS){
                        var imgGellary = (scrollSattr.imgGellary) ? scrollSattr.imgGellary : '',
                            preloadImg = (scrollSattr.preloadImg) ? scrollSattr.preloadImg/100 : 0,
                            startOffset = (scrollSattr.startOffset) ? Number(scrollSattr.startOffset) : 0,
                            endOffset = (scrollSattr.endOffset) ? Number(scrollSattr.endOffset) : 0;

                            get_section.insertAdjacentHTML("afterbegin", `<div class="tp-scroll-sequence-canvas"><div class="tp-scroll-seq-inner elementor-element-${widget_id}-canvas"><canvas/></div></div>`);

                            let scrollCalcVal, scrollImage, scrollCurrent = {}, scrollInit = {}, lastScrollStatus, lastScrollImg, ticking = false;
                            var mainWrap = document.querySelector(containerSS),
                                scrollCanvas = mainWrap.querySelector('canvas'),
                                canvasContext = scrollCanvas.getContext("2d");

                                /**Initialization Canvas*/
                                function initScrollImg() {
                                    scrollImage = {
                                        objArray: [],
                                        absoluteOrder: [],
                                        imageTotalNo: 0,
                                        priorityCount: 0,
                                        nonPriorityCount: 0,
                                        priorityDone: false,
                                        nonpriorityDone: false,
                                        firstDrawEn: false,
                                    };
                                    
                                    let absCountImg = 0;
                                    scrollImage.objArray = [];
                                    scrollImage.imageTotalNo = scrollImage.imageTotalNo + imgGellary.length
                                    scrollImage.objArray = [];
                                    for (let j = 0; j < imgGellary.length; j++) { 
                                        scrollImage.objArray[j] = new Image();
                                        scrollImage.absoluteOrder[absCountImg] = [j];
                                        absCountImg = absCountImg + 1
                                    } 
                                }
                                initScrollImg();
                                function scrollInitialCal() {
                                    scrollCalcVal = {}
                                    if( document.querySelector(containerSS) ){
                                        scrollCalcVal.scStart = document.querySelector(containerSS).getBoundingClientRect().top + window.scrollY + startOffset;
                                        scrollCalcVal.scEnd = scrollCalcVal.scStart + document.querySelector(containerSS).clientHeight - window.innerHeight + endOffset;
                                        scrollCalcVal.scInBetween = document.querySelector(containerSS).clientHeight - window.innerHeight + endOffset;
                                    }
                                }
                                var lastScrollTop = 0;
                                function scrollEventFun(scrollPos) {
                                    
                                    let currentScrollStatus;
                                    if (scrollPos >= scrollCalcVal.scStart && scrollPos < scrollCalcVal.scEnd) {
                                        currentScrollStatus = 'active'
                                        if (scrollPos >= scrollCalcVal.scStart && scrollPos < scrollCalcVal.scEnd) {
                                            scrollCurrent.pageProgress = (scrollPos - scrollCalcVal.scStart) / scrollCalcVal.scInBetween
                                            scrollCurrent.image = Math.round(scrollCurrent.pageProgress * (imgGellary.length - 1))
                                        }    
                                    }else if(scrollPos < scrollCalcVal.scStart) {
                                        currentScrollStatus = 'before'
                                    }else{
                                        currentScrollStatus = 'after'
                                    }
                                    if (lastScrollStatus !== currentScrollStatus) {
                                        lastScrollStatus = currentScrollStatus
                                    }
                                    if (currentScrollStatus === 'active') {

                                        if (lastScrollImg !== scrollCurrent.image) {
                                            lastScrollImg = scrollCurrent.image
                                            renderCanvas(scrollCurrent.image)
                                        }

                                    }

                                    if (scrollPos >= scrollCalcVal.scStart && scrollPos < scrollCalcVal.scEnd) {
                                        tpStickyAnimation();
                                    } else{
                                        getSec.forEach(function(item){
                                            if( item ){
                                                item.classList.add('tp-animation-hide')
                                                item.classList.remove('tp-animation-start')
                                            }
                                        })
                                    }

                                }

                                function tpStickyAnimation(){
                                    var tpStart = window.pageYOffset || document.documentElement.scrollTop;

                                        getSec.forEach(function(self,index){
                                            let currentImg = scrollCurrent.image,
                                                secStart = getSecId[index].secStart ? getSecId[index].secStart : '',
                                                secEnd = getSecId[index].secEnd ? getSecId[index].secEnd : '',
                                                tpclassList = self ? self.classList : '',
                                                startAni = getSecId[index].secAnimationstart,
                                                startEnd = getSecId[index].secAnimationend

                                            if( self ) {
                                                if (tpStart > lastScrollTop) {

                                                    if( currentImg >= secStart && currentImg <= secEnd){
                                                        tpclassList.remove('tp-animation-hide')
                                                        if(tpclassList.contains('tp-animation-end')){
                                                            tpclassList.remove('tp-animation-end')
                                                        }else{
                                                            tpclassList.add('tp-animation-start')
                                                        }
                                                        
                                                        self.style.cssText = `top:${secoffTop[index]}%`
                                                        if(startAni != 'none'){
                                                            removeAnimation('end',tpclassList,startAni,startEnd)
                                                            addAnimation('start',tpclassList,startAni)
                                                        }
                                                            
                                                    }else if( currentImg >= secEnd ){

                                                        removeAnimation('start',tpclassList,startAni)

                                                        if(startAni != 'none'){
                                                            addAnimation('end',tpclassList,startAni,startEnd)
                                                        }
                        
                                                        setTimeout(() => {
                                                            tpclassList.remove('tp-animation-start')
                                                            tpclassList.add('tp-animation-end')
                                                        }, 500);
                            
                                                    }

                                                }else if (tpStart < lastScrollTop) {

                                                    if( currentImg <= secEnd && currentImg >= secStart){
                            
                                                        if(tpclassList.contains('tp-animation-end')){
                                                            tpclassList.remove('tp-animation-end')
                                                        }else{
                                                            tpclassList.add('tp-animation-start')
                                                        }
                                                        self.style.cssText = `top:${secoffTop[index]}%`
                            
                                                        removeAnimation('end',tpclassList,startAni,startEnd)
                                                        if(startAni != 'none'){
                                                            addAnimation('start',tpclassList,startAni)
                                                        }

                                                    }else if( currentImg <= secStart ){
                                
                                                            removeAnimation('start',tpclassList,startAni)

                                                            if(startAni != 'none'){
                                                                addAnimation('end',tpclassList,startAni,startEnd)
                                                            }
                                
                                                            setTimeout(() => {
                                                                tpclassList.remove('tp-animation-start')
                                                                tpclassList.add('tp-animation-end')
                                                            }, 500);

                                                    }
                                                }
                                            }
                                        })

                                    lastScrollTop = tpStart <= 0 ? 0 : tpStart;
                                }

                                const addAnimation = (val,tpclassList,startAni,startEnd) =>{
                                    if(val == 'start'){
                                        tpclassList.add(`${startAni}`)
                                        tpclassList.add('tp-plus-animated')
                                    }
                                    if(val == 'end'){
                                        tpclassList.add(`${startEnd}`)
                                        tpclassList.add('tp-plus-animated')
                                    }
                                }

                                const removeAnimation = (val,tpclassList,startAni,startEnd) => {
                                    if(val == 'start'){
                                        tpclassList.remove(`${startAni}`)
                                        tpclassList.remove('tp-plus-animated')
                                    }
                                    if(val == 'end'){
                                        tpclassList.remove(`${startEnd}`)
                                        tpclassList.remove('tp-plus-animated')
                                    }
                                }

                                let lastWindowWidth, lastWindowHeight;
                                function scrollEventFrame(scrollPos, evtReq) {
                                    switch (evtReq) {
                                        case "scroll":
                                            scrollEventFun(scrollPos)
                                            break;
                                        case "resize":
                                            if (document.documentElement.clientWidth !== lastWindowWidth) {
                                                lastWindowWidth = document.documentElement.clientWidth
                                                scrollInitialCal()
                                                renderCanvas(scrollCurrent.image)
                                            } else if (document.documentElement.clientHeight !== lastWindowHeight) {
                                                lastWindowHeight = document.documentElement.clientHeight
                                                renderCanvas(scrollCurrent.image)
                                            }
                                            break;
                                        case "orientationchange":
                                            scrollInitialCal()
                                            renderCanvas(scrollCurrent.image)
                                            break;
                                        default:
                                            console.log('Sorry error not recognized.')
                                    }
            
                                }
                                function scrollEventTrigger(scrollPos, evtReq) {
                                    if (!ticking) {
                                        window.requestAnimationFrame(function () {
                                            scrollEventFrame(scrollPos, evtReq);
                                            ticking = false;
                                        });
                                        ticking = true;
                                    }
                                }
                                function initialDraw() {
                                    scrollInitialCal();
                                    scrollCalc(window.scrollY)
                                    scrollEventFun(window.scrollY);
                                    switch (lastScrollStatus) {
                                        case 'before':
                                            scrollEventFun(scrollCalcVal.scStart);
                                            scrollEventFun(window.scrollY);
                                        break;
                                        case 'active':
                                            scrollEventFun(window.scrollY);
                                        break;
                                        case 'after':
                                            scrollEventFun(scrollCalcVal.scEnd - 1);
                                            scrollEventFun(window.scrollY);
                                        break;
                                    }
                                    /**All Window triggers*/
                                    window.addEventListener("scroll", ()=>{ scrollEventTrigger(window.scrollY, 'scroll') });
                                    window.addEventListener("resize", ()=> { scrollEventTrigger(window.scrollY, 'resize') });
                                    window.addEventListener("orientationchange", ()=> { scrollEventTrigger(window.scrollY, 'orientationchange') });

                                    let numLoad = 0;
                                    setInterval(function () {
                                        numLoad = numLoad + 1;
                                        scrollInitialCal();
                                    }, 1000);
                                }
                                function scrollCalculate() {
                                    scrollInit.aascr = window.scrollY;
                                    scrollInit.active = {}
                                    scrollInit.active.pageProgress = scrollCurrent.pageProgress;
                                    scrollInit.active.image = scrollCurrent.image;
                                    scrollInit.active.pixel = scrollCurrent.pixel;
                                    scrollInit.active.isActive = scrollCurrent.isActive;
                                    /**When scrollsequence active*/  
                                    if (scrollInit.active.isActive) {
                                        scrollInit.active.curAbsImg = scrollInAbs(scrollInit.active.image);
                                    }
                                    /**Get Non-active Scrollsequence & Look for nearest*/
                                    scrollInit.notActive = [];
                                    /**look for next*/
                                    if (window.scrollY < scrollCalcVal.scStart) {
                                        scrollInit.notActive.push({
                                            type: 'next',
                                            distance: Math.abs(window.scrollY - scrollCalcVal.scStart),
                                            image: 0,
                                            pageProgress: 0,
                                        });
                                    }
                                    /**look for prev*/
                                    if (window.scrollY > scrollCalcVal.scEnd) {
                                        scrollInit.notActive.push({
                                            type: 'prev',
                                            distance: Math.abs(window.scrollY - scrollCalcVal.scEnd),
                                            image: imgGellary.length - 1,
                                            pageProgress: 1,
                                        });
                                    }
                                    /**find the smallest distance in the array and assign it as*/ 
                                    let notActiveArray = [];
                                    for (let x = 0; x < scrollInit.notActive.length; x++) {
                                        notActiveArray[x] = scrollInit.notActive[x].distance
                                    }
                                    /**sort by absolute value*/
                                    notActiveArray.sort((a, b) => a - b);
                                    /**nearest assign*/
                                    for (let x = 0; x < scrollInit.notActive.length; x++) {
                                        if (notActiveArray[0] === scrollInit.notActive[x].distance) {
                                            scrollInit.notActive.nearAbsImg = scrollInAbs( scrollInit.notActive[x].image)
                                            break
                                        } 
                                    }
                                    if (scrollInit.active.curAbsImg == undefined) {
                                        scrollInit.inCurImage = scrollInit.notActive.nearAbsImg
                                    }else{
                                        scrollInit.inCurImage = scrollInit.active.curAbsImg
                                    }
                                }
                                function renderCanvas(image, evtReq, info) { 
                                    if (scrollImage.objArray[image].imageLoad) {
                                        let canvasWidth, canvasHeight, canvasAspectRatio;
                                        let canvasDPR = 1;
                                        let conttt = mainWrap.querySelector(".tp-scroll-seq-inner");
                                        canvasWidth = conttt.offsetWidth;
                                        canvasHeight = conttt.offsetHeight;
                                        canvasAspectRatio = canvasWidth / canvasHeight
                                        scrollCanvas.style.width = canvasWidth+"px";
                                        scrollCanvas.style.height = canvasHeight+"px";
                                        canvasContext.canvas.width = canvasWidth * canvasDPR;
                                        canvasContext.canvas.height = canvasHeight * canvasDPR;
                                        
                                        if (canvasAspectRatio >= 1) { 
                                            renderScaled(scrollImage.objArray[image]);
                                        }else{  
                                            renderScaled(scrollImage.objArray[image]);
                                        }
                                    }else{ console.log('Image [image] was not loaded in time ') };
                                }
                                /**renderScaled function*/
                                function renderScaled(image) {
                                    
                                    var scale = Math.max(scrollCanvas.width / image.width, scrollCanvas.height / image.height),
                                        cWidth = (scrollCanvas.width / 2) - (image.width / 2) * scale,
                                        cHeight = (scrollCanvas.height / 2) - (image.height / 2) * scale;
                                    canvasContext.drawImage(image, Math.round(cWidth), Math.round(cHeight), Math.ceil(image.width * scale), Math.ceil(image.height * scale));
                                } 
                                function scrollInAbs(image) {
                                    for (let x = 0; x < scrollImage.imageTotalNo; x++) {
                                        if ( scrollImage.absoluteOrder[x][0] === image) {
                                            return x;
                                            break
                                        }
                                    }
                                }
                                function preloadImagesCallbackFunction() {
                                    this.imageLoad = true;
                                    imageWasLoaded(this.aPriority)
                                }
                                function imageWasLoaded(isPriority) {
                                    if (isPriority) {
                                        scrollImage.priorityCount = scrollImage.priorityCount + 1;
                                    }else{
                                        scrollImage.nonPriorityCount = scrollImage.nonPriorityCount + 1;
                                    }
                                    if (scrollImage.priorityCount >= scrollImage.priorityImageArray.length) {
                                        scrollImage.priorityDone = true;
                                    }
                                    if (scrollImage.nonPriorityCount >= scrollImage.nonPriorityImageCountRequired) {
                                        scrollImage.nonpriorityDone = true;
                                    }
                                    if (scrollImage.priorityDone && scrollImage.nonpriorityDone) {
                                        if (!scrollImage.firstDrawEn) {
                                            scrollImage.firstDrawEn = true;
                                            initialDraw()
                                            const event = document.createEvent('Event');
                                            event.initEvent('scrollPreloadPercentage', true, true);
                                            document.dispatchEvent(event);  
                                        }
                                    }
                                }
                                function scrollPrePriorityNonPriorityImages() {
                                    scrollImage.priorityImageArray = [];        
                                    for (let x = 0; x < scrollInit.notActive.length; x++) {
                                        var obr = scrollInit.notActive[x].image;
                                        scrollImage.priorityImageArray[x] = scrollInAbs(obr);
                                        scrollImage.objArray[obr].aPriority = true;
                                        scrollImage.objArray[obr].aImgInfo = { image: obr };
                                        scrollImage.objArray[obr].onload = preloadImagesCallbackFunction;
                                        scrollImage.objArray[obr].src = imgGellary[obr];
                                    }
                                    const divideInArrays = (arr1, arr2) => arr1.filter(el => !arr2.includes(el))
                                    scrollImage.nonPriorityImageArray = divideInArrays(scrollImage.dumbPreloadOrder, scrollImage.priorityImageArray)
                                    for (var i = 0; i < scrollImage.nonPriorityImageArray.length; i++) {
                                        var obr = scrollImage.absoluteOrder[scrollImage.nonPriorityImageArray[i]][0];
                                        scrollImage.objArray[obr].aPriority = false;
                                        scrollImage.objArray[obr].aImgInfo = { image: obr };
                                        scrollImage.objArray[obr].onload = preloadImagesCallbackFunction;
                                        scrollImage.objArray[obr].src = imgGellary[obr];
                                    }
                                    scrollImage.nonPriorityImageCountRequired = Math.round((scrollImage.imageTotalNo - scrollImage.priorityImageArray.length) * preloadImg)
                                }
                                function scrollPreloadArray(curImg, imageTotalLength) {
                                    var justOddArray = [];
                                    for (var i = 0; i < imageTotalLength / 2; i++) {
                                        justOddArray[i] = i * 2 + 1;
                                    }
                                    /**POSITIVE SIDE*/   
                                    var blueArrayLimit = [];
                                    for (var i = 0; i < (imageTotalLength - curImg) / 2; i++) {
                                        blueArrayLimit[i] = Math.log((imageTotalLength - curImg) / justOddArray[i]) / Math.log(2);
                                    }
                                    var blueArray = []
                                    for (var i = 0; i < (imageTotalLength - curImg) / 2; i++) {
                                        blueArray[i] = []
                                        for (var j = 0; j < blueArrayLimit[i]; j++) {
                                            blueArray[i][j] = Math.pow(2, [j]) * justOddArray[i] + curImg;
                                        };
                                    }
                                    var blueArrayLinear = [];
                                    var blueArrayLength = blueArray.length;
                                    for (var i = 0; i < blueArrayLength; i++) {
                                        var blueArrayLengthSub = blueArray[i].length;
                                        for (var j = 0; j < blueArrayLengthSub; j++) {
                                            blueArrayLinear.push(blueArray[i][j]);
                                        }
                                    }
                                    /**NEGATIVE SIDE*/ 
                                    var pinkArrayLimit = [];
                                    for (var i = 0; i < (curImg) / 2; i++) {
                                        pinkArrayLimit[i] = Math.log((curImg) / justOddArray[i]) / Math.log(2);
                                    }
                                    var pinkArray = []
                                    for (var i = 0; i < (curImg) / 2; i++) { /**or equal results in empty array*/
                                        pinkArray[i] = []
                                        for (var j = 0; j <= pinkArrayLimit[i]; j++) { /**if or equal is not there, zero does not get counted*/
                                            pinkArray[i][j] = -Math.pow(2, [j]) * justOddArray[i] + curImg;
                                        };
                                    }
                                    var pinkArrayLinear = []
                                    var pinkArrayLength = pinkArray.length;
                                    for (var i = 0; i < pinkArrayLength; i++) {
                                        var pinkArrayLengthSub = pinkArray[i].length;
                                        for (var j = 0; j < pinkArrayLengthSub; j++) {
                                            pinkArrayLinear.push(pinkArray[i][j]);
                                        }
                                    }
                                    var combinedArray = [curImg]
                                    var maxLengthOfBlueAndPink = Math.max(blueArrayLinear.length, pinkArrayLinear.length)
                                    for (var i = 0; i < maxLengthOfBlueAndPink; i++) {
                                        if (blueArrayLinear[i] !== undefined) {
                                            combinedArray.push(blueArrayLinear[i])
                                        }
                                        if (pinkArrayLinear[i] !== undefined) {
                                            combinedArray.push(pinkArrayLinear[i])
                                        }
                                    }
                                    scrollImage.dumbPreloadOrder = combinedArray;
                                }
                                function scrollCalc(scrollPos) {
                                    scrollCurrent.isActive = false
                                    if (scrollPos >= scrollCalcVal.scStart && scrollPos < scrollCalcVal.scEnd) {
                                        scrollCurrent.isActive = true

                                        if (scrollPos >= scrollCalcVal.scStart && scrollPos < scrollCalcVal.scEnd) {
                                            scrollCurrent.pageProgress = (scrollPos - scrollCalcVal.scStart) / (scrollCalcVal.scEnd)
                                            scrollCurrent.image = Math.round(scrollCurrent.pageProgress * (imgGellary.length - 1))
                                            scrollCurrent.pixel = scrollPos;
                                        }
                                    }    

                                    if (!scrollCurrent.isActive) {
                                        scrollCurrent.isActive = false;
                                        scrollCurrent.pageProgress;
                                        scrollCurrent.image;
                                        scrollCurrent.pixel = scrollPos;
                                    }  
                                }

                                setTimeout(()=>{
                                    if( scrollImage && scrollImage.imageTotalNo ){
                                        scrollInitialCal();
                                        scrollCalc(window.scrollY);
                                        scrollCalculate();
                                        scrollPreloadArray(scrollInit.inCurImage, scrollImage.imageTotalNo);
                                        scrollPrePriorityNonPriorityImages()
                                    }
                                }, 50);
                    }
            } 
        } 
    };  

    $(window).on('elementor/frontend/init', function () {
      elementorFrontend.hooks.addAction('frontend/element_ready/tp-scroll-sequence.default', WidgetScrollSequenceHandler);
    });
})(jQuery);