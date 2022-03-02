<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;

//use Bitrix\Main\Data\Cache;

class EventFilterComponent extends CBitrixComponent
{
	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}

	public function onPrepareComponentParams($arParams)
	{
		$arParams['IBLOCK_EVENTS_ID'] = intval($arParams['IBLOCK_EVENTS_ID']);
		$arParams['IBLOCK_BG_ID'] = intval($arParams['IBLOCK_BG_ID']);
		$arParams['BG_SECTION_ID'] = intval($arParams['BG_SECTION_ID']);

		return $arParams;
	}

	public function executeComponent()
	{
		try {
			$this->checkModules();
			$this->getBackground();
			$this->getSections();
			$this->getRequest();
			$this->includeComponentTemplate();
		} catch (Exception $e) {
			ShowError($e->getMessage());
		}

	}

	protected function checkModules()
	{
		if (!Loader::includeModule('iblock'))
			throw new LoaderException(Loc::getMessage(''));
	}

	protected function getBackground()
	{
		$obElement = new \CIBlockElement();
		$arElement = $obElement->GetList(
			array(
				"RAND" => "ASC",
			),
			array(
				'IBLOCK_ID' => $this->arParams['IBLOCK_BG_ID'],
				'SECTION_ID' => $this->arParams['BG_SECTION_ID'],
				'<=DATE_ACTIVE_FROM' => date('d.m.Y H:i:s'),
				array(
					'LOGIC' => 'OR',
					array(
						'DATE_ACTIVE_TO' => false,
					),
					array(
						'>=DATE_ACTIVE_TO' => date('d.m.Y H:i:s'),
					),
				),
				'ACTIVE' => 'Y'
			),
			false,
			array(
				"nTopCount" => 1,
			),
			array(
				'ID',
				'NAME',
				'DETAIL_PICTURE',
			)
		)->Fetch();

		$this->arResult['BACKGROUND']['ACTIVE'] = false;

		if (0 < $arElement["DETAIL_PICTURE"]) {
			$this->arResult['BACKGROUND']["IMG"] = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);
			$this->arResult['BACKGROUND']['ACTIVE'] = true;
		}
	}

	protected function getSections()
	{
		$this->arResult['SECTION'] = array();

		$obSection = new \CIBlockSection();
		$rsSection = $obSection->GetList(
			array(
				'NAME' => 'ASC',
			),
			array(
				'ACTIVE' => 'Y',
				'IBLOCK_ID' => $this->arParams['IBLOCK_EVENTS_ID'],
			)
		);

		while ($arSection = $rsSection->fetch()) {
			$this->arResult['SECTION'][$arSection['ID']] = $arSection;
		}
	}

	private function getRequest()
	{
		$this->arResult['FILTER'] = array(
			"SECTION" => "",
			"TAGS" => "",
			"DATE" => "",
			"NOW" => "",
		);

		$request = Application::getInstance()->getContext()->getRequest();

		if ($request->getQuery('SECTION')) {
			$this->arResult['FILTER']['SECTION'] = $request->getQuery('SECTION');
		}
		if ($request->getQuery('TAGS')) {
			$this->arResult['FILTER']['KEYWORDS'] = $request->getQuery('TAGS');
		}
		if ($request->getQuery('KEYWORDS')) {
			$this->arResult['FILTER']['KEYWORDS'] = $request->getQuery('KEYWORDS');
		}
		if ($request->getQuery('DATE')) {
			$this->arResult['FILTER']['DATE'] = $request->getQuery('DATE');
		}
		if ($request->getQuery('NOW')) {
			$this->arResult['FILTER']['NOW'] = $request->getQuery('NOW');
		}
	}

}