<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $selectedFiles;
    /**
     * Create a new message instance.
     */
    public function __construct($selectedFiles)
    {
        $this->selectedFiles = $selectedFiles;
    }

    public function build()
    {
        $mail = $this->view('emails.test')
            ->with(['files' => $this->selectedFiles])
            ->attach($this->selectedFiles);

        $file = $this->selectedFiles;
        $mail->attach($file->getRealPath(), array(
            'as' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType()
        ));

        // later can be used for multiple files.
        // if (count($this->selectedFiles) > 0) {
        //     foreach ($this->selectedFiles as $file) {
        //         $mail->attach($file->getRealPath(), array(
        //             'as' => $file->getClientOriginalName(),
        //             'mime' => $file->getMimeType()
        //         ));
        //     }
        // }

        return $mail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Email',
        );
    }

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
