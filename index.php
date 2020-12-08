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



// Functions

function show_index()
{
    $var = 'My Test Var';

    // heredoc
    $content = render();

    render_layout($content);
}


function view()
{

    $_GET['path'];
    $content = file_get_contents($_GET['file']);
    $file=$_GET['file'];
    $name = basename($_GET['file']);

    $content = <<<GVS
 <h1>View Page</h1>

<form method="POST" action="/?action=save_name&file=${file}">
 <textarea name="name">${name}</textarea><br>
<input type=submit name="edit" value="Сохранить">
</form>
<br>
<br>


<form method="post" action="/?action=save&file=${file}">
<textarea rows="10" cols="20"  name="edit_text">${content}</textarea><br>
<input type="submit" name="edit_file">
</form>
<form method="get" action="/">
<button>back</button>
</form>


<form method="post" action="/?action=delete&file=${file}">
        <input type="submit" name="delete"
                value="delete"/>
    </form>



GVS;
    render_layout($content);
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


function save(){
    if(isset($_POST['edit_file']))
    {
        $file_name=$_GET['file'];
        $write_text=$_POST['edit_text'];
        $handle = fopen($file_name, 'r+');
        fwrite($handle, $write_text);
        fclose($handle);
    }

}
function save_name(){
    $oldname = $_GET['file'];
    $newname = $_POST['name'];

    rename($oldname,'uploads/'.$newname);
    header('Location: /');

}


function upload(){
    if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload')
    {
        if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK)
        {

            $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
            $fileName = $_FILES['uploadedFile']['name'];
            $fileSize = $_FILES['uploadedFile']['size'];
            $fileType = $_FILES['uploadedFile']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));



            $dest_path = 'uploads/';

            move_uploaded_file($fileTmpPath, $dest_path.$fileName);
            header('Location: /');
        }
    }
}

function delete(){
    $path=$_GET['file'];

    rmRec($path);

    function rmRec($path) {
        if (is_file($path)) return unlink($path);
        if (is_dir($path)) {
            foreach(scandir($path) as $p) if (($p!='.') && ($p!='..'))
                rmRec($path.DIRECTORY_SEPARATOR.$p);
            return rmdir($path);
        }
        return false;
    }
    header('Location: /');
}
function render(){
    $dir  = 'uploads';
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
            <td> <a href = '/?action=view&file=uploads/<?php echo $file?>'> <?php echo $file . '</a></td>
        <td>' .filesize('uploads/' . $file). ' bytes' . '</td>
        <td>' .date ("m.d.Y.H:i:s.", filemtime('uploads/' . $file)) . '</td>
        <td>' .substr(sprintf('%o', fileperms('uploads/' .$file)), -4)  . '</td>
    </tr>'
                    ;}?>
        </tbody>
    </table>

    <form method="POST" action="/?action=upload" enctype="multipart/form-data">
        <div>
            <span>Upload a File:</span>
            <input type="file" name="uploadedFile" />
        </div>

        <input type="submit" name="uploadBtn" value="Upload" />
    </form>

    <?php
}


