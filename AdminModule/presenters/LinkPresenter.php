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
	 * @var \Flame\CMS\LinkBundle\Models\Links\Link
	 */
	private $link;

	/**
	 * @var \Flame\CMS\LinkBundle\Models\Links\LinkFacade $linkFacade
	 */
	private $linkFacade;

	/** @var \Flame\CMS\LinkBundle\Forms\Links\ILinkFormFactory */
	private $linkFormFactory;

	/**
	 * @param \Flame\CMS\LinkBundle\Forms\Links\ILinkFormFactory $linkFormFactory
	 */
	public function injectLinkFormFactory(\Flame\CMS\LinkBundle\Forms\Links\ILinkFormFactory $linkFormFactory)
	{
		$this->linkFormFactory = $linkFormFactory;
	}

	/**
	 * @param \Flame\CMS\LinkBundle\Models\Links\LinkFacade $linkFacade
	 */
	public function injectLinkFacade(\Flame\CMS\LinkBundle\Models\Links\LinkFacade $linkFacade)
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
			if(!$link = $this->linkFacade->getOne($id)){
				$this->flashMessage('Link does not exist');
			}else{
				$this->linkFacade->delete($link);
			}
		}

		if($this->isAjax()){
			$this->invalidateControl();
		}else{
			$this->redirect('default');
		}
	}

	/**
	 * @return \Flame\CMS\LinkBundle\Forms\Links\LinkForm
	 */
	protected function createComponentLinkForm()
	{
		$default = array();
		if($this->link instanceof \Flame\CMS\LinkBundle\Models\Links\Link)
			$default = $this->link->toArray();

		$form = $this->linkFormFactory->create($default);

		if($this->link){
			$form->onSuccess[] = $this->lazyLink('default');
		}else{
			$form->onSuccess[] = $this->lazyLink('this');
		}

		return $form;
	}
}
