<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Izinkan request ini.
     */
    public function authorize(): bool
    {
        // user yang sedang login boleh update profilnya sendiri
        return true;
    }

    /**
     * Normalisasi input sebelum validasi (biar tidak gagal karena kapital/spasi).
     */
    protected function prepareForValidation(): void
    {
        $email = $this->input('email');
        $name  = $this->input('name');
        $phone = $this->input('phone');

        $this->merge([
            'email' => is_string($email) ? mb_strtolower(trim($email)) : $email,
            'name'  => is_string($name)  ? trim($name)  : $name,
            'phone' => is_string($phone) ? trim($phone) : $phone,
        ]);
    }

    /**
     * Aturan validasi.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            // Sesuai form: NIM dipakai mahasiswa, NIP dipakai admin/superadmin.
            // Tetap nullable supaya tidak wajib diisi pada role yang tak memakainya.
            'nim'     => ['nullable', 'string', 'max:20', Rule::unique('users', 'nim')->ignore($userId)],
            'nip'     => ['nullable', 'string', 'max:20', Rule::unique('users', 'nip')->ignore($userId)],

            // Opsi tambahan: batasi karakter phone agar lebih rapi (boleh dihapus kalau tak diperlukan)
            'phone'   => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-\(\)]+$/'],
            'address' => ['nullable', 'string'],

            // Avatar: hanya jpg/jpeg/png, max 2MB
            'avatar'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * Pesan error kustom.
     */
    public function messages(): array
    {
        return [
            'name.required'   => 'Nama wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'email.unique'    => 'Email sudah digunakan.',
            'nim.unique'      => 'NIM sudah digunakan.',
            'nip.unique'      => 'NIP sudah digunakan.',
            'phone.max'       => 'Nomor telepon maksimal :max karakter.',
            'phone.regex'     => 'Nomor telepon hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
            'avatar.image'    => 'File harus berupa gambar.',
            'avatar.mimes'    => 'Avatar harus berformat JPG, JPEG, atau PNG.',
            'avatar.max'      => 'Ukuran avatar maksimal 2MB.',
        ];
    }

    /**
     * Nama atribut yang lebih ramah (opsional).
     */
    public function attributes(): array
    {
        return [
            'name'    => 'nama',
            'email'   => 'email',
            'nim'     => 'NIM',
            'nip'     => 'NIP',
            'phone'   => 'nomor telepon',
            'address' => 'alamat',
            'avatar'  => 'avatar',
        ];
    }
}
