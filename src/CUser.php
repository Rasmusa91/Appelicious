<?php
	class CUser 
	{
		private $mAccountInformation;
		private $mIsLoggedIn;
		
		function __construct()
		{
			$this->mIsLoggedIn = false;
		}
		
		function LoginArray($pUsername, $pPassword, $pAccounts)
		{
			$errorMessage = null;
			
			if(isset($pAccounts) && is_array($pAccounts)) 
			{
				if(isset($pUsername) && isset($pPassword))
				{
					$indexedAccounts = array_values($pAccounts);
					
					for($i = 0; $i < count($indexedAccounts) && !$this->IsLoggedIn(); $i++)
					{
						$indexedAccount = array_values($indexedAccounts[$i]);
						
						if(strtolower($indexedAccount[0]) == strtolower($pUsername) && $indexedAccount[1] == sha1($pPassword)) {
							$this->Login($pUsername);
						}
					}
					
					if(!$this->IsLoggedIn()) {
						$errorMessage = "Inget användarnamn matchade lösenordet";
					}
				}
				else {
					$errorMessage = "Ogiltigt användarnamn eller lösenord";
				}
			}
			else {
				$errorMessage = "Fel på listan med användarnamn";
			}
			
			return $errorMessage;
		}
		
		function LoginDatabase($pUsername, $pPassword, $pDatabase, $pAccountsTable = "accounts", $pCheckDeleted = false)
		{
			$errorMessage = null;
		 
			if(isset($pUsername) && isset($pPassword))
			{
				$db = new CDatabase($pDatabase);
				$query = "SELECT * FROM " . $pAccountsTable . " WHERE username = \"" . $pUsername . "\" AND password = \"" . sha1($pPassword) . "\" " . ($pCheckDeleted ? "AND (deleted IS NULL OR deleted >= NOW())" : "") . " LIMIT 1";	
				$res = $db->ExecuteSelectQueryAndFetchAll($query);
				
				if(count($res) > 0)
				{
					$acc = $res[0];
					$this->Login($acc);				
				}
				else {
					$errorMessage = "Inget användarnamn matchade lösenordet";
				}			
			}
			else {
				$errorMessage = "Ogiltigt användarnamn eller lösenord";
			}			
			
			return $errorMessage;
		}
		
		function Login($pAccountInformation)
		{
			$this->mAccountInformation = $pAccountInformation;
			$this->mIsLoggedIn = true;
		}
		
		function Logout()
		{
			$this->mAccountInformation = null;
			$this->mIsLoggedIn = false;
		}
		
		function IsLoggedIn()
		{
			return $this->mIsLoggedIn;
		}
		
		function HasAccess($pOwnerID = -1)
		{
			return ($this->IsLoggedIn() && ($pOwnerID == $this->mAccountInformation->id || $this->HasAdminAccess() || $pOwnerID == -1));
		}
		
		function HasAdminAccess()
		{
			return ($this->IsLoggedIn() && $this->mAccountInformation->access == 1);
		}
		
		function GetID()
		{
			return $this->mAccountInformation->id;
		}

		function GetPassword()
		{
			return $this->mAccountInformation->password;
		}

		function GetUsername()
		{
			return $this->mAccountInformation->username;
		}

		function GetAccess()
		{
			return $this->mAccountInformation->access;
		}
		
		function GetFullName()
		{
			return $this->mAccountInformation->first_name . " " . $this->mAccountInformation->last_name;
		}
		
		function GetFirstName()
		{
			return $this->mAccountInformation->first_name;
		}
		
		function GetLastName()
		{
			return $this->mAccountInformation->last_name;
		}
		
		function GetEmail()
		{
			return $this->mAccountInformation->email;
		}

		function GetCountry()
		{
			return $this->mAccountInformation->country;
		}	

		function GetDeletedAt()
		{
			return $this->mAccountInformation->deleted;
		}
	}
?>