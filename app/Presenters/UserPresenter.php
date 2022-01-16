<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Database\Explorer;
use App\Model\Authenticator;
use Nette\Application\UI\Form;

final class UserPresenter extends Nette\Application\UI\Presenter {
	private Explorer $database;
	private Authenticator $auth;

	public function __construct(Explorer $database, Authenticator $auth) {
		$this->database = $database;
		$this->auth = $auth;
	}

	public function createComponentSignupForm() {
		$form = new Form;
		$form->addText('username', 'Username:')->setRequired();
		$form->addPassword('password', 'Password:')->setRequired();
		$form->addSubmit('submit', 'Sign up');

		$form->onSuccess[] = [$this, 'formSignupSuccess'];

		return $form;
	}

	public function formSignupSuccess(array $values) : void {
		$this->auth->register(strtolower($values['username']), $values['password']);
	}

	public function createComponentLoginForm() {
		$form = new Form;
		$form->addText('username', 'Username:')->setRequired();
		$form->addPassword('password', 'Password:')->setRequired();
		$form->addSubmit('submit', 'Log In');

		$form->onSuccess[] = [$this, 'formLoginSuccess'];

		return $form;
	}

	public function formLoginSuccess(array $values) : void {
		try {
			$this->getUser()->login($values["username"], $values["password"]);
		} catch(\Exception $e) {
			$this->flashMessage($e->getMessage());
		}
	}

	public function actionOut() {
		$this->getUser()->logout(true);
		$this->redirect('Homepage:');
	}

	public function renderDefault() {
		$this->template->tempName = 'User:';
	}

	public function renderRegister() {
		$this->template->tempName = 'User:register';
	}
}