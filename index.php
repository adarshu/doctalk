<?php
require_once("common/includes/util_inc.php");
require_once("shared.php");
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DocTalk</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/messsages.css" rel="stylesheet">
    <link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="container ng-scope">
        <div class="block-header"><h2></h2></div>
        <div class="card m-b-0" id="messages-main" style="box-shadow:0 0 40px 1px #c9cccd;">
            <div class="ms-menu card" id="ms-scrollbar" style="box-shadow:0 0 40px 1px #c9cccd; height: 547px">
                <div class="ms-block">
                    <div class="ms-user"><img src="pics/anuhya.jpg" alt="">

                        <h3 class="q-title" align="center">Dr. Anuhya Uppula <br/></h3>
                        <h6 class="q-title hidden" align="center"><b>5</b> New Messages <br/></h6>
                    </div>
                    <div class="ms-block hidden"><a class="btn btn-primary btn-block ms-new" href="#"><span class="glyphicon glyphicon-envelope"></span>&nbsp; New Message</a></div>
                    <hr/>
                    <h4 style="padding: 10px 25px 10px 20px">Your Patients</h4>

                    <div class="listview lv-user m-t-20">
                    </div>
                </div>
            </div>
            <div class="ms-body">
                <div class="listview lv-message">
                    <div class="lv-header-alt clearfix">
                        <div id="ms-menu-trigger">
                            <div class="line-wrap">
                                <div class="line top"></div>
                                <div class="line center"></div>
                                <div class="line bottom"></div>
                            </div>
                        </div>
                        <div class="lvh-label">
                        </div>
                        <ul class="lv-actions actions list-unstyled list-inline">
                            <li class="hidden"><a data-toggle="dropdown" href="#" data-toggle="tooltip" data-placement="left" title="Tooltip on left"><span
                                        class="glyphicon glyphicon-trash"></span></a>
                                <ul class="dropdown-menu user-detail" role="menu">
                                    <li><a href="">Delete Messages</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="lv-body" id="ms-scrollbar" style="height:420px;">
                    </div>
                    <div class="clearfix"></div>
                    <div class="lv-footer ms-reply"><textarea id="chat-message" rows="10" placeholder="Send a message..."></textarea>
                        <button class="" id="send-chat"><span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="css/jquery.js"></script>
<script src="css/bootstrap.min.js"></script>
<script src="//cdn.pubnub.com/pubnub.min.js"></script>
<script src="css/js.js"></script>
</body>
</html>