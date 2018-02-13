<?php

class AuthenticationDAOMySQL {
  function AuthenticationDAOMySQL($host, $database, $user, $password, $authTable, $credField, $userIdField) {
    $this->mysqli = new mysqli($host, $user, $password, $database);
    $this->mysqli->set_charset('utf8');
    $this->authTable = $authTable;
    $this->credField = $credField;
    $this->userIdField = $userIdField;

    $this->typePwReset = 'passwordreset';
    $this->requestTable = 'zs20_change_requests';
  }

  function getCredentialHash($userId) {
    $stmt = $this->mysqli->prepare("SELECT $this->credField FROM $this->authTable WHERE $this->userIdField = ?"); 
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $stmt->bind_result($credField);
    $stmt->fetch();
    $stmt->close();
    return $credField;
  }

  function storeCredentials($userId, $hash) {
    $stmt = $this->mysqli->prepare("UPDATE $this->authTable SET $this->credField = ? WHERE $this->userIdField = ?"); 
    $stmt->bind_param("ss", $hash, $userId);
    $res = $stmt->execute();
    $stmt->close();
  }

  function createPwResetRequest($userId, $token, $expiresUnixtime) {
    $stmt = $this->mysqli->prepare("INSERT INTO $this->requestTable (username, token, type, expires) VALUES (?, ?, ?, FROM_UNIXTIME(?))"); 
    $stmt->bind_param("ssss", $userId, $token, $this->typePwReset, $expiresUnixtime);
    $stmt->execute();
    $stmt->close();
  }

  function getRequestByToken($token) {
    $stmt = $this->mysqli->prepare("SELECT username, type, UNIX_TIMESTAMP(expires) as expires FROM ".$this->requestTable." WHERE token = ?"); 
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($userId, $type, $expires);
    $stmt->fetch();
    $stmt->close();
    return array('username' => $userId, 'type' => $type, 'expires' => $expires);
  }
  
  function invalidateToken($token) {
    $stmt = $this->mysqli->prepare("DELETE FROM $this->requestTable WHERE token = ?"); 
    $stmt->bind_param("s", $token);
    $stmt->execute();
  }
}

class AuthenticationFactory {
  function AuthenticationFactory(&$options) {
    $this->options = &$options;
  }

  function create() {
    $db = $this->options->getOption('DB_database');
    $user = $this->options->getOption('DB_username');
    $host = $this->options->getOption('DB_hostname');
    $passwd = $this->options->getOption('DB_password');
    $authTable = $this->options->getOption('DB_tablename_main');
    $credField = 'hash';
    $userIdField = 'username';

    $dao = new AuthenticationDAOMySQL($host, $db, $user, $passwd, $authTable, $credField, $userIdField);
    return new Authenticate($dao);
  }
}

class Authenticate {
  function Authenticate($authDAO) {
    $this->authDAO = $authDAO;
    $this->algo = PASSWORD_DEFAULT;
    $this->resetTokenValidSeconds = 3600;
  } 

  /**
   * Returns true or false depending on the user can be authenticated or not.
   * Will at the same time rehash the password if nessesary.
   * If you are storing your password in plain text, then pass an empty
   * password and the plain text password in $legacyPasswordSaved.
   * That will convert it into a secure password.
   * If you have specified a $legacyPasswordSaved and this method has
   * returned true, you must delete your plain text password as it is
   * no longer needed and just poses a security thread.
   */
  function doAuthenticate($userId, $password, $legacyPasswordSaved = '') {
    $hash = $this->authDAO->getCredentialHash($userId);
    if (($hash === NULL or trim($hash) === '') and $legacyPasswordSaved !== NULL and $legacyPasswordSaved !== '') {
      if ($password !== $legacyPasswordSaved) {
        return FALSE;
      }
    } else {
      $hash = $this->authDAO->getCredentialHash($userId);
      if (! password_verify($password, $hash)) {
        return FALSE;
      }
    }
    if (password_needs_rehash($hash, $this->algo)) {
      $this->updatePasswordHash($userId, $password);
    }
    return TRUE;
  }

  function updatePasswordHash($userId, $password) {
      $newHash = password_hash($password, $this->algo);
      if ($newHash === NULL or $newHash === FALSE) {
        return FALSE;
      }
      $this->authDAO->storeCredentials($userId, $newHash);
      return TRUE;
  }

  function createPwResetRequest($userId, $token) {
    $this->authDAO->createPwResetRequest($userId, $token, time() + $this->resetTokenValidSeconds);
  }
 
  function invalidateToken($token) {
    $this->authDAO->invalidateToken($token);
  }

 function isPasswordResetTokenValid($token) {
    $req = $this->authDAO->getRequestByToken($token);
    if (! is_array($req) or $req['type'] !== 'passwordreset' or $req['expires'] < time()) {
      return null;
    }

    return $req['username'];
  }
}

