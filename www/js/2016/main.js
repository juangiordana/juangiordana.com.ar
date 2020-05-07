$(document).ready(function() {
    /**
     * Reload captcha on image click.
     */
    $('.captcha').click(function() {
        $(this).attr({ src: '/captcha/?' + Math.random() });
    });
});
