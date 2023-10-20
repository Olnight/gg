<?php
require_once ('helpers.php');
require_once ('functions.php');
require_once ('init.php');
$lots = getNewLots($con);
$categories = getCategories($con);
$nav = include_template('categories.php',['categories' => $categories]);
$main = include_template('main.php', [
    'lots'=> $lots,
    'categories'=> $categories,
]);
$layout = include_template('layout.php', [
    'lots' => $lots,
    'main' => $main,
    'title' => 'Главная',
    'is_auth'=> $is_auth,
    'user_name'=> $user_name,
    'categories'=> $categories,
    'nav'=> $nav,
]);
print($layout);