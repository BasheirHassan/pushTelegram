<?php
/*
 * 1-11-2017
 * Module  Pushtelegram
 * pushtelegram
 * PushTelegram
 * pushtelegram_boot_token
 * pushtelegram_chat_ids
 *
 */
class ControllerExtensionModulePushTelegram extends Controller
{
    public function index()
    {
        //Load language file and settings model
        $this->load->language('extension/module/pushtelegram');
        $this->load->model('setting/setting');



        $this->document->setTitle = $this->language->get('heading_title');


        //Form posted from the PushTelegram module page
        if ((isset($this->request->post['pushtelegram_boot_token']))  && $this->validate())
        {

            $data['pushtelegram_boot_token'] = $this->request->post['pushtelegram_boot_token'];
            $data['pushtelegram_chat_ids'] = $this->request->post['pushtelegram_chat_ids'];

            /*
             * See what checkboxes need to be saved
             */
            if(isset($this->request->post['pushtelegram_order_alert']))
            {
                $data['pushtelegram_order_alert'] = true;
            }
            if(isset($this->request->post['pushtelegram_add_total']))
            {
                $data['pushtelegram_add_total'] = true;
            }
            if(isset($this->request->post['pushtelegram_add_products']))
            {
                $data['pushtelegram_add_products'] = true;
            }
            if(isset($this->request->post['pushtelegram_customer_alert']))
            {
                $data['pushtelegram_customer_alert'] = true;
            }
            if(isset($this->request->post['pushtelegram_add_customer_name']))
            {
                $data['pushtelegram_add_customer_name'] = true;
            }


            //Save the new settings for the module
            $this->model_setting_setting->editSetting('pushtelegram',$data);

            //Redirect to the setting page
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'], 'SSL'));






        }


        //Get the settings
        if (isset($this->request->post['pushtelegram_chat_ids']))
        {
            $data['settings'] = $this->request->post['pushtelegram_chat_ids'];

        } elseif ($this->model_setting_setting->getSetting('pushtelegram')) {
            $data['settings'] = $this->model_setting_setting->getSetting('pushtelegram');
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token='. $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', 'user_token='. $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/pushtelegram', 'user_token='. $this->session->data['user_token'], 'SSL')
        );


        $data['cancel'] = htmlspecialchars_decode($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'));

        //Set language & other variables
        $data['entry_user_key'] = $this->language->get('entry_user_key');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['entry_app_key'] = $this->language->get('entry_app_key');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_send_new_customer_alert'] = $this->language->get('entry_send_new_customer_alert');
        $data['entry_add_amount_total'] = $this->language->get('entry_add_amount_total');
        $data['entry_add_products'] = $this->language->get('entry_add_products');
        $data['entry_send_order_alert'] = $this->language->get('entry_send_order_alert');
        $data['entry_add_customer_name'] = $this->language->get('entry_add_customer_name');
        $data['header_customer_message'] = $this->language->get('header_customer_message');
        $data['header_order_message'] = $this->language->get('header_order_message');
        $data['error_no_key'] = $this->language->get('error_no_key');
        $data['message_sent_error'] = $this->language->get('message_sent_error');
        $data['message_sent_success'] = $this->language->get('message_sent_success');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['testUrl'] = htmlspecialchars_decode($this->url->link('extension/module/pushtelegram/testToken','user_token='.$this->session->data['user_token'], 'SSL'));
        $data['action'] = htmlspecialchars_decode($this->url->link('extension/module/pushtelegram','user_token='.$this->session->data['user_token'], 'SSL'));

        $this->load->model('design/layout');
        $data['layouts'] = $this->model_design_layout->getLayouts();
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');




        $this->response->setOutput($this->load->view('extension/module/pushtelegram',$data));
    }

    /*
     * Send test message, to see if the push functionality is working
     */
    public function testToken()
    {

        $botToken = $this->request->get['pushtelegram_boot_token'];
        $website  = "https://api.telegram.org/bot".$botToken;
        $chatId   = $this->request->get['pushtelegram_chat_ids'];  //Receiver Chat Id
        $params=[
            'chat_id'=>$chatId,
            'text'=>'Test',
        ];
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response;

    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/pushtelegram'))
        {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->error)
        {
            return true;
        } else
        {
            return false;
        }
    }
}
?>
