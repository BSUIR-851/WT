<?php
	session_start();

	class DBWorker {
		public $host;
		public $db;
		private $user;
		private $pass;
		public $charset;
		public $pdo;

		public function __construct($db_data) {
			if ($db_data == NULL) {
				$db_data = [
					'host' => 'yura.morozov',
					'db' => 'volleybet',
					'user' => 'root',
					'pass' => 'rootroot',
					'charset' => 'utf8',
				];
			}
			$this->host  = $db_data['host'];
			$this->db = $db_data['db'];
			$this->user = $db_data['user'];
			$this->pass = $db_data['pass'];
			$this->charset = $db_data['charset'];
			$dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
			$opt = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			];
			$this->pdo = new PDO($dsn, $this->user, $this->pass, $opt);
		}

		private function getCommandNameById($id) {
			$data = $this->pdo->prepare("SELECT short_name FROM commands WHERE id = :id");
			$data->execute(array('id' => $id));
			return $data->fetch();
		}

		public function getCommands() {
			$data = $this->pdo->query("SELECT * FROM commands");
			return $data->fetchAll();
		}

		public function insertCommand($commandData) {
			$sth = $this->pdo->prepare("INSERT INTO commands SET full_name=:full_name, short_name=:short_name, 
										wins=:wins, loses=:loses");
			$sth->execute($commandData);
			return $this->pdo->lastInsertId();
		}

		public function updateCommand($commandData) {
			$sth = $this->pdo->prepare("UPDATE commands SET full_name=:full_name, short_name=:short_name, 
										wins=:wins, loses=:loses WHERE id=:id");
			$sth->execute($commandData);
		}

		public function getCommandInfoById($id) {
			$data = [
				'id' => $id,
			];

			$sth = $this->pdo->prepare("SELECT * FROM commands where id = :id");
			$sth->execute($data);
			$command = $sth->fetch();
			if ($command) {
				return $command;
			} else {
				return 404;
			}
		}

		public function deleteCommand($id) {
			$data = [
				'id' => $id,
			];

			$sth = $this->pdo->prepare("DELETE FROM commands where id = :id");
			$sth->execute($data);
		}

		private function formatMatchList($matches) {
			foreach($matches as $key => $match) {
				$commandName_1 = $this->getCommandNameById($match['id_command_1']);
				$commandName_2 = $this->getCommandNameById($match['id_command_2']);

				$match['name'] = $commandName_1['short_name'] . ':' . $commandName_2['short_name'];
				$match['name_command_1'] = $commandName_1['short_name'];
				$match['name_command_2'] = $commandName_2['short_name'];
				$match['coeff_1'] = round(floatval($match['coeff_1']), 2);
				$match['coeff_2'] = round(floatval($match['coeff_2']), 2);

				$matches[$key] = $match;
			}
			return $matches;
		}

		public function getMatchesId() {
			$data = $this->pdo->query("SELECT id FROM matches");
			return $data->fetchAll();
		}

		private function formatMatch($match) {
			$commandName_1 = $this->getCommandNameById($match['id_command_1']);
			$commandName_2 = $this->getCommandNameById($match['id_command_2']);

			$match['name'] = $commandName_1['short_name'] . ':' . $commandName_2['short_name'];
			$match['name_command_1'] = $commandName_1['short_name'];
			$match['name_command_2'] = $commandName_2['short_name'];
			$match['coeff_1'] = round(floatval($match['coeff_1']), 2);
			$match['coeff_2'] = round(floatval($match['coeff_2']), 2);
			return $match;
		}

		public function getMatchInfoById($id) {
			$data = [
				'id' => $id,
			];

			$sth = $this->pdo->prepare("SELECT * FROM matches where id = :id");
			$sth->execute($data);
			$match = $sth->fetch();
			if ($match) {
				return $this->formatMatch($match);
			} else {
				return 404;
			}
		}

		public function getMatches() {
			$live_matches = [];
			$future_matches = [];

			$data = $this->pdo->query("SELECT * FROM matches");
			$matches = $data->fetchAll();
			
			$matches = $this->formatMatchList($matches);

			foreach($matches as $match) {
				if ($match['isLive'] != NULL) {
					array_push($live_matches, $match);
				} else {
					array_push($future_matches, $match);
				}
			}

			$return_matches = [
				'live' => $live_matches,
				'future' => $future_matches,
			];
			
			return $return_matches;
		}

		public function updateMatch($editData) {
			$sth = $this->pdo->prepare("UPDATE matches SET id_command_1=:id_command_1, id_command_2=:id_command_2, date=:date, 
										coeff_1=:coeff_1, coeff_2=:coeff_2, isLive=:isLive WHERE id=:id");
			$sth->execute($editData);
		}

		public function deleteMatch($id) {
			$data = [
				'id' => $id,
			];

			$sth = $this->pdo->prepare("DELETE FROM matches where id = :id");
			$sth->execute($data);
		}

		public function insertMatch($matchData) {
			$sth = $this->pdo->prepare("INSERT INTO matches SET id_command_1=:id_command_1, id_command_2=:id_command_2, date=:date, 
										coeff_1=:coeff_1, coeff_2=:coeff_2");
			$sth->execute($matchData);
			return $this->pdo->lastInsertId();
		}

		public function insertUser($userData) {
			$sth = $this->pdo->prepare("INSERT INTO users SET reg_date = :now, login = :login, password = :pass, 
									  email = :email, first_name = :firstName, last_name = :lastName");
			$sth->execute($userData);
			return $this->pdo->lastInsertId();
		}

		public function isUserExists($login) {
			$data = [
				'login' => $login,
			];

			$sth = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE login = :login");
			$sth->execute($data);
			$count = $sth->fetchColumn();
			if ($count > 0) {
				return true;
			} else {
				return false;
			}
		}

		private function isAdmin($user_id) {
			$data = [
				'user_id' => $user_id,
			];
			$sth = $this->pdo->prepare("SELECT COUNT(*) FROM admins WHERE user_id = :user_id");
			$sth->execute($data);
			$count = $sth->fetchColumn();
			if ($count > 0) {
				return true;
			} else {
				return false;
			}
		}

		public function getUserInfoById($id) {
			$data = [
				'id' => $id,
			];
			$sth = $this->pdo->prepare("SELECT login, email, balance, first_name, last_name FROM users WHERE id = :id");
			$sth->execute($data);
			$userInfo = $sth->fetch();
			$userInfo['admin'] = $this->isAdmin($id);
			return $userInfo;
		}

		public function getUserData($login) {
			$data = [
				'login' => $login,
			];
			$sth = $this->pdo->prepare("SELECT id, login, password FROM users WHERE login = :login");
			$sth->execute($data);
			return $sth->fetch();
		}

	}

























