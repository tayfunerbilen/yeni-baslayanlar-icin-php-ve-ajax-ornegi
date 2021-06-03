<?php
require __DIR__ . '/connection.php';
require __DIR__ . '/helpers.php';

$total = $db->query('SELECT count(id) as total FROM todos')->fetch(PDO::FETCH_ASSOC);
$todos = $db->query('SELECT * FROM todos ORDER BY id DESC LIMIT 0, 4')->fetchAll(PDO::FETCH_ASSOC);
$pagination = ceil($total['total'] / 4);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    tr.updated td {
        background-color: green;
        color: #fff;
    }
    tr.inserted td {
        background-color: orange;
        color: #fff;
    }
    tr.deleted td {
        background-color: red;
        opacity: .3;
        color: #fff;
    }
    .popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.7);
        display: none;
        align-items: center;
        justify-content: center;
    }
    .popup.open {
        display: flex;
    }
    </style>
</head>
<body>

<div class="popup"></div>

<hr>

<button class="new-todo">Yeni Ekle</button>

<hr>

<select name="pagination" class="change-pagination">
    <?php for ($i = 1; $i <= $pagination; $i++): ?>
        <option value="<?=$i?>"><?=$i?>. Sayfa</option>
    <?php endfor; ?>
</select>

<hr>

<table id="todo-table" border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Todo</th>
            <th>Tip</th>
            <th>Yapıldı mı?</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($todos as $todo): ?>
        <?php require __DIR__ . '/static/todo-item.php' ?>
    <?php endforeach; ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('.new-todo').on('click', function(e) {
    const data = {
        action: 'new-todo-popup'
    }
    $.post('api.php', data, function(response) {
        $('.popup').addClass('open').html(response.html)
    }, 'json')
});

$(document.body).on('click', '.todo-edit', function(){
    const data = {
        id: $(this).data('id'),
        action: 'edit-todo-popup'
    }
    $.post('api.php', data, function(response) {
        $('.popup').addClass('open').html(response.html)
    }, 'json')
});

$(document.body).on('click', '.todo-delete', function() {
    const id = $(this).data('id')
    const data = {
        id: id,
        action: 'delete-todo'
    }
    $.post('api.php', data, function(response) {
        if (response.error) {
            alert(response.error)
        } else {
            const todo = $('#todo_' + id);
            todo.addClass('deleted')
            setTimeout(() => {
                todo.remove()
            }, 500);
        }
    }, 'json')
})

$('.change-pagination').on('change', function() {
    const data = {
        page: $(this).val(),
        action: 'get-todo'
    }
    $.post('api.php', data, function(response) {
        $('#todo-table tbody').html(response.html);
    }, 'json')
});

$(document.body).on('click', '.close-popup', function(e) {
    e.preventDefault();
    $('.popup').html('').removeClass('open')
});
</script>

</body>
</html>