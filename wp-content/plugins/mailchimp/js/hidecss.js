/* show/hide on checkbox click */
    function showMe (box) {
        
        var chboxs = document.getElementsByName("mc_nuke_all_styles");
        var vis = "none";
        for(var i=0;i<chboxs.length;i++) { 
            if(chboxs[i].checked){
             vis = "none";
            }
            else{
              var vis = "";
              vis = "";
            }
        }
        document.getElementById(box).style.display = vis;
    
    
    }