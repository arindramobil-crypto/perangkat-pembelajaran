<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;

class Users extends BaseController
{
    // ══════════════════════════════════════════════
    // DATA GURU
    // ══════════════════════════════════════════════
    public function guru()
    {
        $guruModel = new GuruModel();
        return view('users/guru', [
            'title' => 'Data Guru',
            'gurus' => $guruModel->getGurus()
        ]);
    }

    public function save_guru()
    {
        $userModel = new UserModel();
        $guruModel = new GuruModel();
        $db        = \Config\Database::connect();

        $user_id  = $this->request->getVar('user_id');
        $username = $this->request->getVar('username');

        $db->transStart();

        if (empty($user_id)) {
            // ── TAMBAH BARU ─────────────────────
            if (!$this->validate(['username' => 'required|is_unique[users.username]'])) {
                return redirect()->to('/users/guru')->with('error', 'Username sudah digunakan!');
            }

            $userModel->save([
                'username'     => $username,
                'password'     => password_hash('guru123', PASSWORD_DEFAULT),
                'nama_lengkap' => $this->request->getVar('nama_lengkap'),
                'email'        => $this->request->getVar('email'),
                'role'         => 'Guru'
            ]);
            $newUserId = $userModel->insertID();

            $guruModel->save([
                'user_id'       => $newUserId,
                'nip'           => $this->request->getVar('nip'),
                'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
                'tempat_lahir'  => $this->request->getVar('tempat_lahir'),
                'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
                'alamat'        => $this->request->getVar('alamat'),
                'no_telp'       => $this->request->getVar('no_telp')
            ]);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->to('/users/guru')->with('error', 'Gagal menyimpan data.');
            }
            return redirect()->to('/users/guru')
                ->with('success', "Guru '{$username}' berhasil ditambahkan. Password default: guru123");

        } else {
            // ── EDIT ────────────────────────────
            $guru = $guruModel->find($user_id); // user_id di sini adalah gurus.id
            if (!$guru) {
                return redirect()->to('/users/guru')->with('error', 'Data tidak ditemukan.');
            }

            // Cek apakah username baru sudah dipakai orang lain
            $existingUser = $userModel->where('username', $username)
                                      ->where('id !=', $guru['user_id'])
                                      ->first();
            if ($existingUser) {
                return redirect()->to('/users/guru')->with('error', 'Username sudah digunakan pengguna lain.');
            }

            $updateUser = [
                'nama_lengkap' => $this->request->getVar('nama_lengkap'),
                'email'        => $this->request->getVar('email'),
                'username'     => $username,
            ];
            // Ganti password hanya jika diisi
            $newPass = $this->request->getVar('password_baru');
            if (!empty($newPass)) {
                if (strlen($newPass) < 6) {
                    return redirect()->to('/users/guru')->with('error', 'Password baru minimal 6 karakter.');
                }
                $updateUser['password'] = password_hash($newPass, PASSWORD_DEFAULT);
            }

            $userModel->update($guru['user_id'], $updateUser);
            $guruModel->update($user_id, [
                'nip'           => $this->request->getVar('nip'),
                'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
                'tempat_lahir'  => $this->request->getVar('tempat_lahir'),
                'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
                'alamat'        => $this->request->getVar('alamat'),
                'no_telp'       => $this->request->getVar('no_telp'),
            ]);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->to('/users/guru')->with('error', 'Gagal memperbarui data.');
            }
            return redirect()->to('/users/guru')->with('success', 'Data Guru berhasil diperbarui.');
        }
    }

    public function delete_guru($id)
    {
        $guruModel = new GuruModel();
        $userModel = new UserModel();
        $guru = $guruModel->find($id);
        if ($guru) {
            $userModel->delete($guru['user_id']);
            $guruModel->delete($id);
        }
        return redirect()->to('/users/guru')->with('success', 'Data Guru berhasil dihapus.');
    }

    // ══════════════════════════════════════════════
    // DATA SISWA
    // ══════════════════════════════════════════════
    public function siswa()
    {
        $siswaModel = new SiswaModel();
        return view('users/siswa', [
            'title'  => 'Data Siswa',
            'siswas' => $siswaModel->getSiswas()
        ]);
    }

    public function save_siswa()
    {
        $userModel  = new UserModel();
        $siswaModel = new SiswaModel();
        $db         = \Config\Database::connect();

        $user_id  = $this->request->getVar('user_id');
        $username = $this->request->getVar('username');

        $db->transStart();

        if (empty($user_id)) {
            // ── TAMBAH BARU ─────────────────────
            $rules = [
                'username' => 'required|is_unique[users.username]',
                'nis'      => 'required|is_unique[siswas.nis]'
            ];
            if (!$this->validate($rules)) {
                return redirect()->to('/users/siswa')->with('error', 'Username atau NIS sudah digunakan!');
            }

            $userModel->save([
                'username'     => $username,
                'password'     => password_hash('siswa123', PASSWORD_DEFAULT),
                'nama_lengkap' => $this->request->getVar('nama_lengkap'),
                'email'        => $this->request->getVar('email'),
                'role'         => 'Siswa'
            ]);
            $newUserId = $userModel->insertID();

            $siswaModel->save([
                'user_id'       => $newUserId,
                'nis'           => $this->request->getVar('nis'),
                'nisn'          => $this->request->getVar('nisn'),
                'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
                'tempat_lahir'  => $this->request->getVar('tempat_lahir'),
                'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
                'alamat'        => $this->request->getVar('alamat'),
                'no_telp'       => $this->request->getVar('no_telp')
            ]);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->to('/users/siswa')->with('error', 'Gagal menyimpan data.');
            }
            return redirect()->to('/users/siswa')
                ->with('success', "Siswa '{$username}' berhasil ditambahkan. Password default: siswa123");

        } else {
            // ── EDIT ────────────────────────────
            $siswa = $siswaModel->find($user_id); // siswas.id
            if (!$siswa) {
                return redirect()->to('/users/siswa')->with('error', 'Data tidak ditemukan.');
            }

            $existingUser = $userModel->where('username', $username)
                                      ->where('id !=', $siswa['user_id'])
                                      ->first();
            if ($existingUser) {
                return redirect()->to('/users/siswa')->with('error', 'Username sudah digunakan pengguna lain.');
            }

            // Cek apakah NIS baru sudah dipakai siswa lain
            $nis = $this->request->getVar('nis');
            $existingNis = $siswaModel->where('nis', $nis)->where('id !=', $user_id)->first();
            if ($existingNis) {
                return redirect()->to('/users/siswa')->with('error', 'NIS sudah digunakan siswa lain.');
            }

            $updateUser = [
                'nama_lengkap' => $this->request->getVar('nama_lengkap'),
                'email'        => $this->request->getVar('email'),
                'username'     => $username,
            ];
            $newPass = $this->request->getVar('password_baru');
            if (!empty($newPass)) {
                if (strlen($newPass) < 6) {
                    return redirect()->to('/users/siswa')->with('error', 'Password baru minimal 6 karakter.');
                }
                $updateUser['password'] = password_hash($newPass, PASSWORD_DEFAULT);
            }

            $userModel->update($siswa['user_id'], $updateUser);
            $siswaModel->update($user_id, [
                'nis'           => $nis,
                'nisn'          => $this->request->getVar('nisn'),
                'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
                'tempat_lahir'  => $this->request->getVar('tempat_lahir'),
                'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
                'alamat'        => $this->request->getVar('alamat'),
                'no_telp'       => $this->request->getVar('no_telp'),
            ]);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->to('/users/siswa')->with('error', 'Gagal memperbarui data.');
            }
            return redirect()->to('/users/siswa')->with('success', 'Data Siswa berhasil diperbarui.');
        }
    }

    public function delete_siswa($id)
    {
        $siswaModel = new SiswaModel();
        $userModel  = new UserModel();
        $siswa      = $siswaModel->find($id);
        if ($siswa) {
            $userModel->delete($siswa['user_id']);
            $siswaModel->delete($id);
        }
        return redirect()->to('/users/siswa')->with('success', 'Data Siswa berhasil dihapus.');
    }

    // ══════════════════════════════════════════════
    // PROFIL — Semua role bisa akses
    // Route: GET  /profil
    //        POST /profil/update
    // ══════════════════════════════════════════════
    public function profil()
    {
        $userModel  = new UserModel();
        $user       = $userModel->find(session()->get('id'));
        $role       = session()->get('role');

        $profileDetail = null;
        if ($role === 'Guru') {
            $guruModel    = new GuruModel();
            $profileDetail = $guruModel->where('user_id', $user['id'])->first();
        } elseif ($role === 'Siswa') {
            $siswaModel   = new SiswaModel();
            $profileDetail = $siswaModel->where('user_id', $user['id'])->first();
        }

        return view('users/profil', [
            'title'         => 'Profil Saya',
            'user'          => $user,
            'profileDetail' => $profileDetail,
        ]);
    }

    public function update_profil()
    {
        $userModel = new UserModel();
        $user_id   = session()->get('id');
        $role      = session()->get('role');

        // Validasi input dasar
        if (!$this->validate([
            'nama_lengkap' => 'required|min_length[3]',
            'email'        => 'permit_empty|valid_email',
        ])) {
            return redirect()->to('/profil')->with('error', implode(' ', $this->validator->getErrors()));
        }

        $update = [
            'nama_lengkap' => $this->request->getVar('nama_lengkap'),
            'email'        => $this->request->getVar('email'),
        ];

        // Ganti password jika diisi
        $passBaru = $this->request->getVar('password_baru');
        $passLama = $this->request->getVar('password_lama');
        if (!empty($passBaru)) {
            $user = $userModel->find($user_id);
            if (!password_verify($passBaru !== '' ? $passLama : '', $user['password'])) {
                // Jika password lama tidak cocok, tolak
                if (!password_verify($passLama, $user['password'])) {
                    return redirect()->to('/profil')->with('error', 'Password lama tidak sesuai.');
                }
            }
            if (strlen($passBaru) < 6) {
                return redirect()->to('/profil')->with('error', 'Password baru minimal 6 karakter.');
            }
            $update['password'] = password_hash($passBaru, PASSWORD_DEFAULT);
        }

        $userModel->update($user_id, $update);

        // Update nama_lengkap di session juga
        session()->set('nama_lengkap', $this->request->getVar('nama_lengkap'));

        // Update profil spesifik (opsional, field no_telp & alamat)
        if ($role === 'Guru') {
            $guruModel = new GuruModel();
            $guru = $guruModel->where('user_id', $user_id)->first();
            if ($guru) {
                $guruModel->update($guru['id'], [
                    'no_telp' => $this->request->getVar('no_telp'),
                    'alamat'  => $this->request->getVar('alamat'),
                ]);
            }
        } elseif ($role === 'Siswa') {
            $siswaModel = new SiswaModel();
            $siswa = $siswaModel->where('user_id', $user_id)->first();
            if ($siswa) {
                $siswaModel->update($siswa['id'], [
                    'no_telp' => $this->request->getVar('no_telp'),
                    'alamat'  => $this->request->getVar('alamat'),
                ]);
            }
        }

        return redirect()->to('/profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
