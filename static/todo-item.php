<tr id="todo_<?=$todo['id']?>">
    <td><?=$todo['id']?></td>
    <td><?=$todo['todo']?></td>
    <td><?=todoTypes($todo['type'])?></td>
    <td><?=$todo['done'] == 1 ? 'Evet' : 'Hayır' ?></td>
    <td>
        <button data-id="<?=$todo['id']?>" class="todo-edit btn btn-primary btn-sm">Düzenle</button>
        <button data-id="<?=$todo['id']?>" class="todo-delete btn btn-danger btn-sm">Sil</button>
    </td>
</tr>