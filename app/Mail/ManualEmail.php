<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class ManualEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $selectedFiles;
    protected $template;

    /**
     * Create a new message instance.
     */
    public function __construct($template, $subject, $fromName, $mailSettings, $selectedFiles = null)
    {
        $this->selectedFiles = $selectedFiles;
        $this->subject = $subject;
        $this->template = $template;

        $this->from = [['address' => $mailSettings->FromEmail, 'name' => $fromName]];
        $this->bcc = [['address' => $mailSettings->BCCEmail, 'name' => $mailSettings->BCCEmail]];
    }

    public function build()
    {
        \Log::info('TestEmail.Build()' . $this->template);
        $mail = $this->view('emails.test', ['content' => $this->template]);

        if (!is_null($this->selectedFiles)) {
            $mail->attach($this->selectedFiles->getRealPath(), array(
                'as' => $this->selectedFiles->getClientOriginalName(),
                'mime' => $this->selectedFiles->getMimeType()
            ));
        }

        return $mail;
        // ->with(['htmlContent' => $this->template])



        // $mail = $this->view('emails.test')
        //     ->with([
        //         'files' => $this->selectedFiles,
        //         'htmlContent' => $this->template
        //     ])
        //     ->attach($this->selectedFiles);

        // $file = $this->selectedFiles;
        // $mail->attach($file->getRealPath(), array(
        //     'as' => $file->getClientOriginalName(),
        //     'mime' => $file->getMimeType()
        // ));
        // $mail->subject($this->subject);

        // later can be used for multiple files.
        // if (count($this->selectedFiles) > 0) {
        //     foreach ($this->selectedFiles as $file) {
        //         $mail->attach($file->getRealPath(), array(
        //             'as' => $file->getClientOriginalName(),
        //             'mime' => $file->getMimeType()
        //         ));
        //     }
        // }

        // return $mail;
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Test Email',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.test',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
