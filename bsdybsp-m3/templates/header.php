<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Добавление</title>
  <link href="../css/normalize.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
</head>
<body>

<div class="page-wrapper">
  <header class="main-header">
    <div class="main-header__container container">
      <h1 class="visually-hidden">YetiCave</h1>
      <a class="main-header__logo" href="/index.php">
        <img src="../img/logo.svg" width="160" height="39" alt="Логотип компании YetiCave">
      </a>
      <form class="main-header__search" method="get" action="https://echo.htmlacademy.ru" autocomplete="off">
        <input type="search" name="search" placeholder="Поиск лота">
        <input class="main-header__search-btn" type="submit" name="find" value="Найти">
      </form>
      <a class="main-header__add-lot button" href="../add.php">Добавить лот</a>

      <nav class="user-menu">
            <?php if (isset($_SESSION['is_auth']) && $_SESSION['is_auth']): ?>
                <div class="user-menu__logged">
                    <p> <?= $_SESSION['username'] ?> </p>
                    <a class="user-menu__bets" href="../my-bets.php">Мои ставки</a>
                    <a class="user-menu__logout" href="../logout.php">Выход</a>
                </div>
            <?php else: ?>
                <ul class="user-menu__list">
                    <li class="user-menu__item">
                        <a href="../sign-up.php">Регистрация</a>
                    </li>
                    <li class="user-menu__item">
                        <a href="../login.php">Вход</a>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>

    </div>
  </header>

  <main>
 