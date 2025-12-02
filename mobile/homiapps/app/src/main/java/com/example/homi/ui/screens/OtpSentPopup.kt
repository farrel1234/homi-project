package com.example.homi.ui.components

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.Text
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import kotlinx.coroutines.delay

@Composable
fun OtpSentPopup(
    onDismiss: () -> Unit
) {
    var isDismissEnabled by remember { mutableStateOf(false) } // hanya aktif setelah 5 detik
    val poppins = FontFamily(Font(R.font.poppins_semibold))

    // Jalankan timer
    LaunchedEffect(Unit) {
        delay(5_000) // setelah 5 detik baru bisa ditutup
        isDismissEnabled = true
        delay(5_000) // total 10 detik
        onDismiss()  // otomatis hilang setelah total 10 detik
    }

    // Latar belakang + konten popup
    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0x99000000))
            .clickable(enabled = isDismissEnabled) { onDismiss() },
        contentAlignment = Alignment.Center
    ) {
        Card(
            shape = RoundedCornerShape(20.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            modifier = Modifier
                .width(280.dp)
                .height(340.dp)
                .shadow(10.dp, RoundedCornerShape(20.dp), clip = false)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 16.dp, vertical = 32.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Spacer(modifier = Modifier.height(60.dp))

                Image(
                    painter = painterResource(id = R.drawable.surat),
                    contentDescription = "Surat Icon",
                    modifier = Modifier
                        .size(150.dp)
                        .padding(bottom = 12.dp)
                )

                Text(
                    text = "Berhasil Mengirim Kode OTP ke Email Anda!",
                    fontFamily = poppins,
                    fontSize = 14.sp,
                    fontWeight = FontWeight.Medium,
                    color = Color.Black,
                    textAlign = TextAlign.Center,
                    modifier = Modifier.padding(horizontal = 8.dp)
                )
            }
        }
        Box(
            modifier = Modifier
                .fillMaxSize(),
            contentAlignment = Alignment.Center
        ) {
            Box(
                modifier = Modifier
                    .offset(y = (-160).dp)
                    .size(100.dp),
                contentAlignment = Alignment.Center
            ) {
                Image(
                    painter = painterResource(id = R.drawable.background_hijau),
                    contentDescription = "Background Hijau",
                    contentScale = ContentScale.Fit,
                    modifier = Modifier.size(80.dp)
                )
                Image(
                    painter = painterResource(id = R.drawable.notif),
                    contentDescription = "Notif Icon",
                    modifier = Modifier.size(45.dp)
                )
            }
        }
    }
}

