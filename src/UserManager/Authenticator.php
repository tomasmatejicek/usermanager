<?php

namespace Mepatek\UserManager;

use Nette,
	Nette\Security,
    Nette\Security\IAuthenticator,
    Nette\Database\Context;


/**
 * Users authenticator.
 */
class Authenticator implements IAuthenticator
{
	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
        list($username, $password) = $credentials;
		// read password from table SYS_Uzivatele
        $row = $this->database->table('SYS_Uzivatele')->where('PrihlasovaciJmeno', $username)->fetch();

		if (!$row) {
			throw new Security\AuthenticationException('Chybné uživatelské jméno.', self::IDENTITY_NOT_FOUND);
		} elseif (!Security\Passwords::verify($password, $row->SifrovaneHeslo)) {
			throw new Security\AuthenticationException('Chybné heslo.', self::INVALID_CREDENTIAL);
		}

		$arr = $row->toArray();
		unset($arr['SifrovaneHeslo']);
		
        // read Role from SYS_UzivateleRole
        $role = $this->database->table("SYS_UzivateleRole")->select("Role")->where("UzivatelID", $row->UzivatelID)->fetchPairs("Role", "Role");
        $role = array_values($role);
        
        return new Security\Identity($row->UzivatelID, $role, $arr);
	}
    
    /**
     * Generate password token for change.
     * @param string $Email
     * @return string
     */
    public function resetPasswordToken($Email)
    {
        // userExist? Get UzivatelID
        $row = $this->database->table("SYS_Uzivatele")->select("UzivatelID")->where("Email", $Email)->fetch();
        if ($row) {
            $token = md5(md5(uniqid(rand(), true)));
            $tokenExpires = new \DateTime();
            $tokenExpires->add(new \DateInterval('PT60M'));     // 60 min for expire
            
            $this->database->table('SYS_Uzivatele')->where("UzivatelID",$row->UzivatelID)->update(array("HesloToken"=>$token,"HesloTokenPlatnost"=>$tokenExpires));
            return $token;
        } else {
            return false;
        }
    }
    
    /**
     * Change password and reset tokens.
     * @param integer $UzivatelID
     * @param string $NewPassword
     * @return boolean
     */
    public function changePassword($UzivatelID, $NewPassword)
    {
        $row = $this->database->table("SYS_Uzivatele")->select("UzivatelID")->where("UzivatelID", $UzivatelID)->fetch();
        if ($row) {
            $this->database->table("SYS_Uzivatele")->where("UzivatelID",$row->UzivatelID)->update( array("SifrovaneHeslo"=>Nette\Security\Passwords::hash($NewPassword),"HesloToken"=>null,"HesloTokenPlatnost"=>null) );
            return true;
        } else {
            return false;
        }
    }
    
    /**
    * Funkce zkontroluje složitost a délku hesla
    * @return integer = 0 = OK, 2=krátké, 4=jednoduché, 6=krátké a jednoduché
    */
    public function isPasswordSafe($password, $minlength = 8, $minlevel = 3)
    {
        $urovenHesla = 0;
        
        if(preg_match('`[A-Z]`',$password)) // alespoň jeden velký znak
            $urovenHesla++;
        if(preg_match('`[a-z]`',$password)) // alespoň jeden malý znak
            $urovenHesla++;
        if(preg_match('`[0-9]`',$password)) // alespoň jedno číslo
            $urovenHesla++;
        if(preg_match('`[-!"#$%&\'()* +,./:;<=>?@\[\] \\\\^_\`{|}~]`',$password)) // alespoň jeden speciální znak
            $urovenHesla++;
        
        $vysledek = 0;
        
        if($minlength > strlen($password))
            $vysledek += 2;
        if($minlevel > $urovenHesla)
            $vysledek += 4;
        
        return $vysledek;
    }
    
    /**
    * Validate token if exists and if not expired and get UserID
    * If not valid return false
    * @param string $token
    * @return integer
    */
    public function getUserForToken($token)
    {
        $now = new \DateTime();
        $row = $this->database->table("SYS_Uzivatele")->select("UzivatelID")->where("HesloToken = ? AND HesloTokenPlatnost >= ?", $token, $now)->fetch();
        if ($row)
            return $row->UzivatelID;
    
        return false;
    }
    
    
    /**
    * List of user activity (login) from newest to oldest
    * @param integer $UzivatelID
    * @return Selection
    */
    public function getUserActivity($UzivatelID, $limit=null)
    {
        $userActivity = $this->database->table("SYS_UzivatelePrihlaseni")->select("DatumCasPrihlaseni,IP")->where("UzivatelID", $UzivatelID)->order("DatumCasPrihlaseni");
        if ($limit) {
            $userActivity->limit($limit);
        }
        return $userActivity;
    }

    
    /**
    * Set/Update user Identity data
    * 
    * @param \Nette\Security\User $user
    * @param array|\Traversable $values
    */
    public function setUserIdentityData($user, $values)
    {
        if ($values instanceof \Traversable) {
            $values = iterator_to_array($values);

        } elseif (!is_array($values)) {
            throw new Nette\InvalidArgumentException(sprintf('First parameter must be an array, %s given.', gettype($values)));
        }

        // security
        foreach ( explode(",", "UzivatelID,PrihlasovaciJmeno,SifrovaneHeslo,HesloToken,HesloTokenPlatnost") as $secCol) {
            if (isset($values[$secCol])) unset($values[$secCol]);
        }
        
        // set CeleJmeno (if not exist in $values)
        if (!isset($values["CeleJmeno"])) {
            $values["CeleJmeno"] = trim(
                                (isset($values["TitulPred"]) ? $values["TitulPred"] : "") .
                                (isset($values["Jmeno"]) ? " " . $values["Jmeno"] : "") .
                                (isset($values["Prijmeni"]) ? " " . $values["Prijmeni"] : "") .
                                (isset($values["TitulZa"]) ? " " . $values["TitulZa"] : "")
                                );
        }
        // update table
        $this->database->table("SYS_Uzivatele")->where("UzivatelID",$user->id)->update($values);
        
        // update identity current user
        foreach ($user->identity->data as $name => $value) {
            if (isset($values[$name])) {
                $user->identity->$name = $values[$name];
            }
        }
    }
    
}
