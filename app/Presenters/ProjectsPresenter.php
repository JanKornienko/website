<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Database\Explorer;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;

final class ProjectsPresenter extends Nette\Application\UI\Presenter {
	private Explorer $database;

	public function __construct(Explorer $database) {
		$this->database = $database;
	}

	public function renderDefault() {
		$this->template->projects = $this->database->table('projects')->fetchAll();
		$this->template->tempName = 'Projects:';
	}

	public function createComponentNewProject() {
		$form = new Form;
		$form->addText('name', 'Name')->setRequired('Name is required');
		$form->addText('link', 'Link')->setRequired('Link is required');
		$form->addTextArea('description', 'Description');
		$form->addMultiUpload('images', 'Images')->addRule($form::IMAGE, 'Select images');
		$form->addSubmit('submit', 'Add Project');

		$form->onSuccess[] = [$this, 'newProjectSuccess'];

		return $form;
	}

	public function newProjectSuccess(array $projectFormData) : void {
		$path = '/files/projects/' . $projectFormData['name'] . '/';
		FileSystem::createDir('.' . $path);
		foreach ($projectFormData['images'] as $file) {
			$name = '.' . $path . 'IMG_' . $file->name . '_' . uniqid();
			$file->move($name);
		}
		$this->database->table('projects')->insert([
			'name' => $projectFormData['name'],
			'link' => $projectFormData['link'],
			'description' => $projectFormData['description'],
			'path' => $path
		]);
		$this->redirect('Projects:');
	}

	public function actionDeleteProject($id) {
		$project = $this->database->table('projects')->get($id);
		FileSystem::delete('.' . $project->path);
		$this->database->table('projects')->where('id', $id)->delete();
		$this->redirect('Projects:');
	}

	public function renderDetail($id) {
		$this->template->project = $this->database->table('projects')->get($id);
		$this->template->images = array_diff(scandir('.' . $this->template->project->path), array('.', '..'));
		$this->template->tempName = 'Projects:detail';
	}

	public function renderNew() {
		$this->template->tempName = 'Projects:new';
	}
}