<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Database\Explorer;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;

final class HomepagePresenter extends Nette\Application\UI\Presenter {
	private Explorer $database;

	public function __construct(Explorer $database) {
		$this->database = $database;
	}

	public function createComponentNewSkill() {
		$form = new Form;
		$form->addText('name', 'Name')->setRequired('Add name');
		$form->addInteger('percentage', 'Percentage')->addRule($form::RANGE, 'Number of % must be in range %d-%d', [0, 100])->setRequired('Add % of skill!');
		$form->addUpload('image', 'Image')->addRule($form::MIME_TYPE, 'Select .svg file!', 'image/svg+xml')->setRequired('Add logo of Skill!');
		$form->addSubmit('submit', 'Add Skill');

		$form->onSuccess[] = [$this, 'skillFormSuccess'];

		return $form;
	}

	public function skillFormSuccess(array $skillFormData) : void {
		$name = './files/skills/' . $skillFormData['name'] . '.svg';
		$skillFormData['image']->move($name);
		$this->database->table('skills')->insert([
			'name' => $skillFormData['name'],
			'percentage' => $skillFormData['percentage']
		]);
		$this->redirect('Homepage:#skills');
	}

	public function actionSkillDelete($id) {
		$skill = $this->database->table('skills')->get($id);
		FileSystem::delete('./files/skills/' . $skill->name . '.svg');
		$this->database->table('skills')->where('id', $id)->delete();
		$this->redirect('Homepage:#skills');
	}

	public function renderDefault() {
		$this->template->skills = $this->database->table('skills')->fetchAll();
		$this->template->tempName = 'Homepage:';
	}
}