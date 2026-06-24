<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * XssFilter — Filter keamanan kustom untuk proteksi XSS.
 *
 * Cara kerja:
 *  1. Membersihkan seluruh input POST dari tag berbahaya (strip_tags + esc)
 *  2. Membersihkan parameter GET
 *  3. Menambahkan header keamanan tambahan
 *
 * Aktifkan di Filters.php:
 *   $aliases['xss'] = \App\Filters\XssFilter::class;
 *   $globals['before'][] = 'xss';
 */
class XssFilter implements FilterInterface
{
    /**
     * Daftar tag HTML yang MASIH DIBOLEHKAN melewati filter.
     * Kosongkan array ini untuk memblokir semua tag HTML.
     */
    private array $allowedTags = ['b', 'i', 'u', 'em', 'strong', 'br', 'p', 'ul', 'li', 'ol'];

    // ────────────────────────────────────────────────────────────
    public function before(RequestInterface $request, $arguments = null)
    {
        // Hanya proses request yang mengandung data input
        if (! $request instanceof \CodeIgniter\HTTP\IncomingRequest) {
            return;
        }

        // Bersihkan POST
        $post = $request->getPost();
        if (! empty($post)) {
            $request->setGlobal('post', $this->cleanArray($post));
        }

        // Bersihkan GET
        $get = $request->getGet();
        if (! empty($get)) {
            $request->setGlobal('get', $this->cleanArray($get));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tambahkan header keamanan XSS yang tidak di-cover SecureHeaders CI4
        $response->setHeader('X-XSS-Protection',        '1; mode=block');
        $response->setHeader('Referrer-Policy',           'strict-origin-when-cross-origin');
        $response->setHeader('Permissions-Policy',        'geolocation=(), microphone=(), camera=()');
    }

    // ── Helper: Bersihkan array secara rekursif ──────────────────
    private function cleanArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->cleanArray($value);
            } else {
                $data[$key] = $this->cleanString((string) $value);
            }
        }
        return $data;
    }

    // ── Helper: Bersihkan satu string ────────────────────────────
    private function cleanString(string $value): string
    {
        // 1. Hapus null bytes (serangan null byte injection)
        $value = str_replace("\0", '', $value);

        // 2. Decode HTML entity dulu (cegah double encoding bypass)
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 3. Strip semua tag kecuali yang diizinkan
        $allowedTagStr = '<' . implode('><', $this->allowedTags) . '>';
        $value = strip_tags($value, $allowedTagStr);

        // 4. Hapus event handler berbahaya (onclick, onerror, dll.)
        $value = preg_replace('/\bon\w+\s*=\s*["\']?[^"\']*["\']?/i', '', $value ?? '');

        // 5. Hapus javascript: dan data: scheme di atribut
        $value = preg_replace('/(javascript|data|vbscript)\s*:/i', 'blocked:', $value ?? '');

        return trim($value);
    }
}
