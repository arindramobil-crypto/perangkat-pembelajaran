<?php
namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        helper(['form']);
        
        // Redirect to dashboard if already logged in
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login | Perangkat Pembelajaran SMK'
        ];
        
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]',
                'password' => 'required|min_length[5]|max_length[255]',
            ];
            
            if ($this->validate($rules)) {
                $userModel = new UserModel();
                $user = $userModel->where('username', $this->request->getVar('username'))->first();
                
                if ($user) {
                    if (password_verify($this->request->getVar('password'), $user['password'])) {
                        $sessionData = [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'nama_lengkap' => $user['nama_lengkap'],
                            'role' => $user['role'],
                            'foto' => $user['foto'],
                            'isLoggedIn' => TRUE
                        ];
                        session()->set($sessionData);
                        
                        // Coba kirim notifikasi instalasi (hanya 1x per server)
                        if ($user['role'] === 'Admin') {
                            $this->_sendTelemetry($user);
                        }

                        return redirect()->to('/dashboard');
                    } else {
                        session()->setFlashdata('msg', 'Password salah.');
                    }
                } else {
                    session()->setFlashdata('msg', 'Username tidak ditemukan.');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }
        
        return view('auth/login', $data);
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    /**
     * Mengirim notifikasi ke Google Sheets (Webhook) 1x saja per instalasi
     */
    private function _sendTelemetry($user)
    {
        $flagFile = WRITEPATH . 'installed.txt';
        $webhookUrl = env('TELEMETRY_WEBHOOK_URL');

        // Jika file sudah ada, atau URL Webhook belum diatur di .env, skip.
        if (file_exists($flagFile) || empty($webhookUrl)) {
            return;
        }

        try {
            $client = \Config\Services::curlrequest();
            $postData = [
                'tanggal' => date('Y-m-d H:i:s'),
                'url'     => base_url(),
                'role'    => $user['role'],
                'nama'    => $user['nama_lengkap']
            ];

            // Kirim request POST non-blocking (timeout 2 detik agar tidak menghambat login)
            $client->post($webhookUrl, [
                'form_params' => $postData,
                'timeout'     => 2.0,
                'http_errors' => false 
            ]);

            // Jika berhasil (meskipun requestnya timeout/gagal tapi mencoba jalan), 
            // buat file flag agar tidak dikirim berulang-ulang
            file_put_contents($flagFile, date('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            // Abaikan jika terjadi error jaringan (misal server lokal offline)
        }
    }
}
