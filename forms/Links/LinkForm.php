<?php
/**
 * LinkForm.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame\CMS\AdminModule
 *
 * @date    15.10.12
 */

namespace Flame\CMS\LinkBundle\Forms\Links;

class LinkForm extends \Flame\Application\UI\Form
{

	/** @var \Flame\CMS\LinkBundle\Models\Links\LinkManager */
	private $linkManager;

	/**
	 * @param \Flame\CMS\LinkBundle\Models\Links\LinkManager $linkManager
	 */
	public function injectLinkManager(\Flame\CMS\LinkBundle\Models\Links\LinkManager $linkManager)
	{
		$this->linkManager = $linkManager;
	}

	/**
	 * @param array $default
	 */
	public function __construct(array $default = array())
	{
		parent::__construct();

		$this->setRenderer(new \Kdyby\BootstrapFormRenderer\BootstrapRenderer);

		$this->configure();

		if(count($default)){
			$this->setDefaults($default);
			$this->addSubmit('send', 'Edit')
				->setAttribute('class', 'btn-primary');
		}else{
			$this->setDefaults(array('public' => true));
			$this->addSubmit('send', 'Add')
				->setAttribute('class', 'btn-primary');
		}

		$this->onSuccess[] = $this->formSubmitted;
	}

	/**
	 * @param LinkForm $form
	 */
	public function formSubmitted(LinkForm $form)
	{
		try {
			$this->linkManager->update($form->getValues());
			$form->presenter->flashMessage('Link management was successful.', 'succes');
		}catch (\Nette\InvalidArgumentException $ex){
			$form->addError($ex->getMessage());
		}

	}

	private function configure()
	{
		$this->addText('name', 'Title')
			->addRule(self::MAX_LENGTH, null, 255)
			->setRequired();

		$this->addText('url', 'URL')
			->addRule(self::MAX_LENGTH, null, 255)
			->setRequired();

		$this->addTextArea('description', 'Description')
			->addRule(self::MAX_LENGTH, null, 500);

		$this->addCheckbox('public', 'Public link?');
	}

}
