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
    const val LupaKataSandi = "lupa_kata_sandi"

    // Beranda (Dashboard) + shortcut ke tab Akun
    const val Beranda = "beranda"
    const val BerandaAkun = "beranda_akun"

    const val DetailPengumuman = "detail_pengumuman"
    const val Pembayaran = "pembayaran"

    // Pengajuan Surat
    const val FormAjuan1 = "form_ajuan_1"
    const val SuratDomisili = "surat_domisili"
    const val SuratPengantar = "surat_pengantar"
    const val SuratUsaha = "surat_usaha"
    const val SuratKematian = "surat_kematian"
    const val SuratBelumMenikah = "surat_belum_menikah"
    const val PengajuanSuratStatus = "pengajuan_surat_status"

    // Pengaduan
    const val FormPengaduan = "form_pengaduan"
    const val PengaduanWarga = "pengaduan_warga" // kalau masih dipakai, aman
    const val ProsesPengajuan = "proses_pengajuan" // dipakai untuk stepper pengaduan

    // Riwayat & lainnya
    const val DetailRiwayatPengaduan = "detail_riwayat_pengaduan"
    const val DetailRiwayatPengajuan = "detail_riwayat_pengajuan"
    const val UbahKataSandi = "ubah_kata_sandi"
    const val LaporkanMasalah = "laporkan_masalah"
    const val ListProsesPengaduan = "list_proses_pengaduan"
}
