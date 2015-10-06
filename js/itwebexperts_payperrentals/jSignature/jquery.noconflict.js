var $jpprSig;

/******** Load jQuery if not present *********/
if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.11.1') {
    $jpprSig = window.jQuery.noConflict();
} else {
    // The jQuery version on the window is the one we want to use
    $jpprSig = window.jQuery.noConflict();
}