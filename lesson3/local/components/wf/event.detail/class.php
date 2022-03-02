<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Iblock\Component\Tools,
	Bitrix\Main\Loader,
	Bitrix\Main\LoaderException,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\UserTable;
use WF\Project\User,
	WF\Project\Models;

class EventDetailComponent extends CBitrixComponent
{
	CONST PICTURE_DEFAULT = SITE_TEMPLATE_PATH . "/assets/img/default/default_1238_444.jpg";

	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}

	protected function checkModules()
	{
		if (!Loader::includeModule('iblock'))
			throw new LoaderException(Loc::getMessage(''));
		if (!Loader::includeModule('wf.project'))
			throw new LoaderException(Loc::getMessage(''));
	}

	public function getSection($sectionId)
	{
		$obSection = new \CIBlockSection();

		$rsSection = $obSection->GetList(
			array(),
			array(
				'ACTIVE' => 'Y',
				'IBLOCK_ID' => $this->arParams['IBLOCK_EVENTS_ID'],
				'ID' => $sectionId,
			));
		if ($arSection = $rsSection->Fetch()) {
			return $arSection;
		}

		return false;
	}

	public function getTags($tagsId)
	{
		$obElement = new \CIBlockElement();

		$arTags = array();

		$rsElement = $obElement->GetList(
			array(),
			array(
				'IBLOCK_ID' => $this->arParams['IBLOCK_TAGS_ID'],
				'ID' => $tagsId,
			),
			false,
			false,
			array(
				'ID',
				'NAME',
			)
		);
		while ($arElement = $rsElement->Fetch()) {
			$arTags[$arElement['ID']] = $arElement['NAME'];
		}

		return $arTags;
	}

	public function getResult()
	{
		global $APPLICATION, $USER;

		$obElement = new \CIBlockElement();

		$rsElement = $obElement->GetList(
			array(
				'PROPERTY_DATETIME_START' => 'DESC'
			),
			array(
				'IBLOCK_ID' => $this->arParams['IBLOCK_EVENTS_ID'],
				'ID' => $this->arParams['ITEM_ID'],
				'ACTIVE' => 'Y',
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
				'PROPERTY_ADDRESS',
				'PROPERTY_AVARAGE_BILL',
				'PROPERTY_LIMIT_MEMBERS',
				'PROPERTY_DATETIME_START',
				'PROPERTY_DATETIME_END',
				'PROPERTY_FOR_18_PLUS',
				'PROPERTY_PRIVATE',
				'PROPERTY_HIDDEN',
				'PROPERTY_BLOCKED',
				'PROPERTY_BLOCKED_DESCRIPTION',
				'PROPERTY_TAGS_ID',
				'PROPERTY_BLOCKED_USERS',
			)
		);

		if ($arElement = $rsElement->Fetch()) {
			if ($arElement['PROPERTY_BLOCKED_VALUE'] == "Да") {
				$this->arResult['DESCRIPTION'] = $arElement['PROPERTY_BLOCKED_DESCRIPTION_VALUE'];
				$APPLICATION->SetPageProperty("title", 'Заблокированное мероприятие');
				$this->includeComponentTemplate('disable');
			} else {
				if ($arElement['PROPERTY_FOR_18_PLUS_VALUE'] == "Да" && !$USER->IsAuthorized()) {
					LocalRedirect('/login/');
				}
				if ($arElement['PROPERTY_FOR_18_PLUS_VALUE'] == "Да" && User::getAge() < 18) {
					$this->arResult['DESCRIPTION'] = "Мероприятие 18+, вы не можете принять в нем участие в соответствии с законом РФ.";
					$APPLICATION->SetPageProperty("title", 'Мероприятие 18+');
					$this->includeComponentTemplate('disable');
				} else {
					$arSection = $this->getSection($arElement['IBLOCK_SECTION_ID']);

					$arElement['TAGS'] = array();
					$arElement['INVITE'] = false;
					$arElement['BID'] = false;
					$arElement['USER'] = false;
					$arElement['DATETIME'] = "";
					$arElement['SECTION'] = $arSection['NAME'];

					if (count($arElement['PROPERTY_TAGS_ID_VALUE']) > 0) {
						$arElement['TAGS'] = $this->getTags($arElement['PROPERTY_TAGS_ID_VALUE']);
					}

					$datetime1 = strtotime($arElement['PROPERTY_DATETIME_START_VALUE']);
					$datetime2 = strtotime($arElement['PROPERTY_DATETIME_END_VALUE']);

					if (date('d.m.Y H:i', $datetime1) == date('d.m.Y H:i', $datetime2)) {
						$arElement['DATETIME'] = date('d.m.Y H:i', $datetime1);
					} else {
						if (date('d.m.Y', $datetime1) == date('d.m.Y', $datetime2)) {
							$arElement['DATETIME'] = date('d.m.Y H:i', $datetime1) . " - " . date('H:i', $datetime2);
						} else {
							$arElement['DATETIME'] = date('d.m.Y H:i', $datetime1) . " - " . date('d.m.Y H:i', $datetime2);
						}
					}

					if (intval($arElement['DETAIL_PICTURE']) > 0) {
						$picture = CFile::ResizeImageGet($arElement['DETAIL_PICTURE'], array('width' => 1238, 'height' => 444), BX_RESIZE_IMAGE_EXACT);
					} elseif (intval($arSection['PICTURE']) > 0) {
						$picture = CFile::ResizeImageGet($arSection['PICTURE'], array('width' => 1238, 'height' => 444), BX_RESIZE_IMAGE_EXACT);
					} else {
						$picture['src'] = self::PICTURE_DEFAULT;
					}

					if ($USER->IsAuthorized()) {
						$obInvite = new Models\EventInviteListTable();
						$rsInvite = $obInvite->getList(array(
							'select' => array('ID'),
							'filter' => array(
								'USER_ID' => intval($USER->GetID()),
								'EVENT_ID' => $arElement['ID'],
							)
						));
						if ($arInvite = $rsInvite->fetch()) {
							$arElement['INVITE'] = true;
						}

						$obBid = new Models\EventBidListTable();
						$rsBid = $obBid->getList(array(
							'select' => array('ID'),
							'filter' => array(
								'USER_ID' => intval($USER->GetID()),
								'EVENT_ID' => $arElement['ID'],
							)
						));
						if ($arBid = $rsBid->fetch()) {
							$arElement['BID'] = true;
						}

						$obEventUser = new Models\EventUserListTable();
						$rsEventUser = $obEventUser->getList(array(
							'select' => array('ID'),
							'filter' => array(
								'USER_ID' => intval($USER->GetID()),
								'EVENT_ID' => $arElement['ID'],
								'IS_AUTHOR' => 0,
							)
						));
						if ($arEventUser = $rsEventUser->fetch()) {
							$arElement['USER'] = true;
						}
					}

					$obUser = new UserTable();
					$rsUser = $obUser->getList(array(
						'filter' => array(
							'ID' => $arElement['CREATED_BY']
						)
					));
					if ($arUser = $rsUser->fetch()) {
						$arElement['AUTHOR'] = $arUser;
					}

					$privateHidden = 'N';
					if ($arElement['PROPERTY_PRIVATE_VALUE'] == 'Да') {
						$privateHidden = 'Y';
						if ($USER->IsAuthorized()) {
							if ($arElement['CREATED_BY'] == $USER->GetID()) {
								$privateHidden = 'N';
							} elseif ($arElement['USER']) {
								$privateHidden = 'N';
							}
						}
					}

					$this->arResult = array(
						'ID' => $arElement['ID'],
						'NAME' => $arElement['NAME'],
						'CODE' => $arElement['CODE'],
						'DETAIL_TEXT' => $arElement['DETAIL_TEXT'],
						'ADDRESS' => $arElement['PROPERTY_ADDRESS_VALUE'],
						'LIMIT_MEMBERS' => $arElement['PROPERTY_LIMIT_MEMBERS_VALUE'],
						'AVARAGE_BILL' => $arElement['PROPERTY_AVARAGE_BILL_VALUE'],
						'DATETIME' => $arElement['DATETIME'],
						'FOR_18_PLUS' => $arElement['PROPERTY_FOR_18_PLUS_VALUE'],
						'PRIVATE' => $arElement['PROPERTY_PRIVATE_VALUE'],
						'HIDDEN' => $arElement['PROPERTY_HIDDEN_VALUE'],
						'PICTURE' => $picture,
						'TAGS' => $arElement['TAGS'],
						'BLOCKED_USERS' => $arElement['PROPERTY_BLOCKED_USERS_VALUE'],
						'AUTHOR' => $arElement['AUTHOR'],
						'USER' => $arElement['USER'],
						'BID' => $arElement['BID'],
						'INVITE' => $arElement['INVITE'],
						'SECTION' => $arElement['SECTION'],
						'PRIVATE_HIDDEN' => $privateHidden,
					);

					$str = '';
					$str .= $arElement['DATETIME'] . PHP_EOL;
					$str .= $arElement['PROPERTY_ADDRESS_VALUE'] . PHP_EOL;
					if (strlen($arElement['DETAIL_TEXT']) > 100) {
						$str .= substr($arElement['DETAIL_TEXT'], 0, 100) . "..." . PHP_EOL;
					} else {
						$str .= $arElement['DETAIL_TEXT'] . PHP_EOL;
					}
					$str = trim($str);

					$APPLICATION->SetPageProperty("title", $arElement['NAME']);
					$APPLICATION->SetPageProperty("keywords", $arElement['NAME']);
					$APPLICATION->SetPageProperty("description", $str);
					$APPLICATION->SetPageProperty("twitter:site", "@wannafriends");
					$APPLICATION->SetPageProperty("twitter:creator", "@wannafriends");
					$APPLICATION->SetPageProperty("twitter:title", $arElement['NAME'] . ", " . $arElement['PROPERTY_ADDRESS_VALUE'] . ", " . $arElement['DATETIME']);
					//$APPLICATION->SetPageProperty("twitter:description", $str);
					$APPLICATION->SetPageProperty("og:site_name", "wannafriends.com");
					$APPLICATION->SetPageProperty("og:title", $arElement['NAME'] . ", " . $arElement['PROPERTY_ADDRESS_VALUE'] . ", " . $arElement['DATETIME']);
					//$APPLICATION->SetPageProperty("og:description", $str);
					$APPLICATION->SetPageProperty("og:locale", "ru_RU");
					$APPLICATION->SetPageProperty("og:image", 'https://' . preg_replace('/(:\d+)$/', '', $_SERVER['HTTP_HOST']) . $picture['src']);
					$APPLICATION->SetPageProperty("og:url", 'https://' . preg_replace('/(:\d+)$/', '', $_SERVER['HTTP_HOST']) . "/event/item/" . $arElement['ID'] . "/");
					$APPLICATION->SetPageProperty("twitter:image:src", 'https://' . preg_replace('/(:\d+)$/', '', $_SERVER['HTTP_HOST']) . $picture['src']);
					$APPLICATION->SetPageProperty("twitter:card", "summary");
					$APPLICATION->SetPageProperty("og:image:width", "1238");
					$APPLICATION->SetPageProperty("og:image:height", "444");

					$this->includeComponentTemplate();
				}
			}

		} else {
			Tools::process404(
				"",
				($this->arParams["SET_STATUS_404"] === "Y"),
				($this->arParams["SET_STATUS_404"] === "Y"),
				($this->arParams["SHOW_404"] === "Y"),
				$this->arParams["FILE_404"]
			);
		}
	}

	public function executeComponent()
	{
		try {
			$this->checkModules();
			$this->getResult();
		} catch (Exception $e) {
			ShowError($e->getMessage());
		}
	}
}