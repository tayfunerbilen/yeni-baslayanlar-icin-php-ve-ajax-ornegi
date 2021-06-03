<?php

function post($name) {
    if (isset($_POST[$name]) && !empty($_POST[$name])) {
        return htmlspecialchars($_POST[$name]);
    }
}

function todoTypes($typeId = null) {
    $types = [
        1 => 'Ders',
        2 => 'Her gün yapılacaklar',
        3 => 'Sorumluluklarım'
    ];
    return $types[$typeId] ?? $types;
}