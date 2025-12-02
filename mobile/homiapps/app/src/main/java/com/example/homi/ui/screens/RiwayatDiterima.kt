package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
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
import com.example.homi.R

private val BlueMain = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFFF9966)
private val BorderBlue = Color(0xFF4D8FB0)
private val LabelGray = Color(0xFF444444)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

@Composable
fun RiwayatDiterimaScreen() {
    Box(modifier = Modifier.fillMaxSize()) {
        // Background gambar biru melengkung (dari layout lain)
        Image(
            painter = painterResource(R.drawable.bg_dashboard),
            contentDescription = null,
            contentScale = ContentScale.Crop,
            modifier = Modifier.fillMaxSize()
        )

        Column(
            modifier = Modifier
                .fillMaxSize()
                .statusBarsPadding()
                .navigationBarsPadding()
        ) {
            /* ===== Header ===== */
            Spacer(Modifier.height(8.dp))
            Text(
                text = "Riwayat Pengajuan",
                color = Color.White,
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp)
            )

            Spacer(Modifier.height(6.dp))
            Text(
                text = "Lihat status pengajuanmu di sini",
                color = Color.White.copy(alpha = 0.9f),
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp)
            )

            /* ===== Panel putih isi ===== */
            Spacer(Modifier.height(28.dp))
            Surface(
                color = Color.White,
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                tonalElevation = 0.dp,
                shadowElevation = 0.dp,
                modifier = Modifier.fillMaxSize()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(rememberScrollState())
                        .padding(horizontal = 20.dp, vertical = 18.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    // Jenis Pengajuan
                    Text(
                        text = "Jenis Pengajuan:",
                        fontFamily = PoppinsReg,
                        fontSize = 14.sp,
                        color = LabelGray
                    )
                    Text(
                        text = "Peminjaman Fasilitas",
                        fontFamily = PoppinsSemi,
                        fontSize = 16.sp,
                        color = AccentOrange
                    )

                    Spacer(modifier = Modifier.height(16.dp))

                    // Card isi dengan border biru
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .border(
                                width = 1.dp,
                                color = BorderBlue,
                                shape = RoundedCornerShape(10.dp)
                            )
                            .padding(horizontal = 16.dp, vertical = 14.dp)
                    ) {
                        ItemField(label = "Nama Pelapor", value = "Lily")
                        ItemField(label = "Tanggal", value = "1 Oktober 2025")
                        ItemField(label = "Tempat", value = "Masjid Perumahan Hawaii Garden")
                        ItemField(
                            label = "Perihal",
                            value = "Peminjaman fasilitas masjid untuk acara pengajian warga"
                        )
                        ItemField(
                            label = "Status",
                            value = "Diterima",
                            valueColor = Color(0xFF1BAE58)
                        )
                        ItemField(
                            label = "Catatan",
                            value = "Penggunaan fasilitas bangunan masjid untuk acara pengajian pada tanggal 1 Oktober 2025 akan diumumkan di dashboard Pengumuman."
                        )
                    }

                    Spacer(modifier = Modifier.height(32.dp))
                }
            }
        }
    }
}

/* ===== ItemField reusable ===== */
@Composable
fun ItemField(label: String, value: String, valueColor: Color = Color.Black) {
    Column(modifier = Modifier.fillMaxWidth()) {
        Text(
            text = label,
            fontFamily = PoppinsSemi,
            fontSize = 12.sp,
            color = BlueMain
        )
        Text(
            text = value,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = valueColor,
            lineHeight = 18.sp
        )
        Divider(color = Color(0xFFE5E7EB), thickness = 1.dp, modifier = Modifier.padding(vertical = 6.dp))
    }
}

/* ===== Preview ===== */
@Preview(showBackground = true, showSystemUi = true)
@Composable
fun PreviewRiwayatDiterimaScreen() {
    MaterialTheme {
        RiwayatDiterimaScreen()
    }
}
