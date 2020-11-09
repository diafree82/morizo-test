<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->set_cookie("utm_source", $_GET["utm_source"]);
$newVal = $APPLICATION->get_cookie("utm_source");

if (CModule::IncludeModule("sale")) {
    $prop = Bitrix\Sale\Internals\OrderPropsValueTable::getList([
        "filter" => [
            "ORDER_ID" => 1,
            "CODE"     => "UTM_SOURCE"
        ]
    ])->Fetch();

    if ($prop) {
        CSaleOrderPropsValue::Update($prop["ID"], [
            "VALUE" => $newVal
        ]);
    }
}
