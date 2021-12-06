<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Database\Explorer;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;


final class ProjectsPresenter extends Nette\Application\UI\Presenter
{
	private Explorer $database;

	public function __construct(Explorer $database) {
		$this->database = $database;
	}

	public function renderDefault() {
		$this->template->projects = $this->database->table('projects')->fetchAll();
	}

	public function createComponentNewProject() {
		$form = new Form;
		$form->addText('name', 'Name')->setRequired('Add name');
		$form->addText('link', 'Link')->setRequired('Add link');
		$form->addMultiUpload('images', 'Images')->addRule($form::IMAGE, 'Select images');
		$form->addUpload('readme', 'Readme')->addRule($form::IMAGE, 'Select markdown');
		$form->addSubmit('submit', 'Add Project');
		$form->onSuccess[] = [$this, 'formDbWrite'];
		return $form;
	}

	public function formDbWrite(array $formData) : void {
		$path = './projects/' . $formData['name'];
		FileSystem::createDir($path);
		$this->database->table('projects')->insert([
			'name' => $formData['name'],
			'link' => $formData['link'],
			'path' => $path
		]);
		$this->redirect('Projects:');
	}

	public function renderSingle($id) {
		$this->template->project = $this->database->table('projects')->get($id);
	}
}