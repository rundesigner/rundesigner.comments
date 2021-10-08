<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/*
 * Шаблон компонента, отвечает за вывод результатов работы компонента
 */
?>
<? if (!empty($arResult["ERRORPARAMS"])) { ?>
    <div id="rundesignercommentsparamserror" class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><?= GetMessage("RUNDESIGNER_ERRORPARAMSMESSAGE") ?>!</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?
    return;
}
?>
<div id="rundesignercommentsdata" 
     data-param-iblockid="<?= $arParams["IBLOCK_ID"] ?>" 
     data-param-propertyelementid="<?= $arParams["IBLOCK_PROPERTY_ELEMENT_ID"] ?>" 
     data-param-propertyemail="<?= $arParams["IBLOCK_PROPERTY_EMAIL"] ?>" 
     data-param-propertyname="<?= $arParams["IBLOCK_PROPERTY_NAME"] ?>" 
     data-param-elementid="<?= $arParams["IBLOCK_ELEMENT_ID"] ?>" 
     data-message-error="<?= GetMessage("RUNDESIGNER_MESSAGE_ERROR") ?>" 
     data-message-wrongform="<?= GetMessage("RUNDESIGNER_MESSAGE_WRONGFORM") ?>" 
     class="container justify-content-center mt-5 border-left border-right">
    <div class="d-flex justify-content-center pt-3 pb-2">
        <form class="w-100">
            <?php
            //не показываем для авторизованных
            $dnone = empty($arResult["isauth"]) ? "" : "d-none";
            ?>
        <div id="hcimyaemail" class="form-group row <?= $dnone?> m-0">
            <input id="rundesignercomment-input-imya" type="text" name="imya" placeholder="<?= GetMessage("RUNDESIGNER_MESSAGE_IMYA") ?>" class="col form-control addtxt"> 
            <input id="rundesignercomment-input-email" type="email" name="email" placeholder="<?= GetMessage("RUNDESIGNER_MESSAGE_EMAIL") ?>" class="col form-control addtxt"> 
        </div>
        <div  class="form-group row m-0">
            <textarea id="rundesignercomment-input-text" name="comment" placeholder="<?= GetMessage("RUNDESIGNER_MESSAGE_ADD") ?>" class="mt-2 form-control addtxt"></textarea> 
        </div>
            <div  class="form-group row m-0">
            <button id="rundesignercommentssend" type="submit" class="mt-2 btn btn-primary"><?= GetMessage("RUNDESIGNER_MESSAGE_SEND") ?></button>
            </div>
          </form>  
    </div>

   

    <div id="rundesignernocomments" class="d-none alert alert-success alert-dismissible fade show justify-content-center" role="alert">
        <strong><?= GetMessage("RUNDESIGNER_NOCOMMENTS") ?></strong> 
    </div>
</div>
<div id="hcrowtemplate" class="d-none justify-content-center py-2 pb-3">
    <div class="second py-2 px-2"> <span class="text1 textcomment"></span>
        <div class="d-flex justify-content-between py-1 pt-2">
            <div><span class="text2 textauthor"></span></div>
            <div><span class="text3"><a class="hcemail" href=""><i class="fa fa-envelope"></i></a></span><span class="thumbup"><i class="fa fa-calendar"></i></span><span class="text4 text4o hcdate">1</span></div>
        </div>
    </div>
</div>
<? CJSCore::Init(['ajax', 'jquery']); ?>