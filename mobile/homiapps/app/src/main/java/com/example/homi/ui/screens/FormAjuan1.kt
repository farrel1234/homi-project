@file:OptIn(ExperimentalMaterial3Api::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.navigation.Routes

private data class SuratChoice(
    val title: String,
    val desc: String,
    val route: String
)

@Composable
fun FormAjuan1(
    onBack: () -> Unit = {},
    onKonfirmasi: (String) -> Unit = {}
) {
    val poppins = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
    val poppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }

    val items = remember {
        listOf(
            SuratChoice(
                title = "Surat Domisili",
                desc = "Untuk keterangan domisili di Hawai Garden.",
                route = Routes.SuratDomisili
            ),
            SuratChoice(
                title = "Surat Pengantar",
                desc = "Untuk pengurusan dokumen ke instansi/kelurahan.",
                route = Routes.SuratPengantar
            ),
            SuratChoice(
                title = "Surat Keterangan Usaha",
                desc = "Untuk legalitas/keterangan usaha warga.",
                route = Routes.SuratUsaha
            ),
            SuratChoice(
                title = "Surat Keterangan Kematian",
                desc = "Untuk keperluan administrasi kematian.",
                route = Routes.SuratKematian
            ),
            SuratChoice(
                title = "Surat Keterangan Belum Menikah",
                desc = "Untuk persyaratan administrasi pernikahan.",
                route = Routes.SuratBelumMenikah
            )
        )
    }

    var selectedRoute by remember { mutableStateOf<String?>(null) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF2F79A0)),
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Spacer(modifier = Modifier.height(40.dp))

        Box(
            modifier = Modifier
                .fillMaxWidth()
                .padding(start = 20.dp)
        ) {
            Image(
                painter = painterResource(id = R.drawable.panahkembali),
                contentDescription = "Kembali",
                modifier = Modifier
                    .size(26.dp)
                    .align(Alignment.CenterStart)
                    .clickable { onBack() }
            )
        }

        Spacer(Modifier.height(8.dp))

        Text(
            text = "Pengajuan Surat",
            fontFamily = poppinsSemi,
            fontWeight = FontWeight.SemiBold,
            color = Color.White,
            fontSize = 22.sp
        )

        Spacer(Modifier.height(8.dp))

        Text(
            text = "Pilih jenis surat yang ingin kamu ajukan.\nNanti kamu akan diarahkan ke formulir sesuai pilihan.",
            fontFamily = poppins,
            color = Color.White,
            fontSize = 13.sp,
            lineHeight = 18.sp,
            modifier = Modifier.padding(horizontal = 32.dp),
            textAlign = TextAlign.Center
        )

        Spacer(Modifier.height(22.dp))

        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Color.White,
                    shape = RoundedCornerShape(topStart = 40.dp, topEnd = 40.dp)
                )
                .padding(horizontal = 22.dp, vertical = 18.dp)
        ) {
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .border(
                        BorderStroke(1.dp, Color(0xFF1C6BA4)),
                        RoundedCornerShape(18.dp)
                    ),
                shape = RoundedCornerShape(18.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .verticalScroll(rememberScrollState())
                        .padding(16.dp)
                ) {
                    Text(
                        text = "Jenis Surat",
                        fontFamily = poppinsSemi,
                        fontWeight = FontWeight.SemiBold,
                        color = Color(0xFF1C6BA4),
                        fontSize = 14.sp
                    )

                    Spacer(Modifier.height(6.dp))

                    Text(
                        text = "Pilih salah satu surat di bawah ini:",
                        fontFamily = poppins,
                        color = Color(0xFF64748B),
                        fontSize = 12.sp
                    )

                    Spacer(Modifier.height(14.dp))

                    items.forEach { item ->
                        SuratChoiceCard(
                            title = item.title,
                            desc = item.desc,
                            selected = (selectedRoute == item.route),
                            onClick = { selectedRoute = item.route },
                            poppins = poppins,
                            poppinsSemi = poppinsSemi
                        )
                        Spacer(Modifier.height(10.dp))
                    }

                    Spacer(Modifier.height(12.dp))

                    val canContinue = selectedRoute != null

                    Button(
                        onClick = { selectedRoute?.let { onKonfirmasi(it) } },
                        enabled = canContinue,
                        colors = ButtonDefaults.buttonColors(
                            containerColor = Color(0xFFFFA06B),
                            disabledContainerColor = Color(0xFFFFA06B).copy(alpha = 0.45f)
                        ),
                        shape = RoundedCornerShape(12.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(46.dp)
                            .padding(horizontal = 6.dp)
                    ) {
                        Text(
                            text = "Lanjut Isi Form",
                            fontFamily = poppinsSemi,
                            fontWeight = FontWeight.SemiBold,
                            color = Color.White,
                            fontSize = 14.sp
                        )
                    }

                    Spacer(Modifier.height(6.dp))

                    Text(
                        text = if (canContinue) "Kamu bisa lanjut ke formulir." else "Pilih jenis surat dulu ya.",
                        fontFamily = poppins,
                        color = if (canContinue) Color(0xFF16A34A) else Color(0xFFEF4444),
                        fontSize = 12.sp,
                        modifier = Modifier.padding(horizontal = 6.dp)
                    )

                    Spacer(Modifier.height(10.dp))
                }
            }
        }
    }
}

@Composable
private fun SuratChoiceCard(
    title: String,
    desc: String,
    selected: Boolean,
    onClick: () -> Unit,
    poppins: FontFamily,
    poppinsSemi: FontFamily
) {
    val borderColor = if (selected) Color(0xFF1C6BA4) else Color(0xFFE5E7EB)
    val bgColor = if (selected) Color(0xFF1C6BA4).copy(alpha = 0.06f) else Color(0xFFF3F4F6)

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() },
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = bgColor),
        border = BorderStroke(1.dp, borderColor),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(14.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(12.dp)
                    .background(
                        color = if (selected) Color(0xFF1C6BA4) else Color.Transparent,
                        shape = RoundedCornerShape(99.dp)
                    )
                    .border(1.5.dp, Color(0xFF1C6BA4), RoundedCornerShape(99.dp))
            )

            Spacer(Modifier.width(12.dp))

            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = title,
                    fontFamily = poppinsSemi,
                    fontWeight = FontWeight.SemiBold,
                    color = Color(0xFF1C6BA4),
                    fontSize = 14.sp
                )
                Spacer(Modifier.height(3.dp))
                Text(
                    text = desc,
                    fontFamily = poppins,
                    color = Color(0xFF64748B),
                    fontSize = 12.sp,
                    lineHeight = 16.sp
                )
            }
        }
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
fun PreviewFormAjuan1Best() {
    MaterialTheme { FormAjuan1() }
}
