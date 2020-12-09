<?php
$action = 'index';
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

if ($action == 'index') {
    show_index();
} elseif ($action == 'view') {
    view();
} elseif ($action == 'save'){
    save();
}
elseif ($action == 'save_name'){
    save_name();
}
elseif ($action == 'upload'){
    upload();
}
elseif ($action == 'delete'){
    delete();
}
elseif ($action == 'permission'){
    permission();
}


// Functions

function show_index()
{
    $var = 'My Test Var';
    $content = render('uploads');
    render_layout($content);
}
//Редактировать файл
function view()
{
    $path=$_GET['file'];

    if (is_file($path)){
        up($path);
        $content = file_get_contents($_GET['file']);
        $name = basename($_GET['file']);
        $content = <<<HTML
 <h1>View Page</h1>
 <!-- Переименовать -->
<form method="POST" action="/?action=save_name&file=${path}">
 <textarea name="name">${name}</textarea><br>
<input type=submit name="edit" value="Сохранить">
</form>
<!-- Редактировать содержимоев-->
<form method="post" action="/?action=save&file=${path}">
<textarea rows="10" cols="20"  name="edit_text">${content}</textarea><br>
<input type="submit" name="edit_file" value="Сохранить">
</form>
<!-- Возврат -->
<form method="get" action="/">
<button>back</button>
</form>
<!-- Удаление -->
<form method="post" action="/?action=delete&file=${path}">
        <input type="submit" name="delete"
                value="delete"/>
</form>
HTML;
        render_layout($content);
    }
    elseif (is_dir($path)){
        render($path);
    }
}

function render_layout($content)
{
    echo <<<HTML
    <html>
    <body>
    ${content}
    </body>    
    </html>
HTML;
}

//Изменение содержимого файла
function save(){
    $file_name=$_GET['file'];
    $write_text=$_POST['edit_text'];
    file_put_contents($file_name,$write_text);
    header('Location: /');
}

//Изменение имени файла
function save_name(){
    $oldname = $_GET['file'];
    $newname = $_POST['name'];
    rename($oldname,'uploads/'.$newname);
    header('Location: /');
}
//Загрузка файла
function upload(){
    if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload') {
        if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
            $path=$_GET['file'];
            $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
            $fileName = $_FILES['uploadedFile']['name'];
            $dest_path = $path;
            move_uploaded_file($fileTmpPath, $dest_path.'/'.$fileName);
            header('Location: /');
        }
    }
}
//Удаление файла
function delete(){
    $path=$_GET['file'];
    deleteDir($path);
    header('Location: /');
}
//Рекурсивная функция для удаления файлов и папок
function deleteDir($path) {
    return is_file($path) ?
        @unlink($path) :
        array_map(__FUNCTION__, glob($path.'/*')) == @rmdir($path);
}
//Изменение прав доступа
function permission(){
    $perm = $_POST['permission'];
    $file = $_GET['file'];
    if (isset($_POST['edit'])) {
        exec("chmod $perm $file");
        header('Location: /');}
    ?>
    <form method="POST">
        <textarea name="permission"></textarea><br>
        <input type=submit name="edit" value="Сохранить">
    </form>
    <?php
}
//Передвижение на уровень вверх
function up($path){
    $up = dirname($path);
    if ($path !='uploads'){
        $content = <<<HTML
<a href="/?action=view&file=${up}">Вверх</a>
HTML;
    } else{
        $content = <<<HTML
<a href="/?action=view&file=${path}">Корень</a>
HTML;
    }
    render_layout($content);
}
//Отображение таблицы файлов
function render($path){
    up($path);
    $dir  = $path;
    $allFiles = scandir($dir);
    $files = array_diff($allFiles, array('.', '..')); ?>
    <table>
        <thead>
        <tr>
            <th> Name </th>
            <th> File Size </th>
            <th> Created_At </th>
            <th> File Perms </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($files as $file){ ?>
        <tr>
            <td><a href = '/?action=view&file=<?php echo $path .'/'. $file ?>'><?php echo $file. '</a>' .'</td>
        <td>' .filesize($path.'/'. $file). ' bytes' . '</td>
        <td>' .date ("m.d.Y.H:i:s.", filemtime($path.'/' . $file)) . '</td>
        <td><a href="/?action=permission&file='.$path .'/'.$file.'">' .substr(sprintf('%o', fileperms($path.'/' .$file)), -4)  . '</td>
    </a></tr>'
                    ;}?>
        </tbody>
    </table>
    <form method="POST" action="/?action=upload&file=<?php echo $path?>" enctype="multipart/form-data">
        <div>
            <span>Upload a File:</span>
            <input type="file" name="uploadedFile" />
        </div>

        <input type="submit" name="uploadBtn" value="Upload" />
    </form>
    <?php
}


