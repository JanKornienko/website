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

	public function newUser($username, $password) {
		$user = $this->database->table('users')->where('username', $username)->fetch();
		
		if(!$user) {
			$this->database->table('users')->insert([
				'username' => $username,
				'password' => $this->passwords->hash($password)
			]);
		} else {
			throw new \Exception('Username already exists');
		}
	}

	public function authenticate($username, $password) : SimpleIdentity {
		$user = $this->database->table('users')->where('username', $username)->fetch();

		if(!$user) {
			throw new Nette\Security\AuthenticationException('Username or Password is incorrect');
		} elseif(!$this->passwords->verify($password, $user->password)) {
			throw new Nette\Security\AuthenticationException('Username or Password is incorrect');
		} else {
			return new SimpleIdentity(
				$user->id,
				null,
				['name' => $user->username]
			);
		}
	}

	public function changePassword($id, $oldPassword, $newPassword) {
		$user = $this->database->table('users')->where('id', $id)->fetch();

		if(!$this->passwords->verify($oldPassword, $user->password)) {
			throw new Nette\Security\AuthenticationException('Old Password is incorrect');
		} else {
			$this->database->table('users')->where('id', $id)->update([
				'password' => $this->passwords->hash($newPassword)
			]);
		}
	}
}