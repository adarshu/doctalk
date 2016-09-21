var gmenustate = true;

function toggleView(menuState) {
    if (!menuState) {
        $("#messages-main .ms-body").css("display", "block");
        $("#messages-main .ms-menu").css("display", "none");
    }
    else {
        $("#messages-main .ms-body").css("display", "none");
        $("#messages-main .ms-menu").css("display", "block");
    }
    gmenustate = menuState;
}

function loadUsersMenu() {
    $.getJSON("/api.php?a=users", function (data) {
        $(".lv-user").html(data.html);
    });
}

function loadMsgsForUser(pid, doFunc) {
    $.getJSON("/api.php?a=msgs&pid=" + pid, function (data) {
        $(".lv-body").html(data.html);
        $(".lvh-label").html(data.top);
        doFunc();
        $(".lv-body").scrollTop($(".lv-body")[0].scrollHeight);
    });
}

$(document).ready(function () {
    // PRODUCTION
    var pubnub = PUBNUB.init({
        publish_key: 'pub-c-2ead9eb0-71c7-42d0-846c-54d1d05aee61',
        subscribe_key: 'sub-c-f13159c4-0b8c-11e4-ae9d-02ee2ddab7fe',
        ssl: true
    })

    pubnub.subscribe({
        channel: "doctalk-ch",
        message: handleEvent,
        connect: connected
    });

    function connected(msg) {
        //alert(1);
    }

    function handleEvent(m) {
        console.log('m: ' + m.event);
        if (m.event == "incoming") {
            if (gmenustate)
                loadUsersMenu();
            else
                loadMsgsForUser(m.user, function () {
                });
        }
    }

    $("#ms-menu-trigger").on("click", function () {
        loadUsersMenu();
        toggleView(true);
    });

    $(document).on('click', '.user-node', function () {
        var pid = $(this).attr("data-pid");
        loadMsgsForUser(pid, function () {
            toggleView(false);
        });
    });

    $(document).on('click', '#send-chat', function () {
        var msg = $("#chat-message").val();
        var pid = $(".top-avatar").attr("data-pid");
        $.getJSON("/api.php?a=postmsg&pid=" + pid + "&msg=" + encodeURIComponent(msg), function (data) {
            $("#chat-message").val('');
            $(".lv-body").html(data.html);
            $(".lv-body").scrollTop($(".lv-body")[0].scrollHeight);
            $(".lvh-label").html(data.top);
        });
    });

    loadUsersMenu();

});
