/*
 * Copyright (c) 2014, Gogohire, Inc. All rights reserved.
 * Proprietary and confidential. Unauthorized copying of any part of this file, via any medium is strictly prohibited without the express permission of Gogohire, Inc.
 */

/************************* on load setup ***************************/

$(document).ready(function () {
    setRevealPassword();
});

/************************* page utilities ***************************/

function standardFormValidate(formid) {
    $(formid).validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest(".controls").children(".text-error"));
//            element.closest(".form-group").addClass("has-error").removeClass("has-success")
        },
        errorClass: "input-error",
        validClass: "input-valid"
//        success: function(label) {
//            label.closest(".form-group").removeClass("has-error").addClass("has-success")
//        }
    });
}

function setBootstrapTabsOnHash() {
    $(function () {
        $(window).hashchange(function () {
            setBootstrapTabsOnHelper();
        });
        $(window).hashchange();
    });
}

function setBootstrapTabsOnHelper() {
// Javascript to enable link to tab
    var hash = document.location.hash;
    var prefix = "tab_";
    if (hash) {
        $('.nav-tabs a[href='+hash.replace("#", "#" + prefix)+']').tab('show');
    }

// Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash.replace(prefix,"");
    });
}

function setRevealPassword() {
    $('.reveal-password').attr("title", "Show Password");
    $('.reveal-password').click(function (e) {
        var item = '#' + $(this).attr('rel');
        var totype = "password";
        var titleshow = "Show Password";
        var addc = "fa-eye";
        var remc = "fa-eye-slash";
        if ($(item).attr('type') == "password") {
            totype = "text";
            titleshow = "Hide Password";
            var t = addc;
            addc = remc;
            remc = t;
        }
        $(this).find("i").removeClass(remc).addClass(addc);
        $(this).attr("title", titleshow);
        $(item).clone().attr('type', totype).insertAfter(item).prev().remove();
    });
}
