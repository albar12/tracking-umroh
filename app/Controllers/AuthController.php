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
}
