<?php

class YNSOCIALSTREAM_CLASS_Core
{
	function checkSocialBridgePlugin($provider)
	{
		if (!$plugin = BOL_PluginService::getInstance() -> findPluginByKey('ynsocialbridge'))
		{
			return false;
		}
		else
		{
			if (!$plugin->isActive() || !YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($provider))
			{
				return false;
			}
		}
		return true;
	}
}