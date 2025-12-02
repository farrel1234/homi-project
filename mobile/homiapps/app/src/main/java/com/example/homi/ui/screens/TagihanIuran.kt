package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.mutableStateListOf
import androidx.compose.runtime.remember
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

/* ===== THEME COLORS ===== */
private val BlueMain = Color(0xFF2F7FA3)
private val BlueBorder = Color(0xFF2F7FA3)
private val BlueText = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFFF9966)
private val RowBg = Color(0xFFF7F7F7)
private val TextDark = Color(0xFF0E0E0E)
private val PaidGreen = Color(0xFF2EAD67)
private val LineGray = Color(0xFFE6E6E6)

/* ===== FONTS ===== */
private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

/* ===== DATA CLASSES ===== */
data class TagihanItem(val bulan: String, val nominal: String, val paid: Boolean)
data class TagihanTahun(val label: String, val items: List<TagihanItem>)

/* ===== SCREEN ===== */
@Composable
fun TagihanIuranScreen(
    @DrawableRes backIcon: Int = R.drawable.panah,
    onBack: (() -> Unit)? = null,
    onBayarClick: ((tahun: String, item: TagihanItem) -> Unit)? = null
) {
    val data = remember {
        mutableStateListOf(
            TagihanTahun(
                "IPL 2025",
                listOf(
                    TagihanItem("Agustus 2025", "Rp. 25.000", false),
                    TagihanItem("Juli 2025", "Rp. 25.000", false),
                    TagihanItem("Juni 2025", "Rp. 25.000", true),
                    TagihanItem("Mei 2025", "Rp. 25.000", true),
                    TagihanItem("April 2025", "Rp. 25.000", true),
                    TagihanItem("Maret 2025", "Rp. 25.000", true),
                    TagihanItem("Februari 2025", "Rp. 25.000", true),
                    TagihanItem("Januari 2025", "Rp. 25.000", true),
                )
            ),
            TagihanTahun(
                "IPL 2024",
                listOf(
                    TagihanItem("Desember 2024", "Rp. 25.000", true),
                    TagihanItem("November 2024", "Rp. 25.000", true)
                )
            )
        )
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        /* ===== TOP BAR ===== */
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            IconButton(
                onClick = { onBack?.invoke() },
                colors = IconButtonDefaults.iconButtonColors(contentColor = Color.White)
            ) {
                Icon(painterResource(backIcon), contentDescription = "Kembali")
            }
            Text(
                text = "Tagihan",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )
            Spacer(Modifier.width(40.dp))
        }

        /* ===== SUBTITLE ===== */
        Text(
            text = "Segera membayar tagihan iuran yang tersedia",
            fontFamily = PoppinsReg,
            fontSize = 14.sp,
            color = Color.White,
            textAlign = TextAlign.Center,
            lineHeight = 18.sp,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp)
        )

        /* ===== WHITE CONTAINER ===== */
        Spacer(Modifier.height(12.dp))
        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            border = BorderStroke(2.dp, BlueBorder),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(16.dp)
            ) {
                Text(
                    text = "Iuran Wajib",
                    fontFamily = PoppinsSemi,
                    fontSize = 16.sp,
                    color = BlueText,
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Center
                )

                Spacer(Modifier.height(12.dp))

                LazyColumn(
                    verticalArrangement = Arrangement.spacedBy(14.dp),
                    modifier = Modifier.fillMaxSize()
                ) {
                    items(data.size) { idx ->
                        TahunSection(
                            tahun = data[idx].label,
                            items = data[idx].items,
                            onBayarClick = { item -> onBayarClick?.invoke(data[idx].label, item) }
                        )
                    }
                }
            }
        }
    }
}

/* ===== TAHUN SECTION ===== */
@Composable
private fun TahunSection(
    tahun: String,
    items: List<TagihanItem>,
    onBayarClick: (TagihanItem) -> Unit
) {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .border(1.dp, LineGray, RoundedCornerShape(10.dp))
            .padding(12.dp)
    ) {
        // Header Oranye
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .clip(RoundedCornerShape(10.dp))
                .background(AccentOrange)
                .padding(vertical = 10.dp, horizontal = 14.dp)
        ) {
            Text(
                text = tahun,
                fontFamily = PoppinsSemi,
                fontSize = 14.sp,
                color = Color.White
            )
        }

        Spacer(Modifier.height(10.dp))

        // List Rows
        items.forEachIndexed { i, item ->
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(if (i % 2 == 0) RowBg else Color.White)
                    .padding(horizontal = 12.dp, vertical = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = item.bulan,
                    fontFamily = PoppinsReg,
                    fontSize = 13.sp,
                    color = TextDark,
                    modifier = Modifier.weight(1.2f)
                )
                Text(
                    text = item.nominal,
                    fontFamily = PoppinsReg,
                    fontSize = 13.sp,
                    color = TextDark,
                    modifier = Modifier.weight(1f)
                )

                if (item.paid) {
                    Text(
                        text = "Sudah Dibayar",
                        fontFamily = PoppinsSemi,
                        fontSize = 10.sp,
                        color = PaidGreen,
                        textAlign = TextAlign.Center,
                        modifier = Modifier
                            .weight(0.9f)
                            .fillMaxWidth()
                    )
                } else {
                    OutlinedButton(
                        onClick = { onBayarClick(item) },
                        border = BorderStroke(1.dp, AccentOrange),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = AccentOrange),
                        shape = RoundedCornerShape(6.dp),
                        contentPadding = PaddingValues(horizontal = 20.dp, vertical = 6.dp),
                        modifier = Modifier
                            .height(28.dp)
                            .widthIn(min = 91.dp)
                    ) {
                        Text(
                            "Bayar",
                            fontFamily = PoppinsSemi,
                            fontSize = 12.sp
                        )
                    }
                }
            }

            if (i != items.lastIndex) {
                Divider(color = LineGray, thickness = 1.dp)
            }
        }
    }
}

/* ===== PREVIEW ===== */
@Preview(showSystemUi = true, showBackground = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun PreviewTagihan() {
    MaterialTheme { TagihanIuranScreen() }
}
