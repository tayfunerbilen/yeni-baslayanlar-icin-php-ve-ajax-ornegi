<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

try {
    $db = new PDO('mysql:host=localhost;dbname=todo', 'root', 'root');
} catch (PDOException $e) {
    die($e->getMessage());
}