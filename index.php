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

    $content = <<<GVS
 <h1>View Page</h1>

<textarea>${content}</textarea>
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
<?php
}


