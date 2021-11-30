<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Database\Explorer;


final class ProjectsPresenter extends Nette\Application\UI\Presenter
{
	private Explorer $database;

	public function __construct(Explorer $database) {
		$this->database = $database;
	}
}