<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use WF\Project\User;

//use Bitrix\Main\Data\Cache;

class EventEditComponent extends CBitrixComponent
{
	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}

	public function executeComponent()
	{
		try {
			$this->checkModules();
			$this->checkAuthorize();
			$this->getResult();
			$this->getSection();
			$this->includeComponentTemplate();
		} catch (Exception $e) {
			ShowError($e->getMessage());
		}

	}

	protected function checkModules()
	{
	}

	public function checkAuthorize()
	{
		global $APPLICATION, $USER;

		if (!$USER->IsAuthorized()) {
			LocalRedirect('/login/?redirect=' . $APPLICATION->GetCurPage());
		}
	}

	protected function getResult()
	{
		global $APPLICATION, $USER;

		$this->arResult = array(
			'USER' => array(
				'AGE' => User::getAge(),
			),
			'ITEM' => array(
				'ID' => 0,
				'NAME' => '',
				'ADDRESS' => '',
				'DATE' => array(
					'START' => '',
					'END' => '',
				),
				'TIME' => array(
					'START' => '',
					'END' => '',
				),
				'SECTION_ID' => 0,
				'DETAIL_TEXT' => '',
				'DETAIL_PICTURE' => '',
				'PREVIEW_PICTURE' => '',
				'IMG_ORIGINAL' => '',
				'IMG_BIG_CROP' => '',
				'IMG_SMALL_CROP' => '',
				'AVARAGE_BILL' => '',
				'LIMIT_MEMBERS' => '',
				'FOR_18_PLUS' => 'N',
				'PRIVATE' => 'N',
				'TAGS' => '',
			),
		);

		if (intval($this->arParams['EVENT_CODE']) > 0) {

			$obElement = new \CIBlockElement();
			$rsElement = $obElement->GetList(
				array(),
				array(
					'IBLOCK_ID' => $this->arParams['IBLOCK_EVENTS_ID'],
					'ID' => intval($this->arParams['EVENT_CODE']),
				),
				false,
				array(
					'nTopCount' => 1,
				),
				array(
					'ID',
					'NAME',
					'CODE',
					'CREATED_BY',
					'IBLOCK_SECTION_ID',
					'DETAIL_TEXT',
					'DETAIL_PICTURE',
					'PREVIEW_PICTURE',
					'PROPERTY_IMG_ORIGINAL',
					'PROPERTY_IMG_BIG_CROP',
					'PROPERTY_IMG_SMALL_CROP',
					'PROPERTY_ADDRESS',
					'PROPERTY_AVARAGE_BILL',
					'PROPERTY_LIMIT_MEMBERS',
					'PROPERTY_DATETIME_START',
					'PROPERTY_DATETIME_END',
					'PROPERTY_FOR_18_PLUS',
					'PROPERTY_PRIVATE',
					'PROPERTY_HIDDEN',
					'PROPERTY_TAGS_ID',
				)
			);

			if ($arElement = $rsElement->Fetch()) {
				$this->arResult['ITEM'] = array(
					'ID' => $arElement['ID'],
					'NAME' => $arElement['NAME'],
					'ADDRESS' => $arElement['PROPERTY_ADDRESS_VALUE'],
					'DATE' => array(
						'START' => date('d.m.Y', strtotime($arElement['PROPERTY_DATETIME_START_VALUE'])),
						'END' => date('d.m.Y', strtotime($arElement['PROPERTY_DATETIME_END_VALUE'])),
					),
					'TIME' => array(
						'START' => date('H:i', strtotime($arElement['PROPERTY_DATETIME_START_VALUE'])),
						'END' => date('H:i', strtotime($arElement['PROPERTY_DATETIME_END_VALUE'])),
					),
					'SECTION_ID' => $arElement['IBLOCK_SECTION_ID'],
					'DETAIL_PICTURE' => (intval($arElement['DETAIL_PICTURE']) > 0) ? CFile::GetPath($arElement['DETAIL_PICTURE']) : '',
					'PREVIEW_PICTURE' => (intval($arElement['PREVIEW_PICTURE']) > 0) ? CFile::ResizeImageGet($arElement['PREVIEW_PICTURE'], array('width' => 85, 'height' => 85), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'] : '',
					'IMG_ORIGINAL' => (intval($arElement['PROPERTY_IMG_ORIGINAL_VALUE']) > 0) ? CFile::GetPath($arElement['PROPERTY_IMG_ORIGINAL_VALUE']) : '',
					'IMG_BIG_CROP' => $arElement['PROPERTY_IMG_BIG_CROP_VALUE'],
					'IMG_SMALL_CROP' => $arElement['PROPERTY_IMG_SMALL_CROP_VALUE'],
					'DETAIL_TEXT' => str_replace('<br />', ' ', $arElement['DETAIL_TEXT']),
					'AVARAGE_BILL' => $arElement['PROPERTY_AVARAGE_BILL_VALUE'],
					'LIMIT_MEMBERS' => $arElement['PROPERTY_LIMIT_MEMBERS_VALUE'],
					'FOR_18_PLUS' => ($arElement['PROPERTY_FOR_18_PLUS_VALUE'] == 'Да' ? 'Y' : 'N'),
					'PRIVATE' => ($arElement['PROPERTY_PRIVATE_VALUE'] == 'Да' ? 'Y' : 'N'),
					'TAGS' => '',
				);

				if (is_array($arElement['PROPERTY_TAGS_ID_VALUE'])
					&& count($arElement['PROPERTY_TAGS_ID_VALUE']) > 0) {
					$this->arResult['ITEM']['TAGS'] = $this->getTags($arElement['PROPERTY_TAGS_ID_VALUE']);
				}

				if ($arElement['CREATED_BY'] != $USER->GetID()) {
					LocalRedirect('/event/edit/new/');
				}
			} else {
				LocalRedirect('/event/edit/new/');
			}
		}

		$pathFolderTmp = "/upload/wf/tmp/" . intval($USER->GetID()) . "/";

		if (intval($this->arParams['EVENT_CODE']) > 0) {
			$pathFolderTmp = $pathFolderTmp . $this->arParams['EVENT_CODE'] . '/';

			CheckDirPath($_SERVER['DOCUMENT_ROOT'] . $pathFolderTmp);
			DeleteDirFilesEx($pathFolderTmp);

			$APPLICATION->SetPageProperty("title", 'Редактирование мероприятия / Мероприятия / wannafriends.com');
			$APPLICATION->SetTitle("Мероприятия / Редактирование мероприятия");
		} else {
			CheckDirPath($_SERVER['DOCUMENT_ROOT'] . $pathFolderTmp);
			DeleteDirFilesEx($pathFolderTmp);

			$APPLICATION->SetPageProperty("title", 'Создание мероприятия / Мероприятия / wannafriends.com');
			$APPLICATION->SetTitle("Мероприятия / Создание мероприятия");
		}
	}

	protected function getTags($arID)
	{
		$strTags = "";
		$obElement = new \CIBlockElement();
		$rsElement = $obElement->GetList(
			array(),
			array('
				IBLOCK_ID' => $this->arParams['IBLOCK_TAGS_ID'],
				'ID' => $arID
			)
		);
		$arTags = array();
		while ($arElement = $rsElement->Fetch()) {
			$arTags[] = $arElement['NAME'];
		}
		if (count($arTags) > 0) {
			$strTags = implode(",", $arTags);
		}

		return $strTags;
	}

	protected function getSection()
	{
		$obSection = new \CIBlockSection();
		$rsSection = $obSection->GetList(
			array('NAME' => 'ASC'),
			array(
				'ACTIVE' => 'Y',
				'IBLOCK_ID' => $this->arParams['IBLOCK_EVENTS_ID'],
			)
		);

		while ($arSection = $rsSection->Fetch()) {
			$this->arResult['SECTION'][] = $arSection;
		}
	}

}