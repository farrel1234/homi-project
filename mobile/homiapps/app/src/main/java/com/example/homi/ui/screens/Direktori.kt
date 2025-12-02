package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

/* ===== Tokens (konsisten dengan screen lain) ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueBorder   = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFFF9966)
private val TextPrimary  = Color(0xFF0E0E0E)
private val TextMuted    = Color(0xFF8A8A8A)
private val LineGray     = Color(0xFFE6E6E6)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun DirektoriScreen(
    @DrawableRes backIcon: Int = R.drawable.panah // jika mau pakai di top bar nanti
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        /* ===== Header ===== */
        Spacer(Modifier.height(8.dp))
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Spacer(Modifier.width(24.dp)) // slot back icon; kosong agar judul center
            Text(
                text = "Direktori",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )
            Spacer(Modifier.width(24.dp))
        }

        Spacer(Modifier.height(6.dp))
        Text(
            text = "Berikut adalah nama dan alamat warga perumahan\nHawaii Garden",
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = Color.White,
            lineHeight = 18.sp,
            textAlign = TextAlign.Center,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp)
        )

        /* ===== White container ===== */
        Spacer(Modifier.height(24.dp))
        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(16.dp)
            ) {

                // 🔍 Kotak Cari (non-edit, tampilannya aja sesuai desain)
                Card(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(44.dp),
                    shape = RoundedCornerShape(24.dp),
                    border = BorderStroke(2.dp, BlueBorder),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                ) {
                    Box(
                        modifier = Modifier.fillMaxSize(),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            text = "🔍 Cari",
                            fontFamily = PoppinsSemi,
                            fontSize = 16.sp,
                            color = BlueBorder
                        )
                    }
                }

                Spacer(Modifier.height(16.dp))

                // 📋 Tabel
                DirektoriTable(
                    data = sampleDirektori
                )
            }
        }
    }
}

/* ===== Tabel ===== */

@Composable
private fun DirektoriTable(
    data: List<Pair<String, String>>
) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(8.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, LineGray, RoundedCornerShape(8.dp))
        ) {
            // Header
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(AccentOrange)
                    .clip(RoundedCornerShape(topStart = 8.dp, topEnd = 8.dp))
                    .height(IntrinsicSize.Min)
                    .padding(vertical = 10.dp, horizontal = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "Nama",
                    fontFamily = PoppinsSemi,
                    color = Color.White,
                    fontSize = 14.sp,
                    modifier = Modifier.weight(1f)
                )
                // divider vertikal
                Box(
                    modifier = Modifier
                        .fillMaxHeight()
                        .width(1.dp)
                        .background(Color.White.copy(alpha = 0.6f))
                )
                Text(
                    text = "Blok Alamat",
                    fontFamily = PoppinsSemi,
                    color = Color.White,
                    fontSize = 14.sp,
                    modifier = Modifier
                        .weight(1f)
                        .padding(start = 12.dp)
                )
            }

            // Rows
            LazyColumn {
                items(data) { (nama, alamat) ->
                    TableRow(nama, alamat)
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(1.dp)
                            .background(LineGray)
                    )
                }
            }
        }
    }
}

@Composable
private fun TableRow(
    nama: String,
    alamat: String
) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .heightIn(min = 52.dp)
            .padding(horizontal = 12.dp, vertical = 10.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = nama,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextPrimary,
            modifier = Modifier.weight(1f)
        )
        Box(
            modifier = Modifier
                .fillMaxHeight()
                .width(1.dp)
                .background(LineGray)
        )
        Text(
            text = alamat,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextPrimary,
            modifier = Modifier
                .weight(1f)
                .padding(start = 12.dp)
        )
    }
}

/* ===== Sample data (bisa diganti API/DB) ===== */
private val sampleDirektori = listOf(
    "Awal Abyad" to "Blok I No.3",
    "Biswan" to "Blok AA1 No 10",
    "Sardinia" to "Blok I No.3",
    "Muhammad Iwan" to "Blok iII0 No 1",
    "Irwansyah" to "Blok I No.3",
    "Wawan Gustiar" to "Blok AA1 No 10",
    "Irwan Baharuddin" to "Blok I No.3",
    "Muhammad Wawan" to "Blok AA1 No 10",
)

/* ===== Preview ===== */
@Preview(showBackground = true, showSystemUi = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun PreviewDirektori() {
    MaterialTheme { DirektoriScreen() }
}