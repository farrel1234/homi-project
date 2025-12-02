package com.example.homi.ui.screens

import androidx.compose.animation.core.*
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowForward
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.scale
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.zIndex
import androidx.navigation.NavController
import androidx.navigation.compose.rememberNavController
import com.example.homi.R
import com.example.homi.ui.components.OtpExpiredPopup
import com.example.homi.ui.components.OtpSuccessPopup
import kotlinx.coroutines.delay

// ====================
// ROUTE DEFINITIONS
// ====================
object Routes {
    const val Login = "login"
    const val Konfirmasi = "konfirmasi"
    const val Beranda = "beranda"
}

// ====================
// MAIN SCREEN
// ====================
@Composable
fun KonfirmasiDaftarScreen(
    navController: NavController
) {
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val poppinsRegular = FontFamily(Font(R.font.poppins_regular))

    var otpCode by remember { mutableStateOf("") }

    // 🔹 Animasi amplop berdenyut
    val infiniteTransition = rememberInfiniteTransition()
    val scaleAnim by infiniteTransition.animateFloat(
        initialValue = 1f,
        targetValue = 1.15f,
        animationSpec = infiniteRepeatable(
            animation = tween(durationMillis = 800, easing = EaseInOutCubic),
            repeatMode = RepeatMode.Reverse
        )
    )

    // 🔹 Countdown kirim ulang
    var remainingTime by remember { mutableStateOf(30) }
    var isCounting by remember { mutableStateOf(true) }

    LaunchedEffect(isCounting) {
        if (isCounting) {
            while (remainingTime > 0) {
                delay(1000)
                remainingTime--
            }
            isCounting = false
        }
    }

    // 🔹 Popup state
    var showExpiredPopup by remember { mutableStateOf(false) }
    var showSuccessPopup by remember { mutableStateOf(false) }

    // Popup otomatis saat waktu habis
    LaunchedEffect(remainingTime) {
        if (remainingTime == 0) showExpiredPopup = true
    }

    // 🔹 Verifikasi otomatis (contoh: 123456 benar)
    var isVerifying by remember { mutableStateOf(false) }
    var hasVerified by remember { mutableStateOf(false) }

    LaunchedEffect(otpCode) {
        if (otpCode.length == 6 && !isVerifying && !hasVerified) {
            isVerifying = true
            delay(400) // simulasi API
            val ok = otpCode == "123456"
            if (ok) showSuccessPopup = true else showExpiredPopup = true
            hasVerified = true
            isVerifying = false
        } else if (otpCode.length < 6) {
            hasVerified = false
        }
    }

    val minutes = remainingTime / 60
    val seconds = remainingTime % 60
    val timeText = String.format("%02d.%02d", minutes, seconds)

    // ====================
    // UI
    // ====================
    Box(Modifier.fillMaxSize()) {
        // Background
        Image(
            painter = painterResource(id = R.drawable.konfirmasi_pendaftaran),
            contentDescription = "Background Konfirmasi",
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Crop
        )

        // Konten utama
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Spacer(Modifier.height(40.dp))
            Text(
                text = "Konfirmasi Pendaftaran",
                fontFamily = poppins,
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold,
                color = Color.White
            )

            Spacer(Modifier.height(16.dp))
            Image(
                painter = painterResource(id = R.drawable.amplop),
                contentDescription = "Amplop Icon",
                modifier = Modifier
                    .size(210.dp)
                    .scale(scaleAnim)
            )

            Spacer(Modifier.height(55.dp))

            Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.fillMaxWidth()) {
                Text(
                    text = "Kode OTP",
                    fontFamily = poppins,
                    fontSize = 19.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color.Black,
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(start = 20.dp),
                    textAlign = TextAlign.Start
                )

                Spacer(Modifier.height(8.dp))

                OutlinedTextField(
                    value = otpCode,
                    onValueChange = { otpCode = it.take(6) },
                    singleLine = true,
                    shape = RoundedCornerShape(8.dp),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = Color(0xFF2E6F8E),
                        unfocusedBorderColor = Color(0xFF2E6F8E),
                        focusedContainerColor = Color(0xFFF5F5F5),
                        unfocusedContainerColor = Color(0xFFF5F5F5),
                        cursorColor = Color(0xFF2E6F8E)
                    ),
                    modifier = Modifier
                        .fillMaxWidth(0.90f)
                        .align(Alignment.CenterHorizontally)
                )

                Spacer(Modifier.height(6.dp))
                Text(
                    text = "*Masukkan kode yang sudah dikirimkan ke alamat Email Anda",
                    fontFamily = poppinsRegular,
                    fontWeight = FontWeight.Bold,
                    fontSize = 12.sp,
                    color = Color.Gray,
                    textAlign = TextAlign.Start,
                    modifier = Modifier
                        .fillMaxWidth(0.90f)
                        .align(Alignment.CenterHorizontally)
                        .padding(start = 12.dp)
                )

                Spacer(Modifier.height(38.dp))

                Text(
                    text = if (isCounting) "Kirim Ulang Kode : $timeText" else "Kirim Ulang Kode",
                    fontFamily = poppins,
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Medium,
                    color = if (isCounting) Color.Gray else Color(0xFF2F7FA3),
                    textAlign = TextAlign.End,
                    modifier = Modifier
                        .fillMaxWidth(0.90f)
                        .align(Alignment.CenterHorizontally)
                        .padding(end = 2.dp)
                        .clickable(enabled = !isCounting) {
                            remainingTime = 30
                            isCounting = true
                            // TODO: panggil API resend OTP di sini
                        }
                )
            }
        }

        // === Ikon Panah tanpa overlay ===
        Box(
            modifier = Modifier
                .fillMaxSize(),
            contentAlignment = Alignment.BottomCenter
        ) {
            Icon(
                imageVector = Icons.Filled.ArrowForward,
                contentDescription = "Lanjut ke Beranda",
                tint = Color.White,
                modifier = Modifier
                    .offset(x = 9.dp, y = (-195).dp) // ⬅️ geser kanan 12dp, naik 195dp
                    .size(49.dp)
                    .clickable {
                        navController.navigate(Routes.Beranda) {
                            popUpTo(Routes.Konfirmasi) { inclusive = true }
                            launchSingleTop = true
                        }
                    }
            )
        }

        // ====================
        // Popups (auto-close 2 detik)
        // ====================
        if (showExpiredPopup) {
            OtpExpiredPopup()
            LaunchedEffect(Unit) {
                delay(2000)
                showExpiredPopup = false
            }
        }

        if (showSuccessPopup) {
            OtpSuccessPopup()
            LaunchedEffect(Unit) {
                delay(2000)
                showSuccessPopup = false
                navController.navigate(Routes.Login) {
                    popUpTo(Routes.Konfirmasi) { inclusive = true }
                    launchSingleTop = true
                }
            }
        }
    }
}

// ====================
// PREVIEW
// ====================
@Preview(showSystemUi = true)
@Composable
fun PreviewKonfirmasiDaftarScreen() {
    val nav = rememberNavController()
    MaterialTheme {
        KonfirmasiDaftarScreen(navController = nav)
    }
}
