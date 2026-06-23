<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class AuthController extends ResourceController
{
    protected $db;
    protected $title;
    protected $userModel;

    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        $this->title = 'Login';

        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['title'] = $this->title;
        return view('auth/login', $data);
    }

    public function login()
    {
        $session = session();
        $userModel = new userModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel
            ->select('tbl_m_users.*, tbl_m_role_akses.role, tbl_m_role_akses.akses_menu, tbl_m_role_akses.akses_submenu, tbl_m_role_akses.permissions')
            ->join('tbl_m_role_akses', 'tbl_m_role_akses.role_id = tbl_m_users.role')
            ->where('tbl_m_users.email', $email)
            ->where('tbl_m_users.status', 'Aktif')
            ->first();
        if (!$user) {
            setToast('error', ' Akun Anda belum aktif. Silakan hubungi administrator untuk aktivasi.');
            return redirect()->to('/login');
        }

        if (password_verify($password, $user['password'])) {
            $session->set([
                'user_id' => $user['user_id'],
                'nama_lengkap' => $user['nama_lengkap'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'akses_menu' => $user['akses_menu'],
                'akses_submenu' => $user['akses_submenu'],
                'permissions' => $user['permissions'],
                'logged_in' => true,
            ]);


            // Ambil semua data login aktif (belum logout)
            $now = date('Y-m-d H:i:s');
            $aktifLogins = $this->db->table('tbl_h_log_login')
                ->where('user_id', $user['user_id'])
                ->where('logout_time IS NULL')
                ->get()
                ->getResult();

            // Cek jika lebih dari 1 data belum logout
            if (count($aktifLogins) > 1) {
                // Update semua logout_time (misalnya hanya simpan agent dan IP logout terakhir saja)
                $this->db->table('tbl_h_log_login')
                    ->where('user_id', $user['user_id'])
                    ->where('logout_time IS NULL')
                    ->update([
                        'logout_time' => $now,
                        'updated_at' => $now,
                    ]);
            }

            // Simpan log login
            $this->db->table('tbl_h_log_login')->insert([
                'user_id'      => $user['user_id'],
                'login_ip'     => $this->request->getIPAddress(),
                'login_agent'  => $this->request->getUserAgent()->getBrowser() . ' - ' . $this->request->getUserAgent()->getVersion() . ' ' . $this->request->getUserAgent()->getPlatform(),
                'login_time'   => $now,
                'created_at'   => $now,
            ]);
            return redirect()->to('/home');
        } else {
            setToast('error', ' Login gagal. Periksa kembali email dan password Anda, atau hubungi dukungan jika mengalami kendala.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        // Ambil log terakhir dari user ini yang belum logout
        $lastLogin = $this->db->table('tbl_h_log_login')
            ->where('user_id', session()->get('user_id'))
            ->where('logout_time', null)
            ->orderBy('log_id', 'DESC')
            ->get()
            ->getRow();

        if ($lastLogin) {
            $now = date('Y-m-d H:i:s');
            $this->db->table('tbl_h_log_login')
                ->where('log_id', $lastLogin->log_id)
                ->update([
                    'logout_ip'    => $this->request->getIPAddress(),
                    'logout_agent' => $this->request->getUserAgent()->getAgentString(),
                    'logout_time'  =>  $now,
                    'updated_at'   =>  $now,
                ]);
        }

        session()->destroy();
        return redirect()->to('/login');
    }

    public function lupa_password()
    {
        $data['title'] = "Lupa Password";

        return view('auth/lupa_password', $data);
    }

    public function sendForgotPassword()
    {
        // 1. Ambil email tujuan dari input form
        $emailTujuan = $this->request->getPost('email');

        // check user
        $userModel = new userModel();
        $user = $userModel
            ->select('*')
            ->where('tbl_m_users.email', $emailTujuan)
            ->where('tbl_m_users.status', 'Aktif')
            ->where('tbl_m_users.deleted_at', null)
            ->first();
        if (!$user) {
            setToast('error', 'Permintaan gagal. Email tidak ditemukan atau akun Anda belum aktif.');
            return redirect()->to('/lupa-password');
        }

        // 2. Generate Kode OTP 6 Digit Acak
        $kodeOtp = rand(100000, 999999);

        // 3. Simpan OTP ke Session (Aktif selama 5 menit untuk verifikasi nanti)
        // Catatan: Di tingkat produksi, disarankan menyimpannya ke database beserta waktu kedaluwarsa.
        session()->set([
            'otp_email' => $emailTujuan,
            'otp_code'  => $kodeOtp,
            'otp_expires' => time() + 300 // Kedaluwarsa dalam 5 menit
        ]);

        $this->db->table('tbl_m_users')
            ->where('email', $emailTujuan)
            ->update([
                'otp' => $kodeOtp,
                'otp_expired' => date("Y-m-d H:i:s", strtotime("+5 minutes")),
            ]);

        // 4. Panggil Email Service CI4 & Atur Karakter Newline
        $email = \Config\Services::email();
        $config = config('Email');
        $config->CRLF    = "\r\n";
        $config->newline = "\r\n";
        $email->initialize($config);

        // 5. Set parameter email
        $email->setTo($emailTujuan);
        $email->setSubject('Kode Verifikasi OTP - Stock Management System');

        // 6. Tampilan Email HTML Baru Bertema OTP Profesional
        $message = "
        <div style='background-color: #f8fafc; padding: 40px 10px; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; min-height: 100%;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 550px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #e2e8f0;'>
                
                <tr>
                    <td bgcolor='#2563eb' style='padding: 30px 40px; text-align: center;'>
                        <h2 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;'>
                            Stock Management System
                        </h2>
                    </td>
                </tr>

                <tr>
                    <td style='padding: 40px 40px 30px 40px; text-align: left;'>
                        <h3 style='color: #1e293b; margin-top: 0; margin-bottom: 16px; font-size: 18px; font-weight: 600;'>
                            Halo,
                        </h3>
                        <p style='color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 24px 0;'>
                            Kami menerima permintaan pengaturan ulang kata sandi untuk akun Anda. Gunakan kode verifikasi OTP di bawah ini untuk melanjutkan:
                        </p>
                        
                        <table align='center' border='0' cellpadding='0' cellspacing='0' style='margin: 30px auto; background-color: #f1f5f9; border-radius: 12px;'>
                            <tr>
                                <td align='center' style='padding: 16px 40px; letter-spacing: 6px; font-size: 32px; font-weight: 800; color: #1e40af; font-family: Courier, monospace;'>
                                    " . $kodeOtp . "
                                </td>
                            </tr>
                        </table>

                        <p style='color: #ef4444; font-size: 14px; font-weight: 600; text-align: center; margin-bottom: 24px;'>
                            *Kode ini hanya berlaku selama 5 menit.
                        </p>

                        <p style='color: #64748b; font-size: 13px; line-height: 1.5; margin: 24px 0 0 0; padding-top: 20px; border-top: 1px solid #e2e8f0;'>
                            <strong>Keamanan:</strong> Jangan membagikan kode OTP ini kepada siapa pun, termasuk pihak yang mengatasnamakan Stock Management System. Jika Anda tidak merasa mengajukan ini, silakan abaikan email ini.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style='padding: 0 40px 40px 40px; text-align: center;'>
                        <p style='color: #94a3b8; font-size: 12px; margin: 0;'>
                            &copy; " . date('Y') . " Stock Management System. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </div>
        ";

        $email->setMessage($message);

        // 7. Proses pengiriman
        if ($email->send()) {
            session()->setFlashdata('toast', [
                'type' => 'success',
                'message' => 'Kode OTP berhasil dikirim! Silakan periksa kotak masuk atau folder spam Anda.'
            ]);

            // Alihkan ke halaman verifikasi OTP yang Anda miliki
            $email_enkrip = stringEncryptions('encrypt', $emailTujuan);
            return redirect()->to('/verify-otp?email=' . $email_enkrip);
        } else {
            session()->setFlashdata('toast', [
                'type' => 'error',
                'message' => 'Gagal mengirim email OTP. Silakan coba lagi nanti.'
            ]);
            return redirect()->back();
        }
    }

    public function verify_otp()
    {
        $email = stringEncryptions('decrypt', $this->request->getGet('email'));
        $data['title'] = "Verifikasi OTP";
        $data['email'] = $email;

        return view('auth/verifikasi_otp', $data);
    }

    public function resend_otp()
    {
        // Ambil dari query string ?email=
        $emailTujuan = $this->request->getPost('email');

        // 2. Generate Kode OTP 6 Digit Acak
        $kodeOtp = rand(100000, 999999);

        // 3. Simpan OTP ke Session (Aktif selama 5 menit untuk verifikasi nanti)
        // Catatan: Di tingkat produksi, disarankan menyimpannya ke database beserta waktu kedaluwarsa.

        $this->db->table('tbl_m_users')
            ->where('email', $emailTujuan)
            ->update([
                'otp' => $kodeOtp,
                'otp_expired' => date("Y-m-d H:i:s", strtotime("+5 minutes")),
            ]);

        // 4. Panggil Email Service CI4 & Atur Karakter Newline
        $email = \Config\Services::email();
        $config = config('Email');
        $config->CRLF    = "\r\n";
        $config->newline = "\r\n";
        $email->initialize($config);

        // 5. Set parameter email
        $email->setTo($emailTujuan);
        $email->setSubject('Kode Verifikasi OTP - Stock Management System');

        // 6. Tampilan Email HTML Baru Bertema OTP Profesional
        $message = "
        <div style='background-color: #f8fafc; padding: 40px 10px; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; min-height: 100%;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 550px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #e2e8f0;'>
                
                <tr>
                    <td bgcolor='#2563eb' style='padding: 30px 40px; text-align: center;'>
                        <h2 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;'>
                            Stock Management System
                        </h2>
                    </td>
                </tr>

                <tr>
                    <td style='padding: 40px 40px 30px 40px; text-align: left;'>
                        <h3 style='color: #1e293b; margin-top: 0; margin-bottom: 16px; font-size: 18px; font-weight: 600;'>
                            Halo,
                        </h3>
                        <p style='color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 24px 0;'>
                            Kami menerima permintaan pengaturan ulang kata sandi untuk akun Anda. Gunakan kode verifikasi OTP di bawah ini untuk melanjutkan:
                        </p>
                        
                        <table align='center' border='0' cellpadding='0' cellspacing='0' style='margin: 30px auto; background-color: #f1f5f9; border-radius: 12px;'>
                            <tr>
                                <td align='center' style='padding: 16px 40px; letter-spacing: 6px; font-size: 32px; font-weight: 800; color: #1e40af; font-family: Courier, monospace;'>
                                    " . $kodeOtp . "
                                </td>
                            </tr>
                        </table>

                        <p style='color: #ef4444; font-size: 14px; font-weight: 600; text-align: center; margin-bottom: 24px;'>
                            *Kode ini hanya berlaku selama 5 menit.
                        </p>

                        <p style='color: #64748b; font-size: 13px; line-height: 1.5; margin: 24px 0 0 0; padding-top: 20px; border-top: 1px solid #e2e8f0;'>
                            <strong>Keamanan:</strong> Jangan membagikan kode OTP ini kepada siapa pun, termasuk pihak yang mengatasnamakan Stock Management System. Jika Anda tidak merasa mengajukan ini, silakan abaikan email ini.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style='padding: 0 40px 40px 40px; text-align: center;'>
                        <p style='color: #94a3b8; font-size: 12px; margin: 0;'>
                            &copy; " . date('Y') . " Stock Management System. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </div>
        ";

        $email->setMessage($message);

        // 7. Proses pengiriman
        if ($email->send()) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Kode OTP berhasil dikirim! Silakan periksa kotak masuk atau folder spam Anda.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal mengirim email OTP. Silakan coba lagi nanti.'
            ]);
        }
    }

    public function check_otp()
    {
        $email = $this->request->getPost('email');
        $otp = $this->request->getPost('otp');

        $userModel = new userModel();
        $user = $userModel->where('email', $email)->first();

        // JIKA OTP SALAH
        if ($otp != $user['otp']) {
            // Set flashdata toast dengan tipe error
            session()->setFlashdata('toast', [
                'type'    => 'error',
                'message' => 'Kode OTP salah. Silakan periksa kembali email Anda.'
            ]);

            // Redirect kembali ke halaman form verifikasi
            return redirect()->to('/verify-otp?email=' . stringEncryptions('encrypt', $email))->withInput();
        }

        if (strtotime(date("Y-m-d H:i:s")) > strtotime($user['otp_expired'])) {
            setToast('error', 'Kode OTP telah kedaluwarsa. Silakan klik Kirim Ulang untuk mendapatkan kode baru.');
            return redirect()->to('/verify-otp?email=' . stringEncryptions('encrypt', $email));
        }


        $this->db->table('tbl_m_users')
            ->where('email', $email)
            ->update([
                'otp' => null,
                'otp_expired' => null,
            ]);


        session()->setFlashdata('toast', [
            'type' => 'success',
            'message' => 'Verifikasi berhasil!'
        ]);
        return redirect()->to('/update-password?email=' . stringEncryptions('encrypt', $email));
    }

    public function update_password()
    {
        $email = stringEncryptions('decrypt', $this->request->getGet('email'));
        $data['title'] = "Update Password";
        $data['email'] = $email;

        return view('auth/update_password', $data);
    }

    public function update_password_action()
    {
        // 1. Ambil data dari post form update password
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirm_password = $this->request->getPost('confirm_password');

        // Validasi dasar: pastikan email ada
        if (empty($email)) {
            session()->setFlashdata('toast', [
                'type'    => 'error',
                'message' => 'Sesi Anda telah habis. Silakan ulangi proses dari awal.'
            ]);
            return redirect()->to('/login');
        }

        // 2. Cek kecocokan password baru dan konfirmasinya (Proteksi Sisi Backend)
        if ($password !== $confirm_password) {
            session()->setFlashdata('toast', [
                'type'    => 'error',
                'message' => 'Konfirmasi password tidak cocok. Silakan periksa kembali.'
            ]);
            return redirect()->back()->withInput();
        }

        // 3. Cek apakah user memang ada dan aktif
        $userModel = new userModel();
        $user = $userModel->where('email', $email)
            ->where('status', 'Aktif')
            ->where('deleted_at', null)
            ->first();

        if (!$user) {
            session()->setFlashdata('toast', [
                'type'    => 'error',
                'message' => 'Akun tidak ditemukan atau sudah tidak aktif.'
            ]);
            return redirect()->to('/login');
        }

        // 4. Proses Update Password (Gunakan password_hash demi keamanan)
        // *Sesuaikan field 'password' di bawah dengan nama kolom password di tbl_m_users Anda*
        $updateStatus = $userModel->update($user['user_id'], [
            'password'    => password_hash($password, PASSWORD_DEFAULT),
            'otp'         => null,         // Pastikan dibersihkan total
            'otp_expired' => null          // Pastikan dibersihkan total
        ]);

        if ($updateStatus) {
            // Set flashdata sukses untuk halaman login
            session()->setFlashdata('toast', [
                'type'    => 'success',
                'message' => 'Password Anda berhasil diperbarui! Silakan login dengan password baru.'
            ]);
            return redirect()->to('/login');
        } else {
            session()->setFlashdata('toast', [
                'type'    => 'error',
                'message' => 'Gagal memperbarui password. Silakan coba beberapa saat lagi.'
            ]);
            return redirect()->back()->withInput();
        }
    }
}
