/*Switcher*/
( function( $ ) {
	"use strict";
	var WidgetSwitcherHandler = function ($scope, $) {
        class tp_switcher{
            constructor() {
                this.container = $scope[0].querySelectorAll('.theplus-switcher');
                this.tp_switcher();
            }
            
            tp_switcher (){
                let $this = this,
                    hash = window.location.hash,
                    switch_1_toggle = $this.container[0].querySelectorAll('.switch-1'),
                    switch_2_toggle = $this.container[0].querySelectorAll('.switch-2');

                    $this.switch_toggle = $this.container[0].querySelectorAll('.switch-toggle');
                    $this.switch_section_1 = $this.container[0].querySelector('.switcher-section-1'),
                    $this.switch_section_2 = $this.container[0].querySelector('.switcher-section-2');
                    $this.TGclass = $this.container[0].querySelectorAll('.switcher-toggle');

                    if( $this.switch_toggle.length > 0 ){
                        $this.switch_toggle[0].addEventListener("click", function(e){   
                            $this.tp_switcherToggle();
                            $this.tp_switchtoggle();
                            $this.tp_resize();
                        });
                    }

                    if( switch_1_toggle.length > 0 ){
                        switch_1_toggle[0].addEventListener("click", function(e){
                            $this.tp_switch1_toggle();
                        });
                    }

                    if( switch_2_toggle.length > 0 ){
                        switch_2_toggle[0].addEventListener("click", function(e){
                            $this.tp_switch2_toggle();
                        });
                    }

                    if( hash ){
                        $this.tp_hash_url(hash);
                    }
            }

            tp_switchtoggle (){
                if( this.TGclass[0].classList.contains('active') ){
                    this.tp_hide(this.switch_section_1);
                    this.tp_show(this.switch_section_2);
                }else{
                    this.tp_show(this.switch_section_1);
                    this.tp_hide(this.switch_section_2);
                }
            }

            tp_switch1_toggle (){
                if( this.TGclass[0].classList.contains('inactive') ){
                    return;
                }

                this.switch_toggle[0].checked = false;

                this.tp_show(this.switch_section_1);
                this.tp_hide(this.switch_section_2);
                this.tp_switcherToggle();
                this.tp_resize();
                return;
            }

            tp_switch2_toggle (){
                if( this.TGclass[0].classList.contains('active') ){
                    return;
                }

                this.switch_toggle[0].checked = true;

                this.tp_hide(this.switch_section_1);
                this.tp_show(this.switch_section_2);
                this.tp_switcherToggle();
                this.tp_resize();

                return;
            }

            tp_switcherToggle (){
                if( this.TGclass[0].classList.contains('active') ){
                    this.TGclass[0].classList.remove('active');
                    this.TGclass[0].classList.add('inactive');
                }else{
                    this.TGclass[0].classList.remove('inactive');
                    this.TGclass[0].classList.add('active');
                }
            }

            tp_hash_url (hash){
                let $FindID = this.container[0].querySelectorAll(`${hash}`);
                if( $FindID.length > 0 ){
                    $FindID.forEach(function(self){
                        if( !self.classList.contains('active') ){
                            document.querySelector('html, body').animate({
                                scrollTop: $(hash).offset().top,
                            }, 1500);

                            if( self.classList.contains('switch-1') ){
                                self.click();
                            }
                            if( self.classList.contains('switch-2') ){
                                self.click();
                            }
                        }
                    });	
                }
            }

            tp_resize () {
                let FindSection = "";
                    if( this.TGclass[0].classList.contains('active') ){
                        FindSection = this.switch_section_2;
                    }else  if( this.TGclass[0].classList.contains('inactive') ){
                        FindSection = this.switch_section_1;
                    }

                if(FindSection){
                    let FindMetro = FindSection.querySelectorAll(`.list-isotope-metro .post-inner-loop`),
                        FindGrid = FindSection.querySelectorAll(`.list-isotope .post-inner-loop`),
                        Findslick = FindSection.querySelectorAll(`.list-carousel-slick .post-inner-loop`),
                        Findunfold = FindSection.querySelectorAll(`.tp-unfold-wrapper`);

                        if( FindMetro.length > 0 ){
                            setTimeout(function(){ 
                                theplus_setup_packery_portfolio('*');	
                            }, 10);
                        }
                        if( FindGrid.length > 0 ){
                            FindGrid.forEach(function(self){
                                setTimeout(function(){ 
                                    $(self).isotope('layout');
                                }, 10);
                            });
                        }
                        if( Findslick.length > 0 ){
                            Findslick.forEach(function(self){
                                $(self).slick('setPosition');
                            });
                        }
                        if( Findunfold.length > 0 ){
                            Findunfold.forEach(function(self){
                                let unfolinner = self.querySelectorAll('.tp-unfold-description .tp-unfold-description-inner'),
                                    unfoldunfold = self.querySelectorAll('.tp-unfold-last-toggle'),
                                    get_height_of_div = unfolinner[0].getBoundingClientRect().height,
                                    data_content_max_height = self.dataset.contentMaxHeight;

                                    if( get_height_of_div <= data_content_max_height ){
                                        if(unfoldunfold.length > 0){
                                            unfoldunfold[0].style.cssText = "display: none;";
                                        }
                                    }else{
                                        if(unfoldunfold.length > 0){
                                            unfoldunfold[0].style.cssText = "display: flex;";
                                        }
                                    }
                            });
                        }
                }
            }

            tp_show (elem) {
                elem.style.display = 'block';
            }

            tp_hide (elem) {
                elem.style.display = 'none';
            }
        }
        new tp_switcher();
	};

    window.addEventListener('elementor/frontend/init', (event) => {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-switcher.default', WidgetSwitcherHandler);
    });

})(jQuery);