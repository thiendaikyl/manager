<?php

    use Librarys\File\FileInfo;
    use Librarys\App\AppDirectory;
    use Librarys\App\AppLocationPath;

    define('LOADED',      1);
    define('TYPE_FOLDER', 1);
    define('TYPE_FILE',   2);

    require_once('global.php');

    if ($appUser->isLogin() == false)
        $appAlert->danger(lng('login.alert.not_login'), ALERT_LOGIN, 'login.php');

    $title  = lng('create.title_page');
    $themes = [ env('resource.theme.file') ];
    $appAlert->setID(ALERT_CREATE);
    require_once('header.php');

    if ($appDirectory->getDirectory() == null || is_dir($appDirectory->getDirectory()) == false)
        $appAlert->danger(lng('home.alert.path_not_exists'), ALERT_INDEX, env('app.http.host'));
    else if ($appDirectory->isPermissionDenyPath($appDirectory->getDirectory()))
        $appAlert->danger(lng('home.alert.path_not_permission', 'path', $appDirectory->getDirectory()), ALERT_INDEX, env('app.http.host'));

    $appLocationPath = new AppLocationPath($appDirectory, 'create.php?');
    $appLocationPath->setIsPrintLastEntry(true);

    $parameter = AppDirectory::createUrlParameter(
        AppDirectory::PARAMETER_DIRECTORY_URL, $appDirectory->getDirectory(), true,
        AppDirectory::PARAMETER_PAGE_URL,      $appDirectory->getPage(),      $appDirectory->getPage() > 1
    );

    $forms = [
        'name' => null,
        'type' => TYPE_FOLDER,
        'path' => null
    ];

    if (isset($_POST['create']) || isset($_POST['create_and_continue'])) {
        $forms['name'] = addslashes($_POST['name']);
        $forms['type'] = intval(addslashes($_POST['type']));

        if (empty($forms['name'])) {
            if ($forms['type'] === TYPE_FOLDER)
                $appAlert->danger(lng('create.alert.not_input_name_directory'));
            else if ($forms['type'] === TYPE_FILE)
                $appAlert->danger(lng('create.alert.not_input_name_file'));
            else
                $appAlert->danger(lng('create.alert.not_choose_type'));
        } else if ($forms['type'] != null && $forms['type'] !== TYPE_FOLDER && $forms['type'] !== TYPE_FILE) {
            $appAlert->danger(lng('create.alert.not_choose_type'));
        } else if (FileInfo::isNameError($forms['name']) == true) {
            if ($forms['type'] === TYPE_FOLDER)
                $appAlert->danger(lng('create.alert.name_not_validate_type_directory'));
            else if ($forms['type'] === TYPE_FILE)
                $appAlert->danger(lng('create.alert.name_not_validate_type_file'));
        } else {
            $forms['path'] = FileInfo::validate($appDirectory->getDirectory() . SP . $forms['name']);

            if (file_exists($forms['path'])) {
                if (is_dir($forms['path']))
                    $appAlert->danger(lng('create.alert.name_is_exists_type_directory'));
                else
                    $appAlert->danger(lng('create.alert.name_is_exists_type_file'));
            } else {
                if ($forms['type'] === TYPE_FOLDER) {
                    if (@mkdir($forms['path']) == false) {
                        $appAlert->danger(lng('create.alert.create_directory_failed', 'filename', $forms['name']));
                    } else if (isset($_POST['create_and_continue']) == false) {
                        $appAlert->success(lng('create.alert.create_directory_success', 'filename', $forms['name']), ALERT_INDEX, 'index.php' . $parameter);
                    } else {
                        $appAlert->success(lng('create.alert.create_directory_success', 'filename', $forms['name']));
                        $forms['name'] = null;
                    }
                } else if ($forms['type'] === TYPE_FILE) {
                    if (@file_put_contents($forms['path'], '...') === false) {
                        $appAlert->danger(lng('create.alert.create_file_failed', 'filename', $forms['name']));
                    } else if (isset($_POST['create_and_continue']) == false) {
                        $appAlert->success(lng('create.alert.create_file_success', 'filename', $forms['name']), ALERT_INDEX, 'index.php' . $parameter);
                    } else {
                        $appAlert->success(lng('create.alert.create_file_success', 'filename', $forms['name']));
                        $forms['name'] = null;
                    }
                } else {
                    $appAlert->danger(lng('create.alert.not_choose_type'));
                }
            }
        }
    }
?>

    <?php $appAlert->display(); ?>
    <?php $appLocationPath->display(); ?>

    <div class="form-action">
        <div class="title">
            <span><?php echo lng('create.title_page'); ?></span>
        </div>
        <form action="create.php<?php echo $parameter; ?>" method="post">
            <input type="hidden" name="<?php echo $boot->getCFSRToken()->getName(); ?>" value="<?php echo $boot->getCFSRToken()->getToken(); ?>"/>

            <ul>
                <li class="input">
                    <span><?php echo lng('create.form.input_name'); ?></span>
                    <input type="text" name="name" value="<?php echo $forms['name']; ?>" class="none" placeholder="<?php echo lng('create.form.placeholder.input_name'); ?>"/>
                </li>
                </li>
                <li class="radio-choose">
                    <ul class="radio-choose-tab">
                        <li>
                            <input type="radio" name="type" value="<?php echo TYPE_FOLDER; ?>"<?php if ($forms['type'] === TYPE_FOLDER) { ?> checked="checked"<?php } ?> id="type-folder"/>
                            <label for="type-folder">
                                <span><?php echo lng('create.form.radio_choose_type_directory'); ?></span>
                            </label>
                        </li>
                        <li>
                            <input type="radio" name="type" value="<?php echo TYPE_FILE; ?>" id="type-file"<?php if ($forms['type'] === TYPE_FILE) { ?> checked="checked"<?php } ?>/>
                            <label for="type-file">
                                <span><?php echo lng('create.form.radio_choose_type_file'); ?></span>
                            </label>
                        </li>
                    </ul>
                </li>
                <li class="button">
                    <button type="submit" name="create">
                        <span><?php echo lng('create.form.button.create'); ?></span>
                    </button>
                    <button type="submit" name="create_and_continue">
                        <span><?php echo lng('create.form.button.create_and_continue'); ?></span>
                    </button>
                    <a href="index.php<?php echo $parameter; ?>">
                        <span><?php echo lng('create.form.button.cancel'); ?></span>
                    </a>
                </li>
            </ul>
        </form>
    </div>

    <ul class="menu-action">
        <li>
            <a href="upload.php<?php echo $parameter; ?>">
                <span class="icomoon icon-folder-upload"></span>
                <span><?php echo lng('home.menu_action.upload'); ?></span>
            </a>
        </li>
        <li>
            <a href="import.php<?php echo $parameter; ?>">
                <span class="icomoon icon-folder-download"></span>
                <span><?php echo lng('home.menu_action.import'); ?></span>
            </a>
        </li>
    </ul>

<?php require_once('footer.php'); ?>