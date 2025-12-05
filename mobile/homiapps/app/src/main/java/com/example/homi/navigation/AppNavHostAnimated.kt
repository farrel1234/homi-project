package com.example.homi.navigation

import androidx.compose.animation.ExperimentalAnimationApi
import androidx.compose.animation.core.tween
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.animation.slideInVertically
import androidx.compose.animation.slideOutVertically
import androidx.compose.runtime.Composable
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
import com.example.homi.ui.screens.UbahKataSandiPage   // ⬅️ NAMA BARU

@OptIn(ExperimentalAnimationApi::class)
@Composable
fun AppNavHostAnimated() {
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

        composable(
            route = Routes.Login,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            LoginScreen(
                onLoginSuccess = {
                    navController.navigate(Routes.Beranda) {
                        popUpTo(Routes.Login) { inclusive = true }
                        launchSingleTop = true
                    }
                },
                onRegisterClicked = {
                    navController.navigate(Routes.Daftar)
                },
                onForgotPasswordClicked = { }
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

        // =================== BERANDA & FITUR ===================

        composable(
            route = Routes.Beranda,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            DashboardScreen(
                onPengajuan = {
                    navController.navigate(Routes.FormAjuan1)
                },
                onPengaduan = {
                    navController.navigate(Routes.FormPengaduan)
                },
                onPembayaran = {
                    navController.navigate(Routes.Pembayaran)
                },
                onDetailPengumumanClicked = {
                    navController.navigate(Routes.DetailPengumuman)
                },
                onRiwayatItemClick = {
                    navController.navigate(Routes.DetailRiwayatPengaduan)
                },
                onRiwayatPengajuanItemClick = {
                    navController.navigate(Routes.DetailRiwayatPengajuan)
                },
                onUbahKataSandi = {
                    navController.navigate(Routes.UbahKataSandi)
                },
                onKeluarConfirmed = {
                    navController.navigate(Routes.Login) {
                        popUpTo(Routes.Beranda) { inclusive = true }
                    }
                }
            )
        }

        composable(
            route = Routes.DetailPengumuman,
            enterTransition = {
                slideInVertically(
                    animationSpec = tween(320),
                    initialOffsetY = { fullHeight -> fullHeight / 3 }
                ) + fadeIn(animationSpec = tween(320))
            },
            exitTransition = { fadeOut(animationSpec = tween(200)) },
            popEnterTransition = { fadeIn(animationSpec = tween(220)) },
            popExitTransition = {
                slideOutVertically(
                    animationSpec = tween(260),
                    targetOffsetY = { fullHeight -> fullHeight / 3 }
                ) + fadeOut(animationSpec = tween(260))
            }
        ) {
            DetailPengumumanScreen()
        }

        composable(
            route = Routes.Pembayaran,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            PembayaranIuranScreen(
                onBack = { navController.popBackStack() }
            )
        }

        // =================== FORM PENGAJUAN LAYANAN ===================

        composable(
            route = Routes.FormAjuan1,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            FormAjuan1(
                onBack = { navController.popBackStack() },
                onKonfirmasi = {
                    navController.popBackStack()
                }
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
                onKonfirmasi = { _, _, _, _ ->
                    navController.popBackStack()
                }
            )
        }

        // =================== DETAIL RIWAYAT PENGADUAN ===================

        composable(
            route = Routes.DetailRiwayatPengaduan,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            DetailRiwayatPengaduan(
                onBack = { navController.popBackStack() }
            )
        }

        // =================== DETAIL RIWAYAT PENGAJUAN ===================

        composable(
            route = Routes.DetailRiwayatPengajuan,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            RiwayatDiterimaScreen(
                onBack = { navController.popBackStack() }
            )
        }

        // =================== UBAH KATA SANDI ===================

        composable(
            route = Routes.UbahKataSandi,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            UbahKataSandiPage(                          // ⬅️ PAKAI NAMA BARU
                onBack = { navController.popBackStack() },
                onSelesai = { navController.popBackStack() },
                onLupaKataSandi = {
                    // flow lupa sandi nanti taruh di sini
                }
            )
        }
    }
}
