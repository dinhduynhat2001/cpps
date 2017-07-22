<?php

final class Database extends PDO {

	private $config = [
		"Host" => "localhost",
		"User" => "root",
		"Pass" => "",
		"Name" => "sweater"
	];

	private $connection = null;

	public function __construct() {
		$connectionString = sprintf("mysql:dbname=%s;host=%s", $this->config["Name"], $this->config["Host"]);

		parent::__construct($connectionString, $this->config["User"], $this->config["Pass"]);
	}

	public function addUser($username, $password, $color, $email) {
		$hashedPassword = md5($password);
		
$query = $this->prepare( "SELECT `Email` FROM `users` WHERE `Email` = ?" );
$query->bindValue( 1, $email );
$query->execute();

if( $query->rowCount() > 0 ) { 
     response([
			"error" => "Email is already taken"
]);
}
		$insertPenguin = "INSERT INTO `users` (`ID`, `Username`, `Password`, `LoginKey`, `Active`, `Status`, `RegisteredTime`, `Email`, `Coins`, `Color`, `Head`, `Face`, `Neck`, `Body`, `Hand`, `Feet`, `Photo`, `Flag`, `Buddies`, `Ignores`, `Banned`, `Inventory`, `Igloo`, `Igloos`, `Music`, `Floor`, `RoomFurniture`, `Furniture`, `Postcards`, `Moderator`, `Rank`, `LastLogin`, `LastPaycheck`) VALUES ";
		$insertPenguin .= "(NULL, :username, :password, NULL, :aa, :bb, NULL, :bb, :cc, :aa, :dd, :dd, :dd, :dd, :dd, :dd, :dd, :dd, :bb, :bb, :dd, NULL, NULL, :bb, :dd, :dd, :bb, :bb, :bb, :dd, :aa, NULL, NULL);";
		
	   $insertPenguins = "INSERT INTO `igloos` (`igloo`) VALUES ";
	   $insertPenguins .= "(NULL, :igloo);";

	  $insertStatementz = $this->prepare($insertPenguins);
	  $insertStatementz->bindValue(":Igloo", "2");
	  $insertStatementz->execute();

		$insertStatement = $this->prepare($insertPenguin);
		$insertStatement->bindValue(":username", $username);
		$insertStatement->bindValue(":password", $hashedPassword);
		$insertStatement->bindValue(":email", $email);
		$insertStatement->bindValue(":date", time());
		$insertStatement->bindValue(":colour", $color);
		$insertStatement->bindValue(":aa", "1");
		$insertStatement->bindValue(":bb", "");
		$insertStatement->bindValue(":cc", "500");
		$insertStatement->bindValue(":dd", "0");
		
		$insertStatement->execute();
		$insertStatement->closeCursor();
		
		$penguinId = $this->lastInsertId();
		
		$this->addActiveIgloo($penguinId);
		$this->sendMail($penguinId, "sys", 0, "", time(), 125);
	}
	
	public function sendMail($recipientId, $senderName, $senderId, $postcardDetails, $sentDate, $postcardType) {
		$sendMail = $this->prepare("INSERT INTO `postcards` (`ID`, `Recipient`, `SenderName`, `SenderID`, `Details`, `Date`, `Type`) VALUES (NULL, :Recipient, :SenderName, :SenderID, :Details, :Date, :Type)");
		$sendMail->bindValue(":Recipient", $recipientId);
		$sendMail->bindValue(":SenderName", $senderName);
		$sendMail->bindValue(":SenderID", $senderId);
		$sendMail->bindValue(":Details", $postcardDetails);
		$sendMail->bindValue(":Date", $sentDate);
		$sendMail->bindValue(":Type", $postcardType);
		$sendMail->execute();
		$sendMail->closeCursor();

		$postcardId = $this->lastInsertId();

		return $postcardId;
	}

    
	private function addActiveIgloo($penguinId) {
		$insertStatement = $this->prepare("INSERT INTO `igloos` (`ID`, `Owner`) VALUES (NULL, :Owner);");
		$insertStatement->bindValue(":Owner", $penguinId);
		$insertStatement->execute();
		$insertStatement->closeCursor();
		
		$iglooId = $this->lastInsertId();
		
		$setActiveIgloo = $this->prepare("UPDATE `users` SET `Igloo` = :Igloo WHERE ID = :Penguin;");
		$setActiveIgloo->bindValue(":Igloo", $iglooId);
		$setActiveIgloo->bindValue(":Penguin", $penguinId);
		$setActiveIgloo->execute();
		$setActiveIgloo->closeCursor();
	}
	

	
	public function usernameTaken($username) {
		$usernameTaken = "SELECT Username FROM `users` WHERE Username = :Username";
		
		$takenQuery = $this->prepare($usernameTaken);
		$takenQuery->bindValue(":Username", $username);
		$takenQuery->execute();
		
		$rowCount = $takenQuery->rowCount();
		$takenQuery->closeCursor();
		
		return $rowCount > 0;
	}
	
	
	
