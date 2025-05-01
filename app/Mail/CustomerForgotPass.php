<?php

namespace App\Mail;


use Illuminate\Queue\SerializesModels;

class CustomerForgotPass extends BaseMail
{

    use Queueable, SerializesModels;

    // public $user_id, $token;
    // public $html;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $user_id, $token )
    {
        parent::__construct($user_id,$token);
	}

    /**
     * Build the message.
     *
     * @return $this
     */
	public function build()
	{
		$variables = (object) ['forgot_url' => route('reset.password.token', [app()->getLocale() ,  $this->token])];
		$this->template->body = $this->convert_email_variables($this->template->body, $this->user_id, $variables);

		$username = config("app.MAIL_FROM");
		$sender_name = config("app.MAIL_FROM_NAME");
		if( !empty( $this->store ) )
		{
			$transport = (new \Swift_SmtpTransport($this->store->smtp_host, $this->store->smtp_port))
			// ->setEncryption('ssl')
			->setUsername($this->store->smtp_username)
			->setPassword($this->store->smtp_password);

			$mailer = app(\Illuminate\Mail\Mailer::class);
			$mailer->setSwiftMailer(new \Swift_Mailer($transport));

			$username = $this->store->smtp_username;
			$sender_name = $this->store->smtp_sender_name;
		}

		return $this
            ->from($address = $username, $name = $sender_name)
            ->subject($this->template->subject)
            ->markdown(
                $this->html=(view('emails.site.reset_password',

                [
                    'body' => $this->template->body
                ]
             )->render()));
	}
}
