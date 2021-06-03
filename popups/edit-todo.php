<form action="api.php" id="update-todo-form">
    <h4>Todo Düzenle</h4>
    <input type="text" name="todo" value="<?=$todo['todo']?>" placeholder="Todo"> <br>
    <select name="type">
        <option value="">Tipi Seçin</option>
        <?php foreach(todoTypes() as $id => $type): ?>
        <option <?=$id == $todo['type'] ? 'selected' : null ?> value="<?=$id?>"><?=$type?></option>
        <?php endforeach; ?>
    </select> <br>
    <label>
        <input type="checkbox" <?=$todo['done'] == 1 ? 'checked' : null ?> name="done" value="1">
        Bunu yaptım olarak işaretle
    </label> <br>
    <input type="hidden" name="id" value="<?=$todo['id']?>">
    <button>Güncelle</button>
    <button class="close-popup">Kapat</button>
</form>

<script>
$('#update-todo-form').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    formData += '&action=update-todo'
    $.post($(this).attr('action'), formData, function(response) {
        if (response.error) {
            alert(response.error)
        } else {

            $('.popup').html('').removeClass('open')
            const tr = $('#todo_<?=$todo['id']?>');
            tr.after(response.html);
            tr.remove();

            $('#todo_<?=$todo['id']?>').addClass('updated')
            setTimeout(() => {
                $('#todo_<?=$todo['id']?>').removeClass('updated')
            }, 2000);

        }
    }, 'json')
});
</script>