	public function takenUsernames($username) {
		$usernamesTaken = "SELECT Username FROM `users` WHERE Username LIKE :Username";
		
		$usernamesQuery = $this->prepare($usernamesTaken);
		$usernamesQuery->bindValue(":Username", $username . "%");
		$usernamesQuery->execute();
		
		$usernames = $usernamesQuery->fetchAll(self::FETCH_COLUMN);
		return $usernames;
	}

}


session_start();

function response($data) {
	die(http_build_query($data));
}

function attemptDataRetrieval($key, $session = false) {
	if(!$session && array_key_exists($key, $_POST)) {
		return $_POST[$key];
	}
    
    if($session && array_key_exists($key, $_SESSION)) {
        return $_SESSION[$key];
    }

	response([
		"error" => ""
	]);
}

$action = attemptDataRetrieval("action");

if($action == "validate_agreement") {
	$agreeTerms = attemptDataRetrieval("agree_to_terms");
	$agreeRules = attemptDataRetrieval("agree_to_rules");
	if(!$agreeTerms || !$agreeRules) {
		response([
			"error" => "You must agree to the Rules and Terms."
		]);
	}
	
	response([
		"success" => 1
	]);
} elseif($action == "validate_username") {
	$username = attemptDataRetrieval("username");
	$color = attemptDataRetrieval("colour");
	$colors = range(1, 15);
	
	if(strlen($username) == 0) {
		response([
			"error" => "You need to name your penguin."
		]);
	} elseif(strlen($username) < 4 || strlen($username) > 12) {
		response([
			"error" => "Penguin name is too short."
		]);
	} elseif(preg_match_all("/[0-9]/", $username) > 5) {
		response([
			"error" => "Penguin names can only contain 5 numbers."
		]);
	} elseif(!preg_match("/[A-z]/i", $username)) {
		response([
			"error" => "Penguin names must contain at least 1 letter."
		]);
    } elseif(preg_match("/[^A-Za-z0-9)(*&^$!`\_+={};:@~#>.<]/", $username)) {
		response([
			"error" => "That penguin name is not allowed."
		]);
    } elseif(!is_numeric($color) || !in_array($color, $colors)) {
		response([
			"error" => ""
		]);
	}
	
	$db = new Database();

	if($db->usernameTaken($username)) {
		$username = preg_replace("/\d+$/", "", $username);
		$takenUsernames = $db->takenUsernames($username);
		$i = 1;
		while(true) {
			$suggestion = $username . $i++;
			if(preg_match_all("/[0-9]/", $username) > 1) {
				response([
					"error" => "Penguin name is already taken."
				]);
			}
			if(!in_array(strtolower($suggestion), $takenUsernames)) {
				break;
			}
		}
		response([
			"error" => "Penguin name is already taken. Try $suggestion"
		]);
	}
	
	
	
	$_SESSION['sid'] = session_id();
	$_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
	$_SESSION['colour'] = $color;
	
	response([
		"success" => 1,
		"sid" => session_id()
	]);
	
} elseif($action == "validate_password_email") {
	$sessionId = attemptDataRetrieval("sid", true);
	$username = attemptDataRetrieval("username", true);
	$color = attemptDataRetrieval("colour", true);
	$password = attemptDataRetrieval("password");
	$passwordConfirm = attemptDataRetrieval("password_confirm");
	$email = attemptDataRetrieval("email");
	
	if($sessionId !== session_id()) {
		response([
			"error" => ""
		]);
	} elseif($password !== $passwordConfirm) {
		response([
			"error" => "Passwords do not match."
		]);
	} elseif(strlen($password) < 4) {
		response([
			"error" => "Password is too short."
		]);
	} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		response([
			"error" => "Invalid email address."
		]);
	}
	
	$db = new Database();
	$db->addUser($username, $password, $color, $email);
	
	session_destroy();
	
	response([
		"success" => 1
	]);
}

?>