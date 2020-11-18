<?php
//записываем в куки
function setCookieValue ($code, $value, $flush)
{
	$context = Bitrix\Main\Application::getInstance()->getContext();

	$cookie = new Bitrix\Main\Web\Cookie($code, $value);
	$cookie->setDomain($context->getServer()->getHttpHost());
	$cookie->setHttpOnly(false);

	Bitrix\Main\Application::getInstance()->getContext()->getResponse()->addCookie($cookie);

	$context->getResponse()->addCookie($cookie);

	//если не подключаем header и footer, на пример если работаем через ajax
	if ($flush)
		$context->getResponse()->flush("");
}

//получаем из куки
function getCookieValue ($code)
{
	return Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getCookie($code);
}