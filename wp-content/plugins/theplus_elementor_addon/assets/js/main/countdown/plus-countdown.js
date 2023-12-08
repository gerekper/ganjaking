( function( $ ) {
	"use strict";
	var WidgetCountDownHandler = function ($scope, $) {
        class tpcounter{
            constructor() {
                this.container = $scope[0].querySelectorAll('.tp-countdown');
                this.tp_load_counterdown();
            }

            tp_load_counterdown(){
                let GetBasic = (this.container[0] && this.container[0].dataset && this.container[0].dataset.basic) ? JSON.parse(this.container[0].dataset.basic) : '',                    
                    style = GetBasic.style;
                    this.Basic = GetBasic;
                    this.GetClassList = (this.container[0] && this.container[0].dataset && this.container[0].dataset.classlist) ? JSON.parse(this.container[0].dataset.classlist) : '';
                
                    if(GetBasic && GetBasic.type == "normal"){
                        if(style && style == 'style-1') {
                            this.tp_normal_style1();
                        }else if(style && style == 'style-2') {
                            this.tp_filpdown_style2();
                        }else if(style && style == 'style-3') {
                            this.tp_progressbar_style3();
                        }
                    }else if(GetBasic && GetBasic.type == "numbers"){
                        this.setTime = `tp-fakenumber-${this.Basic.widgetid}-${this.Basic.fakeinitminit}`;
                        this.tp_fakenumber_countdown();
                    }else if(GetBasic && GetBasic.type == "scarcity"){
                        if(this.Basic.storetype == "cookie"){
                            this.scarcityid = `tp-${this.Basic.type}-${this.Basic.style}-${this.Basic.widgetid}-${this.Basic.scarminit}`;
                        }
                        
                        if(style && style == 'style-1') {
                            this.tp_scarcity_style1();
                        }else if(style && style == 'style-2') {
                            this.FlipdownID = `tp-flipdown-${this.Basic.widgetid}`;
                            this.tp_scarcity_style2();
                        }else if(style && style == 'style-3') {
                            this.tp_scarcity_style3();
                        }
                    }     
            }

            tp_normal_style1(){
                let $this = this,
                    plus_countdown = this.container[0].querySelectorAll('.pt_plus_countdown');
                    
                    if( this.Basic.normalexpiry ){
                        if( plus_countdown.length > 0  ){
                            plus_countdown.forEach(function(self) {
                                $(self).downCount({
                                    date: $this.Basic.timer,
                                    offset: $this.Basic.offset,
                                    text_day: $this.Basic.days,
                                    text_hour: $this.Basic.hours,
                                    text_minute: $this.Basic.minutes,
                                    text_second: $this.Basic.seconds,
                                });
                                if($this.GetClassList){
                                    document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'block';
                                    document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'none';
                                }
                            });
                        }
                    }else {
                        this.tp_countdown_expiry();
                        if($this.GetClassList){
                            document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'none';
                            document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'block';
                        }
                        return;
                    }
            }

            tp_filpdown_style2(){
                let $this = this;
                    this.FlipdownID = `tp-flipdown-${this.Basic.widgetid}`;
                    
                if( this.container[0].classList.contains('countdown-style-2') ){
                    if( (this.Basic.normalexpiry) || (this.Basic.normalexpiry == false && this.Basic.expiry == 'none') ){
                        this.container[0].insertAdjacentHTML("afterbegin", `<div id ="${this.FlipdownID}" class="tp-scarcity-flipdown flipdown"></div>`);

                        let CounterDate = new Date(this.Basic.timer).getTime() /1000,
                            ThemeCr = this.tp_flipdown_gettheme();

                            new FlipDown(CounterDate, this.FlipdownID, {
                                theme: ThemeCr,
                                headings: [
                                    $this.Basic.days, 
                                    $this.Basic.hours, 
                                    $this.Basic.minutes, 
                                    $this.Basic.seconds
                                ],
                            })
                            .start()
                            .ifEnded(() => {
                                $this.tp_countdown_expiry();
                                if($this.GetClassList){
                                    document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'none';
                                    document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'block';
                                }
                            });
                            if($this.GetClassList){
                                document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'block';
                                document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'none';
                            }

                        if(this.Basic.fliptheme == 'mix'){
                            this.tp_flipdown_themechange();
                        }
                    }else{
                        $this.tp_countdown_expiry();
                        if($this.GetClassList){
                            document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'none';
                            document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'block';
                        }                       
                        return;
                    }

                    this.tp_enable_column();
                }
            }

            tp_progressbar_style3(){
                let $this = this;

                if( (this.Basic.normalexpiry) || (this.Basic.normalexpiry == false && this.Basic.expiry == 'none') ) {
                    this.tp_progressbar_sethtml();

                    let elements = this.container[0].querySelector(`#tp-sec-widget-${this.Basic.widgetid}`),
                        elementm = this.container[0].querySelector(`#tp-min-widget-${this.Basic.widgetid}`),
                        elementh = this.container[0].querySelector(`#tp-hour-widget-${this.Basic.widgetid}`),
                        elementd = this.container[0].querySelector(`#tp-day-widget-${this.Basic.widgetid}`),
                        param = this.tp_style3_styleobj();

                        if(elements){
                            
                            let CounterDate = new Date(this.Basic.timer).getTime(),
                                seconds = new ProgressBar.Circle(elements, param),
                                minutes = new ProgressBar.Circle(elementm, param),
                                hours = new ProgressBar.Circle(elementh, param),
                                days = new ProgressBar.Circle(elementd, param);
                            
                            var countInterval = setInterval(function() {
                                let now = new Date(),
                                    countTo = new Date(CounterDate),
                                    difference = (countTo - now);

                                let day = Math.floor(difference / (60 * 60 * 1000 * 24) * 1);
                                if (day <= 0) {
                                    day = 0;
                                }
                                days.animate(day / (day + 5), function() {
                                    $this.tp_progressbar_settext(days, day, $this.Basic.days);
                                });
                            
                                let hour = Math.floor((difference % (60 * 60 * 1000 * 24)) / (60 * 60 * 1000) * 1);
                                    if (hour <= 0) {
                                        hour = 0;
                                    }
                                    hours.animate(hour / 24, function() {
                                        $this.tp_progressbar_settext(hours, hour, $this.Basic.hours);
                                    });
                                
                                let minute = Math.floor(((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) / (60 * 1000) * 1);
                                    if (minute <= 0) {
                                        minute = 0;
                                    }
                                    minutes.animate(minute / 60, function() {
                                        $this.tp_progressbar_settext(minutes, minute, $this.Basic.minutes);
                                    });

                                let second = Math.floor((((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) % (60 * 1000)) / 1000 * 1);
                                    if (second <= 0) {
                                        second = 0;
                                    }
                                    seconds.animate(second / 60, function() {
                                        $this.tp_progressbar_settext(seconds, second, $this.Basic.seconds);
                                    });
                                        
                                        if(day + hour + minute + second <= 0) {                                        
                                            $this.tp_countdown_expiry();
                                            clearInterval(countInterval);                                       
                                        }
                            });                           
                        }
                }else{
                    this.tp_countdown_expiry();
                    return;
                }

                this.tp_enable_column();
            }

            tp_scarcity_style1(){
                let $this = this,
                    $uid = this.scarcityid,
                    plus_countdown = this.container[0].querySelectorAll('.pt_plus_countdown'),
                    addtime = new Date().getTime() + this.Basic.scarminit * 60000;

                if( plus_countdown.length > 0 ){

                    if(this.Basic.storetype == "cookie"){
                        if( !localStorage.getItem($uid) ){
                            localStorage.setItem($uid, + new Date(addtime) );
                        }

                        let updatedtime = this.updatedtime = + localStorage.getItem($uid);
                            this.looptime = updatedtime + Number(this.Basic.delayminit) * 60000;

                        this.container.forEach(function(self) {
                            $this.s1self = self;
                            let x = $this.s1Interval = setInterval(function() {
                                let minit = Math.floor( (updatedtime - new Date()) / 60000);                                
                                if((minit+1) > 0){
                                    let now = new Date(),
                                        distance = updatedtime - now,
                                        days = Math.floor(distance / (1000 * 60 * 60 * 24)),
                                        hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                                        minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
                                        seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                        if( self.querySelectorAll(".days").length > 0 ){
                                            self.querySelector(".days").innerHTML = days;
                                        }
                                        if( self.querySelectorAll(".hours").length > 0 ){
                                            self.querySelector(".hours").innerHTML = hours;
                                        }
                                        if( self.querySelectorAll(".minutes").length > 0 ){
                                            self.querySelector(".minutes").innerHTML = minutes;
                                        }
                                        if( self.querySelectorAll(".seconds").length > 0 ){
                                            self.querySelector(".seconds").innerHTML = seconds;
                                        }

                                        if($this.GetClassList){
                                            document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'block';
                                            document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'none';
                                        }
                                }else{
                                    $this.tp_countdown_expiry();
                                    if($this.GetClassList){
                                        document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'none';
                                        document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'block';
                                    }
                                    if( $this.Basic.fakeloop == false ){
                                        clearInterval(x);
                                    }
                                }
                            }, 1000);
                        });
                    }else if( this.Basic.storetype == "normal" ){
                        this.container.forEach(function(self) {
                            let x = setInterval(function() {
                                let updatedtime = + new Date(addtime),
                                    now = new Date(),
                                    distance = updatedtime - now;

                                let days = Math.floor(distance / (1000 * 60 * 60 * 24)),
                                    hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                                    minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
                                    seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                    if(days + hours + minutes + seconds >= 0){
                                        if( self.querySelectorAll(".days").length > 0 ){
                                            self.querySelector(".days").innerHTML = days;
                                        }
                                        if( self.querySelectorAll(".hours").length > 0 ){
                                            self.querySelector(".hours").innerHTML = hours;
                                        }
                                        if( self.querySelectorAll(".minutes").length > 0 ){
                                            self.querySelector(".minutes").innerHTML = minutes;
                                        }
                                        if( self.querySelectorAll(".seconds").length > 0 ){
                                            self.querySelector(".seconds").innerHTML = seconds;
                                        }
                                    }else{
                                        clearInterval(x);
                                    }
                            }, 1000);
                        });
                    }
                }
            }

            tp_scarcity_style2(){
                let $this = this,
                    $uid = this.scarcityid;
                    if( this.container[0].classList.contains('countdown-style-2') ){
                        this.container[0].insertAdjacentHTML("afterbegin", `<div id ="${this.FlipdownID}" class="tp-scarcity-flipdown flipdown"></div>`);
                        
                        if(this.Basic.storetype == "cookie"){
                            let addtime = new Date().getTime() + this.Basic.scarminit * 60000;
                            if( !localStorage.getItem($uid) ){
                                localStorage.setItem($uid, + new Date(addtime) );
                            }

                            let updatedtime = + localStorage.getItem($uid),
                                CounterDate = this.CounterDate = new Date(updatedtime).getTime() / 1000,
                                minit = Math.floor( (updatedtime - new Date()) / 60000),
                                ThemeCr = this.tp_flipdown_gettheme();
                                this.looptime = updatedtime + Number(this.Basic.delayminit) * Number(60000);
                            
                                if(minit >= 0){
                                    new FlipDown( CounterDate, this.FlipdownID, {
                                        theme: ThemeCr,
                                        headings: [
                                            $this.Basic.days, 
                                            $this.Basic.hours, 
                                            $this.Basic.minutes, 
                                            $this.Basic.seconds
                                        ],
                                    })
                                    .start()
                                    .ifEnded(() => {
                                        $this.tp_countdown_expiry();
                                        if($this.GetClassList){
                                            document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'none';
                                            document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'block';
                                        }
                                    });
                                    if($this.GetClassList){
                                        document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'block';
                                        document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'none';
                                    }
                                }else{
                                    this.tp_countdown_expiry();
                                    if($this.GetClassList){
                                        document.querySelector($this.GetClassList.duringcountdownclass).style.display = 'none';
                                        document.querySelector($this.GetClassList.afterexpcountdownclass).style.display = 'block';
                                    }
                                }
                        }else if(this.Basic.storetype == "normal"){
                            let addtime = new Date().getTime() + this.Basic.scarminit * 60000,
                                updatedtime = + new Date(addtime),
                                CounterDate = this.CounterDate = new Date(updatedtime).getTime() /1000,
                                ThemeCr = this.tp_flipdown_gettheme();
                        
                                new FlipDown( CounterDate, this.FlipdownID, {
                                    theme: ThemeCr,
                                    headings: [
                                        $this.Basic.days, 
                                        $this.Basic.hours, 
                                        $this.Basic.minutes, 
                                        $this.Basic.seconds
                                    ],
                                })
                                .start()
                                .ifEnded(() => {
                                });
                        }

                        if(this.Basic.fliptheme == 'mix'){
                            this.tp_flipdown_themechange();
                        }
                        
                        this.tp_enable_column();
                    }
            }
            
            tp_scarcity_style3(){ 
                let $this = this,
                    $uid = this.scarcityid,
                    addtime = new Date().getTime() + this.Basic.scarminit * 60000;

                    this.tp_progressbar_sethtml();

                let elements = this.container[0].querySelector(`#tp-sec-widget-${this.Basic.widgetid}`),
                    elementm = this.container[0].querySelector(`#tp-min-widget-${this.Basic.widgetid}`),
                    elementh = this.container[0].querySelector(`#tp-hour-widget-${this.Basic.widgetid}`),
                    elementd = this.container[0].querySelector(`#tp-day-widget-${this.Basic.widgetid}`),
                    param = this.tp_style3_styleobj();

                if(this.Basic.storetype == "cookie"){
                    if( !localStorage.getItem($uid) ){
                        localStorage.setItem($uid, + new Date(addtime) );
                    }

                    if(elements) {
                        let updatedtime = + localStorage.getItem($uid),
                            minit = Math.floor( (updatedtime - new Date()) / 60000);
                            this.looptime = updatedtime + Number(this.Basic.delayminit) * 60000;

                        let CounterDate = new Date(updatedtime).getTime(),
                            seconds = new ProgressBar.Circle(elements, param),
                            minutes = new ProgressBar.Circle(elementm, param),
                            hours = new ProgressBar.Circle(elementh, param),
                            days = new ProgressBar.Circle(elementd, param);

                        if((minit+1) > 0){
                            var countInterval = setInterval(function() {
                                let now = new Date(),
                                    countTo = new Date(CounterDate),
                                    difference = (countTo - now);

                                let day = Math.floor(difference / (60 * 60 * 1000 * 24) * 1);
                                    days.animate(day / (day + 5), function() {
                                        $this.tp_progressbar_settext(days, day, $this.Basic.days);
                                    });
                                
                                let hour = Math.floor((difference % (60 * 60 * 1000 * 24)) / (60 * 60 * 1000) * 1);
                                    hours.animate(hour / 24, function() {
                                        $this.tp_progressbar_settext(hours, hour, $this.Basic.hours);
                                    });
                                
                                let minute = Math.floor(((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) / (60 * 1000) * 1);
                                    minutes.animate(minute / 60, function() {
                                        $this.tp_progressbar_settext(minutes, minute, $this.Basic.minutes);
                                    });

                                let second = Math.floor((((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) % (60 * 1000)) / 1000 * 1);
                                    seconds.animate(second / 60, function() {
                                        $this.tp_progressbar_settext(seconds, second, $this.Basic.seconds);
                                    });

                                    if(day + hour + minute + second == 0) {
                                        $this.tp_countdown_expiry();

                                        if( $this.Basic.expirytype != "loop" ){
                                            clearInterval(countInterval);
                                        }
                                    }
                            });
                        }else{
                            $this.tp_countdown_expiry();
                        }
                    }
                }else if( this.Basic.storetype == "normal" ){
                    if(elements) {
                        let updatedtime = + new Date(addtime),
                            CounterDate = new Date(updatedtime).getTime(),
                            seconds = new ProgressBar.Circle(elements, param),
                            minutes = new ProgressBar.Circle(elementm, param),
                            hours = new ProgressBar.Circle(elementh, param),
                            days = new ProgressBar.Circle(elementd, param);
                        
                        let x = setInterval(function() {
                                let now = new Date(),
                                    countTo = new Date(CounterDate),
                                    difference = (countTo - now);

                                let day = Math.floor(difference / (60 * 60 * 1000 * 24) * 1);
                                    days.animate(day / (day + 5), function() {
                                        $this.tp_progressbar_settext(days, day, $this.Basic.days);
                                    });
                                
                                let hour = Math.floor((difference % (60 * 60 * 1000 * 24)) / (60 * 60 * 1000) * 1);
                                    hours.animate(hour / 24, function() {
                                        $this.tp_progressbar_settext(hours, hour, $this.Basic.hours);
                                    });
                                
                                let minute = Math.floor(((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) / (60 * 1000) * 1);
                                    minutes.animate(minute / 60, function() {
                                        $this.tp_progressbar_settext(minutes, minute, $this.Basic.minutes);
                                    });

                                let second = Math.floor((((difference % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) % (60 * 1000)) / 1000 * 1);
                                    seconds.animate(second / 60, function() {
                                        $this.tp_progressbar_settext(seconds, second, $this.Basic.seconds);
                                    });
                                
                              
                                if(day + hour + minute + second == 0) {
                                    clearInterval(x);
                                }

                            });
                    }
                }
                this.tp_enable_column();
            }

            tp_progressbar_sethtml(){
                if(this.Basic && this.Basic.widgetid){
                    let $HTML = `<div class="tp-countdown-counter"><div class="counter-part" id="tp-day-widget-${this.Basic.widgetid}"></div><div class="counter-part" id="tp-hour-widget-${this.Basic.widgetid}"></div><div class="counter-part" id="tp-min-widget-${this.Basic.widgetid}"></div><div class="counter-part" id="tp-sec-widget-${this.Basic.widgetid}"></div></div>`;
                                
                    this.container[0].insertAdjacentHTML("afterbegin", $HTML);
                }
            }

            tp_progressbar_settext(content, Number, Data){
                content.setText(`<span class="number">${Number}</span><span class="label">${Data}</span>`);
            }

            tp_fakenumber_countdown() {
                let $this = this,
                    $Basic = $this.Basic,
                    container = $this.container[0];
                    container.insertAdjacentHTML("afterbegin", `<div class="tp-fake-number"></div>`)

                let SetInterval,
                    $setTime = $this.setTime,
                    digit = container.querySelector('.tp-fake-number'),
                    init = digit.innerText,
                    initNum = $Basic.fakeinitminit,
                    end = $Basic.fakeend,
                    range = $Basic.fakerange,
                    interval = $Basic.fakeinterval,
                    loop = $Basic.fakeloop,
                    massage = $Basic.fackeMassage,
                    storetype = $Basic.storetype;

                    digit.innerHTML = massage.replace('{visible_counter}', `<span class="tp-fake-visiblecounter">${initNum}</span>` );

                    if(Number(initNum) >= Number(end)){
                        let startcount,remaining;
                        if(storetype == "normal"){
                            startcount = initNum;
                        }else if(storetype == "cookie"){
                            startcount = initNum;
                            if(!localStorage.getItem($setTime)){
                                $this.tp_fakereset_number($setTime, initNum);
                            }
                        }
                        
                        SetInterval = setInterval(function(){
                            let randomNum = (Number(range) > 1) ? Math.round(Math.random() * range) : Number(1);

                            if(storetype == "normal"){
                                remaining = initNum = initNum - randomNum;
                            }else if(storetype == "cookie"){
                                remaining = initNum = localStorage.getItem($setTime) - randomNum;
                                $this.tp_fakereset_number($setTime, initNum);
                            }

                            if( Number(remaining) >= Number(end) ){
                                $this.tp_fake_addnumber(massage, remaining, digit);
                            }else{
                                $this.tp_fake_addnumber(massage, end, digit);
                                if(loop){
                                    if(storetype == "normal"){
                                        initNum = startcount;
                                    }else if(storetype == "cookie"){
                                        $this.tp_fakereset_number($setTime, startcount);
                                    }
                                }else{
                                    clearInterval(SetInterval);
                                }
                            }
                        }, interval * 1000)
                    }

                    if( Number(initNum) <= Number(end) ){
                        let startcount, remaining;

                        if(storetype == "normal"){
                            startcount = initNum;
                        }else if(storetype == "cookie"){
                            startcount = initNum;
                        }
                        
                        SetInterval = setInterval(function(){
                            let randomNum = (Number(range) > 1) ? Math.round( Math.random() * range ) : Number(1);

                                if(storetype == "normal"){
                                    remaining = initNum = Number(initNum) + Number(randomNum);
                                }else if(storetype == "cookie"){
                                    remaining = initNum = Number(localStorage.getItem($setTime)) + Number(randomNum);
                                    $this.tp_fakereset_number($setTime, initNum);
                                }
                                
                                if( Number(remaining) <= Number(end) ){
                                    $this.tp_fake_addnumber(massage, remaining, digit);
                                }else{
                                    $this.tp_fake_addnumber(massage, end, digit);
                                    
                                    if(loop){
                                        if(storetype == "normal"){
                                            initNum = startcount;
                                        }else if(storetype == "cookie"){
                                            $this.tp_fakereset_number($setTime, startcount);
                                        }
                                    }else{
                                        clearInterval(SetInterval);
                                    }
                                }
                        }, interval * 1000);
                    }
            }

            tp_fake_addnumber(massage, Plusvalue, digit) {
                let Newval = massage.replace('{visible_counter}', `<span class="tp-fake-visiblecounter">${ Math.floor( Plusvalue ) }</span>` );
                    digit.innerText = "";
                    if(Newval){
                        digit.insertAdjacentHTML("afterbegin", Newval)
                    }else{
                        digit.insertAdjacentHTML("afterbegin", Math.floor( Plusvalue ))
                    }
            }

            tp_fakereset_number(Name, value) {
                localStorage.setItem(Name, value);
            }

            tp_flipdown_gettheme(){
                if(this.Basic.fliptheme == 'mix'){
                    return 'dark';
                }else if(this.Basic.fliptheme){
                    return this.Basic.fliptheme;
                }else{
                    return 'dark';
                }
            }

            tp_flipdown_themechange(){
                if( this.Basic.type == "scarcity" || this.Basic.type == "normal" ){
                    let html = this.container[0].querySelectorAll(".flipdown");
                    if( this.container[0] && html.length > 0 && html[0].id == this.FlipdownID ){
                        setInterval(() => {
                            document.body.classList.toggle('light-theme');
                            html[0].classList.toggle('flipdown__theme-dark');
                            html[0].classList.toggle('flipdown__theme-light');
                        }, 5000);
                    }
                }
            }

            tp_style3_styleobj(){
                return { duration: 200, color: "#000000", trailColor: "#ddd", strokeWidth: 5, trailWidth: 3 }
            }

            tp_countdown_expiry() {
                let $this = this,
                    Basic = this.Basic;

                if( ( Basic.fakeloop && Basic.expirytype != 'expiry' ) ){
                    if( Basic.type == "scarcity" ){
                        if( Basic.style == "style-1" ){
                            let minittt = Math.floor( (this.looptime - new Date()) / 60000),
                                addtime = new Date().getTime() + Basic.scarminit * 60000;
                                if( minittt < 0){
                                    localStorage.removeItem(this.scarcityid);
                                    this.updatedtime = localStorage.setItem(this.scarcityid, + new Date(addtime));
                                    this.tp_scarcity_style1();
                                }
                        }else if( Basic.style == "style-2" ){
                            let x = setInterval(function() {
                                let minittt = Math.floor( ($this.looptime - new Date()) / 60000);
                                    $this.container[0].innerHTML = "";
                                    if( minittt < 0 ){
                                        localStorage.removeItem($this.scarcityid);
                                        clearInterval(x);
                                        $this.tp_scarcity_style2();
                                    }
                            }, 1000);
                        }else if( Basic.style == "style-3" ){
                            let x = setInterval(function() {
                                let minittt = Math.floor( ($this.looptime - new Date()) / 60000);
                                    $this.container[0].innerHTML = "";
                                    if( minittt < 0 ){
                                        localStorage.removeItem($this.scarcityid);
                                        clearInterval(x);
                                        $this.tp_scarcity_style3();
                                    }
                            }, 1000);
                        }
                    }
                }
               
                if( Basic.expirytype == "expiry" ){
                    let $this = this,
                        expiry = (Basic) ? Basic.expiry : '',
                        minittt = Math.floor( (this.looptime - new Date()) / 60000),
                        addtime = new Date().getTime() + Basic.scarminit * 60000;

                        if( expiry == "redirect" ){
                            if( Basic.type == "scarcity" ){
                                if( Basic.style == "style-1" ){
                                    if(Basic.fakeloop){
                                        if( minittt < 0){
                                            localStorage.removeItem(this.scarcityid);
                                            this.updatedtime = localStorage.setItem(this.scarcityid, + new Date(addtime));
                                            this.tp_scarcity_style1();
                                        }else{
                                            if( !elementorFrontend.isEditMode() ){
                                                window.location.href = decodeURIComponent(Basic.expirymsg); 
                                            }
                                        }
                                    }else{
                                        if( !elementorFrontend.isEditMode() ){
                                            window.location.href = decodeURIComponent(Basic.expirymsg); 
                                        }
                                    }
                                }else if( Basic.style == "style-2" ){
                                    if(Basic.fakeloop){
                                        let x = setInterval(function() {
                                            let minittt = Math.floor( ($this.looptime - new Date()) / 60000);
                                                if( minittt < 0 ){
                                                    localStorage.removeItem($this.scarcityid);
                                                    clearInterval(x);
                                                    $this.tp_scarcity_style2();
                                                }else{
                                                    if( !elementorFrontend.isEditMode() ){
                                                        window.location.href = decodeURIComponent(Basic.expirymsg); 
                                                    }
                                                }
                                        }, 1000);
                                    }else{
                                        if( !elementorFrontend.isEditMode() ){
                                            window.location.href = decodeURIComponent(Basic.expirymsg); 
                                        }
                                    }
                                }else if( Basic.style == "style-3" ){
                                    let removecountdown = this.container[0].querySelectorAll(".tp-countdown .tp-countdown-counter");
                                    if( removecountdown.length > 0 ){
                                        removecountdown[0].remove();
                                    }
                                    if(Basic.fakeloop){
                                        let x = setInterval(function() {
                                            let minittt = Math.floor( ($this.looptime - new Date()) / 60000);
                                            if( minittt < 0 ){
                                                localStorage.removeItem($this.scarcityid);
                                                clearInterval(x);
                                                $this.tp_scarcity_style3();
                                            }else{
                                                if( !elementorFrontend.isEditMode() ){
                                                    window.location.href = decodeURIComponent(Basic.expirymsg); 
                                                }
                                            }
                                        }, 1000);
                                    }else{
                                        if( !elementorFrontend.isEditMode() ){
                                            window.location.href = decodeURIComponent(Basic.expirymsg); 
                                        }
                                    }
                                }
                            }else if( Basic.type == "normal" ){
                                if( !elementorFrontend.isEditMode() ){
                                    window.location.href = decodeURIComponent(Basic.expirymsg); 
                                }
                            }
                        }else if( expiry == "showmsg" ){
                            this.container.forEach(function(self) {
                                if( Basic.type == "scarcity" ){
                                    if( Basic.style == "style-1" ){
                                        let FindCountDown = self.querySelectorAll(".tp-countdown-expiry"),
                                            pt_plusclass = self.querySelectorAll(".pt_plus_countdown");
                                            if( minittt < 0 ){
                                                localStorage.removeItem($this.scarcityid);
                                                $this.updatedtime = localStorage.setItem($this.scarcityid, + new Date(addtime));

                                                if( FindCountDown.length > 0 ){
                                                    FindCountDown[0].remove();
                                                }
                                                clearInterval($this.s1Interval);
                                                $this.tp_show( pt_plusclass[0] );
                                                $this.tp_scarcity_style1();
                                            }else{
                                                if( FindCountDown.length == 0 ){
                                                    $this.tp_hide( pt_plusclass[0] );
                                                    self.insertAdjacentHTML("afterbegin", `<div class="tp-countdown-expiry">${Basic.expirymsg}</div>`);
                                                }
                                            }
                                    }else if( Basic.style == "style-2" ){
                                        if(Basic.fakeloop){
                                            let x = setInterval(function() {
                                                let minittt = Math.floor( ($this.looptime - new Date()) / 60000);
                                                    $this.container[0].innerHTML = "";
                                                    if( minittt < 0 ){
                                                        localStorage.removeItem($this.scarcityid);
                                                        clearInterval(x);
                                                        $this.tp_scarcity_style2();
                                                    }else{
                                                        self.innerHTML = `<div class="tp-countdown-expiry">${Basic.expirymsg}</div>`;
                                                    }
                                            }, 1000);
                                        }else{
                                            self.innerHTML = "";
                                            self.innerHTML = `<div class="tp-countdown-expiry">${Basic.expirymsg}</div>`;
                                        }
                                    }else if( Basic.style == "style-3" ){
                                        if(Basic.fakeloop){
                                            let x = setInterval(function() {
                                                let minittt = Math.floor( ($this.looptime - new Date()) / 60000);
                                                if( minittt < 0 ){
                                                    localStorage.removeItem($this.scarcityid);
                                                    self.innerHTML = "";
                                                    clearInterval(x);
                                                    $this.tp_scarcity_style3();
                                                }else{
                                                    self.innerHTML = "";
                                                    self.innerHTML = `<div class="tp-countdown-expiry">${Basic.expirymsg}</div>`;
                                                }
                                            }, 1000);
                                        }else{
                                            self.innerHTML = "";
                                            self.innerHTML = `<div class="tp-countdown-expiry">${Basic.expirymsg}</div>`;
                                        }
                                    }
                                }else if( Basic.type == "normal" ){
                                    self.innerHTML = "";
                                    self.innerHTML = `<div class="tp-countdown-expiry">${Basic.expirymsg}</div>`;
                                }
                            });
                        }else if( Basic.expiry == "showtemp" ){
                            let expriytmp = this.container[0].querySelectorAll(".tp-expriy-template");
                            if( Basic.type == "scarcity" ){
                                if( Basic.style == "style-1" ){
                                    let pt_plusclass = this.container[0].querySelectorAll(".pt_plus_countdown");
                                        if( minittt < 0 ){
                                            localStorage.removeItem($this.scarcityid);
                                            $this.updatedtime = localStorage.setItem($this.scarcityid, + new Date(addtime));
                                            
                                            if( pt_plusclass.length > 0 && pt_plusclass[0].classList.contains('tp-hide') ){
                                                pt_plusclass[0].classList.remove('tp-hide');
                                            }
                                            if( expriytmp.length > 0 ){
                                                expriytmp[0].classList.add('tp-hide');
                                            }

                                            clearInterval($this.s1Interval);
                                            $this.tp_scarcity_style1();
                                        }else{
                                            pt_plusclass[0].classList.add('tp-hide');

                                            if( expriytmp.length > 0 && expriytmp[0].classList.contains('tp-hide') ){
                                                expriytmp[0].classList.remove('tp-hide');
                                            }
                                            $this.tp_resize_layout();
                                        }
                                }else if(Basic.style == "style-2"){
                                    let removecountdown = this.container[0].querySelectorAll(".tp-scarcity-flipdown.flipdown");
                                        if( removecountdown.length > 0 ){
                                            removecountdown[0].innerHTML = "";
                                        }
                                        if( expriytmp[0].classList.contains('tp-hide') ){
                                            expriytmp[0].classList.remove('tp-hide');
                                            expriytmp[0].classList.add('tp-show');
                                        }
                                        if(Basic.fakeloop){
                                            let x = setInterval(function() {
                                                let minittt = Math.floor( ($this.looptime - new Date()) / 60000);
                                                if( minittt < 0 ){
                                                    localStorage.removeItem($this.scarcityid);

                                                    if( expriytmp[0].classList.contains('tp-show') ){
                                                        expriytmp[0].classList.remove('tp-show');
                                                    }
                                                    if( !expriytmp[0].classList.contains('tp-hide') ){
                                                        expriytmp[0].classList.add('tp-hide');
                                                    }

                                                    clearInterval(x);
                                                    $this.tp_scarcity_style2();
                                                }else{
                                                    self.innerHTML = `<div class="tp-countdown-expiry">${Basic.expirymsg}</div>`;
                                                }
                                            }, 1000);
                                        }
                                }else if(Basic.style == "style-3"){
                                    let removecountdown = this.container[0].querySelectorAll(".tp-countdown-counter");
                                    if( removecountdown.length > 0 ){
                                        removecountdown[0].remove();
                                    }
                                    if( expriytmp[0].classList.contains('tp-hide') ){
                                        expriytmp[0].classList.remove('tp-hide');
                                        expriytmp[0].classList.add('tp-show');
                                        this.tp_resize_layout();
                                    }
                                    if(Basic.fakeloop){
                                        let x = setInterval(function() {
                                            let minittt = Math.floor( ($this.looptime - new Date()) / 60000);
                                            if( minittt < 0 ){
                                                localStorage.removeItem($this.scarcityid);

                                                if( expriytmp[0].classList.contains('tp-show') ){
                                                    expriytmp[0].classList.remove('tp-show');
                                                }
                                                if( !expriytmp[0].classList.contains('tp-hide') ){
                                                    expriytmp[0].classList.add('tp-hide');
                                                }

                                                clearInterval(x);
                                                $this.tp_scarcity_style3();
                                            }
                                        }, 1000);
                                    }
                                }
                            }else if( Basic.type == "normal" ){
                                let expriytmp = this.container[0].querySelectorAll(".tp-expriy-template"),
                                    removecountdown = this.container[0].querySelectorAll(".tp-countdown-counter");
                                    if( removecountdown.length > 0 ){
                                        removecountdown[0].remove();
                                    }
                                    if( expriytmp[0].classList.contains('tp-hide') ){
                                        expriytmp[0].classList.remove('tp-hide');
                                        expriytmp[0].classList.add('tp-show');
                                        this.tp_resize_layout();
                                    }
                            }                       
                        }   
                }
            }

            tp_enable_column(){
                if( this.Basic.style == 'style-2' ){
                    let GetRotorGroup = this.container[0].querySelectorAll('.rotor-group');
                    
                    if( !this.Basic.daysenable ){
                        GetRotorGroup[0].remove();
                    } 

                    if( !this.Basic.hoursenable ){
                        GetRotorGroup[1].remove();
                    }

                    if( !this.Basic.minutesenable ){
                        GetRotorGroup[2].remove();
                    }

                    if( !this.Basic.secondsenable ){
                        GetRotorGroup[3].remove();
                    }

                    GetRotorGroup = this.container[0].querySelectorAll('.rotor-group');

                    if( !this.Basic.daysenable || !this.Basic.hoursenable || !this.Basic.minutesenable || !this.Basic.secondsenable ){
                        let getElem = GetRotorGroup[GetRotorGroup.length - 1]
                            getElem.style.setProperty('--setDisplay','none')
                    }

                }else if( this.Basic.style == 'style-3' ){
                    let GetRotorGroup = this.container[0].querySelectorAll('.counter-part');
                    if( !this.Basic.daysenable ){
                        GetRotorGroup[0].remove();
                    }

                    if( !this.Basic.hoursenable ){
                        GetRotorGroup[1].remove();
                    }

                    if( !this.Basic.minutesenable ){
                        GetRotorGroup[2].remove();
                    }

                    if( !this.Basic.secondsenable ){
                        GetRotorGroup[3].remove();
                    }
                }
            }

            tp_resize_layout(){
                let resize = document.querySelectorAll(".post-inner-loop");
                    resize.forEach(function(item, index) {
                        if( item.parentNode.classList.contains('list-isotope') ){
                            setTimeout(function(){
                                $(item).isotope('reloadItems').isotope();
                            }, 300);
                        }else if( item.parentNode.classList.contains('list-isotope-metro') ){
                            setTimeout(function(){	
                                theplus_setup_packery_portfolio("*");	
                            }, 300);
                        }
                    });
            }

            tp_show (elem) {
                elem.style.display = 'block';
            }

            tp_hide (elem) {
                elem.style.display = 'none';
            }

        }
        new tpcounter();
	};

    window.addEventListener('elementor/frontend/init', (event) => {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-countdown.default', WidgetCountDownHandler);	
    });
})(jQuery);