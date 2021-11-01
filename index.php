<?php
	require_once('config.php');
	require_once('const.php');
	require_once('functions.php');
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ru" xml:lang="ru" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="private">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <title>Шаг 1 - создать шаблоны</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <header class="header header2" id="myTab" role="tablist">
            <div class="navigation">
                <a id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
                    aria-selected="true">Создать рассылку</a>
            </div>
            <div class="navigation">
                <a id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
                    aria-selected="false">Картинки</a>
            </div>
            <div class="navigation">
                <a id="text-tab" data-toggle="tab" href="text.html" role="tab" aria-controls="text"
                    aria-selected="false">Текста</a>
            </div>
            <div class="navigation">
                <a id="others-tab" data-toggle="tab" href="#others" role="tab" aria-controls="others"
                    aria-selected="false">Разное</a>
            </div>
        </header>
        <main>
            <div class="main-content col-md mt-4" id="myTabContent">
                <!-- ШАБЛОНЫ -->
                <div class="tab-pane fade show home" id="home" role="tabpanel" aria-labelledby="home-tab">

                    <form name="temp_params" enctype="multipart/form-data" action="make_templ.php" method="post">

                        <div class="form-group col-md-6">
                            <select name="project" id="pr_list" class="form-control">
                                <option value='0' checked="checked">Выберите проект</option>
                                <?php foreach ($projects as $key => $pr) { ?>
                                <option value="<?php echo $key; ?>"><?php echo $pr[0]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6" style="display: none; background-color: #eeeeee;"
                            id='templates_versions'></div>
                        <div class="download-form">
                            <div class="label-container">
                                <input type="checkbox" class="checkbox" id="download_text" name="download_text" checked>
                                <span></span>
                                <label for="download_text">Загрузить текст</label>
                            </div>
                            <input type="hidden" class="form-control" name="MAX_FILE_SIZE" value="5000000">
                            <input name="texts" class="form-control" type="file">
                        </div>
                        <div class="download-form">
                            <div class="label-container">
                                <input type="checkbox" class="checkbox" id="find_text" name="find_text">
                                <span></span>
                                <label for="find_text">Искать текст</label>
                            </div>
                            <input class="calendar form-control" type="date" name="calendar">
                        </div>
                        <div>
                            <input type='submit' name='submit' value="Дальше" class="btn gen-templates" />
                            <!--<a href="#" class="btn gen-templates" onclick='sub();'>Дальше</a>-->
                        </div>
                        <div class="mt-3 navigation">
                            <a href="templates_list.php" class="btn btn-primary">Список шаблонов</a>
                        </div>

                    </form>
                </div>
                <!-- НОВОСТИ -->
                <div class="tab-pane profile" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="block">

                        <form name="banners" action="banners.php" method="post">

                            <div class="form-group-textarea">

                                <textarea name="jira_task_ids" id="jira_task_ids"
                                    placeholder="Введите ID задач в Jira через запятую ..." class="form-control"
                                    rows="2"></textarea>
                            </div>
                            <div class="add-emoji-container">
                                <input type="checkbox" id="for_email_only" checked>
                                <span></span>
                                <label for='for_email_only'>Только баннера для email</label>
                            </div>
                            <div>
                                <input type='submit' name='banners_submit' style="display:none;" />
                                <a href="#" class="btn gen-templates" onclick='banners_sub();'>Дальше</a>
                            </div>

                            <div class="mt-3 navigation">
                                <a href="banners_list.php" class="btn btn-primary">Список баннеров</a>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="tab-pane others" id="others" role="tabpanel" aria-labelledby="others-tab">
                    <form name="temp_params" enctype="multipart/form-data" action="make_templ.php" method="post">

                        <div class="form-group col-md-6">
                            <select name="project" id="pr_list" class="form-control">
                                <option value='0' checked="checked">Выберите проект</option>
                                <?php foreach ($projects as $key => $pr) { ?>
                                <option value="<?php echo $key; ?>"><?php echo $pr[0]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6" style="display: none; background-color: #eeeeee;"
                            id='templates_versions'></div>
                        <div class="download-form">
                            <label>Загрузить текст</label>
                            <input type="hidden" class="form-control" name="MAX_FILE_SIZE" value="5000000">
                            <input name="texts" class="form-control" type="file">
                        </div>
                        <div>
                            <input type='submit' name='submit' style="display:none;" />
                            <a href="#" class="btn gen-templates" onclick='sub();'>Дальше</a>
                        </div>
                        <div class="mt-3 navigation">
                            <a href="templates_list.php" class="btn btn-primary">Список шаблонов</a>
                        </div>

                    </form>
                </div>
            </div>
        </main>
        <div class="save-text-container">
            <p>Текст сохраняется 👍</p>
        </div>
    </div>

</body>
<script text="text/javascript">
    $.ajax({
        cache: false,
    });
    const home = document.querySelector(".home");
    const profile = document.querySelector(".profile");
    const others = document.querySelector(".others");
    let redactBtn = document.querySelector(".redact-btn");

    window.onscroll = function (e) {
        redactBtn.style.position = "sticky";
        redactBtn.style.bottom = "20px";
        redactBtn.style.left = "80%";
    }
    home.style.display = "block";
    profile.style.display = "none";
    others.style.display = "none";

    function tabHome() {
        home.style.display = "block";
        profile.style.display = "none";
        others.style.display = "none";
    }

    function tabProfile() {
        home.style.display = "none";
        profile.style.display = "block";
        others.style.display = "none";
    }

    function tabOthers() {
        home.style.display = "none";
        profile.style.display = "none";
        others.style.display = "block";
    };

    document.querySelector("#home-tab").addEventListener("click", tabHome);
    document.querySelector("#profile-tab").addEventListener("click", tabProfile);
    document.querySelector("#others-tab").addEventListener("click", tabOthers);

    $('#pr_list').change(function () {
        if ($('#pr_list option:selected').val() != '0') {
            var pr_id = $('#pr_list option:selected').val();

            $.ajax({
                type: 'GET',
                url: 'lib/ajax/get_templates_htmls.php',
                data: 'project_id=' + pr_id,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function (data) {

                    console.log(data);

                    if (data["empty"] == true) {
                        console.log('Нет ни одной версии верстки');

                        let html = '<p>Нет ни одной версии верстки</p>';

                        $('#templates_versions').empty();
                        $('#templates_versions').append(html);
                        $('#templates_versions').show();

                        //return false;
                    } else {
                        let html = '';
                        for (key in data) {
                            if (key != 'empty')
                                html += '<p><input type="radio" id="' + data[key]["dir_name"] +
                                '" name="templates_version" value="' + data[key]["dir_name"] +
                                '" /><label for="' + data[key]["dir_name"] + '">&nbsp;&nbsp;' +
                                data[key]["name"] + '</label></p>';
                        }
                        console.log(html);
                        $('#templates_versions').empty();
                        $('#templates_versions').append(html);
                        $('#templates_versions').show();
                    }

                },
                error: function (error) {

                    console.log(status);
                    console.log(error);



                }
            });

            // $('.lang_divs').hide();
            // $('#pr_lang' + pr_id).show();

            // $('.lang_divs input[type=checkbox]').prop('checked', '');

            // $('#pr_lang' + pr_id).find('input[name=all]').prop('checked', 'checked');
        } else {
            $('#templates_versions').empty();
        }
    });


    function sub() {
        if ($('#pr_list option:selected').val() == '0')
            alert('Проект не выбран!');
        else if ($('input[name=templates_version]:checked').length == 0) {
            alert('Выберите вариант верстки!');
        } else {
            //alert('Дальше');

            // console.log($('input[name=submit1]').val());
            // $('form[name=temp_params] input[name=submit]').click();
            document.forms["temp_params"].submit();
        }
    }

    $('form[name=temp_params]').submit(function (event) {
        // console.log();
        // console.log('1212');
        // alert('123123!');
        // event.preventDefault();


        if ($('#pr_list option:selected').val() == '0') {
            console.log('Проект не выбран!');
            alert('Проект не выбран!');
            event.preventDefault();
        } else if ($('input[name=templates_version]:checked').length == 0) {
            console.log('Выберите вариант верстки!');
            alert('Выберите вариант верстки!');
            event.preventDefault();
        } else if ($("#find_text").prop('checked') === true && $("input[name=calendar]").val() === '') {
            console.log('Выберите дату создания текста!');
            alert('Выберите дату создания текста!');
            event.preventDefault();
        }
        // else
        // console.log($("input['calendar']").val());


    });


    // function news_sub() {
    //     if ($('#news_pr_list option:selected').val() == '0')
    //         alert('Проект для новостей не выбран!');
    //     else {
    //         //alert('Дальше');
    //         $('#news_forms input[name=submit]').click();
    //     }
    // }


    function banners_sub() {
        if ($('#jira_task_ids').val() == '')
            alert('Задачи не введены!');
        else {
            //alert('Дальше');
            $('input[name=banners_submit]').click();
        }
    }
</script>

</html>


