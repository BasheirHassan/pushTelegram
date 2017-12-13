<?php
/*
 * TomIT
 * 29-11-2013
 * Frontend part of the PushTelegram module
 */
class ModelExtensionModulePushTelegram extends Model {


    public function sendOrderAlert($data)
    {

        $this->load->model('setting/setting');
        $setting = $this->model_setting_setting->getSetting('pushtelegram');

        if(isset($setting['pushtelegram_order_alert']))
        {
            $message = "You have a new order\n";

            if(isset($setting['pushtelegram_add_total']))
            {
                $message.= "from {$data['firstname']} {$data['lastname']}\nTotal: {$data['total']}\n";
            }
            if(isset($data['status']))
            {
                $message.="Status: {$data['status']}";
            }
            if(isset($setting['pushtelegram_add_products']))
            {
                foreach($data['products'] as $product){
                    $message.= "{$product['quantity']} : {$product['name']}\n";
                }
            }
            $this->sendMessagetoTelegam($setting,$message);

        }
    }

    public function sendAccountAlert($data)
    {
        $this->load->model('setting/setting');
        $setting = $this->model_setting_setting->getSetting('pushtelegram');

        if(isset($setting['pushtelegram_customer_alert']))
        {
            $message = "New Customer";
            if(isset($setting['pushtelegram_add_customer_name']))
            {
                $message.= "\n{$data['firstname']} {$data['lastname']}";
            }
            $this->sendMessagetoTelegam($setting,$message);

        }
    }

    //Send  message To PushTelegram

    public function sendMessagetoTelegam($setting,$msg){

//        "boot_token" => $setting['pushtelegram_boot_token'],
//        "chat_ids" => $setting['pushtelegram_chat_ids'],

        $botToken = $setting['pushtelegram_boot_token'];
        $website  = "https://api.telegram.org/bot".$botToken;
        $chatIds   = $setting['pushtelegram_chat_ids'];  //Receiver Chat Id

        if (is_array($chatIds)){


            foreach ($chatIds as $val ){
                $this->initMessage($botToken,$val,$msg);
            }

        }
        else {
            $this->initMessage($botToken,$chatIds,$msg);

        }


    }



    private function initMessage($botToken,$chatID,$msg){

        $website  = "https://api.telegram.org/bot".$botToken;

        $params=[
            'chat_id'=>$chatID,
            'text'=>$msg,
        ];
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

    }


}