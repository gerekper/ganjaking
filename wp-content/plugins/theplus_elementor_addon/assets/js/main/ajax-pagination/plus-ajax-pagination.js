/**Ajax Paggination*/
var productData = document.querySelectorAll('.tp-ajax-paginate-wrapper');

if( productData.length > 0 ){
    productData.forEach(function(self, index){
        Ajaxpagination(self, index)
    })

    function Ajaxpagination(item, index){
        let getPostWidget = item.dataset.widgetid,
            scope = document.querySelectorAll(`.elementor-element-${getPostWidget}`),
            producAttr = (productData && productData[index].dataset.searchattr) ? JSON.parse(productData[index].dataset.searchattr) : [],
            Pagin = scope[0].querySelectorAll('.theplus-pagination'),
            oldOffset = producAttr.offset_posts ? producAttr.offset_posts : 0

        /**Pagination CLick */
        if(Pagin.length > 0){
            let pagBtn = Pagin[0].querySelectorAll('.tp-ajax-paginate'),
                totalResult = (item.dataset) ? Number(item.dataset.totalCount) : 0,
                displayPost = producAttr.display_post ? Number(producAttr.display_post) : 0,
                tpPagNum = Math.ceil(totalResult/displayPost);

                pagBtn.forEach(function(self) {
                    self.addEventListener('click', function(e){
                        e.preventDefault();
                        tpgbSkeleton_filter("visible",item);
    
                        var PageNumber=crntPage='';
                        if(!this.classList.contains('paginate-prev') && !this.classList.contains('paginate-next')){
                            PageNumber = Number(this.dataset.page);
                                
                            let offset = (Number(PageNumber) - Number(1) ) * Number(producAttr.display_post);
                                producAttr['new_offset'] = offset + Number(oldOffset);
    
                            let NextChild = this.nextElementSibling,
                                SecNextChild = (NextChild && NextChild.nextElementSibling) ? NextChild.nextElementSibling : '',
                                PrevChild = this.previousElementSibling,
                                SecPrevChild = (PrevChild && PrevChild.previousElementSibling) ? PrevChild.previousElementSibling : '';
    
                                if(NextChild && NextChild.classList.contains('tp-page-hide') && NextChild.classList.contains('tp-number')){
                                    NextChild.classList.remove('tp-page-hide');
                                }
                                
                                if(SecPrevChild && !SecPrevChild.classList.contains('tp-page-hide') && SecPrevChild.classList.contains('tp-number')){
                                    SecPrevChild.classList.add('tp-page-hide');
                                }else{
                                    PrevChild.classList.remove('tp-page-hide');
                                    if(SecNextChild && !SecNextChild.classList.contains('paginate-next') && PageNumber != 1){
                                        SecNextChild.classList.add('tp-page-hide');
                                    }
                                }

                                if( PageNumber > 0 ){
                                    addRemoveClass(PageNumber,Pagin,tpPagNum)
                                    crntPage = Pagin[0].querySelector('.current');

                                    if( crntPage.classList.contains('current') ){
                                        crntPage.classList.remove('current');
                                        this.classList.add('current');
                                    }
    
                                }
                        }
                        
                        if( this.classList.contains('paginate-prev') ){
                            crntPage = Pagin[0].querySelector('.tp-ajax-paginate.current');
                            PageNumber = Number(crntPage.previousElementSibling.dataset.page);

                            let offset = PageNumber ? (Number(PageNumber) - Number(1) ) * Number(producAttr.display_post) : '';
                                producAttr['new_offset'] = offset + Number(oldOffset);
    
                                crntPage.previousElementSibling.classList.add('current');
                                crntPage.classList.remove('current');
    
                                prevNextadd('prev', crntPage)
                                addRemoveClass(PageNumber, Pagin, tpPagNum)
                        }
    
                        if( this.classList.contains('paginate-next') ){
                            crntPage = Pagin[0].querySelector('.current');
                            PageNumber = Number(crntPage.nextElementSibling.dataset.page);

                            let offset = PageNumber ? (Number(PageNumber) - Number(1) ) * Number(producAttr.display_post) : '';
                                producAttr['new_offset'] = offset + Number(oldOffset);
                                crntPage.classList.remove('current')
                                crntPage.nextElementSibling.classList.add('current')
                                
                                prevNextadd('next',crntPage)
                                addRemoveClass(PageNumber,Pagin,tpPagNum)
                        }

                        paginateAjax(producAttr,item,index)
                    })
                })
        }
    }

    function prevNextadd(val, crntPage){
        let NextChild = crntPage.nextElementSibling,
            PrevChild = crntPage.previousElementSibling,
            SecPrevChild = (PrevChild && PrevChild.previousElementSibling) ? PrevChild.previousElementSibling : '';

        if( 'next' === val ){
            if( NextChild && SecPrevChild && NextChild.classList.contains('tp-number') ){
                SecPrevChild.classList.add('tp-page-hide') 
                NextChild.classList.remove('tp-page-hide')
            }
        }

        if( 'prev' === val ){
            if( NextChild && SecPrevChild.classList.contains('tp-number') ){
                NextChild.classList.add('tp-page-hide') 
                SecPrevChild.classList.remove('tp-page-hide')
            }
        }

    }

    function addRemoveClass(PageNumber, Pagin, tpPagNum){
        let GetPrev = Pagin[0].querySelectorAll('.paginate-prev'),
            GetNext = Pagin[0].querySelectorAll('.paginate-next')

        if( PageNumber > 1 ){
            addPageHide('remove', GetPrev)
        }else{
            addPageHide('add', GetPrev)
        }

        if( PageNumber == tpPagNum ){
            addPageHide('add', GetNext)
        }else{
            addPageHide('remove', GetNext)
        }
    }

    function addPageHide(type, GetPrev){
        if( GetPrev.length > 0 ){
            if( 'add' === type && !GetPrev[0].classList.contains('tp-page-hide') ){
                GetPrev[0].classList.add('tp-page-hide')
            }
            if( 'remove' === type && GetPrev[0].classList.contains('tp-page-hide') ){
                GetPrev[0].classList.remove('tp-page-hide')
            }
        }
    }

    function paginateAjax(producAttr,item,index) {
        var security = (producAttr.theplus_nonce) ? producAttr.theplus_nonce : '';

        jQuery.ajax({
            url : theplus_ajax_url,
            method : "POST",
            async: true,
            cache: false,
            data : {
                action : 'theplus_ajax_based_pagination',
                option : producAttr,
                nonce : security,
            },
            success: function(data){
                if( productData.length > 0 ){
                    productData[index].innerHTML = '';
                    productData[index].innerHTML = data.HtmlData;
                }
            },
            complete: function() {
                Resizelayout(index, producAttr);
                tpgbSkeleton_filter("hidden",item);
            }
        })
    }

    var tpgbSkeleton_filter = function(type, item) {
        let skeleton = item.querySelectorAll('.tp-skeleton');
        if( skeleton.length > 0 ){
            skeleton.forEach(function(self) {
                if( self.style.visibility == 'visible' && self.style.opacity == 1 ){
                    if( "hidden" === type ){
                        self.style.cssText = "visibility: hidden; opacity: 0;";
                    }
                }else{
                    if( "visible" === type ){
                        self.style.cssText = "visibility: visible; opacity: 1;";
                    }
                }
            });
        }
    }

    var Resizelayout = function(index, producAttr) {
        if( 'grid' === producAttr.layout || 'masonry' === producAttr.layout ){
            setTimeout(function(){
                jQuery(productData[index]).isotope('reloadItems').isotope();
            }, 1000);
        }else if( 'metro' === producAttr.layout ){
            setTimeout(function(){
                theplus_setup_packery_portfolio();	
            }, 1000);
        }
    }
}