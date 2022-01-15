<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Database\Explorer;

final class HomepagePresenter extends Nette\Application\UI\Presenter {
	private Explorer $database;

	public function __construct(Explorer $database) {
		$this->database = $database;
	}

	public function renderDefault() {
		$this->template->skills = $this->database->table('skills')->fetchAll();
	}
}