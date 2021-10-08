<?php

namespace Hals\Components;

/*
 * Файл компонента комментариев, отвечает за вывод комментариев
 */

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Loader;

class CHalsComments extends \CBitrixComponent implements Controllerable {
    /*
     * Проверка входных параметров 
     */

    public function onPrepareComponentParams($arParams) {
        $keys = [
            "IBLOCK_TYPE",
            "IBLOCK_ID",
            "IBLOCK_PROPERTY_ELEMENT_ID",
            "IBLOCK_PROPERTY_EMAIL",
            "IBLOCK_PROPERTY_NAME",
            "IBLOCK_ELEMENT_ID"
        ];

        $paramsError = false;
        //Если не заполнены необходимые параметры - требуем настройки компонента и ничего не делаем
        foreach ($keys AS $key) {
            if (empty($arParams[$key])) {
                $paramsError = true;
                break;
            }
        }
        if ($paramsError) {
            $this->arResult['ERRORPARAMS'] = 1;
        } else {
            $result = $arParams;
        }

        return $result;
    }

    /*
     * Основная логика компонента
     */

    public function executeComponent() {
        global $USER;
        $isauth = $USER->IsAuthorized() ? "1" : "";
        if (!$this->_checkIblockModule()) {
            return;
        }
        /*
         * Подключаем бутстрап 4
         */
        \Bitrix\Main\UI\Extension::load("ui.bootstrap4");

        if ($this->startResultCache(false, $isauth)) {
            $this->arResult["isauth"] = $isauth;
            $this->includeComponentTemplate();
        }
    }

    /*
     *   Сбрасываем фильтры по-умолчанию (ActionFilter\Authentication и ActionFilter\HttpMethod)
     *   Предустановленные фильтры находятся в папке /bitrix/modules/main/lib/engine/actionfilter/
     */

    public function configureActions() {
        return [
            'postComment' => [// Ajax-метод
                'prefilters' => [],
            ],
            'getComments' => [// Ajax-метод
                'prefilters' => [],
            ],
        ];
    }

    /*
     *  Ajax-api метод-обертка, записывает комментарий
     */

    public function postCommentAction($postdata, $commentdata) {
        $this->_postComment($postdata, $commentdata);
        $result = $this->_getComments($postdata);
        return $result;
    }

    /*
     *  Ajax-api метод-обертка, возвращает список комментариев
     */

    public function getCommentsAction($postdata) {
        $result = $this->_getComments($postdata);
        return $result;
    }

    public function _checkIblockModule() {
        if (!Loader::includeModule("iblock")) {
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return false;
        }
        return true;
    }

    /*
     * записывает комментарий
     */

    public function _postComment($postdata, $commentdata) {
        //$commentdata  imya email text
        if (!$this->_checkIblockModule()) {
            return;
        }
        global $USER;
        
        $dataArray = [
            'ACTIVE_FROM' => \ConvertTimeStamp(time() + \CTimeZone::GetOffset(), "SHORT"),
            'IBLOCK_SECTION_ID' => false, // элемент лежит в корне раздела  
            'IBLOCK_ID' => $postdata["iblockid"],
            'NAME' => 'Комментарий ',
            'ACTIVE' => 'Y', // активен  
            'DETAIL_TEXT' => $commentdata["text"],
        ];
        
        if($USER->IsAuthorized()){
            $imya = $USER->GetFirstName();
            $email = $USER->GetEmail();   
            $dataArray['MODIFIED_BY'] = $USER->GetID();
        }else{
            $imya = $commentdata["imya"];
            $email = $commentdata["email"];        
        }
        
        $el = new \CIBlockElement;
        $PROP = [];
        $PROP[$postdata["propertyemail"]] = $email;  
        $PROP[$postdata["propertyname"]] = $imya; 
        $PROP[$postdata["propertyelementid"]] = $postdata['elementid']; 
         
        $dataArray['PROPERTY_VALUES'] = $PROP;        

        $res = $el->Add($dataArray);
        \Bitrix\Main\Diag\Debug::dumpToFile($dataArray, "dataArray", "rundesignercomments_log.txt");
        \Bitrix\Main\Diag\Debug::dumpToFile($res, "result add", "rundesignercomments_log.txt");
        \Bitrix\Main\Diag\Debug::dumpToFile($el, "el", "rundesignercomments_log.txt");

    }

    /*
     * возвращает список комментариев
     */

    public function _getComments($postdata) {
        if (!$this->_checkIblockModule()) {
            return;
        }
        //iblockid: "17", propertyelementid: "85", propertyemail: "84", propertyname: "83", elementid: "3"}
        //SELECT
        $select = [
            "ID",
            "TIMESTAMP_X",
            "DETAIL_TEXT",
            "PROPERTY_{$postdata["propertyemail"]}",
            "PROPERTY_{$postdata["propertyname"]}"
        ];

        $filter = [
            "IBLOCK_ID" => $postdata["iblockid"],
            "PROPERTY_{$postdata["propertyelementid"]}_VALUE" => $postdata['elementid'],
            "ACTIVE" => "Y"
        ];

        $order = ['TIMESTAMP_X' => 'desc'];
        $result = [];
        $rsElement = \CIBlockElement::GetList($order, $filter, false, false, $select);
        while ($arr = $rsElement->Fetch()) {
         //   \Bitrix\Main\Diag\Debug::dumpToFile($arr, "arr", "rundesignercomments_log.txt");
            $row = new \stdClass();
            $row->id = $arr["ID"];
            $row->imya = $arr["PROPERTY_".strtoupper($postdata["propertyname"])."_VALUE"];
            $row->email = $arr["PROPERTY_".strtoupper($postdata["propertyemail"])."_VALUE"];
            $row->comment = $arr["DETAIL_TEXT"];
            $row->comentdate = \CIBlockFormatProperties::DateFormat('SHORT', MakeTimeStamp($arr["TIMESTAMP_X"], \CSite::GetDateFormat()));
            $result[] = $row;
            
        }
        return $result;
    }

}
