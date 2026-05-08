<?php



namespace App\Http\Controllers\Customer\Auth;



use App\Http\Controllers\Controller;

use App\Models\User;

use Illuminate\Auth\Events\Registered;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rules;

use Illuminate\View\View;



class RegisteredUserController extends Controller

{

    public function create(): View

    {

        return view('customer.pages.auth.register');

    }



    public function store(Request $request): RedirectResponse

    {

        // KITA KEMBALIKAN KE ATURAN STANDAR LARAVEL

        $request->validateWithBag('register', [

            'name'         => ['required', 'string', 'max:255'],

            'birthday'     => ['required', 'date'],

            'gender'       => ['required', 'in:L,P'],

            'phone_number' => ['required', 'string', 'max:15'],

            // FIX 1: Kembalikan validasi 'unique:'.User::class agar otomatis mengecek database

            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],

            'password'     => ['required', 'confirmed', Rules\Password::defaults()],

            'terms'        => ['accepted']

        ], [

            'name.required'         => 'Nama lengkap wajib diisi',

            'birthday.required'     => 'Tanggal lahir wajib diisi',

            'gender.required'       => 'Pilih jenis kelamin Anda',

            'phone_number.required' => 'Nomor handphone wajib diisi',

            'email.required'        => 'Email wajib diisi',

            'email.email'           => 'Format email tidak valid (pastikan menggunakan tanda @)',

            // FIX 2: Tambahkan pesan error kustom untuk validasi unique di sini

            'email.unique'          => 'Email ini sudah terdaftar',

            'password.required'     => 'Password wajib diisi',

            'password.confirmed'    => 'Password dan Konfirmasi password tidak cocok',

            'password.min'          => 'Password minimal harus :min karakter',

            'terms.accepted'        => 'Anda harus menyetujui kebijakan keamanan data'

        ]);



        // Jika lolos validasi (email belum dipakai), sistem langsung membuat akun baru

        $user = User::create([

            'name'         => $request->name,

            'birthday'     => $request->birthday,

            'gender'       => $request->gender,

            'phone_number' => '62' . ltrim($request->phone_number, '0'),

            'email'        => $request->email,

            'password'     => Hash::make($request->password),

        ]);



        event(new Registered($user));

        Auth::login($user);



        return redirect(route('verification.notice'));

    }

}