<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

//для задания 1
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', ['SaleEvents', 'OnSaleOrderSavedHandler']);

//для задания 2.1
$eventManager->addEventHandler('catalog', 'OnSuccessCatalogImport1C', ['CatalogEvents', 'OnSuccessCatalogImport1CHandler']);

//для задания 2.2
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockSectionUpdate', ['IblockEvents', 'OnBeforeIBlockSectionUpdateHandler']);

class SaleEvents
{
	public function OnSaleOrderSavedHandler (\Bitrix\Main\Event $event)
	{
		$order = $event->getParameter("ENTITY");

		//если новый заказ, т.е. создаваемый покупателем
		if ($event->getParameter("IS_NEW")) {
			$arrNewValues = [
				'UTM_SOURCE' => getCookieValue('utm_source')
			];

			//цикл по свойствам заказа и поиск нужных
			foreach ($order->getPropertyCollection() as $property) {
				$propCode = $property->getField('CODE');

				if (key_exists($propCode, $arrNewValues)) {
					$property->setValue($arrNewValues[$propCode]);
				}
			}

			//записываем нужные новые значения. Даже если запись не прошла, то ошибку пользователю не показываем, т.к. заказ в любом случаи должен быть создан
			$order->getPropertyCollection()->save();
		}
	}
}

class IblockEvents
{
	public function OnBeforeIBlockSectionUpdateHandler(&$arFields)
	{
		$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

		//проверка на то, что это выгрузка из 1С
		if ($request->get('type') == 'catalog') {
			//убираем из списка название и код
			unset($arFields['NAME']);
			unset($arFields['CODE']);
		}
	}
}

class CatalogEvents
{
	public function OnSuccessCatalogImport1CHandler ()
	{
		//тип почтового события не создавал не создавал, письмо смотрел в таблице b_event
		\Bitrix\Main\Mail\Event::send([
			"EVENT_NAME" => "TEST_EVENT_NAME",
			"LID" => SITE_ID,
			"C_FIELDS" => [
				"EMAIL" => 'useremail@test.ru',
				"DATE_END" => date('d.m.Y H:i:s'),//к примеру дата завершения
			],
		]);
	}
}