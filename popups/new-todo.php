<form action="api.php" id="new-todo-form">
    <h4>Todo Ekle</h4>
    <input type="text" name="todo" placeholder="Todo"> <br>
    <select name="type">
        <option value="">Tipi Seçin</option>
        <option value="1">Ders</option>
        <option value="2">Her gün yapılacaklar</option>
        <option value="3">Sorumluluklarım</option>
    </select> <br>
    <label>
        <input type="checkbox" name="done" value="1">
        Bunu yaptım olarak işaretle
    </label> <br>
    <button>Ekle</button>
    <button class="close-popup">Kapat</button>
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

            $('.popup').html('').removeClass('open')
            $('#todo-table tbody').prepend(response.html);
            $('#todo-table tbody tr:first').addClass('inserted')

            // $('.change-pagination').trigger('change')

            setTimeout(() => {
                $('#todo-table tbody tr:first').removeClass('inserted')
            }, 2000);

        }
    }, 'json')
});
</script>