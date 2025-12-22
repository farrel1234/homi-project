// File: GantiSandiBaru.kt
package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import kotlinx.coroutines.delay

/* ===== Tokens (selaras layar lain) ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueBorder   = Color(0xFF2F7FA3)
private val FieldBg      = Color(0xFFF1F2F4)
private val TextDark     = Color(0xFF0E0E0E)
private val HintGray     = Color(0xFF8A8A8A)
private val AccentOrange = Color(0xFFF7A477)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun GantiSandiBaruScreen(
    onBack: (() -> Unit)? = null,
    onSelesai: (() -> Unit)? = null, // panggil ini setelah popup 2 detik
    @DrawableRes backIcon: Int = R.drawable.panahkembali,
    @DrawableRes successImage: Int = R.drawable.bahagia, // pakai ilustrasi yang kamu punya
    @DrawableRes bellIcon: Int = R.drawable.notif
) {
    var pass1 by remember { mutableStateOf("") }
    var pass2 by remember { mutableStateOf("") }

    var errorText by remember { mutableStateOf<String?>(null) }
    var showPopup by remember { mutableStateOf(false) }

    Box(Modifier.fillMaxSize()) {
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
                Image(
                    painter = painterResource(backIcon),
                    contentDescription = "Kembali",
                    modifier = Modifier
                        .size(28.dp)
                        .clip(CircleShape)
                        .clickable(enabled = onBack != null) { onBack?.invoke() }
                )

                Spacer(Modifier.width(8.dp))
                Text(
                    text = "Buat Kata Sandi",
                    fontFamily = PoppinsSemi,
                    fontSize = 22.sp,
                    color = Color.White,
                    modifier = Modifier.weight(1f),
                    textAlign = TextAlign.Center
                )
                Spacer(Modifier.width(24.dp))
            }

            /* ===== WHITE SHEET ===== */
            Spacer(Modifier.height(10.dp))
            Surface(
                color = Color.White,
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                modifier = Modifier.fillMaxSize()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 18.dp, vertical = 18.dp)
                ) {
                    Spacer(Modifier.height(10.dp))

                    Text(
                        text = "Buat kata sandi baru",
                        fontFamily = PoppinsReg,
                        fontSize = 13.sp,
                        color = TextDark
                    )
                    Spacer(Modifier.height(8.dp))
                    OutlinedTextField(
                        value = pass1,
                        onValueChange = { pass1 = it; errorText = null },
                        singleLine = true,
                        placeholder = { Text(" ", color = HintGray, fontFamily = PoppinsReg) },
                        shape = RoundedCornerShape(6.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = BlueBorder,
                            unfocusedBorderColor = BlueBorder,
                            focusedContainerColor = FieldBg,
                            unfocusedContainerColor = FieldBg,
                            cursorColor = BlueBorder
                        ),
                        modifier = Modifier
                            .fillMaxWidth()
                            .heightIn(min = 44.dp)
                    )

                    Spacer(Modifier.height(14.dp))

                    Text(
                        text = "Masukkan ulang kata sandi baru",
                        fontFamily = PoppinsReg,
                        fontSize = 13.sp,
                        color = TextDark
                    )
                    Spacer(Modifier.height(8.dp))
                    OutlinedTextField(
                        value = pass2,
                        onValueChange = { pass2 = it; errorText = null },
                        singleLine = true,
                        placeholder = { Text(" ", color = HintGray, fontFamily = PoppinsReg) },
                        shape = RoundedCornerShape(6.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = BlueBorder,
                            unfocusedBorderColor = BlueBorder,
                            focusedContainerColor = FieldBg,
                            unfocusedContainerColor = FieldBg,
                            cursorColor = BlueBorder
                        ),
                        modifier = Modifier
                            .fillMaxWidth()
                            .heightIn(min = 44.dp)
                    )

                    errorText?.let {
                        Spacer(Modifier.height(10.dp))
                        Text(
                            text = it,
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            color = Color(0xFFD32F2F)
                        )
                    }

                    Spacer(Modifier.height(22.dp))

                    Button(
                        onClick = {
                            when {
                                pass1.isBlank() || pass2.isBlank() ->
                                    errorText = "Semua kolom wajib diisi."
                                pass1.length < 6 ->
                                    errorText = "Kata sandi minimal 6 karakter."
                                pass1 != pass2 ->
                                    errorText = "Konfirmasi kata sandi tidak cocok."
                                else -> {
                                    errorText = null
                                    showPopup = true
                                }
                            }
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = AccentOrange),
                        shape = RoundedCornerShape(8.dp),
                        modifier = Modifier
                            .fillMaxWidth(0.55f)
                            .height(42.dp)
                            .align(Alignment.CenterHorizontally)
                    ) {
                        Text(
                            text = "Simpan",
                            fontFamily = PoppinsSemi,
                            fontSize = 13.sp,
                            color = Color.White
                        )
                    }
                }
            }
        }

        /* ===== POPUP SUKSES (2 DETIK) ===== */
        if (showPopup) {
            ResetSuccessPopup(
                successImage = successImage,
                bellIcon = bellIcon,
                message = "Berhasil Mengatur Ulang\nKata Sandi Anda !",
                onFinished = {
                    showPopup = false
                    pass1 = ""
                    pass2 = ""
                    onSelesai?.invoke()
                }
            )
        }
    }
}

