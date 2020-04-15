<?php

use esas\cmsgate\epos\ConfigFieldsEpos;
use esas\cmsgate\epos\view\client\ClientViewFieldsEpos;
use esas\cmsgate\view\admin\AdminViewFields;

return array(
    ConfigFieldsEpos::shopName() => 'Название магазина',
    ConfigFieldsEpos::shopName() . _DESC => 'Произвольное название Вашего магазина',

    ConfigFieldsEpos::iiiClientId() => 'Идентификатор клиента',
    ConfigFieldsEpos::iiiClientId() . _DESC => 'Идентификатор клиента для доступа к сервису идентификации',

    ConfigFieldsEpos::iiiClientSecret() => 'Секрет',
    ConfigFieldsEpos::iiiClientSecret() . _DESC => 'Секретный ключ для доступа к сервису идентификации',

    ConfigFieldsEpos::eposServiceProviderCode() => 'Код ПУ',
    ConfigFieldsEpos::eposServiceProviderCode() . _DESC => 'Код поставщика услуги в системе EPOS',

    ConfigFieldsEpos::eposServiceCode() => 'Код услуги EPOS',
    ConfigFieldsEpos::eposServiceCode() . _DESC => 'Код услуги в системе EPOS',

    ConfigFieldsEpos::eposRetailOutletCode() => 'Код торговой точки',
    ConfigFieldsEpos::eposRetailOutletCode() . _DESC => 'Код торговой точки',

    ConfigFieldsEpos::eposEsasConnector() => 'Подключение к ESAS',
    ConfigFieldsEpos::eposEsasConnector() . _DESC => 'Поставщик услаг Подлючение к ООО "Электронные Системы и Сервисы". Иначе счета будут выставлятся через ООО "Универсальные Платежные Системы"',

    ConfigFieldsEpos::sandbox() => 'Sandbox',
    ConfigFieldsEpos::sandbox() . _DESC => 'Режим *песочницы*. Если включен, то все счета буду выставляться в тестовой системе api-dev.hgrosh.by',

    ConfigFieldsEpos::instructionsSection() => 'Секция "Инструкция"',
    ConfigFieldsEpos::instructionsSection() . _DESC => 'Если включена, то на итоговом экране клиенту будет доступна пошаговая инструкция по оплате счета в ЕРИП',

    ConfigFieldsEpos::qrcodeSection() => 'Секция QR-код',
    ConfigFieldsEpos::qrcodeSection() . _DESC => 'Если включена, то на итоговом экране клиенту будет доступна оплата счета по QR-коду',

    ConfigFieldsEpos::webpaySection() => 'Секция Webpay',
    ConfigFieldsEpos::webpaySection() . _DESC => 'Если включена, то на итоговом экране клиенту отобразится кнопка для оплаты счета картой (переход на Webpay)',

    ConfigFieldsEpos::completionText() => 'Текст успешного выставления счета',
    ConfigFieldsEpos::completionText() . _DESC => 'Текст, отображаемый кленту после успешного выставления счета. Может содержать html. ' .
        'В тексте допустимо ссылаться на переменные @order_id, @order_number, @order_total, @order_currency, @order_fullname, @order_phone, @order_address',
    ConfigFieldsEpos::completionText() . _DEFAULT => '<p>Счет №<strong>@order_number</strong> успешно выставлен в EPOS</p>
<p>Вы можете оплатить его наличными деньгами, пластиковой карточкой и электронными деньгами, в любом из отделений
    банков, кассах, банкоматах, платежных терминалах, в системе электронных денег, через Интернет-банкинг, М-банкинг,
    интернет-эквайринг</p>',

    ConfigFieldsEpos::completionCssFile() => 'CSS файл для итогового экрана',
    ConfigFieldsEpos::completionCssFile() . _DESC => 'Позволяет задать путь к CSS файлу для экрана успешного выставления счета',

    ConfigFieldsEpos::paymentMethodName() => 'Название способы оплаты',
    ConfigFieldsEpos::paymentMethodName() . _DESC => 'Название, отображаемое клиенту, при выборе способа оплаты',
    ConfigFieldsEpos::paymentMethodName() . _DEFAULT => 'Через систему EPOS',

    ConfigFieldsEpos::paymentMethodDetails() => 'Описание способа оплаты',
    ConfigFieldsEpos::paymentMethodDetails() . _DESC => 'Описание, отображаемое клиенту, при выборе способа оплаты',
    ConfigFieldsEpos::paymentMethodDetails() . _DEFAULT => '«EPOS»™ — платежный сервис по выставлению счетов в АИС *Расчет* (ЕРИП). ' .
        'После выставления счета Вам будет доступна его оплата пластиковой карточкой и электронными деньгами, в любом из отделений банков, кассах, банкоматах, платежных терминалах, в системе электронных денег, через Интернет-банкинг, М-банкинг, интернет-эквайринг. ' .
        'Также сервис предоставляет способ удобной оплаты счетов через QR-коды',

    ConfigFieldsEpos::billStatusPending() => 'Статус при выставлении счета',
    ConfigFieldsEpos::billStatusPending() . _DESC => 'Какой статус выставить заказу при успешном выставлении счета в ЕРИП (идентификатор существующего статуса)',

    ConfigFieldsEpos::billStatusPayed() => 'Статус при успешной оплате счета',
    ConfigFieldsEpos::billStatusPayed() . _DESC => 'Какой статус выставить заказу при успешной оплате выставленного счета (идентификатор существующего статуса)',

    ConfigFieldsEpos::billStatusFailed() => 'Статус при ошибке оплаты счета',
    ConfigFieldsEpos::billStatusFailed() . _DESC => 'Какой статус выставить заказу при ошибке выставленния счета (идентификатор существующего статуса)',

    ConfigFieldsEpos::billStatusCanceled() => 'Статус при отмене оплаты счета',
    ConfigFieldsEpos::billStatusCanceled() . _DESC => 'Какой статус выставить заказу при отмене оплаты счета (идентификатор существующего статуса)',

    ConfigFieldsEpos::dueInterval() => 'Срок действия счета (дней)',
    ConfigFieldsEpos::dueInterval() . _DESC => 'Как долго счет, будет доступен в EPOS для оплаты',

    ClientViewFieldsEpos::INSTRUCTIONS_TAB_LABEL => 'Инструкция по оплате счета в EPOS',
    ClientViewFieldsEpos::INSTRUCTIONS => '<p>Для оплаты счета в EPOS необходимо:</p>
<ol>
    <li>Выбрать дерево платежей ЕРИП</li>
    <li>Выбрать услугу: <strong>Сервис EPOS</strong></li>
    <li>Ввести номер счета: <strong>@epos_order_id</strong></li>
    <li>Проверить корректность информации</li>
    <li>Совершить платеж.</li>
</ol>',


    ClientViewFieldsEpos::QRCODE_TAB_LABEL => 'Оплата по QR-коду',
    ClientViewFieldsEpos::QRCODE_DETAILS => '<p>Оплатить счет через банковское мобильное приложение по QR-коду:</p><div align="center">@qr_code</div><p>Информация о мобильных приложениях, поддерживающих сервис оплаты по QR-коду (платёжной ссылке), <a href="http://pay.raschet.by/" target="_blank"
style="color: #8c2003;"><span>здесь</span></a></p>',

    ClientViewFieldsEpos::WEBPAY_TAB_LABEL => 'Оплатить картой',
    ClientViewFieldsEpos::WEBPAY_DETAILS => 'Вы можете оплатить счет с помощью карты Visa, Mastercard или Белкарт через систему электронных платежей WEBPAY',
    ClientViewFieldsEpos::WEBPAY_BUTTON_LABEL => 'Перейти к оплате',
    ClientViewFieldsEpos::WEBPAY_MSG_SUCCESS => 'Счет успешно оплачен через сервис WebPay',
    ClientViewFieldsEpos::WEBPAY_MSG_UNSUCCESS => 'Ошибка оплаты счета через сервис WebPay',
    ClientViewFieldsEpos::WEBPAY_MSG_UNAVAILABLE => 'Извините, операция временно недоступна',

    AdminViewFields::ADMIN_PAYMENT_METHOD_NAME => 'EPOS',
    AdminViewFields::ADMIN_PAYMENT_METHOD_DESCRIPTION => 'Оплата через сервис EPOS',
);