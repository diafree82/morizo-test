<?
private function setImgSrc(&$arBasketItems, $arElementId, $arSku2Parent)
{
    //TODO: need refactoring
    $arImgFields = array ("PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_MORE_PHOTO", "PROPERTY_ARTICLE");
    $arProductData = getProductProps(array_merge($arElementId, $arSku2Parent), array_merge(array("ID"), $arImgFields));

    foreach ($arBasketItems as &$arItem)
    {
        if (array_key_exists($arItem["PRODUCT_ID"], $arProductData) && is_array($arProductData[$arItem["PRODUCT_ID"]]))
        {
            foreach ($arProductData[$arItem["PRODUCT_ID"]] as $key => $value)
            {
                if (strpos($key, "PROPERTY_") !== false || in_array($key, $arImgFields))
                    $arItem[$key] = $value;
            }
        }

        if (array_key_exists($arItem["PRODUCT_ID"], $arSku2Parent)) // if sku element doesn't have value of some property - we'll show parent element value instead
        {
            foreach ($arImgFields as $field) // fields to be filled with parents' values if empty
            {
                $fieldVal = (in_array($field, $arImgFields)) ? $field : $field."_VALUE";
                $parentId = $arSku2Parent[$arItem["PRODUCT_ID"]];

                if ((!isset($arItem[$fieldVal]) || (isset($arItem[$fieldVal]) && strlen($arItem[$fieldVal]) == 0))
                    && (isset($arProductData[$parentId][$fieldVal]) && !empty($arProductData[$parentId][$fieldVal]))) // can be array or string
                {
                    $arItem[$fieldVal] = $arProductData[$parentId][$fieldVal];
                }
            }
        }
        
        $arItem["PICTURE_SRC"] = "";
        $arImage = null;

        if ($arProductData[$arItem["PRODUCT_ID"]]["PROPERTY_ARTICLE_VALUE"]) {
            $arItem["PICTURE_SRC"] = "/upload/photo/".$arProductData[$arItem["PRODUCT_ID"]]["PROPERTY_CODE_VALUE"].".jpg";
        } else {
            if (isset($arItem["PREVIEW_PICTURE"]) && intval($arItem["PREVIEW_PICTURE"]) > 0)
                $arImage = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
            elseif (isset($arItem["DETAIL_PICTURE"]) && intval($arItem["DETAIL_PICTURE"]) > 0)
                $arImage = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
            if ($arImage)
            {
                $arFileTmp = CFile::ResizeImageGet(
                    $arImage,
                    array("width" => $this->arParams['MAX_IMAGE_SIZE'], "height" => $this->arParams['MAX_IMAGE_SIZE']),
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    true
                );
                $arItem["PICTURE_SRC"] = $arFileTmp["src"];
            }
        }
    }
}