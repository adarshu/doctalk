/*
 * Copyright (c) 2014, Gogohire, Inc. All rights reserved.
 * Proprietary and confidential. Unauthorized copying of any part of this file, via any medium is strictly prohibited without the express permission of Gogohire, Inc.
 */

/************************* dom utilities ***************************/

function getStyle(el, cssprop) {
    if (el.currentStyle) //IE
        return el.currentStyle[cssprop]
    else if (document.defaultView && document.defaultView.getComputedStyle) //Firefox
        return document.defaultView.getComputedStyle(el, "")[cssprop]
    else //try and get inline style
        return el.style[cssprop]
}

function getStyle(el) {
    if (el.currentStyle) //IE
        return el.currentStyle
    else if (document.defaultView && document.defaultView.getComputedStyle) //Firefox
        return document.defaultView.getComputedStyle(el, "")
    else //try and get inline style
        return el.style
}

function getHTMLOfSelection() {
    var range;
    if (document.selection && document.selection.createRange) {
        range = document.selection.createRange();
        return range.htmlText;
    }
    else if (window.getSelection || document.getSelection) {
        var selection = window.getSelection ? window.getSelection() : document.getSelection();
        if (selection.rangeCount > 0) {
            range = selection.getRangeAt(0);
            var clonedSelection = range.cloneContents();
            var div = document.createElement('div');
            div.appendChild(clonedSelection);
            return div.innerHTML;
        }
        else {
            return '';
        }
    }
    else {
        return '';
    }
}

function getRawSelection() {
    if (document.selection) {
        return document.selection.createRange().text;
    }
    else if (window.getSelection) {
        return window.getSelection();
    }
    else if (document.getSelection) {
        return  document.getSelection();
    }
}


//hook event to element
function hookEvent(element, eventName, callback) {
    if (typeof(element) == "string")
        element = document.getElementById(element);
    if (element == null)
        return;
    if (element.addEventListener) {
        if (eventName == 'mousewheel') {
            element.addEventListener('DOMMouseScroll',
                callback, false);
        }
        element.addEventListener(eventName, callback, false);
    }
    else if (element.attachEvent)
        element.attachEvent("on" + eventName, callback);
}

//unhook event to element
function unhookEvent(element, eventName, callback) {
    if (typeof(element) == "string")
        element = document.getElementById(element);
    if (element == null)
        return;
    if (element.removeEventListener) {
        if (eventName == 'mousewheel') {
            element.removeEventListener('DOMMouseScroll',
                callback, false);
        }
        element.removeEventListener(eventName, callback, false);
    }
    else if (element.detachEvent)
        element.detachEvent("on" + eventName, callback);
}

//do mouseweheel event
function MouseWheel(e) {
    e = e ? e : window.event;
    var wheelData = e.detail ? e.detail * -1 : e.wheelDelta / 40;
    //do something
    return cancelEvent(e);
}

//cancel event e
function cancelEvent(e) {
    e = e ? e : window.event;
    if (e.stopPropagation)
        e.stopPropagation();
    if (e.preventDefault)
        e.preventDefault();
    e.cancelBubble = true;
    e.cancel = true;
    e.returnValue = false;
    return false;
}

//select everything in element
function selectAll(objId) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(objId));
        range.select();
    }
    else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(objId));
        window.getSelection().addRange(range);
    }
}

//get caret postion of control
function getCaretPosition(ctrl) {
    var CaretPos = 0;
    // IE Support
    if (document.selection) {
        ctrl.focus();
        var Sel = document.selection.createRange();
        Sel.moveStart('character', -ctrl.value.length);
        CaretPos = Sel.text.length;
    }
    // Firefox support
    else if (ctrl.selectionStart || ctrl.selectionStart == '0')
        CaretPos = ctrl.selectionStart;
    return (CaretPos);
}

//set caret postion of control
function setCaretPosition(ctrl, pos) {
    if (ctrl.setSelectionRange) {
        ctrl.focus();
        ctrl.setSelectionRange(pos, pos);
    }
    else if (ctrl.createTextRange) {
        var range = ctrl.createTextRange();
        range.collapse(true);
        range.moveEnd('character', pos);
        range.moveStart('character', pos);
        range.select();
    }
}

function getUrlBase(url) {
    var result = getUrlParts(url);

    var port = "";
    if (result[4]) {
        port = ":" + result[4];
    }
    return result[1] + ":" + result[2] + result[3] + port;
}

function getUrlParam(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.href);
    if (results == null)
        return "";
    else
        return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function getUrlParts(url) {
    var parse_url = /^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/;

    var result = parse_url.exec(url);
    var names = [ 'url', 'scheme', 'slash', 'host', 'port', 'path', 'query', 'hash' ];
    return result;
}

function getWebSocketUrl(url) {
    var wsLocation = getUrl(url).replace('http://', 'ws://').replace('https://', 'wss://');
    return wsLocation;
}

/************************* utilities ***************************/

function enterPressed(e) {
    return (e.keyCode || e.which) == 13;
}