/* ===== Popup sukses (auto 2 detik) ===== */
@Composable
private fun ResetSuccessPopup(
    @DrawableRes successImage: Int,
    @DrawableRes bellIcon: Int,
    message: String,
    onFinished: () -> Unit
) {
    LaunchedEffect(Unit) {
        delay(2000)
        onFinished()
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0x99000000))
            .clickable(
                interactionSource = remember { MutableInteractionSource() },
                indication = null
            ) { /* block click */ },
        contentAlignment = Alignment.Center
    ) {
        Box(contentAlignment = Alignment.TopCenter) {

            Card(
                shape = RoundedCornerShape(22.dp),
                border = CardDefaults.outlinedCardBorder().copy(
                    width = 2.dp,
                    brush = androidx.compose.ui.graphics.SolidColor(BlueBorder)
                ),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 10.dp),
                modifier = Modifier
                    .fillMaxWidth(0.84f)
                    .widthIn(max = 380.dp)
                    .defaultMinSize(minHeight = 420.dp)
                    .padding(top = 42.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 22.dp, vertical = 22.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Image(
                        painter = painterResource(successImage),
                        contentDescription = "Sukses",
                        contentScale = ContentScale.Fit,
                        modifier = Modifier.size(220.dp)
                    )
                    Spacer(Modifier.height(18.dp))
                    Text(
                        text = message,
                        fontFamily = PoppinsReg,
                        fontSize = 14.sp,
                        color = TextDark,
                        textAlign = TextAlign.Center,
                        lineHeight = 20.sp
                    )
                }
            }

            // Badge lonceng (bulat biru di atas)
            Box(
                modifier = Modifier
                    .offset(y = (-22).dp)
                    .size(78.dp),
                contentAlignment = Alignment.Center
            ) {
                Box(
                    modifier = Modifier
                        .size(78.dp)
                        .clip(CircleShape)
                        .background(Color.White)
                )
                Box(
                    modifier = Modifier
                        .size(64.dp)
                        .clip(CircleShape)
                        .background(BlueMain),
                    contentAlignment = Alignment.Center
                ) {
                    Image(
                        painter = painterResource(bellIcon),
                        contentDescription = "Notifikasi",
                        modifier = Modifier.size(28.dp)
                    )
                    // badge kecil angka 1
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
                                fontFamily = PoppinsSemi,
                                fontSize = 10.sp,
                                color = Color.White
                            )
                        }
                    }
                }
            }
        }
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewGantiSandiBaru() {
    MaterialTheme {
        GantiSandiBaruScreen()
    }
}
