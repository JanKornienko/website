<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Database\Explorer;
use Nette\Application\UI\Form;


final class ProjectsPresenter extends Nette\Application\UI\Presenter
{
	private Explorer $database;

	public function __construct(Explorer $database) {
		$this->database = $database;
	}

	public function renderDefault() {
		$this->template->projects = $this->database->table("projects-table")->fetchAll();
	}

	public function createComponentNewProject() {
		$form = new Form;
		$form->addText("name", "Name")->setRequired();
		$form->addText("link", "Link")->setRequired();
		$form->addText("path", "Path");
		$form->addSubmit("submit", "Add Project");
		$form->onSuccess[] = [$this, "newProjectDbWrite"];
		return $form;
	}

	public function newProjectDbWrite(array $formData) : void {
		$this->database->table("projects-table")->insert([
			"name" => $formData["name"],
			"link" => $formData["link"],
			"path" => $formData["path"]
		]);
	}

	public function renderSingle($id) {
		$this->template->project = $this->database->table("projects-table")->get($id);
	}
}