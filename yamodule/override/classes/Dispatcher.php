<?php

class Dispatcher extends DispatcherCore
{
	protected function setRequestUri()
	{
		parent::setRequestUri();
		if (Module::isInstalled('yamodule') && strpos($this->request_uri, 'module/yamodule/'))
			$this->request_uri = iconv('windows-1251', 'UTF-8', $this->request_uri);
	}
}