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
		echo 'Encrypted: '. ($string = $this->encrypt('test string')).'<br />';
		echo 'Decrypted: '. print_r(unserialize($this->decrypt(base64_decode("MjCsOfncm/fsjDUBzJcYEhBr5Y9Ckz2ygMhXF+2HmfgKCiMjIElWIC8gRGF0YSAjIwoK6vGUPpNJRQZBRO5vTRhyqSH9ENNpPLWSmsATsVR/9jHkbepXDeK4WtLJp3ALWgtmuQ6pd7n53UQ6z73Oqha0XlHIhVLgs6Ge43+eMR6AwMOZkBiXA2Gb1HAF4j0DdYoklD89jt0m75rm/atqSjBvPJF7WA9aDtqcDlqv5aX4tPFKFCbEWK2Tkm6TJ9COc05A6fKhnFNeE7Y2lJyy7i3baV0D67TLIAaL3WYteLLBEJEqI+3wW8pXVszoAECuFzPSDXOPPbKlRvdNqHufhHUqi0uXsdBDIB9C2J/w2QBMEELC9i1f+xqhepJmxD+eu0mPOdh4ryyDwfxFeUL5qRZgXdX/gVD8RahHTCMjUOuGAuYVrsY/EKcqNprhvDNWnapFXvHkSE1rJaKvaJJ4f3u/VBIDgjRBzK6jDrHM6owc69u63P/xvCH3PxQXwUjTrY5OO18bKaRQ8U1F699PnVrX8ch26YzyAOQT/Y5yGb7+J2XC2jxSFyPwvmW09XvqPWuvBW4X/Rjtl/fn6o+2L/P5zGrQuclAz9sUW+IQvfqoZXX2IWPG2CEnvLm105Nv+rriBQjfnktJnoLK5mOMKTrJ2qdhQRrghtLgU4Iyaz5k6BgLp0pLb0WqMEYSNsIC4r3IoA1J3y+0lzKvSWbOmIwOV9vZEEuh2ToT9tiPoH5Ywg3RM7dYO9BEPhbdNZwAdZEVscMZiHFmjojew6R0cnEkhL5BVR9yiRVKynfJdPPbXru5J4mZOW7hfHrTBZ3nIqS+fXNOn1b0iPbE6g/bqACnHJ4SIpU="), '7378742833')));
		e\Complete();
	}

}