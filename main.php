<?php
include 'config.php';
include 'book.php';
include 'users.php';

// Подключение к бд
$link = mysqli_connect($hostname, $user, $pass, $bd);
if (!$link) {
    die('Ошибка соединения: ' . mysql_error());
}

$content = '';

// Изменения в сессии при выходе из гостевой книги
if ((($_GET[action] == 'authorization') || ($_GET[action] == 'registration')) && ($_GET[act] == 'exit_book')) {
	if ($_GET[action] == 'authorization') {
		$_SESSION[action] = 'authorization';
	} else {
		$_SESSION[action] = 'registration';
	}
	unset($_SESSION[status]);
	unset($_SESSION[login]);
	unset($_SESSION[password]);
} elseif ($_GET[action] == 'enter') {   // Изменения в сессии при входе неавторизованного пользователя
	$_SESSION[status] = 'noAuto';
	$_SESSION[login] = '';
	$_SESSION[password] = '';
}

// Регистрация и авторизация
switch ($_SESSION[action]) {
	case "no":
		break;
	case "authorization":
		if ((isset($_POST[login])) && (isset($_POST[password]))) {
			$user = new user($_POST[login], $_POST[password]);
			if ($user->isUserLoggedIn() == 'yes') {
				$_SESSION[status] = $user->get_status();
				$_SESSION[login] = $_POST[login];
				$_SESSION[password] = $_POST[password];
			}
		}
		break;
	case "registration":
		if ((isset($_POST[login])) && (isset($_POST[password])) && (isset($_POST[password_too])) &&
		  ($_POST[password] == $_POST[password_too])) {
			$user = new user($_POST[login], $_POST[password]);
			if ($user->isUserLoggedIn() == 'no') {
				$user->insertUser();
				$_SESSION[status] = 'guest';
				$_SESSION[login] = $_POST[login];
				$_SESSION[password] = $_POST[password];
			}
		}
		break;
}

// Если не установлен статус пользователя, то не отображается книга 
if (isset($_SESSION[status])) {
	$_SESSION[action] = 'no';
} else {
	$user = new user();
	$content .= $user->enter();   // формы регистрации и авторизации
	return $content;
}

$user = new user($_SESSION[login], $_SESSION[password]);

// Добавление/изменение новости
if ((isset($_POST[text])) && ($_POST[text] != '') && (isset($_POST[name])) && ($_POST[name] != '')) {
	$user->insertNews($_POST[text], $_POST[name], $_POST[anonim]);
	unset($_GET[action]);
} elseif (isset($_POST[text_update])) {
	$user->change($_SESSION[id_update], $_POST[text_update]);
	unset($_GET[action]);
	unset($_GET[id]);
}

// Поиск страницы книги, которая будет отображаться
if (!isset($_SESSION[page])) {
		$_SESSION[page] = 1;
	}
if (isset($_GET[page])) {
	$_SESSION[page] = $_GET[page];
}
$page = $_SESSION[page];

$book = new book($page, $param_X);
$content .= $book->show_action_top();         // Отображение действий: регистрация, авторизация или выход
$content .= $book->show_pages();			  // Пагинация

// Удаление новости
if ($_GET[action] == 'delete') {
	$user->delete($_GET[id]);
} elseif ($_GET[action] == 'change') {        // показ формы изменения новости
	$_SESSION[id_update] = $_GET[id];
	$content .= $user->change_forma($_GET[id]);
}

// Показ страницы книги
$content .= $book->show_list();

// Отображение формы добавления новости
if ($_GET[action] == 'show_add_form') {
	$content .= $user->insertNewsForm();
} else {
	$content .= $book->show_action_bottom();
}


return $content;
?>