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
}
