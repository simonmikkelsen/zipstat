<?php

class AuthorizeDAOMySQL {
  function AuthorizeDAOMySQL($host, $database, $user, $password, $authTable, $credField, $userIdField) {
    $this->mysqli = new mysqli($host, $user, $password, $database);
    $this->mysqli->set_charset('utf8');
    $this->authTable = $authTable;
    $this->credField = $credField;
    $this->userIdField = $userIdField;
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
    echo "Store credentials: $userId, $hash";
    $stmt = $this->mysqli->prepare("UPDATE $this->authTable SET $this->credField = ? WHERE $this->userIdField = ?"); 
    $stmt->bind_param("ss", $hash, $userId);
    $stmt->execute();
  }
}

class AuthorizeFactory {
  function AuthorizeFactory(&$options) {
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

    $dao = new AuthorizeDAOMySQL($host, $db, $user, $passwd, $authTable, $credField, $userIdField);
    return new Authorize($dao);
  }
}

class Authorize {
  function Authorize($authDAO) {
    $this->authDAO = $authDAO;
    $this->algo = PASSWORD_DEFAULT;
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
  function doAuthorize($userId, $password, $legacyPasswordSaved = '') {
    if ($legacyPasswordSaved !== NULL and $legacyPasswordSaved !== '') {
      if ($password !== $legacyPasswordSaved) {
        return FALSE;
      }
      $password = $legacyPasswordSaved;
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
}

