<?php
/**
 * Generic Mailable class for creating mailable object.
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class GenericMailable. A single mailable class for every email.
 */
class GenericMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Subject of the mail
     *
     * @var string Subject of the mail
     */
    protected $msubject;

    /**
     * View template.
     *
     * @var string Template.
     */
    protected $mview;

     /**
      * Variables that will be passed to the template.
      *
      * @var array Vars used in template.
      */
    protected $view_data;

    /**
     * Attachment Data.
     *
     * @var array $attachment Attachment Data(url, name).
     */
    protected $attachment;


    /**
     * Create a new message instance.
     *
     * @param string $subject    Email subject.
     * @param string $view       View Template name.
     * @param array  $view_data  View Template vars.
     * @param array  $attachment Attachment vars.
     *
     * @return void
     */
    public function __construct(string $subject, string $view, array $view_data=[], array $attachment=[])
    {
        $this->msubject   = $subject;
        $this->mview      = $view;
        $this->view_data  = $view_data;
        $this->attachment = $attachment;

    }//end __construct()


    /**
     * Called while sending. It build the mailable object
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->msubject)->view($this->mview)->with(['view_data' => $this->view_data])->attachment();

    }//end build()


    /**
     * Add Attachment Data
     *
     * @return $this
     */
    private function attachment()
    {
        if (empty($this->attachment) === false) {
            return $this->attach(
                $this->attachment['url'],
                [
                    'as' => $this->attachment['name'],
                ]
            );
        }

        return $this;

    }//end attachment()


}//end class
