<?php

/**
 * WA Helper untuk mengirim pesan WhatsApp (Gateway)
 */

if (!function_exists('send_wa')) {
    function send_wa(string $no_hp, string $pesan)
    {
        $url = env('WA_API_URL');
        $token = env('WA_API_TOKEN');

        // Jika nomor HP kosong, abaikan
        if (empty($no_hp)) {
            return false;
        }

        // Format nomor HP ke standar internasional (62)
        if (substr($no_hp, 0, 1) == '0') {
            $no_hp = '62' . substr($no_hp, 1);
        } elseif (substr($no_hp, 0, 1) == '+') {
            $no_hp = substr($no_hp, 1);
        }

        // SIMULASI jika URL/Token kosong
        if (empty($url) || empty($token)) {
            $logMsg = "\n[WA SIMULATION] To: $no_hp\nMessage: $pesan\n------------------------\n";
            log_message('info', $logMsg);
            // Bisa juga ditulis ke file log khusus jika log bawaan CI ter-disable
            file_put_contents(WRITEPATH . 'logs/wa_simulation.log', date('Y-m-d H:i:s') . $logMsg, FILE_APPEND);
            return true; 
        }

        // Jika terkonfigurasi, panggil API (Contoh umum Fonnte / Wablas)
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $no_hp,
                'message' => $pesan,
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            log_message('error', "WA API Error: $err");
            return false;
        }

        return true;
    }
}
