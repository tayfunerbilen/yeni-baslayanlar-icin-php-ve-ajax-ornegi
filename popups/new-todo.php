<form action="api.php" id="new-todo-form">
    <div class="modal-header">
        <h5 class="modal-title">Todo Düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Todo</label>
            <input class="form-control" type="text" name="todo">
        </div>
        <div class="mb-3">
            <label class="form-label">Todo Tipi</label>
            <select name="type" class="form-control">
                <option value="">Seçin</option>
                <?php foreach(todoTypes() as $id => $type): ?>
                <option value="<?=$id?>"><?=$type?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3 form-check">
            <input id="done" type="checkbox" class="form-check-input" name="done" value="1">
            <label class="form-check-label" for="done">Bunu yaptım olarak işaretle</label>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
        <button type="submit" class="btn btn-primary">Ekle</button>
    </div>
</form>

<script>
$('#new-todo-form').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    formData += '&action=new-todo'
    $.post($(this).attr('action'), formData, function(response) {
        if (response.error) {
            alert(response.error)
        } else {

            // $('.popup').html('').removeClass('open')
            modal.hide()
            modalContent.html('')
            $('#todo-table tbody').prepend(response.html);
            $('#todo-table tbody tr:first').addClass('table-success')

            totalTodos += 1 // todo sayısını güncelle
            setTodosWithPagination()

            // eğer yeni eklenen todo ile birlikte değer mevcut sayfalama limitnden büyükse
            // en alttaki tr 'yi silelim
            if (totalTodos > 4) {
                $('#todo-table tbody tr:last').remove()
            }

            // $('.change-pagination').trigger('change')

            setTimeout(() => {
                $('#todo-table tbody tr:first').removeClass('table-success')
            }, 2000);

        }
    }, 'json')
});
</script>