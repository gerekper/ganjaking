jQuery(function () {
    var $ = jQuery;
    var subJSOptionOnloadValues = {}; // this is the initial values of the JS options
    var enableOptimizer = false;
    console.log('Optimization is on');
    function toggleAllOptions(enable = true){
        var options = $('.defer-sub-option');
        for(let x = 0; x < options.length; x++){
            if(enable){
                $(options[x]).parent().parent().removeClass('opt_disable') // select tr and remove class for disabling
            }else{
                $(options[x]).parent().parent().addClass('opt_disable') // select tr and add class for disabling
            }
        }
    }
    function init(){
        // enableOptimizer = $('#optimization_settings')
        var enableOptimizeSwitcher = $('#optimization_settings').parent();
        enableOptimizeSwitcher.on('click', '.ct-ultimate-gdpr-checkbox-switch', function(){
            if( enableOptimizeSwitcher.find('input').attr('checked') === "checked") {
                console.log('check all')
                toggleAllOptions()
            }else{
                toggleAllOptions(false)
                console.log('not check all')
            }
        })
    }
    init()
}); 
 