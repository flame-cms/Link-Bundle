<?php
/**
 * LinkFormFactory.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame\CMS\AdminModule
 *
 * @date    15.10.12
 */

namespace Flame\CMS\LinkBundle\Forms\Links;

class LinkFormFactory extends \Nette\Object
{

	/**
	 * @var LinkForm
	 */
	protected $form;

	/**
	 * @var \Flame\CMS\LinkBundle\Models\Links\Link
	 */
	private $link;

	/**
	 * @var \Flame\CMS\LinkBundle\Models\Links\LinkFacade $linkFacade
	 */
	private $linkFacade;

	/**
	 * @param \Flame\CMS\LinkBundle\Models\Links\LinkFacade $linkFacade
	 */
	public function injectLinkFacade(\Flame\CMS\LinkBundle\Models\Links\LinkFacade $linkFacade)
	{
		$this->linkFacade = $linkFacade;
	}

	/**
	 * @param $link
	 * @return LinkFormFactory
	 */
	public function configure($link)
	{
		$this->link = $link;

		$this->form = new LinkForm();
		if($this->link){
			$this->form->configureEdit($this->link ? $this->link->toArray() : array());
		}else{
			$this->form->configureAdd();
		}
		$this->form->onSuccess[] = $this->formSubmitted;
		return $this;
	}

	/**
	 * @param LinkForm $form
	 */
	public function formSubmitted(LinkForm $form)
	{
		$values = $form->getValues();

		if($this->link){
			$this->link->setName($values->name)
				->setDescription($values->description)
				->setUrl($this->treatUrl($values->url))
				->setPublic($values->public);
			$this->linkFacade->save($this->link);
			$form->presenter->flashMessage('Link was edited!', 'success');
		}else{
			$link = new \Flame\CMS\LinkBundle\Models\Links\Link($values->name, $this->treatUrl($values->url));
			$link->setDescription($values->description)
				->setPublic($values->public);
			$this->linkFacade->save($link);
			$form->presenter->flashMessage('Link was added!', 'success');
		}
	}

	/**
	 * @param $url
	 * @return string
	 */
	protected function treatUrl($url)
	{
		if(substr($url, 0, 4) !== 'http'){
			$url = 'http://' . $url;
		}

		return $url;
	}

}
