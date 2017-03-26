<?php if (!defined('LOADED')) exit(0); ?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow, noodp, nodir"/>

        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Ubuntu"/>
        <link rel="stylesheet" type="text/css" href="<?php echo env('resource.theme.app'); ?>?rand=<?php echo rand(1000, 9000); ?>" media="all,handheld" />

        <?php if (isset($themes) && is_array($themes)) { ?>
            <?php foreach ($themes AS $entry) { ?>
                <link rel="stylesheet" type="text/css" href="<?php echo $entry; ?>?rand=<?php echo rand(1000, 9000); ?>" media="all,handheld" />
            <?php } ?>
            <?php unset($themes); ?>
        <?php } ?>

        <link rel="stylesheet" type="text/css" href="<?php echo env('resource.theme.icomoon'); ?>" media="all,handheld" />

        <link rel="icon" type="image/png" href="icon/icon.png">
        <link rel="icon" type="image/x-icon" href="icon/icon.ico" />
        <link rel="shortcut icon" type="image/x-icon" href="icon/icon.ico" />

        <?php if (isset($scripts) && is_array($scripts)) { ?>
            <?php foreach ($scripts AS $entry) { ?>
                <script type="text/javascript" src="<?php echo $entry; ?>?rand=<?php echo rand(1000, 9000); ?>"></script>
            <?php } ?>
            <?php unset($scripts); ?>
        <?php } ?>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <div id="logo">
                    <a href="#">
                        <span id="logo" class="icomoon icon-home"></span>
                    </a>
                </div>
                <div id="search">
                    <form action="#" method="post">
                        <input type="text" name="keyword" value=""/>
                        <button type="submit" name="serach">
                            <span class="icomoon icon-search"></span>
                        </button>
                    </form>
                </div>
                <ul id="action">
                    <?php if ($appUser->isLogin()) { ?>
                        <li>
                            <a href="#">
                                <span class="icomoon icon-mysql"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="icomoon icon-user"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="icomoon icon-config"></span>
                            </a>
                        </li>
                        <li class="about">
                            <a href="#">
                                <span class="icomoon icon-about"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="icomoon icon-close"></span>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li>
                            <a href="#">
                                <span class="icomoon icon-about"></span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div id="content">
