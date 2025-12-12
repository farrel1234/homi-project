package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.zIndex
import com.example.homi.R
import kotlinx.coroutines.delay

/* ===== Tokens ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFFF9966)
private val FieldBorder  = Color(0xFF4D8FB0)
private val FieldBg      = Color(0xFFF1F2F4)
private val LabelColor   = Color(0xFF1B1B1B)
private val HintColor    = Color(0xFF9AA4AF)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun LaporkanMasalahScreen(
    onKirim: (email: String, perihal: String, detail: String) -> Unit = { _, _, _ -> },
    onBack: () -> Unit = {},
    onGoAkun: () -> Unit = {} // ✅ dipanggil setelah popup 2 detik
) {
    val poppins = FontFamily(Font(R.font.poppins_regular))

    var email by remember { mutableStateOf("") }
    var perihal by remember { mutableStateOf("") }
    var detail by remember { mutableStateOf("") }
    var showPopup by rememberSaveable { mutableStateOf(false) }

    // ✅ setelah popup tampil, tunggu 2 detik lalu pindah Akun
    LaunchedEffect(showPopup) {
        if (showPopup) {
            delay(2000)
            showPopup = false
            onGoAkun()
        }
    }

    Box(Modifier.fillMaxSize()) {
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
            Spacer(Modifier.height(12.dp))

            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp)
            ) {
                // ✅ Panah kembali seperti mockup (tanpa background)
                Image(
                    painter = painterResource(R.drawable.panahkembali),
                    contentDescription = "Kembali",
                    modifier = Modifier
                        .size(22.dp)
                        .align(Alignment.CenterStart)
                        .clickable { onBack() }
                )

                Text(
                    text = "Laporkan Masalah",
                    color = Color.White,
                    fontFamily = PoppinsSemi,
                    fontSize = 22.sp,
                    textAlign = TextAlign.Center,
                    modifier = Modifier
                        .align(Alignment.Center)
                        .padding(horizontal = 40.dp)
                )
            }

            Spacer(Modifier.height(6.dp))
            Text(
                text = "Laporkan yaaa kalau ada masalah dengan aplikasi kami",
                color = Color.White.copy(alpha = 0.9f),
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp)
            )

            Spacer(Modifier.height(24.dp))
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
                        .imePadding()
                        .padding(horizontal = 20.dp, vertical = 18.dp),
                    horizontalAlignment = Alignment.Start
                ) {
                    Spacer(Modifier.height(8.dp))

                    LabelText("Email")
                    OutlineField(
                        value = email,
                        onValueChange = { email = it },
                        singleLine = true,
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email)
                    )

                    Spacer(Modifier.height(14.dp))

                    LabelText("Perihal")
                    OutlineField(
                        value = perihal,
                        onValueChange = { perihal = it },
                        singleLine = true
                    )

                    Spacer(Modifier.height(14.dp))

                    LabelText("Detail Pesan")
                    OutlineField(
                        value = detail,
                        onValueChange = { detail = it },
                        singleLine = false,
                        minLines = 7
                    )

                    Spacer(Modifier.height(26.dp))

                    Box(
                        modifier = Modifier.fillMaxWidth(),
                        contentAlignment = Alignment.Center
                    ) {
                        Button(
                            onClick = {
                                onKirim(email.trim(), perihal.trim(), detail.trim())
                                showPopup = true
                            },
                            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFFA06B)),
                            shape = RoundedCornerShape(10.dp),
                            modifier = Modifier
                                .fillMaxWidth(0.72f)
                                .height(44.dp)
                        ) {
                            Text(
                                text = "Kirim",
                                color = Color.White,
                                fontFamily = poppins,
                                fontWeight = FontWeight.SemiBold,
                                fontSize = 14.sp
                            )
                        }
                    }

                    Spacer(Modifier.height(20.dp))
                }
            }
        }

        /* ===== POPUP SUKSES (2 detik) ===== */
        if (showPopup) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Color(0x88000000))
                    .zIndex(10f),
                contentAlignment = Alignment.Center
            ) {
                Box(contentAlignment = Alignment.TopCenter) {

                    Card(
                        shape = RoundedCornerShape(22.dp),
                        border = CardDefaults.outlinedCardBorder().copy(
                            width = 2.dp,
                            brush = androidx.compose.ui.graphics.SolidColor(FieldBorder)
                        ),
                        colors = CardDefaults.cardColors(containerColor = Color.White),
                        elevation = CardDefaults.cardElevation(defaultElevation = 10.dp),
                        modifier = Modifier
                            .fillMaxWidth(0.86f)
                            .widthIn(max = 380.dp)
                            .defaultMinSize(minHeight = 360.dp)
                            .padding(top = 38.dp)
                            .shadow(10.dp, RoundedCornerShape(22.dp), clip = false)
                    ) {
                        Column(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(horizontal = 20.dp, vertical = 18.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Image(
                                painter = painterResource(R.drawable.bahagia),
                                contentDescription = null,
                                contentScale = ContentScale.Fit,
                                modifier = Modifier.size(210.dp)
                            )

                            Spacer(Modifier.height(10.dp))

                            Text(
                                text = "Laporan Anda Berhasil\nDi Kirim !",
                                fontFamily = PoppinsSemi,
                                fontSize = 16.sp,
                                color = Color(0xFF111827),
                                textAlign = TextAlign.Center
                            )

                            Spacer(Modifier.height(8.dp))

                            Text(
                                text = "Mohon Tunggu Proses Laporan",
                                fontFamily = PoppinsReg,
                                fontSize = 12.sp,
                                color = AccentOrange,
                                textAlign = TextAlign.Center
                            )
                        }
                    }

                    // Badge lonceng atas
                    Box(
                        modifier = Modifier
                            .offset(y = (-20).dp)
                            .size(74.dp),
                        contentAlignment = Alignment.Center
                    ) {
                        Box(
                            modifier = Modifier
                                .size(74.dp)
                                .clip(CircleShape)
                                .background(Color.White)
                        )
                        Box(
                            modifier = Modifier
                                .size(62.dp)
                                .clip(CircleShape)
                                .background(BlueMain),
                            contentAlignment = Alignment.Center
                        ) {
                            Image(
                                painter = painterResource(R.drawable.notif),
                                contentDescription = "Notifikasi",
                                modifier = Modifier.size(28.dp)
                            )

                            Box(
                                modifier = Modifier
                                    .align(Alignment.TopEnd)
                                    .offset(x = 6.dp, y = (-6).dp)
                                    .size(18.dp)
                                    .clip(CircleShape)
                                    .background(Color.White),
                                contentAlignment = Alignment.Center
                            ) {
                                Box(
                                    modifier = Modifier
                                        .size(14.dp)
                                        .clip(CircleShape)
                                        .background(AccentOrange),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Text(
                                        text = "1",
                                        color = Color.White,
                                        fontFamily = PoppinsSemi,
                                        fontSize = 10.sp
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

/* ===== Components ===== */

@Composable
private fun LabelText(text: String) {
    Text(
        text = text,
        fontFamily = PoppinsReg,
        fontSize = 12.sp,
        color = LabelColor,
        modifier = Modifier
            .fillMaxWidth()
            .padding(bottom = 6.dp)
    )
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun OutlineField(
    value: String,
    onValueChange: (String) -> Unit,
    singleLine: Boolean,
    minLines: Int = if (singleLine) 1 else 4,
    keyboardOptions: KeyboardOptions = KeyboardOptions.Default
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        singleLine = singleLine,
        minLines = minLines,
        keyboardOptions = keyboardOptions,
        shape = RoundedCornerShape(8.dp),
        modifier = Modifier.fillMaxWidth(),
        colors = OutlinedTextFieldDefaults.colors(
            focusedBorderColor = FieldBorder,
            unfocusedBorderColor = FieldBorder,
            focusedContainerColor = FieldBg,
            unfocusedContainerColor = FieldBg,
            cursorColor = FieldBorder
        )
    )
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewLaporkanMasalah() {
    MaterialTheme { LaporkanMasalahScreen() }
}
