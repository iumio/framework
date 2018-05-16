<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs.framework") ?>assets/images/favicon.ico/favicon.ico">
    <title><?= $this->code.' '.strtolower(ucfirst($this->codeTitle)).' - Environment '.(ucfirst(strtolower($this->env))) ?></title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />


    <!-- Bootstrap core CSS     -->
    <link href="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'bootstrap/' ?>css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'animate.css/' ?>animate.min.css" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'iumio-manager/' ?>css/light-bootstrap-dashboard.css" rel="stylesheet"/>


    <link href="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'iumio-manager/' ?>css/index.css" rel="stylesheet" />
    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'iumio-manager/' ?>css/demo.css" rel="stylesheet" />


    <!--     Fonts and icons     -->
    <link href="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'font-awesome/' ?>css/font-awesome.min.css" rel="stylesheet">
    <link href="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'/iumio-manager/' ?>css/pe-icon-7-stroke.css" rel="stylesheet" />

</head>
<body style="background: rgba(203, 203, 210, 0.15);">
<div class="se-pre-con"> <h3 class='validform'><?= 'An exception was generated - Environment '.(strtoupper(strtolower($this->env))) ?></h3> </div>
<div class="wrapper">
    <nav class="navbar navbar-default navbar-fixed fgm-black" style="font-size: 14px;color: white;min-height: 0px;margin-bottom: 0px">
        <div class="container">
            <div class="col-md-6">
                <a href="#"><i class="fa fa-pagelines"></i> <strong>Framework exception page</strong></a>
            </div>
            <div class="col-md-3 text-center">
                <a href="https://learn.framework.iumio.com"><i class="fa fa-book"></i> Framework documentation</a>
            </div>
            <div class="col-md-3 text-right">
                <a href="https://framework.iumio.com"><i class="fa fa-book"></i> Framework website</a>
            </div>
        </div>
    </nav>

    <nav class="navbar navbar-default navbar-fixed <?= $this->color_class_checked ?>">
        <div class="container">
            <div class="col-md-8">
                <div class="navbar-header w100 ">
                    <h4>An error was generated</h4>
                    <h5><?= $this->code.' '.$this->codeTitle ?></h5>
                    <p style="overflow: auto"><?= $this->explain ?></p>
                </div>
            </div>
            <div class="col-md-4" style="text-align: center;">
                <img src="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs.framework").'img/' ?>iumio.logo.white.framework.png" width="140" style="padding-top: 40px"/>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="header">
                                <h4 class="title text-center">Characteristics</h4>
                                <p></p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-config"></i> Uidie : <strong><?= $this->uidie ?></strong></p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-target"></i> Event code : <?= $this->code ?> <?= $this->codeTitle ?></p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-clock"></i> Date : <?php echo (new \DateTime($this->time))->format("d/m/Y").
                                        ' at '.(new \DateTime($this->time))->format("H:i:s") ?></p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-magnet"></i> Method : <?php echo  $_SERVER['REQUEST_METHOD'] ?></p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-server"></i> Environment : <?= ucfirst(strtolower($this->env)) ?></p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-link"></i> Path : <?= $_SERVER['REQUEST_URI'] ?></p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-user"></i> Referer IP : { <?php echo  $this->client_ip ?>}</p>
                                <hr>
                                <?php if ($this->type_error != null) { ?><p class="category fs16"><i class="pe-7s-close-circle"></i> Type : <?= $this->type_error ?></p><hr/><?php } ?>
                            </div>
                            <div class="content text-center">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title text-center">Details</h4>
                            </div>
                            <div class="content" style="padding-top: 0px">
                                <hr>
                                <p class="category fs16"><i class="pe-7s-info"></i> Message :  <span class="fw900 break-word"><?= $this->explain ?></span></h5></p>
                                <hr>
                                <?php if ($this->file_error != null) { ?>
                                    <p class="category fs16 "><i class="pe-7s-paperclip"></i> File :  <span class="fs16 filelink "><?= $this->file_error ?></span></p>

                                    <hr>
                                <?php } ?>

                                <?php if ($this->line_error != null) { ?>
                                    <p class="category fs16 "><i class="pe-7s-target"></i> Line :  <span class="fw900"><?= $this->line_error ?></span></p>
                                    <hr>
                                <?php } ?>

                                <p class="category fs16 "><i class="pe-7s-magic-wand"></i> Solution :  <span class=""><?= $this->solution ?></span></p>
                                <hr>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card" style="height: 310px">
                            <div class="header">
                                <h4 class="title">Event Logs</h4>
                                <p class="category">Last events</p>
                            </div>
                            <div class="content" style="overflow: auto;max-height: 220px">
                                <ul class="errorlastlog" attr-href="<?php $master =
                                    new \iumioFramework\Core\Masters\MasterCore();
                                    echo $master->generateRoute(
                                        "iumio_manager_logs_get",
                                        null,
                                        "ManagerApp"
                                    ) ?>">

                                </ul>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Trace</h4>
                                <p class="category">For Code : <?= $this->code ?> | Type : <?= $this->codeTitle ?></p>
                            </div>
                            <div class="content text-center">
                                <?php
                                    $trace = null;
                                    $trace = ($this->trace != null)? $this->trace : $this->getTrace();
                                    foreach ($trace as $one) {
                                        ?>
                                        <div class="content text-center card-content-new">
                                            <?php if (isset($one['file'])) { ?>
                                                <div class="typo-line">
                                            <span class="break-word"><p class="category">File</p>
                                                <?= ((isset($one['file']))? $one['file'] : "*") ?></span>
                                                </div>
                                            <?php } ?>
                                            <div class="typo-line">
                                            <span class="break-word"><p class="category">Function
                                                    <?= (isset($one['line']))? "& Line" : "" ?></p><?= (($one['class'] ?? '')).
                                                ($one['type'] ?? ''). $one['function'] ?> <?= (isset($one['line']))?
                                                    "on line ". $one['line'] : "" ?></span>
                                            </div>
                                        </div>
                                        <hr>
                                    <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="copyright pull-right">
                &copy; <?= date('Y') ?> <a href="https://framework.iumio.com">
                    iumio Framework</a>, let's create more simply
            </p>
        </div>
    </footer>
</div>
<?= (\iumioFramework\Core\Additional\TaskBar\TaskBar::getTaskBar() != "#none")?
    \iumioFramework\Core\Additional\TaskBar\TaskBar::getTaskBar() : "" ?>
</body>

<!--   Core JS Files   -->
<script src="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'jquery/' ?>jquery.js" type="text/javascript"></script>
<script src="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'bootstrap/' ?>js/bootstrap.min.js" type="text/javascript"></script>

<!--  Checkbox, Radio & Switch Plugins -->
<script src="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'iumio-manager/' ?>js/bootstrap-checkbox-radio-switch.js"></script>

<!--  Notifications Plugin    -->
<script src="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'iumio-manager/' ?>js/bootstrap-notify.js"></script>


<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
<script src="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'iumio-manager/' ?>js/demo.js"></script>

<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
<script src="<?= \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'iumio-manager/' ?>js/main.js"></script>

<?php \iumioFramework\Core\Additional\TaskBar\TaskBar::getJsTaskBar() ?>

</html>