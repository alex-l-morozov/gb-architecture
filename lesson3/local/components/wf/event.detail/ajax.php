<?php
// Запрет сбора статистики на данной странице
define("NO_KEEP_STATISTIC", "Y");
// Запрет действий модуля "Статистика", выполняемых ежедневно при помощи технологии агентов:
// перевод на новый день;
// очистка устаревших данных статистики;
// отсылка ежедневного статистического отчета.
define("NO_AGENT_STATISTIC", "Y");

// Подключение служебной части пролога
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

//use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use Bitrix\Main\HttpResponse;
use Bitrix\Main\Loader;
use Bitrix\Main\Web;
use WF\Project\Models;

Loader::includeModule('iblock');
Loader::includeModule('wf.project');

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

if ($request->isAjaxRequest()) {

	$response = new HttpResponse($context);
	$response->addHeader("Content-Type", "application/json");

	$obUser = new Bitrix\Main\UserTable();
	$obEventUser = new Models\EventUserListTable();
	$obEventBid = new Models\EventBidListTable();
	$obEventInvite = new Models\EventInviteListTable();
	$obElement = new \CIBlockElement();

	if ($request->getPost('action')) {

		if ($request->getPost('action') == "signEvent" && intval($request->getPost('id'))) {
			if (!$USER->IsAuthorized()) {
				$response->flush(Web\Json::encode(array(
					"result" => "error",
					"error" => "auth",
				)));

				die();
			}

			$rsElement = $obElement->GetList(
				array(),
				array(
					'IBLOCK_ID' => IBLOCK_EVENTS_ID,
					'ID' => intval($request->getPost('id')),
				),
				false,
				array(
					'nTopCount' => 1,
				),
				array(
					'ID',
					'PROPERTY_PRIVATE',
					'PROPERTY_LIMIT_MEMBERS',
					'PROPERTY_DATETIME_START',
					'PROPERTY_DATETIME_END',
					'PROPERTY_AUTHOR',
					'PROPERTY_BLOCKED_USERS',
				)
			);
			if ($arElement = $rsElement->Fetch()) {
				$eventPrivate = ($arElement['PROPERTY_PRIVATE_VALUE'] == 'Да');
				$eventLimit = intval($arElement['PROPERTY_LIMIT_MEMBERS_VALUE']);
				$eventDateStart = strtotime($arElement['PROPERTY_DATETIME_START_VALUE']);
				$eventDateEnd = strtotime($arElement['PROPERTY_DATETIME_END_VALUE']);
				$blockUsers = $arElement['PROPERTY_BLOCKED_USERS_VALUE'];
				$eventSignID = false;
				$eventInvite = false;
				$eventInviteAll = false;

				if (in_array(intval($USER->GetID()), $blockUsers)) {
					$response->flush(Web\Json::encode(array(
						"result" => "error",
						"error" => "date",
						"msg_header" => 'Ошибка',
						"msg" => "Организатор мероприятия ограничил круг участников",
					)));
					die();
				}

				$rsEventUser = $obEventUser->getList(
					array(
						'filter' => array(
							'EVENT_ID' => $arElement['ID'],
							'USER_ID' => intval($USER->GetID()),
							//'IS_AUTHOR' => 0,
						)
					)
				);
				if ($arEventUser = $rsEventUser->Fetch()) {
					$eventSignID = $arEventUser['ID'];
				}

				if (time() >= $eventDateEnd) {
					if (intval($eventSignID) > 0) {
						$response->flush(Web\Json::encode(array(
							"result" => "error",
							"error" => "date",
							"msg_header" => 'Ошибка',
							"msg" => "Мероприятие уже закончилось, вы не можете его покинуть",
						)));
					} else {
						$response->flush(Web\Json::encode(array(
							"result" => "error",
							"error" => "date",
							"msg_header" => 'Ошибка',
							"msg" => "Мероприятие уже закончилось, вы не можете присоединиться",
						)));
					}
					die();
				}

				if (intval($eventSignID) > 0) {
					$obEventUser->delete(intval($eventSignID));

					$rsEventUser = $obEventUser->getList(array(
						'filter' => array(
							'EVENT_ID' => $arElement['ID']
						)
					));
					if (!$rsEventUser->Fetch()) {
						$enumHidden = '';
						$rsHidden = \CIBlockProperty::GetPropertyEnum('HIDDEN', array(), array('IBLOCK_ID' => IBLOCK_EVENTS_ID));
						if ($arHidden = $rsHidden->fetch()) {
							$enumHidden = $arHidden['ID'];
						}

						$obElement->SetPropertyValuesEx($arElement['ID'], $arElement['IBLOCK_ID'], array('HIDDEN' => $enumHidden));
					}

					$response->flush(Web\Json::encode(array(
						"result" => "success",
						"button" => "присоединиться",
					)));
					die();
				} else {
					$rsInvite = $obEventInvite->getList(
						array(
							'filter' => array(
								'EVENT_ID' => $arElement['ID'],
								'USER_ID' => intval($USER->GetID()),
							)
						)
					);
					if ($arInvite = $rsInvite->Fetch()) {
						$eventInviteAll = true;
					}
					$rsInvite = $obEventInvite->getList(
						array(
							'filter' => array(
								'EVENT_ID' => $arElement['ID'],
								'USER_ID' => intval($USER->GetID()),
								'SENDER_ID' => $arElement['PROPERTY_AUTHOR_VALUE'],
							)
						)
					);
					if ($arInvite = $rsInvite->Fetch()) {
						$eventInvite = true;
					}

					if ($eventPrivate && !$eventInvite) {
						$rsEventBid = $obEventBid->getList(array(
							'filter' => array(
								'EVENT_ID' => $arElement['ID'],
								'USER_ID' => intval($USER->GetID())
							)
						));
						if ($arEventBid = $rsEventBid->Fetch()) {
							$obEventBid->delete($arEventBid['ID']);

							$response->flush(Web\Json::encode(array(
								"result" => "success",
								"button" => "подать заявку",
							)));
							die();
						} else {
							$obEventBid->add(
								array(
									'USER_ID' => intval($USER->GetID()),
									'EVENT_ID' => $arElement['ID'],
								)
							);
							if ($eventInviteAll) {
								$rsEventInvite = $obEventInvite->getList(array(
									'filter' => array(
										'EVENT_ID' => $arElement['ID'],
										'USER_ID' => intval($USER->GetID()),
									)
								));
								while ($arEventInvite = $rsEventInvite->fetch()) {
									$obEventInvite->delete($arEventInvite['ID']);
								}
								$response->flush(Web\Json::encode(array(
									"result" => "success",
									"button" => "отменить заявку",
									"msg_header" => "Информация",
									"msg" => "Заявка подана, ожидайте подтверждения",
								)));
							} else {
								$response->flush(Web\Json::encode(array(
									"result" => "success",
									"button" => "отменить заявку",
								)));
							}
							die();
						}
					} else {
						$rsEventUser = $obEventUser->getList(array(
							'select' => array('CNT'),
							'filter' => array(
								'EVENT_ID' => $arElement['ID']
							)
						));
						if ($arEventUser = $rsEventUser->Fetch()) {
							if ($eventLimit && $eventLimit <= $arEventUser['CNT']) {
								$response->flush(Web\Json::encode(array(
									"result" => "error",
									"error" => "limit",
									"msg" => 'Невозможно принять участие, количество участников ' . $arEventUser['CNT'] . ' из ' . $eventLimit,
								)));
								die();
							}
						}

						$obEventUser->add(
							array(
								'USER_ID' => intval($USER->GetID()),
								'EVENT_ID' => $arElement['ID'],
								'IS_MEMBER' => true,
								'IS_AUTHOR' => ($USER->GetID() == $arElement['PROPERTY_AUTHOR_VALUE'] ? true : false)
							)
						);

						$rsEventInvite = $obEventInvite->getList(array(
							'filter' => array(
								'EVENT_ID' => $arElement['ID'],
								'USER_ID' => intval($USER->GetID()),
							)
						));
						while ($arEventInvite = $rsEventInvite->fetch()) {
							$obEventInvite->delete($arEventInvite['ID']);
						}

						$response->flush(Web\Json::encode(array(
							"result" => "success",
							"button" => "выйти",
						)));
						die();
					}
				}
			}

		}

		if ($request->getPost('action') == "refuseEvent" && intval($request->getPost('id'))) {
			if (!$USER->IsAuthorized()) {
				$response->flush(Web\Json::encode(array(
					"result" => "error",
					"error" => "auth",
				)));
				die();
			}

			$rsElement = $obElement->GetList(
				array(),
				array(
					'IBLOCK_ID' => IBLOCK_EVENTS_ID,
					'ID' => intval($request->getPost('id')),
				),
				false,
				array(
					'nTopCount' => 1,
				),
				array(
					'ID',
					'PROPERTY_PRIVATE',
				)
			);
			if ($arElement = $rsElement->Fetch()) {
				$eventPrivate = ($arElement['PROPERTY_PRIVATE_VALUE'] == 'Да');
				$rsEventInvite = $obEventInvite->getList(
					array(
						'filter' => array(
							'EVENT_ID' => $arElement['ID'],
							'USER_ID' => intval($USER->GetID())
						)
					)
				);
				while ($arEventInvite = $rsEventInvite->Fetch()) {
					$obEventInvite->delete($arEventInvite['ID']);
				}
				if ($eventPrivate) {
					$response->flush(Web\Json::encode(array(
						"result" => "success",
						"button" => "подать заявку",
					)));
				} else {
					$response->flush(Web\Json::encode(array(
						"result" => "success",
						"button" => "присоединиться",
					)));
				}
				die();
			}
		}

	}
}