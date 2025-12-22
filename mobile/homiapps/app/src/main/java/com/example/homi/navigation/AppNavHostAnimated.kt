package com.example.homi.navigation

import androidx.compose.animation.ExperimentalAnimationApi
import androidx.compose.animation.core.tween
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.animation.slideInVertically
import androidx.compose.animation.slideOutVertically
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavType
import androidx.navigation.navArgument
import com.google.accompanist.navigation.animation.AnimatedNavHost
import com.google.accompanist.navigation.animation.composable
import com.google.accompanist.navigation.animation.rememberAnimatedNavController

// Screens
import com.example.homi.ui.screens.SplashScreen
import com.example.homi.ui.screens.TampilanAwalScreen
import com.example.homi.ui.screens.TampilanAwalScreen2
import com.example.homi.ui.screens.TampilanAwalScreen3
import com.example.homi.ui.screens.LoginScreen
import com.example.homi.ui.screens.DaftarScreen
import com.example.homi.ui.screens.KonfirmasiDaftarScreen
import com.example.homi.ui.screens.DashboardScreen
import com.example.homi.ui.screens.DetailPengumumanScreen
import com.example.homi.ui.screens.PembayaranIuranScreen
import com.example.homi.ui.screens.FormAjuan1
import com.example.homi.ui.screens.FormPengaduanScreen
import com.example.homi.ui.screens.DetailRiwayatPengaduan
import com.example.homi.ui.screens.RiwayatDiterimaScreen
import com.example.homi.ui.screens.UbahKataSandiScreen
import com.example.homi.ui.screens.LupaKataSandiEmailScreen
import com.example.homi.ui.screens.ProsesPengajuanScreen
import com.example.homi.ui.screens.LaporkanMasalahScreen

import com.example.homi.data.local.TokenStore

import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.homi.ui.viewmodel.AuthViewModel
import com.example.homi.ui.viewmodel.AuthViewModelFactory
import com.example.homi.data.model.AnnouncementDto
import com.example.homi.ui.viewmodel.AnnouncementViewModel
import com.example.homi.ui.viewmodel.AnnouncementViewModelFactory