function getKeyCodeForEvent(ev) {
    if (window.event)
        return window.event.keyCode;
    return ev.keyCode;
}

function getTimestampNow() {
    return getToday() + " " + getTimeNow() + ": ";
}

function getToday() {
    var d = new Date();
    return ((d.getDate() < 10) ? "0" : "") + d.getDate() + "/" + (((d.getMonth() + 1) < 10) ? "0" : "") + (d.getMonth() + 1) + "/" + d.getFullYear();
}

function getTimeNow() {
    var d = new Date();
    return ((d.getHours() < 10) ? "0" : "") + d.getHours() + ":" + ((d.getMinutes() < 10) ? "0" : "") + d.getMinutes() + ":" + ((d.getSeconds() < 10) ? "0" : "") + d.getSeconds();
}

//time and date
function getTimeString() {
    var Days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday',
        'Thursday', 'Friday', 'Saturday')

    var today = new Date()
    var Year = takeYear(today)
    var Month = addLeadingZero(today.getMonth() + 1)
    var DayName = Days[today.getDay()]
    var Day = addLeadingZero(today.getDate())
    var Hours = today.getHours()
    var ampm = "am"
    if (Hours == 0) Hours = 12
    if (Hours > 11)
        ampm = "pm"
    if (Hours > 12)
        Hours -= 12
    Hours = addLeadingZero(Hours)
    var Minutes = addLeadingZero(today.getMinutes())
    var Seconds = addLeadingZero(today.getSeconds())

    var def = Hours + ':' + Minutes + ':' + Seconds + ' ' + ampm + ' ' + ' ' + Day + '-' + Month + '-' + Year
    var ret = Hours + '-' + Minutes + '-' + Seconds + '_' + ampm + '_' + Day + '-' + Month + '-' + Year
    return ret
}

function takeYear(theDate) {
    var x = theDate.getYear()
    var y = x % 100
    y += (y < 38) ? 2000 : 1900
    return y
}

function addLeadingZero(nr) {
    if (nr < 10) nr = "0" + nr
    return nr
}

function isValidEmail(email) {
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return filter.test(email);
}

function validateDomain(domain) {
    var pattern = new RegExp(/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/);
    return pattern.test(domain);
}

function validateEmail(email) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(email);
}


//replace all
function replaceAll(str, text, by) {
    // Replaces text with by in string
    var strLength = str.length, txtLength = text.length;
    if ((strLength == 0) || (txtLength == 0)) return str;

    var i = str.indexOf(text);
    if ((!i) && (text != str.substring(0, txtLength))) return str;
    if (i == -1) return str;

    var newstr = str.substring(0, i) + by;

    if (i + txtLength < strLength)
        newstr += replaceAll(str.substring(i + txtLength, strLength), text, by);

    return newstr;
}

function toProperCase(s) {
    return s.toLowerCase().replace(/^(.)|\s(.)/g, function ($1) {
        return $1.toUpperCase();
    });
}

//alert which mouse button is pressed
function whichMouseButton(e) {
    // Handle different event models
    var e = e || window.event;
    var btnCode;

    if ('object' == typeof e) {
        btnCode = e.button;

        switch (btnCode) {
            case 0  :
                alert('Left button clicked');
                break;
            case 1  :
                alert('Middle button clicked');
                break;
            case 2  :
                alert('Right button clicked');
                break;
            default :
                alert('Unexpected code: ' + btnCode);
        }
    }
}

/* other util file helpers */

//start reg key session
function regKeyShortcutStart() {
    ShortcutMan.keyShortcuts = {};
    document.onkeypress = ShortcutMan.readShortcut;
}

//reg key shortcut
function regKeyShortcut(keyp, functouse) {
    ShortcutMan.registerShortcut(keyp, functouse);
}

//start reg key event code show
function regKeyCodeStart() {
    ShortcutMan.testEventCode();
}

/************************* protos ***************************/

String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
};

//Array.indexOf( value, begin, strict ) - Return index of the first element that matches value
Array.prototype.indexOf = function (v, b, s) {
    for (var i = +b || 0, l = this.length; i < l; i++) {
        if (this[i] === v || s && this[i] == v) {
            return i;
        }
    }
    return -1;
};
// Array.random( range ) - Return a random element, optionally up to or from range
Array.prototype.random = function (r) {
    var i = 0, l = this.length;
    if (!r) {
        r = this.length;
    }
    else if (r > 0) {
        r = r % l;
    }
    else {
        i = r;
        r = l + r % l;
    }
    return this[ Math.floor(r * Math.random() - i) ];
};

// Array.shuffle( deep ) - Randomly interchange elements
Array.prototype.shuffle = function (b) {
    var i = this.length, j, t;
    while (i) {
        j = Math.floor(( i-- ) * Math.random());
        t = b && typeof this[i].shuffle !== 'undefined' ? this[i].shuffle() : this[i];
        this[i] = this[j];
        this[j] = t;
    }
    return this;
};

String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, '');
}
