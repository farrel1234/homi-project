@file:OptIn(ExperimentalMaterial3Api::class)

package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
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
import androidx.compose.ui.graphics.Brush
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

private val BlueMain = Color(0xFF2F7FA3)
private val BlueDark = Color(0xFF1A5E7B)
private val AccentOrange = Color(0xFFF7A477)
private val TextDark = Color(0xFF1E293B)
private val HintGray = Color(0xFF94A3B8)
private val SuccessGreen = Color(0xFF22C55E)
private val ErrorRed = Color(0xFFEF4444)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

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
            .background(
                Brush.verticalGradient(listOf(BlueMain, BlueDark))
            )
    ) {
        // ===== Header (Premium Gradient Style) =====
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .statusBarsPadding()
                .padding(top = 16.dp, bottom = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Box(modifier = Modifier.fillMaxWidth().padding(horizontal = 8.dp)) {
                IconButton(
                    onClick = onBack,
                    modifier = Modifier.align(Alignment.CenterStart)
                ) {
                    Icon(
                        painter = painterResource(id = R.drawable.panahkembali),
                        contentDescription = "Kembali",
                        tint = Color.White,
                        modifier = Modifier.size(24.dp)
                    )
                }

                Text(
                    text = "Pengajuan Surat",
                    fontFamily = PoppinsSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(12.dp))

            Text(
                text = "Pilih jenis surat yang ingin Anda ajukan.\nLengkapi data sesuai instansi yang dituju.",
                fontFamily = PoppinsReg,
                color = Color.White.copy(alpha = 0.85f),
                fontSize = 12.sp,
                lineHeight = 18.sp,
                modifier = Modifier.padding(horizontal = 32.dp),
                textAlign = TextAlign.Center
            )
        }

        // ===== White Container Card =====
        Surface(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            color = Color.White
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 24.dp, vertical = 24.dp)
            ) {
                Text(
                    text = "Pilih Kategori Surat",
                    fontFamily = PoppinsSemi,
                    color = TextDark,
                    fontSize = 15.sp
                )

                Spacer(Modifier.height(4.dp))

                Text(
                    text = "Ketuk untuk memilih salah satu:",
                    fontFamily = PoppinsReg,
                    color = HintGray,
                    fontSize = 12.sp
                )

                Spacer(Modifier.height(18.dp))

                Column(
                    modifier = Modifier
                        .weight(1f)
                        .verticalScroll(rememberScrollState()),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    items.forEach { item ->
                        SuratChoiceCard(
                            title = item.title,
                            desc = item.desc,
                            selected = (selectedRoute == item.route),
                            onClick = { selectedRoute = item.route }
                        )
                    }
                    Spacer(Modifier.height(20.dp))
                }

                // Action Footer
                Divider(color = Color(0xFFF1F5F9), thickness = 1.dp)
                Spacer(Modifier.height(16.dp))

                val canContinue = selectedRoute != null

                Button(
                    onClick = { selectedRoute?.let { onKonfirmasi(it) } },
                    enabled = canContinue,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = BlueMain,
                        disabledContainerColor = BlueMain.copy(alpha = 0.45f)
                    ),
                    shape = RoundedCornerShape(16.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(52.dp),
                    elevation = ButtonDefaults.buttonElevation(defaultElevation = 4.dp)
                ) {
                    Text(
                        text = "Lanjut Isi Form",
                        fontFamily = PoppinsSemi,
                        color = Color.White,
                        fontSize = 15.sp
                    )
                }

                if (!canContinue) {
                    Spacer(Modifier.height(8.dp))
                    Text(
                        text = "Silakan pilih salah satu kategori di atas.",
                        fontFamily = PoppinsReg,
                        color = ErrorRed,
                        fontSize = 11.sp,
                        modifier = Modifier.fillMaxWidth(),
                        textAlign = TextAlign.Center
                    )
                } else {
                    Spacer(Modifier.height(8.dp))
                    Text(
                        text = "Anda dapat melanjutkan ke pengisian data.",
                        fontFamily = PoppinsReg,
                        color = SuccessGreen,
                        fontSize = 11.sp,
                        modifier = Modifier.fillMaxWidth(),
                        textAlign = TextAlign.Center
                    )
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
    onClick: () -> Unit
) {
    val borderColor = if (selected) BlueMain else Color(0xFFF1F5F9)
    val bgColor = if (selected) BlueMain.copy(alpha = 0.05f) else Color(0xFFF8FAFC)
    val iconColor = if (selected) BlueMain else HintGray

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() },
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = bgColor),
        border = BorderStroke(1.dp, borderColor),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Radio-like indicator
            Box(
                modifier = Modifier
                    .size(20.dp)
                    .background(
                        color = if (selected) BlueMain else Color.Transparent,
                        shape = RoundedCornerShape(99.dp)
                    )
                    .border(1.5.dp, if (selected) BlueMain else HintGray, RoundedCornerShape(99.dp)),
                contentAlignment = Alignment.Center
            ) {
                if (selected) {
                    Box(
                        modifier = Modifier
                            .size(8.dp)
                            .background(Color.White, RoundedCornerShape(99.dp))
                    )
                }
            }

            Spacer(Modifier.width(16.dp))

            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = title,
                    fontFamily = PoppinsSemi,
                    color = if (selected) BlueMain else TextDark,
                    fontSize = 14.sp
                )
                Spacer(Modifier.height(2.dp))
                Text(
                    text = desc,
                    fontFamily = PoppinsReg,
                    color = HintGray,
                    fontSize = 12.sp,
                    lineHeight = 16.sp
                )
            }
        }
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
fun PreviewFormAjuan1Premium() {
    MaterialTheme { FormAjuan1() }
}
