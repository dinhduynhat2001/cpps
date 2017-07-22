<?php

// TODO: Perhaps implement getPuffleColumn(s) method(s)

namespace Sweater;
use Silk;

final class CPDatabase extends \PDO {

	public function getColumn($mixPlayer, $strColumn){
		$strWhere = is_numeric($mixPlayer) ? 'ID' : 'Username';
		$strQuery = "SELECT $strColumn FROM `users` WHERE $strWhere = :Player";
		try {
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $mixPlayer);
			$objStatement->execute();
			$objStatement->bindColumn($strColumn, $mixResult);
			$objStatement->fetch(\PDO::FETCH_BOUND);
			$objStatement->closeCursor();
			return $mixResult;
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}
	
	public function deleteMailFromUser($recipientId, $senderId) {
		try {
			$deleteMail = $this->prepare("DELETE FROM `postcards` WHERE `Recipient` = :Recipient AND `SenderID` = :Sender");
			
			$deleteMail->bindValue(":Recipient", $recipientId);
			$deleteMail->bindValue(":Sender", $senderId);
			
			$deleteMail->execute();
			$deleteMail->closeCursor();
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}
	
	public function ownsPostcard($postcardId, $penguinId) {
		try {
			$ownsPostcard = $this->prepare("SELECT Recipient FROM `postcards` WHERE ID = :Postcard");
			$ownsPostcard->bindValue(":Postcard", $postcardId);
			$ownsPostcard->execute();

			list($recipientId) = $ownsPostcard->fetch(\PDO::FETCH_NUM);

			$ownsPostcard->closeCursor();

			return $penguinId == $recipientId;
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}


	public function deleteMail($postcardId) {
		try {
			$deleteMail = $this->prepare("DELETE FROM `postcards` WHERE `ID` = :Postcard");
			$deleteMail->bindValue(":Postcard", $postcardId);
			$deleteMail->execute();
			$deleteMail->closeCursor();
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function mailChecked($penguinId) {
		try {
			$mailChecked = $this->prepare("UPDATE `postcards` SET HasRead = '1' WHERE Recipient = :Penguin");
			$mailChecked->bindValue(":Penguin", $penguinId);
			$mailChecked->execute();
			$mailChecked->closeCursor();
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function sendMail($recipientId, $senderName, $senderId, $postcardDetails, $sentDate, $postcardType) {
		try {
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
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function getUnreadPostcardCount($penguinId) {
		try {
			$getPostcards = $this->prepare("SELECT HasRead FROM `postcards` WHERE Recipient = :Penguin");
			$getPostcards->bindValue(":Penguin", $penguinId);
			$getPostcards->execute();

			$penguinPostcards = $getPostcards->fetchAll(\PDO::FETCH_NUM);
			$getPostcards->closeCursor();

			$unreadCount = 0;
			foreach($penguinPostcards as $hasRead) {
				list($hasRead) = $hasRead;

				$unreadCount = $hasRead == 0 ? ++$unreadCount : $unreadCount;
			}

			return $unreadCount;
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function getPostcardCount($penguinId) {
		try {
			$getPostcards = $this->prepare("SELECT Recipient FROM `postcards` WHERE Recipient = :Penguin");
			$getPostcards->bindValue(":Penguin", $penguinId);
			$getPostcards->execute();

			$postcardCount = $getPostcards->rowCount();
			$getPostcards->closeCursor();

			return $postcardCount;
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function getPostcardsById($penguinId) {
		try {
			$getPostcards = $this->prepare("SELECT SenderName, SenderID, Type, Details, Date, ID FROM `postcards` WHERE Recipient = :Penguin");
			$getPostcards->bindValue(":Penguin", $penguinId);
			$getPostcards->execute();

			$receivedPostcards = $getPostcards->fetchAll(\PDO::FETCH_NUM);
			$getPostcards->closeCursor();

			return $receivedPostcards;
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}
	
	public function getColumns($intPlayer, Array $arrColumns){
		$strColumns = implode(', ', $arrColumns);
		$strQuery = "SELECT $strColumns FROM `users` WHERE ID = :Player";
		try {
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $intPlayer);
			$objStatement->execute();
			$arrColumns = $objStatement->fetch(\PDO::FETCH_ASSOC);
			$objStatement->closeCursor();
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
		return $arrColumns;
	}

	public function divorceBestFriends($strPlayer){
		try {
			$strQuery = "SELECT `Bestie` FROM `users` WHERE Username = :Player";
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $strPlayer);
			$objStatement->execute();
			$strBestie = $objStatement->fetchColumn();
			$objStatement->closeCursor();
			$strQuery = "UPDATE `users` SET `Bestie` = -1 WHERE Username = :Player OR Username = :Bestie";
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $strPlayer);
			$objStatement->bindValue(':Bestie', $strBestie);
			$objStatement->execute();
			$objStatement->closeCursor();
		} catch(\PDOException $objException) {
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}

	public function makeBestFriends($strPlayer, $strBestie) {
		$strQuery = "UPDATE `users` SET `Bestie` = :Player WHERE Username = :Bestie";
		$objStatement = $this->prepare($strQuery);
		$objStatement->bindValue(':Player', $strPlayer);
		$objStatement->bindValue(':Bestie', $strBestie);
		$objStatement->execute();
		$objStatement->closeCursor();
		$strQuery = "UPDATE `users` SET `Bestie` = :Bestie WHERE Username = :Player";
		$objStatement = $this->prepare($strQuery);
		$objStatement->bindValue(':Player', $strPlayer);
		$objStatement->bindValue(':Bestie', $strBestie);
		$objStatement->execute();
		$objStatement->closeCursor();
	}
	
	public function divorceMarriage($strPlayer){
		try {
			$strQuery = "SELECT `Spouse` FROM `users` WHERE Username = :Player";
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $strPlayer);
			$objStatement->execute();
			$strSpouse = $objStatement->fetchColumn();
			$objStatement->closeCursor();
			$strQuery = "UPDATE `users` SET `Spouse` = -1 WHERE Username = :Player OR Username = :Spouse";
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $strPlayer);
			$objStatement->bindValue(':Spouse', $strSpouse);
			$objStatement->execute();
			$objStatement->closeCursor();
		} catch(\PDOException $objException) {
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}

	public function makeMarriage($strPlayer, $strSpouse) {
		$strQuery = "UPDATE `users` SET `Spouse` = :Player WHERE Username = :Spouse";
		$objStatement = $this->prepare($strQuery);
		$objStatement->bindValue(':Player', $strPlayer);
		$objStatement->bindValue(':Spouse', $strSpouse);
		$objStatement->execute();
		$objStatement->closeCursor();
		$strQuery = "UPDATE `users` SET `Spouse` = :Spouse WHERE Username = :Player";
		$objStatement = $this->prepare($strQuery);
		$objStatement->bindValue(':Player', $strPlayer);
		$objStatement->bindValue(':Spouse', $strSpouse);
		$objStatement->execute();
		$objStatement->closeCursor();
	}
	
	public function adoptPuffle($intPlayer, Array $arrPuffle){
		list($strPuffle, $intPuffle) = $arrPuffle;
		$strQuery = 'INSERT INTO `Puffles` (`Owner`, `Name`, `Type`) VALUES (:Owner, :Name, :Type)';
		$objStatement = $this->prepare($strQuery);
		$objStatement->bindValue(':Owner', $intPlayer, \PDO::PARAM_INT);
		$objStatement->bindValue(':Name', $strPuffle);
		$objStatement->bindValue(':Type', $intPuffle, \PDO::PARAM_INT);
		$objStatement->execute();
		$objStatement->closeCursor();
	}
	
	public function changePuffleStats($intPuffle, $strStatistic, $intNumber, $blnIncrement = true){
		try {
			$strQuery = "SELECT $strStatistic FROM `Puffles` WHERE ID = :Puffle";
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Puffle', $intPuffle);
			$objStatement->execute();
			$arrColumns = $objStatement->fetch(\PDO::FETCH_ASSOC);
			$objStatement->closeCursor();
			$intStatisticValue = $arrColumns[$strStatistic];
			$blnIncrement ? $intStatisticValue += $intNumber : $intStatisticValue -= $intNumber;
			$strQuery = "UPDATE `Puffles` SET $strStatistic = :Value WHERE ID = :Puffle";
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Value', $intStatisticValue);
			$objStatement->bindValue(':Puffle', $intPuffle);
			$objStatement->execute();
			$objStatement->closeCursor();
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		} 
	}
	
	public function getBuddies($intPlayer){
		try {
			$strQuery = 'SELECT Buddies FROM `users` WHERE ID = :Player';
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $intPlayer);
			$objStatement->execute();
			$objStatement->bindColumn('Buddies', $strBuddies);
			$objStatement->fetch(\PDO::FETCH_BOUND);
			$objStatement->closeCursor();
			$arrBuddies = json_decode($strBuddies, true);
			return $arrBuddies;
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}
	
	public function getLoginKey($intPlayer){
		try {
			$strQuery = 'SELECT LoginKey FROM `users` WHERE ID = :Player';
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $intPlayer);
			$objStatement->execute();
			$objStatement->bindColumn('LoginKey', $strLoginKey);
			$objStatement->fetch(\PDO::FETCH_BOUND);
			$objStatement->closeCursor();
			return $strLoginKey;
		}
		catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}

	public function getPlayer($intPlayer){
		$arrPlayer = $this->getColumns($intPlayer, ['Username', 'Color', 'Head', 'Face', 'Neck', 'Body', 'Hand', 'Feet', 'Flag', 'Photo']);
		$strPlayer = $intPlayer;
		$strPlayer .= '|' . $arrPlayer['Username'];
		$strPlayer .= '|' . 1;
		$strPlayer .= '|' . $arrPlayer['Color'];
		$strPlayer .= '|' . $arrPlayer['Head'];
		$strPlayer .= '|' . $arrPlayer['Face'];
		$strPlayer .= '|' . $arrPlayer['Neck'];
		$strPlayer .= '|' . $arrPlayer['Body'];
		$strPlayer .= '|' . $arrPlayer['Hand'];
		$strPlayer .= '|' . $arrPlayer['Feet'];
		$strPlayer .= '|' . $arrPlayer['Flag'];
		$strPlayer .= '|' . $arrPlayer['Photo'] . '|';
		unset($arrPlayer);
		return $strPlayer;
	}
	
	public function getPlayerIgloo($intPlayer){
		$strIgloo = $intPlayer;
		$strIgloo .= '%' . $this->getColumn($intPlayer, 'Igloo');
		$strIgloo .= '%' . $this->getColumn($intPlayer, 'Music');
		$strIgloo .= '%' . $this->getColumn($intPlayer, 'Floor');
		$strIgloo .= '%' . $this->getColumn($intPlayer, 'RoomFurniture');
		return $strIgloo;
	}
	
	public function getPuffleColumn($intPuffle, $strColumn){
		try {
			$strQuery = "SELECT $strColumn FROM `Puffles` WHERE ID = :Puffle";
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Puffle', $intPuffle);
			$objStatement->execute();
			$objStatement->bindColumn($strColumn, $mixResult);
			$objStatement->fetch(\PDO::FETCH_BOUND);
			$objStatement->closeCursor();
			return $mixResult;
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}
	
	public function getPuffles($intPlayer){
		if(is_numeric($intPlayer)){
			$strQuery = 'SELECT ID, Name, Type, Health, Hunger, Rest, Walking FROM `Puffles` WHERE `Owner` = :Owner';
			try {
				$objStatement = $this->prepare($strQuery);
				$objStatement->bindValue(':Owner', $intPlayer, \PDO::PARAM_INT);
				$objStatement->execute();
				$arrPuffles = $objStatement->fetchAll(\PDO::FETCH_NUM);
				$objStatement->closeCursor();
				$strPuffles = '';
				foreach($arrPuffles as $arrPuffle){
					$intWalking = $arrPuffle[6];
					if($intWalking == 0) $strPuffles .= '%' . join('|', $arrPuffle);
				}
				$strPuffles = substr($strPuffles, 1);
				return $strPuffles;
			} catch(\PDOException $objException){
				Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
			}
		}
	}
	
	public function getPuffleString($intPuffle){
		if(is_numeric($intPuffle)){
			$strQuery = 'SELECT ID, Name, Type, Health, Hunger, Rest FROM `Puffles` WHERE ID = :Puffle';
			try {
				$objStatement = $this->prepare($strQuery);
				$objStatement->bindValue(':Puffle', $intPuffle, \PDO::PARAM_INT);
				$objStatement->execute();
				$arrPuffle = $objStatement->fetch(\PDO::FETCH_NUM);
				print_r($arrPuffle);
				$strPuffle = join('|', $arrPuffle);
				return $strPuffle;
			} catch(\PDOException $objException){
				Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
			}
		}
	}
	
	// Should be only used once for every client instance
	public function getRow($strUsername){
		if(is_string($strUsername)){
			$strQuery = 'SELECT * FROM `users` WHERE Username = :Player';
			try {
				$objStatement = $this->prepare($strQuery);
				$objStatement->bindValue(':Player', $strUsername);
				$objStatement->execute();
				$arrPlayer = $objStatement->fetch(\PDO::FETCH_ASSOC);
				$objStatement->closeCursor();
				return $arrPlayer;
			} catch(\PDOException $objException){
				Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
			}
		}
	}
	
	public function getServerPopulation(){
        $strQuery = 'SELECT * FROM `Stats`';
        try {
            $objStatement = $this->prepare($strQuery);
            $objStatement->execute();
            $arrServer = $objStatement->fetch(\PDO::FETCH_ASSOC);
            $objStatement->closeCursor();
        } catch(\PDOException $objException){
            Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
        }
        return $arrServer['Population'];
    }
	
	public function getUsername($intPlayer){
		try {
			$strQuery = 'SELECT Username FROM `users` WHERE ID = :Player';
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $intPlayer);
			$objStatement->execute();
			$objStatement->bindColumn('Username', $strUsername);
			$objStatement->fetch(\PDO::FETCH_BOUND);
			$objStatement->closeCursor();
			return $strUsername;
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}
	
	public function playerExists($mixPlayer){
		$strWhere = is_numeric($mixPlayer) ? 'ID' : 'Username';
		try {
			$strQuery = "SELECT ID FROM `users` WHERE $strWhere = :Player";
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $mixPlayer);
			$objStatement->execute();
			$intRows = $objStatement->rowCount();
			$objStatement->closeCursor();
			return $intRows > 0;
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}
	
	public function setBuddies($intPlayer, Array $arrBuddies){
		try {
			$strBuddies = json_encode($arrBuddies);
			$strQuery = 'UPDATE `users` SET Buddies = :Buddies WHERE ID = :Player';
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Buddies', $strBuddies);
			$objStatement->bindValue(':Player', $intPlayer);
			$objStatement->execute();
			$objStatement->closeCursor();
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}
	
	public function updatePuffleStatistics($mixPlayer, Client $objClient){
		$strQuery = "SELECT ID, Name, Health, Hunger, Rest, Type FROM `Puffles` WHERE Owner = :Player";
		try {
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Player', $mixPlayer);
			$objStatement->execute();
			$arrPuffles = $objStatement->fetchAll(\PDO::FETCH_ASSOC);
			$objStatement->closeCursor();
			$intRows = $objStatement->rowCount();
			if($intRows !== 0){
				$intRand = mt_rand(0, 4);
				$intTime = strtotime('-5 days');
				$intLastLogin = $this->getColumn($mixPlayer, 'LastLogin');
				if($intLastLogin !== null){
					$intSubtract = $intLastLogin - $intTime;
					$blnMajor = $intSubtract < 0;
				} else {
					$blnMajor = false;
				}
				if($intRand === 2 ^ $blnMajor){
					Silk\Logger::Log('Updating puffles', Silk\Logger::Debug);
					foreach($arrPuffles as $arrPuffle){
						$intPuffle = $arrPuffle['ID'];
						$intHealth = $arrPuffle['Health'];
						$intHunger = $arrPuffle['Hunger'];
						$intRest = $arrPuffle['Rest'];
						$intMin = $blnMajor ? 25 : 0;
						$intMax = $blnMajor ? 45 : 15;
						$intHealth = $intHealth - mt_rand($intMin, $intMax);
						$intHunger = $intHunger - mt_rand($intMin, $intMax);
						$intRest = $intRest - mt_rand($intMin, $intMax);
						$strHealth = "UPDATE `Puffles` SET Health = $intHealth WHERE ID = :Puffle;";
						$strHunger = "UPDATE `Puffles` SET Hunger = $intHunger WHERE ID = :Puffle;";
						$strRest = "UPDATE `Puffles` SET Rest = $intRest WHERE ID = :Puffle;";
						$strUpdate = sprintf('%s %s %s', $strHealth, $strHunger, $strRest);
						$objStatement = $this->prepare($strUpdate);
						$objStatement->bindValue(':Puffle', $intPuffle);
						$objStatement->execute();
						$objStatement->closeCursor();
					}
				}
				$objStatement = $this->prepare($strQuery);
				$objStatement->bindValue(':Player', $mixPlayer);
				$objStatement->execute();
				$arrPuffles = $objStatement->fetchAll(\PDO::FETCH_ASSOC);
				$objStatement->closeCursor();
				foreach($arrPuffles as $arrPuffle){
					$intHealth = $arrPuffle['Health'];
					$intPuffle = $arrPuffle['ID'];
					$intType = $arrPuffle['Type'];
					$strName = $arrPuffle['Name'];
					if($intHealth < 5){
						$strQuery = "DELETE FROM `Puffles` WHERE ID = :Puffle";
						$objStatement = $this->prepare($strQuery);
						$objStatement->bindValue(':Puffle', $intPuffle);
						$objStatement->execute();
						$objStatement->closeCursor();
						// TODO: Implement sending the player some mail here
						$intPostcard = function() use ($intType){
							switch($intType){
								case 0: return 100;
								case 1: return 101;
								case 2: return 102;
								case 3: return 103;
								case 4: return 104;
								case 5: return 105;
								case 6: return 106;
							}
						};
						$strPostcards = $this->getColumn($mixPlayer, 'Postcards');
						$arrPostcards = json_decode($strPostcards, true);
						$arrPostcard = [
							'From' => [
								'ID' => $intPuffle,
								'Name' => $strName
							],
							'ID' => $intPostcard(),
							'Message' => $strName,
							'Timestamp' => time(),
							'Unique' => $mixPlayer . sizeof($arrPostcards)
						];
						$arrPostcards[$arrPostcard['Unique']] = $arrPostcard;
						$strPostcards = json_encode($arrPostcards);
						$this->updateColumn($mixPlayer, 'Postcards', $strPostcards);
						$strPostcard = $arrPostcard['From']['Name'] . '%' . $arrPostcard['From']['ID'] . '%' . $arrPostcard['ID'] . '%%' . $arrPostcard['Timestamp'] . '%' . $arrPostcard['Unique'];
						$objClient->arrPostcards = $arrPostcards;
						$objClient->sendXt('mr', -1, $strPostcard);
					}
				}
						
			}
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}
	
	public function updateColumn($mixPlayer, $strColumn, $mixValue){
		$strWhere = is_numeric($mixPlayer) ? 'ID' : 'Username';
		$strQuery = "UPDATE `users` SET $strColumn = :Value WHERE $strWhere = :Player";
		try {
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Value', $mixValue);
			$objStatement->bindValue(':Player', $mixPlayer);
			$objStatement->execute();
			$objStatement->closeCursor();
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}
	
	public function updatePuffleColumn($intPuffle, $strColumn, $mixValue){
		try {
			$strQuery = "UPDATE `Puffles` SET $strColumn = :Value WHERE ID = :Puffle";
			$objStatement = $this->prepare($strQuery);
			$objStatement->bindValue(':Value', $mixValue);
			$objStatement->bindValue(':Puffle', $intPuffle);
			$objStatement->execute();
			$objStatement->closeCursor();
		} catch(\PDOException $objException){
			Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
		}
	}
	
	public function updateStats($intServer, $intClients, $strOnline){
        $strQuery = 'INSERT INTO `Stats` VALUES (:Server, :Clients, :Online) ON duplicate KEY UPDATE Population = :Clients, Players = :Online;';
        try {
            $objStatement = $this->prepare($strQuery);
            $objStatement->bindValue(':Server', $intServer);
            $objStatement->bindValue(':Clients', $intClients);
            $objStatement->bindValue(':Online', $strOnline);
            $objStatement->execute();
            $objStatement->closeCursor();
        } catch(\PDOException $objException){
            Silk\Logger::Log($objException->getMessage(), Silk\Logger::Warn);
        }
    }
	
}

?>
