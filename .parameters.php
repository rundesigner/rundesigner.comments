<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*
 * Файл параметров, используется для отображения редактора параметров компонента 
 */

if(!CModule::IncludeModule("iblock"))
	return;

if($arCurrentValues["IBLOCK_ID"] > 0)
	$bWorkflowIncluded = CIBlock::GetArrayByID($arCurrentValues["IBLOCK_ID"], "WORKFLOW") == "Y" && CModule::IncludeModule("workflow");
else
	$bWorkflowIncluded = CModule::IncludeModule("workflow");

//список типов блоков для выбора
$arIBlockType = CIBlockParameters::GetIBlockTypes();

//список инфоблоков для выбора
$arIBlock=[];
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

//список свойств с типом привязка элемента для выбора  из инфоблока $arCurrentValues["IBLOCK_ID"]
$arIBlockElementIDPropertyList = [];
$sort = ["sort"=>"asc", "name"=>"asc"];
$filter = [
    "ACTIVE"=>"Y", 
    "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"],
    "PROPERTY_TYPE"=>"E" //привязка к элементам    
        ]; 
$rsProp = CIBlockProperty::GetList($sort, $filter);
while ($arr=$rsProp->Fetch())
{
	$arIBlockElementIDPropertyList[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
}

//список свойств с типом строка для выбора хранения емайл  из инфоблока $arCurrentValues["IBLOCK_ID"]
$arIBlockEmailPropertyList = [];
$filter["PROPERTY_TYPE"] = "S"; //строка
$rsProp = CIBlockProperty::GetList($sort, $filter);
while ($arr=$rsProp->Fetch())
{
	$arIBlockEmailPropertyList[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
}

//список свойств с типом строка  для выбора хранения имени  из инфоблока $arCurrentValues["IBLOCK_ID"] за исключением $arCurrentValues["IBLOCK_PROPERTY_EMAIL"]
$arIBlockNamePropertyList = [];
$rsProp = CIBlockProperty::GetList($sort, $filter);
while ($arr=$rsProp->Fetch())
{
    if($arCurrentValues["IBLOCK_PROPERTY_EMAIL"] != $arr["CODE"]){
	$arIBlockNamePropertyList[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
    }
}

$arComponentParameters = [
    "PARAMETERS" => [
        //тип инфоблока
        "IBLOCK_TYPE" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("RUNDESIGNER_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y",
        ],
        //ид инфоблока
        "IBLOCK_ID" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("RUNDESIGNER_IBLOCK_IBLOCK"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ],
        //Ид свойства инфоблока в котором будет хранится ссылка на комментируемый элемент
        "IBLOCK_PROPERTY_ELEMENT_ID" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("RUNDESIGNER_IBLOCK_PROPERTY_ELEMENT_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlockElementIDPropertyList,
            "REFRESH" => "N",
        ],
        //ИД свойства инфоблока в котором будет хранится емайл
        "IBLOCK_PROPERTY_EMAIL" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("RUNDESIGNER_IBLOCK_PROPERTY_EMAIL"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlockEmailPropertyList,
            "REFRESH" => "Y",
        ],
        //ИД свойства инфоблока в котором будет хранится имя
        "IBLOCK_PROPERTY_NAME" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("RUNDESIGNER_IBLOCK_PROPERTY_NAME"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIBlockNamePropertyList,
            "REFRESH" => "N",
        ],
        //ид элемента, который комментируем
        "IBLOCK_ELEMENT_ID" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("RUNDESIGNER_IBLOCK_ELEMENT_ID"),
            "TYPE" => "STRING",
            "ADDITIONAL_VALUES" => "Y",
        ],
        "CACHE_TIME" => []
    ]
];
