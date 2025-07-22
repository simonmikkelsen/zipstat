<?php

class SessionDAOMySQL {
  function __construct($host, $database, $user, $password, $sessionTable) {
    $this->mysqli = new mysqli($host, $user, $password, $database);
    $this->mysqli->set_charset('utf8');
    $this->sessionTable = $sessionTable;
  }

  function getSessionInfo($userId, $token) {
    $stmt = $this->mysqli->prepare("SELECT permissions, expires FROM $this->sessionTable WHERE username = ? AND token = ?"); 
    $stmt->bind_param("ss", $userId, $token);
    $stmt->execute();
    $stmt->bind_result($permField, $expiresField);
    $success = $stmt->fetch();
    $stmt->close();
    if ($success) {
      return array('permissions' => $permField, 'expires' => $expiresField);
    } else {
      return NULL;
    }
  }

  function createNewSession($username, $token, $permissions, $expire) {
    $sql = "INSERT INTO $this->sessionTable (token, username, expires, permissions) VALUES (?, ?, FROM_UNIXTIME(?), ?)"; 
    $stmt = $this->mysqli->prepare($sql);
    $stmt->bind_param("ssss", $token, $username, $expire, $permissions);
    $res = $stmt->execute();
    $stmt->close();
  }

  function sessionRefresh($username, $token, $expires) {
    $sql = "UPDATE $this->sessionTable SET expires = FROM_UNIXTIME(?) WHERE token = ? AND username = ?"; 
    $stmt = $this->mysqli->prepare($sql);
    $stmt->bind_param("sss", $expires, $token, $username);
    $res = $stmt->execute();
    $stmt->close();
  }

  function deleteSession($token) {
    $stmt = $this->mysqli->prepare("DELETE FROM $this->sessionTable WHERE token = ?");
    $stmt->bind_param("s", $token);
    $res = $stmt->execute();
    $stmt->close();
  }
}


class SessionFactory {
  function __construct(&$options) {
    $this->options = &$options;
  }

  function create() {
    $db = $this->options->getOption('DB_database');
    $user = $this->options->getOption('DB_username');
    $host = $this->options->getOption('DB_hostname');
    $passwd = $this->options->getOption('DB_password');
    $sessionTable= 'zs20_sessions';

    $dao = new SessionDAOMySQL($host, $db, $user, $passwd, $sessionTable);
    return new Session($dao, $this->options);
  }
}


class Session {
  function __construct($dao, &$options) {
    $this->dao = $dao;
    $this->options = $options;
    $this->cookieName = 'session';
    $this->sessionExpires = 3600;
    $this->sessionNoExpires = 365 * 24 * 3600;
  }

  function createNewSession($username, $permissions, $noExpire) {
    $expire = $this->getSessionExpire($noExpire);

    // Create session cookie value.
    $b = openssl_random_pseudo_bytes(128);
    $value = base64_encode($b);

    // Persist session in the database.
    $permissions = implode(",", $permissions);
    $this->dao->createNewSession($username, $value, $permissions, $expire);
    $this->setCookie($value, $expire);
  }

  function setCookie($value, $expire) {
    // Send the session cookie.
    $domain = '.' . $this->options->getOption('domain');
    $path = '/';
    $options = array(
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => true,
            'httponly' => true,
            'samesite' => 'strict'
    );
    setcookie($this->cookieName, $value, $options);
  }

  function getSessionPermissions($username) {
    if (! isset($_COOKIE[$this->cookieName])) {
      return NULL;
    }
    $token = $_COOKIE[$this->cookieName];
    $info = $this->dao->getSessionInfo($username, $token);
    if ($info !== NULL) {
      $expires = $this->getSessionExpire($this->isSessionNoExpire($info['expires']));
      $this->dao->sessionRefresh($username, $token, $expires);
      $this->setCookie($token, $expires);
      return explode(",", $info['permissions']);
    } else {
      return NULL;
    }
  }

  function getSessionExpire($noExpire) {
    if ($noExpire) {
      return time() + $this->sessionNoExpires;
    } else {
      return time() + $this->sessionExpires;
    }
  }

  function isSessionNoExpire($expires) {
    return $expires > time() + $this->sessionExpires;
  }

  function closeSession() {
    if (! isset($_COOKIE[$this->cookieName])) {
      return;
    }
    $token = $_COOKIE[$this->cookieName];
    $this->dao->deleteSession($token);
    $this->setCookie('', 0);
  }
}
?>
