package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
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
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

/* ===== Tokens ===== */
private val BlueMain    = Color(0xFF000000)
private val BlueBorder  = Color(0xFF2F7FA3)
private val OrangeTitle = Color(0xFFE69B73)
private val TextDark    = Color(0xFF0E0E0E)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun DetailRiwayatPengaduan(
    nama: String = "Lily",
    tanggal: String = "1 Oktober 2025",
    tempat: String = "di depan lapangan voli",
    perihal: String =
        "Sampah Berserakan di Jalan, lingkungan menjadi kotor, bau dan banyak lalat.",
    @DrawableRes headerImage: Int = R.drawable.sampah
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
    ) {
        /* Header image — tinggi dibuat besar agar menutup area atas */
        Image(
            painter = painterResource(headerImage),
            contentDescription = null,
            contentScale = ContentScale.Crop,
            modifier = Modifier
                .fillMaxWidth()
                .height(260.dp)
        )
        Spacer(Modifier.height(-960.dp))
        /* Panel putih melengkung menimpa gambar */
        Surface(
            color = Color.White,
            shape = RoundedCornerShape(topStart = 36.dp, topEnd = 36.dp),
            modifier = Modifier
                .fillMaxSize()
                .offset(y = (-92).dp)
        ) {
            Column(
                modifier = Modifier
                    .verticalScroll(rememberScrollState())
                    .padding(horizontal = 16.dp, vertical = 18.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Text(
                    text = "Riwayat Pengaduan",
                    fontFamily = PoppinsSemi,
                    fontWeight = FontWeight.SemiBold,
                    fontSize = 22.sp,
                    color = OrangeTitle,
                    textAlign = TextAlign.Center,
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(bottom = 12.dp, top = 6.dp)
                )
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(16.dp),
                    border = BorderStroke(2.dp, BlueBorder),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(0.dp)
                ) {
                    Column(Modifier.padding(16.dp)) {

                        FieldLabel("Nama Pelapor")
                        ValueText(nama)
                        DividerLine()
                        Spacer(Modifier.height(10.dp))
                        FieldLabel("Tanggal")
                        ValueText(tanggal)
                        DividerLine()
                        Spacer(Modifier.height(5.dp))
                        FieldLabel("Tempat")
                        ValueText(tempat)
                        DividerLine()
                        Spacer(Modifier.height(5.dp))
                        FieldLabel("Perihal")
                        ValueParagraph(perihal)
                        DividerLine()
                        Spacer(Modifier.height(135.dp))
                    }
                }

                Spacer(Modifier.height(20.dp))
            }
        }
    }
}

/* ===== Subcomponents ===== */

@Composable
private fun FieldLabel(text: String) {
    Text(
        text = text,
        fontFamily = PoppinsSemi,
        fontSize = 14.sp,
        color = TextDark
    )
    Spacer(Modifier.height(6.dp))
}

@Composable
private fun ValueText(text: String) {
    Text(
        text = text,
        fontFamily = PoppinsReg,
        fontSize = 14.sp,
        color = TextDark
    )
}

@Composable
private fun ValueParagraph(text: String) {
    Text(
        text = text,
        fontFamily = PoppinsReg,
        fontSize = 14.sp,
        lineHeight = 20.sp,
        color = TextDark
    )
}

@Composable
private fun DividerLine() {
    Spacer(Modifier.height(10.dp))
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(1.dp)
            .clip(RoundedCornerShape(1.dp))
            .background(BlueMain.copy(alpha = 0.9f))
    )
    Spacer(Modifier.height(14.dp))
}

/* ===== Preview (untuk Interactive Mode) ===== */
@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewDetailRiwayatPengaduan() {
    MaterialTheme { DetailRiwayatPengaduan() }
}
