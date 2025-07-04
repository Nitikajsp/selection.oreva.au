<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\HelloMail;

class MailController extends Controller
{
    public function sendHelloEmail()
    {
        // Replace with the recipient's email address
        $recipientEmail = 'miteshdalsaniya@jspinfotech.com';

        // Send the email
        Mail::to($recipientEmail)->send(new HelloMail());

        return 'Hello email sent successfully!';
    }
}
    