@OptIn(ExperimentalAnimationApi::class)
@Composable
fun AppNavHostAnimated(tokenStore: TokenStore) {
    val navController = rememberAnimatedNavController()

    AnimatedNavHost(
        navController = navController,
        startDestination = Routes.Splash
    ) {

        // =================== SPLASH & INTRO ===================

        composable(
            route = Routes.Splash,
            exitTransition = { fadeOut(tween(250)) },
            popEnterTransition = { fadeIn(tween(250)) }
        ) {
            SplashScreen(
                onSplashFinished = {
                    navController.navigate(Routes.TampilanAwal) {
                        popUpTo(Routes.Splash) { inclusive = true }
                    }
                }
            )
        }

        composable(
            route = Routes.TampilanAwal,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            TampilanAwalScreen(
                onNextClicked = {
                    navController.navigate(Routes.TampilanAwal2) {
                        launchSingleTop = true
                        popUpTo(Routes.TampilanAwal) { inclusive = true }
                    }
                }
            )
        }

        composable(
            route = Routes.TampilanAwal2,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            TampilanAwalScreen2(
                onNextClicked = {
                    navController.navigate(Routes.TampilanAwal3) {
                        launchSingleTop = true
                        popUpTo(Routes.TampilanAwal2) { inclusive = true }
                    }
                }
            )
        }

        composable(
            route = Routes.TampilanAwal3,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            TampilanAwalScreen3(
                onNextClicked = {
                    navController.navigate(Routes.Login) {
                        launchSingleTop = true
                        popUpTo(Routes.TampilanAwal3) { inclusive = true }
                    }
                }
            )
        }

        // =================== AUTH ===================

        composable(route = Routes.Login) {

            val authVm: AuthViewModel = viewModel(
                factory = AuthViewModelFactory(tokenStore)
            )

            LoginScreen(
                vm = authVm,
                onLoginSuccess = {
                    navController.navigate(Routes.Beranda) {
                        popUpTo(Routes.Login) { inclusive = true }
                        launchSingleTop = true
                    }
                },
                onRegisterClicked = { navController.navigate(Routes.Daftar) },
                onForgotPasswordClicked = { navController.navigate(Routes.LupaKataSandi) }
            )
        }


        composable(
            route = Routes.Daftar,
            enterTransition = { fadeIn(tween(250)) },
            exitTransition = { fadeOut(tween(200)) }
        ) {
            DaftarScreen(
                onGoLogin = {
                    navController.navigate(Routes.Login) {
                        launchSingleTop = true
                        popUpTo(Routes.Daftar) { inclusive = true }
                    }
                }
            )
        }

        composable(
            route = Routes.Konfirmasi,
            enterTransition = { fadeIn(tween(250)) },
            exitTransition = { fadeOut(tween(200)) }
        ) {
            KonfirmasiDaftarScreen(navController = navController)
        }

        composable(
            route = Routes.LupaKataSandi,
            enterTransition = { fadeIn(tween(250)) },
            exitTransition = { fadeOut(tween(200)) }
        ) {
            LupaKataSandiEmailScreen(
                onBack = { navController.popBackStack() },
                onOtpSent = { _ ->
                    navController.popBackStack(Routes.Login, inclusive = false)
                }
            )
        }

        // =================== BERANDA & FITUR ===================

        composable(
            route = Routes.Beranda,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {


            val annVm: AnnouncementViewModel = viewModel(
                factory = AnnouncementViewModelFactory(tokenStore)
            )


            DashboardScreen(
                annVm = annVm,
                onPengajuan = { navController.navigate(Routes.FormAjuan1) },
                onPengaduan = { navController.navigate(Routes.FormPengaduan) },
                onPembayaran = { navController.navigate(Routes.Pembayaran) },

                onDetailPengumumanClicked = { id ->
                    navController.navigate(Routes.detailPengumuman(id))
                },
                onRiwayatItemClick = { navController.navigate(Routes.DetailRiwayatPengaduan) },

                // kalau DashboardScreen kamu sudah tipe (StatusPengajuan) -> Unit,
                // pastikan navnya sesuai (kalau tidak pakai argumen, ganti jadi ignore)
                onRiwayatPengajuanItemClick = { _ ->
                    navController.navigate(Routes.DetailRiwayatPengajuan)
                },

                onUbahKataSandi = { navController.navigate(Routes.UbahKataSandi) },

                // ✅ INI YANG PENTING: tombol Laporkan Masalah di Akun (tab) jadi bisa navigate
                onLaporkanMasalah = { navController.navigate(Routes.LaporkanMasalah) },

                // (opsional) kalau tombol "Proses Pengajuan" di akun mau ke screen ini
                onProsesPengajuan = { navController.navigate(Routes.ProsesPengajuan) },

                onKeluarConfirmed = {
                    navController.navigate(Routes.Login) {
                        popUpTo(Routes.Beranda) { inclusive = true }
                    }
                }
            )
        }

        composable(
            route = Routes.DetailPengumuman,
            arguments = listOf(navArgument("id") { type = NavType.LongType })
        ) { backStackEntry ->
            val id = backStackEntry.arguments?.getLong("id") ?: return@composable

            // pakai VM yang sama (factory tokenStore)
            val annVm: AnnouncementViewModel = viewModel(
                factory = AnnouncementViewModelFactory(tokenStore)
            )

            // load detail pas masuk screen
            LaunchedEffect(id) { annVm.loadDetail(id) }

            val state by annVm.state.collectAsState()
            val data = state.detail

            if (data != null) {
                DetailPengumumanScreen(
                    announcement = data,
                    onBack = { navController.popBackStack() }
                )
            } else {
                // loading sederhana
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator()
                }
            }
        }



        composable(
            route = Routes.Pembayaran,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            PembayaranIuranScreen(onBack = { navController.popBackStack() })
        }

        // =================== FORM PENGAJUAN ===================

        composable(
            route = Routes.FormAjuan1,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            FormAjuan1(
                onBack = { navController.popBackStack() },
                onKonfirmasi = {
                    navController.navigate(Routes.ProsesPengajuan) { launchSingleTop = true }
                }
            )
        }

        composable(
            route = Routes.ProsesPengajuan,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            ProsesPengajuanScreen(
                onBack = { navController.popBackStack() },
                onWhatsappClick = { /* TODO */ }
            )
        }

        // =================== FORM PENGADUAN ===================

        composable(
            route = Routes.FormPengaduan,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            FormPengaduanScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { _, _, _, _ -> navController.popBackStack() }
            )
        }

        // =================== RIWAYAT ===================

        composable(
            route = Routes.DetailRiwayatPengaduan,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            DetailRiwayatPengaduan(onBack = { navController.popBackStack() })
        }

        composable(
            route = Routes.DetailRiwayatPengajuan,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            RiwayatDiterimaScreen(onBack = { navController.popBackStack() })
        }

        // =================== LAPORKAN MASALAH ===================

        composable(
            route = Routes.LaporkanMasalah,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            LaporkanMasalahScreen(
                onBack = { navController.popBackStack() },
                onGoAkun = {
                    // balik ke Dashboard (tab akun tetap ada di Dashboard)
                    navController.popBackStack()
                }
            )
        }

        // =================== UBAH KATA SANDI ===================

        composable(
            route = Routes.UbahKataSandi,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            UbahKataSandiScreen(
                onBack = { navController.popBackStack() },
                onSelesai = { navController.popBackStack() },
                onLupaKataSandi = { navController.navigate(Routes.LupaKataSandi) }
            )
        }
    }
}
