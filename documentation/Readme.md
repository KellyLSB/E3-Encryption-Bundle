About the Encryption Bundle
===========================
The encryption bundle encrypts data using a salted Rijndael 256 algorithm. This salt is made up of two parts the first being a salt you specify site wide (Which is stored in your sites `environment.yaml` in the `encryption.key` key). The other is specified in the encryption and decryption process.

Dependencies
============
- PHP Mcrypt

Usage
=====

	$data = e::$encryption->encrypt($data, 'my salt');
	e::$encruption->decrypt($data, 'my salt');