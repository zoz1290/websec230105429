<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
 private $link = null;
 private $name = null;

 public function __construct($link, $name) {
 $this->link = $link; $this->name = $name;
 }
 
 public function content(): Content
 {
 return new Content(
 view: 'emails.verification',
 with: [ 'link' => $this->link,'name' => $this->name],
 );
 }
}

