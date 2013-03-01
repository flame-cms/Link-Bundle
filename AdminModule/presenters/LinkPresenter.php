<?php
/**
 * LinkPresenter.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame\CMS\AdminModule
 *
 * @date    15.10.12
 */

namespace Flame\CMS\AdminModule;

class LinkPresenter extends AdminPresenter
{

	/**
	 * @var \Flame\CMS\LinkBundle\Model\Link
	 */
	private $link;

	/**
	 * @autowire
	 * @var \Flame\CMS\LinkBundle\Model\LinkFacade
	 */
	protected $linkFacade;

	/**
	 * @autowire
	 * @var \Flame\CMS\LinkBundle\Forms\ILinkFormFactory
	 */
	protected $linkFormFactory;

	/**
	 * @autowire
	 * @var \Flame\CMS\LinkBundle\Model\LinkManager
	 */
	protected $linkManager;

	public function renderDefault()
	{
		$this->template->links = $this->linkFacade->getLastLinks();
	}

	/**
	 * @param $id
	 */
	public function actionUpdate($id = null)
	{
		$this->link = $this->linkFacade->getOne($id);
		$this->template->link = $this->link;
	}

	/**
	 * @param $id
	 */
	public function handleDelete($id)
	{
		if(!$this->getUser()->isAllowed('Admin:Link', 'delete')){
			$this->flashMessage('Access denied!');
		}else{
			try {
				$this->linkManager->delete($id);
			}catch (\Nette\InvalidArgumentException $ex){
				$this->flashMessage($ex->getMessage());
			}
		}

		$this->redirect('this');
	}

	/**
	 * @return \Flame\CMS\LinkBundle\Forms\LinkForm
	 */
	protected function createComponentLinkForm()
	{
		$default = array();
		if($this->link instanceof \Flame\CMS\LinkBundle\Model\Link)
			$default = $this->link->toArray();

		$form = $this->linkFormFactory->create($default);

		if($this->link){
			$form->onSuccess[] = $this->lazyLink('this');
		}else{
			$form->onSuccess[] = $this->lazyLink('default');
		}

		return $form;
	}
}
