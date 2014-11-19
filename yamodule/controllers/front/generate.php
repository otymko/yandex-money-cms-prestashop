<?php

class YamoduleGenerateModuleFrontController extends ModuleFrontController
{
	public $display_header = false;
	public $display_column_left = false;
	public $display_column_right = false;
	public $display_footer = false;
	public $ssl = false;

	public function postProcess()
	{
		parent::postProcess();
		if(Tools::getValue('cron') == 1)
		{
			$this->module->generateXML(true);			
			die('OK');
		}
		else
		{
			$this->module->generateXML(false);
		}
	}
}