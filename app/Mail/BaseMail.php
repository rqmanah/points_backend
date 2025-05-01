<?php

namespace App\Mail;

use App\Bll\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BaseMail extends Mailable
{

    use Queueable, SerializesModels;

    public $user_id, $token;
    public $html;
    protected $store ;
    protected $template ;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $user_id, $token="" )
    {
        $this->user_id = $user_id;
        $this->token = $token;
        $this->store = $this->getSmtpSettings();

        $this->template = MailingTemplate::
            where('category', 'customers')
            ->where('store_id' ,Utility::get_store_id())
            ->where('type','forget_password')
            ->where('lang_id', Utility::lang_id())
            ->first();
	}
    public static function getSmtpSettings()
    {
        $settings = StoreSmtp::where('store_id', Utility::get_store_id())->first();
        return $settings;
    }


}
