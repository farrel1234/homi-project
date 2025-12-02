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

/* ===== Theme tokens (selaras layar lain) ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val FieldBg      = Color(0xFFF1F2F4)
private val FieldBorder  = Color(0xFF4D8FB0)
private val TextDark     = Color(0xFF0E0E0E)
private val HintGray     = Color(0xFF9AA4AF)
private val AccentOrange = Color(0xFFFFA06B)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun LupaKataSandiEmailScreen(
    onBack: (() -> Unit)? = null,
    onOtpSent: (email: String) -> Unit = { _ -> },
    @DrawableRes backIcon: Int = R.drawable.panahkembali,
    @DrawableRes illustrationRes: Int = R.drawable.amplop,
    @DrawableRes bellIcon: Int = R.drawable.notif
) {
    var email by remember { mutableStateOf("") }
    val isValid = remember(email) {
        Regex("^[A-Za-z0-9+_.-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$").matches(email.trim())
    }
    var showPopup by remember { mutableStateOf(false) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        /* ===== Top bar ===== */
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
                    .size(24.dp)
                    .clip(CircleShape)
                    .clickable(enabled = onBack != null) { onBack?.invoke() }
            )
            Spacer(Modifier.width(8.dp))
            Text(
                text = "Ubah Kata Sandi",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )
            Spacer(Modifier.width(24.dp))
        }

        /* ===== Konten putih rounded ===== */
        Spacer(Modifier.height(10.dp))
        Surface(
            color = Color.White,
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            modifier = Modifier.fillMaxSize()
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 20.dp, vertical = 18.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Spacer(Modifier.height(8.dp))
                Image(
                    painter = painterResource(illustrationRes),
                    contentDescription = null,
                    contentScale = ContentScale.Fit,
                    modifier = Modifier.size(220.dp)
                )

                Spacer(Modifier.height(6.dp))
                Text(
                    text = "Masukkan alamat Email Anda",
                    fontFamily = PoppinsSemi,
                    fontSize = 14.sp,
                    color = BlueMain,
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Center
                )

                Spacer(Modifier.height(10.dp))
                OutlinedTextField(
                    value = email,
                    onValueChange = { email = it },
                    singleLine = true,
                    placeholder = { Text("nama@email.com", fontFamily = PoppinsReg, color = HintGray) },
                    shape = RoundedCornerShape(10.dp),
                    textStyle = LocalTextStyle.current.copy(fontFamily = PoppinsReg, fontSize = 14.sp, color = TextDark),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = FieldBorder,
                        unfocusedBorderColor = FieldBorder,
                        focusedContainerColor = FieldBg,
                        unfocusedContainerColor = FieldBg,
                        cursorColor = FieldBorder
                    ),
                    modifier = Modifier
                        .fillMaxWidth()
                        .heightIn(min = 52.dp)
                )

                Spacer(Modifier.height(26.dp))
                Button(
                    onClick = { showPopup = true },
                    enabled = isValid,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = AccentOrange,
                        disabledContainerColor = AccentOrange.copy(alpha = 0.5f)
                    ),
                    shape = RoundedCornerShape(10.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp)
                ) {
                    Text("Selanjutnya", fontFamily = PoppinsSemi, color = Color.White, fontSize = 15.sp)
                }
            }
        }
    }

    if (showPopup) {
        OtpSentPopup(
            bellIcon = bellIcon,
            illustrationRes = illustrationRes,
            message = "Berhasil Mengirim Kode OTP\nke Email Anda !",
            onFinished = {
                showPopup = false
                onOtpSent(email.trim()) // lanjut ke layar OTP
            }
        )
    }
}

/* ===== POPUP (center + badge lonceng) ===== */
@Composable
private fun OtpSentPopup(
    @DrawableRes bellIcon: Int,
    @DrawableRes illustrationRes: Int,
    message: String,
    onFinished: () -> Unit
) {
    // auto-dismiss 2.5s
    LaunchedEffect(Unit) {
        delay(2500)
        onFinished()
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0x99000000))
            .clickable(
                interactionSource = remember { MutableInteractionSource() },
                indication = null
            ) { /* block click to behind */ },
        contentAlignment = Alignment.Center
    ) {
        // wrapper agar badge overlap di atas kartu
        Box(contentAlignment = Alignment.TopCenter) {
            // card utama
            Card(
                shape = RoundedCornerShape(22.dp),
                border = CardDefaults.outlinedCardBorder().copy(
                    width = 2.dp,
                    brush = androidx.compose.ui.graphics.SolidColor(BlueMain)
                ),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 10.dp),
                modifier = Modifier
                    .fillMaxWidth(0.86f)
                    .widthIn(max = 380.dp)
                    .defaultMinSize(minHeight = 360.dp)
                    .padding(top = 38.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 20.dp, vertical = 18.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Image(
                        painter = painterResource(illustrationRes),
                        contentDescription = null,
                        contentScale = ContentScale.Fit,
                        modifier = Modifier.size(200.dp)
                    )
                    Spacer(Modifier.height(12.dp))
                    Text(
                        text = message,
                        fontFamily = PoppinsSemi,
                        fontSize = 16.sp,
                        color = Color(0xFF111827),
                        textAlign = TextAlign.Center,
                        lineHeight = 22.sp
                    )
                    Spacer(Modifier.height(8.dp))
                }
            }

            // badge lonceng menempel
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
                        painter = painterResource(bellIcon),
                        contentDescription = "Notifikasi",
                        contentScale = ContentScale.Fit,
                        modifier = Modifier.size(28.dp)
                    )
                    // titik/badge kecil 1 di pojok
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
                            Text("1", color = Color.White, fontFamily = PoppinsSemi, fontSize = 10.sp)
                        }
                    }
                }
            }
        }
    }
}

/* ===== Preview ===== */
@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewLupaKataSandiEmailWithPopup() {
    MaterialTheme {
        LupaKataSandiEmailScreen()
    }
}
