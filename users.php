<?php
class user
{
	public $login = '';
	public $password = '';

	private function get_data() {
		global $link;
		$sql = "SELECT id, login, password, status FROM `users`";
		$result = mysqli_query($link, $sql);
		$data = array();
		while ($row = mysqli_fetch_array($result)) {

			$arr = array();
			$arr += array('id'=>$row[id],
						  'login'=>$row[login],
						  'password'=>$row[password],
						  'status'=>$row[status]);
			array_push($data, $arr);
		}
		return $data;
	}

	public function get_status() {
		$result = 'noAuto';
		$data = $this->get_data();
		foreach ($data as $row) {
			if ($row[login] == $this->login) {
				$result = $row[status];
			}
		}
		return $result;
	}

// Проверка регистрации: 'yes' - зарегестрирован, 'no' - не зарег., 'login' - пользователь с таким логином уже зарег.
	public function isUserLoggedIn() {
		$data = $this->get_data();
		$result = '';
		foreach ($data as $row) {
			if (($row[login] == $this->login) && ($row[password] == $this->password)) {
				return 'yes';
			} elseif ($row[login] == $this->login) {
				$result = 'login';
			}
		}
		if ($result == 'login') {
			return $result;
		} else {
			return 'no';
		}
	}

	// Отображение формы регистрации или авторизации
	public function enter() {
		if ($_GET[action] == 'registration') {
			$result = $this->registration_form();
		} else {
			$result = $this->authorization_form();
		}
		return $result;
	}

	private function registration_form() {
		$_SESSION[action] = 'registration';
		return "<div class='alert alert-success' role='alert'>
				<form action='' method = 'post'>
			    	<p>Регистрация:</p>
			    	<label>Логин</label><input type='text' name='login'><br>
					<label>Пароль</label><input type='password' name='password'><br>
					<label>Повторите пароль</label><input type='password' name='password_too'><br>
					<input class='btn btn-outline-success' type='submit' value='Зарегистрироваться'>
				</form>
				<strong><a href=?action=authorization style='color: green'>Авторизация</a></strong>
				<p>Войти как <strong><a href=?action=enter style='color: green'>гость</a></strong></p>
				</div>";
	}

	private function authorization_form() {
		$_SESSION[action] = 'authorization';
		return "<div class='alert alert-success' role='alert'>
				<form action='' method = 'post'>
			    	<p>Авторизация:</p>
			    	<label>Логин</label><input type='text' name='login'><br>
					<label>Пароль</label><input type='password' name='password'><br>
					<input class='btn btn-outline-success' type='submit' value='Войти'>
				</form>
				<strong><a href=?action=registration style='color: green'>Регистрация</a></strong>
				<p>Войти как <strong><a href=?action=enter style='color: green'>гость</a></strong></p>
				</div>";
	}

	public function insertUser(){
		global $link;
    	$sql = "INSERT INTO `users` (`login`, `password`, `status`)
            VALUES ('{$this->login}', '{$this->password}', 'guest')";
    	mysqli_query($link, $sql);
	    echo mysql_error();
	}

	public function insertNewsForm(){
	    $str = "<div class='alert alert-success' role='alert'>
	           	  <form method='post'>";
	    if ($_SESSION[status] == 'noAuto') {
	        $str .= "<label>Выше имя:</label><input type='text' name='name'><br>";
	    }
	    $str .= "<label>Текст:</label><input type='text' name='text'><br>
	          	 <label>Написать анонимно?</label><input type='checkbox' name='anonim'><br>    
	             <input class='btn btn-outline-success' type='submit' value='Добавить'>
	           	 </form>
	    		 </div>";
	    return $str;
	}

	public function insertNews($text, $name = '', $anonim = 'off') {
    	if ($name == '') {
    		$name = $this->login;
    	}
	    global $link;
	    if ($anonim == 'on'){
	        $sql = "INSERT INTO `book` (`name`, `text`, `anonim`)
	            VALUES ('{$name}', '{$text}', 1)";
	    } else {
	        $sql = "INSERT INTO `book` (`name`, `text`, `anonim`)
	            VALUES ('{$name}', '{$text}', 0)";
	    }
	    mysqli_query($link, $sql);
	}	

	public function delete($id_news) {
	    global $link;
	    $sql = "DELETE FROM `book` WHERE id = '$id_news'";
	    mysqli_query($link, $sql);
	}

	public function change_forma($id_news) {
	    $str = "<div class='alert alert-success' role='alert' style='margin-bottom: auto'>
				<form method='post'>
					<label>Текст:</label><input type='text' name='text_update'><br>    
					<input class='btn btn-outline-success' type='submit' value='сохранить'>
				</form>
				</div>";
		return $str;
	}

	function change($id_news, $text_update){
	    global $link;
	    $sql = "UPDATE `book` SET `text`= '$text_update' WHERE id = '$id_news'";
	    mysqli_query($link, $sql);
	}

	function __construct($login = '', $password = '') {
		$this->login = $login;
		$this->password = $password;
	}
}
?>