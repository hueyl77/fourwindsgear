document.observe("dom:loaded", function() {
    $$('.damage-waiver-input').each(function(el,index) {
       $(el).observe('change', function() {
            var updateForm = el.up('form');
            if (typeof updateForm != 'undefined') {
                updateForm.submit();
            }
       });
    });
});