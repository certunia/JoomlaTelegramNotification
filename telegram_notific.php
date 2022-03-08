<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgVmcustomTelegram_notific extends JPlugin {
    function plgVmConfirmedOrder (&$_orderData) {
        if ( $this->params->get('on_off', 'text_def') == 0 ) {
            return true;
        }

        ///////////////////////////////////////////////////////
        $msg = '';

        // add something before
        // those are configurable text fields, you can set them in admin page
        $msg_before = $this->params->get('msg_before', 'text_def');
        $msg_after  = $this->params->get('msg_after' , 'text_def');

        $i = 1;
        $product_msg = '';

        // if you wanna see all orderDetails on the order page
        //var_dump($_orderData->orderDetails["details"]["BT"]);

        // get every product detail
        foreach ($_orderData->products as $product) {
            $product_msg = $i.'| '.$product->quantity.$product->product_name."\n" ;
        }

        //// create text message

        // msg before
        $msg = $msg.$msg_before."\n";

        // id
        $msg = $msg.'Номер заказа: '.$_orderData->orderDetails["details"]["BT"]->order_number."\n";

        // created
        $msg = $msg.'Создано: '.$_orderData->orderDetails["details"]["BT"]->created_on."\n";


        // Product sum
        $msg = $msg.'Сумма: '.$_orderData->orderDetails["details"]["BT"]->order_salesPrice."\n";

        // Delivery sum
        //$msg = $msg.'Доставка : '.$_orderData->orderDetails["details"]["BT"]->order_shipment."\n";

        // Total
        //$msg = $msg.'Всего : '.$_orderData->orderDetails["details"]["BT"]->order_shipment."\n";


        // Имя
        $msg = $msg.'Имя: '.$_orderData->orderDetails["details"]["BT"]->first_name."\n";

        // Фамилия
        $msg = $msg.'Фамилия: '.$_orderData->orderDetails["details"]["BT"]->last_name."\n";

        // Email
        $msg = $msg.'Почта: '.$_orderData->orderDetails["details"]["BT"]->email."\n";

        // Phone
        $msg = $msg.'Телефон: '.$_orderData->orderDetails["details"]["BT"]->phone_1."\n";

        // Description
        $msg = $msg.'Описание: '.$_orderData->orderDetails["details"]["BT"]->customer_note."\n";

        // Products
        $msg = "\n\n".$msg.$product_msg."\n";

        // msg after
        $msg = "\n".$msg.$msg_after."\n";


        ////////////////////////////////////////////////////////

        // send telegram function
        function sendMessage($chatID, $msg, $token) {
            $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatID;
            $url = $url . "&text=" . urlencode($msg);
            $ch = curl_init();
            $optArray = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $optArray);
            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        }

        // Set your Bot ID and Chat ID.
        // those are configurable text fields, you can set them in admin page
        $telegrambot = $this->params->get('token', 'text_def');
        $telegramchatid = $this->params->get('chat_id', 'text_def');

        // Send message
        sendMessage($telegramchatid, $msg, $telegrambot);

        return true;
    }
}
