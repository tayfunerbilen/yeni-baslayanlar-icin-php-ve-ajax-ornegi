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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
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

<div class="modal" id="modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" id="modal-content"></div>
  </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12 pt-3">

            <button class="new-todo btn btn-primary">Yeni Ekle</button>

            <hr>

            <select name="pagination" class="change-pagination form-control">
                <?php for ($i = 1; $i <= $pagination; $i++): ?>
                    <option value="<?=$i?>"><?=$i?>. Sayfa</option>
                <?php endfor; ?>
            </select>

            <hr>

            <table id="todo-table" class="table table-bordered table-hover" border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Todo</th>
                        <th>Tip</th>
                        <th>Yapıldı mı?</th>
                        <th width="130">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($todos as $todo): ?>
                    <?php require __DIR__ . '/static/todo-item.php' ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const modal = new bootstrap.Modal(document.getElementById('modal'))
const modalContent = $('#modal-content')

let totalTodos = <?=$total['total']?>;
let currentPage = 1;
let totalPage = <?=$pagination?>;

function setTodosWithPagination() {
    let pagination = Math.ceil(totalTodos / 4)

    // eğer ekleme ya da silme sonrasında sayfa değeri değiştiyse o zaman işlem yapalım
    if (pagination != totalPage) {
        totalPage = pagination
        let options
        for ( let i = 1; i <= pagination; i++ ) {
            options += `<option ${i == currentPage ? 'selected' : null } value="${i}">${i}. Sayfa</option>`
        }
        $('.change-pagination').html(options).trigger('change')
    }
}

$('.new-todo').on('click', function(e) {
    const data = {
        action: 'new-todo-popup'
    }
    $.post('api.php', data, function(response) {
        // $('.popup').addClass('open').html(response.html)
        modal.show()
        modalContent.html(response.html)

        // eğer yeni todo eklerken 1. sayfada değilse 1. sayfaya gönderelim
        if (currentPage !== 1) {
            $('.change-pagination option[value="1"]').attr('selected', 'selected').trigger('change')
        }
    }, 'json')
});

$(document.body).on('click', '.todo-edit', function(){
    const data = {
        id: $(this).data('id'),
        action: 'edit-todo-popup'
    }
    $.post('api.php', data, function(response) {
        // $('.popup').addClass('open').html(response.html)
        modal.show()
        modalContent.html(response.html)
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
            todo.addClass('table-danger')
            totalTodos -= 1 // todo sayısını güncelle
            setTodosWithPagination()
            setTimeout(() => {
                todo.remove()
                $('.change-pagination').trigger('change')
            }, 500);
        }
    }, 'json')
})

$('.change-pagination').on('change', function() {
    const data = {
        page: $(this).val(),
        action: 'get-todo'
    }
    currentPage = $(this).val() // varsayılan sayfa değerini güncelle
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