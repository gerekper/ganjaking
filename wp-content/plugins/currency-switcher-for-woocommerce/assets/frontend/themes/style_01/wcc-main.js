jQuery(document).ready(function($){
    /* Do slide function when click main button-------------- */
    $(".wcc-switcher-style-01").find(".wcc-crnt-currency").on("click", function () {
        // Define which theme is clicked
        let $this;
        $this = $(this).parent();
        $thisCrnt = $this.find(".wcc-crnt-currency");
        $thisList = $this.find(".wcc-list");               

        $thisList.slideToggle();
        
        function toggleClass() {
            if ($thisCrnt.hasClass("wcc-list-opened")) {
                $thisCrnt.removeClass("wcc-list-opened");                
            } else {
                $thisCrnt.addClass("wcc-list-opened");                
            }            
        }        

        toggleClass()

        /* Do slide function when select item
          and add 'crnt' class to selected item ---------------- */
        $thisList.find("li").on("click", function () {
            var selectedItemHTML = $(this).html();
            $thisList.find(".crnt").removeClass("crnt");
            $(this).addClass("crnt");            
            $thisList.slideUp();            
            $thisCrnt.html(selectedItemHTML);
            $thisCrnt.removeClass("wcc-list-opened")
        });
    });

});

jQuery(document).ready(function($){
    $(document).on('click', '#wcc-switcher-style-01 ul li', function(){
        var code = $(this).data('code');
        $('.wcc_switcher_form_01 .wcc_switcher').val(code);
        setTimeout(function(){ $('form.wcc_switcher_form_01').submit(); }, 500);
    });
});