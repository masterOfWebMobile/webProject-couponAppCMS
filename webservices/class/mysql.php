<?php
class DBmysql {
    // Соединение с базой данных
    private $DB;

    // Подключение к базе данных при создании класса
    function __construct($host, $user, $pass, $dbname) {
        $this->DB = new MySQLi($host, $user, $pass, $dbname);
        //$this->DB->set_charset('utf8');
    }

    function __destruct() {
        $this->DB->close();
    }

    // Запрос к базе
    public function Query($query) {
        $result = $this->DB->query($query);
		return $result;
    }

    // Получение ассоциированного массива ответа
    public function GetRows($query) {
        $result = $this->DB->query($query);

        $return = array();
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }

        $result->close();
        return $return;
    }

    // Получение ассоциированного массива строчки ответа
    public function GetRow($query) {
        $result = $this->DB->query($query);

        $row = $result->fetch_assoc();

        $result->close();
        return $row;
    }

    public function InsertID() {
        return $this->DB->insert_id;
    }
    
    public function real_escape_string($string){
    	$result = $this->DB->real_escape_string($string);
    	return $result;
    }
    
    public function escape_string($string){
    	$result = $this->DB->escape_string($string);
    	return $result;
    }
    
    public function num_rows($result){
    	$result = mysqli_num_rows($result);
    	return $result;
    }
}
?>