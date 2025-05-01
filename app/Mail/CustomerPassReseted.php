<?php

namespace App\Mail;

use Illuminate\Queue\SerializesModels;

class CustomerPassReseted extends BaseMail
{
    use Queueable, SerializesModels;

    public $user_id;
    public $html;
    private $store;
    private $template ;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $user_id )
    {
        parent::__construct($user_id);
	}

    /**
     * Build the message.
     *
     * @return $this
     */
	public function build()
	{
		$variables = (object) [];
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
                $this->html=(view('emails.user.customer_pass_reseted',

                [
                    'request' => $this->template
                ]
            )->render()));
	}
}
