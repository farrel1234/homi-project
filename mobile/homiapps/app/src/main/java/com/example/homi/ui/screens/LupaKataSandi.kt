package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.Email
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
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
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

private val BlueMain     = Color(0xFF2F7FA3)
private val BlueDark     = Color(0xFF1A5E7B)
private val FieldBg      = Color(0xFFF8FAFC)
private val FieldBorder  = Color(0xFFE2E8F0)
private val TextDark     = Color(0xFF1E293B)
private val HintGray     = Color(0xFF94A3B8)
private val AccentOrange = Color(0xFFF7A477)
private val ErrorRed     = Color(0xFFEF4444)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun LupaKataSandiEmailScreen(
    onBack: (() -> Unit)? = null,
    onRequestOtp: suspend (email: String) -> Unit = {},
    onOtpSent: (email: String) -> Unit = { _ -> },
    @DrawableRes backIcon: Int = R.drawable.panahkembali,
    @DrawableRes illustrationRes: Int = R.drawable.surat,
    @DrawableRes bellIcon: Int = R.drawable.notif
) {
    var email by remember { mutableStateOf("") }
    val isValid = remember(email) {
        Regex("^[A-Za-z0-9+_.-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$").matches(email.trim())
    }
    val scope = rememberCoroutineScope()
    var showPopup by remember { mutableStateOf(false) }
    var loading by remember { mutableStateOf(false) }
    var errorText by remember { mutableStateOf<String?>(null) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(listOf(BlueMain, BlueDark))
            )
            .statusBarsPadding()
    ) {
        // ===== Header =====
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 12.dp, bottom = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Box(modifier = Modifier.fillMaxWidth().padding(horizontal = 8.dp)) {
                if (onBack != null) {
                    IconButton(
                        onClick = { onBack.invoke() },
                        modifier = Modifier.align(Alignment.CenterStart)
                    ) {
                        Icon(
                            painter = painterResource(backIcon),
                            contentDescription = "Kembali",
                            tint = Color.White,
                            modifier = Modifier.size(22.dp)
                        )
                    }
                }

                Text(
                    text = "Lupa Kata Sandi",
                    fontFamily = PoppinsSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    modifier = Modifier.align(Alignment.Center)
                )
            }

            Spacer(Modifier.height(12.dp))

            // Icon circle
            Box(
                modifier = Modifier
                    .size(56.dp)
                    .background(Color.White.copy(alpha = 0.15f), RoundedCornerShape(16.dp)),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Outlined.Email,
                    contentDescription = null,
                    tint = Color.White,
                    modifier = Modifier.size(28.dp)
                )
            }

            Spacer(Modifier.height(8.dp))
            Text(
                text = "Kami akan mengirimkan kode OTP\nke email terdaftar Anda",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center,
                lineHeight = 18.sp
            )
        }

        // ===== White Card =====
        Surface(
            color = Color.White,
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            modifier = Modifier.fillMaxSize()
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 24.dp, vertical = 28.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                // Illustration
                Image(
                    painter = painterResource(illustrationRes),
                    contentDescription = null,
                    contentScale = ContentScale.Fit,
                    modifier = Modifier.size(180.dp)
                )

                Spacer(Modifier.height(24.dp))

                Text(
                    text = "Masukkan Email Anda",
                    fontFamily = PoppinsSemi,
                    fontSize = 16.sp,
                    color = TextDark,
                    fontWeight = FontWeight.Bold,
                    textAlign = TextAlign.Center
                )

                Spacer(Modifier.height(6.dp))
                Text(
                    text = "Email yang terdaftar di akun HOMI",
                    fontFamily = PoppinsReg,
                    fontSize = 12.sp,
                    color = HintGray,
                    textAlign = TextAlign.Center
                )

                Spacer(Modifier.height(20.dp))

                OutlinedTextField(
                    value = email,
                    onValueChange = {
                        email = it
                        errorText = null
                    },
                    singleLine = true,
                    enabled = !loading,
                    placeholder = {
                        Text("nama@email.com", fontFamily = PoppinsReg, color = HintGray, fontSize = 13.sp)
                    },
                    leadingIcon = {
                        Icon(
                            imageVector = Icons.Outlined.Email,
                            contentDescription = null,
                            tint = if (isValid) BlueMain else HintGray
                        )
                    },
                    shape = RoundedCornerShape(14.dp),
                    textStyle = LocalTextStyle.current.copy(
                        fontFamily = PoppinsReg,
                        fontSize = 14.sp,
                        color = TextDark
                    ),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = BlueMain,
                        unfocusedBorderColor = FieldBorder,
                        focusedContainerColor = FieldBg,
                        unfocusedContainerColor = FieldBg,
                        cursorColor = BlueMain
                    ),
                    modifier = Modifier
                        .fillMaxWidth()
                        .heightIn(min = 52.dp)
                )

                // Validation feedback
                if (email.isNotEmpty() && !isValid) {
                    Spacer(Modifier.height(6.dp))
                    Text(
                        text = "Format email belum valid",
                        color = ErrorRed,
                        fontFamily = PoppinsReg,
                        fontSize = 11.sp
                    )
                }

                errorText?.let {
                    Spacer(Modifier.height(12.dp))
                    Surface(
                        shape = RoundedCornerShape(10.dp),
                        color = ErrorRed.copy(alpha = 0.08f),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text(
                            text = it,
                            color = ErrorRed,
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            modifier = Modifier.padding(12.dp),
                            textAlign = TextAlign.Center
                        )
                    }
                }

                Spacer(Modifier.height(32.dp))

                Button(
                    onClick = {
                        if (loading) return@Button
                        val targetEmail = email.trim()
                        if (targetEmail.isBlank() || !isValid) {
                            errorText = "Masukkan email yang valid."
                            return@Button
                        }
                        loading = true
                        errorText = null
                        scope.launch {
                            runCatching { onRequestOtp(targetEmail) }
                                .onSuccess { showPopup = true }
                                .onFailure {
                                    errorText = it.message ?: "Gagal mengirim OTP reset."
                                }
                            loading = false
                        }
                    },
                    enabled = isValid && !loading,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = BlueMain,
                        disabledContainerColor = BlueMain.copy(alpha = 0.4f)
                    ),
                    shape = RoundedCornerShape(16.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(52.dp),
                    elevation = ButtonDefaults.buttonElevation(defaultElevation = 4.dp)
                ) {
                    if (loading) {
                        CircularProgressIndicator(
                            modifier = Modifier.size(20.dp),
                            strokeWidth = 2.dp,
                            color = Color.White
                        )
                        Spacer(Modifier.width(10.dp))
                    }
                    Text(
                        text = if (loading) "Mengirim..." else "Kirim Kode OTP",
                        fontFamily = PoppinsSemi,
                        color = Color.White,
                        fontSize = 15.sp
                    )
                }
            }
        }
    }

    // ===== OTP Sent Popup =====
    if (showPopup) {
        OtpSentPopup(
            bellIcon = bellIcon,
            illustrationRes = illustrationRes,
            message = "Berhasil Mengirim Kode OTP\nke Email Anda !",
            onFinished = {
                showPopup = false
                onOtpSent(email.trim())
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
            ) { },
        contentAlignment = Alignment.Center
    ) {
        Box(contentAlignment = Alignment.TopCenter) {
            Card(
                shape = RoundedCornerShape(24.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 16.dp),
                modifier = Modifier
                    .fillMaxWidth(0.86f)
                    .widthIn(max = 380.dp)
                    .defaultMinSize(minHeight = 320.dp)
                    .padding(top = 38.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 24.dp, vertical = 24.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Spacer(Modifier.height(20.dp))
                    Image(
                        painter = painterResource(illustrationRes),
                        contentDescription = null,
                        contentScale = ContentScale.Fit,
                        modifier = Modifier.size(160.dp)
                    )
                    Spacer(Modifier.height(16.dp))
                    Text(
                        text = message,
                        fontFamily = PoppinsSemi,
                        fontSize = 15.sp,
                        color = TextDark,
                        textAlign = TextAlign.Center,
                        lineHeight = 22.sp
                    )
                }
            }

            // Badge lonceng
            Box(
                modifier = Modifier.size(74.dp),
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
                        .size(60.dp)
                        .clip(CircleShape)
                        .background(
                            Brush.linearGradient(listOf(BlueMain, BlueDark))
                        ),
                    contentAlignment = Alignment.Center
                ) {
                    Image(
                        painter = painterResource(bellIcon),
                        contentDescription = "Notifikasi",
                        contentScale = ContentScale.Fit,
                        modifier = Modifier.size(26.dp)
                    )
                }
            }
        }
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewLupaKataSandiEmailWithPopup() {
    MaterialTheme { LupaKataSandiEmailScreen() }
}
