package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.layout.statusBarsPadding
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.BasicTextField
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.SolidColor
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.zIndex
import com.example.homi.R
import kotlinx.coroutines.delay

/* ===== Theme tokens ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueBorder   = Color(0xFF2F7FA3)
private val BlueText     = Color(0xFF2F7FA3)
private val TextPrimary  = Color(0xFF0E0E0E)
private val TextMuted    = Color(0xFF8A8A8A)
private val FieldLine    = Color(0xFF2F7FA3)
private val UploadBg     = Color(0xFFF0F0F0)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun FormPengaduanScreen(
    onBack: (() -> Unit)? = null,
    onKonfirmasi: ((nama: String, tanggal: String, tempat: String, perihal: String) -> Unit)? = null,
    @DrawableRes backIcon: Int = R.drawable.panahkembali,
    @DrawableRes icUpload: Int = R.drawable.kamera,
    @DrawableRes successImage: Int? = R.drawable.bahagia,
    @DrawableRes bellIcon: Int = R.drawable.notif
) {
    val poppins = FontFamily(Font(R.font.poppins_regular))

    var nama by rememberSaveable { mutableStateOf("") }
    var tanggal by rememberSaveable { mutableStateOf("") }
    var tempat by rememberSaveable { mutableStateOf("") }
    var perihal by rememberSaveable { mutableStateOf("") }

    var showPopup by rememberSaveable { mutableStateOf(false) }

    Box(modifier = Modifier.fillMaxSize()) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(BlueMain)
        ) {
            /* Top bar */
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .statusBarsPadding()
                    .padding(horizontal = 16.dp, vertical = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Image(
                    painter = painterResource(id = backIcon),
                    contentDescription = "Kembali",
                    modifier = Modifier
                        .size(24.dp)
                        .clip(CircleShape)
                        .clickable(enabled = onBack != null) { onBack?.invoke() }
                )
                Spacer(Modifier.width(8.dp))
                Text(
                    text = "Formulir Pengaduan",
                    fontFamily = PoppinsSemi,
                    fontSize = 22.sp,
                    color = Color.White,
                    modifier = Modifier.weight(1f),
                    textAlign = TextAlign.Center
                )
                Spacer(Modifier.width(24.dp))
            }

            Text(
                text = "Untuk melaporkan masalah di area lingkungan Anda,\n" +
                        "silahkan mengisi data formulir dibawah ini:",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color.White,
                lineHeight = 18.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 24.dp)
            )

            Spacer(Modifier.height(24.dp))

            Card(
                modifier = Modifier.fillMaxSize(),
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 16.dp, vertical = 18.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Spacer(Modifier.height(16.dp))
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(16.dp),
                        border = BorderStroke(2.dp, BlueBorder),
                        colors = CardDefaults.cardColors(containerColor = Color.White)
                    ) {
                        Spacer(Modifier.height(16.dp))
                        Column(modifier = Modifier.padding(16.dp)) {
                            FieldLabel("Nama Pelapor")
                            UnderlineTextField(value = nama, onValueChange = { nama = it })
                            Spacer(Modifier.height(16.dp))

                            FieldLabel("Tanggal")
                            UnderlineTextField(value = tanggal, onValueChange = { tanggal = it })
                            Spacer(Modifier.height(16.dp))

                            FieldLabel("Tempat")
                            UnderlineTextField(value = tempat, onValueChange = { tempat = it })
                            Spacer(Modifier.height(16.dp))

                            FieldLabel("Perihal")
                            UnderlineTextField(value = perihal, onValueChange = { perihal = it })
                            Spacer(Modifier.height(16.dp))

                            FieldLabel("Upload Foto")
                            Column(horizontalAlignment = Alignment.Start) {
                                Box(
                                    modifier = Modifier
                                        .size(96.dp)
                                        .clip(RoundedCornerShape(12.dp))
                                        .background(UploadBg),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Image(
                                        painter = painterResource(id = icUpload),
                                        contentDescription = "Upload",
                                        contentScale = ContentScale.Fit,
                                        modifier = Modifier.size(46.dp)
                                    )
                                }
                                Spacer(Modifier.height(6.dp))
                                Text(
                                    text = "Max 5 MB",
                                    fontFamily = PoppinsReg,
                                    fontSize = 11.sp,
                                    color = TextMuted,
                                    modifier = Modifier.align(Alignment.CenterHorizontally)
                                )
                                Spacer(Modifier.height(24.dp))
                            }

                            Button(
                                onClick = { showPopup = true },
                                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFFA06B)),
                                shape = RoundedCornerShape(10.dp),
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(horizontal = 35.dp)
                                    .height(48.dp)
                            ) {
                                Text(
                                    text = "Konfirmasi",
                                    color = Color.White,
                                    fontFamily = poppins,
                                    fontWeight = FontWeight.SemiBold,
                                    fontSize = 15.sp
                                )
                            }
                            Spacer(Modifier.height(24.dp))
                        }
                    }
                }
            }
        }

        /* ===== POPUP FIX CENTER (mirip desain gambar) ===== */
        if (showPopup) {
            SuccessPopup10s(
                successImage = successImage,
                bellIcon = bellIcon,
                message = "Formulir Pengaduan Anda\nBerhasil Dikirim !",
                onFinished = {
                    showPopup = false
                    onKonfirmasi?.invoke(nama, tanggal, tempat, perihal)
                    nama = ""; tanggal = ""; tempat = ""; perihal = ""
                }
            )
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
        color = BlueText
    )
    Spacer(Modifier.height(6.dp))
}

