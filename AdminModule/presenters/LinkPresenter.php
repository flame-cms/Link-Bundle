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
	 * @var \Flame\CMS\LinkBundle\Model\LinkFacade $linkFacade
	 */
	private $linkFacade;

	/** @var \Flame\CMS\LinkBundle\Forms\ILinkFormFactory */
	private $linkFormFactory;

	/**
	 * @autowire
	 * @var \Flame\CMS\LinkBundle\Model\LinkManager
	 */
	protected $linkManager;

	/**
	 * @param \Flame\CMS\LinkBundle\Forms\ILinkFormFactory $linkFormFactory
	 */
	public function injectLinkFormFactory(\Flame\CMS\LinkBundle\Forms\ILinkFormFactory $linkFormFactory)
	{
		$this->linkFormFactory = $linkFormFactory;
	}

	/**
	 * @param \Flame\CMS\LinkBundle\Model\LinkFacade $linkFacade
	 */
	public function injectLinkFacade(\Flame\CMS\LinkBundle\Model\LinkFacade $linkFacade)
	{
		$this->linkFacade = $linkFacade;
	}

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

		if($this->isAjax()){
			$this->invalidateControl();
		}else{
			$this->redirect('default');
		}
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
