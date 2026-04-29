<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthenticationFeatureTest extends TestCase
{
    use RefreshDatabase; 

    /* ====================================================================
       TABEL 1: PENGUJIAN REGISTRASI MANUAL (RM)
       ==================================================================== */

    public function test_rm_01_validasi_input_kosong_saat_registrasi()
    {
        $response = $this->post('/register', []);

        // Mencari error di dalam bag khusus bernama 'register'
        $response->assertSessionHasErrors([
            'name', 'birthday', 'gender', 'phone_number', 'email', 'password', 'terms'
        ], null, 'register');
    }

    public function test_rm_02_validasi_format_email_salah()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'birthday' => '2000-01-01',
            'gender' => 'L',
            'phone_number' => '08123456789',
            'email' => 'emailtanpakeong.com', // Format salah
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['email'], null, 'register');
    }

    public function test_rm_03_validasi_email_sudah_terdaftar()
    {
        User::create([
            'name' => 'User Lama',
            'email' => 'sudahada@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/register', [
            'name' => 'User Baru',
            'birthday' => '2000-01-01',
            'gender' => 'L',
            'phone_number' => '08123456789',
            'email' => 'sudahada@gmail.com', 
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['email'], null, 'register');
    }

    public function test_rm_04_registrasi_sukses_dan_redirect_verifikasi()
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso Baru',
            'birthday' => '2000-01-01',
            'gender' => 'L',
            'phone_number' => '08123456789',
            'email' => 'budi.baru@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ]);

        $this->assertDatabaseHas('users', ['email' => 'budi.baru@gmail.com']);
        $this->assertAuthenticated();
        // Memastikan sistem mengarahkan ke halaman verifikasi sesuai controller
        $response->assertRedirect(route('verification.notice')); 
    }


    /* ====================================================================
       TABEL 3: PENGUJIAN LOGIN MANUAL (LM)
       ==================================================================== */

    public function test_lm_01_kredensial_salah()
    {
        $user = User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('passwordbenar'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@gmail.com',
            'password' => 'passwordsalah',
        ]);

        $this->assertGuest(); 
        $response->assertSessionHasErrors();
    }

    public function test_lm_02_proteksi_akun_belum_verifikasi()
    {
        // Membuat user tanpa verifikasi email
        $user = User::factory()->unverified()->create([
            'email' => 'belum.verif@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'belum.verif@gmail.com',
            'password' => 'password123',
        ]);

        // Karena controller Anda mengecek verifikasi di dalam fungsi login,
        // maka setelah login akan langsung ditendang ke notice.
        $response->assertRedirect(route('verification.notice'));
    }


    /* ====================================================================
       TABEL 5: PENGUJIAN LUPA & RESET PASSWORD (FP)
       ==================================================================== */

    public function test_fp_01_validasi_email_kosong_atau_salah()
    {
        $response = $this->post('/forgot_password', [
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_fp_02_email_tidak_terdaftar()
    {
        $response = $this->post('/forgot_password', [
            'email' => 'emailngasal@gmail.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_fp_03_kirim_link_sukses()
    {
        $user = User::factory()->create([
            'email' => 'valid.reset@gmail.com',
        ]);

        $response = $this->post('/forgot_password', [
            'email' => 'valid.reset@gmail.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status'); 
    }

    public function test_fp_04_reset_password_sukses()
    {
        $user = User::factory()->create([
            'email' => 'siap.reset@gmail.com',
            'password' => Hash::make('passwordlama'),
        ]);

        // Membuat token resmi dari sistem Laravel
        $token = app('auth.password.broker')->createToken($user);

        $response = $this->post('/reset_password', [
            'token' => $token,
            'email' => 'siap.reset@gmail.com',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('login'));
        
        // Pengecekan key 'success' sesuai Controller Anda
        $response->assertSessionHas('success'); 

        // Memastikan password di database benar-benar terganti
        $this->assertFalse(Hash::check('passwordlama', $user->fresh()->password));
        $this->assertTrue(Hash::check('passwordbaru123', $user->fresh()->password));
    }
}