@Composable
private fun UnderlineTextField(
    value: String,
    onValueChange: (String) -> Unit,
) {
    Column(Modifier.fillMaxWidth()) {
        BasicTextField(
            value = value,
            onValueChange = onValueChange,
            textStyle = TextStyle(
                color = TextPrimary,
                fontFamily = PoppinsReg,
                fontSize = 14.sp
            ),
            cursorBrush = SolidColor(FieldLine),
            modifier = Modifier
                .fillMaxWidth()
                .padding(bottom = 6.dp)
        )
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(1.dp)
                .background(FieldLine.copy(alpha = 0.8f))
        )
    }
}

/* ===== POPUP (benar-benar di tengah, mirip referensi) ===== */
@Composable
private fun SuccessPopup10s(
    @DrawableRes successImage: Int? = null,
    @DrawableRes bellIcon: Int,
    message: String,
    onFinished: () -> Unit
) {
    // Auto dismiss 5 detik
    LaunchedEffect(Unit) {
        delay(2_000L)
        onFinished()
    }

    // Overlay gelap transparan
    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0x80000000)) // 50% hitam
            .clickable(
                interactionSource = remember { MutableInteractionSource() },
                indication = null
            ) { /* blok klik belakang */ }
    ) {
        Box(
            modifier = Modifier.fillMaxSize(),
            contentAlignment = Alignment.Center
        ) {

            // Wrapper biar bell nempel di bagian atas card
            Box(
                modifier = Modifier.wrapContentSize(),
                contentAlignment = Alignment.TopCenter
            ) {

                // CARD PUTIH UTAMA
                Card(
                    shape = RoundedCornerShape(20.dp),
                    border = BorderStroke(2.dp, BlueBorder),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp),
                    modifier = Modifier
                        .widthIn(max = 320.dp)
                        .fillMaxWidth(0.8f)
                        .padding(top = 32.dp)      // ruang untuk lingkaran bell
                        .zIndex(1f)
                ) {
                    Column(
                        modifier = Modifier
                            .padding(horizontal = 24.dp, vertical = 20.dp)
                            .fillMaxWidth(),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {

                        // Gambar orang bahagia
                        successImage?.let {
                            Image(
                                painter = painterResource(id = it),
                                contentDescription = "Sukses",
                                contentScale = ContentScale.Fit,
                                modifier = Modifier.size(140.dp)
                            )
                            Spacer(Modifier.height(12.dp))
                        }

                        // Teks pesan
                        Text(
                            text = message,
                            fontFamily = PoppinsSemi,
                            fontSize = 16.sp,
                            color = TextPrimary,
                            textAlign = TextAlign.Center,
                            lineHeight = 22.sp
                        )
                        Spacer(Modifier.height(8.dp))
                    }
                }

                // LINGKARAN BELL DI ATAS CARD
                Box(
                    modifier = Modifier
                        .offset(y = (-4).dp)
                        .size(64.dp)
                        .zIndex(2f),
                    contentAlignment = Alignment.Center
                ) {
                    // Lingkaran biru
                    Box(
                        modifier = Modifier
                            .size(64.dp)
                            .clip(CircleShape)
                            .background(BlueMain),
                        contentAlignment = Alignment.Center
                    ) {
                        // Icon bell
                        Image(
                            painter = painterResource(id = bellIcon),
                            contentDescription = "Notifikasi",
                            contentScale = ContentScale.Fit,
                            modifier = Modifier.size(32.dp)
                        )

                        // Badge merah kecil "1"
                        Box(
                            modifier = Modifier
                                .align(Alignment.TopEnd)
                                .offset(x = 4.dp, y = (-4).dp)
                                .size(18.dp)
                                .clip(CircleShape)
                                .background(Color(0xFFFF9966)),
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

/* ===== Preview ===== */
@Preview(showBackground = true, showSystemUi = true, backgroundColor = 0xFFFFFFFF)
@Composable
private fun PreviewFormPengaduan() {
    MaterialTheme {
        FormPengaduanScreen(
            onKonfirmasi = { _, _, _, _ -> }
        )
    }
}
