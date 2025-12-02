package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.Dp
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

/* ===== Tokens ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueButton   = Color(0xFF4F8EA9)
private val AccentOrange = Color(0xFFE26A2C)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))
private val SuezOne     = FontFamily(Font(R.font.suez_one_regular))

@Composable
fun DashboardScreen(
    onPengajuan: (() -> Unit)? = null,
    onPengaduan: (() -> Unit)? = null,
    onPembayaran: (() -> Unit)? = null,
    onDetailPengumumanClicked: (() -> Unit)? = null,
) {
    Box(Modifier.fillMaxSize()) {

        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(BlueMain)
                .statusBarsPadding()
        ) {
            // Header
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp, vertical = 14.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Image(
                    painter = painterResource(R.drawable.icon_profile),
                    contentDescription = "Profil",
                    modifier = Modifier
                        .size(80.dp)
                        .clip(CircleShape)
                )
                Spacer(Modifier.width(12.dp))
                Column {
                    Text("Hai, Lily", fontFamily = PoppinsSemi, fontSize = 20.sp, color = Color.White)
                    Text("Selamat Datang di Homi", fontFamily = PoppinsSemi, fontSize = 20.sp, color = Color.White)
                    Text("Menghubungkan Warga, Membangun Kebersamaan", fontFamily = PoppinsReg, fontSize = 12.sp, color = Color.White)
                }
                Spacer(Modifier.weight(1f))
            }

            // Container putih
            Spacer(Modifier.height(10.dp))
            Card(
                modifier = Modifier.fillMaxSize(),
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 16.dp, vertical = 18.dp)
                ) {
                    Text(
                        text = "Pengumuman",
                        fontFamily = PoppinsSemi,
                        fontSize = 20.sp,
                        color = AccentOrange,
                        modifier = Modifier.fillMaxWidth(),
                        textAlign = TextAlign.Center
                    )

                    Spacer(Modifier.height(10.dp))

                    // Kartu Pengumuman
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(220.dp)
                            .clip(RoundedCornerShape(16.dp))
                    ) {
                        Image(
                            painter = painterResource(R.drawable.img_pengumuman),
                            contentDescription = "Kegiatan Gotong Royong",
                            contentScale = ContentScale.Crop,
                            modifier = Modifier.fillMaxSize()
                        )
                        Box(
                            modifier = Modifier
                                .matchParentSize()
                                .background(Color.Black.copy(alpha = 0.30f))
                        )
                        Text(
                            text = "Kegiatan Gotong Royong",
                            fontFamily = SuezOne,
                            color = Color.White,
                            fontSize = 18.sp,
                            textAlign = TextAlign.Center,
                            textDecoration = TextDecoration.Underline,
                            modifier = Modifier
                                .align(Alignment.TopCenter)
                                .padding(top = 10.dp)
                                .fillMaxWidth()
                                .clickable(enabled = onDetailPengumumanClicked != null) {
                                    onDetailPengumumanClicked?.invoke()
                                }
                        )
                        Text(
                            text = "Jumat/3 Sep 25\nArea Masjid\nSemua Warga\nPerumahan Hawai Garden",
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            color = Color.White,
                            modifier = Modifier
                                .align(Alignment.BottomStart)
                                .padding(12.dp)
                        )
                    }

                    Spacer(Modifier.height(18.dp))

                    // 3 Tombol Menu
                    MenuButton(icon = R.drawable.icon_pengajuan, title = "    Pengajuan Layanan", onClick = onPengajuan, iconSize = 48.dp)
                    Spacer(Modifier.height(14.dp))
                    MenuButton(icon = R.drawable.icon_pengaduan, title = "Pengaduan Warga", onClick = onPengaduan, iconSize = 48.dp)
                    Spacer(Modifier.height(14.dp))
                    MenuButton(icon = R.drawable.icon_pembayaran, title = "Pembayaran Iuran", onClick = onPembayaran, iconSize = 48.dp)
                    Spacer(Modifier.height(24.dp))
                }
            }
        }
    }
}

@Composable
private fun MenuButton(
    icon: Int,
    title: String,
    onClick: (() -> Unit)? = null,
    iconSize: Dp = 48.dp
) {
    val shape = RoundedCornerShape(16.dp)
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .height(72.dp)
            .clip(shape)
            .background(BlueButton)
            .clickable(enabled = onClick != null) { onClick?.invoke() }
            .padding(horizontal = 14.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Image(painter = painterResource(icon), contentDescription = title, modifier = Modifier.size(iconSize))
        Spacer(Modifier.width(14.dp))
        Text(text = title, fontFamily = PoppinsSemi, color = Color.White, fontSize = 16.sp)
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewDashboardBaru() {
    MaterialTheme { DashboardScreen() }
}
