<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Validator;
class MessageRequest extends FormRequest
{
    public function messageAdd($data)
    {
        $validator = Validator::make($data,[
            'subject' => 'required',
            'message' => 'required',
            'recipients' => 'required'
        ]);
        return $validator;
    }
}
