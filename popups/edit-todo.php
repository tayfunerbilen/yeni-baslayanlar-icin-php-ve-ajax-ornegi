<form action="api.php" id="update-todo-form">
    <div class="modal-header">
        <h5 class="modal-title">Todo Düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Todo</label>
            <input class="form-control" type="text" name="todo" value="<?=$todo['todo']?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Todo Tipi</label>
            <select name="type" class="form-control">
                <option value="">Seçin</option>
                <?php foreach(todoTypes() as $id => $type): ?>
                <option <?=$id == $todo['type'] ? 'selected' : null ?> value="<?=$id?>"><?=$type?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3 form-check">
            <input id="done" type="checkbox" class="form-check-input" <?=$todo['done'] == 1 ? 'checked' : null ?> name="done" value="1">
            <label class="form-check-label" for="done">Bunu yaptım olarak işaretle</label>
        </div>
        <input type="hidden" name="id" value="<?=$todo['id']?>">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
        <button type="submit" class="btn btn-primary">Güncelle</button>
    </div>
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

            // $('.popup').html('').removeClass('open')
            modal.hide()
            modalContent.html('')
            const tr = $('#todo_<?=$todo['id']?>');
            tr.after(response.html);
            tr.remove();

            $('#todo_<?=$todo['id']?>').addClass('table-info')
            setTimeout(() => {
                $('#todo_<?=$todo['id']?>').removeClass('table-info')
            }, 2000);

        }
    }, 'json')
});
</script>