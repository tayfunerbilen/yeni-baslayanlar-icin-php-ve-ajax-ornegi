<?php

require __DIR__ . '/connection.php';
require __DIR__ . '/helpers.php';

$action = post('action');

$response = [];

switch ($action) {

    case 'get-todo':

        $page = (int) post('page') ?? 1;
        $start = ($page - 1) * 4; // 4

        $todos = $db->query('SELECT * FROM todos ORDER BY id DESC LIMIT ' . $start . ' , 4')->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        foreach($todos as $todo) {
            require __DIR__ . '/static/todo-item.php';
        }
        $response['html'] = ob_get_clean();

        break;

    case 'new-todo':
        
        $todo = post('todo');
        $type = post('type');
        $done = post('done');

        if (!$todo) {
            $response['error'] = 'Todo boş olamaz!';
        } elseif (!$type) {
            $response['error'] = 'Tip boş olamaz!';
        } else {

            $query = $db->prepare('INSERT INTO todos SET todo = :todo, type = :type, done = :done');
            $insert = $query->execute([
                'todo' => $todo,
                'type' => $type,
                'done' => $done ?? 0
            ]);

            if ($insert) {
                
                $response['success'] = 'Todo başarıyla eklendi';

                $todoId = $db->lastInsertId();
                $todo = $db->query('SELECT * FROM todos WHERE id = "' . $todoId . '"')->fetch(PDO::FETCH_ASSOC);

                ob_start();
                require __DIR__ . '/static/todo-item.php';
                $response['html'] = ob_get_clean();

            } else {
                $response['error'] = $db->errorMessage();
            }

        }

        break;

    case 'update-todo':
    
        $todoText = post('todo');
        $type = post('type');
        $done = post('done');
        $id = post('id');

        if (!$todoText) {
            $response['error'] = 'Todo boş olamaz!';
        } elseif (!$type) {
            $response['error'] = 'Tip boş olamaz!';
        } else {

            $todo = $db->query('SELECT * FROM todos WHERE id = "' . $id . '"')->fetch(PDO::FETCH_ASSOC);

            if ($todo) {

                $query = $db->prepare('UPDATE todos SET todo = :todo, type = :type, done = :done WHERE id = :id');
                $update = $query->execute([
                    'todo' => $todoText,
                    'type' => $type,
                    'done' => $done ?? 0,
                    'id' => $id
                ]);

                if ($update) {
                    $response['success'] = 'Todo başarıyla güncellendi';

                    $todo = $db->query('SELECT * FROM todos WHERE id = "' . $id . '"')->fetch(PDO::FETCH_ASSOC);

                    ob_start();
                    require __DIR__ . '/static/todo-item.php';
                    $response['html'] = ob_get_clean();
                } else {
                    $response['error'] = $db->errorMessage();
                }

            } else {
                $response['error'] = 'Böyle bir todo yok!';
            }

        }

        break;

    case 'delete-todo':

        $id = post('id');
        if (!$id) {
            $response['error'] = 'Geçersiz bir id gönderdiniz';
        } else {

            $delete = $db->exec('DELETE FROM todos WHERE id = "' . $id . '"');
            if ($delete) {
                $response['success'] = 'Todo başarıyla silindi.';
            } else {
                $response['error'] = 'Todo silinirken bir hata oluştu';
            }

        }

        break;

    case 'new-todo-popup':
    
    ob_start();
    require __DIR__ . '/popups/new-todo.php';
    $response['html'] = ob_get_clean();

    break;

    case 'edit-todo-popup':

        $id = post('id');

        // todo var mı yok mu kontrol etmem gerek
        $query = $db->prepare('SELECT * FROM todos WHERE id = :id');
        $query->execute([
            'id' => $id
        ]);

        $todo = $query->fetch(PDO::FETCH_ASSOC);

        if (!$todo){
            $response['error'] = 'Böyle bir todo yok!';
        } else {
            ob_start();
            require __DIR__ . '/popups/edit-todo.php';
            $response['html'] = ob_get_clean();
        }

        break;

}

echo json_encode($response);