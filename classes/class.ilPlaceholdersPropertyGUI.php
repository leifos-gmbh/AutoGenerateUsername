<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 *
 * @author Jan Posselt <jposselt@databay.de>
 *
 */
class ilPlaceholdersPropertyGUI extends ilFormPropertyGUI
{

	/**
	 * @var array
	 */
	protected $item = array();

	/**
	 * @var string
	 */
	protected $textfield_id = "";

	/**
	 * @var
	 */
	protected $placeholder_advice;

	/**
	 * @var ilAutoGenerateUsernamePlugin
	 */
	protected $plugin;

	/**
	 * ilPlaceholdersPropertyGUI constructor.
	 */
	public function __construct()
	{
		$this->plugin = ilPlugin::getPluginObject(IL_COMP_SERVICE, "EventHandling", "evhk", "AutoGenerateUsername");
		parent::__construct('');
	}

	/**
	 * @param $a_title
	 * @param $a_text
	 */
	public function addPlaceholder($a_title, $a_text)
	{
		$this->item[] = array(
			"title" => $a_title,
			"text" => $a_text,
			"section" => false
		);
	}

	/**
	 * @param $a_title
	 */
	public function addSection($a_title)
	{
		$this->item[] = array(
			"title" => $a_title,
			"section" => true
		);
	}

	/**
	 * @param string $textfield_id
	 */
	public function setTextfieldId($textfield_id)
	{
		$this->textfield_id = $textfield_id;
	}

	/**
	 * @return string
	 */
	public function getTextfieldId()
	{
		return $this->textfield_id;
	}

	/**
	 * @param string $placeholder_advice
	 */
	public function setPlaceholderAdvice($placeholder_advice)
	{
		$this->placeholder_advice = $placeholder_advice;
	}

	/**
	 * @return string
	 */
	public function getPlaceholderAdvice()
	{
		return $this->placeholder_advice;
	}

	/**
	 * @param ilTemplate
	 */
	public function insert($a_tpl)
	{
		global $DIC;

		$lng = $DIC->language();
		$tpl = $DIC->ui()->mainTemplate();

		$tpl->addJavaScript($this->plugin->getDirectory() . "/js/ilAutoGenerateUsername.js");
		$subtpl = $this->plugin->getTemplate("tpl.placeholders.html");

		if($this->getPlaceholderAdvice())
		{
			$subtpl->setCurrentBlock("advice");
			$subtpl->setVariable("TXT_PLACEHOLDERS_ADVISE", $this->getPlaceholderAdvice());
			$subtpl->parseCurrentBlock();
		}


		foreach($this->item as $data)
		{
			if($data['section'])
			{
				$subtpl->setCurrentBlock("section");
				$subtpl->setVariable("TXT_SECTION_TITLE", $data['title']);
				$subtpl->parseCurrentBlock();
			}
			else
			{
				$subtpl->setCurrentBlock("placeholder");
				$subtpl->setVariable("TEXTFILED_ID", $this->getTextfieldId());
				$subtpl->setVariable("PLACEHOLDER_TXT", $data['text']);
				$subtpl->setVariable("PLACEHOLDER_TITLE", $data['title']);
				$subtpl->parseCurrentBlock();
			}
		}

		
		$a_tpl->setCurrentBlock("prop_generic");
		$a_tpl->setVariable("PROP_GENERIC", $subtpl->get());
		$a_tpl->parseCurrentBlock();	
	}

	/**
	 * @return bool
	 */
	public function setValueByArray()
	{
		return true;
	}

	/**
	 * Check input, strip slashes etc. set alert, if input is not ok.
	 *
	 * @return	boolean		Input ok, true/false
	 */
	public function checkInput()
	{
		return true;
	}
}

?>
