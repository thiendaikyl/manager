<?php

    use Librarys\File\FileInfo;
    use Librarys\App\AppDirectory;
    use Librarys\App\AppLocationPath;

    define('LOADED',               1);
    define('EXISTS_FUNC_OVERRIDE', 1);
    define('EXISTS_FUNC_SKIP',     2);
    define('EXISTS_FUNC_RENAME',   3);

    require_once('global.php');

    if ($appUser->isLogin() == false)
        $appAlert->danger(lng('login.alert.not_login'), ALERT_LOGIN, 'login.php');

    $title   = lng('import.title_page');
    $themes  = [ env('resource.theme.file') ];
    $scripts = [ env('resource.javascript.custom_input_file') ];
    $appAlert->setID(ALERT_IMPORT);
    require_once('header.php');

    if ($appDirectory->getDirectory() == null || is_dir($appDirectory->getDirectory()) == false)
        $appAlert->danger(lng('home.alert.path_not_exists'), ALERT_INDEX, env('app.http.host'));
    else if ($appDirectory->isPermissionDenyPath($appDirectory->getDirectory()))
        $appAlert->danger(lng('home.alert.path_not_permission', 'path', $appDirectory->getDirectory()), ALERT_INDEX, env('app.http.host'));

    $appLocationPath = new AppLocationPath($appDirectory, 'import.php?');
    $appLocationPath->setIsPrintLastEntry(true);

    $parameter = AppDirectory::createUrlParameter(
        AppDirectory::PARAMETER_DIRECTORY_URL, $appDirectory->getDirectory(), true,
        AppDirectory::PARAMETER_PAGE_URL,      $appDirectory->getPage(),      $appDirectory->getPage() > 1
    );

    $forms = [
        'files'       => null,
        'is_empty'    => true,
        'files_count' => 0,
        'exists_func' => EXISTS_FUNC_OVERRIDE
    ];
?>

    <?php $appAlert->display(); ?>
    <?php $appLocationPath->display(); ?>

    <div class="form-action">
        <div class="title">
            <span><?php echo lng('import.title_page'); ?></span>
        </div>
        <form action="import.php<?php echo $parameter; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="<?php echo $boot->getCFSRToken()->getName(); ?>" value="<?php echo $boot->getCFSRToken()->getToken(); ?>"/>

            <ul>
                <?php for ($i = 0; $i < $forms['files_count']; ++$i) { ?>
                    <li class="input-file"<?php if ($i === $forms['files_count'] - 1) { ?> id="template-input-file"<?php } ?> name="file_<?php echo $i; ?>">
                        <input type="file" name="files[]" id="file_<?php echo $i; ?>"/>
                        <label for="file_<?php echo $i; ?>">
                            <span lng="<?php echo lng('upload.form.input.choose_file'); ?>"><?php echo lng('upload.form.input.choose_file'); ?></span>
                        </label>
                    </li>
                <?php } ?>

                <li class="radio-choose">
                    <ul class="radio-choose-tab">
                        <li>
                            <input type="radio" name="exists_func" value="<?php echo EXISTS_FUNC_OVERRIDE; ?>" id="exists_func_override"<?php if ($forms['exists_func'] === EXISTS_FUNC_OVERRIDE) { ?> checked="checked"<?php } ?>/>
                            <label for="exists_func_override">
                                <span><?php echo lng('upload.form.input.exists_func_override'); ?></span>
                            </label>
                        </li>
                        <li>
                            <input type="radio" name="exists_func" value="<?php echo EXISTS_FUNC_SKIP; ?>" id="exists_func_skip"<?php if ($forms['exists_func'] === EXISTS_FUNC_SKIP) { ?> checked="checked"<?php } ?>/>
                            <label for="exists_func_skip">
                                <span><?php echo lng('upload.form.input.exists_func_skip'); ?></span>
                            </label>
                        </li>
                        <li>
                            <input type="radio" name="exists_func" value="<?php echo EXISTS_FUNC_RENAME; ?>" id="exists_func_rename"<?php if ($forms['exists_func'] == EXISTS_FUNC_RENAME) { ?> checked="checked"<?php } ?>/>
                            <label for="exists_func_rename">
                                <span><?php echo lng('upload.form.input.exists_func_rename'); ?></span>
                            </label>
                        </li>
                    </ul>
                </li>

                <li class="button">
                    <button type="button" onclick="javasctipt:onAddMoreInputFile('template-input-file', 'file_', '<?php echo lng('upload.form.input.choose_file'); ?>');">
                        <span><?php echo lng('upload.form.button.more'); ?></span>
                    </button>
                    <button type="submit" name="upload">
                        <span><?php echo lng('upload.form.button.upload'); ?></span>
                    </button>
                    <a href="index.php<?php echo $parameter; ?>">
                        <span><?php echo lng('upload.form.button.cancel'); ?></span>
                    </a>
                </li>
            </ul>
        </form>
    </div>

    <ul class="menu-action">
        <li>
            <a href="create.php<?php echo $parameter; ?>">
                <span class="icomoon icon-folder-create"></span>
                <span><?php echo lng('home.menu_action.create'); ?></span>
            </a>
        </li>
        <li>
            <a href="upload.php<?php echo $parameter; ?>">
                <span class="icomoon icon-folder-upload"></span>
                <span><?php echo lng('home.menu_action.upload'); ?></span>
            </a>
        </li>
    </ul>

