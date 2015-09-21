//Base theme script
(function ($) {

    $(document).ready(function ($) {
        //disable save button
        $("button.save").addClass('disabled').prop('disabled', true);
            
        if ($('#wavethemes_demo_import_demo-head').length){
            //show/hide export button
            if (!showBtnExport || showBtnExport == '0'){
                $('#wavethemes_demo_export_demo-head').parent().hide();
            }else{
                $('#wavethemes_demo_export_demo-head').trigger('click');
            }

            //hide some label
            $('#row_wavethemes_demo_import_demo_import_blocks').find('.label').find('label').hide();
            $('#row_wavethemes_demo_import_demo_import_pages').find('.label').find('label').hide();
            $('#row_wavethemes_demo_set_demo_set_demo_button').find('.label').find('label').hide();
            $('#row_wavethemes_demo_import_demo_btn_import_menu').find('.label').find('label').hide();

            //bind event when change overwrite blocks options
            if ($('#wavethemes_demo_import_demo_overwrite_blocks').length){
                //blocks
                $('#wavethemes_demo_import_demo_overwrite_blocks').on('change', function(){
                    var btnLink = $('#btn-import-block-url').val() + 'is_overwrite/' + $(this).val();
                    var onclickFunction = "setLocation('"+btnLink+"')";
                    $('#btn-import-block').attr('onclick', onclickFunction);
                });
                $('#wavethemes_demo_import_demo_overwrite_blocks').trigger('change');

                //pages
                $('#wavethemes_demo_import_demo_overwrite_pages').on('change', function(){
                    var btnLink = $('#btn-import-page-url').val() + 'is_overwrite/' + $(this).val();
                    var onclickFunction = "setLocation('"+btnLink+"')";
                    $('#btn-import-page').attr('onclick', onclickFunction);
                });
                $('#wavethemes_demo_import_demo_overwrite_pages').trigger('change');
            }
        }
        
        //update url when change style
        if ($('#wavethemes_demo_set_demo_demo_style').length){

            $('#wavethemes_demo_set_demo_demo_style').removeClass("disabled");
            $('#wavethemes_demo_set_demo_demo_style').prop("disabled", false);

            $('#wavethemes_demo_set_demo_demo_style_inherit').parent('.use-default').hide();
            $('#wavethemes_demo_set_demo_set_demo_button_inherit').parent('.use-default').hide();

            //blocks
            $('#wavethemes_demo_set_demo_demo_style').on('change', function(){
                var btnLink = $('#btn-set-demo-url').val() + '?style=' + $(this).val();
                var onclickFunction = "setLocation('"+btnLink+"')";
                $('#btn-set-demo').attr('onclick', onclickFunction);
                console.log(onclickFunction);
            });
            $('#wavethemes_demo_set_demo_demo_style').trigger('change');
        }
    });

})(jQuery);