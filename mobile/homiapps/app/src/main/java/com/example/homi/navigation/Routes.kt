// File: Routes.kt
package com.example.homi.navigation

object Routes {
    const val Splash = "splash"
    const val TampilanAwal = "tampilan_awal"
    const val TampilanAwal2 = "tampilan_awal_2"
    const val TampilanAwal3 = "tampilan_awal_3"

    const val Login = "login"
    const val Daftar = "daftar"
    const val Konfirmasi = "konfirmasi"

    // =======================
    // LUPA KATA SANDI (FLOW)
    // =======================
    const val LupaKataSandi = "lupa_kata_sandi" // email input
    const val LupaSandiOtp = "lupa_sandi_otp/{email}"
    const val GantiSandiBaru = "ganti_sandi_baru/{email}"

    fun lupaSandiOtpRoute(email: String): String {
        return "lupa_sandi_otp/${java.net.URLEncoder.encode(email, "UTF-8")}"
    }

    fun gantiSandiBaruRoute(email: String): String {
        return "ganti_sandi_baru/${java.net.URLEncoder.encode(email, "UTF-8")}"
    }

    // =======================
    // BERANDA & MENU LAINNYA
    // =======================
    // ✅ Beranda punya query param tab, supaya bisa balik ke tab AKUN tanpa hilang navbar
    const val Beranda = "beranda?tab={tab}"
    const val BerandaBase = "beranda"

    const val TAB_BERANDA = "BERANDA"
    const val TAB_DIREKTORI = "DIREKTORI"
    const val TAB_RIWAYAT = "RIWAYAT"
    const val TAB_AKUN = "AKUN"

    fun berandaRoute(tab: String = TAB_BERANDA): String {
        return "beranda?tab=$tab"
    }

    const val DetailPengumuman = "detail_pengumuman"

    // Pembayaran iuran
    const val TagihanIuran = "tagihan_iuran"
    const val PembayaranIuran = "pembayaran_iuran/{amount}/{bulan}"

    fun pembayaranIuranRoute(amount: String, bulan: String): String {
        return "pembayaran_iuran/${java.net.URLEncoder.encode(amount, "UTF-8")}/${java.net.URLEncoder.encode(bulan, "UTF-8")}"
    }

    const val FormAjuan1 = "form_ajuan_1"
    const val ProsesPengajuan = "proses_pengajuan"

    const val FormPengaduan = "form_pengaduan"
    const val PengaduanWarga = "pengaduan_warga"

    const val DetailRiwayatPengaduan = "detail_riwayat_pengaduan"
    const val DetailRiwayatPengajuan = "detail_riwayat_pengajuan"
    const val UbahKataSandi = "ubah_kata_sandi"
    const val LaporkanMasalah = "laporkan_masalah"
    const val ListProsesPengaduan = "list_proses_pengaduan"
}
