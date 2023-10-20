<?php
require_once ('helpers.php');
require_once ('functions.php');
require_once ('init.php');
$categories = getCategories($con);
$nav = include_template('categories.php', ['categories' => $categories]);
const MIN_NAME = 3;
const MAX_NAME = 100;
const MIN_MESSAGE = 3;
const MAX_MESSAGE = 500;

function getPostVal($name) {
    return $_POST[$name] ?? "";
}
$errors = [];
$required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }

    if(!isset($errors['lot-date'])) {
        if((!is_date_valid($_POST['lot-date']) || time() >= strtotime($_POST['lot-date']))){
            $errors['lot-date'] = 'Дата должна быть корректной';
        }
    }

    if(!isset($errors['lot-rate'])){
        if(!filter_var($_POST['lot-rate'], FILTER_VALIDATE_INT)){
            $errors['lot-rate'] = 'Цена должна быть корректной';
        }elseif($_POST['lot-rate'] <= 0){
            $errors['lot-rate'] = 'Цена должна быть корректной';
        }
    }

    if(!isset($errors['lot-step'])){
        if(!filter_var($_POST['lot-step'], FILTER_VALIDATE_INT)){
            $errors['lot-step'] = 'Шаг ставки должен быть корректным';
        }elseif($_POST['lot-step'] <= 0){
            $errors['lot-step'] = 'Шаг ставки должен быть корректным';
        }
    }


    if(!isset($errors['lot-name'])){
        $len = strlen($_POST['lot-name']);
       
        if ($len < MIN_NAME or $len > MAX_NAME) {
            $errors['lot-name'] = "Значение должно быть от 3 до 100 символов";
        }
    }
    if(!isset($errors['message'])){
        $len = strlen($_POST['message']);
        if ($len < MIN_MESSAGE or $len > MAX_MESSAGE) {
            $errors['message'] = "Значение должно быть от 3 до 500 символов";
        }
    }
    if(!isset($errors['category'])) {
        if((!is_date_valid($_POST['category']) || time() >= strtotime($_POST['category']))){
            $errors['category'] = 'Укажите категорию';
        }
    }

    if(isset($_FILES['picture'])){
        $file_name = $_FILES['picture']['tmp_name']; 
        $time_img =time();
        $file_type = ['image/png', 'image/jpeg'];
        if (!in_array(mime_content_type($file_name), $file_type)){
            $errors['picture'] = 'Изображение должно иметь формат .jpg/.jpeg/.png';
        }else{
            $file_name = $_FILES['picture']['name']; 
            $file_path = __DIR__ . '/uploads/';
            $file_url = '/uploads/' .$time_img. $file_name;
            move_uploaded_file($_FILES['picture']['tmp_name'], $file_path .$time_img. $file_name);
        }
    }else{
        $errors['picture'] = 'Загрузите картинку!';
    }

    if(!$errors){
        $lot_name = $_POST['lot-name'];
        $category= $_POST['category'];
        $message = $_POST['message'];
        $picture = $file_url; 
        $lotRate = $_POST['lot-rate'];
        $lotStep = $_POST['lot-step'];
        $endDate = $_POST['lot-date'];
        $lotAdd = add_lot($lot_name,  $category,  $message,  $picture,  $lotRate,  $lotStep,  $endDate, $con);
        $lots = lot_detail($con, $lotAdd);
        if (http_response_code() === 404) {
            $detail_lot = include_template('404.php',['nav' => $nav]);
        }else{
            $detail_lot = header('Location: /lot.php?id=' . $lotAdd);
        }

        $detail_lot = print(include_template('layout.php', [
            'is_auth' => $is_auth,
            'user_name' => $user_name,
            'title' => $title,
            'nav' => $nav,
            'main' => $detail_lot]));

            }

        
    }


$add_content = include_template('add.php',['nav' => $nav, 'errors' => $errors, 'categories' => $categories]);
$detail_lot = print(include_template('layout.php', [
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => "Добавление",
    'main' => $add_content,
    'nav' => $nav
]));