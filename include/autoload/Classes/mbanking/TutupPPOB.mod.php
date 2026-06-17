<?php
/**
 * TutupPPOB - Konstanta penutupan layanan sementara
 */
defined('main') or die('Restricted access');

class TutupPPOB
{
    // Response code untuk inquiry saat tutup
    const AKHIRTAHUN_RCRQ = '4042412';

    // Response code untuk payment saat tutup
    const AKHIRTAHUN_RCRS = '4042512';

    // Waktu mulai penutupan (unix timestamp) - 31 Des 2024 00:00 WIB
    const AKHIRTAHUN_FROMTIME = 1735635600;

    // Waktu selesai penutupan (unix timestamp) - 1 Jan 2025 12:00 WIB
    const AKHIRTAHUN_TOTIME = 1735768800;

    // Pesan penutupan lebaran
    const AKHIRTAHUN_MSG = 'Mohon maaf atas ketidaknyamanan yang dialami, dalam rangka libur lebaran layanan kami tutup sementara waktu. Terima kasih atas pengertian dan dukungan Anda.';

    // Pesan penutupan akhir tahun
    const AKHIRTAHUN_MSG_THNBARU = 'Mohon maaf atas ketidaknyamanan yang dialami, dalam rangka tutup buku akhir tahun layanan kami tutup sementara waktu. Terima kasih atas pengertian dan dukungan Anda dan selamat tahun baru.';
}
