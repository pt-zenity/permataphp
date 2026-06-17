<?php
/**
 * Global Authentication
 * Ubah logika ini sesuai kebutuhan produksi
 */
defined('main') or die('Restricted access');

class MVC_Authentication
{
    /**
     * Return true  = request diterima
     * Return false = request ditolak
     */
    public function Authentication(): bool
    {
        // Saat ini selalu diterima.
        // Tambahkan validasi sesuai kebutuhan:
        // - cek IP whitelist
        // - cek API Key global
        // - dsb.
        return true;
    }
}
