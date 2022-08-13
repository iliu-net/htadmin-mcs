<?php
require_once('tools/crypt_apr1_md5.php');
/**
 * htpasswd tools for Apache Basic Auth.
 *
 * Uses crypt only!
 */
class htpasswd {
	var $fp;
	var $filename;
	var $msg;

	/* All ht-files. These files are stored within the secured folder. */
	const HTPASSWD_NAME = "_htpasswd";

	function getmsg() {
		return $this->msg;
	}
	function __construct($configpath) {
		$path = realpath ( $configpath );
		$htpasswdfile = $path . "/" . self::HTPASSWD_NAME;

		$this->filename = $htpasswdfile;
		$this->msg = '';
		$this->fp = fopen($htpasswdfile, 'c+');
		if (!$this->fp) {
		  $this->msg .= 'CONFIG PATH: '.$configpath.PHP_EOL;
		  $ec = error_get_last();
		  $this->msg .= 'FOPEN ERROR'.PHP_EOL;
		  foreach ($ec as $k => $v) {
			  $this->msg .= $k . ': ' . $v . PHP_EOL;
		  }
		  echo '<pre>'.$this->msg.'</pre>';
		  exit;
		}
	}
	function user_exists($username) {
		$fp = $this->fp;
		rewind ( $fp );
		while ( !feof ( $fp )) {
		  list($lusername,) = explode ( ":", $line = rtrim ( fgets ( $this->fp)));
		  $lusername = trim($lusername);
		  if ($lusername == $username) return true;
		}
		return false;
	}
	function get_users() {
		rewind ( $this->fp );
		$users = array ();
		$i = 0;
		while ( ! feof ( $this->fp )) {
		  list($lusername,) = explode ( ":", $line = rtrim ( fgets ( $this->fp)));
		  $lusername = trim($lusername);
		  if (!$lusername) continue;
		  $users [$i] = $lusername;
		  $i ++;
		}
		return $users;
	}
	function user_add($username, $password) {
		if ($this->user_exists ( $username ))
			return false;
		fseek ( $this->fp, 0, SEEK_END );
		fwrite ( $this->fp, $username . ':' . self::htcrypt ( $password ) . "\n" );
		return true;
	}

	function user_delete($username) {
		$fp = $this->fp;
		$filename = $this->filename;

		$data = '';
		rewind ( $fp );
		while ( ! feof ( $fp ) && trim ( $lusername = array_shift ( explode ( ":", $line = rtrim ( fgets ( $fp ) ) ) ) ) ) {
			if (! trim ( $line ))
				break;
			if ($lusername != $username)
				$data .= $line . "\n";
		}
		$fp = fopen ( $filename, 'w' );
		fwrite ( $fp, rtrim ( $data ) . (trim ( $data ) ? "\n" : '') );
		fclose ( $fp );
		$fp = fopen ( $filename, 'r+' );
		return true;
	}

	function user_update($username, $password) {
		rewind ( $this->fp );
		$rc = false;
		$txt = [];
		while ( ! feof ( $this->fp )) {
		  list($lusername,) = explode ( ":", $line = rtrim ( fgets ( $this->fp)));
		  $lusername = trim($lusername);
		  if (!$lusername) continue;
		  if ($lusername == $username) {
		    $txt[] = $lusername . ':' . self::htcrypt ( $password );
		    $rc = true;
		  } else {
		    $txt[] = $line;
		  }
		}
		$txt[] = '';
		rewind($this->fp);
		fwrite($this->fp,implode("\n", $txt));
		return false;
	}
	function user_check($username, $password) {
	  rewind ( $this->fp );
	  $rc = false;
	  $txt = [];
	  while ( ! feof ( $this->fp )) {
	    $line = trim(fgets($this->fp));
	    if (!$line) continue;
	    list($lusername,$lpasswd) = explode(':',$line,2);
	    if ($lusername == $username) {
	      $xpw = self::htcrypt($password, $lpasswd);
	      return $xpw == $lpasswd;
	    } else {
	      $txt[] = $line;
	    }
	  }
	  $txt[] = '';
	  rewind($this->fp);
	  fwrite($this->fp,implode("\n", $txt));
	  return false;
	}

	static function htcrypt($password,$salt=null) {
		// return md5_hash_tool::crypt_apr_md5($password, 'ksjdfs');

		// return crypt ( $password, substr ( str_replace ( '+', '.', base64_encode ( pack ( 'N4', mt_rand (), mt_rand (), mt_rand (), mt_rand () ) ) ), 0, 22 ) );
		return crypt_apr1_md5($password,$salt);
	}



}


?>
