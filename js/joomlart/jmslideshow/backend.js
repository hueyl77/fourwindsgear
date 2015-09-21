/**
 * Script for jmslideshow backend
 */
(function ($) {
    $(document).ready(function(){

        //Initial
        if($("#row_joomlart_jmslideshow_joomlart_jmslideshow_list_images").length){
            $("#row_joomlart_jmslideshow_joomlart_jmslideshow_list_images").find("td.label").remove();
            $("#row_joomlart_jmslideshow_joomlart_jmslideshow_list_images").find("td.value").attr("colspan", 2);

            //Hide shortcode config field
            //$("#row_joomlart_jmslideshow_joomlart_jmslideshow_description").hide();
            $('#joomlart_jmslideshow_joomlart_jmslideshow_description').attr('readonly', true);
        }

        //Show/hide list slide images
        $(document).delegate("#jm-slide-images h3", "click", function(){
            $(this).siblings("ul.slide-images").toggle(300);
        });

        $("#joomlart_jmslideshow_joomlart_jmslideshow_source").on("change", function(){
            if($(this).val() != 'images'){
                $("#row_joomlart_jmslideshow_joomlart_jmslideshow_list_images").fadeOut();
            }
            else{
                $("#joomlart_jmslideshow_joomlart_jmslideshow_folder").select();
                $("#row_joomlart_jmslideshow_joomlart_jmslideshow_list_images").fadeIn(1000);
            }
        });

        //Ajax list images from folder
        $("#joomlart_jmslideshow_joomlart_jmslideshow_folder").on('blur', function(){

            //Ajax list images
            var data = {
                form_key: window.FORM_KEY,
                folder_path: $(this).val(),
                website: websiteCode,
                store: storeCode
            };

            var latestImagePath = $("#latest_image_folder").val();
            if(latestImagePath != $(this).val()){

                if(typeof loadImagesUrl != 'undefined'){
                    //Ajax request to load images list form folder path
                    $.ajax({
                        type: 'POST',
                        url: loadImagesUrl,
                        data: data,
                        async: true,
                        beforeSend: function () {
                            showLoadingMask();
                        },
                        success: function (result) {
                            hideLoadingMask();

                            if($("#jm-slide-images").length){
                                $("#jm-slide-images").html(result).addClass("loaded").fadeIn(1000);

                                //Update current folder
                                if($("#latest_image_folder").length){
                                    $("#latest_image_folder").val($("#joomlart_jmslideshow_joomlart_jmslideshow_folder").val());
                                }
                            }
                        }
                    });
                }
            }else{
                if($("#jm-slide-images").hasClass("loaded")){
                    $("#jm-slide-images").fadeIn(1000);
                }
            }
        });

        if($("#joomlart_jmslideshow_joomlart_jmslideshow_folder").length){
            var dataSource = $("#joomlart_jmslideshow_joomlart_jmslideshow_source").val();
            if(dataSource == 'images' && $("#joomlart_jmslideshow_joomlart_jmslideshow_folder").val() != ''){
                $("#joomlart_jmslideshow_joomlart_jmslideshow_folder").trigger("blur");
            }
        }

        //Bind event to save button
        $("button.save").attr("onclick", "");
        $(document).delegate("button.save", "click", function (e) {
            var dataSource = $("#joomlart_jmslideshow_joomlart_jmslideshow_source").val();
            if(dataSource == 'images'){
                beforeSaveSlideConfig(e);
            }else{
                configForm.submit();
            }
        });

    });

    function beforeSaveSlideConfig(e) {

        if(makeDescShortCode()){
            //Save config
            if(configForm.submit()){
                showLoadingMask();
            }
        }
    }

    function makeDescShortCode(){
        var shortCode = "";
        var images = new Array();
        var titles = new Array();
        var links = new Array();
        var targets = new Array();
        var desc = new Array();

        //Get data from images form
        $('input[name="slide_images[]"]').each(function() {
            images.push($(this).val());
        });
        $('input[name="slide_titles[]"]').each(function() {
            titles.push($(this).val());
        });
        $('input[name="slide_links[]"]').each(function() {
            links.push($(this).val());
        });
        $('select[name="link_targets[]"] option:selected').each(function() {
            targets.push($(this).attr("value"));
        });
        $('textarea[name="slide_desc[]"]').each(function() {
            desc.push($(this).val());
        });
        //console.log(targets);

        //build short code
        $.each(images, function( index, value ) {
            shortCode += '[desc img="'+images[index]+'" url="'+links[index]+'" title="'+titles[index]+'" target="'+targets[index]+'"]\n';
            shortCode += desc[index] + '\n';
            shortCode += '[/desc]'+"\n\r";
        });
        //console.log(shortCode);

        //Assign short code
        if($("#joomlart_jmslideshow_joomlart_jmslideshow_description").length){
            $("#joomlart_jmslideshow_joomlart_jmslideshow_description").html(shortCode.trim());
            return true;
        }

        return false;
    }

    function showLoadingMask() {
        $("#loading-mask").css({"width": $(window).width() + "px", "height": $(window).height() + "px", "top": "0px", "z-index": "9999"});
        //of browser scroll
        $("body").css({ overflow: 'hidden' });
        $("#loading-mask").show();
    }

    function hideLoadingMask() {
        $("#loading-mask").css({"width": "auto", "height": "auto", "top": "auto"});
        //on browser scroll
        $("body").css({ overflow: 'inherit' });
        $("#loading-mask").hide();
    }

})(jQuery);