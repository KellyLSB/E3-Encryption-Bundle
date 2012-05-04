<?php

namespace Bundles\Encryption;
use Exception;
use e;

class Bundle {

	private $key;

	public function _on_framework_loaded() {
		$this->key = e::$environment->requireVar('encryption.key', '/.+/', 'This is a key used when encrypting data.');
	}

	public function encrypt($data, $salt = '') {
		return $this->crypt($salt, $data, true);
	}

	public function decrypt($data, $salt = '') {
		return $this->crypt($salt, $data, false);
	}

	private function crypt($salt, $data, $crypt = true) {
		$key = md5($this->key . $salt);

		/**
		 * Generate the IV
		 */
		if($crypt) {
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_OFB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		}

		/**
		 * Or use the one of the encrypted data
		 */
		else {
			$data = explode("\n\n## IV / Data ##\n\n", $data);
			$iv = array_shift($data);
			$data = array_shift($data);
		}

		/**
		 * Encrypt / Decrypt
		 */
		if($crypt) return $iv."\n\n## IV / Data ##\n\n".mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_OFB, $iv);
		else return mcrypt_decrypt (MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_OFB, $iv);
	}

	public function route($path) {
		$get = e::$resource->get;
		$string = $get['string'];
		$salt = $get['salt'];
		
		echo 'Encrypted: '. ($string = $this->encrypt($string, $salt)).'<br />';
		echo 'Decrypted: '. $this->decrypt($string, $salt);
		e\Complete();
	}

}