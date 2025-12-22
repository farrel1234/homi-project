@file:OptIn(ExperimentalMaterial3Api::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
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
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.foundation.layout.statusBarsPadding
import com.example.homi.R

/* ===== Tokens ===== */
private val BlueHeader  = Color(0xFF2F79A0)
private val BlueBorder  = Color(0xFF2F7FA3)
private val OrangeTitle = Color(0xFFE69B73)
private val TextDark    = Color(0xFF0E0E0E)
private val TextMuted   = Color(0xFF6B7280)

private val PoppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }
private val PoppinsReg  = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
private val Inter       = try { FontFamily(Font(R.font.inter_variablefont_opsz_wght)) } catch (_: Exception) { FontFamily.Default }

@Composable
fun DetailRiwayatPengaduan(
    nama: String = "Lily",
    tanggal: String = "1 Oktober 2025",
    tempat: String = "di depan lapangan voli",
    perihal: String = "Sampah berserakan di jalan, lingkungan menjadi kotor, bau dan banyak lalat.",
    status: String = "Diproses",
    kategori: String = "Kebersihan",
    ticketId: String = "PGD-0021",
    onBack: (() -> Unit)? = null
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
    ) {

        // ===== HEADER (PASTI KELIATAN) =====
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(240.dp)
                .background(BlueHeader)
                .statusBarsPadding()
        ) {
            // Back icon
            IconButton(
                onClick = { onBack?.invoke() },
                enabled = onBack != null,
                modifier = Modifier
                    .align(Alignment.TopStart)
                    .padding(start = 10.dp, top = 6.dp)
            ) {
                Icon(
                    painter = painterResource(id = R.drawable.panahkembali),
                    contentDescription = "Kembali",
                    tint = Color.White
                )
            }

            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 22.dp, vertical = 16.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                Text(
                    text = "Detail Riwayat Pengaduan",
                    fontFamily = PoppinsSemi,
                    fontWeight = FontWeight.SemiBold,
                    fontSize = 22.sp,
                    color = Color.White,
                    textAlign = TextAlign.Center
                )
                Spacer(Modifier.height(10.dp))
                Text(
                    text = "Ini adalah halaman detail riwayat pengaduan.\nKamu bisa cek status laporan kamu di sini.",
                    fontFamily = PoppinsReg,
                    fontSize = 12.sp,
                    color = Color.White,
                    textAlign = TextAlign.Center,
                    lineHeight = 16.sp
                )
            }
        }

        // ===== BODY PUTIH (NEMPEL KE HEADER) =====
        Surface(
            color = Color.White,
            shape = RoundedCornerShape(topStart = 36.dp, topEnd = 36.dp),
            modifier = Modifier
                .fillMaxSize()
                .offset(y = (-28).dp) // overlap dikit biar kayak desain kamu
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
                    .padding(horizontal = 16.dp, vertical = 18.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {

                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(16.dp),
                    border = BorderStroke(2.dp, BlueBorder),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(0.dp)
                ) {
                    Column(Modifier.padding(16.dp)) {

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            StatusChip(status = status)
                            Spacer(Modifier.width(10.dp))
                            Text(
                                text = "ID: $ticketId",
                                fontFamily = Inter,
                                fontSize = 12.sp,
                                color = TextMuted
                            )
                        }

                        Spacer(Modifier.height(14.dp))
                        DividerLine()

                        FieldLabel("Nama Pelapor")
                        ValueText(nama)
                        DividerLine()

                        FieldLabel("Tanggal")
                        ValueText(tanggal)
                        DividerLine()

                        FieldLabel("Kategori")
                        ValueText(kategori)
                        DividerLine()

                        FieldLabel("Tempat")
                        ValueText(tempat)
                        DividerLine()

                        FieldLabel("Perihal")
                        ValueParagraph(perihal)
                        DividerLine()

                        Spacer(Modifier.height(120.dp))
                    }
                }

                Spacer(Modifier.height(20.dp))
            }
        }
    }
}

/* ===== Subcomponents ===== */

@Composable
private fun StatusChip(status: String) {
    val (bg, fg) = when (status.lowercase()) {
        "diajukan" -> Color(0xFFF3F4F6) to Color(0xFF374151)
        "diproses" -> Color(0xFFEFF6FF) to Color(0xFF1D4ED8)
        "selesai"  -> Color(0xFFECFDF3) to Color(0xFF047857)
        "ditolak"  -> Color(0xFFFEF2F2) to Color(0xFFB91C1C)
        else       -> Color(0xFFF3F4F6) to Color(0xFF374151)
    }

    Box(
        modifier = Modifier
            .clip(RoundedCornerShape(999.dp))
            .background(bg)
            .padding(horizontal = 10.dp, vertical = 6.dp)
    ) {
        Text(
            text = status,
            fontFamily = PoppinsSemi,
            fontSize = 11.sp,
            fontWeight = FontWeight.SemiBold,
            color = fg
        )
    }
}

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
            .background(Color.Black.copy(alpha = 0.14f))
    )
    Spacer(Modifier.height(14.dp))
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewDetailRiwayatPengaduanFixed() {
    MaterialTheme { DetailRiwayatPengaduan() }
}
