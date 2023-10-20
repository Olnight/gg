<?php
date_default_timezone_set('Asia/Yekaterinburg');

function format(int $price): string {
    return number_format($price, 0, '.', ' ').' ₽';
}
function lastTime(string $dateEnd) : array{
    //преобразование
    $currentDate = time();
    $endDate = strtotime($dateEnd.'+ 1 day'.'+ 1 minute');
    $COUNT_SECONDS = 3600;
    $COUNT_MINUTES = 60;
    //интервал между датами
    $hours = str_pad(floor(($endDate - $currentDate)/ $COUNT_SECONDS), 2, "0", STR_PAD_LEFT);
    $minutes = str_pad(floor((($endDate - $currentDate)% $COUNT_SECONDS)/$COUNT_MINUTES), 2, "0", STR_PAD_LEFT);
    return [$hours, $minutes];
}
function addStyle(string $dateEnd){
    $hour = 1;
    $COUNT_MINUTES = 60;
    $hours = floor((strtotime($dateEnd)-time())/$COUNT_MINUTES);
    $isAddStyle = $hours < $hour;
    return $isAddStyle ? "timer--finishing":"";
}

function getNewLots(mysqli $con): array{
    $sql = "SELECT
    l.id,
    l.name,
    l.start_price,
    l.image,
    c.name AS categoryName,
    l.end_date
FROM Lot AS l
    INNER JOIN Category AS c ON l.category_id = c.id
WHERE l.end_date >= CURRENT_DATE
ORDER BY l.creation_date DESC";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

function getCategories(mysqli $con): array{
    $sql = "SELECT * FROM Category";
    return mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC);
}

function lot_detail(mysqli $con, int $id_lot):array|int{
    $sql = "SELECT
    l.name, 
    l.image, 
    l.start_price, 
    l.end_date,
    l.description,
    c.name AS categoryName
FROM Lot AS l
    INNER JOIN Category AS c ON l.category_id = c.id 
    WHERE l.id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_lot);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($res,MYSQLI_ASSOC);
    if(mysqli_num_rows($res) !== 0){
        return $rows[0];
    }else{
        return http_response_code(404);
    }
}

function empty_field($fields){
    foreach ($fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }
}

function get_lot_by_id(mysqli $con, int $lot_id): array|null
{
    $sql = "SELECT 
    Lot.id, 
    `name`,
    `description`,
    `creation_date`,
    `start_price`,
    `image`,
    Category.name,
    `end_date`,
    `bidding_step`
FROM `Lot`
         INNER JOIN Category ON Lot.category_id = Category.id
WHERE Lot.id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $select_res = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_assoc($select_res);
    if (mysqli_num_rows($select_res) === 0) {
        http_response_code(404);
    }
    return $rows;
}

function add_lot(
    string $lot_name, int $category, string $message, string $picture, int $lotRate, int $lotStep, string $endDate, mysqli $con) : int {
    $authorId  = 2;
    $sql = "INSERT INTO Lot( name, description, image, start_price, end_date, bidding_step, author_id, category_id)
            VALUES ( ?,?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssisiii',  $lot_name, $message, $picture, $lotRate, $endDate, $lotStep, $authorId, $category);
    mysqli_stmt_execute($stmt);
    return $con->insert_id;
}


function pr ($val){
    $bt   = debug_backtrace();
    $file = file($bt[0]['file']);
    $src  = $file[$bt[0]['line']-1];
    $pat = '#(.*)'.__FUNCTION__.' *?\( *?(.*) *?\)(.*)#i';
    $var  = preg_replace ($pat, '$2', $src);
    echo '<script>console.log("'.trim($var).'='. 
     addslashes(json_encode($val,JSON_UNESCAPED_UNICODE)) .'")</script>'."\n";
}