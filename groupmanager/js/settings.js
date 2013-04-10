function getSettings(){
    // generate a url from the /appinfo/routes.php to the
    // adminSettings Method in /controller/pagecontroller.php
    var url = OC.Router.generate('adminSettings');
    var formData = $('#settingsForm').serialize();
    $.post(url,formData,function(result){
        console.log(formData);
        // fill the div with the result from the url
        $('#groupmanagerSettings').html(result);      
        $('#save').click(function(event){
            // disable the standard action of the button (submit things)
            event.preventDefault();
            // generate a url from the /appinfo/routes.php to the
            // saveSettings Method in /controller/pagecontroller.php
            var saveUrl = OC.Router.generate('saveSettings');
            // put alle input fields from the form into the post query
            var formData = $('#settingsForm').serialize();
            $.post(saveUrl,formData,function(result){
                console.log("blblaaaaa");
                $('#notificationMod').html(result.data.notification);
            },"json");
         
        });	
    });
}

$(document).ready(function () {
    // be sure that all routes from /appinfo/routes.php are loaded
	OC.Router.registerLoadedCallback(function(){
        getSettings();
       
	});
});

