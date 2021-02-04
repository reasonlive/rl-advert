var expires = new Date();
expires.setTime(expires.getTime() + 2592000000);
$(document).ready(function(){
    $("#mnu_title1").click(function(){
        if (document.getElementById('mnu_tblock1').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock1="+val+"; path=/; expires="+expires;
        $("#mnu_tblock1").slideToggle("fast");
        return false;
    });
    $("#mnu_title2").click(function(){
        if (document.getElementById('mnu_tblock2').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock2="+val+"; path=/; expires="+expires;
        $("#mnu_tblock2").slideToggle("fast");
        return false;
    });
    $("#mnu_title3").click(function(){
        if (document.getElementById('mnu_tblock3').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock3="+val+"; path=/; expires="+expires;
        $("#mnu_tblock3").slideToggle("fast");
        return false;
    });
    $("#mnu_title4").click(function(){
        if (document.getElementById('mnu_tblock4').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock4="+val+"; path=/; expires="+expires;
        $("#mnu_tblock4").slideToggle("fast");
        return false;
    });
    $("#mnu_title5").click(function(){
        if (document.getElementById('mnu_tblock5').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock5="+val+"; path=/; expires="+expires;
        $("#mnu_tblock5").slideToggle("fast");
        return false;
    });
    $("#mnu_title6").click(function(){
        if (document.getElementById('mnu_tblock6').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock6="+val+"; path=/; expires="+expires;
        $("#mnu_tblock6").slideToggle("fast");
        return false;
    });
    $("#mnu_title7").click(function(){
        if (document.getElementById('mnu_tblock7').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock7="+val+"; path=/; expires="+expires;
        $("#mnu_tblock7").slideToggle("fast");
        return false;
    });
    $("#mnu_title8").click(function(){
        if (document.getElementById('mnu_tblock8').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock8="+val+"; path=/; expires="+expires;
        $("#mnu_tblock8").slideToggle("fast");
        return false;
    });
    $("#mnu_title9").click(function(){
        if (document.getElementById('mnu_tblock9').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock9="+val+"; path=/; expires="+expires;
        $("#mnu_tblock9").slideToggle("fast");
        return false;
    });
    $("#mnu_title10").click(function(){
        if (document.getElementById('mnu_tblock10').style.display == 'block') val = 0; else val = 1;
        document.cookie="vblock10="+val+"; path=/; expires="+expires;
        $("#mnu_tblock10").slideToggle("fast");
        return false;
    });
});