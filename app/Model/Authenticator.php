<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Database\Explorer;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

final class Authenticator implements Nette\Security\Authenticator {
	private Explorer $database;
	private Passwords $passwords;

	public function __construct(Explorer $database, Passwords $passwords) {
		$this->database = $database;
		$this->passwords = $passwords;
	}

	public function register($username, $password) {
		$user = $this->database->table('users')->where('username', $username)->fetch();
		
		if(!$user) {
			$this->database->table('users')->insert([
				'username' => $username,
				'password' => $password
				//'password' => $this-passwords->hash($password)
			]);
		} else {
			throw new \Exception('Username is already taken');
		}
	}

	public function authenticate($username, $password) : SimpleIdentity {
		$user = $this->database->table('users')->where('username', $username)->fetch();

		if(!$user) {
			throw new Nette\Security\AuthenticationException('User not found.');
		}

		//if(!$this->passwords->verify($password, $user->password)) {
		if($user->password != $password) {
			throw new Nette\Security\AuthenticationException('Invalid password.');
		}

		return new SimpleIdentity(
			$user->id,
			null,
			['name' => $user->username]
		);
	}
}