<?php require_once('footer.php'); ?>

<?php
/* define('ACCESS', true);

    include_once 'function.php';

    if (IS_LOGIN) {
        $title = 'Tải lên tập tin';

        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>';

        if ($dir == null || !is_dir(processDirectory($dir))) {
            echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
            <div class="title">Chức năng</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
            </ul>';
        } else {
            $dir = processDirectory($dir);

            if (isset($_POST['submit'])) {
                $isEmpty = true;

                foreach ($_POST['url'] AS $entry) {
                    if (!empty($entry)) {
                        $isEmpty = false;
                        break;
                    }
                }

                if ($isEmpty) {
                    echo '<div class="notice_failure">Chưa nhập url nào cả</div>';
                } else {
                    for ($i = 0; $i < count($_POST['url']); ++$i) {
                        if (!empty($_POST['url'][$i])) {
                            $_POST['url'][$i] = processImport($_POST['url'][$i]);

                            if (!isURL($_POST['url'][$i]))
                                echo '<div class="notice_failure">URL <strong class="url_import">' . $_POST['url'][$i] . '</strong> không hợp lệ</div>';
                            else if (import($_POST['url'][$i], $dir . '/' . basename($_POST['url'][$i])))
                                echo '<div class="notice_succeed">Nhập khẩu tập tin <strong class="file_name_import">' . basename($_POST['url'][$i]) . '</strong>, <span class="file_size_import">' . size(filesize($dir . '/' . basename($_POST['url'][$i]))) . '</span> thành công</div>';
                            else
                                echo '<div class="notice_failure">Nhập khẩu tập tin <strong class="file_name_import">' . basename($_POST['url'][$i]) . '</strong> thất bại</div>';
                        }
                    }
                }
            }

            echo '<div class="list">
                <span>' . printPath($dir, true) . '</span><hr/>
                <form action="import.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull;</span>URL 1:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <span class="bull">&bull;</span>URL:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <span class="bull">&bull;</span>URL 3:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <span class="bull">&bull;</span>URL 4:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <span class="bull">&bull;</span>URL 5:<br/>
                    <input type="text" name="url[]" size="18"/><br/>
                    <input type="submit" name="submit" value="Nhập khẩu"/>
                </form>
            </div>

            <div class="tips"><img src="icon/tips.png"/> Không có http:// đứng trước cũng được, nếu có https:// phải nhập vào</div>

            <div class="title">Chức năng</div>
            <ul class="list">
                <li><img src="icon/create.png"/> <a href="create.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Tạo mới</a></li>
                <li><img src="icon/upload.png"/> <a href="upload.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Tải lên tập tin</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
            </ul>';
        }

        include_once 'footer.php';
    } else {
        goURL('login.php');
    }*/

?>