<?php
class book
{
	public $page = 1;
	public $param_X = 0;

	private function get_data() {
		global $link;
		$sql = "SELECT id, name, text, anonim FROM `book`";
		$result = mysqli_query($link, $sql);
		$data = array();
		while ($row = mysqli_fetch_array($result)) {

			$arr = array();
			$arr += array('id'=>$row[id],
						  'name'=>$row[name],
						  'text'=>$row[text],
						  'anonim'=>$row[anonim]);
			array_push($data, $arr);
		}
		return $data;
	}

	public function show_list() {
		$str = '';
		$data = $this->get_data();
		$data = array_reverse($data);
		$ids = $this->get_ids($data);
		$str .= '<table border = 1 style="text-align: center">';
		foreach ($data as $row) {
			if (in_array($row[id], $ids)) {
				$str .= $this->show_row($row);
			}
		}
		$str .= '</table>';
		return $str;
	}

	private function show_row($row) {
		if ($row[anonim] == 1) {
			$name = 'Anonym';
		} else {
			$name = $row[name];
		}
		$str = "<tr><td class='alert alert-info'><strong>$name</strong></td>
				<td class='alert alert-info'>$row[text]</td>";
		if ($_SESSION[status] == 'admin') {
			$str .= "<td class='alert alert-info'><a href=?action=delete&id=$row[id]>удалить</a>/<br>
                	 <a href=?action=change&id=$row[id]>редактировать</a></td>";
		}
		$str .= "</tr>";
		return $str;
	}

	// Получение id новостей, которые будут отображаться
	private function get_ids($data) {
		$result = array();
		$page = $this->page;
		$param_X = $this->param_X;
		for ($i = ($page - 1) * $param_X; $i < $page * $param_X; $i++) {
			if (isset($data[$i])) {
				$result[] += $data[$i][id];
			}
		}
		return $result;
	}

	public function show_pages() {
		$str = "Страницы: ";
    	$data = $this->get_data();
    	$count = count($data);
    	$i = 1;
    	while ($count > 0) {
    		$str .= "<strong><a href=?page=$i style='color: red'>$i</a></strong>" . " ";
    		$i++;
    		$count -= $this->param_X;
    	}
    	$str .= "</div>";
    	return $str;
	}

	public function show_action_top() {
		$str = "<div class='alert alert-danger' role='alert' style='margin-bottom: auto'>";
		if ($_SESSION[status] == 'noAuto') {
			$str .= "Вы можете <strong><a href=?action=authorization&act=exit_book style='color: red'>авторизоваться</a></strong>
			 или <strong><a href=?action=registration&act=exit_book style='color: red'>зарегистрироваться</a></strong>";
		} else {
			$str .= "Вы можете <strong><a href=?action=authorization&act=exit_book style='color: red'>выйти</a></strong>";
		}
		$str .=	"</br>";
		return $str;
	}

	public function show_action_bottom() {
		$str = "<div class='alert alert-success' role='alert' style='margin-bottom: auto' role='alert'>
				<strong><a href=?action=show_add_form style='color: green'>Добавить новость</a></strong></div></br>";
		return $str;
	}

	function __construct($page, $param_X) {
		$this->page = $page;
		$this->param_X = $param_X;
	}
}
?>