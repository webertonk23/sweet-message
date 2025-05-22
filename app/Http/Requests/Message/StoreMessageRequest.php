<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title"        => "required|string|max:255",
            "content"      => "required|string",
            "spotify_link" => "nullable|url",
            "images.*"     => "nullable|image|mimes:jpeg,png,jpg,gif|max:5120", // 5MB max
            "images"       => "nullable|array|max:5",
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'spotify_link.regex' => 'O link do Spotify deve ser um link válido de música, álbum ou playlist.',
            'images.max' => 'Você pode enviar no máximo 5 imagens.',
            'images.*.max' => 'Cada imagem deve ter no máximo 5MB.',
            'images.*.mimes' => 'As imagens devem ser do tipo: jpeg, png, jpg ou gif.',
        ];
    }
}
