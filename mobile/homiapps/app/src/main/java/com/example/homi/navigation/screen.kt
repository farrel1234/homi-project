package com.example.homi.navigation

/**
 * Definisi rute navigasi aplikasi
 */
sealed class Screen(val route: String) {
    object Beranda : Screen("beranda")
    object Direktori : Screen("direktori")
    object Riwayat : Screen("riwayat")
    object Akun : Screen("akun")
}
