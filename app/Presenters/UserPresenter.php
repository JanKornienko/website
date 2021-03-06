<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Database\Explorer;
use App\Model\Authenticator;
use Nette\Application\UI\Form;
use Nette\Utils\Strings;

final class UserPresenter extends Nette\Application\UI\Presenter {
	private Explorer $database;
	private Authenticator $auth;
	private $error;

	public function __construct(Explorer $database, Authenticator $auth) {
		$this->database = $database;
		$this->auth = $auth;
	}

	public function createComponentNewUser() {
		$form = new Form;
		$form->addText('username', 'Username:')->setRequired('Username is required');
		$form->addPassword('password', 'Password:')->setRequired('Password is required');
		$form->addSubmit('submit', 'Sign Up');

		$form->onSuccess[] = [$this, 'newUserSuccess'];

		return $form;
	}

	public function newUserSuccess(array $values) : void {
		try {
			$this->auth->newUser(strtolower($values['username']), $values['password']);
		} catch(\Exception $e) {
			$this->error = $e->getMessage();
		}
	}

	public function createComponentLogin() {
		$form = new Form;
		$form->addText('username', 'Username:')->setRequired('Username is required');
		$form->addPassword('password', 'Password:')->setRequired('Password is required');
		$form->addSubmit('submit', 'Log In');

		$form->onSuccess[] = [$this, 'loginSuccess'];

		return $form;
	}

	public function loginSuccess(array $values) : void {
		try {
			$this->getUser()->login($values['username'], $values['password']);
		} catch(\Exception $e) {
			$this->error = $e->getMessage();
		}
	}

	public function createComponentChangePassword() {
		$form = new Form;
		$form->addPassword('oldPassword', 'Old Password:')->setRequired('Old password is required');
		$form->addPassword('newPassword', 'New Password:')->setRequired('New password is required');
		$form->addSubmit('submit', 'Change Password');

		$form->onSuccess[] = [$this, 'changePasswordSuccess'];

		return $form;
	}

	public function changePasswordSuccess(array $values) : void {
		try {
			$this->auth->changePassword($this->getUser()->id, $values['oldPassword'], $values['newPassword']);
		} catch(\Exception $e) {
			$this->error = $e->getMessage();
		}
	}

	public function actionOut() {
		$this->getUser()->logout(true);
		$this->redirect('Homepage:');
	}

	public function actionDeleteUser($id) {
		$this->database->table('users')->where('id', $id)->delete();
		$this->redirect('User:delete');
	}

	public function renderDefault() {
		$this->template->alert = $this->error;
		$this->template->tempName = 'User:';
	}

	public function renderNew() {
		$this->template->alert = $this->error;
		$this->template->tempName = 'User:new';
	}

	public function renderChangePassword() {
		$this->template->alert = $this->error;
		$this->template->tempName = 'User:changePassword';
	}

	public function renderDelete() {
		$this->template->users = $this->database->table('users')->where('NOT id', $this->getUser()->getId())->fetchAll();
		$this->template->tempName = 'User:delete';
	}
}