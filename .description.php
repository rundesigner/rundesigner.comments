<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/*
 * Файл описания компонента, используется для отображения 
 * в панели визуального редактора
 */
$arComponentDescription = [
    "NAME" => GetMessage("RUNDESIGNER_DESC_NAME"),
    "DESCRIPTION" => GetMessage("RUNDESIGNER_DESC_NAME_DESC"),
    "ICON" => "/images/icon.png",
    "SORT" => 1,
    "CACHE_PATH" => "Y",
    "PATH" => [
        "ID" => "rundesigner",
        "NAME" => GetMessage("RUNDESIGNER_COMPONENTS"),
        "SORT" => 1,
    ],
];
