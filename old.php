<html lang="en">
<head>
    <title>SkipScene 2 </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="/common/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/common/vendor/bootstrap/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/common/vendor/css/bootstrap-flat.min.css">
    <link rel="stylesheet" href="/common/vendor/css/bootstrap-flat-extras.min.css">
    <link rel="stylesheet" href="/common/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/theme.css">
    <link rel="stylesheet" href="/css/main.gen.css">
    <script src="/common/vendor/js/jquery.min.js"></script>

    <style>
        body {
            background-color: black;
        }

        div.absolute {
            position: absolute;
            top: 280px;
            left: 50px;
            width: 200px;
            height: 100px;
            border: 1px solid #73AD21;
        }

        div.relative {
            position: relative;
            left: 150px;
            top: -545px;
            border: 1px solid black;
            height: 480px;
            width: 852px;
        }

        .main {
            background-color: black !important;
            color: white;
            background: #000 url("/SkipScene/snlbg.jpg") no-repeat center center fixed;
            background-size: cover;
        }

        .cover-container {
            height: 180px;
            width: 100%;
            white-space: nowrap;
            overflow-x: hidden;
            overflow-y: hidden;
        }

        .cover-item {
            position: relative;
            display: inline-block;
            margin: 8px 8px;
            box-shadow: 2px 2px 4px #bbb;
            border-top-right-radius: 4px;
            width: 350px;
            height: 197px;
            vertical-align: bottom;
            background-position: top left;
            background-repeat: no-repeat;
            background-size: cover;
            cursor: pointer;
        }

        .under-epi {
            font-size: 16px;
            padding-top: 10px;
        }
    </style>
</head>
<body>
<div class="page-whole" style="margin-top: 10px">
    <!-- main -->
    <div class="page-body container center-margin" style="margin-top: 30px;">
        <div class="step1 pointer hide">
            <img class='img-responsive center-margin col-sm-8' src="/SkipScene/hacklogo.png" style="margin-top: 200px; margin-bottom: 10px"/>
        </div>
        <div class="step2" style="display: block">
            <img class='img-responsive center-margin' src="/SkipScene/iphonebghoriz.png" style="margin-top: 20px; margin-bottom: 10px"/>

            <div class="relative main">
                <div class="col-sm-4" style="padding: 15px"><span style="font-weight: bold">UBER</span> ETA <span style="font-weight: bold; padding-left: 10px">19 min</span></div>
                <div class="init-1">
                    <img class='img-responsive center-margin col-sm-5' src="/SkipScene/hacklogo.png" style="margin-top: 50px; margin-bottom: 10px"/>

                    <div class="col-lg-12 col-md-10" style="margin-top: 50px">
                        <div class="cover-container">
                            <div class="cover-item" style="background-image: url('/SkipScene/snl_epi.jpg')"></div>
                            <div class="cover-item robot-btn" style="background-image: url('/SkipScene/mrrobot_epi.png')"></div>
                            <div class="cover-item" style="background-image: url('/SkipScene/fire_epi.jpg')"></div>
                            <div class="cover-item" style="background-image: url('/SkipScene/grimm_epi.jpg')"></div>
                        </div>
                        <div class="under-epi">
                            <div class="col-sm-4" style="padding-left: 8px">Saturday Night Live - S41 E1696</div>
                            <div class="col-sm-4" style="padding-left: 105px">Mr Robot - S1 E1</div>
                            <div class="col-sm-4" style="padding-left: 202px">Chicago</div>
                        </div>
                    </div>

                    <div class="col-lg-12 center" style="margin-top: 35px">Shows have been compressed to fit your ETA</div>
                </div>
                <div class="init-2" style="display: none">
                    <img class='img-responsive center-margin col-sm-5' src="/SkipScene/hacklogo.png" style="margin-top: 50px; margin-bottom: 10px"/>

                    <div class="col-lg-12 col-md-10" style="margin-top: 50px">
                        <div class="cover-container">
                            <div class="cover-item" style="background-image: url('/SkipScene/snl_epi.jpg')"></div>
                            <div class="cover-item" style="background-image: url('/SkipScene/mrrobot_epi.png')"></div>
                            <div class="cover-item" style="background-image: url('/SkipScene/fire_epi.jpg')"></div>
                            <div class="cover-item" style="background-image: url('/SkipScene/grimm_epi.jpg')"></div>
                        </div>
                        <div class="under-epi">
                            <div class="col-sm-4" style="padding-left: 8px">Saturday Night Live - S41 E1696</div>
                            <div class="col-sm-4" style="padding-left: 105px">Mr Robot - S1 E1</div>
                            <div class="col-sm-4" style="padding-left: 202px">Chicago</div>
                        </div>
                    </div>

                    <div class="col-lg-12 center" style="margin-top: 35px">Shows have been compressed to fit your ETA</div>
                </div>
            </div>

            <div class="player-panel" style="display: none">
                Put player panel here
            </div>

            <div class="graph-panel"style="display: none">
                Put "like" graph stuff here
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".step1").on("click", function () {
            $(this).fadeOut(0, function () {
                $(".step2").fadeIn(0);
            });
        });

        $(".robot-btn").on("click", function () {
            $(".init-1").fadeOut(1000, function () {
                $(".init-2").fadeIn(500);
                $(".player-panel, .graph-panel").show();
            });
        });
    });
</script>
</body>
</html>