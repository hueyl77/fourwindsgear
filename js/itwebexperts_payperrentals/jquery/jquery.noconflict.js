var $jppr;

/******** Load jQuery if not present *********/
if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.11.1') {
    $jppr = window.jQuery.noConflict();
} else {
    // The jQuery version on the window is the one we want to use
    $jppr = window.jQuery.noConflict();
}