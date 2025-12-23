package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.material3.CardColors
import androidx.compose.material3.CardElevation
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
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

/* ===== Model ===== */
private data class DirektoriItem(
    val nama: String,
    val alamat: String
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DirektoriScreen(
    onBack: (() -> Unit)? = null,
    @DrawableRes backIcon: Int = R.drawable.panahkembali // sesuaikan drawable kamu
) {
    var query by remember { mutableStateOf("") }

    // data dummy (nanti gampang diganti dari API)
    val data = remember { sampleDirektori }

    // filter lokal (search beneran jalan)
    val filtered = remember(query, data) {
        val q = query.trim().lowercase()
        if (q.isEmpty()) data
        else data.filter {
            it.nama.lowercase().contains(q) || it.alamat.lowercase().contains(q)
        }
    }

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
            // Back icon (optional)
            Box(
                modifier = Modifier
                    .size(28.dp)
                    .clickable(enabled = onBack != null) { onBack?.invoke() },
                contentAlignment = Alignment.Center
            ) {
                if (onBack != null) {
                    Image(
                        painter = painterResource(id = backIcon),
                        contentDescription = "Kembali",
                        modifier = Modifier.size(24.dp)
                    )
                } else {
                    // biar judul tetap center walau tanpa back
                    Spacer(Modifier.size(24.dp))
                }
            }

            Text(
                text = "Direktori",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )

            // slot kanan biar center bener
            Spacer(Modifier.size(28.dp))
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
        Spacer(Modifier.height(18.dp))
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

                // 🔍 Search beneran
                OutlinedTextField(
                    value = query,
                    onValueChange = { query = it },
                    singleLine = true,
                    shape = RoundedCornerShape(24.dp),
                    placeholder = { Text("Cari nama / blok / rumah...", fontFamily = PoppinsReg, fontSize = 13.sp, color = TextMuted) },
                    leadingIcon = { Text("🔍", fontSize = 16.sp) },
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = BlueBorder,
                        unfocusedBorderColor = BlueBorder,
                        cursorColor = BlueBorder
                    ),
                    modifier = Modifier.fillMaxWidth(),
                    textStyle = androidx.compose.ui.text.TextStyle(fontFamily = PoppinsReg, fontSize = 13.sp),
                )


                Spacer(Modifier.height(14.dp))

                // ===== TABEL (Header fixed + list scroll) =====
                Card(
                    modifier = Modifier.fillMaxSize(),
                    shape = RoundedCornerShape(10.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .border(1.dp, LineGray, RoundedCornerShape(10.dp))
                    ) {
                        // Header
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .background(AccentOrange)
                                .clip(RoundedCornerShape(topStart = 10.dp, topEnd = 10.dp))
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

                        // Rows scroll
                        LazyColumn(
                            modifier = Modifier
                                .fillMaxSize()
                        ) {
                            items(filtered) { item ->
                                TableRow(nama = item.nama, alamat = item.alamat)
                                Box(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .height(1.dp)
                                        .background(LineGray)
                                )
                            }

                            // kalau hasil kosong
                            if (filtered.isEmpty()) {
                                item {
                                    Box(
                                        modifier = Modifier
                                            .fillMaxWidth()
                                            .padding(18.dp),
                                        contentAlignment = Alignment.Center
                                    ) {
                                        Text(
                                            text = "Tidak ada hasil untuk \"$query\"",
                                            fontFamily = PoppinsReg,
                                            fontSize = 13.sp,
                                            color = TextMuted
                                        )
                                    }
                                }
                            }
                        }
                    }
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

/* ===== Sample data (nanti diganti API) ===== */
private val sampleDirektori = listOf(
    DirektoriItem("Awal Abyad", "Blok I No.3"),
    DirektoriItem("Biswan", "Blok AA1 No 10"),
    DirektoriItem("Sardinia", "Blok I No.3"),
    DirektoriItem("Muhammad Iwan", "Blok III0 No 1"),
    DirektoriItem("Irwansyah", "Blok I No.3"),
    DirektoriItem("Wawan Gustiar", "Blok AA1 No 10"),
    DirektoriItem("Irwan Baharuddin", "Blok I No.3"),
    DirektoriItem("Muhammad Wawan", "Blok AA1 No 10"),
)

@Preview(showBackground = true, showSystemUi = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun PreviewDirektori() {
    MaterialTheme { DirektoriScreen(onBack = {}) }
}
